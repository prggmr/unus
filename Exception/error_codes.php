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
 * UNUS SYSTEM ERROR CODES
 *
 * These codes are used by unus to give a more detailed error reporting system, rather than
 * PHP's current reporting system.
 *
 * Note that this is still experimental and not all codes will be implemented
 * It is highly unrecommended to change or add new codes if you ever plan on
 * upgrading Unus
 */

define('U_INVALID_CONTROLLER', 100);
define('U_INVALID_ACTION', 101);
define('U_USER_ERROR', 102);
define('U_NOTICE', 103);
define('U_ERROR', 104);
define('U_PARSE', 105);
define('U_AUTHORIZATION_ERROR', 106);
define('U_FATAL', 107);
define('U_INVALID_VIEW', 108);

/**
 * Triggers a Unus_Error will be handled the exact same as all other PHP errors
 * ... unfortunatly we cannot use trigger_error because of php limitations ...
 *
 * @param  string  message  Error String
 *
 * @return
 */

function unus_error($message, $code = 103)
{
    $backtrace = debug_backtrace();
    Unus_Exception_Handler::errorHandler($code, $message, $backtrace[0]['file'], $backtrace[0]['line']);
}
