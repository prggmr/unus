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

class Unus_Role_Object extends Unus_Object
{

    /**
     * Creates a new Resource object & add required param
     *
     */

    public function __construct($roleId, $identifier, $level)
    {
        $this->setId($roleId);
        $this->setIdentifier($identifier);
        $this->setLevel($level);
    }

    /**
     * Sets the resources internal numeric Id
     *
     * @param  int  int  Internal numerical database identifier
     *
     * @return self
     */

    public function setId($int)
    {
        $this->setData('id', $int);
        return $this;
    }

    /**
     * Sets the interal identifier
     *
     * @param  string  str  Name of the role
     *
     * @return self
     */

    public function setIdentifier($str)
    {
        $this->setData('identifier', $str);
        return $this;
    }

    /**
     * Sets the role parentId
     *
     * @param  mixed  parent  ParentId for this Role
     *
     * @return
     */

    public function setParent($parent)
    {
        $this->setData('parent', $parent);
        return $this;
    }

    /**
     * Sets the resource permission level
     * 0 - Guest
     * 1 - User
     * 2 - Admin
     * 3 - Root
     *
     * @param  int  int  Numerical Level for this level ( 0-3 )
     *
     * @return
     */

    public function setLevel($int = 0) {
        if ($int > 4) {
            throw new Unus_Role_Object_Exception('Role level must be a interger value 1-4; '.$int.' level given; Role level cannot be greater than roots permission');
        }
        $this->setData('level', $int);
        return $this;
    }

    /**
     * Retuns the resources database ID
     *
     * @return int
     */

    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Returns the resources string identifier
     *
     * @return string
     */

    public function getIdentifier()
    {
        return $this->getData('identifier');
    }

    /**
     * Returns resources parents
     *
     * @return mixed
     */

    public function getParent()
    {
        return $this->getData('parent');
    }

    /**
     * Returns resource level
     *
     * @return int
     */

    public function getLevel()
    {
        return $this->getData('level');
    }

    public function hasParent()
    {
        if ($this->getParent() != 0) {
            return true;
        }
        return false;
    }
}
