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

class Unus_Helper_Directory_Clean
{

    /**
     * Strips . and .. from directory str/array
     *
     * @param  mixed  $str
     * @return string
     */

    public static function stripArray($str)
    {
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = self::stripArray($v);
                // Bug
                if (null == $str[$k]) {
                    unset($str[$k]);
                }
            }
            return $str;
        /**
         * BUG FIX: Will not return files
         */
        } elseif (strpos($str,'.') === false || strpos($str,'.') > 0) {
            return $str;
        }
    }
}

?>
