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
 * @version    $Rev: 2$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

class Unus_Session extends Unus_Object
{
    /**
     * Default Session Namespace name
     */


    const DEFAULT_NAMESPACE = 'Default';

     /**
     * Default Session Name :: Also name of the default session namespace
     */

    private $_namespace = self::DEFAULT_NAMESPACE;

	/**
	 * Instance of self
	 */

	private static $_instance = null;

	/**
	 * Returns self instance
	 *
	 * @return Unus_Session
	 */

	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
     * Starts Unus_Session, Runs php session_start() if it has not yet begun,
     * autoloads all session namespaces into the data object.
     *
     */

    private function __construct()
    {
		$config = Unus_Session_Config::getInstance();

        // autostart session if not yet started
        if (!$this->_sessionStarted()) {
            // Run the session configuration
            $config->runConfiguration();
            session_start();
			$_SESSION['_UN'] = (array_key_exists('_UN', $_SESSION)) ? $_SESSION['_UN'] : array();
			$_SESSION['_UN_CONF'] = (array_key_exists('_UN_CONF', $_SESSION)) ? $_SESSION['_UN_CONF'] : array();
            $_SESSION['_UN_CONF']['hop_count'] = (!array_key_exists('hop_count', $_SESSION['_UN_CONF'])) ? 1 : $_SESSION['_UN_CONF']['hop_count'] + 1;
            $this->startSession();
        } else {
            $_SESSION['_UN_CONF']['hop_count']++;
        }

        // set the expire for the session
        if (!array_key_exists($config->getConfig('name'), $_COOKIE)) {
            $_SESSION['_UN_CONF']['expire'] = time() + ini_get('session.cookie_lifetime');
        }

		/**
		 * @bug  Throws a fatal error if a session starts and fails to set the _UN array
		 *
		 * @fix  Just reset everything ...
		 *
		 * @todo  Find a better workaround for this issue
		 */
		if (!array_key_exists('_UN', $_SESSION)) {
			session_destroy();
			session_start();
			$_SESSION['_UN'] = (array_key_exists('_UN', $_SESSION)) ? $_SESSION['_UN'] : array();
			$_SESSION['_UN_CONF'] = (array_key_exists('_UN_CONF', $_SESSION)) ? $_SESSION['_UN_CONF'] : array();
            $_SESSION['_UN_CONF']['hop_count'] = (!$_SESSION['_UN_CONF']['hop_count']) ? 1 : $_SESSION['_UN_CONF']['hop_count'] + 1;
            $this->startSession();
		}


        // Autoload all sessions into session object
        foreach ($_SESSION['_UN'] as $k => $v) {
            if (is_object($v['object'])) {
                if ($v['object']->getIdentifer() != 'Default') {
                    $v['object']->startSession();
                }
                $handler = new Unus_Session_Handler($v['object']);
                $this->setData($k, $handler);
            }
        }

        // Create the capture cookie after namespaces have started to check for browser expirations

        $this->_setCaptureCookie();

    }

    /**
     * Creates a new session namespace
     *
     * @param  mixed  identifier   Name of the new namespace or a already created Unus_Session_Namespace Instance
     * @param  array  data         Array of data to add to the session once it is created
     * @param  array  config       Configuration values for this namespace. See Unus_Session_Namespace_Config for namespace configuration
     *
     * @return this
     */

    public function startSession($identifier = null, $data = array(), $config = array())
    {
        $identifier = (null == $identifier) ? self::DEFAULT_NAMESPACE : $identifier;

        if (is_array($_SESSION['_UN']) && !array_key_exists($identifier, $_SESSION['_UN'])) {

            if (is_object($identifier) && $identifier instanceof Unus_Session_Namespace) {
                $namespace = $identifier;
            } elseif (is_object($identifier)) {
                throw new Unus_Session_Exception('Object of instance '.get_class($identifier).' given; expected Unus_Session_Namespace');
            } else {
                // Start the session Namespace
                $namespace = new Unus_Session_Namespace($identifier, $config);
            }

            $handler = new Unus_Session_Handler($namespace);

            $handler->startSession($handler->getNamespace(), $data);

            $this->setData($identifier, $handler);

            $this->setNamespace($identifier);
        }

        return $this;
    }

    /**
     * Sets the current session namespace, defaults to Default if not exists
     *
     * @param  string  namespace  Identifier of namespace to use for overloading variables and triggering object code
     *
     * @return this
     */

    public function setNamespace($namespace)
    {
        if ($this->sessionExists($namespace)) {
            $this->_namespace = $namespace;
        } else {
            $this->_namespace = self::DEFAULT_NAMESPACE;
        }
        return $this;
    }

