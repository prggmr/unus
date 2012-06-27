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

class Unus_Acl extends Unus_Object
{

    /**
     * Builds the registry
     */

    public function __construct()
    {
        $this->setData('roles', new Unus_Acl_Role())
             ->setData('resource', new Unus_Acl_Resource())
			 ->setData('cache', array());
    }

    /**
     * Overload to the data object
     */

    public function __get($name)
    {
        return $this->getData($name);
    }

    /**
     * Overload to the data object
     */

    public function __set($name, $value)
    {
        $this->setData($name, $value);
    }

    /**
     * Checks if a user is allowed to access a resource and
     *
     * Resource permission are checked in the following order each check overwrites the previous
     *
     * 1. Parents (if exists)
     * 2. Level
     * 3. Role Specific Permissions (if set)
     * 4. User Specific Permissions (if set)
     *
     *
     * @param  string  resource  Resource name to check
     *
     */

    public function isAllowed($resource)
    {
        // check if resource exists...
        if (null == $this->resource->getResource($resource)) {
            return true;
        }

		$cached = $this->getData('cache/'.$resource);

		if (null != $cached) {
			return $cached;
		}

        if (Unus_Development::getDevMode() && null == $this->getData('final')) {
            $this->setData('final', $this->resource->getResource($resource)->getIdentifier());
        }

        $traceRoute = array();
        $return = false;
        $userAllow = false;

        // get the resource level
        $level = $this->resource->getLevel($resource);

        if ($this->resource->hasParent($resource)) {
            // Check Parent Information
            $parent = $this->resource->getResource($this->resource->getParent($resource));
            $this->setData('parent_resource_check_'.$parent->getIdentifier());
            if ($this->isAllowed($parent->getIdentifier())) {
                $return = true;

            }
            $this->unsetData('parent_resource_check_'.$parent->getIdentifier());
        }

        // allowed roles
        $rolesAllow = $this->resource->getRolesAllow($resource);

        // denied roles
        $rolesDeny = $this->resource->getRolesDeny($resource);

        // allowed users
        $usersAllow = $this->resource->getUsersAllow($resource);

        // denied users
        $usersDeny = $this->resource->getUsersDeny($resource);

        // get a user's roles
        $roles = Unus::registry('user')->getRoles();

        if (is_array($roles)) {
            foreach ($roles as $k => $v) {
                $return = $this->_checkRolePermission($v, $resource, $rolesAllow, $rolesDeny);
            }
        } elseif (null !== $roles) {
            $return = $this->_checkRolePermission($roles, $resource, $rolesAllow, $rolesDeny);
        } else {

           /**
            * User has no roles?
            *
            * This is a bug caused when a user may first visit and their session data is not properly
            * initalized ...
            *
            * Current Workaround is to manually check the user session and set a default role
            *
            * TODO: Find a better way to solve this issue user acl norole bug
            *
            */

            $cache = $this->getData('cache');

            $cache[$resource] = false;

            $this->setData('cache', $cache);

            return false;
        }

        // User Based Permissions
        if (null != $usersAllow) {
            if (in_array(Unus::registry('user')->id, $usersAllow)) {
                $userAllow = true;
                $return = true;
            }
        }

        if ($this->resource->hasParent($resource) && $this->getData('parent_resource_user_deny_'.$this->resource->getResource($this->resource->getResource($resource)->getParent())->getIdentifier()) && $userAllow == false) {
            $return = false;
        }

        if (null != $usersDeny) {
            if (in_array(Unus::registry('user')->id, $usersDeny)) {
                if ($this->getData('parent_resource_check_'.$this->resource->getResource($resource)->getIdentifier())) {
                    $this->setData('parent_resource_user_deny_'.$this->resource->getResource($resource)->getIdentifier());
                    return false;
                }
                $return = false;
            }
        }



        // Logs a resource in dev mode

        if (Unus_Development::getDevMode()) {
            if ($this->resource->getResource($resource)->getIdentifier() == $this->getData('final')) {
                Unus_Development_Acl::log($this->resource->getResource($resource)->getIdentifier(), $return);
                $this->unsetData('final');
            }
        }

        // do stuff here go jump

        // make sure we dont return a null result true|false only
        if (null == $return) {
            $return = false;
        }

		$cache = $this->getData('cache');

		$cache[$resource] = $return;

		$this->setData('cache', $cache);

        return $return;
    }

