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

class Unus_Controller
{

    // Controller Startup

    public function __construct()
    {
		Unus::dispatchEvent('global_init');

		// set the request
		$this->request = Unus_Request::getInstance();

		// by default we always load the view into the scope
		if (null == $this->view) {
			$this->view = Unus::registry('view');
		}
    }

	/**
	 * Returns Safe Post Data
	 */

	public function getPost()
	{
		if ($this->_request->isPost()) {
			foreach ($_POST as $k => $v) {
				$_POST[$k] = htmlentities(addslashes($v));
			}
			return $_POST;
		} else {
			return false;
		}
	}

    /**
     * Load a requested variable from POST or GET
     */
	public function getParam($str)
	{
		if ($this->request->isGet()) {
			if (array_key_exists($str, $_GET)) {
				return $_GET[$str];
			}
		} elseif ($this->request->isPost()) {
			if (array_key_exists($str, $_POST)) {
				return $_POST[$str];
			}
		}
		return false;
	}

	/**
	 * Send a HTTP Header Redirect
	 * Uses 301 Redirect
	 * exit() will be called and script parsing will be halted
	 *
	 * @param  string  url      Url to redirect system
	 * @param  string  refresh  Seconds to pause the redirect
	 *
	 * @return
	 */

	public function redirect($url, $refresh = null)
	{
		$refresh = (null == $refresh) ? 0 : $refresh;
		header("Cache-Control: no-cache, must-revalidate"); // DO NOT CACHE REDIRECT
		Unus_Dispatch_Header::triggerCode('307');
		header('refresh: '.$refresh.'; url='.$url);
		exit(); // exit script
	}

	/**
	 * We do not call anything from within controllers
	 */

	public function __call($method, $arg)
	{
		unus_error('Call to undefinied controller method '.$method.'();', U_FATAL);
		//Unus_Exception_Handler::errorHandler(E_ERROR, 'Call to undefinied method '.$method.'();', __FILE__, __LINE__);
	}

	// Overloading into the Unus:data object

	public function __set($name, $value)
	{
		Unus::register($name, $value);
	}

	// Overloading from the Unus:data object

	public function __get($name)
	{
		return Unus::registry($name);
	}

	// Overloading from the Unus:data object

	public function __unset($name)
	{
		Unus::unregister($name);
	}

	// Overloading from the Unus:data object

	public function __isset($name)
	{
		if (null == Unus::registry($name)) {
			return false;
		} else {
			return true;
		}
	}
}