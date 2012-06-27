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

interface Unus_Form_Validator_Interface
{
	/**
	 * Validates the given input
	 *
	 * @param  mixed  val  Variable to validate
	 *
	 * @return boolean
	 */
	public function isValid($val);
	/**
	 * Returns error messages given from isValid
	 *
	 * @return  mixed
	 */
	public function getMessages();
	/**
	 * Sets a error message, if error messages exist appends current
	 * into an array
	 *
	 * @param  string  str  Error message
	 *
	 * @return
	 */
	public function setError($str);
	/**
	 * Sets an array of error messages to setError()
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */
	public function setErrors($errors);
	/**
	 * Returns the name of the validator
	 *
	 */
	public function getName();
}