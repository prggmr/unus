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

class Unus_Validate_Alnum extends Unus_Validate_Abstract
{
	/**
	 * Error messages that can be returned
	 *
	 */

	public static $INVALID  = '%s is a invalid type, please enter a string';
	public static $NOT_ALNUM = '%s is allowed only numbers';
	public static $STRING_EMPTY    = '%s cannot be left blank';

	/**
	 * Validates a string to be all numbers
	 *
	 * @param  string  str  String to validate
	 *
	 * @return boolean
	 */

    public function isValid($str)
    {
		$return = true;

		if (!is_string($val) || !is_int($val) || !is_float($val)) {
			$this->setError(self::$INVALID);
			$return = false;
		}

		if ($val == '') {
			$this->setError(self::$STRING_EMPTY);
			$return = false;

		}

		if (!ctype_alnum($val)) {
			$this->setError(self::$NOT_ALNUM);
			$return = false;
		}

		return $return;
    }
}
