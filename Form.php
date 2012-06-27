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

class Unus_Form extends Unus_Object
{
    /**
    * Path to custom form decorater
    */

    public static $decorator = array(
           'form_start' => '<div class="form">',
           'form_end' => '</div>',
        );

    /**
    * jQuery form validator object
    */

    protected static $_formValidator = null;

    /**
     * Name of current element
     */

    private $_element = null;
	
	private $_validateJquery = false;

    /**
    * Constructs Unus_Form
    *
    * @param  string  name     Name of this form
    * @param  string  action   URL for form to send its submittion
    * @param  array   options  Options for form
    *
    * return Unus_Form
    */

    public function __construct($name, $action = '', $options = null)
    {
        
        if (null != $options) {
            $this->setOptions($options);
        }
        
        $this->setData('name', $name);
        $this->setData('action', $action);
        $this->setData('elements', array());
    }

    /**
     * Adds a new form element
     *
     * @param  object  $element  Unus_Form_Elements_Abstract
     *
     * @return Unus_Form_Elements_Abstract
     */

    public function addElement($element, $name = null)
    {
		if (is_object($element)) {
			if (!$element instanceof Unus_Form_Elements_Abstract) {
				throw new Unus_Form_Exception('Form Element Object must inherit Unus_Form_Element_Abstract');
			}
		} else {
			$object = 'Unus_Form_Elements_'.ucfirst($element);
			$element = new $object();

			if (!$element instanceof Unus_Form_Elements_Interface) {
				throw new Unus_Form_Exception('Form Element Object must inherit Unus_Form_Element_Interface');
			}
		}
		
		if (null != $name) {
			$element->setName($name);
		} else {
			// Try to get name from the element
			$name = $element->getData('name');
			if (null == $name) {
				throw new Unus_Form_Exception('Cannot add element as it does not have a name, a name must be specified');
			}
		}

        $elements = $this->getData('elements');

        if ($this->elementExists($name)) {
			throw new Unus_Form_Exception('Form Element '.$name.' already exists');
		}

		$elements[$name] = $element;

		$this->setData('elements', $elements);

		return $elements[$name];
    }
	
	/**
	 * Sets jQuery Validation, once set all elements that have
	 * a jquery validator attatched will be automatically appended
	 * to the form
	 *
	 * @param  bool  flag   Flag true|false for jQuery form validation
	 *
	 * @return
	 */
	
	public function setjQueryValidate($flag = true)
	{
		if (is_bool($flag)) {
			$this->_validateJquery = $flag;
			if ($flag) {
				// Set the jQuery Validator Object
				$this->setData('jqueryValidator', Unus_Helper_Forms_Validator_Jquery::getInstance());
				$this->getData('jqueryValidator')->setId($this->getData('name'));
			}
			return $this;
		}
		throw new Unus_Form_Exception('setjQueryValidate expected boolean '.gettype($flag).' given');
	}
	
	/**
	 * Returns flag for jQuery validation
	 *
	 * @return  boolean
	 */
	public function getjQueryValidate()
	{
		return $this->_validateJquery;
	}
	
	/**
	 * Checks if a element exists for this form
	 *
	 * @param  string  name  Name of element
	 *
	 * @return  boolean
	 */

    public function elementExists($name)
    {
        if ($this->getData('elements/'.$name) != null) {
            return true;
        }
        return false;
    }

    /**
     * Sets the decorator options for form render
     *
     * Example Form Decorator
        ------------------------------
        The goal of the form decorator is to be as simple and flexiable as possiale
        unlike other framework decorators.
        We will use smart loading and check for possiabilities of each element before it will use
        element_start|end as its default the same applies to labels.

        array(
           'form_start' => '<div class="form">',
           'form_end' => '</div>',
           'label_start' => '<div class="elementRow"><div class="label">',
           'label_end' => '</div>
           'element_start' => '<div class="element">',
           'element_end' => '</div></div> <div class="help-line"></div>',
           'element_submit_label_start' => '',
           'element_submit_label_end' => '',
           'element_submit_start' => '<div class="submit-button">',
           'element_submit_end' => '</div>'
        )
     *
     * @param  array  decorator  Array containing information for decorator
     *
     * @return true
     */

    public static function setDecorator($decorator)
    {
        self::$decorator = $decorator;
    }

    /**
    * Returns the decorator file path
    * By Default we check for it in library/Form/Decorator/default.php
    *
    * @param  str  $file  File to parse
    *
    */

    public static function getDecorator()
    {
        return self::$decorator;
    }

    /**
     * Removes a form element
     *
     * @param  string  name  Name of element to remove
     *
     * @return
     */
    public function removeElement($name)
    {
        if ($this->elementExists($name)) {
			$elements = $this->getData('elements');
			unset($elements[$name]);
			$this->setData('elements', $elements);
		}
        return $this;
    }

    /**
     * Renders the form into a string for output
     *
     * @param  object  decoratorForm     Decorator object used to parse the form body
     * @param  object  decoratorElement  Decorator object used to parse the form elements body 
     * 
     * @return  string
     */
    public function render(object $decoratorForm = null, object $decoratorElement = null)
    {
		if (null == $decoratorForm) {
			$decorator = new Unus_Form_Decorator_Form($this);
		} elseif (!$decorator instanceof Unus_Form_Decorator_Form_Interface) {
			throw new Unus_Form_Exception('Form Decorator must implement Unus_Form_Decorator_Form_Interface');
		}
		
        $str = $decorator->renderStart();

        foreach ($this->getData('elements') as $k => $v) {
			$str .= $v->render(self::$decorator);
		}

        $str .= $decorator->renderEnd();
        return $str;
    }
	
	/**
	 * Parses the form elements and checks for validation objects attachted to a element
	 * loops through each element and checks if it validates returning any recieved
	 * errors
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */
	
	public function isValid()
	{
		if (count($this->getData('elements')) == 0) {
			throw new Unus_Form_Exception('Unus Form cannot validate elements, no form elements have been added');
		}
		
		
	}
}
