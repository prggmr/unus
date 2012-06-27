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

class Unus_Form_Decorator_Form
{
    private $_form = null;
    
    /**
     * Parses a new element and returns a decorated string
     *
     * @param  object  element  Unus_Form_Elements_Abstract
     *
     * @return
     */
    
    public function __construct(Unus_Form $form)
    {
        $this->_form = $form;
        $this->_dec = Unus_Form::getDecorator();
    }
    
    public function renderStart()
    {
        $str = null;
        // Check for jQuery Validation and append to form
        if ($this->_form->getjQueryValidate()) {
			$validate = Unus_Helper_Forms_Validator_Jquery::getInstance()->build();
            $str .= $validate;
        }
        $str .= '<form action="'.$this->_form->getData('action').'" name="'.$this->_form->getData('name').'" id="'.$this->_form->getData('name').'"';
        $method = false;
        $enctype = false;
        $options = $this->_form->getData('options');
        if (is_array($options)) {
            foreach ($options as $k => $v) {
                if ($k == 'method') {
                    $method = true;
                }
                if ($k == 'enctype') {
                    $enctype = true;
                }
                $str .= ' '.$k.'="'.$v.'"';
            }
        }
        
        if ($method == false) {
            $str .= ' method="post"';
        }
        if ($enctype == false) {
            $str .= ' enctype="application/x-www-form-urlencoded"';
        }
        
        $str .= '>';
        
        $str .= $this->_dec['form_start'];
        
        return $str;
        
    }
    
    public function renderEnd()
    {
        $str = $this->_dec['form_end'];
        
        $str .= '</form>';
        
        return $str;
    }
}
