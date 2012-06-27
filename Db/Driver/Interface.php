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

interface Unus_Db_Driver_Interface
{
    /**
     * Return all results from database
     */
    public function all();
    /**
     * Return searched results from database
     */
    public function find($fields = array());
    /**
     * Add new entry to database
     */
    public function add($fields = array());
    /**
     * Update row in database
     */
    public function update($id, $fields = array());
    /**
     * Delete row in database
     */
    public function delete();
    /**
     * Set current model to use
     */
    public function setModel($name);
    /**
     * Add a where clause to sql statement
     */
    public function where($fields);
    /**
     * Add a order clause to sql statement
     */
    public function order($str);
    /**
     * Add a limit clause to sql statement
     */
    public function limit($start, $limit = 30, $offset = 0);
    /**
     * Builds and executes SQL Statement
     */
    public function build();
    /**
     * Set fetch method to return query results
     */
    public function fetchMethod($method);
}
