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

abstract class Unus_Observer_Abstract
{
	public $request = null;

	public function __construct()
	{
		//$this->request = Zend_Controller_Front::getInstance()->getRequest();
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

