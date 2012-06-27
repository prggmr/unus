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

class Unus_Model_Abstract
{
    protected $_table = null;

    /**
     * Insert a new row into the database from a posted form
     *
     * @param   array   $exclude   Array of keys to exclude from the SQL statment
     *
     * @return
     */

    public function insert($exclude = array('submit'))
    {
        $fields = Unus_Helper_Text_Html_Parse::cure($_POST);

        $cols = array();

        // Build statment
        $statement = 'INSERT INTO '.$this->_table.' (';

        $a = 0;

        foreach ($fields as $k => $v) {
            // do not include
            if (!in_array($k, $exclude)) {
                $cols[$k] = $v;
                if ($a == 1) {
                    $statement .= ',';
                }
                $statement .= '`'.$k.'`';
                $a = 1;
            }
        }

        $statement .= ') VALUES (';

        $a = 0;
        // Build the fields
        foreach ($cols as $k => $v) {
            if ($a == 1) {
                $statement .= ',';
            }
            $v = ($k == 'password') ? Unus_Helper_Hash_Password::hash($v) : $v;
            $statement .= '"'.$v.'"';
            $a = 1;
        }

        $statement .= ')';

        return $this->db->query($statement);
    }

    /**
     * Update a row in the database from a posted form
     *
     * @param   string   $exclude   Identifier of row to use for row update
     * @param   array    $exclude   Array of keys to exclude from the SQL statment
     *
     * @return
     */

    public function update($key, $exclude = array('submit'))
    {
        $fields = Unus_Helper_Text_Html_Parse::cure($_POST);

        $cols = array();

        // Build statment
        $statement = 'UPDATE `'.$this->_table.'` SET ';

        $a = 0;

        foreach ($fields as $k => $v) {
            // do not include
            if (!in_array($k, $exclude) && $k != $key) {
                $cols[$k] = $v;
                if ($a == 1) {
                    $statement .= ',';
                }
                $v = ($k == 'password') ? Unus_Helper_Hash_Password::hash($v) : $v;
                $statement .= '`'.$k.'` = "'.$v.'"';
                $a = 1;
            }
        }

        if (is_array($key)) {
            $statement .= ' WHERE '.$key[0].' = "'.$key[1].'"';
        } else {
            $statement .= ' WHERE '.$key.' = "'.$_POST[$key].'"';
        }

        return $this->db->query($statement);
    }

    /**
     * Generic select statement for non-complex SQL statments
     * For complex statements Use Zend_Db_* via $this->db; or write them out manually
     *
     * @param  array  key       Array of key => ID, column => Name to use for selecting a specific row
     * @param  mixed  cols      Columns to select null to select all
     * @param  array  options   ORDER => (username DESC), LIMIT => (10,50)
     *
     * @return
     */

    public function select($key = array(), $cols = null, $options = array())
    {
        $statement = 'SELECT ';
        if (null != $cols) {
            if (is_array($cols)) {
                $a = 0;
                foreach ($cols as $v) {
                    if ($a == 1) {
                        $statement .= ',';
                    }
                    $statement .= '`'.$v.'`';
                    $a = 1;
                }
            } else {
                $statement .= '`'.$cols.'`';
            }
        } else {
            $statement .= '* ';
        }

        $statement .= ' FROM '.$this->_table.'';

        if (count($key) != 0) {
            $statement .= ' WHERE ';
            $a = 0;
            foreach ($key as $k => $v) {
                if ($a != 0) {
                    $statement .= ' AND ';
                }
                $statement .= '`'.$k.'` = "'.$v.'"';
                $a = 1;
            }
        }

        if ($options['ORDER']) {
            $statement .= ' ORDER BY '.$options['ORDER'].'';
        }

        if ($options['LIMIT']) {
            $statement .= ' LIMIT '.$options['LIMIT'].'';
        }

        return $this->db->fetchAll($statement);
    }

    // Overloading into the Unus:data object

	public function __set($name, $value)
	{
		Unus::register($name, $value);
	}

	// Overloading from the Unus:data object

	public function __get($name)
	{
		return Unus::registry($name);
	}

	// Overloading from the Unus:data object

	public function __unset($name)
	{
		Unus::unregister($name);
	}

	// Overloading from the Unus:data object

	public function __isset($name)
	{
		if (null == Unus::registry($name)) {
			return false;
		} else {
			return true;
		}
	}
}

