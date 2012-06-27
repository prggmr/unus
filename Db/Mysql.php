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

class Unus_Db_Mysql extends PDOStatement implements Unus_Db_Driver_Interface
{
    /**
     * SQL Statement to be executed
     */
    private $_sql = null;

    /**
     * Values of where clause for exec statement
     */
    protected $_where = null;

    /**
     * Default fetch Method
     */
    protected $_fetch = PDO::FETCH_ASSOC;

    /**
     * Current Model to use for SQL Build Statements
     */
    protected $_model = null;

    /**
     * Limit attributes for sql statement
     */
    protected $_limit = null;

    /**
     * Select fields for sql statement
     */
    protected $_select = array();

	/**
     * Result of latest SQL Query
     */

	public $result = null;

    /**
     * PDO Object
     */
    protected $_pdo = null;

    /**
     *  Tables that need to be joined for a build statement
     */
    private $_joins = array();

    /**
     *  Tables that need to be called for a build statement
     */
    private $_tables = array();

	/**
     * Order Clause
     */
    private $_order = array();


    /**
     * Array of join prefixs Used to check to ensure we dont have nameing collisions
     */
    private $_prefix = array();

	/**
	 * Profile SQL Query Usage and Results with Firebug
	 *
	 */
	private $_profile = false;

    /**
     * Constructor we dont need to really do anything here
     */
    public function __construct($pdo) {
        $this->_pdo = $pdo;
    }

    public function fetchMethod($method)
    {
        switch($method) {
            case PDO::FETCH_ASSOC:
            default:
                $this->_fetch = PDO::FETCH_ASSOC;
                break;
            case PDO::FETCH_BOTH:
                $this->_fetch = PDO::FETCH_BOTH;
                break;
            case PDO::FETCH_CLASS:
                $this->_fetch = PDO::FETCH_CLASS;
                break;
            case PDO::FETCH_COLUMN:
                $this->_fetch = PDO::FETCH_COLUMN;
                break;
            case PDO::FETCH_NUM:
                $this->_fetch = PDO::FETCH_NUM;
                break;
            case PDO::FETCH_OBJ:
                $this->_fetch = PDO::FETCH_OBJ;
                break;
            case PDO::FETCH_LAZY:
                $this->_fetch = PDO::FETCH_LAZY;
                break;
        }

        return $this;
    }

	/**
	 * Sets the firebug database profiler
	 *
	 * @param  flag  boolean  Flag true|false enable or disable profiler
	 *
	 * @return this
	 */

	public function setProfiler($flag)
	{
		$this->_profile = $flag;
		if ($flag) {
			$observer = new Unus_Event_Observer('Stop DB Profiler', 'dispatch_router_routed', array('Unus_Db_Profiler', 'stopProfiler'));
			Unus_Event::getInstance()->addObserver($observer);
		}
		return $this;
	}

	public function stopProfiler()
	{
		Unus_Development_Benchmark::stop('DB Query Builds');
	}

    /**
     * Search and return rows in database based on fields
     *
     * @param  array  fields  Fields => Values to use for finding select rows
     *
     * @return resource
     */
    public function find($fields = array())
    {
		$where = array();

		foreach ($fields as $k => $v) {
			//if (is_int($k)) {
			//	$k = $v;
			//}
			if (stripos($k, '.') === false) {
				$k = $k.'.equal';
			}
			$where[$k] = $v;
		}

		$this->where($where);

		return $this->build();
    }

