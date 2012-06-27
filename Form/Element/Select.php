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

class Unus_Form_Element_Select extends Unus_Form_Element_Abstract
{
    
    public function __construct()
    {
        $this->_setType('select');
    }
    
    /**
     * Adds a new select option
     *
     * @param  string  $name   Name of option
     * @param  string  $value  Value of option
     *
     * @return  $this
     **/
    
    public function addOption($name, $value)
    {
        $this->setData('select', array($name => $value));
        return $this;
    }
    
    /**
     * Adds a new array of select options (array('key' => 'value')) mapped
     *
     * @param  array  $array  Array of options
     *
     * @return $this
     **/
    
    public function addOptions($array)
    {
        foreach ($array as $k => $v) {
            $this->addOption($k, $v);
        }
        return $this;
    }
    
    /**
     * Add a new optgroup options must be array('key' => 'value')
     *
     * @param  string  $name    Optgroup name
     * @param  array   $options Options to be in optgroup 
     *
     * @return $this
     **/
    
    public function addOptgroup($name, $options)
    {
        $this->setData('optgroups', array($name => $options));
        return $this;
    }
}
