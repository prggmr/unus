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

class Unus_Role extends Unus_Object_Instance
{
    /**
     * Db Model
     */

    private $_model = null;

    /**
	 * Instance of Unus_Role
	 *
	 */

	private static $_instance = null;

	/**
     *  Returns Instance of Unus_Role
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
     * Builds role registry Auto loads database roles
     *
     * @return Unus_Role
     */

    private function __construct()
    {
        $this->setData('registry', new Unus_Role_Registry());

        Unus_Model::getInstance()->registerTable('role')
              ->registerField('roleId', Unus_Model_Table::PRIMARY)
              ->registerField('parentId', Unus_Model_Table::ONETOONE, array('use' => 'self'))
              ->registerField('title', Unus_Model_Table::CHAR, array('max_length' => 225))
              ->registerField('level', Unus_Model_Table::INTERGER, array('max_length' => 1));


        $roles = Unus::registry('db')->_use('role')->all();
		
        foreach ($roles->fetchAll(PDO::FETCH_ASSOC) as $k => $v) {
            $this->addRole($v['roleId'], $v['title'], $v['level'], $v['parentId']);
        }

		return $this;
    }

    /**
     * Adds a new role object to the role registry
     *
     * @param  int  id  DatabaseId of the role
     * @param  str  identifier  Title of the role
     * @param  int  level  Role Standard Permission Level
     * @param  int  parent  Parent id of the role
     *
     * @return self
     */

    public function addRole($id, $identifier, $level, $parent = null)
    {
        $role = new Unus_Role_Object($id, $identifier, $level);

        if (null != $parent) {
            $role->setParent($parent);
        }

        if (null != $level && $level < 4) {
            $role->setLevel($level);
        }

        $this->getData('registry')->addRole($role);

        return $this;
    }

    /**
     * Removes a role
     *
     * @param  str  str  Role identifier to remove
     *
     * @return boolean
     */

    public function removeRole($str) {
        if (null != $this->getData('registry/'.$str)) {
            $this->getData('registry')->removeRole($str);
            return true;
        }

        return false;
    }

    /**
     * This will attempt to find a given role in the registry from a id
     *
     * @param  string  str  ID of role to locate
     *
     * @return object
     */

    public function getIdentifier($str, $return = 'object')
    {
        $roles = $this->getData('registry')->getRegistry();

        foreach ($roles as $k => $v) {
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
     * Get a roles id
     *
     * @param  mixed  role  Role ID or Identifier
     *
     * @return str
     */

    public function getId($role)
    {
        if (!is_object($role)) {
            if (!is_numeric($role)) {
                return $this->getData('registry/'.$role)->getId();
            } else {
                return $this->getIdentifier($role)->getId();
            }
        } elseif ($role instanceof Unus_Role_Object) {
                return $role->getId();
        } else {
            throw new Unus_Role_Exception('Cannot retrieve role Id expected string, interger or Unus_Role_Object; instanceof '.get_class($role).' given');
        }
    }

    /**
     * Get a roles level
     *
     * @param  mixed  role  Role ID or Identifier
     *
     * @return int
     */

    public function getLevel($role)
    {
        if (!is_object($role)) {
            if (is_numeric($role)) {
                return $this->getData('registry/'.$role)->getLevel();
            } else {
                return $this->getIdentifier($role)->getLevel();
            }
        } elseif ($role instanceof Unus_Role_Object) {
            return $role->getLevel();
        } else {
            throw new Unus_Role_Exception('Cannot retrieve role level expected string, interger or Unus_Role_Object; instanceof '.get_class($role).' given');
        }
    }

    /**
     * Get a roles parents
     *
     * @param  mixed  role  Role ID or Identifier
     *
     * @return int
     */

    public function getParent($role)
    {
        if (!is_object($role)) {
            if (is_numeric($role)) {
                return $this->getData('registry/'.$role)->getParent();
            } else {
                return $this->getIdentifier($role)->getParent();
            }
        } elseif ($role instanceof Unus_Role_Object) {
            return $role->getParent();
        } else {
            throw new Unus_Role_Exception('Cannot retrieve role parents expected string, interger or Unus_Role_Object; instanceof '.get_class($role).' given');
        }
    }

    public function hasParent($role)
    {
        if ($this->getParent($role) == 0) {
            return false;
        }
        return true;
    }


}
