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

class Unus_Event_Observer_Collection
{
    /**
    * Listing of observers
    */

    protected $_observers;

    /**
    * Clean list of observers
    *
    */

    public function __construct()
    {
        $this->_observers = array();
    }

    /**
    * Adds observer to $_observer listing
    *
    * @param  object  $observer  Unus_Event_Observer
    */

    public function addObserver(Unus_Event_Observer $observer)
    {
        $this->_observers[$observer->getName()] = $observer;
    }

    /**
    * Cleans observer list
    */

    public function clearObservers()
    {
        $this->_observers = null;
    }

    /**
    * Remove observer by name
    *
    * @param  str  $name  Name of observer to be removed
    *
    * @return
    */

    public function clearObserverByName($name)
    {
        $this->getObservers()->clearObserverByName($name);
    }

    /**
    * Returns list of all observers
    *
    * @return array
    */

    public function getAllObservers()
    {
        return $this->_observers;
    }

    /**
    * Dispatches Event
    *
    * @param  object  $event  Unus_Event (event trigger)
    *
    * @return this
    */

    public function dispatch(Unus_Event $event)
    {
        foreach ($this->_observers as $observer) {
            $observer->dispatch($event);
        }
        return $this;
    }
}
