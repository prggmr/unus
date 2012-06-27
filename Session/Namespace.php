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

class Unus_Session_Namespace extends Unus_Object
{
    /**
     * Creates a new session namespace with the given string
     *
     * @param  str  namespace  Namespace title
     *
     * @return this
     */

    public function __construct($namespace = 'Default', $config = array())
    {

        $this->setIdentifier($namespace);

        if (!$this->_sessionStarted()) {
            $this->setSessionInitialConfig($config);
        }

        return $this;
    }

    /**
     * Sets a configuration value MUST BE CALLED BEFORE THE SESSION IS STARTED
     *
     * @param  type  name  desc
     *
     * @return this
     */

    public function setConfig($key, $value)
    {
        $this->getData('config')->setConfig($key, $value);
        return $this;
    }


    public function setSessionInitialConfig($config)
    {
        $this->setData('config', new Unus_Session_Namespace_Config($config));
    }

    public function setIdentifier($str)
    {
        $this->setData('identifier', $str);
    }

    public function getIdentifer()
    {
        return $this->getData('identifier');
    }

    /**
     * Locks a session namespace from further writing
     *
     * @return this
     */

    public function lockNamepace()
    {
        $this->getData('config')->setConfig('lock', true);
        return $this;
    }

    /**
     * Checks if a namespace has been locked
     *
     * @return boolean
     */

    public function isLocked()
    {
        if ($this->getData('config')->getConfig('lock') == true) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves namespace session data if it exists
     *
     * @param  type  name  desc
     *
     * @return
     */

    public function startSession($data = array())
    {
        // Create a new session
        if ($this->_isExpired()) {
            // destory the session......to bad....
            unset($_SESSION['_UN'][$this->getIdentifer()]);
        } elseif (!$this->_sessionStarted()) {
            
            $_SESSION['_UN'][$this->getIdentifer()] = $data;

            /**
             *  Lock the session configuration from any further modification
             *  this will also disallow further modification after the session has been started until it expires
             *  and a new one begins
             */

            //$this->getData('config')->setConfig('allow_modify', false);
            $_SESSION['_UN'][$this->getIdentifer()]['object'] = $this;
            $_SESSION['_UN'][$this->getIdentifer()]['config'] = $this->getData('config');
            $_SESSION['_UN'][$this->getIdentifer()]['config_hop_count'] = 1;
        } else {
            $_SESSION['_UN'][$this->getIdentifer()]['config_hop_count'] = $_SESSION['_UN'][$this->getIdentifer()]['config_hop_count'] + 1;
        }

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->setVar($k, $v);
            }
        } else {
            throw new Unus_Session_Namespace_Exception('Data expected array '.gettype($data).' given');
        }

        return true;
    }

    /**
     * Sets a new session variable
     *
     * @param  type  name  desc
     *
     * @return
     */

    public function setVar($name, $value)
    {
        $_SESSION['_UN'][$this->getIdentifer()][$name] = $value;
        return $this;
    }

    private function _sessionStarted()
    {
        if (!array_key_exists($this->getData('identifier'), $_SESSION['_UN'])) {
            return false;
        }

        return true;
    }

    /**
     * Returns current namespace session array
     *
     * @param  type  name  desc
     *
     * @return
     */

    public function getNamespaceSession()
    {
        return $_SESSION['_UN'][$this->getIdentifer()];
    }

    private function _isExpired()
    {
        if ($this->_sessionStarted()) {
            $config = $_SESSION['_UN'][$this->getIdentifer()]['config'];
            $hop_expire = $config->getConfig('hop_expires');
            if ($this->_captureSessionStart() && $config->getConfig('lifetime') == 0) {
                // Session was set to expire upon browser close
                return true;
            } elseif (null != $hop_expire) {
                if ($hop_expire >= $_SESSION['_UN'][$this->getIdentifer()]['config_hop_count']) {
                    // destroy session
                    return true;
                }
            } elseif (time() >= $config->getConfig('lifetime') && $config->getConfig('lifetime') != 0) {
                // destroy session
                return true;
            } else {
                // session is a ok
                return false;
            }
        }

        return false;

        // Allways return session hasnt expired even if it never even started to avoid conflicts and confusion
        // with unstarted sessions and prexisting expired session with common namespace
    }

    /**
     * Destroys the current namespace session only
     *
     * @return
     */

    public function sessionDestroy()
    {
        unset($_SESSION['_UN'][$this->getIdentifer()]);
    }

    /**
     * Captures when a session is first being started :: First Page visit
     *
     * @return boolean
     */

    private function _captureSessionStart()
    {
        if ($_COOKIE['__EXPIRE']) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves time when a session will expire :: Returns -1 for sessions ending upon website close
     *
     * @return int
     */

    public function getSessionExpire()
    {
        return $this->getData('lifetime');
    }

    /**
     * Retrieves number of hops excuted before a session expires
     *
     * @return mixed
     */

    public function getHopExpire()
    {
       return $this->getData('hops_expire');
    }

    /**
     * Retrieves flag for namespace configuration alterations
     *
     * @return boolean
     */

    public function getConfigLock()
    {
        return $this->getData('allow_modify');
    }

    /**
     * Retrieves flag for locking of further modification for this namespace
     *
     * @return mixed
     */

    public function getLock()
    {
        // Unlike other config locking a namespace is only valid for that page and can change so
        // we do not set it for the entire session
        return $this->getData('lock');
    }
}
