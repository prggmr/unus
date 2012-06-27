<?php

/**
 * Unus
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://nwhiting.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@nwhiting.com so we can send you a copy immediately.
 *
 * DO NOT MODIFY this files contents if you wish to upgrade Unus in the future,
 * If there is a bug with this file address them at http://www.nwhiting.com/
 * so we can include this fix for future releases.
 *
 * For improvements please address them at http://www.nwhiting.com/
 * they will be greatly appreciated, while it is not required it would be good
 * to contribute. HAVE FUN and HAPPY CODING
 *
 */

/**
 * @category   Unus
 * @package    Unus
 * @version    $Rev: 1$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

class Unus_Dispatch
{
	/**
	 * Array of system routes stored in Array/Unus_Object->Data
	 */

	private $_routes = array();

	/**
	 * Instance of self
	 */

	private static $_instance = null;

	/**
	 * URL that is requested
	 */
	private $_requestedURL = null;

	/**
	 * Array of paramaters that have been system set
	 */

	protected $_param = array();

	/**
	 * Private constructor inforce singleton instance
	 */

	private function __construct()
	{}

	/**
	 * Sets a disatcher/controller param access is avaliable only through the dispatch->controller layer
	 *
	 * @param  string  key
	 */

	public function setParam($key, $val)
	{
		$this->_param[$key] = $val;
		return $this;
	}

	/**
	 * Returns a param set in the dispatcher
	 *
	 * @param  string  key
	 */

	public function getParam($key)
	{
		return $this->_param[$key];
	}

	/**
	 * Returns self instance
	 *
	 * @return Unus_Dispatch
	 */

	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Loads the system routes int dispatcher
	 */
	public function loadRoutes($router = null)
	{
		if (null == $router) {
			$router = Unus_Router::getInstance();
		} else {
			if (!$router instanceof Unus_Router_Interface) {
				throw new Unus_Dispatch_Exception('System Router provideded must implement Unus_Router_Interface');
			}
		}

		$this->_routes = $router->getRoutes();
	}

	/**
	 * Dispatch System and route to layer provided by router
	 */

    public function dispatch()
    {
		$request = Unus_Request::getInstance();
		Unus_Development_Benchmark::start('unus dispatch');
		// Load URL
		$this->_requestedURL = $request->getRequestedUrl();

		Unus::dispatchEvent('dispatch_router_init');

		if (null == $this->_routes) {
			$this->loadRoutes();
		}

		$found = false;
		try {
			/**
			 * TODO: Possiably add option to exit script after the dispatcher finds the route as there is no point in finishing unless we plugin dispatch_router_routed
			 * OR unus_shutdown event ... possiable to save a small amount of uneeded execution
			 */
			foreach ($this->_routes as $k => $v) {
				$pattern = $v->getData('pattern');
				$route = $v->getData('route');
				if ($this->match($pattern)) {
					if (array_key_exists('view', $route)) {
						$request->setView($route['view']);
						Unus_Development_Benchmark::stop('unus dispatch');
						echo Unus::registry('view')->getHtml($route['view']);
						$found = true;
						break;
					} elseif (array_key_exists('event', $route)) {
						$request->setEvent($route['event']);
						Unus_Development_Benchmark::stop('unus dispatch');
						Unus::dispatchEvent($route['event']);
						$found = true;
						break;
					} else {
						$controller = $this->transformCName(Unus_Helper_Text_UcWords::ucWords($route['controller']));
						if (!class_exists($controller, true)) {
							throw new Unus_Dispatch_Exception('Dispatch controller : '.$controller .' could not be found');
						}
						$request->setAction($route['action']);
						$request->setController($route['controller']);
						$c = new $controller();
						if (method_exists($c, 'init')) {
							$c->init();
						}
						if (!method_exists($c, $route['action'])) {
							throw new Unus_Dispatch_Exception('Dispatch controller < '.$controller.' > Action : < ' . $route['action'] . ' > could not be found');
						}
						Unus_Development_Benchmark::stop('unus dispatch');
						Unus_Development_Benchmark::start('unus controller');
						// call the controller
						$c->$route['action']();
						// call the end to the controller
						Unus_Development_Benchmark::stop('unus controller');
						$found = true;
						break;
					}
				}
			}
		} catch (Exception $e) {
			throw new Unus_Dispatch_Exception($e);
		}

		if (!$found) {
			Unus_Dispatch_Header::triggerCode('404');
		}

		//ob_end_flush();

		Unus::dispatchEvent('dispatch_router_routed');

		return true;
    }

	/**
	 * Matches a route with URL
	 *
	 * @param  string  pattern  Regex Pattern to match URL
	 *
	 * @return boolean
	 */

	public function match($pattern)
	{
		Unus_Development_Benchmark::start_add('unus dispatch');
		$pattern =  '#' . $pattern . '$#i';
		$r = preg_match($pattern, $this->_requestedURL, $matches);
		//echo $pattern . Unus::dump($r);
		if ($r === 0 || $r === false) {
			return false;
		}
		$this->_setMappedValues($matches);
		Unus_Development_Benchmark::stop_add('unus dispatch');
		return true;
	}

	/**
	 * Maps a array of values from a route into the registry ID
	 *
	 * @param  array  map  	Array of values to map with $values
	 * @param  array  key	Array of keys to map with map array
	 *
	 * @return this
	 */

	protected function _setMappedValues($map)
	{
		if (count($map) == 0) {
			return null;
		}

		foreach ($map as $k => $v) {

			if (!is_int($k)) {
				Unus::register($k, $v);
			}
		}
	}

	/**
	 * Transforms a controller name give from the router
	 * Seperates - into underscores captilizing the first letter in each
	 *
	 * @param  string  name  Controller name to transform
	 *
	 * @return
	 */
	public function transformCName($name)
	{
		$nameArray = explode(' ', $name);
		$return = null;
		foreach ($nameArray as $k => $v) {
			$return .= ($return != null) ? '_'.$v : $v;
		}
		return $return;
	}
}
