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

class Unus_Cura extends Unus_Object
{
    public function __construct() {

        if (Unus::isDeveloperMode()) {
            $this->syncDb();
        }

        // Dispatch Cura startup
        Unus::dispatchEvent('cura_init');

        Unus_Helper_Admin_Navigation::addLink('Dashboard', '');

        $router = Unus_Router::getInstance();

        $defaultRoutes = array('' => 'index',
                               'login' => 'login',
                               'logout' => 'logout',
                               'lost-password' => 'lostPassword'
                               );
        foreach ($defaultRoutes as $k => $v) {
            $router->route(Unus::getAdminPath(false).$k, array('controller' => 'cura_controller', 'action' => $v));
        }
        $this->setData('router', $router);
        // Initialize Registry
        // Anaylze model structure to a easier to handle infastructure
        $this->addSystemRoutes();

        $this->setData('run', true);
    }

    public function addSystemRoutes()
    {
        $routes = array();
        $acl = Unus::registry('acl');
        
        $adminPath = Unus::getAdminPath(false);

        $this->getData('router')->route($adminPath.'dashboard', array('controller' => 'cura_controller', 'action' => 'index'));
        // add the system routes from analyzed model structure
        foreach (Unus::registry('models')->getData('registry')->getData() as $k => $v) {

            if ($v->getData('config/admin') == true && $acl->isAllowed('admin_'.$k.'_main')) {
                $prepend = $adminPath.$k.'/';

                $prependLink = $adminPath.$k.'/';

                // Add | Edit | Delete CONFIGURATION
                $add = $v->getData('config/add');
                $edit = $v->getData('config/edit');
                $delete = $v->getData('config/delete');

                // Get permissions for each resource
                $mainPerm = true;

                if ($add != false) {
                    $addPerm = true;
                }
                if ($edit != false) {
                    $editPerm = true;
                }
                if ($delete != false) {
                    $deletePerm = true;
                }

                // Add TOP Level Navigation Item ONLY if they can view one of any page

                if ((null == $v->getData('config/print_nav') || $v->getData('config/print_nav') == true) && ($mainPerm == true || $mainPerm == false && $addPerm == true && $add == true)) {
                    Unus_Helper_Admin_Navigation::addLink($this->convertString($k), $prepend, $v->getData('config/parent'));
                    if ($mainPerm == true) {
                        Unus_Helper_Admin_Navigation::addLink($this->convertString($k) . ' Dashboard', $prepend.'dashboard/', $k);
                    }
                }

                // Configure the routes that can be accessed

                $routables = array();
                $routables[] = $prependLink;
                $routables[] = $prependLink.'dashboard/';

                if (($add != false && $addPerm == true) && (null == $v->getData('config/print_nav') || $v->getData('config/print_nav') == true) && $acl->isAllowed('admin_'.$k.'_add')) {
                    $routables[] = $prepend.'add';
                    Unus_Helper_Admin_Navigation::addLink('Add ' . $this->convertString($k), $prepend.'add/', $k);
                }
                if ($edit != false && $editPerm == true  && $acl->isAllowed('admin_'.$k.'_edit')) {
                    $routables[] = $prepend.'edit/';
                }
                if ($delete != false && $deletePerm == true  && $acl->isAllowed('admin_'.$k.'_delete')) {
                    $routables[] = $prepend.'delete/';
                }
                $routes[$k] = $routables;
            }
        }

        //$front = Zend_Controller_Front::getInstance();
        foreach ($routes as $k => $t) {
            foreach ($t as $r => $v) {
                $action = null;
                if (stripos($v, 'dashboard') != false) {
                    $action = 'index';
                    $name = 'dashboard';
                } elseif (stripos($v, 'add') != false) {
                    $action = 'add';
                    $name = 'add';
                } elseif (stripos($v, 'edit') != false) {
                    $action = 'edit';
                    $name = 'edit';
                } elseif (stripos($v, 'delete') != false) {
                    $action = 'delete';
                    $name = 'delete';
                } else {
                    $action = 'index';
                    $name = 'index';
                }

                $this->getData('router')->route($v, array('controller' => 'cura_controller', 'action' => $action));
            }
        }
    }

    /**
     * Parses a str into human readbable text changing _ to a space and captilizing first word
     *
     * @param  str  str  String to convert
     *
     * @return str
     */

    public function convertString($str)
    {
        $str = str_replace('_', ' ', $str);
        $str = ucfirst($str);
        return $str;
    }

    /**
     * Sync's the database with new model information
     * This will not run if DEBUG is disabled
     */

    private function syncDb()
    {
        $acl = Unus::registry('acl');
        $insert = array();
        foreach (Unus::registry('models')->getData('registry')->getData() as $k => $v) {
            if (null == $acl->getData('resource')->getResource('admin_'.$k.'_main')) {
                $insert[] = 'admin_'.$k.'_main';
            }
            if ($v->getData('config/add') != false) {
                if (null == $acl->getData('resource')->getResource('admin_'.$k.'_add')) {
                    $insert[] = 'admin_'.$k.'_add';
                }
            }
            if ($v->getData('config/edit') != false) {
                if (null == $acl->getData('resource')->getResource('admin_'.$k.'_edit')) {
                    $insert[] = 'admin_'.$k.'_edit';
                }
            }
            if ($v->getData('config/delete') != false) {
                if (null == $acl->getData('resource')->getResource('admin_'.$k.'_delete')) {
                    $insert[] = 'admin_'.$k.'_delete';
                }
            }
        }

        if (count($insert) == 0) {
        } else {
            $changes = count($insert);
            echo 'Unus Development Mode Active :: <br />
            Cura Has detected modifications needed for system resources <br />
            Running Cura Sync ... <br />';
            echo '<span id="update" style="clear: none;">0</span>% Done';
            flush();
            usleep(800000);
            $db = Unus::registry('db');
            $parentId = 0;
            $db->setModel('resource');
            $a = 0;
            $added = null;
            foreach ($insert as $k => $v) {
                if ($a == 0) {
                    flush();
                    usleep(500000);
                }
                $a++;
                $update = (($a / $changes) * 100);
               echo '
                <script type="text/javascript">
                    document.getElementById(\'update\').innerHTML = "'.$update.'";
                </script>';
                $empty = serialize(array());
                //$parentId = (substr($v, (strlen($v) - 4), 4) == 'main') ? '0' : (($parentId == '0') ? $db->lastInsertId() : $parentId);
                if (substr($v, (strlen($v) - 4), 4) == 'main') {
                    $parentId = 2;
                } elseif ($parentId == 2) {
                    $parentId = $db->lastInsertId();
                }
                $added .= $v.' <br />';
                $db->add(array(
                            'parentId' => $parentId,
                            'title' => $v,
                            'level' => 2,
                            'roleAllow' => $empty,
                            'roleDeny' => $empty,
                            'userAllow' => $empty,
                            'userDeny' => $empty));
                flush();
                usleep(5000);
            }
            echo '<br /> Cura Sync has finished, refresh to continue <br /><br /><strong>This process will run only if development mode is active!</strong>';
            echo '<br /><br /> Resources Added <br /><br /> '.$added.'';
            die();
        }
    }
}
