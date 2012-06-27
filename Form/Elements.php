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

class Unus_Form_Elements extends Unus_Object
{
    
    public function __construct()
    {
        $this->setData('registry', array());
    }
    
    /**
     * Adds a new form element
     *
     * @param  string  type  Type of element
     * @param  string  name  Name of form element
     *
     * @return Unus_Form_Element
     */
    public function addElement($type, $name)
    {
        $class = 'Unus_Form_Elements_'.ucfirst($type);
        $element = new $class($name);
        
        $registry = $this->getData('registry');
        $registry[$name] = $element;
        $this->setData('registry', $registry);
    }
    
    /**
     * Gets a element in the registry
     *
     * @param  string  name  Name of element
     *
     * returns Unus_Form_Element_Interface
     */
    public function getElement($name)
    {
        return $this->getData('registry/'.$name);
    }
    

    /**
     * Removes a form element
     *
     * @param  string  name  Name of element to remove
     *
     * @return this
     */
    public function removeElement($name)
    {
        $registry = $this->getData('registry');
        unset($registry[$name]);
        $this->setData('registry', $registry);
    }
    
    /**
     * Renders the form and returns printable string
     *
     * return string
     */
    
    public function render()
    {
        $render = null;
        
        foreach ($this->getData('registry') as $k => $v)
        {
           $render .= $v->render();
        }
        
        return $render;
    }
}
