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

class Unus_Plugin extends Unus_Object
{
    private $_plugins = null;

    /**
    * Loads
    *
    * @param  type  name  desc
    * @param  type  name  desc
    *
    * @return
    */

    public function __construct()
    {
        $this->_plugins = new Unus_Plugin_Model();
        $this->_loadPlugins();
        // This has been moved to the administration
        $this->_scanPlugins();
    }

    /**
    * Loads all plugins from the database and registers
    * them into active and deactive plugins
    *
    * @param  type  name  desc
    * @param  type  name  desc
    *
    * @return
    */

    private function _loadPlugins()
    {

        $pluginsDatabase = $this->_plugins->registerLoad();

        $activePlugins = array();
        $pluginListData = array();

        foreach ($pluginsDatabase as $k => $v) {
            if ($v['active'] == 1) {
                $activePlugins[$v['name']] = array('callBack' => array($v['class'], $v['method']), 'event' => $v['event']);
                Unus::addObserver($v['name'], array($v['class'], $v['method']), $v['event']);
            }

            $pluginListData[$v['name']] = array('callBack' => array($v['class'], $v['method']), 'event' => $v['event']);
        }

        $this->setData('activePlugins', $activePlugins);
        $this->setData('registerdPlugins', $pluginListData);
    }

    /**
    * Checks if plugin is a registerd plugin
    *
    * @param  str  $id
    *
    * @return bool
    */

    public function isRegisterd($id)
    {
        if (null != $this->getData('registerdPlugins/'.$id)) {
            return true;
        }
        return false;
    }

    /**
    * Disables a enabled plugin
    *
    * @param  str  $id
    *
    * @return bool
    */

    public function disablePlugin($id)
    {
        if ($this->isRegisterd($id)) {
            if ($this->_plugins->disable($id)) {
                return true;
            }
        }
        return false;
    }

    /**
    * Enables a disabled Plugin
    *
    * @param  str  $id
    *
    * @return bool
    */

    public function enablePlugin()
    {
        if ($this->isRegisterd($id)) {
            if ($this->_plugins->enable($id)) {
                return true;
            }
        }
        return false;
    }

    /**
    * Receives new plugin data checks and sends
    * information to _registerPlugin
    *
    * @param  str    $name      Name of Plugin
    * @param  array  $callBack  Class and method call
    * @param  str    $event     Event that plugin will exec
    *
    * @throws Unus_Plugin_Exception
    */

    public function addPlugin($name, $callBack, $event = null)
    {
        if ($name == null) {
            throw new Unus_Plugin_Exception('Undefined name; Plugin Cannot be registerd');
        } if (!is_array($callBack)) {
            throw new Unus_Plugin_Exception('Parameter (callBack) expected array '.gettype($callBack).' given');
        } elseif ($this->isRegisterd($name)) {
            throw new Unus_Plugin_Exception('Plugin By Name <strong>'.$name.'</strong> is already registerd');
        }

        // Attempt to load the Plugins config file to get information
        $this->_registerPlugin($name, $callBack, $event);
    }

    /**
    * Scans app/public/plugins for newly installed plugins
    * and either returns or installs them
    *
    * @param  bool  $scan  Flag to only scan and return plugin listing
    * @param  bool  $new   Flag to scan and return newly installed plugin listing
    *
    * @return mixed
    */

    private function _scanPlugins($scan = false, $new = false)
    {
        if (!is_readable(Unus::getPublicPath().'plugins')) {
            throw new Unus_Plugin_Exception('Plugin directory cannot be found; Please check the path '.Unus::getPublicPath().'plugins');
        }

        $pluginList = Unus_Helper_Directory_Clean::stripArray(scandir(Unus::getPublicPath().'plugins/'));

        $pluginDir = Unus::getPublicPath().'plugins/';

        if (is_array($pluginList)) {
            if ($scan && $new == false) {
                return $pluginList;
            }
            $newPlugins = array();
            foreach ($pluginList as $dir) {
                if (!$this->isRegisterd($dir)) {
                    if (is_readable($pluginDir.$dir.'/core.php') && $new == false) {
                        include_once $pluginDir.$dir.'/core.php';
                        $this->addPlugin($dir, array('class' => $config['class'], 'method' => $config['method']), $config['event']);
                    } else {
                        $newPlugins[] = $dir;
                    }
                }
            }
            if ($scan == true && $new == true) {
                return $newPlugins;
            }
        }
        return true;
    }

    /**
    * Registers a newly installed plugin, it checks
    * for the plugins optional configuration file
    * and installs the plugin information into the database
    *
    * @param  str    $name      Name of Plugin
    * @param  array  $callBack  Class and method call
    * @param  str    $event     Event that plugin will exec
    * @access private
    *
    * @return bool
    */

    private function _registerPlugin($name, $callBack, $event = null)
    {
        // Attempt to load the Plugins config file to get information
        if (is_readable(Unus::getPublicPath().'plugins/'.$name.'/info.php')) {
            require_once Unus::getPublicPath().'plugins/'.$name.'/info.php';
        } else {
            // Set Defaults
            $config = array();
            $config['author'] = 'Unknown Author';
            $config['image'] = 'images/defaultPlugin.jpg';
            $config['version'] = '0.01';
            $config['website'] = 'Unavaliable';
            $config['support'] = 'support@nwhiting.com';
            $config['date'] = time();
        }
        $sql = 'INSERT INTO '.PLUGINS.'
                (name, author, image, version, date, install, class, method, event, website, support)
                VALUES
                ("'.$name.'", "'.$config['author'].'", "'.$config['image'].'", "'.$config['version'].'", "'.$config['date'].'",
                "'.time().'", "'.$callBack['class'].'", "'.$callBack['method'].'", "'.$event.'", "'. $config['website'].'", "'. $config['support'].'")';
        Unus::registry('db')->query($sql);

        return true;
    }

    /**
    * Deletes a plugin from app/public/plugins and removes data from database
    * This method can only be called from within the administration
    *
    * @param  str  $id  Plugin to delete
    *
    * @return bool
    * @throw  Unus_Plugin_Exception
    */

    protected function _unRegisterPLugin($id)
    {
        /**
        * TODO: Move this method into more secure location
        */
        if (!Unus::registry('acl')->isAllowed('admin')) {
            throw new Unus_Plugin_Exception('Disallowed Method call; Plugins can only be deleted by an administrator');
        }
        if ($this->isRegisterd($id)) {
            if (is_dir(Unus::getPublicPath().'plugins/'.$id)) {
                @unlink(Unus::getPublicPath().'plugins/'.$id);
                /**
                 * TODO: Fix suppressed deletion error
                 */
                $sql = 'DELETE FROM '.PLUGINS.' WHERE name = "'.$id.'"';
                Unus::registry('db')->query($sql);
                return true;
            }
        }

        return false;
    }

}