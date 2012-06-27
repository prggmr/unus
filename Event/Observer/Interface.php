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

interface Unus_Event_Observer_Interface
{
    /**
    * Algorithm for checking current observer event callback aganist event triggerd
    */
    public function triggerCall();
    
    /**
    * Set observer name
    * @param  str  $str  Event Name
    */

    public function setName($str);
    
    /**
    * Return observer name
    */
    
    public function getName();
    
    /**
    * Set observer event callback
    *
    * @param  str  $str  Event Callback
    */
    
    public function setEventCall($str);
    
    /**
    * Return observer event callback
    */
    
    public function getEventCall();
    
    /**
    * Set class and method callback
    * @param  array  $str  Array of class/method to call on event trigger
    */
    
    public function setCallback($str);
    
    /**
    * Return class/method callback
    */
    
    public function getCallback();
    
    /**
    * Dispatch Event
    * @param  object  $event  Unus_Event (event triggerd)
    */
    
    public function dispatch(Unus_Event $event);
}
