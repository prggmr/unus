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

interface Unus_Form_Decorator_Element_Interface
{

    /**
     * Parses a new element and returns a decorated string
     *
     * @param  object  element  Unus_Form_Elements_Abstract
     *
     * @return
     */

    public function __construct($element, $decorator = null);

    /**
    * Renders the element
    */

    public function render();

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

    function getDecorator($type, $element, $position, $default);
}
