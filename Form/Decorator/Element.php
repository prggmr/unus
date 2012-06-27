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

class Unus_Form_Decorator_Element implements Unus_Form_Decorator_Element_Interface
{
    private $_element = null;

	private $_dec = array();

    /**
     * Parses a new element and returns a decorated string
     *
     * @param  object  element  Unus_Form_Elements_Abstract
     *
     * @return
     */

    public function __construct($element, $decorator = null)
    {
		if (!$element instanceof Unus_Form_Elements_Interface) {
			throw new Unus_Form_Decorator_Element_Exception('Decorator element must implement Unus_Form_Elements_Interface');
		}
        $this->_element = $element;
        $this->_dec = $decorator;
        return $this->render();
    }

    /**
    * Renders the element
    */

    public function render()
    {
        $str = null;

        $elementType = $this->_element->getData('type');
        $elementId = (null == $this->_element->getData('id')) ? $this->_element->getData('name') : $this->_element->getData('id');
        $elementName = $this->_element->getData('name');
        $options = (null == $this->_element->getData('options')) ? array() : $this->_element->getData('options');

        $str .= $this->getDecorator('row', $elementType, 'start', '<dl>');

        if (array_key_exists('label', $options) && $options['label'] != false ||
            !array_key_exists('label', $options)) {
            // label decorator
            $str .= $this->getDecorator('label', $elementType, 'start', '<dt>');

            if (array_key_exists('label', $options)) {
                $verbose_name = $options['label'];
                unset($options['label']);
            } else {
                $verbose_name = Unus_Helper_Text_UcWords::ucWords($elementName);
            }

            // Add Label
            $str .= '<label for="'.$elementId.'">'.$verbose_name.'</label>';
            $str .= $this->getDecorator('label', $elementType, 'end', '</dt>');
        }

        $str .= $this->getDecorator('element', $elementType, 'start', '<dd>');


        switch ($elementType) {
            default:
                $str .= '<input id="'.$elementId.'" name="'.$elementName.'" type="'.$elementType.'" ';
                break;

            case 'select':
                $str .= '<select id="'.$elementId.'" name="'.$elementName.'" ';
                break;
        }

        foreach ($options as $k => $v) {
            $str .= $k . '="'.$v.'" ';
        }

        switch ($elementType) {
            default:
                $str .= ' />';
                break;

            case 'select':
                $str .= '>';
                break;
        }

        $select = $this->_element->getData('select');
        if (null != $select && $elementType == 'select') {
            foreach ($select as $k => $v) {
                if (is_array($v)) {
                    $value = $v['value'];
                    $verbose_name = (array_key_exists('verbose_name', $v)) ? $v['verbose_name'] : Unus_Helper_Text_UcWords::ucWords($v['value']);
                } else {
                    $verbose_name = Unus_Helper_Text_UcWords::ucWords($v);
                    $value = $v;
                }
                $str .= '<option value="'.$value.'">'.$verbose_name.'</option>';
            }
        }
		
		switch ($elementType) {
        
            case 'select':
                $str .= '</select>';
                break;
        }

        // End
        $str .= $this->getDecorator('element', $elementType, 'end', '</dd>');
        $str .= $this->getDecorator('row', $elementType, 'end', '</dl>');

        if ($elementType == 'checkbox' || $elementType == 'radio') {
            if (null != $select) {
                // we create a new entry foreach one ... and render it with name and options :)
                $a = 0;
                foreach ($select as $k => $v) {
                    $item = null;
                    $string = 'Unus_Form_Elements_'.ucfirst($elementType).'';
                    $item = new $string($elementId);
                    $item->setOptions($v)->setId($v['value']);
                    $str .= $item->render();
                }
            }
        }


        return $str;
    }

   /**
     * Returns a element's decorator if it exists if not returns default given
     *
     * @param  string  type      Type of decorator (label,element,error) etc..
     * @param  string  element   Type of element (input, button, checkbox) etc..
     * @param  string  position  Position of the decorator (start|end)
     * @param  string  default   Default value if decorator is not found
     * 
     * @return str
     */

    public function getDecorator($type, $element, $position, $default)
    {
        $str = null;

        if (array_key_exists($type.'_'.$element.'_'.$position, $this->_dec)) {
            $str = $this->_dec[$type.'_'.$element.'_'.$position];
        } elseif (array_key_exists($type.'_'.$position, $this->_dec))  {
            $str = $this->_dec[$type.'_'.$position];
        } else {
            $str = $default;
        }

        return $str;
    }

    public function __toString()
    {
        return $this->render();
    }
}
