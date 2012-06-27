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

/**
 * @see Unus_Event_Interface
 */

class Unus_Event_Observer extends Unus_Object implements Unus_Event_Observer_Interface
{
	/**
	 * Creates a new event observer
	 *
	 * @param  string  name  	 Name of event observer
	 * @param  array   callBack  Array of [0] => class, [1] => method to call
	 * @param  string  event     Event that triggers this observer
	 *
	 * @return
	 */

	public function __construct($name, $callBack, $event)
	{
		$this->setName($name)->setCallback($event)->setEventCall($callBack);
		return $this;
	}

    public function triggerCall()
    {
        // Global Event
        if ($this->getEventCall() == null) {
            return true;
        } elseif (stripos($this->getEventCall(), '(')) {
            // Regex Event
            $pattern = '#^' . $this->getEventCall() . '$#i';
            if (preg_match($pattern, Unus_Event::getEvent()) != 0) {
             return true;
            }
        } else {
            // Strict Event
            return Unus_Event::getEvent() === $this->getEventCall();
        }
    }

    public function setName($str)
    {
        $this->setData('name', $str);
        return $this;
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setEventCall($str)
    {
        $this->setData('event_call', $str);
        return $this;
    }

    public function getEventCall()
    {
        return $this->getData('event_call');
    }

    public function setCallback($str)
    {
        $this->setData('callback', $str);
        return $this;
    }

    public function getCallback()
    {
        return $this->getData('callback');
    }

    public function dispatch(Unus_Event $event)
    {
        if (!$this->triggerCall()) {
            return false;
        }
	
        $callback = $this->getCallback();
        $class = $callback[0];
        $method = $callback[1];

        // Non-static method call
		//if (method_exists($class, 'getInstance')) {
		//	$classObject = $class::getInstance();
		//} else {
		$classObject = new $class();
		//}

        $classObject->$method($this);
    }
}
