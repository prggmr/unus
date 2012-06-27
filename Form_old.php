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

class Unus_Form extends Unus_Data
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

    protected static $_formValidator = object;

    /**
     * Name of current element
     */

    private $_element = null;

    /**
    * Constructs Unus_Form
    *
    * @param  string  name     Name of this form
    * @param  string  action   URL for form to send its submittion
    * @param  array   options  Options for form
    *
    * return Unus_Form
    */

    public function __construct($name, $action, $options = null)
    {
        parent::__construct($options);

        if (null != $options) {
            $this->setOptions($options);
        }

        // Set the jQuery Validator Object
        $this->setData('validator', Unus_Form_jQuery_Validator::getInstance());
        $this->getData('validator')->setId($name);

        $this->setData('name', $name);
        $this->setData('action', $url);
        $this->setData('elements', new Unus_Form_Element());

        return $this;
    }

    /**
     * Adds a array of form options
     *
     * @param  array  options  Array of key => value mapped form options
     *
     * @return this
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new Unus_Form_Exception('setOptions() expected array '.gettype($options).' given');
        }

        foreach ($options as $k => $v) {
            $this->setOption($k, $v);
        }

        return $this;
    }

    /**
     * Adds a new form option
     *
     * @param  string  option  Name of the form option
     * @param  string  value   Value of option
     *
     * @return  this
     */
    public function setOption($option, $value)
    {
        $options = $this->getData('options');
        if (null == $options) {
            // set the options
            $this->setData('options', array());
        }

        $options[$option] = $value;
        $this->setData('options', $options);

        return $this;
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
     * Adds a new form element
     *
     * @param  string  type  Type of element
     * @param  string  name  Name of form element
     *
     * @return this
     */
    public function addElement($type, $name)
    {
        if (!class_exists('Unus_Form_Element_'.ucfirst($type), true)) {
            throw new Unus_Form_Exception('Element type :'.$type.' is a unknown form element');
        }

        $this->getData('elements')->addElement($type, $name);
        $this->_element = $name;

        return $this;
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
        $this->getData('elements')->removeElement($name);

        return $this;
    }

    /**
     * Sets rendering options for form element
     *
     * @param  array  options  Array of key => value mapped element options
     * @param  string name     Name of form element to add options leave blank to use last added element
     *
     * @return this
     */

    public function setElementOptions($options, $name = null)
    {
        if (!is_array($options)) {
            throw new Unus_Form_Exception('setElementOptions() expected array '.gettype($options).' given');
        }

        // set options to use for the element while rendering
        if (null == $name) {
            if (null == $this->_element) {
                throw new Unus_Form_Exception('Unus_Form attempted to set element options and no element has been specified');
            }
            $name = $this->_element;
        }

        $this->getData('elements/registry/'.$name)->setOptions($options);

        return $this;
    }


    /**
     * Adds a new select option for a select|radio|checkbox form elements
     *
     * @param  array  options  Array of value element selection options
     * @param  string name     Name of form element to add options leave blank to use last added element
     *
     * @return
     */
    public function addElementOptions($options, $name = null)
    {
        if (!is_array($options)) {
            throw new Unus_Form_Exception('addElementOptions() expected array '.gettype($options).' given');
        }

        if (null == $name) {
            if (null == $this->_element) {
                throw new Unus_Form_Exception('Unus_Form attempted to set element options and no element has been specified');
            }
            $name = $this->_element;
        }

        $this->getData('elements/registry/'.$name)->addOptions($options);

        return $this;
    }

    /**
     * Sets a element status to required and automatically generates a
     * added jQuery Form validator based on type
     *
     * @param  mixed    param    Flag to set this element to required
     * @param  string   name     Name of form element to add options leave blank to use last added element
     *
     * @return this
     */
    public function setRequire($param = true, $name = null)
    {
        if (null == $name) {
            if (null == $this->_element) {
                throw new Unus_Form_Exception('Unus_Form attempted to set element options and no element has been specified');
            }
            $name = $this->_element;
        }

        if (null == $this->getData('required')) {
            $this->setData('required', true);
        }

        $this->getData('elements/registry/'.$name)->setRequire($param);

        return $this;
    }

    /**
     * Renders the form into a string for output
     *
     * return string
     */
    public function render()
    {
        $decorator = new Unus_Form_Decorator_Form($this);
        $str = $decorator->renderStart();
        $str .= $this->getData('elements')->render();
        $str .= $decorator->renderEnd();

        return $str;
    }
}
