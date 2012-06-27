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

abstract class Unus_Form_Elements_Abstract extends Unus_Object implements Unus_Form_Elements_Interface
{
	// We Dont need any parsing
    public function __construct()
    {}

	protected function _setType($type)
	{
		$this->setData('type', $type);
	}

	public function setName($name)
	{
		$this->setData('name', $name);
	}
	
	public function getName()
	{
		return $this->getData('name');
	}

    /**
     * Sets options for select|radio|checkbox elements
     *
     * @param  array  options  Array of values for selectable options
     */
    public function addOptions($options)
    {
        $allowed = array('checkbox', 'select', 'radio');
        if (!in_array($this->getData('type'), $allowed)) {
            throw new Unus_Form_Elements_Exception('Mutliple select options are only avaliable for checkbox, select and radio elements');
        }

        if (!is_array($options)) {
            throw new Unus_Form_Elements_Exception('addOptions expected array '.gettype($options).' given');
        }

        $add = array();

        foreach ($options as $option) {
            $add[] = $option;
        }

        $this->setData('select', $add);

        return $this;
    }

	/**
     * Sets a element config option
     */
    public function setConfig($option, $value)
    {
		$options = $this->getData('options');

		$options[$option] = $value;

        $this->setData('options', $options);

        return $this;
    }

    /**
     * Sets element options
     */
    public function setConfigArray($array)
    {
		$options = $this->getData('options');

        if (!is_array($array)) {
            throw new Unus_Form_Elements_Exception('setOptions expected array '.gettype($array).' given');
        }

        foreach ($array as $k => $v) {
            $options[$k] = $v;
        }

        $this->setData('options', $options);

        return $this;
    }

    /**
     * Renders a form element
     */
    public function render($decorator, object $decoratorObj = null)
    {
		if (null == $decoratorObj) {
			$dec = new Unus_Form_Decorator_Element($this, $decorator);
		} elseif (!$decoratorObj instanceof Unus_Form_Decorator_Element_Interface) {
			throw new Unus_Form_Elements_Exception('Form Element decorator must implement Unus_Form_Decorator_Element_Interface');
		}
        return $dec;
    }

	/**
	 * Sets element as required an adds validator
	 *
	 * 
	 */

	public function addValidator($validator = null)
	{
		if (null == $this->getData('validate')) {
			$this->setData('validate', new Unus_Form_Validate($this));
		}
		
		if (null == $validator) {
            // automatically add the validator for this option as required by default
            $validator = new Unus_Form_Validator_Required(); 
        } else {
            $this->getData('validate')->addValidator($validator);
        }
		
		$this->setData('required', true);
		return $this;
	}
	
	public function addValidators($validators)
	{
		if (is_array($validators)) {
			foreach ($validators as $k => $v) {
				$this->addValidator($v);
			}
			return $this;
		}
		throw new Unus_Form_Elements_Exception('addValidators expected array input, recieved '.gettype($validators).'; To set a single validator use the plural method addValidator()');
	}
	
	/**
	 * Sets validation for jQuery Validate
	 *
	 * This will not validate information once the form is submitted server-side
	 * it is recommended to use this ONLY if you are 100% sure
	 * all users will have javascript enabled
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */
	public function setjQueryValidator()
	{
		
	}
	
    /**
     * The following methods are convience factors and can also be set by using setConfigArray() or setConfig
     * with key => value mappings
     */

    /**
     * Sets the display string for element label. Set to false to display no label
     *
     * @param  mixed  string  String to display for element label
     *
     * @return  this
     */
    public function setLabel($str)
    {
        $options = $this->getData('options');
        $options['label'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the value for element
     *
     * @param  string  string  Value of element
     *
     * @return  this
     */
    public function setValue($str)
    {
        $options = $this->getData('options');
        $options['value'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the class for element
     *
     * @param  string  string  Class of element
     *
     * @return  this
     */
    public function setClass($str)
    {
        $options = $this->getData('options');
        $options['class'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the onclick for element
     *
     * @param  mixed  string  Value of element
     *
     * @return  this
     */
    public function setOnClick($str)
    {
        $options = $this->getData('options');
        $options['onclick'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the onfocus for element
     *
     * @param  mixed  string  Value of element
     *
     * @return  this
     */
    public function setOnFocus($str)
    {
        $options = $this->getData('options');
        $options['onfocus'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the onblur for element
     *
     * @param  mixed  string  onblur of element
     *
     * @return  this
     */
    public function setOnBlur($str)
    {
        $options = $this->getData('options');
        $options['onblur'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the help text for element. HTML is displayed as given
     *
     * @param  mixed  string  Help text for element
     *
     * @return  this
     */
    public function setHelp($str)
    {
        $options = $this->getData('options');
        $options['help'] = $str;
        $this->setData('options', $options);
        return $this;
    }

    /**
     * Sets the id for element, left blank the name will be used
     *
     * @param  mixed  string  Id of element
     *
     * @return  this
     */
    public function setId($str)
    {
        $this->setData('id', $str);
        return $this;
    }
}