    /**
     * Adds a new entry to the database
     * Data values are not escaped!
     *
     * @param  array  fields  Fields -> Values for query insert
     *
     * @return resource
     */
    public function add($fields = array())
    {
        $sql = 'INSERT INTO `'.$this->_model->getData('id').'` (';

        $model = $this->_model->getData('fields');
        $a = 0;
        foreach ($fields as $k => $v) {
            // validate field exists
            if (!array_key_exists($k, $model)) {
                foreach ($model as $a => $b) {
                    $av .= $a . ': ';
                }
                throw new Unus_Db_Driver_Mysql_Exception('Field By name '.$k.' unknown avaliable fields: '.$av);
            }

            if ($a >= 1) {
                $sql .= ', `'.$k.'`';
            } else {
                $sql .= '`'.$k.'`';
            }
            $a++;
        }

        $sql .= ') VALUES (';
        $a = 0;
        foreach ($fields as $k => $v) {
            if ($a >= 1) {
                $sql .= ', :'.$k.'';
            } else {
                $sql .= ':'.$k.'';
            }
            $a++;
        }
        $sql .= ');';

		$result = $this->_pdo->prepare($sql);

		foreach ($fields as $k => $v) {
			$result->bindValue(':'.$k, $v);
		}

        try {
            $result->execute();
        } catch (Exception $e) {
            throw new Unus_Db_Driver_Mysql_Exception($e);
        }

		if (Unus_Development::getDevMode() && $this->_profile) {
			Unus_Development_Database::addResult($result);
		}

        return $result;
    }

    /**
     * Delets entry from the database
     *
     * @param  array  fields  Fields -> Values for query delete
     *
     * @return resource
     */
    public function delete($fields = array())
    {
        $sql = 'DELETE FROM `'.$this->_model->getData('id').'` WHERE';
        if (count($fields) == 0) {
            throw new Unus_Db_Driver_Mysql_Exception('Delete Build statement recieved null data for fields');
        }
        $a = 0;
        $model = $this->_model->getData('fields');
        foreach ($fields as $k => $v) {
            if (!array_key_exists($k, $model)) {
                foreach ($model as $a => $b) {
                    $av .= $a . ': ';
                }
                throw new Unus_Db_Driver_Mysql_Exception('Field By name '.$k.' unknown avaliable fields: '.$av);
            }

            if ($a >= 1) {
                $sql .= ' AND `'.$k.'` = :'.$k.'';
            } else {
                $sql .= ' `'.$k.'` = :'.$k.'';
            }
            $a++;
        }
        $sql .= ';';

		$result = $this->_pdo->prepare($sql);

		foreach ($fields as $k => $v) {
			$result->bindValue(':'.$k, $v);
		}

        try {
            $result->execute();
        } catch (Exception $e) {
            throw new Unus_Db_Driver_MySql_Exception($e->getMessage());
        }

		if (Unus_Development::getDevMode() && $this->_profile) {
			Unus_Development_Database::addResult($result);
		}



        return $result;
    }

    /**
     * Updates a row in the database
     *
     * @param  mixed    id      ID of primary key in database to update or Array of fields to use
     * @param  array    fields  Fields -> Values for query delete
     * @param  string   key     Name of field to use intead of primary key
     *
     * @return resource
     */
    public function update($id, $fields = array())
    {
        $model = $this->_model->getData('fields');
        // find PrimaryKey if none given
        if (!is_array($id)) {
            $key = $this->_model->getPrimaryKey();
        }

        $sql = 'UPDATE `'.$this->_model->getData('id').'` SET';
        if (count($fields) == 0) {
            throw new Unus_Db_Driver_Mysql_Exception('Update Build statement recieved null data for fields');
        }
        $a = 0;
        foreach ($fields as $k => $v) {
            if (!array_key_exists($k, $model)) {
                foreach ($model as $a => $b) {
                    $av .= $a . ': ';
                }
                throw new Unus_Db_Driver_Mysql_Exception('Field By name '.$k.' unknown avaliable fields: '.$av);
            }

            if ($a >= 1) {
                $sql .= ' ,`'.$k.'` = :'.$k.'';
            } else {
                $sql .= ' `'.$k.'` = :'.$k.'';
            }
            $a++;
        }

        if (!$key) {
            $a = 0;
            $sql .= ' WHERE';
            foreach ($id as $k => $v) {
                if (!array_key_exists($k, $model)) {
                    foreach ($model as $a => $b) {
                        $av .= $a . ': ';
                    }
                    throw new Unus_Db_Driver_Mysql_Exception('Field By name '.$k.' unknown avaliable fields: '.$av);
                }

                if ($a >= 1) {
                    $sql .= ' AND `'.$k.'` = :'.$k.'';
                } else {
                    $sql .= ' `'.$k.'` = :'.$k.'';
                }
                $a++;
            }
        } else {
            $sql .= ' WHERE `'.$key.'` = :'.$key.'';
        }

		$result = $this->_pdo->prepare($sql);

		// bind add values
		foreach ($fields as $k => $v) {
			$result->bindValue(':'.$k, $v);
		}

		// Bind Key or Keys
		if (!$key) {
			foreach ($id as $k => $v) {
				$result->bindValue(':'.$k, $v);
			}
		} else {
			$result->bindValue(':'.$key, $id);
		}

        try {
            $result->execute();
        } catch (Exception $e) {
            throw new Unus_Db_Driver_MySql_Exception($e->getMessage());
        }

		if (Unus_Development::getDevMode() && $this->_profile) {
			Unus_Development_Database::addResult($result);
		}

        return $result;
    }

