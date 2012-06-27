<?php

/**
 * Unus
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://nwhiting.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@nwhiting.com so we can send you a copy immediately.
 *
 */



/**
 * @category   Unus
 * @package    Unus
 * @version    $Rev: 1$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

class Unus_Helper_Text_Html_Limit
{

    public static function countWords($str)
    {
        $str = explode(' ', $str);
        $a = 0;
        foreach ($str as $k) $a++;
        return $a;
    }

    /**
     * Breaks a string into X amount of words
     * and places a str and the end ** HTML SAFE
     *
     * @param string $string
     * @param int $limit
     * @param string $end
     */

    public static function limitWords($str, $limit = 25, $end = '...')
    {
        $strLength = self::countWords($str);

        if ($strLength >= $limit)
        {

            $str = explode(' ', $str);

            $return = '';

            $a = 0;

            $end_found = FALSE;

            foreach($str as $stra)
            {
                if($a <= $limit)
                {
                    $a++;
                    $return .= ' '. $stra;
                }
                elseif($end_found == FALSE)
                {
                    $end_found = TRUE;
                    $return .= '  '. $end;
                }
            }
        }
        else
        {
            $return = $str;
        }

        return self::restoreHTML($return);
    }

    /**
     * Fixes any broken html tags
     *
     * @param string $input
     */

    public static function restoreHTML($input)
    {
        $opened = $closed = array();

        // Match All Tags....
        if (preg_match_all("/<([^<>]*)>/i", $input, $matches))
        {
            foreach($matches[1] as $tag)
            {
                // Find and match Open and Closed tags in the order they are given
                if (preg_match("/^([(strong|em|u|a|span|div|img|li|center)]+)/i", $tag, $regs))
                {
                    $opened[] = $regs[0];
                }
                // Find the closing match if it exists
                elseif (preg_match("/^(\/[(strong|em|u|a|span|div|img|li|center)]+)/i", $tag, $regs))
                {
                    $closed[] = $regs[1];
                }
            }
        }

        if ($closed)
        {
            foreach ($opened as $idx => $tag)
            {
                foreach ($closed as $idx2 => $tag2)
                {
                    if ($tag2 == $tag)
                    {
                        unset($opened[$idx]);
                        unset($closed[$idx2]);
                        break;
                    }
                }
            }
        }

        if($opened)
        {
            $tagstoclose = array_reverse($opened);

            foreach($tagstoclose as $tag)
            {
                $input .= '</'.$tag.'>';
            }
        }

        return $input;
    }
}