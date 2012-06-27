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

class Unus_Form_Generate_Form extends Unus_Form_Generate_Abstract
{

	/**
	 * HTML Tags for a form
	 *
	 * Open
	 * Close
	 * jQuery Validator
	 *
	 * @var  string
	 */
	public $open_tag    = null;
	public $close_tag   = null;
	public $jQuery 	    = null;
	public $errors      = null;
	public $errorGroups = null;

	/**
	 * Generates a html fieldset tag
	 *
	 * @return  string
	 */

    public function generate()
	{
		$id    	 = $this->_element->getData('id');
		$options = $this->_element->getData('options');
		$enctype = (isset($options['enctype'])) ? $options['enctype'] : 'application/x-www-form-urlencoded';
		$jquery  = $this->_element->getjQueryValidator();
		$name 	 = $this->getName();
		$method  = (null == $options['method']) ? Unus_Form::POST : $options['method'];

		if ($this->_element->getjQueryValidate()) {

			$jValidator = $this->_element->getData('jqueryValidator');
			$jValidator->attachHeaders();
			$this->jQuery = $jValidator->build();
		} else {
			$this->jQuery = null;
		}

		if (null != $this->_element->errors) {
			$this->errors = $this->_element->errors;
			$this->errorGroups = $this->_element->errorGroups;
		}

		$array['open_tag'] = '<form enctype="'.$enctype.'" id="'.$id.'" name="'.$name.'" method="'.$method.'"';

		if (null != $options) {
			foreach ($options as $k => $v) {
				$array['open_tag'] .= ' '.$k.'="'.$v.'"';
			}
		}

		$array['open_tag'] .= '>';

		$array['close_tag'] = '</form>';

		$this->open_tag  = $array['open_tag'];
		$this->close_tag = $array['close_tag'];

		return $this;
	}

	/**
	 * Form object cannot be converted to a string
	 *
	 * @return
	 * @throws Unus_Form_Generate_Fieldset_Exception
	 */

	public function __toString()
	{
		throw new Unus_Form_Generate_Fieldset_Exception('Form Tag generator cannot be converted to string; Use open_tag, close_tag, jQuery object properties');
	}
}
