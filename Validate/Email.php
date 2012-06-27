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

class Unus_Validate_Email extends Unus_Validate_Abstract
{
	/**
	 * Regex used for checking email address
	 *
	 */

	const EMAIL_REGEX = '/[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)*\@[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)+/i';

	/**
	 * DNS Check failure
	 *
	 */

	const ERROR_DNS = 'DNS Hostname lookup failed';

	/**
	 * Regex check failure
	 *
	 */

	const ERROR_REGEX = 'Regular expression check failed';
	
	/**
	 * String is Empty
	 *
	 */
	const STRING_EMPTY = 'Email cannot be empty';
	
	/**
	 * Lookup DNS information verification using checkdnsrr()
	 */
	
	private $_checkDns = true;
	
	/**
	 * Set flag for checking DNS status information
	 *
	 * Defaults to true
	 */
	
	public function checkDns($flag = false) {
		if (is_bool($flag)) {
			$this->_checkDns = $flag;
		} else {
			$this->_checkDns = true;
		}
		return $this;
	}
	
	/**
	 * Validates a email address
	 *
	 * Can check DNS record information (*Nix Platforms only unless running PHP5.3)
	 *
	 * @param  str   email  	Email address to validate
	 * @param  type  boolean    Flag to check DNS record of domain ( Note works only on Unix Systems until PHP 5.3 )
	 *
	 * @return boolean
	 */

    public function isValid($str)
    {
		$return = true;
		
		if ($str == '') {
			$this->setError(self::STRING_EMPTY);
			$return = false;
		}
		
		// Validate an email Address
		if (preg_match(self::EMAIL_REGEX, $str)) {
			$email = explode('@', $str);
			if ($this->_checkDns) {
				if (!checkdnsrr($email[1], 'MX')) {
					$this->setError(self::ERROR_DNS);
					return false;
				}
				return true;
			} else {
				return true;
			}
		}

		$this->setError(self::ERROR_REGEX);
		return false;
    }
}
