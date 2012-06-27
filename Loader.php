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

class Unus_Loader
{
    /**
     * Use Data Caching
     */
    public static $cache = null;

    /**
     * Register AutoLoader
     *
     * @param  boolean  cache    Use file caching for autoloading
     *
     * @return
     */

    public static function registerAutoload($cache = null)
    {
         // register autoload
        spl_autoload_register(array('Unus_Loader', 'autoload'));
        if (null != $cache) {
            self::$cache = new $cache();
            if (!self::$cache instanceof Unus_Cache_Interface) {
                throw new Unus_Loader_Exception('Unus Autoload Cache Object '.$cache.' must implement Unus_Cache_Interface');
            }

        }
    }

    public static function autoload($className)
    {
        $className = str_replace('_', '/', $className).'.php';
        $fileData = null;

        if (null !== self::$cache) {
            self::$cache->add('file-cache--'.$className, 'true');
            self::$cache->fetch('file-cache--'.$className);
        }

//        if(!file_exists($className)) {
//			require 'Exception.php';
//			throw new Unus_Exception('Failed to find file '.$className);
//		}
		try {
			include($className);
		} catch (Error_Exception $e) {
			throw new Unus_Exception('Failed to find file : '.$className.'');
		}

    }

    private static function _clean($name)
    {
        $name = str_replace('/', '_', $name);
        $name = str_replace('.php', '', $name);
        return $name;
    }
}
