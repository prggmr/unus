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

abstract class Unus_Form_Validator_Abstract extends Unus_Object implements Unus_Form_Validator_Interface
{
	/**
	 * Creates a new Unus Form Validator Object
	 *
	 * @param  object  element  Element object that validator belongs to
	 */
	
	public function __construct($element)
	{
		if (!$element instanceof Unus_Form_Elements_Interface) {
			throw new Unus_Form_Validator_Exception('Element must implement Unus Unus_Form_Elements_Interface');
		}
		$this->setData('element_name', $element->getName());
		$this->setData('element', $element);
	}
	
	/*
	 * Returns all errors in array
	 *
	 * @return array
	 */
	
	public function getMessages()
	{
		return $this->getData('errors');	
	}
	
	/**
	 * Adds a new error to error array
	 *
	 * @param  str  str  Error to add
	 *
	 * @return this
	 */
	
	public function setError($str)
	{
		$errors = $this->getData('errors');
		
		if (null == $errors) {
			$errors = array();
		}
		
		$errors[] = $str;
		$this->setData('errors', $errors);
		
		return $this;
	}
	
	/**
	 * Adds a array of errors to error string
	 *
	 * @param  array  errors  Array of errors to add
	 *
	 * @return this
	 */
	
	public function setErrors($errors)
	{
		if (is_array($errors)) {
			throw new Unus_Form_Validator_Exception('Validator expected error message as array input '.gettype($errors).' given');
		}
		
		foreach ($errors as $k => $v) {
			$this->setError($v);
			
		}
		
		return $this;
	}
	
}
