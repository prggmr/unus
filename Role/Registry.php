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

class Unus_Role_Registry extends Unus_Object
{
    public function construct() {

    }

    /**
     * Adds a new role to the role registry
     *
     * @param  object  role  Unus_Role_Object
     *
     * @return
     */

    public function addRole(Unus_Role_Object $role, $overload = false)
    {
        if (null != $this->getData($role->getIdentifier()) && $overload == false) {
            throw new Unus_Role_Registry_Exception('Role '.$role->getIdentifier().' already exists in the role registry');
        }
        $this->setData($role->getIdentifier(), $role);
        return $this;
    }

    /**
     * Removes a role from the registry
     *
     * @param  object  role  Unus_Role_Object
     *
     * @return
     */

    public function removeRole(Unus_Role_Object $role)
    {
        if (null != $this->getData($role->getIdentifier())) {
            $this->unsetData($role->getIdentifier());
            return true;
        }
        return false;
    }

    /**
     * Returns the role registry
     *
     * @return array
     */

    public function getRegistry()
    {
        return $this->getData();
    }
}