	/**
     * Sets the current session namespace, defaults to Default if not exists.
     * Identical to setNamespace
     *
     * @param  string  namespace  Identifier of namespace to use for overloading variables and triggering object code
     *
     * @return this
     */

    public function _use($namespace)
    {
        if ($this->sessionExists($namespace)) {
            $this->_namespace = $namespace;
        } else {
            $this->_namespace = self::DEFAULT_NAMESPACE;
        }
        return $this;
    }

    /**
     * Returns the current namespace being used by Unus_Session as its identifier
     *
     * @return string
     */

    public function getNamespace()
    {
        return $this->_namespace;
    }


    /**
    * Checks to see if the php session has been iniatied
    *
    * @return boolean
    */

    protected function _sessionStarted()
    {
        if (session_id() === '') {
            return false;
        }

        return true;
    }

    /**
     * Sets a session to the current namespace var using overloading
     *
     */

    public function __set($name, $value)
    {
        if ($name == 'config' || $name == 'config_hop_count') {
            throw new Unus_Session_Namespace_Exception('Session var {config} is protected and cannot be used');
        }

        if ($this->isLocked()) {
            throw new Unus_Session_Exception(sprintf('Session Namespace {%s} has been locked from further modification; namespace must be unlocked to modify', $this->getNamespace()));
        }

        $_SESSION['_UN'][$this->getNamespace()][$name] = $value;
    }

    /**
     * Retrieves a session var from the current namespace via overloading
     *
     * @return mixed
     */

    public function __get($name)
    {
        if ($name == 'config' || $name == 'config_hop_count') {
            throw new Unus_Session_Namespace_Exception('Session var {config} is protected and cannot be retrieved via overloading');
        }

        // check for globally getting values with setting namespace
        if ($this->getNamespace() == self::DEFAULT_NAMESPACE) {
            if (array_key_exists($name, $_SESSION['_UN'][$this->getNamespace()])) {
                return $_SESSION['_UN'][$this->getNamespace()][$name];
            } else {
                foreach ($_SESSION['_UN'] as $k => $v) {
                    if (array_key_exists($name, $v)) {
                        return $v[$name];
                    }
                }
            }
        } else {
            return $_SESSION['_UN'][$this->getNamespace()][$name];
        }

        return false;
    }

    /**
     * Removes a session variable
     */

    public function  __unset($name)
    {
        if ($name == 'config' || $name == 'config_hop_count') {
            throw new Unus_Session_Namespace_Exception('Session var {config} is protected and cannot be removed');
        }

       unset($_SESSION['_UN'][$this->getNamespace()][$name]);
    }

    /**
     * Gets data from session namespace :: Overlooks the current namespace set and uses $namespace instead
     *
     * @param  str  namespace  Namespace session to get data from
     * @param  str  key        Data to fetch from namespace session
     *
     * @return mixed
     */

    public function getParam($key, $namespace = null)
    {
        $namespace = (null == $namespace) ? self::DEFAULT_NAMESPACE : $namespace;
        return $_SESSION['_UN'][$namespace][$key];
    }

    /**
     * Checks if a session registered (not started)
     *
     * @param  string  namespace  Name of session to check if it is registered
     *
     * @return boolean
     */

    public function sessionRegistered($namespace)
    {
        if (array_key_exists($namespace, $_SESSION['_UN'])) {
            return true;
        }

        return false;
    }

    /**
     * Destroys Current Namespace Session Data
     *
     * @return
     */

    public function sessionDestroy($namespace = null)
    {
        $namespace = (null == $namespace) ? $this->getNamespace() : $namespace;
        $this->getData($namespace.'/namespace')->sessionDestroy();
        return $this;
    }

    /**
     * Removes all session data
     *
     * @return
     */

    public function sessionDestroyAll()
    {
        session_destroy();
    }

    /**
     * Sets the session expire cookie for when a browser is closed.
     * Currently used so we can capture and expire namespaces on web browsers closing
     *
     * @return
     */

    private function _setCaptureCookie()
    {
        if (!array_key_exists('__EXPIRE', $_COOKIE)) {
            $sid = session_id();
            setcookie(
                '__EXPIRE',
                $sid
            );
        }
    }

    /**
     * Retireves number of hops performed for this session
     *
     * @return
     */

    public function getSessionHops()
    {
        return $_SESSION['_UN_CONF']['hop_count'];
    }

    /**
     * Locks a session namespace from further writing
     *
     * @return this
     */

    public function lockNamespace()
    {
        $this->getData($this->getNamespace().'/namespace/config')->setConfig('lock', true);
        return $this;
    }

    /**
     * unLocks a previosuly locked session namespace
     *
     * @return this
     */

    public function unlockNamespace()
    {
        $this->getData($this->getNamespace().'/namespace/config')->setConfig('lock', false);
        return $this;
    }

