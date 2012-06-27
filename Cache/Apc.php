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

/**
 * APC Caching
 */
class Unus_Cache_Apc implements Unus_Cache_Interface
{
    public function enable()
    {
        ini_set('apc.enabled', 1);
    }
    
    public function disable()
    {
        ini_set('apc.enabled', 0);
    }
    
    /**
     * Adds a file to the cache. Does not overwrite any existing cache variable with same id
     *
     * @param  string  Id        Id of cache variable
     * @param  mixed   contents  Content of cache var
     * @param  int     ttl       Number of seconds to keep cache content
     *
     * @return boolean
     */

    public function add($id, $contents, $ttl = 0)
    {
        if (apc_add($id, $contents, $ttl)) {
            return true;
        }
        return false;
    }
    
    /**
     * Identical to add. Except it will overwrite contents of already existing file
     *
     * @param  string  Id        Id of cache variable
     * @param  mixed   contents  Content of cache var
     * @param  int     ttl       Number of seconds to keep cache content
     *
     * @throws Unus_Apc_Cache_Exception
     *
     * @return boolean
     */

    public function store($id, $contents, $ttl = 0)
    {
        if (apc_store($id, $contents, $ttl)) {
            return true;
        }
        return false;
    }
    
    /**
     * Clears the system cache
     *
     * @param  string  cache_type  Cache type to clear "user" clear "user" cache; otherwise system cache is cleared
     *
     * @return boolean
     */
    public function clear($cache_type = null)
    {
        return apc_clear_cache($cache_type);
    }
    
    /**
     * Deletes a variable from the cache
     *
     * @param  string  Id  Id of variable to delete
     *
     * @return boolean
     */
    public function delete($id)
    {
        return apc_delete($id);
    }
    
     /**
     * Fetches a variable from the cache
     *
     * @param  string  Id  Id of variable to fetch
     *
     * @return mixed
     */
    public function fetch($id)
    {
        return apc_fetch($id);
    }
    
    /**
     * Compliles a PHP file into bytecode
     *
     * @param  string  file  Full or relative path to file that to be compiled
     *
     * @return boolean
     */
    public function compileFile($file)
    {
        return apc_compile_file($file);
    }
}