    /**
     * Add a where clause for sql query
     *
     * @param  array  array  Array of fields->value to for where clause
     *
     * @return this
     */
    public function where($array, $build = false)
    {
        $this->_where = $array;
		if ($build) {
			return $this->build();
		}
        return $this;
    }

    /**
     * Add a order clause for sql query
     *
     * @param  array  clause  Array of field -> ASC|DESC to for order clause
     *
     * @return this
     */
    public function order($array, $build = false)
    {
        $this->_order = $array;
		if ($build) {
			return $this->build();
		}
        return $this;
    }

    /**
     * Add a limit clause for sql query
     *
     * @param  int  start  row # to start
     * @param  int  limit  # of rows to return
     *
     * @return this
     */
    public function limit($start, $limit = 30, $offset = 0)
    {
        if ($start == false) {
            $this->_limit = false;
        } else {
            $this->_limit = array('start' => (int) intval($start), 'limit' => (int) intval($limit), 'offset' => (int) intval($offset));
        }
        return $this;
    }

    /**
     * Sets model for SQL build Statements
     *
     * @param  string  Model  Name of model
     *
     * @return this
     */
    public function setModel($model)
    {
        $this->_model = Unus_Model::getInstance()->getData('registry')->getModel($model);
        return $this;
    }

    /**
     * Build, Prepare and Execute SQL Query
     *
     * @return PDOStatement
     */
    public function build()
    {
		Unus_Development_Benchmark::start_add('DB Query Builds');
		Unus_Development_Benchmark::start('db_query');

        $model = $this->_model->getData('fields');
        // RESET OUR BUILD PARAM
        $this->_joins = array();
        $this->_tables = array();
        $this->_elements = array();

        // MutliRelations select fields used to check aganist where
        $multi = array();
        $selectAll = false;

        /**
		 * First thing we need to do is build out what fields are going to
		 * be using ofr a where statement if we are
		 */

        if ($this->_where != null) {
            foreach ($this->_where as $k => $v) {
                $found = false;
                $typehint = false;
				$andOr = 'AND';
				// unset the where as to not duplicate the statement
				unset($this->_where[$k]);

				// check for non key value pairing
				if (is_int($k)) {
					$k = $v;
				}


                // MYSQL COMPARISON OPERATORS TYPEHINTING
                // ------------------------------------
                // Typehintng is performed as
                // field.hint EX: username.like => %NI% || username.between.nick.ioana
                // Between ... and ...
                // = 	Equal operator
                // >= 	Greater than or equal operator
                // > 	Greater than operator
                // IS NOT NULL 	NOT NULL value test
                // IS NOT 	Test a value against a boolean
                // IS NULL 	NULL value test
                // IS 	Test a value against a boolean
                // ISNULL() 	Test whether the argument is NULL
                // <= 	Less than or equal operator
                // < 	Less than operator
                // LIKE 	Simple pattern matching
                // !=, <> 	Not equal operator
                // NOT IN() 	Check whether a value is not within a set of values
                // NOT LIKE 	Negation of simple pattern matching

				/**
				 * @todo NOT IN (), BETWEEN .., IN () Operators need to be set to use bindedValues, set this possibily with where and incrementing numerical value?
				 *
				 */

                if (stripos($k, '.') !== false ) {
                    $typehint = true;
                    $hinting = false;
                    $novalue = false;
					$explode = explode('.', $k);
					$k = $explode[0];

					if ($explode[1] == 'or') {
						$andOr = 'OR';
						array_shift($explode);
					}

                    switch ($explode[1]) {
                        case 'equal':
                            default:
                            $hinting = '=';
                            break;
                        case 'between':
                            $hinting = 'BETWEEN "'.$explode[2].'" AND "'.$explode[3].'"';
                            $novalue = true;
                            break;
                        case 'greater':
                            $hintint = '>';
                            break;
                        case 'greaterequal':
                            $hinting = '>=';
                            break;
                        case 'notnull':
                            $hinting = 'IS NOT NULL';
							$novalue = true;
                            break;
                        //case 'isnot':
                        //    $hinting = 'IS NOT';
                        //    break;
                        case 'isnull':
                            $hinting = 'IS NULL';
							$novalue = true;
                            break;
                        case 'is':
                            $hinting = 'IS';
                            break;
                        case 'less':
                            $hinting = '<';
                            break;
                        case 'lessequal':
                            $hinting = '<=';
                            break;
                        case 'like':
                            $hinting = 'LIKE';
                            break;
                        case 'notequal':
                            $hinting = '!=';
                            break;
                        case 'notlike':
                            $hinting = 'NOT LIKE';
                            break;
                        case 'notin':
                            $hinting = 'NOT IN (';
                            $novalue = true;
                            // loop through the values to add NOT IN
                            $notin = false;
                            foreach ($v as $a => $b) {
                                $notin .= ($notin == false) ? '"'.$b.'"' : ', "'.$b.'"';
                            }
                            $hinting .= $notin.')';
                            break;
                        case 'in':
                            $hinting = 'IN (';
                            $novalue = true;
                            // loop through the values to add NOT IN
                            $notin = false;
                            foreach ($v as $a => $b) {
                                $notin .= ($notin == false) ? '"'.$b.'"' : ', "'.$b.'"';
                            }
                            $hinting .= $notin.')';
                            break;
                    }
                }

                if ($this->_elementExists($k)) {
                    $found = true;
                }

                 // add the field to the select statement since its being used for our where clause
                if ($found == false) {
					$this->_select[] = $k;
                }

				if (stripos($k, ':') !== false) {
					$k = explode(':', $k);
					$k = $k[0];
				}

                $this->_where[$k] = array('value' => $v, 'clause' => $andOr);

                if ($typehint) {
                    $this->_where[$k]['operator'] = $hinting;
                    if ($novalue) {
                        $this->_where[$k]['noval'] = true;
                    } else {
                        $this->_where[$k]['noval'] = false;
                    }
                } else {
                    // default operator
                    $this->_where[$k]['operator'] = '=';
                }
            }
        }


		// FEILD SELECTIONS
        if (count($this->_select) == 0) {
            $this->_addField('*', $this->_model);
        } else {
            foreach ($this->_select as $k) {
				$k = trim($k);
                if (!$this->_addField($k, $this->_model)) {
                    // throw an exception if somehow the adding failed ... and we got nothing in return
                    throw new Unus_Db_Driver_Mysql_Exception('Unknown Build Select Error: Please check the logs for more information');
                }
            }
        }

        // NOW WE SHALL BUILD OUR SQL STATEMENT
        $sql = 'SELECT ';
        $a = 0;
        foreach ($this->_tables as $k => $v) {
            // loop through fields
            $prefix = (array_key_exists('prefix', $v) && $v['prefix'] != null) ? $v['prefix'].'.' : null;
            foreach ($v['fields'] as $f => $r) {
                if ($a >= 1) $sql .= ' , ';
                if (array_key_exists('magic', $v) && $r['magic'] != null) {
                    $sql .= ' '.strtoupper($r['magic']).'('.$prefix.$f.') AS';
                    if ($f == '*') {
                        $sql .= ' '.$k.'_count';
                    } else {
                        $sql .= ' '.$f;
                    }
                } else {
                    if ($f == '*') {
                        $sql .= $prefix . '*';
                    } else {
                        $sql .= $prefix.'`'.$f.'` ';
                    }
                }
                $a++;
            }
        }

        $sql .= ' FROM ';

        // ADD THE TABLES IF joining don't add and add join statement
        foreach ($this->_tables as $k => $v) {
            if (!array_key_exists('join', $v) || $v['join'] == null) {
                $sql .= ' '.$k.' ';
            }
        }
        // table joining
        if (count($this->_joins) != 0) {
            foreach ($this->_joins as $k => $v) {
                $sql .= ' LEFT JOIN ( SELECT ';
                    $a = 0;
                    foreach ($this->_tables[$k]['fields'] as $f => $e) {
                        if ($a >= 1) $sql .= ' , ';
                        $sql .= ' `'.$f.'` ';
                        $a++;
                    }
                $sql .= ', `'.$this->_tables[$k]['join']['foreign'].'`';
                $sql .= ' FROM '.$k.'';
                $sql .=  ' ) AS '.$this->_tables[$k]['prefix'].' USING ('.$this->_tables[$k]['join']['primary'].')';
            }
        }

        // WHERE
        if (null != $this->_where) {
            $sql .= ' WHERE';
            $a = 0;
            foreach ($this->_where as $k => $v) {
                if (array_key_exists('noval', $v) && $v['noval'] == true) {
                    // typehinting defines the values we want skip inserting a value
                    $sql .= ($a >= 1) ? ' '.$v['clause'].' `'.$k.'` '.$v['operator'].''  :  ' `'.$k.'` '.$v['operator'].'';
                } else {
                    $sql .= ($a >= 1) ? ' '.$v['clause'].' `'.$k.'` '.$v['operator'].' :'.$k.'' :  ' `'.$k.'` '.$v['operator'].' :'.$k.'';
                }
                $a++;
            }
        }

        // ORDER
        $a = 0;
        if (null != $this->_order) {
            $sql .= ' ORDER BY';
            if (is_array($this->_order)) {
                foreach ($this->_order as $k => $v) {
					echo $v;
                    // CHECK FOR TYPEHINTING
                    if (stripos($v, '.') !== false) {
                        $explode = explode('.', $v);
                        $field = $explode[0];
						echo $field;
                        $order = $field . ' '.strtoupper($explode[1]);
                    } else {
                        $field = $v;
                        $order = $field . ' DESC';
                    }
                    $this->_fieldExists($field, $this->_model);
                    $sql .= ($a >= 1) ? ' , '.$order : '  '.$order;
                    $a++;
                }
            } else {
                if (stripos($this->_order, '.') !== false) {
					$explode = explode('.', $this->_order);
					$field = $explode[0];
					$sql .= ' ' .$field . ' '.strtoupper($explode[1]);
				} else {
					$field = $this->_order;
					$sql .= ' ' .$field . ' DESC';
				}
            }
        }
        if ($this->_limit === false) {
            // no limit*
        } elseif (null === $this->_limit) {
            // default to 30
            //$sql .= ' LIMIT 0, 30';
        } else {
            $sql .= ' LIMIT '.$this->_limit['start'].', '.$this->_limit['limit'];
            if (null != $this->_limit['offset']) {
                $sql .= ' OFFSET '.$this->_limit['offset'];
            }
        }


        // CLOSE THE STATEMENT
        $sql .= ';';

        $result = $this->_pdo->prepare($sql);




		if (Unus_Development::getDevMode() && $this->_profile) {
			Unus_Development_Database::addBuild($this->_pdo->prepare($sql), $this->_where);
		}
		if (null != $this->_where) {
			// bind param
			foreach ($this->_where as $k => $v) {
				if (!array_key_exists('noval', $v) || $v['noval'] == false) {
					if (is_object($result)) {
						$result->bindValue(':'.$k, $v['value']);
					} else {
						throw new Unus_Db_Driver_Mysql_Exception(sprintf('PDOStatement ($result) expect object recieved %s', gettype($result)));
					}
				}
			}
		}

        try {
            $result->execute();
        } catch (Exception $e) {
            throw new Unus_Db_Driver_MySql_Exception($e->getMessage());
        }

		// flush out the statement parameters
		$this->_flush();

        return $result;
    }

