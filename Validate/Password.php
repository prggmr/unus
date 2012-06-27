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

class Unus_Validate_Password extends Unus_Validate_Abstract
{
	/**
	 * Does not meet length and character requirements
	 *
	 */

	const INVALID_CHAR = 'The password must must be at least 6 characters in length, contain a number, uppercase and lowercase letter';

	/**
	 * String is empty
	 *
	 */

	const STRING_EMPTY = 'Please provide a password';
	
	/**
	 * Password Regex
	 *
	 */
	
	const PASSWORD_REGEX = '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!=.*\s).{6,15}$';
	
	/**
	 * Special Character Regex Check
	 * Set self::PASSWORD_REGEX to this to check for at least on special character in the password
	 * You should also reset the INVALID_CHAR error message as to not confuse the user ...
	 *
	 */
	
	const PASSWORD_REGEX_SPECIAL = '^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@\#%^&*\(\)-+\{\}\[\]|\\\'",<.>\?])(?!=.*\s).{6,15}$';
	
	/**
	 * Validates a password
	 *
	 * @param  string  str  Password to validate
	 *
	 * @return boolean
	 */

    public function isValid($str)
    {
		$return = true;
		
		// Validate an email Address
		if ($str == '') {
			$this->setError(self::STRING_EMPTY);
			$return = false;
		}
		
		if (preg_match(self::PASSWORD_REGEX, $str) == 0) {
			$this->setError(self::INVALID_CHAR);
			$return = false;
		}

		return $return;
    }
}
