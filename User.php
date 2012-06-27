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

class Unus_User extends Unus_Object_Instance
{
	const GUEST_ID = 0;

	const GUEST_USERNAME = 'Guest Account';

	private static $_instance = null;

	public static function getInstance()
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
     * Initalizes the User Object
     * Checks if the user session is registered adds default information
     */

    private function __construct()
    {
        $model = Unus_Model::getInstance();
		$session = Unus_Session::getInstance();

        Unus_Model::getInstance()->registerTable('user')
              ->registerField('userId', Unus_Model_Table::PRIMARY)
              ->registerField('username', Unus_Model_Table::CHAR, array('max_length' => 25))
              ->registerField('password', Unus_Model_Table::PASSWORD)
              ->registerField('email', Unus_Model_Table::EMAIL)
			  ->registerField('firstname', Unus_Model_Table::CHAR)
			  ->registerField('lastname', Unus_Model_Table::CHAR)
              ->registerTable('user_role')
              ->registerField('id', Unus_Model_Table::PRIMARY)
              ->registerField('userId', Unus_Model_Table::ONETOMANY)
              ->registerField('roleId', Unus_Model_Table::INTERGER);

        if ($session->sessionRegistered('user')) {
			$db = Unus::registry('db');
	       	$session->_use('user');

			if ($session->id != self::GUEST_ID) {

                $db->_use('user');

				$userInfo = $db->select('username')->where(array('userId' => $session->id),
														   true
														  );

				if (!$userInfo) {
					// destroy user session
					$session->sessionDestroy('user');
					$session->setErrorMessage(__('The account you are trying to access no longer exists'));
					Unus_Request::getInstance()->redirect(Unus::getPath());
					exit;
				}

				$user = $userInfo->fetch(PDO::FETCH_OBJ);

				$this->setData('username', $user->username)
					 ->setData('id', $user->userId);

                $db->_use('user_role');

				$roles = $db->select('*')->where(array('userId' => $session->id), true);
				// add roles
				if ($roles) {
					$result = $roles->fetchAll();
					foreach ($result as $k => $v) {
						$this->addRole($v['roleId']);
					}
				} else {
					// Default to user
					$this->addRole(2);
				}
			}

            Unus::dispatchEvent('user_init');

        } else {
            $this->setData('username', self::GUEST_USERNAME)
                 ->setData('id', self::GUEST_ID);
        }
    }

    /**
     * Returns the user's current role(s)
     *
     * @return mixed
     */

    public function getRoles()
    {
        return $this->getData('role');
    }

    /**
     * Returns the user's ID
     */

    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Returns the user's username with
     */

    public function getUsername()
    {
        return $this->getData('username');
    }

    /**
     * Returns if current user is logged into an account
     */
    public function isLoggedIn()
    {
        if ($this->id > 0) {
            return true;
        }

        return false;
    }

    public function clearRoles()
    {
        $this->__unset('role');
        return $this;
    }

    /**
     * Adds a role to the user
     *
     * @param  string  role  Role to add to this user role can be id, identifier or object for role
     *
     * @return this
     */

    public function addRole($role)
    {
        if (is_object($role)) {
            if (!$role instanceof Unus_Role_Object) {
                throw new Unus_User_Exception('Attempted to add role; expected Unus_Role_Object; '.get_class($role).' given');
            }
        } else {

            $role = Unus::registry('acl/roles')->getRole($role);
        }

        if (null == $this->getRoles()) {
            $this->setData('role', $role);
        } else {
            if (!is_array($this->getRoles())) {
                $array = array($this->getRoles());
            } else {
                $array = $this->getRoles();
            }
            $array[] = $role;
            $this->setData('role', $array);
        }

        return $this;
    }

    /**
     * Overloading data gets stored in the data object
     */

    public function __set($name, $value)
    {
        $this->setData($name, $value);
    }

    /**
     * Overloading data gets retrieved from the data object
     */

    public function __get($name)
    {
        return $this->getData($name);
    }

    /**
     * Overloading data gets retrieved from the data object
     */

    public function __isset($name)
    {
        if(null == $this->getData($name)) {
            return false;
        }

        return true;
    }

    /**
     * Overloading data gets removed from the data object
     */

    public function __unset($name)
    {
        $this->unsetData($name);
    }
}