    /**
     * Parses a model field to add to a select statement
     * Checks for ForeignKeys joins as needed
     *
     * Also parses magic strings such as field.count
     *
     * @param   string   name  Name of the field we need to add
     * @param   object   model Unus_Model_Table
     *
     * @return  boolean
     */

    private function _addField($name, Unus_Model_Table $model)
    {
        $join = false;
        $magic = null;
		$foreign = false;
		$oldModel = null;

        // first we check if the field is being requested from another model
        // using something like user:userId
        if (strpos($name, ':') !== false) {
            $str = explode(':', $name);
            $name = $str[0];
            $oldModel = $model;
            $model = Unus::registry('models')->getData('registry')->getModel($str[1], true);

            if (null == $model) {
                throw new Unus_Db_Driver_Mysql_Exception('Unknown model :'.$str[1].' for field selection');
            }

            $foreign = true;
        }

        if (stripos($name, '.') !== false) {
            $name = explode('.', $name);
            $magic = $name[1];
            $name = $name[0];
        }


        // check if the field were using exists!
        //$this->_fieldExists($name, $model);

        // If we are using another table check if it has a foreign key
        if ($foreign == true) {
            if ($model->hasForeignKey() != $model->hasForeignKey()) {
                throw new Unus_Db_Driver_Mysql_Exception('Model Join for '.$str[1].' does not have a foreign key to match, models cannot communicate on joins');
            }
        } elseif(!$this->_fieldExists($name, $model, false)) {
            /**
             * AUTOLOAD THE MODEL FOR THIS FIELD
             *
             * Field we are using does not exist in the current model
             * maybe it does in a joinable model?
             *
             * Lets check and find out
             *
             * NOTE:
             * Currently limited to finding the first foreign key partnership we come to
             * which may not be the model that is wanted only workaround is defining the model
             * using field:model, also may cause SQL Errors for unlike primary foreignKey names .... this is considered as expiremental
             *
             * @todo: Find fix for autoloading models with multiple foreign keys with like fields returning the unwanted field without using field:model
             */
            $found = false;
            foreach ($model->getData('fields') as $k => $v) {
                if ($v['type'] == Unus_Model_Table::FOREIGN) {
                    if ($v['options']['use'] != false) {
                        $temp = Unus::registry('models')->getData('registry')->getModel($v['options']['use']);
                        if ($this->_fieldExists($name, $temp, false)) {
                            // set our old model for reference
                            $oldModel = $model;
                            // set our new model
                            $model = $temp;
                            // found our model set and break away foreach
                            $found = true;
                            break;
                        }
                    }
                }
            }

            if ($found == false) {
                // just throw out a unknown field exception...
                $this->_fieldExists($name, $model);
            }
        }

        // Now we know what model we are using for the field lets put all this information together
        // in a parseable format for building our statement

        // first lets add our table

        // name of model table
        $table = $model->getData('id');

        if (!$this->_tableExists($table)) {
            $this->_tables[$table] = array();
        }

        //check if table has fields if not we need to set it
        if (!array_key_exists('fields', $this->_tables[$table])) {
            $this->_tables[$table]['fields'] = array();
        }

        if ($this->_elementExists($name)) {
            throw new Unus_Db_Driver_Mysql_Exception('Field '.$name.' is already in the select statement for model : '.$table);
        }

        // are we joining this table?
        if (null != $oldModel) {
            $this->_tables[$table]['join'] = array();
            // So what field are we joining well lets found out
            if ($model->getData('fields/'.$name.'/type') != Unus_Model_Table::FOREIGN) {
                /**
                 * Get the foreign key for this model
                 * and the primary key for the parent model
                 *
                 * To do this we use the foreign key in this model and the primary in the parent
                 */
                $foreignKey = $oldModel->hasForeignKey(false);

                $this->_tables[$table]['join'] = array('foreign' => $foreignKey, 'primary' => $foreignKey);
            } else {
                // if this is our foreign key lets find out if we already have it set ...
                if (null == $this->_tables[$table]['join']['foreign']) {
                    $this->_tables[$table]['join']['foreign'] = $name;
                }

                // we also need to make sure the parent exists as well
                if (null == $this->_tables[$table]['join']['primary']) {
                    $primaryKey = $oldModel->getPrimaryKey();
                    $this->_tables[$table]['join']['primary'] = $primaryKey;
                }
            }
            // we need to set our join for this table so we know
            if (!array_key_exists($table, $this->_joins)) {
                $this->_joins[$table] = true;
            }

            $this->_tables[$table]['prefix'] = $this->_generatePrefix();
        }

        if (null != $magic) {
            $this->_tables[$table]['fields'][$name] = array('magic' => $magic);
            // We need to add the magic operator at this point it doesn matter what is is that will be checked later
        } else {
            // add this field to the table
            $this->_tables[$table]['fields'][$name] = array();
        }
        return true;

    }

