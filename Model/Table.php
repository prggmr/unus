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

class Unus_Model_Table extends Unus_Object
{
    /**
     * Allowed Database table field types
     *
     */

    public $fieldTypes = array(
                                 'PrimaryKey',
                                 'BooleanField',
                                 'CharField',
                                 'DateField',
                                 'DateTimeField',
                                 'DecimalField',
                                 'EmailField',
                                 'FileField',
                                 'FloatField',
                                 'IntergerField',
                                 'IpAddressField',
                                 'NullBooleanField',
                                 'SlugField',
                                 'PasswordField',
                                 'PositiveIntergerField',
                                 'TextField',
                                 'UrlField',
                                 'ForeignKeyField',
                                 'ManyToMany',
                                 'OneToOne',
								 'OneToMany',
                                 //'OneToMany'
                                 );
    const PRIMARY    = 'PrimaryKey';
    const BOOLEAN    = 'BooleanField';
    const CHAR       = 'CharField';
    const DATE       = 'DateField';
    const DATETIME   = 'DateTimeField';
    const DECIMAL    = 'DecimalField';
    const EMAIL      = 'EmailField';
    const FILE       = 'FileField';
    const FLOAT      = 'FloatField';
    const INTERGER   = 'IntergerField';
    const IP         = 'IpAddressField';
    const NULL       = 'NullBooleanField';
    const SLUG       = 'SlugField';
    const PASSWORD   = 'PasswordField';
    const POSTITIVE  = 'PositiveIntergerField';
    const TEXT       = 'TextField';
    const URL        = 'UrlField';
    const FOREIGN    = 'ForeignKeyField';
    const MANYTOMANY = 'ManyToMany';
    const ONETOONE   = 'OneToOne';
	const ONETOMANY   = 'OneToMany';
    //const ONETOMANY  = 'OneToMany';

    /**
     * Default Options Available for a field type
     */

    public $options = array(
                             'max_length' => 200,
                             'index' => false,
                             'editable' => true,
                             'help_text' => null,
                             'unique' => false,
                             'unique_for_date' => null,
                             'unique_for_month' => null,
                             'unique_for_year' => null,
                             'verbose_name' => null,
                             'admin' => true,
                             'add' => true,
                             'delete' => true,
                             'edit' => true
                             );

    public function __construct($tblName)
    {
        $this->_validateName($tblName);
        $this->setData('id', $tblName);
        $this->setData('database', $this->convertTableName($tblName));
        $this->setData('config', new Unus_Object());
        $this->getData('config')->addData(array('add' => true, 'delete' => true, 'edit' => true, 'admin' => true));
        //$this->setData('fields', array());
    }

    /**
     * Converts a given name to the database representation
     *
     * @param  string  tblName  Name of the Database Table
     *
     * @return
     */

    public function convertTableName($tblName)
    {
        $this->_validateName($tblName);

        $str = strtolower($tblName);
    }

    /**
     * Validates a given string for database use
     *
     * @param  type  name  desc
     *
     * @throws Unus_Model_Abstract_Exception
     */

    private function _validateName($str)
    {
        if ($str == null || !preg_match('/([a-zA-Z_0-9]+)/', $str)) {
            throw new Unus_Model_Table_Exception('Table and Field names may include only alphanumeric characters and underscores; '.$str.' given');
        }
    }

    /**
     * Adds a field to the table structure
     *
     * @param  type  name  desc
     *
     * @return
     */

    public function addField($name, $type, $options = array())
    {
        $this->_validateName($name);

        if (!in_array($type, $this->fieldTypes)) {
            throw new Unus_Model_Table_Exception('Field Type <strong>'.$type.'</strong> is not a known data type');
        }

        if (null != $this->getData('fields') && array_key_exists($name, $this->getData('fields'))) {
            throw new Unus_Model_Table_Exception('Field Type by name '.$name.' allready exists in this table');
        }

        $fieldData = $this->validateField($name, $type, $options);

        $fields = $this->getData('fields');
        $fields[$fieldData['name']] = $fieldData;
        $this->setData('fields', $fields);
    }

