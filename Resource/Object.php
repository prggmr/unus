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

class Unus_Resource_Object extends Unus_Object
{

    /**
     * Creates a new role object set Id and Identifier and level
     *
     */

    public function __construct($roleId, $identifier, $level)
    {
        $this->setLevel($level);
        $this->setId($roleId);
        $this->setIdentifier($identifier);
    }

    /**
     * Sets the roles internal numeric Id
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
     * Sets the resource parent
     *
     * @param  mixed  parent  Parent that will be assigned to this resource
     *
     * @return
     */

    public function setParent($parent)
    {

        $this->setData('parent', $parent);

        return $this;
    }

     /**
     * Sets the role permission level
     * 0 - Guest
     * 1 - User
     * 2 - Admin
     * 3 - Root
     *
     * @param  int  int  Numerical Level for this level ( 0-3 )
     *
     * @return this
     */

    public function setLevel($int = 0) {
        if ($int > 4) {
            throw new Unus_Resource_Object_Exception('Role level must be a interger value 1-4; '.$int.' level given');
        }
        $this->setData('level', $int);
        return $this;
    }

    /**
     * Sets a resources custom role permissions for access
     *
     * @return this
     */

    public function setRolesAllow($roles) {
        if (!is_array($roles)) {
            if (!is_array(unserialize($roles))) {
                throw new Unus_Resource_Object_Exception('Allowed roles must a array or serialized array string '.gettype($roles).' given');
            } else {
                $roles = unserialize($roles);
            }
        }
        $this->setData('rolesAllow', $roles);
        return $this;
    }

    /**
     * Sets a resources custom role permissions for disallowed access
     *
     * @return this
     */

    public function setRolesDeny($roles) {
        if (!is_array($roles)) {
            if (!is_array(unserialize($roles))) {
                throw new Unus_Resource_Object_Exception('Disallowed roles must a array or serialized array string '.gettype($roles).' given');
            } else {
                $roles = unserialize($roles);
            }
        }
        $this->setData('rolesDeny', $roles);
        return $this;
    }

    /**
     * Sets a resources custom user permissions access
     *
     * @return this
     */

    public function setUsersAllow($user) {
        if (!is_array($user)) {
            if (!is_array(unserialize($user))) {
                throw new Unus_Resource_Object_Exception('Allowed users must a array or serialized array string '.gettype($user).' given');
            } else {
                $user = unserialize($user);
            }
        }
        $this->setData('userAllow', $user);
        return $this;
    }

    /**
     * Sets a resources custom user permissions disallowed access
     *
     * @return this
     */

    public function setUsersDeny($user) {
        if (!is_array($user)) {
            if (!is_array(unserialize($user))) {
                throw new Unus_Resource_Object_Exception('Disallowed users must a array or serialized array string '.gettype($user).' given');
            } else {
                $user = unserialize($user);
            }
        }
        $this->setData('userDeny', $user);
        return $this;
    }

     /**
     * Gets resources custom role permissions for access
     *
     * @return int
     */

    public function getRolesAllow() {
        return $this->getData('rolesAllow');
    }

    /**
     * Gets resources custom role permissions for disallowed access
     *
     * @return array
     */

    public function getRolesDeny() {
        return $this->getData('rolesDeny');
    }

    /**
     * Gets resources custom user permissions access
     *
     * @return array
     */

    public function getUsersAllow() {
        return $this->getData('userAllow');
    }

    /**
     * Gets resources custom user permissions disallowed access
     *
     * @return array
     */

    public function getUsersDeny() {
        return $this->getData('userDeny');
    }


    /**
     * Retuns the resource database ID
     *
     * @return int
     */

    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Returns the resource string identifier
     *
     * @return string
     */

    public function getIdentifier()
    {
        return $this->getData('identifier');
    }

    /**
     * Returns resource parent
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
