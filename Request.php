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

class Unus_Request
{
    /**
     * GET REQUEST
     */

    const GET = 'GET';
    /**
     * POST REQUEST
     */

    const POST = 'POST';

    /**
     * Is request method post|get
     */

    private $_requestMethod = self::GET;

    private static $_instance = null;

    /**
     * Controller that was dispatched from Unus_Dispatch
     */

    private $_controller = null;

    /**
     * Action that was dispatched from Unus_Dispatch
     */

    private $_action = null;

    /**
     * View that was dispatched from Unus_Dispatch
     */

    private $_view = null;

    /**
     * Event that was dispatched from Unus_Dispatch
     */

    private $_event = null;

    /**
     * Requested URL
     */

    private $_uri = null;

    /**
	 * Method of extracting url
	 * Default will look in the REQUEST_URI
	 */

	private static $_identifierMethod = 'REQUEST_URI';

    /**
     * Return instance of Unus_Request
     *
     * return Unus_Request
     */

    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
	 * Returns and sets requested URL if has not been set
	 *
	 * @return string
	 */
	protected function _loadUri()
	{
		$method = $this->getIdentifierMethod();

		if (null != $this->_uri) {
			return $this->_uri;
		}

		if (is_array($method)) {
			if (!class_exists($method[0], true)) {
				throw new Unus_Request_Exception('Call to undefined class for URL identifier <strong>'.$method[0].'</strong>');
			} else {
				$class = new $method[0];
				if (!method_exists($class, $method[1])) {
					throw new Unus_Request_Exception('Call to undefined method for URL identifier <strong>'.$method[1].'</strong>');
				} else {
					// return url from custom method forcing string
					$this->_uri = (string) $class->method[1];
				}
			}
		} else {
			$this->_uri = str_replace(Unus::getPath(), '', $_SERVER['REQUEST_URI']);

			if (null != $_SERVER['QUERY_STRING']) {
				$this->_uri = str_replace($_SERVER['QUERY_STRING'], '', $this->_uri);
				$this->_uri = str_replace('?', '', $this->_uri);
			}

			// add / for default index
			if ($this->_uri == '') {
				$this->_uri = '/';
			// strip / from end of string
			} elseif (substr($this->_uri, strlen($this->_uri) - 1, 1) == '/') {
				$this->_uri = substr($this->_uri, 0, strlen($this->_uri) - 1);
			}
		}

		return $this;
	}

    /**
     * Enforce singleton instance
     *
     * Set request method
     */

    private function __construct()
    {
        if (null == $this->_uri) {
           $this->_loadUri();
        }

        // set the request method always default to GET
        if ($_SERVER['REQUEST_METHOD'] == self::GET) {
            $this->_requestMethod = self::GET;
        } elseif ($_SERVER['REQUEST_METHOD'] == self::POST) {
            $this->_requestMethod = self::POST;
        } else {
            $this->_requestMethod = self::GET;
        }
    }

    /**
	 * Sets the method for retrieving url; Default: REQUEST_URI
	 *
	 * @param  string  method  Method for obtaining the requested URL. Use array(class, method) for class
	 *
	 * @return this
	 */

	public static function setIdentifierMethod($method)
	{
		self::$_identifierMethod = $method;
	}

	/**
	 * Returns URL identifier method
	 *
	 * @return mixed
	 */

	public function getIdentifierMethod()
	{
		return self::$_identifierMethod;
	}

    /**
     * Checks if requested method is POST
     *
     * @return boolean
     */

    public function isPost()
    {
        if ($this->getRequestMethod() == self::POST) {
            return true;
        }
        return false;
    }

    /**
     * Checks if requested method is GET
     *
     * @return boolean
     */

    public function isGet()
    {
        if ($this->getRequestMethod() == self::GET) {
            return true;
        }
        return false;
    }

    /**
     * Sets the controller that was dispatched
     *
     * @param  string  controller  Name of controller that was dispatched
     *
     * @return this
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

    /**
     * Sets the controller action that was dispatched
     *
     * @param  string  action  Name of action that was dispatched
     *
     * @return this
     */
    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    /**
     * Sets the view that was dispatched
     *
     * @param  string  view  Name of view that was dispatched
     *
     * @return this
     */
    public function setView($view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Sets the event that was dispatched
     *
     * @param  string  event  Name of event that was dispatched
     *
     * @return this
     */
    public function setEvent($event)
    {
        $this->_event = $event;
        return $this;
    }

    /**
     * Returns the controller that was dispatched
     *
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Returns the controller action that was dispatched
     *
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Gets the view that was dispatched
     *
     * @return string
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Gets the event that was dispatched
     *
     * @return string
     */
    public function getEvent()
    {
       return $this->_event;
    }

    /**
     * Gets the requested URL
     *
     * @return string
     */
    public function getRequestedUrl()
    {
       return $this->_uri;
    }

	/**
     * Gets the requested URL
     *
     * @return string
     */
    public function getRequestedUri()
    {
       return $this->_uri;
    }

    /**
     * Returns request method
     *
     * @return boolean
     */

    public function getRequestMethod()
    {
        return $this->_requestMethod;
    }
    /**
     * Disallow clone
     */
    private function __clone(){}


}
