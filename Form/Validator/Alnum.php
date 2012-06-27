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

class Unus_Form_Validator_Alnum extends Unus_Form_Validator_Abstract
{
	
	/**
	 * Name of this validator
	 *
	 */
	
	private $_name = 'alnum';
	
	/**
	 * Error messages that can be returned
	 *
	 */
	
	const INVALID  = 'Invalid type, please enter a string';
	const NOT_ALNUM = 'Please enter numbers only';
	const STRING_EMPTY    = 'This field is required';
	
	/**
	 * Parses the validator object and adds jQuery Validation to the form
	 *
	 */
	
	
	public function jqueryValidate()
	{
		// Add form elements for validation
        $validate = Unus_Helper_Forms_Validator_Jquery::getInstance();
        $validate->addElement($this->getData('element_name'), array('required' => 'true', 'digits' => 'true'));
        $validate->addMessage($this->getData('element_name'), array('required' => __(self::STRING_EMPTY), 'alpha_num' => __(self::NOT_ALNUM)));
	}
	
	/**
	 * Checks if value is not empty
	 *
	 * @param  str  val  String to check
	 *
	 * @return  boolean
	 */
	
	public function isValid($val)
	{
		$return = true;
		
		if (!is_string($val) || !is_int($val) || !is_float($val)) {
			$this->setError(self::INVALID);
			$return = false;
		}
		
		if ($val == '') {
			$this->setError(self::STRING_EMPTY);
			$return = false;

		}
		
		if (!ctype_alnum($val)) {
			$this->setError(self::NOT_ALNUM);
			$return = false;
		}
		
		return $return;
	}
	
	/**
	 * Returns the name of the form validator
	 */
	
	public function getName()
	{
		return $this->_name;
	}
}
