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

class Unus_Helper_Admin_Navigation_Build
{
    public static $links = array();

    public static function addLink($title, $link, $parent = null) {
        if ($parent != null) {
            if (!isset(self::$links[$parent])) {
                self::$links[$parent] = null;
            } else {
                if (!is_array(self::$links[$parent])) {
                    $linkParent = self::$links[$parent];
                    self::$links[$parent] = array('link' => $linkParent);
                }
                self::$links[$parent]['children'][] = array($title, $link);
                return true;
            }
        } elseif (!isset(self::$links[$title])) {
            self::$links[$title] = $link;
            return true;
        }
        return false;
    }

    /**
     * Builds administration navigation based on $_links
     *
     * @return string
     */

    /**public static function build()
    {
        $return = null;
        foreach (self::$_links as $key => $val) {
            $return .= ($return == null) ? '' : ' | ';
            $return .= '<a href="'.Unus::getPath().$val.'">'.$key.'</a>';
        }
        return $return;
    }*/
}

?>