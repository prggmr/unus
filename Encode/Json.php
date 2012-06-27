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


class Unus_Encode_Json
{
    /**
     * Convert PHP to Json Strings
     *
     * @param  mixed  var       String, Interger, Array Object to convert to Json
     * @param  int    options
     *
     * @return string
     *
     * @todo    PHP 5.3 Support add additonal $options Flag
     *          support will be provided for options param
     *          will not be directly inserted until Unus goes to 5.3
     */
    public static function encode($var, $options = null)
    {
        if ($options != null) {
            return json_encode($var, $options);
        } else {
            return json_encode($var);
        }
    }
}