     /**
     * Checks if a role is allowed access to a resource and builds trace
     */

    private function _checkRolePermission($role, $resource, $rolesAllow, $rolesDeny, $parentCheck = false)
    {
        $resource = $this->resource->getResource($resource);
        $roleAllow = false;
        $level = $resource->getlevel($resource);
		$return = null;

        // check if this role has parents build array
        if ($role->hasParent()) {
            $pObject = $this->roles->getRole($role->getParent());
            if ($this->_checkRolePermission($pObject, $level, $rolesAllow, $rolesDeny, true)) {
                $return = true;
            }
        }

        if ($role->getLevel() >= $level) {
            $return = true;
        }

        if (null != $rolesAllow) {
            if (in_array($role->getId(), $rolesAllow)) {
                if ($parentCheck) {
                   $this->setData('role_parent_allow'.$role->getIdentifier(), true);
                }
                if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_allow_'.$resource->getIdentifier(), true);
                }
                $roleAllow = true;
                $return = true;
            } elseif ($role->getId() != 4) {
                if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_deny_'.$resource->getIdentifier($resource), true);
                }
                if ($parentCheck == true) {
                    // set deny for children
                    $this->setData('role_parent_deny_'.$role->getIdentifier(), true);
                }
                $return = false;
            }
        }

        // BEGIN PARENT CHECKS

        if ($role->hasParent() && $this->getData('role_parent_allow'.$this->roles->getRole($role->getParent())->getIdentifier())) {
            if ($parentCheck) {
                $this->setData('role_parent_allow'.$role->getIdentifier(), true);
            }
            $return = true;
        }

        // check if the parent role is on the deny list and this role is not on allow
        if ($role->hasParent() && $this->getData('role_parent_deny_'.$this->roles->getRole($role->getParent())->getIdentifier())
            && $roleAllow == false && null == $this->getData('role_parent_allow'.$this->roles->getRole($role->getParent())->getIdentifier())
            ) {
            $this->unsetData('role_parent_deny_'.$this->roles->getRole($role->getParent())->getIdentifier());
            if ($parentCheck) {
                $this->setData('role_parent_deny_'.$role->getIdentifier(), true);
            }
            $return = false;
        }

        if($resource->hasParent() && $this->getData('resource_parent_role_deny_'.$this->resource->getResource($resource->getParent())->getIdentifier()) == true && $roleAllow == false) {
            if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_deny_'.$resource->getIdentifier($resource), true);
            }
        }

        if($resource->hasParent() && $this->getData('resource_parent_role_allow_'.$this->resource->getResource($resource->getParent())->getIdentifier()) == true && $roleAllow == false) {
            $this->setData('resource_parent_role_allow_'.$this->resource->getResource($resource->getParent())->getIdentifier(), true);
            $return = true;
        }

        // END PARENT CHECKS

        // DENY
        // This specifically denies roles only
        // roles not in this array are not considered being allowed
        if (null != $rolesDeny) {
            if (in_array($role->getId(), $rolesDeny) && $role->getId() != 4) {
                if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_deny_'.$resource->getIdentifier($resource), true);
                }
                if ($parentCheck == true) {
                    // set deny for children
                    $this->setData('role_parent_deny_'.$role->getIdentifier(), true);
                }
                $return = false;
            }
        }

		if ($return == null) {
			$return = false;
		}

        return $return;
    }


    /**
     * Checks if a user is allowed to access a resource and builds a trace
     *
     * Resource permission are checked in the following order each check overwrites the previous
     *
     * 1. Parents (if exists)
     * 2. Level
     * 3. Role Specific Permissions (if set)
     * 4. User Specific Permissions (if set)
     *
     *
     * @param  string  resource  Resource name to check
     *
     */

    public function isAllowed_trace($resource)
    {
        // check if resource exists...
        if (null == $this->resource->getResource($resource)) {
            return true;
        }

        $traceRoute = array();
        $return = false;
        $userAllow = false;

        // get the resource level
        $level = $this->resource->getLevel($resource);

        if (!$this->resource->hasParent($resource)) {
             $traceRoute[] = 'Resource has no parents; inheritance checks will be skipped';
        } else {
            // Check Parent Information
            $parent = $this->resource->getResource($this->resource->getParent($resource));
            $traceRoute[] = 'Resource is child of '.$parent->getIdentifier().' checking inheritance permissions';
            $traceRoute[] = ' ---------------------------------------------------------------------------- ';
            $traceRoute[] = 'Begin Parent Check for resource '.$parent->getIdentifier();
            $this->setData('parent_resource_check_'.$parent->getIdentifier());
            if ($this->isAllowed_trace($parent->getIdentifier())) {
                $return = true;
                $traceRoute[] = 'Parent resource allowes permission';
            } else {
                $traceRoute[] = 'Parent resource denies permission';
            }
            $this->unsetData('parent_resource_check_'.$parent->getIdentifier());

            // add parent trace to trace
            $traceRoute = array_merge($traceRoute, $this->getData('traceroute_'.$parent->getIdentifier()));
            $this->unsetData('traceroute_'.$parent->getIdentifier());

            $traceRoute[] = 'End Parent Checks';
            $traceRoute[] = ' ---------------------------------------------------------------------------- ';

        }

        // allowed roles
        $rolesAllow = $this->resource->getRolesAllow($resource);

        $a = 0;

         // Allow Trace
        if (count($rolesAllow) == 0) {
            $traceRoute[] = 'Resource has no role defined allow access checks will be skipped';
            $a++;
        }

        // denied roles
        $rolesDeny = $this->resource->getRolesDeny($resource);

        // Deny Trace
        if (null == $rolesDeny) {
            $traceRoute[] = 'Resource has no role defined deny access checks will be skipped';
            $a++;
        }

        // allowed users
        $usersAllow = $this->resource->getUsersAllow($resource);

        // Allow Trace
        if (null == $usersAllow) {
            $traceRoute[] = 'Resource has no user defined allow access checks will be skipped';
            $a++;
        }

        // denied users
        $usersDeny = $this->resource->getUsersDeny($resource);

        // Deny Trace
        if (null == $usersDeny) {
            $traceRoute[] = 'Resource has no user defined deny access checks will be skipped';
            $a++;
        }

        if ($a == 4) {
            $traceRoute[] = ' ---------------------------------------------------------------------------- ';
        }

        // get a user's roles
        $roles = Unus::registry('user')->getRoles();
        if (is_array($roles)) {
            foreach ($roles as $k => $v) {
                $return = $this->_checkRolePermission_trace($v, $resource, $rolesAllow, $rolesDeny);
                // add parent trace to trace
                $traceRoute = array_merge($traceRoute, $this->getData('tmp_trace_'.$v->getIdentifier()));
                $this->unsetData('tmp_trace_'.$v->getIdentifier());
            }
        } else {
            $return = $this->_checkRolePermission_trace($roles, $resource, $rolesAllow, $rolesDeny);
            $traceRoute = array_merge($traceRoute, $this->getData('tmp_trace_'.$roles->getIdentifier()));
            $this->unsetData('tmp_trace_'.$roles->getIdentifier());
        }

        // User Based Permissions
        if (null != $usersAllow) {
            if (in_array(Unus::registry('user')->id, $usersAllow)) {
                $userAllow = true;
                $traceRoute[] = 'User is allowed based on per-user permissions';
                $return = true;
            }
        }

        if ($this->resource->hasParent($resource) && $this->getData('parent_resource_user_deny'.$this->resource->getResource($this->resource->getResource($resource)->getParent())->getIdentifier()) && $userAllow == false) {
            $return = false;
            $traceRoute[] = 'User is denied based on parents per-user permissions';
        }

        if (null != $usersDeny) {
            if (in_array(Unus::registry('user')->id, $usersDeny)) {
                if ($this->getData('parent_resource_check_'.$this->resource->getResource($resource)->getIdentifier())) {
                    $this->setData('parent_resource_user_deny'.$this->resource->getResource($resource)->getIdentifier());
                    $traceRoute[] = 'Parent Resource disallows user specific access';
                    return false;
                }
                $traceRoute[] = 'User is denied based on per-user permissions';
                $return = false;
            }
        }

        // Set the traceroute

        $this->setTraceRoute($resource, $traceRoute);

        // make sure we dont return a null result true|false only
        if (null == $return) {
            $return = false;
        }

        return $return;
    }

    /**
     * Checks if a role is allowed access to a resource and builds trace
     */

    private function _checkRolePermission_trace($role, $resource, $rolesAllow, $rolesDeny, $parentCheck = false)
    {
        $resource = $this->resource->getResource($resource);
        $traceRoute = array();
        $roleAllow = false;
        $parentTraces = array();
        $level = $resource->getlevel($resource);
		$return = null;

        // check if this role has parents build array
        if ($role->hasParent()) {
            $traceRoute[] = 'Role '.$role->getIdentifier().' is a child role';
            $pObject = $this->roles->getRole($role->getParent());
            $traceRoute[] = ' ---------------------------------------------------------------------------- ';
            $traceRoute[] = 'BEGIN PARENT CHECK of role : '.$pObject->getIdentifier();
            if ($this->_checkRolePermission($pObject, $level, $rolesAllow, $rolesDeny, true)) {
                $return = true;
                $traceRoute[] = 'Parent Role '.$pObject->getIdentifier().' allows access';
            } else {
                $traceRoute[] = 'Parent Role '.$pObject->getIdentifier().' denies access';
            }
            // add parent trace
            $traceRoute = array_merge($traceRoute, $this->getData('tmp_trace_'.$pObject->getIdentifier()));
            $this->unsetData('tmp_trace_'.$pObject->getIdentifier());
            $traceRoute[] = 'END PARENT CHECK of role : '.$pObject->getIdentifier();
            $traceRoute[] = ' ---------------------------------------------------------------------------- ';
        }

        // First we check the level
        if ($role->getLevel() >= $level) {
            $return = true;
            $traceRoute[] = 'Role '.$role->getIdentifier().' is allowed based on level';
        } else {
            $traceRoute[] = 'Role '.$role->getIdentifier().' is denied based on level';
        }

        // Then we check the roles
        // these overwrite the level permission
        // roles not in this will be set to deny
        // root roles are not affected by this
        if (null != $rolesAllow) {
            if (in_array($role->getId(), $rolesAllow)) {
                if ($parentCheck) {
                   $this->setData('role_parent_allow'.$role->getIdentifier(), true);
                }
                if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_allow_'.$resource->getIdentifier(), true);
                }
                $roleAllow = true;
                $return = true;
                $traceRoute[] = 'Role '.$role->getIdentifier().' is allowed based on specific roles';
            } elseif ($role->getId() != 4) {
                if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_deny_'.$resource->getIdentifier($resource), true);
                }
                if ($parentCheck == true) {
                    // set deny for children
                    $this->setData('role_parent_deny_'.$role->getIdentifier(), true);
                }
                $return = false;
                $traceRoute[] = 'Role '.$role->getIdentifier().' is denied as based that it is not a Root role and not in allowed list';
            }
        }

        // BEGIN PARENT CHECKS

        if ($role->hasParent() && $this->getData('role_parent_allow'.$this->roles->getRole($role->getParent())->getIdentifier())) {
            if ($parentCheck) {
                $this->setData('role_parent_allow'.$role->getIdentifier(), true);
            }
            $return = true;
        }

        // check if the parent role is on the deny list and this role is not on allow
        if ($role->hasParent() && $this->getData('role_parent_deny_'.$this->roles->getRole($role->getParent())->getIdentifier())
            && $roleAllow == false && null == $this->getData('role_parent_allow'.$this->roles->getRole($role->getParent())->getIdentifier())
            ) {
            $this->unsetData('role_parent_deny_'.$this->roles->getRole($role->getParent())->getIdentifier());
            if ($parentCheck) {
                $this->setData('role_parent_deny_'.$role->getIdentifier(), true);
            }
            $traceRoute[] = 'Parent Role : '.$this->roles->getRole($role->getParent())->getIdentifier().' is denied based on specific roles denied or not on allow list and role '.$role->getIdentifier().' not on allowed list; Permission Denied';
            $this->setData('tmp_trace_'.$role->getIdentifier(), $traceRoute);
            $return = false;
        }

        if($resource->hasParent() && $this->getData('resource_parent_role_deny_'.$this->resource->getResource($resource->getParent())->getIdentifier()) == true && $roleAllow == false) {
            if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_deny_'.$resource->getIdentifier($resource), true);
            }
            $traceRoute[] = 'Parent Resource Denies access determained by specific roles';
        }

        if($resource->hasParent() && $this->getData('resource_parent_role_allow_'.$this->resource->getResource($resource->getParent())->getIdentifier()) == true && $roleAllow == false) {
            echo 'Checking resource parent allow';
            $this->setData('resource_parent_role_allow_'.$this->resource->getResource($resource->getParent())->getIdentifier(), true);
            $traceRoute[] = 'Parent Resource Allows access determained by specific roles';
            $return = true;
        }

        // END PARENT CHECKS

        // DENY
        // This specifically denies roles only
        // roles not in this array are not considered being allowed
        if (null != $rolesDeny) {
            if (in_array($role->getId(), $rolesDeny) && $role->getId() != 4) {
                if ($this->getData('parent_resource_check_'.$resource->getIdentifier())) {
                    $this->setData('resource_parent_role_deny_'.$resource->getIdentifier($resource), true);
                }
                if ($parentCheck == true) {
                    // set deny for children
                    $this->setData('role_parent_deny_'.$role->getIdentifier(), true);
                }
                $return = false;
                $traceRoute[] = 'Role '.$role->getIdentifier().' is denied based on specific roles';
            }
        }

		if (null == $return) {
			$return = false;
		}

        $this->setData('tmp_trace_'.$role->getIdentifier(), $traceRoute);

        return $return;
    }

    /**
     * Builds a user's role permission traceroute
     *
     * @param  array  traceroute  Traced array of how the permission is parsed
     *
     * @return this
     */

    public function setTraceRoute($resource, $traceRoute)
    {
        $this->setData('traceroute_'.$resource, $traceRoute);
        return $this;
    }

    /**
     * Gets a resource traceroute check
     *
     * @param  array  traceroute  Traced array of how the permission is parsed
     *
     * @return
     */

    public function getTraceRoute($resource)
    {
        $trace = $this->getData('traceroute_'.$resource);

        $this->unsetData('traceroute_'.$resource);

        $str = null;

        foreach ($trace as $k => $v) {
            $str .= '{'.$k.'} '.$v.' <br />';
        }

        return $str;
    }

    /**
     * Writes data to the log file
     *
     * @param  str  content  Data to be written into the log file
     */

    private function __logData($content)
    {
        $contents = file_get_contents(ACCESSLOG_FILE);
            $log_message = str_replace('<br />', '
', $error);
            $log_message .= '
---------------------------
Recorded : '.date('m/d/y h:ia', time()).'


';
            $contents .= $log_message;
            file_put_contents(ACCESSLOG_FILE, $contents);
    }

    /**
     * Redirect a user with a permission denied message to url
     *
     * @param  string  url      URL to redirect user to
     * @param  string  message  Message to give user on redirected page
     *
     * @return
     */

    public function redirect($url, $message = null)
    {
        $message = (null == $message) ? __('Sorry, you do not have the required permissions to access this page') : __($message);
        Unus::registry('session')->setErrorMessage($message);
        $url = (stripos($url, Unus::getPath()) === false) ? Unus::getPath().$url : $url;
        header('refresh: 0; url='.$url);
        exit;
    }

}
