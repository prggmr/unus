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

class Unus_Development
{

	/**
	 * Current state of development mode
	 */
	private static $_devMode = false;

	private static $_firePHP = false;

	/**
	 * Sets current development mode
	 *
	 * @param  boolean  flag  		Flag true|false development mode
	 * @param  boolean  handle404	Use Unus 404 Error Handler
 	 */

	public static function setDevMode($flag = true, $handle404 = true)
	{
		self::$_devMode = $flag;

		// we will automatically set the 404 collector unless specified...
		if ($flag && $handle404) {
            Unus::addObserver('404 Error Handle', array('Unus_Development_Controller', 'error404'), 'http_code_404');
		}

		if ($flag) {
            error_reporting(E_ALL &  ~E_STRICT);
            ob_start();
            Unus_Development_Benchmark::start('unus core');
		} else {
			error_reporting(0);
		}

        require_once Unus::getLibraryPath().'Unus/Exception/error_codes.php';

		set_error_handler(array('Unus_Exception_Handler', 'errorHandler'));
        set_exception_handler(array('Unus_Exception_Handler', 'exceptionHandler'));

		if ($flag) {
			ini_set('output_buffering', 'true');
		}
	}

	/**
	 * Sets weither to use FirePHP for all tracking
	 *
	 * @param  boolean  flag  Flag true|false  FirePHP tracking mode
 	 */

	public static function setFirePHP($flag = true)
	{
		self::$_firePHP = $flag;

		if ($flag == true) {
			$firephp = Unus_Development_FirePhp::getInstance(true);
			$firephp->setEnabled(true);
		}
	}

	/**
	 * Returns current development mode
	 *
	 * @param  boolean
 	 */

	public static function getDevMode()
	{
		return self::$_devMode;
	}

	/**
	 * Returns weither to use FirePHP for all tracking
	 *
	 * @param  boolean
 	 */

	public static function useFirePHP($flag = true)
	{
		return self::$_firePHP;
	}
}
