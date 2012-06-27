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

class Unus_Helper_Text_Limit
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

            $a = 1;

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

        return $return;
    }
}