    /**
     * Select all fields from the table
     *
     * @return resource
     */
    public function all()
    {
        return $this->build();
    }

    /**
     * Select statement selecting fields given
     *
     * @param  array    fields  Fields to select for sql statement
     * @param  boolean  build   Flag to execute query
     *
     * @return mixed
     */
    public function select($fields, $build = false)
    {
		if (is_array($fields)) {
			$this->_select = $fields;
		} else {
			if (stripos($fields, ',') !== false) {
				$this->_select = explode(',', $fields);
			} else {
				$this->_select = array($fields);
			}
		}
		if ($build) {
			return $this->build();
		}

		return $this;
    }

    /**
     * Checks if a field exists in a model
     *
     * @param  string  field        Name of field to validate aganist model
     * @param  object  model        Model object to use for field validation
     * @param  bool    exception    Throw a exception if result is false otherwise return false
     *
     * @throws Unus_Db_Driver_Mysql_Exception
     * @return boolean
     */

    private function _fieldExists($field, Unus_Model_Table $model, $exception = true)
    {
        if (!$model->fieldExists($field) && $field != '*') {
			$av = null;
            foreach ($model->getData('fields') as $a => $b) {
                $av .= $a . ', ';
            }

            if ($exception) {
                throw new Unus_Db_Driver_Mysql_Exception('Field By name '.$field.' unknown avaliable fields: '.$av);
            }

            return false;
        }
        return true;
    }

