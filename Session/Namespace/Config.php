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

class Unus_Session_Namespace_Config extends Unus_Object
{

   /**
    * Default configuration for a session namespace
    */

    public $config = array(
        'lifetime'     => 86400,    /** 2 Weeks Lifetime Default :: SET TO -1 EXPIRE AS A REAL SESSION (BROWSER CLOSE) */
        'allow_modify' => true,    /** Allow Core Session data to be modified once it is written */
        'hop_expires'  => null,     /** Sets a expiration based on number of hops */
        'lock'         => false     /** Locks a session from further writing until another hop is processed */
    );

    public function __construct($config)
    {
        if (!is_array($config)) {
            throw new Unus_Session_Namespace_Config_Exception('Config Expected array; '.gettype($config).' given');
        }

        $namespaceConfig = array_merge($this->config, $config);
        foreach ($namespaceConfig as $k => $v) {
            $this->setConfig($k, $v);
        }
    }

    /**
     * Sets a specific configuration value
     *
     * @param  string  config  Config value to set
     * @param  string  value   Value of config settings
     *
     * @return
     */

    public function setConfig($key, $value)
    {

        if (!array_key_exists($key, $this->config)) {
            throw new Unus_Session_Namespace_Config_Exception('Invalid configuration option; '.$key.'');
        }

        if (($this->getConfig('allow_modify') == true || $this->getConfig('allow_modify') == null) || $key == 'lock') {
            if (($key == 'lifetime' || $key == 'hop_expires') && (int) $value < 0 && ($key == 'lifetime' && $value != -1)) {
                throw new Unus_Session_Namespace_Config_Exception('Configuration option for session lifetime and hop expiration cannot be a negative number '.$value.' given');
            }
            // Set a timestamp in the future for expiration
            if ($key == 'lifetime' && $value != -1) {
                // We also need to check the expiration on the current session expiration and extend until this one expires....
                $expireTime = ($value == 0) ? 0 : (time() + $value);

                $value = $expireTime;

                if (array_key_exists('expire', $_SESSION['_UN_CONF']) && $_SESSION['_UN_CONF']['expire'] < $value) {
                    // we must reset the session to expire at the time for this session
                     $id = session_id();

                    // expire old
                    setcookie('PHPSESSID', null, time() - 3600);

                    // restart new
                    setcookie('PHPSESSID', $id, $expireTime, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));

                    // set the session expire
                    $_SESSION['_UN_CONF']['expire'] = $expireTime;
                }
            }

            $this->setData($key, $value);

            return $this;
        }

        // failure
        return false;
    }

    /**
     * Retrieves a configuration value
     *
     * @param  string  key  Config value to retrieve
     *
     * @return
     */

    public function getConfig($key)
    {
        return $this->getData($key);
    }
}
