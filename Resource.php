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

class Unus_Resource extends Unus_Object_Instance
{
    /**
     * Db Model
     */

    private $_model = null;

	/**
	 * Instance of Unus_Resource
	 *
	 */

	private static $_instance = null;

	/**
     *  Returns Instance of Unus_Resource
     *
     *  @return Unus_Resource
     */

    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Builds resource registry Auto loads database resource
     *
     * @return none
     */

    private function __construct()
    {
        $this->setData('registry', new Unus_Resource_Registry());


        Unus_Model::getInstance()->registerTable('resource')
              ->registerField('resourceId', Unus_Model_Table::PRIMARY)
              ->registerField('parentId', Unus_Model_Table::ONETOONE, array('use' => 'self'))
              ->registerField('title', Unus_Model_Table::CHAR, array('max_length' => 225))
              ->registerField('level', Unus_Model_Table::INTERGER, array('max_length' => 1))
              ->registerField('roleAllow', Unus_Model_Table::TEXT)
              ->registerField('userAllow', Unus_Model_Table::TEXT)
              ->registerField('roleDeny', Unus_Model_Table::TEXT)
              ->registerField('userDeny', Unus_Model_Table::TEXT);

        $resources = Unus::registry('db')->setModel('resource')->all();

        foreach ($resources as $k => $v) {
            $this->addResource($v['resourceId'], $v['title'], $v['parentId'], $v['level'], $v['roleAllow'], $v['roleDeny'], $v['userAllow'], $v['userDeny']);
        }

		return $this;
    }

    /**
     * Adds a new resource object to the registry
     *
     * @param  int    id         DatabaseId
     * @param  str    identifier Title
     * @param  int    parent     Parent id
     * @param  int    level      Role Standard Permission Level
     * @param  array  allowRole  Per-Role basis to to allow specific roles access
     * @param  array  denyRole   Per-Role basis to to deny specific roles access
     * @param  array  allowUsr   Per-Role basis to to allow specific user access
     * @param  array  denyUsr    Per-Role basis to to deny specific user access
     *
     * @return self
     */

    public function addResource($id, $identifier, $parent = null, $level = 0, $allowRole = '', $denyRole = '', $allowUsr = '', $denyUsr = '')
    {
        $resource = new Unus_Resource_Object($id, $identifier, $level);

        if (null != $parent) {
            $resource->setParent($parent);
        }

        if (count($allowRole) != 0) {
            $resource->setRolesAllow($allowRole);
        }

        if (count($denyRole  != 0)) {
            $resource->setRolesDeny($denyRole);
        }

        if (count($allowUsr) != 0) {
            $resource->setUsersAllow($allowUsr);
        }

        if (count($denyUsr) != 0) {
            $resource->setUsersDeny($denyUsr);
        }

        $this->getData('registry')->addResource($resource);

        return $this;
    }


    /**
     * This will attempt to find a given resource in the registry from a id/string
     *
     * @param  int  str  Resource to retrieve
     *
     * @return object
     */

    public function getIdentifier($str, $return = 'object')
    {
        $resources = $this->getData('registry')->getRegistry();

        foreach ($resources as $k => $v) {
            if ($v->getId() == $str || $k == $str) {
                if ($return === 'object') {
                    return $v;
                } else {
                    return $v->getIdentifier();
                }
            }
        }

        return false;
    }

    /**
     * Get a resource id
     *
     * @param  mixed  resource  Resource ID
     *
     * @return str
     */

    public function getId($resource)
    {
        return $this->getResource($resource)->getId();
    }

    /**
     * Returns the resource object stored in the registry
     *
     * @param  mixed  resource  Resource Name to fetch object
     *
     */

    public function getResource($resource)
    {
        if (!is_object($resource)) {
            if (is_numeric($resource)) {
                return $this->getData('registry/'.$resource);
            } else {
                return $this->getIdentifier($resource);
            }
        }

        return null;
    }

    /**
     * Get a roles level
     *
     * @param  mixed  role  Role ID or Identifier
     *
     * @return int
     */

    public function getLevel($resource)
    {
        return $this->getResource($resource)->getLevel();
    }

    /**
     * Get a roles parents
     *
     * @param  mixed  role  Role ID or Identifier
     *
     * @return int
     */

    public function getParent($resource)
    {
        return $this->getResource($resource)->getParent();
    }

    /**
     * Gets resources custom user permissions access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getUsersAllow($resource) {
        return $this->getResource($resource)->getUsersAllow();
    }

    /**
     * Gets resources custom user permissions disallowed access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getUsersDeny($resource) {
        return $this->getResource($resource)->getUsersDeny();
    }

    /**
     * Gets resources custom roles permissions disallowed access
     *
     * @param  mixed  $str  Numerical or string representation of the identifier
     *
     * @return array
     */

    public function getRolesDeny($resource) {
        return $this->getResource($resource)->getRolesDeny();
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
        return $this->getResource($resource)->getRolesAllow();
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
        if ($this->getResource($resource)->hasParent()) {
            return true;
        } else {
            return false;
        }
    }

}
