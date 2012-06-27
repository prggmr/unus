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

class Unus_Helper_File_Read_Lines
{

    /**
     * Reads and parses lines contained in a file lines and returns array
     *
     * @param  string  $file    File to parse
     * @param  int     $start   Line to start parsing
     * @param  int     $number  Number of lines +/- to parse
     *
     * @return array
     */

    public static function parse($file, $start = 1, $number = 10)
    {
        if (!file_exists($file)) {
            return array();
        }
        $file = file($file);
        $array = array();

        if ($start != 1) {
            for ($i = 0; $i <= $number; $i++) {
                if (($start - $number) < 1) {
                    $counted = $i;
                    $lineBegin = ($start - $number);
                    break;
                } else {
                    $lineBegin = 1;
                }
            }

            if ($lineBegin <= 0) {
                $lineBegin = 1;
                $counted = $number - $lineBegin;
            }

            if (!isset($counted)) {
                $counted = $number;
                $lineBegin = $start - $number;
            }

            for ($i = 1; $i <= $counted; $i++) {
                if ($lineBegin >= 1 && array_key_exists($lineBegin, $file)) {
                    $array[($lineBegin + 1)] = $file[$lineBegin];
                    $lineBegin = $lineBegin + 1;
                } else {
                    break;
                }

            }
        }

        $line = $start;
        for ($i = 0; $i <= $number; $i++) {
            if ($line >= 1 && array_key_exists($line, $file)) {
                $array[($line + 1)] = $file[$line];
                $line = $line + 1;
            } else {
                break;
            }
        }

        return $array;
    }
}

?>