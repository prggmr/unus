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

class Unus_Form_Validate extends Unus_Object
{
	
	/**
	 * Creates a new validation object for a Unus_Form_Element
	 *
	 * @param  object  element  Unus_Form_Elements_Abstract
	 *
	 * @return
	 */
	
	public function __construct(Unus_Form_Elements_Abstract $element)
	{
		$this->setData('element', $element);
		$this->setData('validators', array());
	}
	
	/**
	 * Add a new validator to a form element
	 *
	 * @param  mixed  validator  String Name, Object or array of validator(s) to add
	 *
	 * @return  this
	 */
	public function addValidator($validator)
	{
		if ($validator instanceof Unus_Form_Validator_Abstract) {
			
			if (null != $this->getData('validators/'.$validator->getName())) {
				throw new Unus_Form_Validator_Exception('Form Validator '.$validator->getName().' is already registerd');
			}
			// Check for jQuery Validator and add if it exists
			if (method_exists($validator, 'jqueryValidate') && null != Unus_Helper_Forms_Validator_Jquery::getInstance()->formId) {
				$validator->jqueryValidate();
			}
			
			$validators = $this->getData('validators');
			
			$validators[$validator->getName()] = $validator;
			
			$this->setData('validators', $validators);
			
		} elseif (is_array($validator)) {
			foreach ($validator as $k => $v) {
				$this->addValidator($validator);
			}
		} elseif (is_string($validator)) {
			$class = Unus_Helper_Text_ClassName::convert($validator);
				$className = 'Unus_Form_Validator_'.$class;
				$class = new $className($this->getData('element'));
				$this->addValidator($class);
		} else {
			throw new Unus_Form_Validator_Exception('Form Validator must extend Unus_Form_Validation_Abstract');
		}
		
		return $this;
	}
	
	/**
	 * Validates all added form validators and sets error messages
	 */
	
	public function isValid()
	{
		$validators = $this->getData('validators');
		
		// Return true if there are not any validators to process ...
		
		if (count($validators) == 0) {
			return true;
		}
		
		$errors = array();
		
		foreach ($validators as $k => $v) {
			if (!$v->isValid($_POST[$v->getData('element_name')])) {
				$errorMsgs = $v->getMessages();
				$errors[$k] = $errorMsgs;
			}
		}
		
		if (count($errors) == 0) {
			return true;
		} else {
			$this->errors = $errors;
			return false;
		}
	}
}
