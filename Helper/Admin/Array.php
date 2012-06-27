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
 * Builds a inifitly loopless parent child relationship
 */

class Unus_Helper_Admin_Array
{
    public static $elements = array();

    private static $_view = null;
    
    // Array of Elements Awaiting to be indexed into their parents
    
    private static $_awaitingElements = array();

    /**
     * Adds a element to array list
     *
     * @param  string  title  Display title of the link (also ID)
     * @param  string  link   URL Link (Unus::getAdminPath() can be excluded)
     * @param  string  parent ID (also title) of parent to sub-catergorize
     * @param  array   array  Array of parent navigation Automatically parsed by the system as a helper
     *
     * @return boolean
     */

    public static function addElement($id, $data, $parent = false, $array = null)
    {
        $build = array();
    
        $parent = ($parent != false) ? strtolower(str_replace(' ', '_', $parent)) : $parent;
        $_id = strtolower(str_replace(' ', '_', $id));
        $build['__UN_id'] = $id;
        $build['__UN_data'] = $data;

        $array = ($array == null) ? self::$elements : $array;

        if ($parent == true) {
            
            if (!self::array_key_exists($id, self::$_awaitingElements)) {
                self::$_awaitingElements[$id] = array($data, $parent);
            }      
            
            if (self::array_key_exists($parent, $array)) {
                
                $array[$parent][$_id] = $build;
                
                unset(self::$_awaitingElements[$id]);
                
                if (count(self::$_awaitingElements) != 0) {
                    foreach (self::$_awaitingElements as $k1 => $v1) {
                        $array[$parent] = self::addElement($k1, $v1[0], $v1[1], $array[$parent]);
                    }
                }
                
            } else {
                
                foreach ($array as $k => $v) {
                    // Loop through array elements ON if it is not a preset data element
                    if (is_array($array[$k]) && !stripos('__UN__', $k)) {
                        $array[$k] = self::addElement($id, $data, $parent, $array[$k]);
                    }
                }
            }
            
        } else {

            $array[$_id] = $build;
        }
        
        
        self::$elements = $array;

        return $array;
    }

    /**
     * MODIFICATION FUNCTION
     * -----------------------
     * Checks if key exists in array Top-Level Only non-Recursive
     * array_key_exists() checks recursivly throughout an array
     *
     * @return boolean
     */

    public static function array_key_exists($key, $array)
    {
        foreach ($array as $k => $v) {
            if ($k == $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * Constructs and returns constructed html for elements array
     */

    public static function getView($template, $array = null)
    {
        if (self::$_view == null) {
            self::$_view = Unus::registry('view');
        }

        $array = (null == $array) ? self::$elements : $array;

        $return = null;

        foreach ($array as $k) {
            if (count($k) != 1) {
                if (count($k) > 2) {
                    self::$_view->children = self::getView($template, $k);
                }
                self::$_view->id = $k['_id'];
                self::$_view->data = $k['_data'];
                $return .= self::$_view->getHtml($template);
            }
        }

        return $return;
    }
}

?>