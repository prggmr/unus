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

class Unus_Helper_Admin_Navigation
{
    public static $links = array();

    private static $_view = null;

    /**
     * Adds a link for the administration navigation
     *
     * @param  string  title  Display title of the link (also ID)
     * @param  string  link   URL Link (Unus::getAdminPath() can be excluded)
     * @param  string  parent ID (also title) of parent to sub-catergorize
     * @param  array   array  Array of parent navigation Automatically parsed by the system as a helper
     *
     * @return boolean
     */

    public static function addLink($title, $link, $parent = false, $array = null)
    {
        $build = array();
        $id = strtolower(str_replace(' ', '_', $title));
        $build['title'] = $title;
        $build['link'] = Unus::getAdminPath().$link;

        $build['link'] = str_replace('//', '/', $build['link']);

        $array = ($array == null) ? self::$links : $array;

        if ($parent == true) {
            $parent = strtolower($parent);
            if (self::array_key_exists($parent, $array)) {
                $array[$parent][$id] = $build;
            } else {
                foreach ($array as $k => $v) {
                    if (is_array($array[$k])) {
                        $array[$k] = self::addLink($title, $link, $parent, $array[$k]);
                    }
                }
            }
        } else {
            $array[$id] = $build;
        }

        self::$links = $array;

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
     * Returns administration navigation
     */

    public static function buildNavigation($layout = 'body/navigation/item',  $array = null)
    {
        if (self::$_view == null) {
            self::$_view =  Unus::registry('view');
        }

        if ($array == null) {
            $final = true;
        }

        $array = (null == $array) ? self::$links : $array;

        $return = null;

        foreach ($array as $k) {
            if (count($k) != 1) {

                if (count($k) > 2) {
                    self::$_view->children = self::buildNavigation($layout, $k);
                }
                self::$_view->title = $k['title'];
                self::$_view->link = $k['link'];
                $return .= self::$_view->getHtml($layout);
            }
        }

        if ($final) {
            $return .= '
        <!--UNUS ADMINISTRATION NAVIGATION AUTO GENERATED : '.time().'-->
        ';
        }

        return $return;
    }
}

?>
