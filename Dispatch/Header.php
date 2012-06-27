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

class Unus_Dispatch_Header
{
    /**
     * List of HTTP Message Codes
     */
    private static $_codes = array(
        // 2xx Codes
        '200' => 'Ok',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        // 3xx Codes
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' =>  false,  //unused reserved code @throws Notice
        '307' => 'Temporary Redirect',
        // 4xx Codes
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required', // Unused reserved code @throws Notice
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method not allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI to Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Request Range Not Satisfiable',
        '417' => 'Expectation Failed',
        // 5xx Codes
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavaliable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported'
    );

    /**
     * Triggers a HTTP Header Status Code
     *
     * Codes
     * ----------------------------------
     *
     *   // 2xx Codes
        '200' => 'Ok',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        // 3xx Codes
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '306' =>  false,  //unused reserved code @throws Notice
        '307' => 'Temporary Redirect',
        // 4xx Codes
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required', // Unused reserved code @throws Notice
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method not allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI to Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Request Range Not Satisfiable',
        '417' => 'Expectation Failed',
        // 5xx Codes
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavaliable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported'
     *
     * @param  int  code  HTTP Status code to send
     *
     */

    public static function triggerCode($code)
    {
        if ($code == '402' || $code == '306') {
            unus_error('HTTP STATUS CODE : '.$code.' is reserved and does not change the http status', U_NOTICE);
        }

        if (!array_key_exists($code, self::$_codes)) {
            throw new Unus_Dispatch_Header_Exception('Status Code '.$code.' is a unknown HTTP status code');
        }
		
        // dispatch code and send http code message
		header('Status: '.$code.' '.self::$_codes[$code]);
		Unus::dispatchEvent('http_code_'.$code);
    }
}