    /**
     * Checks if a namespace has been locked
     *
     * @return boolean
     */

    public function isLocked()
    {
        if ($this->getData($this->getNamespace().'/namespace/config')->getConfig('lock') == true) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves time when a session will expire :: Returns -1 for sessions ending upon website close
     *
     * @param  string  namespace  Session namespace to return expiration time Null for current namespace
     *
     * @return int
     */

    public function getSessionExpire($namespace = null)
    {
        $namespace = (null == $namespace) ? $this->getNamespace() : $namespace;
        return $this->getData($namespace.'/namespace/config/lifetime');
    }

    /**
     * Retrieves number of hops excuted before a session expires
     *
     * @param  string  namespace  Session namespace to return hop expiration Null for current namespace
     *
     * @return mixed
     */

    public function getHopExpire()
    {
        $namespace = (null == $namespace) ? $this->getNamespace() : $namespace;
        return $this->getData($namespace.'/namespace/config/hop_expire');
    }


    /**
     * Checks if session namespace exists
     * Identical to sessionRegisterd()
     *
     * @param  string  namespace  Session namespace name
     *
     * @return boolean
     */

    public function sessionExists($namespace)
    {
        if (array_key_exists($namespace, $_SESSION['_UN'])) {
            return true;
        }
        return false;
    }

    /**
     * Sets system error message | Uses Default Namespace
     *
     * @param  type  str  Error Message
     *
     * @return this
     */

    public function setErrorMessage($str)
    {
        // store current namespace so we can default back
        $namespace = $this->getNamespace();

        if (null != $this->setNamespace(self::DEFAULT_NAMESPACE)->ERROR) {
            $this->setNamespace(self::DEFAULT_NAMESPACE)->ERROR .= '<br />'.$str;
        } else {
            $this->setNamespace(self::DEFAULT_NAMESPACE)->ERROR .= $str;
        }
        // reset to orginal namespace
        $this->setNamespace($namespace);

        return $this;
    }

    /**
     * Returns system error message
     *
     * @param  boolean  unset  Unset the Message
     *
     * @return string
     */

    public function getErrorMessage($unset = false)
    {
        $namespace = $this->getNamespace();
        $msg = $this->setNamespace(self::DEFAULT_NAMESPACE)->ERROR;
        if ($unset) {
            unset($this->setNamespace(self::DEFAULT_NAMESPACE)->ERROR);
        }
        $this->setNamespace($namespace);
        return $msg;
    }

    /**
     * Sets system message | Uses Default Namespace
     *
     * @param  type  name  Message
     *
     * @return
     */

    public function setMessage($str)
    {
        // store current namespace so we can default back
        $namespace = $this->getNamespace();

        if (null != $this->setNamespace(self::DEFAULT_NAMESPACE)->MESSAGE) {
            $this->setNamespace(self::DEFAULT_NAMESPACE)->MESSAGE .= '<br />'.$str;
        } else {
            $this->setNamespace(self::DEFAULT_NAMESPACE)->MESSAGE .= $str;
        }
        // reset to orginal namespace
        $this->setNamespace($namespace);

        return $this;
    }

    /**
     * Returns system message
     *
     * @param  boolean  unset  Unset the Message
     *
     * @return string
     */

    public function getMessage($unset = false)
    {
        $namespace = $this->getNamespace();
        $msg = $this->setNamespace(self::DEFAULT_NAMESPACE)->MESSAGE;
        if ($unset) {
            unset($this->setNamespace(self::DEFAULT_NAMESPACE)->MESSAGE);
        }
        $this->setNamespace($namespace);
        return $msg;

    }

     /**
     * Sets system alert message | Uses Default Namespace
     *
     * @param  type  name  Alert Message
     *
     * @return
     */

    public function setAlertMessage($str)
    {
        // store current namespace so we can default back
        $namespace = $this->getNamespace();

        if (null != $this->setNamespace(self::DEFAULT_NAMESPACE)->ALERT) {
            $this->setNamespace(self::DEFAULT_NAMESPACE)->ALERT .= '<br />'.$str;
        } else {
            $this->setNamespace(self::DEFAULT_NAMESPACE)->ALERT .= $str;
        }
        // reset to orginal namespace
        $this->setNamespace($namespace);

        return $this;
    }

    /**
     * Returns system alert message
     *
     * @param  boolean  unset  Unset the Message
     *
     * @return string
     */

    public function getAlertMessage($unset = false)
    {
        $namespace = $this->getNamespace();
        $msg = $this->setNamespace(self::DEFAULT_NAMESPACE)->ALERT;
        if ($unset) {
            unset($this->setNamespace(self::DEFAULT_NAMESPACE)->ALERT);
        }
        $this->setNamespace($namespace);
        return $msg;
    }

}