    /**
     * Validates a model field auto adding needed configuration values
     *
     * @param  type  name  desc
     *
     * @return
     */

    public function validateField($name, $type, $options)
    {
        $return = array('name' => $name, 'type' => $type);
        $options = array_merge($this->options, $options);
        switch ($type) {
            case 'CharField':
                // Default Length for charfield
                $options['max_length'] = (null == $options['max_length'] || !is_int($options['max_length']) || $options['max_length'] > 255) ? 200 : $options['max_length'];
            break;
            // datetime for a datefield and datetimefield
            case 'DateField':
            case 'DateTimeField':
                if (!$options['auto_now'] && !$options['auto_now_add']) {
                    $options['auto_now'] = true;
                } elseif ($options['auto_add_now'] && $options['auto_now_add'] == true) {
                    $options['auto_now'] = false;
                    $options['auto_now_add'] = true;
                } elseif ($options['auto_now_add'] == false) {
                    $options['auto_now'] = true;
                }
            break;
            // max digits and decimal place for decimal field
            case 'decimalField':
                $options['max_digits'] = (null == $options['max_digit'] || !is_int($options['max_digit']) || $options['max_digit'] > 255) ? 10 : $options['max_digit'];
                if (null != $options['decimal_places'] && $options['max_digit'] < $options['decimal_places']) {
                    throw new Unus_Model_Table_Exception('Option decimal_places places the decimal at a location outside of the max_digit resolution');
                } elseif ($null != $options['decimal_places']) {
                    $options['decimal_places'] = (int) $options['decimal_places'];
                }
            break;
            // maxlength for email field
            case 'EmailField':
                $options['max_length'] = (null == $options['max_length'] || !is_int($options['max_length']) || $options['max_length'] > 255) ? 75 : $options['max_length'];
            break;

            case 'FileField':
                $options['upload_to'] = (null == $options['upload_to']) ? 'var/uploads' : $options['upload_to'];
            break;
        }

        $return['options'] = $options;

        return $return;
    }

    /**
     * Sets a configuration value for the model admin
     *
     * @param  mixed  name  Array of String of config value to set; Array is Name => Value
     * @param  mixed  value Value of config setting
     *
     * @return this
     */

    public function setConfig($name, $value = null)
    {
        $this->getData('config')->setData($name, $value);
    }

    /**
     * Finds and returns the primary key for model
     *
     * @return string
     */

    public function getPrimaryKey()
    {
        foreach ($this->getData('fields') as $k => $v) {
            if ($v['type'] == 'PrimaryKey') {
                return $k;
            }
        }
        return false;
    }

    /**
     * Finds and returns a foreign key if found if not found returns false
     *
     * @return mixed
     */

    public function hasForeignKey($boolean = false)
    {
        foreach ($this->getData('fields') as $k => $v) {
            if ($v['type'] == self::FOREIGN) {
                if ($boolean == true) {
					return true;
				} else {
					return $k;
				}
            }
        }
        return false;
    }

    /**
     * Checks if given field exists in model structure
     *
     * @param  string  field  Name of field to check for existance
     *
     * @return  boolean
     */

    public function fieldExists($field)
    {
        if (!array_key_exists($field, $this->getData('fields'))) {
            return false;
        }
        return true;
    }

    /**
     * Sets wether to render a add section in Cura
     *
     * @param  boolean  flag  True|False
     */
    public function setAdmin($flag = true)
    {
        $this->setConfig('admin', $flag);
    }

    /**
     * Sets wether to render a edit section in Cura for model data
     *
     * @param  boolean  flag  True|False
     */
    public function setAdd($flag = true)
    {
        $this->setConfig('add', $flag);
    }

    /**
     * Sets wether to render a delete section in Cura for model data
     *
     * @param  boolean  flag  True|False
     */
    public function setEdit($flag = true)
    {
        $this->setConfig('edit', $flag);
    }

    /**
     * Sets wether to render a add section in Cura for model data
     *
     * @param  boolean  flag  True|False
     */
    public function setDelete($flag = true)
    {
        $this->setConfig('delete', $flag);
    }
}
