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

class Unus_Encode_Bytes
{
    /**
     *
     * @notes
     *
     * Bytes number conversions
     *
     * As all numbers we recieve will be in byte form we are going to include
     * only the procedures to turn bytes into *
     *
     */

    /**
     * Converts bytes to bits
     * Uses notation that 1 byte = 8 bits
     *
     * @param  int  bytes  Number to convert to Bits
     * @param  int  round  Precision of rounding. Leave false for no rounding
     */

    public static function bytes2bits($bytes, $round = false)
    {
        $kb = (8 * $bytes);

        if (is_int($round)) {
            $kb = round($kb, $round);
        }

        return $kb;
    }

    /**
     * Converts bytes to kilobytes
     * Uses notation that 1 kilobyte (K / Kb) = 2^10 bytes = 1,024 bytes
     *
     * @param  int  bytes  Number to convert to KB
     * @param  int  round  Precision of rounding. Leave false for no rounding
     */

    public static function bytes2Kb($bytes, $round = false)
    {
        $kb = ($bytes / 1024);

        if (is_int($round)) {
            $kb = round($kb, $round);
        }

        return $kb;
    }

    /**
     * Converts bytes to Megabytes
     * Uses notation that 1 megabyte (M / MB) = 2^20 bytes = 1,048,576 bytes
     *
     * @param  int  bytes  Number to convert to MB
     * @param  int  round  Precision of rounding. Leave false for no rounding
     */

    public static function bytes2Mb($bytes, $round = false)
    {
        $mb = ($bytes / 1048576);

        if (is_int($round)) {
            $mb = round($mb, $round);
        }

        return $mb;
    }

    /**
     * Converts bytes to Gigabytes
     * Uses notation that 1 gigabyte (G / GB) = 2^30 bytes = 1,073,741,824 bytes
     *
     * @param  int  bytes  Number to convert to GB
     * @param  int  round  Precision of rounding. Leave false for no rounding
     */

    public static function bytes2Gb($bytes, $round = false)
    {
        $gb = ($bytes / 1073741824);

        if (is_int($round)) {
            $gb = round($gb, $round);
        }

        return $gb;
    }

    /**
     * Converts bytes to Terabytes
     * Uses notation that 1 terabyte (T / TB) = 2^40 bytes = 1,099,511,627,776 bytes
     *
     * @param  int  bytes  Number to convert to TB
     * @param  int  round  Precision of rounding. Leave false for no rounding
     */

    public static function bytes2Tb($bytes, $round = false)
    {
        $tb = ($bytes / 1099511627776);

        if (is_int($round)) {
            $tb = round($tb, $round);
        }

        return $tb;
    }

    /**
     * Converts bytes to *
     *
     */
    public static function encode($bytes, $round = false)
    {
        switch($bytes) {
            case $bytes > 1099511627776:
                $return = self::bytes2Tb($bytes, $round) . ' TB';
                break;
            case $bytes > 1073741824:
                $return = self::bytes2Gb($bytes, $round) . ' GB';
                break;
            case $bytes > 1048576:
                $return = self::bytes2Mb($bytes, $round) . ' MB';
                break;
            case $bytes > 1024:
                $return = self::bytes2Kb($bytes, $round) . ' KB';
                break;
            default:
                $return = $bytes . ' B';
        }

        return $return;
    }
}
