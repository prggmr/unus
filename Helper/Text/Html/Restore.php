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

class Unus_Helper_Text_Html_Restore
{
    /**
     * Attempts to fix any broken html tags
     *
     * @param string $input
     */

    public static function restore($input)
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
