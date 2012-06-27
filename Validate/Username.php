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

class Unus_Validate_Username extends Unus_Validate_Abstract
{
	/**
	 * Username is not alphanum
	 *
	 */

	const ERROR_ALNUM = 'Username may contain only numbers and letters';

	/**
	 * Username contains Disallowed characters
	 *
	 */

	const ERROR_DISALLOWED_CHAR = 'Invalid characters given';

	/**
	 * Username is shorter than the minimum length required
	 *
	 */

	const ERROR_LENGTH_MIN = 'Length is less than minimum allowed';
	
	/**
	 * Username is empty
	 *
	 */

	const STRING_EMPTY = 'Please provide a username';

	/**
	 * Username is longer than the maximum length allowed
	 *
	 */

	const ERROR_LENGTH_MAX = 'Length is greater than maximum allowed';

	/**
	 * Username contains disallowed words
	 *
	 */

	const ERROR_DISALLOWED = 'Username is disallowed';


	/**
	 * Disallowed characters
	 *
	 */

	public static $char = null;

	/**
	 * Minimum length a username must be
	 *
	 */

	public static $minLength = 4;

	/**
	 * Maximum length a username is allowed to be
	 *
	 */

	public static $maxLength = 16;

	/**
	 * Listing of disallowed words or usernames
	 * By default the only username is Root
	 *
	 */

	public static $disallow = array('root');

	/**
	 * Validates a username
	 *
	 * Allows for check aganist database user's, disallowed characters,
	 * minimum Length, maximum length and disallowed usernames/words
	 *
	 * @param  string  str   	String for username to validate
	 *
	 * @return  boolean
	 */

    public function isValid($str)
    {
		$return = true;
		
		if (strlen($str) < self::$minLength) {
			$this->setError(self::ERROR_LENGTH_MIN);
			$return = false;
		}

		if (strlen($str) > self::$maxLength) {
			$this->setError(self::ERROR_LENGTH_MAX);
			$return = false;
		}

		if (is_array(self::$disallow)) {
			foreach (self::$disallow as $v) {
				if (stripos($v, $str)) {
					$this->setError(self::ERROR_DISALLOWED);
					$return = false;
				}
			}
		}
		
		if (!ctype_alnum) {
			$this->setError(self::ERROR_ALNUM);
			$return = false;
		}

		if (is_array(self::$char)) {
			foreach (self::$char as $v) {
				if (stripos($v, $str)) {
					$this->setError(self::ERROR_DISALLOWED_CHAR);
					$return = false;
				}
			}
		}

		return $return;
    }
}
