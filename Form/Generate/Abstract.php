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

abstract class Unus_Form_Generate_Abstract implements Unus_Form_Generate_Interface
{

	protected $_element = null;

	protected $_content = null;

	public $errors = null;

	/**
	 * Constructs a new element generator
	 *
	 * @param  object  element  Unus_Form_Element_Abstract
	 *
	 * @return
	 */

	public function __construct($element)
	{
		if (!$element instanceof Unus_Form_Element_Abstract) {
			throw new Unus_Form_Generate_Exception('Element must be child class of Unus_Form_Element_Abstract');
		}

		$this->_element = $element;

		if (null != $element->getData('validation_errors')) {
			$this->errors = $element->getData('validation_errors');
		}
	}

	/**
	 * Generates and returns the html form tag
	 *
	 * @return  string
	 */

    public function generate()
	{
		throw new Unus_Form_Generate_Exception('generate() has not been implemented!');
	}


	/**
	 * Echoing the element will return the generated content
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */

	public function __toString()
	{
		return $this->_content;
	}

	/**
	 * Overloading
	 *
	 * We overload all calls into the element
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */

	public function __call($method, $args)
	{
		return $this->_element->$method($args);
	}

	public function __get($name)
	{
		return $this->_element->__get($name);
	}

	public function __set($name, $value)
	{
		throw new Unus_Form_Generate_Exception('Generatored Form elements cannot overloading property sets');
	}
}
