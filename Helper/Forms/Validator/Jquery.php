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

class Unus_Helper_Forms_Validator_Jquery
{

    /**
     * FormId referenced in the validator class
     */

    public $formId = null;

    /**
     * Validator element rules
     */

    private $_elementRules = array();

    /**
     * Validator element rule messages
     */

    private $_elementMessages = array();

    /**
     * Instance of self
     */
    private static $_instance = null;

    /**
     * Returns instance of Unus_Form_jQuery_Validator
     */
    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Destructs self upon self::build() to allow for multiple instances
     * after validator has been built and returned
     */
    private static function _die()
    {
        self::$_instance = null;
    }


    /**
     * @param  string  $password
     * @return string
     */

    public function setId($id)
    {
        $this->formId = $id;
    }

    /**
     * Add a element to the validator rule set
     *
     * @param  string  $name
     * @param  mixed   $validate
     */

    public function addElement($name, $validate)
    {
        $this->_elementRules[$name] = $validate;
    }

    /**
     * Add a message to the validator message set
     *
     * @param  string  $element
     * @param  string   $message
     */

    public function addMessage($element, $message)
    {
        $this->_elementMessages[$element] = $message;
    }

    /**
     * Builds the jQuery Validator class
     *
     * @param  string  $element
     * @param  string   $message
     */

    public function build()
    {
        $str = '<script type="text/javascript">$().ready(function() {$("#'.$this->formId.'").validate({rules: {
                ';
        // Build Rule Sets

        $totalRules = count($this->_elementRules);
        $a = 0;

        foreach ($this->_elementRules as $k => $v)
        {
            $a++;
            // Start of rule
            $str .= $k . ':';

            if (is_array($v))
            {
                $str .= ' {';
                $totalElementRules = count($v);
                $b = 0;
                foreach ($v as $key => $value)
                {
                    $b++;
                    $str .= $key .': '.$value.'';
                    if ($totalElementRules != $b) $str .= ',';
                }
                $str .= '}';
                if ($totalRules != $a) $str .= ',';
            }
            else
            {
                $str .= ''.$v.'';
                if ($totalRules != $a) $str .= ',';
            }
        }
        $str .= '},messages: {';

        $a = 0;
        foreach ($this->_elementMessages as $k => $v)
        {
            $a++;

            $str .= $k.' :';
            if (is_array($v))
            {
                $totalElementRules = count($v);
                $b = 0;
                $str .= ' {';
                foreach ($v as $key => $value)
                {
                    $b++;
                    $str .= $key .': "'.$value.'"';
                    if ($totalElementRules != $b) $str .= ',';
                }
                $str .= '}';
                if ($totalRules != $a) $str .= ',';
            }
            else
            {
                $str .= '"'.$v.'"';
                if ($totalRules != $a) $str .= ',';
            }
        }

        $str .= '}})});</script>';

        self::_die();

        return $str;
    }
}

?>