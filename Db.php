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

class Unus_Db extends PDO
{
    /**
     * Instance PDO Statement for driver
     */
    private $_dbDriver = null;

    /**
     * List of available Unus PDO Database Drivers
     */
    private $_driverAvaliable = array('mysql', 'sqlite');

    /**
     * Username used to connect to database
     */
    protected $_username = null;

    /**
     * Database to connect
     */
    protected $_database = null;

    /**
     * Host that database is located
     */
    protected $_hostname = '127.0.0.1';

    /**
     * Password used inconjuction with username for database connection
     */
    protected $_password = null;

    /**
     * Pdo Instance
     */
    protected $_pdo = null;

    /**
     * Name of current DB Driver in use
     */
    public $_driver = null;

    /**
     * Create a new database instance
     */
    public function __construct($config)
    {

		$driver = $config['driver'];
		$database = $config['database'];
		$username = $config['username'];
		$password = (array_key_exists('password', $config)) ? $config['password'] : null;
		$hostname = (array_key_exists('hostname', $config)) ? $config['hostname'] : 'localhost';
		$profiler = (array_key_exists('profiler', $config)) ? $config['profiler'] : false;
		$options = (array_key_exists('options', $config)) ? $config['options'] : null;

        if (!in_array($driver, $this->_driverAvaliable)) {
            require_once 'Db/Exception.php';
            throw new Unus_Db_Exception('Driver : '.$driver.' is not avaliable driver for avaliable use; If you need support for this Driver please request this feature');
        }

        switch ($driver) {
            case 'mysql':
            default:
                require_once 'Db/Mysql.php';
                $dsn = 'mysql:dbname='.$database.';host='.$hostname;
                break;
			case 'sqlite':
				require_once 'Db/Sqlite.php';
				$dsn = 'sqlite:'.$database;
				break;
        }
        // init database connection
        try {
            parent::__construct($dsn, $username, $password, $options);
        } catch (Exception $e) {
            require_once 'Db/Exception.php';
            throw new Unus_Db_Exception($e->getMessage());
        }

        $this->driver = $driver;

        $driver = 'Unus_Db_'.ucfirst(strtolower($driver));
        $this->_dbDriver = new $driver($this);

		if ($profiler) {
			$this->_dbDriver->setProfiler(true);
		}

        return true;
    }

    /**
     * Sets the model to use for sql build statements
     *
     * @param  str   name   Name of model to use for sql statement builder
     *
     * @return this
     */

    public function setModel($name)
    {
        return $this->_dbDriver->setModel($name);
    }

	/**
     * Sets the model to use for sql build statements
     *
     * @param  str   name   Name of model to use for sql statement builder
     *
     * @return this
     */

    public function _use($name)
    {
        return $this->_dbDriver->setModel($name);
    }

    /**
     * Search and return rows in database based on fields
     * Build Method Uses current Model
     *
     * @param  array  fields  Fields => Values to use for finding select rows
     *
     * @return resource
     */
    public function find($fields = array())
    {
        return $this->_dbDriver->find($fields);
    }

    /**
     * Adds a new entry to the database
     * Build Method Uses Current Model
     *
     * @param  array  fields  Fields -> Values for query insert
     *
     * @return resource
     */
    public function add($fields = array())
    {
        return $this->_dbDriver->add($fields);
    }

    /**
     * Delets entry from the database
     * Build Method Uses Current Model
     *
     * @param  array  fields  Fields -> Values for query delete
     *
     * @return resource
     */
    public function delete($fields = array())
    {
        return $this->_dbDriver->delete($fields);
    }

    /**
     * Updates a row in the database
     * Build Method Uses Current Model
     *
     * @param  mixed    id      ID of primary key in database to update
     * @param  array    fields  Fields -> Values for query delete
     * @param  string   key     Name of field to use intead of primary key
     *
     * @return resource
     */
    public function update($id, $fields = array(), $key = null)
    {
        return $this->_dbDriver->update($id, $fields, $key);
    }

    /**
     * Add a where clause for sql query
     * Build Method Uses Current Model
     *
     * @param  array  clause  Array of fields->value to for where clause
     *
     * @return this
     */
    public function where($clause, $build = false)
    {
        return $this->_dbDriver->where($clause, $build);
    }

    /**
     * Add a order clause for sql query
     * Build Method Uses Current Model
     *
     * @param  array  clause  Array of field -> ASC|DESC to for order clause
     *
     * @return this
     */
    public function order($array)
    {
        return $this->_dbDriver->order($array);
    }

    /**
     * Add a limit clause for sql query
     * Build Method Uses Current Model
     *
     * @param  int  start  row # to start
     * @param  int  limit  # of rows to return
     *
     * @return this
     */
    public function limit($start, $limit = 30, $offset = null)
    {
        return $this->_dbDriver->limit($start, $limit, $offset);
    }

    /**
     * Select all fields from the table
     *
     * @return resource
     */
    public function all()
    {
        return $this->_dbDriver->build();
    }

    /**
     * Select statement selecting fields given
     *
     * @param  array  fields  Fields to select for sql statement
     *
     * @return
     */
    public function select($fields = array(), $build = false)
    {
        return $this->_dbDriver->select($fields, $build);
    }

    /**
     * Sets the fetch method
     *
     * @param  type  name  desc
     *
     * @return
     */
    public function setFetchMethod($method)
    {
        return $this->_dbDriver->fetchMethod($method);
    }

	/**
     * Sets the firebug database profiler
     *
     * @param  boolean  flag  Boolean true|false enable firebug profiler
     *
     * @return
     */
    public function setProfiler($flag)
    {
        return $this->_dbDriver->setProfiler($flag);
    }

    /**
     * Executes a prepared MySQL Statement from all() and find()
     */

    public function execute($input_parameters = array())
    {
        return $this->_dbDriver->execute($input_parameters);
    }
	
	public function build()
	{
		return $this->_dbDriver->build();
	}

    // OVERLOAD OTHER CALLS TO THE DB DRIVER..

    public function __call($name, $arguments)
    {
       return $this->_dbDriver->$name($arguments);
    }
}
