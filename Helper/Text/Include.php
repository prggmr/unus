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

class Unus_Helper_Text_Include
{
	/**
	 * Transforms a string into a autoload class name
	 * EX: user_blog : User/Blog
	 *
	 * @param  string  str  	  String to convert
	 * @param  string  seperator  String Seperator: DEFAULT _
	 *
	 */

	public static function convert($str, $seperator = '_')
	{
		$str = explode('_', $str);

		$return = null;

		foreach ($str as $k => $v) {
			if (null == $return) {
				$return .= ucfirst($v);
			} else {
				$return .= '/'.ucfirst($v);
			}
		}

		return $return;
	}

	public static function ucClass($str)
	{
		$str = explode('_', $str);

		$return = null;

		foreach ($str as $k => $v) {
			$return .= (null == $return) ? ucfirst($v) : '_'.ucfirst($v);
		}

		return $return;
	}
}