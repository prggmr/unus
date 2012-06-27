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

class Unus_Session_Config
{
    /**
     * Runtime config defaults
     */

    private static $_config = array(
        'save_path'               => null,
        'name'                    => null,
        'save_handler'            => null,
        //'auto_start'            => 0,     /** Feature current not supported */
        'gc_probability'          => null,
        'gc_divisor'              => null,
        'gc_maxlifetime'          => null,
        'gc_serialize_handler'    => null,
        'cookie_lifetime'         => null,  /** This needs to be set to the longest namespaced session*/
        'cookie_path'             => null,
        'cookie_domain'           => null,
        'cookie_secure'           => null,
        'cookie_httponly'         => true,  /** Defaults to true - DISALLOWS REQUESTS MADE FROM XSS */
        'use_cookies'             => null,
        'use_only_cookies'        => null,
        'referer_check'           => null,
        'entropy_file'            => null,
        'entropy_length'          => null,
        'cache_limiter'           => null,
        'cache_expire'            => null,
        'use_trans_id'            => null,
        'hash_function'           => null,
        'hash_bits_per_character' => null
    );

     /**
      * Runtime config user set
      */

    private static $_configValues = array();

    /**
     * Instance of class object
     */

    private static $_instance = null;

    static public function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Sets a php runtime session configuration value; For avaliable configuration see http://www.php.net/manual/en/session.configuration.php
     *
     * @param  type  name  desc
     *
     * @return
     */

    public function setConfig($key, $value = null)
    {
        if (session_id() !== '') {
            throw new Unus_Session_Exception('Session configuration cannot be set after the session has started');
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if (!array_key_exists($k,  self::$_config)) {
                    throw new Unus_Session_Exception('Unknown session runtime configuration option; See Unus_Session::$_config for complete list');
                } else {
                     self::$_configValues[$k] = $v;
                }
            }
        } else {
            if (!array_key_exists($key, self::$_config)) {
                return null;
            } else {
                self::$_configValues[$key] = $value;
            }
        }
    }

    /**
     * Sets the php runtime configuration settings
     */

    public function runConfiguration()
    {
        foreach (self::$_configValues as $k => $v) {
            ini_set('session.'.$k, $v);
        }
    }

    /**
     * Returns a session configuration value
     *
     * @param  string  name  Name of session value
     *
     * @throws  Unus_Session_Config_Exception
     * @return  string|null
     **/
    public function getConfig($name)
    {
        if (!array_key_exists($name, self::$_configValues)) {
            return null;
        }
        return  self::$_configValues[$name];
    }

}
