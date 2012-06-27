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

class Unus_Event extends Unus_Object_Instance
{
    /**
    * List of event observers
    */
    protected $_observers;

    /**
    * Event that is triggerd
    */

    public static $event = null;

	/**
    * Instance of self
    */
    private static $_instance = null;

	/**
	 * Returns self instance
	 *
	 * @return Unus_Event
	 */

	public static function getInstance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
    * Builds Event Observers Collection and Data Object
    *
    * @param  array  $data  Data to pass to data object
    */

    private function __construct(array $data = array())
    {
        $this->_observers = new Unus_Event_Observer_Collection();
        $this->setData($data);
    }


    /**
    * Retrieves current list of event observers
    *
    * @return object
    */

    public function getObservers()
    {
        return $this->_observers;
    }

    /**
    * Sets current event
    *
    * @param  str  $event  Event that is being triggerd
    *
    * @return this
    */

    public static function setEvent($event)
    {
        self::$event = $event;
    }

    /**
    * Adds a observer to Unus_Event_Observer_Collection
    *
    * @param  object  $observer  Unus_Event_Observer
    *
    * @return $this
    */

    public function addObserver(Unus_Event_Observer $observer)
    {
        $this->getObservers()->addObserver($observer);

        return $this;
    }

    /**
    * Clears observer collection
    *
    * @return true
    */

    public function clearObservers()
    {
        $this->_observers = null;
        return true;
    }

    /**
    * Removes event observer from collection by name
    *
    * @param  str  $name  Event observer to remove
    *
    * @return
    */

    public function clearObserverByName($name)
    {
        $this->getObservers()->clearObserverByName($name);
    }

    /**
    * Returns listing of event observers from Unus_Event_Observers_Collection
    *
    * @return object
    */

    public function getAllObservers()
    {
        return $this->getObservers()->getAllObservers();
    }

    /**
    * Dispatches Unus_Event_Observer::dispatch()
    */

    public function dispatch()
    {
        $this->getObservers()->dispatch($this);
    }

    /**
    * Returns name of current observer
    *
    * @return str
    */

    public function getName()
    {
        return (!isset($this->_data['name'])) ? null : $this->_data['name'];
    }

    /**
    * Returns event callback of current observer
    *
    * @return str
    */

    public function getEventCall()
    {
        return (!isset($this->_data['event_call'])) ? null : $this->_data['event_call'];
    }

    /**
    * Returns array of current observer class and method call
    *
    * @return array
    */

    public function getCallback()
    {
        return (!isset($this->_data['callback'])) ? null : $this->_data['callback'];
    }

    /**
    * Returns current event
    *
    * @return str
    */

    public static function getEvent()
    {
        return self::$event;
    }

    /**
    * Sets and dispatches event
    *
    */

    public function dispatchEvent($event = null)
    {
        self::setEvent($event);
        $this->dispatch();
    }

}
