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


/********************************************************************
 *
 *
 *  THIS FILE LOADS THE ENTIRE CORE FOR UNUS
 *  DO NOT ALTER THIS FILE IN ANY WAY YOU SHOULD NOT NEED TO
 *  IF YOU DO THEN PLEASE SUBMIT A BUG/FEATURE REQUEST
 *
 *  HAVE FUN AND HAPPY CODING!
 *
 ********************************************************************/

// INCLUDE PATHS

// CHECK PHP VERSION
if (version_compare(phpversion(), '5.2.0', '<')) {
    exit('Unus Currently Only Supports PHP 5.2.0+. For more information please visit <a href="http://www.php.net">http://www.php.net</a>');
}

/**
 * @category   Unus
 * @package    Unus
 * @version    $Rev: 1$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

final class Unus
{

    /**
     * Current Version of Unus
     */

    static private $_version = '0.1 (infáns)';

    /**
     * Instance of self
     */

    static private $_instance = null;

    /**
     * Registry stores the Unus_Object Data
     */

    static private $_registry = null;

    /**
     * Array of protected names that cannot be overwritten in registry
     */

    static private $_protectedNames = array('events', 'view', 'plugins', 'session', 'acl', 'user', 'db', '_INIT_MICRO');

    /**
     * Registers to true once self::init() has loaded to prevent unauthorized core changes
     */

    static private $_initLoaded = false;

    /**
     * Directory our modules are installed
     */
    static private $_moduleDirectory = 'modules';

    /**
     * Directory our templates
     */
    static private $_templateDirectory = 'skin';

    /**
     * Directory our plugins
     */
    static private $_pluginDirectory = 'plugins';

    /**
     * Directory of var used for logs
     */
    static private $_varDirectory = 'var';

    /**
     * Web path used to access Unus index.php files
     */
    static private $_webPath = '/';

    /**
     * Error log level
     * Defaults to 4 - All Errors
     */
    static private $_logErrors = 4;

    /**
     * Error log file
     * Defaults var/logs/error.log
     */
    static private $_errorLogFile = 'var/logs/error.log';

    /**
     * List of active core applications to be loaded into Unus
     *
     * DEFAULTS ONLY TO VIEW, SESSION AND EVENT DISPATCHER
     */
    static public $installedApps = array();

    /**
     * List of core applications to be loaded into Unus on init()
     *
     */
    static private $_requiredApps = array('session', 'view', 'events', 'models');

    /**
     * Path to library files
     *
     * Defaults to library/
     */
    static private $_libraryPath = 'library/';

    /**
     * Path to application files
     *
     * Defaults to app/
     */
    static private $_applicationPath = 'app/';

    /**
     * Path to administration
     *
     * Defaults to admin
     */
    static private $_adminPath = 'admin';

    /**
     * Unus init has started
     */
    static private $_init = false;

    /**
     * Sets the path to the Unus Library
     *
     * @param  string  path  Path to the Unus Library always append a /
     */

    public static function setLibraryPath($path) {
        self::$_libraryPath = $path;
        set_include_path($path . PATH_SEPARATOR . get_include_path());
    }

    /**
     * Sets the path to the Unus Applicatio
     *
     * @param  string  path  Path to the Unus Library always append a /
     */

    public static function setApplicationPath($path) {
        self::$_applicationPath = $path;
        set_include_path($path . PATH_SEPARATOR . get_include_path());
    }

	/**
     * Returns the path to the Unus Application File
     *
     * @return  string
     */

    public static function getApplicationPath() {
        return self::$_applicationPath;
    }

    /**
     * Returns the path to the Unus library
     *
     * @return  string
     */

    public static function getLibraryPath() {
        return self::$_libraryPath;
    }

    /**
     * Returns the directory for modules
     */

    public static function getModulesDir() {
        return self::$_moduleDirectory;
    }

    /**
     * Sets the modules directory
     *
     * @param  string  dir  Absolute path for module location
     */

    public static function setModulesDir($dir) {
        self::$_moduleDirectory = $dir;
    }

    /**
     * Returns the directory for templates
     */

    public static function getTemplateDir() {
        return self::$_templateDirectory;
    }

    /**
     * Sets the default templates directory
     *
     * @param  string  dir  Absolute path for template location
     */

    public static function setTemplateDir($dir) {
        self::$_templateDirectory = $dir;
        Unus_View::getInstance()->addScriptPath($dir);
    }


    /**
     * Returns the directory for plugins
     */

    public static function getPluginDir() {
        return self::$_moduleDirectory;
    }

    /**
     * Sets the plugins directory
     *
     * @param  string  dir  Absolute path for plugins
     */

    public static function setPluginDir($dir) {
        self::$_pluginDirectory = $dir;
    }

    /**
     * Returns website web path
     */

    public static function getPath() {
        return self::$_webPath;
    }

    /**
     * Returns website web path
     *
     * @param  string  path   Path used to access the index.php file
     */

    public static function setPath($path) {
        self::$_webPath = $path;
    }

    /**
     * Returns var directory location
     */

    public static function getVarDir() {
        return self::$_varDirectory;
    }

    /**
     * Sets the location of the var directory
     *
     * @param  string  path  Absolute path to var directory
     */

    public static function setVarDir($dir) {
        self::$_varDirectory = $dir;
    }

    /**
     * Returns current Unus Version
     */

    public static function getVersion() {
        return self::$_version;
    }

    /**
     * Returns displayable copyright information
     */

    public static function getCopyright() {
        return 'Website Powered By <span class="highlight">Unus '.self::getVersion().'</span> Original Source Code &copy; 2008-2009 Nickolas Whiting';
    }

    public static function init()
    {
        foreach (self::$_requiredApps as $v) {
            switch($v) {
                case 'view':
                    self::register('view', Unus_View::getInstance());
                    break;
                case 'models':
                    self::register('models', Unus_Model::getInstance());
                    break;
                case 'session':
                    self::register('session', Unus_Session::getInstance());
                    break;
                case 'events':
                    self::register('events', Unus_Event::getInstance());
                    break;
            }
            self::$installedApps[] = $v;
        }

        self::$_init = true;
    }

    /**
     * Adds a new core application to be loaded into Unus
     * This can accept any number of params each will be added to our installed apps list
     */
    public static function registerApp()
    {
        if (self::$_init == false) {
            include('Exception.php');
            throw new Unus_Exception('Unus::init() has not started and Unus cannot call Unus::registerApp()');
        }

        $args = func_get_args();

        if (count($args) == 0) {
            include('Exception.php');
            throw new Unus_Exception('Failed to recieve any applications to activate through Unus::registerApp()');
        }
        // add app to app list
        // Load installed apps
        foreach ($args as $v) {
            if (is_array($v)) {
				$registerName = explode('_', $v[0]);
                self::register($registerName[count($registerName) - 1], $v[1]);
                self::$installedApps[] = $v[0];
            } else {

				// Check for model file and load if exists
				$file = Unus_Helper_Text_Include::convert($v);


				if (file_exists(self::$_applicationPath.$file.'/models.php')) {
					include($file.'/models.php');
				} elseif (file_exists(self::$_libraryPath.$file.'/models.php')) {
					include($file.'/models.php');
				}

				if (file_exists(self::$_applicationPath.$file.'/admin.php')) {
					include($file.'/admin.php');
				} elseif (file_exists(self::$_libraryPath.$file.'/admin.php')) {
					include($file.'/admin.php');
				}


				if (file_exists(self::$_applicationPath.$file.'/routes.php')) {
					include($file.'/routes.php');
				} elseif (file_exists(self::$_libraryPath.$file.'/routes.php')) {
					include($file.'/routes.php');
				}

				if (file_exists(self::$_applicationPath.$file.'/dependents.php')) {
					include($file.'/dependents.php');
				} elseif (file_exists(self::$_libraryPath.$file.'/dependents.php')) {
					include($file.'/dependents.php');
				}

				$className = Unus_Helper_Text_Include::ucClass($v);
				$registerName = explode('_', $v);

				$registerName = ($registerName[count($registerName) - 1] == null) ? $registerName[count($registerName)] : $registerName[count($registerName) - 1];

				if (file_exists(self::$_libraryPath.$file.'.php') || file_exists(self::$_applicationPath.$file.'.php')) {
					if (method_exists($className, 'getInstance')) {
						self::register($registerName, call_user_func(array( $className, 'getInstance')));
					} else {
						self::register($registerName, new $className());
					}
				}
                self::$installedApps[] = $v;
            }
        }
    }

    /**
     * Sets or retrieves the current administration path
     *
     * @param  string  Path  Path to the administration. Leave blank to retireve current path
     *
     * @return  string
     */
    public static function adminPath($path = null)
    {
        // disallow changing of the admin path once we have loaded init();
        if (self::$_initLoaded && null != $path) {
            throw new Unus_Exception('Unus init has been called and the administration path cannot be changed');
        }
        if (null != $path) {
            self::$_adminPath = $path;
        }
        return self::$_adminPath;
    }

	public static function getAdminPath($sitePath = true)
	{
        $path = null;

        if ($sitePath) {
            $path = self::getPath();
        }

		$fullPath = $path.self::$_adminPath;

		$return = str_replace('//', '/', $fullPath);

        return $return;
	}

    /**
     * Loads Core Registry
     *
     * Initializes first DB connection
     */

	public static function dispatch()
	{
        // Start off with the data object as the core
        if (null == self::$_registry) {
            self::$_registry = new Unus_Object();
        }
        // include error codes
        require_once self::getLibraryPath().'Unus/Exception/error_codes.php';

        // Debug mode
        if (self::isDeveloperMode()) {
            // ERROR REPORTING
            error_reporting(E_ALL &  ~E_STRICT);
            // UNUS SYSTEM MONITORING
            ob_start();
            Unus_Development_Benchmark::start('unus core');
        } else {
            error_reporting(0);
        }

        // Setup our application include paths
        $path = array();

        $path[] = self::getPluginDir();
        $path[] = self::getModulesDir();
        $path[] = self::getLibraryPath();

        foreach ($path as $v) {
            set_include_path($v . PATH_SEPARATOR . get_include_path());
        }


        set_error_handler(array('Unus_Exception_Handler', 'errorHandler'));
        set_exception_handler(array('Unus_Exception_Handler', 'exceptionHandler'));

        self::$_initLoaded = true;

        if (defined('MEDIA_URL')) {
            self::register('media_url', MEDIA_URL);
        }

        if (defined('ADMIN_MEDIA_URL')) {
            self::register('admin_media_url', ADMIN_MEDIA_URL);
        } elseif (null != self::registry('media_url')) {
            self::register('admin_media_url', MEDIA_URL.'admin/');
        }

        if (defined('SCRIPT_SOURCE_URL')) {
            self::register('script_source_url', SCRIPT_SOURCE_URL);
        } elseif (null != self::registry('media_url')) {
            self::register('script_source_url', MEDIA_URL);
        }

        Unus_Dispatch::getInstance()->dispatch();

        if (self::isDeveloperMode()) {
            // UNUS SYSTEM MONITORING
            Unus_Development_Benchmark::stop('unus core');
        }
	}

    /**
     * Adds a new variable to the registry
     *
     * @param  string  key          Name of the registry key
     * @param  mixed   value        Value of registry key
     * @param  bool    override     Override registry key if it already exists
     * @param  bool    append       Append value to current key if it exists
     *
     * @return
     */

    public static function register($key, $value = null, $override = true, $append = false)
    {
        if (null == self::$_registry) {
            self::$_registry = new Unus_Object();
        }
        if (in_array($key, self::$_protectedNames) && self::$_initLoaded == true) {
            throw new Unus_Exception('Property Name '.$key.' cannot be registered');
        }

        if (null != self::$_registry->getData($key)) {
            if ($append) {
                $data = self::$_registry->getData($key);
                self::$_registry->setData($key, $value.$data);
            } elseif ($override) {
                self::$_registry->setData($key, $value);
            } else {
                return false;
            }
        } else {
            self::$_registry->setData($key, $value);
        }

        return true;
    }

    /**
     * Destroys a key from the registry
     *
     * @param  string  key  Key to destory
     *
     * @return
     */

    public static function unregister($key)
    {
        if (null == self::$_registry) {
            self::$_registry = new Unus_Object();
        }
        if (in_array($key, self::$_protectedNames)) {
            throw new Unus_Exception('Destroying protected registry keys is not allowed');
        }
        if(null != self::$_registry->getData($key))
        {
            if (is_object(self::$_registry->getData($key)) && method_exists(self::$_registry->getData($key), '__destruct'))
            {
                self::$_registry->getData($key)->__destruct();
            }
            self::$_registry->unsetData($key);
        }
    }

    /**
     * Return a key's value from the registry
     *
     * @param  string  key  Name of key which value to retrieve
     *
     * @return
     */

    public static function registry($key)
    {
        if (null == self::$_registry) {
            self::$_registry = new Unus_Object();
        }
        return self::$_registry->getData($key);
    }

    /**
     * Dumps data for debugging purposes
     */

    public static function dump($data, $return = false)
    {
        if ($return) {
            ob_start();
            var_dump($data);
            $contents = ob_get_contents();
            ob_clean();
            return $contents;
        } else {
            echo '<pre>';
            var_dump($data);
            echo '</pre>';
        }
    }

    /**
     * Dumps Unus registry and exists
     */

	public static function regDump()
	{
		exit(self::dump(self::$_registry));

	}

    /**
     * Dispatches event to event listener for observers to hear
     *
     * @param  string  event  Event to dispatch to event listener
     *
     * @return
     */

	public static function dispatchEvent($event)
	{
		Unus_Event::getInstance()->dispatchEvent($event);
	}

    /**
     * Adds event observer
     *
     * @param  string  name           Name of the observer
     * @param  array   callback       Array of class::method which will be called when observer dispatched
     * @param  string  eventCall      Name of the event to listen for and dispatch once heard
     * @param  string  observerClass  Class used to store and call observer
     *
     * @return
     */

	public static function addObserver($name, $callBack = array(), $eventCall = null, $observerClass = null)
	{
		if ($observerClass == '') {
			$className = 'Unus_Event_Observer';
		} else {
			$className = $observerClass;
		}
		$observer = new $className($name, $eventCall, $callBack);
        if (!$observer instanceof Unus_Event_Observer_Interface) {
            throw new Unus_Event_Observer_Exception('Cannot add observer '.$name.' class '.$className.' must implement interface Unus_Event_Observer_Interface');
        }
		$observer->setName($name)->setEventCall($eventCall)->setCallback($callBack);
		Unus_Event::getInstance()->addObserver($observer);
	}

    /**
     * Returns setting variable
     *
     * @param  string  name  Setting variable to retrieve
     *
     * @return
     */

    public static function getSetting($name)
    {
        $settings = self::registry('settings');
        if (array_key_exists($name, $settings)) {
            return $settings[$name];
        }
        return false;
    }

    /**
     * Recursively loads files in a directory to establish routes
     *
     * @param  string  dir  Directory to parse for route files
     *
     * @return
     */

    public static function loadRoutes($dir)
    {
        // load router files
        $dir = Unus_Helper_Directory_Clean::stripArray(scandir($dir));
        foreach ($dir as $k => $v) {
            if (is_dir($routerDir.$v)) {
                self::loadRoutes($routerDir.$v.'/');
            } else {
                include_once($routerDir.$v);
            }
        }
    }

    /**
     * Language Translator
     *
     * TODO : Unus_Local
     */

    public static function __($str)
    {
        return $str;
    }

    /**
     * Error Logging
     * ------------------------------
     *   0 - None (Error Logging Off)
     *   1 - Recoverable Errors (Notices, Parse Errors, Warnings, Strict and Fatal Recoverable Errors)
     *   2 - Fatal Unrecoverable Runtime Errors (Includes Unus Errors as they are considered as fatal unrecoverable)
     *   3 - Unus Errors
     *   4 - All Errors Except Recoverable
     *   5 - All Errors
     *
     * @param  int  set  Level for error logging
     *
     * @return int
     */
    public static function logErrors($set = null)
    {
        if (null != $set) {
            self::$_logErrors = (int) $set;
        }

        return self::$_logErrors;
    }

    /**
     * Error Log File
     *
     * @param  string  file  Location to error log
     *
     * @return string
     */

    public static function errorLogFile($file = null)
    {
        if (null != $file) {
            self::$_errorLogFile = (int) $file;
        }

        return self::$_errorLogFile;
    }

    /**
     * Sets or returns developer mode.
     * Setting to on also enables FirePHP Unus Profiling...Which logs
     * memory usuage, db queries, execution times etc.. etc...
     *
     * @param  boolean  flag
     *
     * @return  boolean
     */
    public static function isDeveloperMode($flag = null)
    {
        if ($flag !== null) {
           Unus_Development::setDevMode($flag);
        }

        return Unus_Development::getDevMode();
    }

    /**
	 * Returns a model class instance
	 *
	 * @var array
	 */

    public static function getModel($className, $isCore = false)
    {

        $classKey = $className;
        $className = 'Model_'.ucwords($className);
        if ($isCore) {
            $className = 'Unus_'.$className;
        }
        $class = new $className();
        return $class;
    }

    /**
	 * Returns a singleton class instance
	 *
	 * @var array
	 */

    public static function getSingleton($className)
    {
        if (self::registry($className))
        {
            return self::registry($className);
        }
        else
        {
            $class = new $className();
            self::register($className, $class);
            return $class;
        }
    }

    /**
     * Adds a setting to the registry
     * Settings must be added before init() is called and cannot be modified after init has been called
     * A setting is called just as any other registry property but is also defined as strtoupper($name)
     *
     * @param  string  name   Name of setting
     * @param  mixed   value  Value of setting
     */
    public static function setting($name, $value, $constant = false)
    {
        if (self::$_initLoaded == true) {
            throw new Unus_Exception('Unus::setting must be called before running Unus::init()');
        }

        // add setting to protected names
        self::$_protectedNames[] = $name;

        // register setting
        self::register($name, $value);

        // define setting
        if ($constant) define(strtoupper($name), $value);
    }

    public static function getRegistry()
    {
        return self::$_registry;
    }
}


/*
 * Translates strings/phrases
 *
 * @return string
 */
function __($str) {
	return Unus::__($str);
}