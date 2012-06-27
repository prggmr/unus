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

class Unus_Form_Element_Unus_Password extends Unus_Form_Element_Abstract
{
    /**
     * Automatically add a Password field with Label/Desc/Validator
     * Pre-Added
     *
     * @param  bool  required  Set this element as required
     * @param  bool  special   Force the use of special characters in password
     *
     */
    
    public function __construct()
    {
        $this->_setType('password');
        $this->setName('password');
        $this->setLabel(__('Password'));
        $this->addValidator(new Unus_Form_Validator_Password($this));
    }
}
