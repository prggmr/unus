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

class Unus_Model extends Unus_Data
{

    /**
     * Instance of Unus_Model
     */

    private static $_instance = null;

    /**
     * Flag set if the table structure has been analyzed and built
     */
    private $_modelAnalyzed = false;

    /**
     * Constructor Set
     *
     * Enforce Singleton
     */

    private function __construct()
    {
        $this->setData('registry', new Unus_Model_Registry());
    }

    /**
    * Enforce singleton; disallow cloning
    *
    * @return void
    */
    private function __clone()
    {
    }

    /**
     *  Returns Instance of Unus_Model
     */

    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * Register a new table
     *
     * @param  string  name  Name of Data table
     *
     * @return this
     */

    public function registerTable($name)
    {
        if ($this->_modelAnalyzed == true) {
            throw new Unus_Model_Exception('Model Data structure has already been analyzed and built; Please add the registerTable code block before analyzing the model structure');
        }
        $this->getData('registry')->addModel($name, new Unus_Model_Table($name));
        $this->getData('registry')->setModel($name);
        return $this;
    }

    /**
     * Register a new table field
     *
     * @param  string  name     Name of data field
     * @param  string  type     Type of data field
     * @param  array   options  Array of options for data field (Options only for Unus_Cura)
     * @param  string  model    Data model to add field
     *
     * @return
     */

    public function registerField($name, $type, $options = array(), $model = null)
    {
        if ($this->_modelAnalyzed == true) {
            throw new Unus_Model_Exception('Model Data structure has already been analyzed and built; Please add the registerField code block before analyzing the model structure');
        }
        $this->getData('registry')->getModel($model)->addField($name, $type, $options);

        return $this;
    }

    /**
     * Sets a configuration value for the model
     *
     * @param  mixed    name      Array of String of config value to set; Array is Name => Value
     * @param  mixed    value     Value of config setting
     * @param  string   model     Name of model to set this configuration value; Leave blank if using directly after initalizing a model
     *
     * @return this
     */

    public function setConfig($name, $value = null, $model = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->setConfig($k, $v, $model);
            }
        } else {
            $this->getData('registry')->getModel($model)->setConfig($name, $value);
        }
    }
}
