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

class Unus_Acl_Resource extends Unus_Object
{

    /**
     * Builds the registry
     */

    public function __construct()
    {
		$resource = Unus_Resource::getInstance();

        $this->setData('acl', $resource);
    }

    /**
     * Retireves resource object
     *
     * @param  mixed  str  Id or Indentifier for resource
     *
     * @return
     */

    public function getResource($str)
    {
        return $this->getData('acl')->getIdentifier($str);
    }

    /**
     * Attempts to retrieve a resource object by its indentifier
     *
     * @param  string  identifier  String identifier for resource to retrieve
     *
     * @return
     */

    public function getResourceByIdentifier($identifier)
    {
        return $this->getData('acl/registry/'.$identifier.'');
    }

    /**
     * Retrieves the level of a resource
     *
     * @param  mixed  role  Role to retrieve its permission level can a roles ID, Indentifier or role object
     */

    public function getLevel($resource)
    {
        return $this->getData('acl')->getLevel($resource);
    }

    /**
     * Retrieves the parents of a resource
     *
     * @param  mixed  role  Role to retrieve its parents can a resource ID, Indentifier or resource object
     */

    public function getParent($resource)
    {
        return $this->getData('acl')->getParent($resource);
    }

    /**
     * Gets resources custom user permissions access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getUsersAllow($resource) {
        return $this->getData('acl')->getUsersAllow($resource);
    }

    /**
     * Gets resources custom user permissions disallowed access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getUsersDeny($resource) {
        return $this->getData('acl')->getUsersDeny($resource);
    }

    /**
     * Gets resources custom roles permissions disallowed access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getRolesDeny($resource) {
        return $this->getData('acl')->getRolesDeny($resource);
    }

    /**
     * Gets resources custom roles permissions access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getRolesAllow($resource)
    {
        return $this->getData('acl')->getRolesAllow($resource);
    }

    /**
     * Checks if a resource has a parent
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return boolean
     */


    public function hasParent($resource)
    {
        if ($this->getData('acl')->hasParent($resource)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Overloading
     */

    public function __get($name)
    {
        return $this->getData($name);
    }
}
