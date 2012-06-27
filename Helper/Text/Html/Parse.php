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

class Unus_Helper_Text_Html_Parse
{
    /**
     * Parses a post
     *
     * @param string $str
     * @param int $limitText
     * @param string $endDelim
     * @param bool $htmlent
     * @param bool $stripslash
     */

    public static function parsePost($str, $limitText = null, $endDelim = '...', $htmlent = false, $stripslash = false)
    {
        $str = ($htmlent == true) ? htmlentities($str) : $str;
        $str = ($stripslash == true) ? stripslashes($str) : $str;
        $str = nl2br($str);
        $str = trim($str);

        if ($limitText != null)
        {
            $limitText = (int) $limitText;
            return Unus_Helper_Text_Html_Limit::limitWords($str, $limitText, $endDelim);
        }
        else
        {
            return Unus_Helper_Text_Html_Limit::restoreHTML($str);
        }
    }

    /**
     * Make all user incoming content safe.
     * Recursivly checks arrays
     */


    public static function cure($str)
    {
        if (is_array($str))
        {
            foreach ($str as $k => $v)
            {
                $str[$k] = self::cure($v);
            }
        }
        else
        {
            $str = htmlentities(addslashes($str));
        }

        return $str;

    }


}