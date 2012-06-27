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

/**
 * Unus Router
 *
 * Routes are performed on a very simple basis
 * 1 - URL
 * 2 - Where to route (Controller, View, Event)
 * 3 - Parameters
 */

class Unus_Router
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
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone()
    {
    }

	/**
	 * Returns instance of Unus_Router
	 *
	 * returns Unus_Router
	 */

	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Adds a new route to router
	 *
	 * @param  str   pattern  Regex String for URL to match
	 * @param  array route	  Configuration for this route to point to within Unus. Can be either a Controller->Action, a Direct View Template or a dispatchable event
	 * @param  array param    List of params to match if route is regex if undefined unus will define each var found in asc order 0+ $this->getParam(0);
	 *
	 * @return this
	 */

	public function route($pattern, $route = array(), $param = array(), $header = '200')
	{
		$router = new Unus_Object();
		// strip from end of string
		if (substr($pattern, strlen($pattern) - 1, 1) == '/' && strlen($pattern) != 1) {
			$pattern = substr($pattern, 0, strlen($pattern) - 1);
		}

		if (substr($pattern, 0, 1) == '/' && strlen($pattern) != 1) {
			$pattern = substr($pattern, 1, strlen($pattern) - 1);
		}

		$router->setData('pattern', $pattern);
		// automatically route to index if no action was provided
		if (array_key_exists('controller', $route)) {
			if (!array_key_exists('action', $route)) {
				$route['action'] = 'index';
			}
		} elseif (!array_key_exists('view', $route) && !array_key_exists('event', $route)) {
			throw new Unus_Router_Exception('Route : '.$pattern.' does not route to allowed layer');
		}

		$router->setData('route', $route);
		$router->setData('param', $param);

		$this->_routes[] = $router;

		return $this;
	}

	/**
	 * Returns current system routes
	 *
	 * @return array
	 */

	public function getRoutes()
	{
		return $this->_routes;
	}
}