    /**
     * Checks if a model table exists in our build statement paramaters
     *
     * @param  string  name  Name of the model
     *
     * @return boolean
     */
    private function _tableExists($name)
    {
        return array_key_exists($name, $this->_tables);
    }

    /**
     * Checks if a model fields exists in our build statement paramaters
     *
     * @param  string  model   Name of the model to use
     * @param  string  field   Name of the field to find
     *
     * @return boolean
     */
    private function _elementExists($field)
    {
        return array_key_exists($field, $this->_select);
    }

    /**
     * Generates a 4 character perfix to use for select statement joins
     * checks for existing prefix to avoid collisions
     *
     * @return str
     */
    private function _generatePrefix()
    {
        $str = null;
        $range = range('a', 'z');
        for($i = 0; $i != 4; $i++) {
            $c = rand(0, 23);
            $str .= $range[$c];
        }

        if (array_key_exists($str, $this->_prefix)) {
            $str = $this->_generatePrefix();
        } else {
            $this->_prefix[$str] = $str;
        }

        return $str;
    }

	/**
	 * Flush the SQL Statement Parameters for our next statement
	 *
	 * @param  type  name  value
	 * @param  type  name  value
	 *
	 * @return
	 */

	private function _flush()
	{
		$this->_sql = null;

		$this->_where = null;

		$this->_limit = null;

		$this->_select = array();

		$this->_joins = array();

		$this->_tables = array();

		$this->_order = array();
	}

}
