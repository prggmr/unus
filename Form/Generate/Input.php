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

class Unus_Form_Generate_Input extends Unus_Form_Generate_Abstract
{
	/**
	 * Type of input html tag to generate
	 *
	 * @var  string
	 */
	private $_type = 'text';

	/**
	 * Label for input element
	 *
	 * @vvar string|null
	 */
	public $label = null;

	/**
	 * Set the type of input to generate
	 *
	 * @param  type  name  desc
	 *
	 * @return  this
	 */

	public function setType($type)
	{
		$this->_type = $type;
	}

	/**
	 * Generates a html input tag using _type as the type
	 *
	 * @return  string
	 */

    public function generate()
	{
		$label 	 = $this->_element->getData('label');
		$id    	 = $this->_element->getId();
		$name  	 = $this->_element->getData('name');
		$options = $this->_element->getData('options');
		$str     = null;

		if (null != $this->_element->getData('validation_errors')) {
			$this->errors = $this->_element->getData('validation_errors');
		}

		if (null != $label) {
			$this->label = '<label for="'.$id.'">'.__($label).'</label>';
		}

		$str .= '<input type="'.$this->_type.'" id="'.$id.'" name="'.$name.'"';

		if (null != $options) {
			foreach ($options as $k => $v) {
				$str .= ' '.$k.'="'.$v.'"';
			}
		}

		$str .= ' />';

		$this->_content = $str;
	}
}
