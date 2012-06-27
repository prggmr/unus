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

class Unus_Exception_Handler extends Unus_Exception
{
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $fatal = false;
        switch ($errno)
        {
            /** Fatal run-time errors.
            * These indicate errors that can not be recovered from, such as a memory allocation problem.
            * Execution of the script is halted.
            */
            case E_ERROR:
                $error = '--------- System Crashed -------- <br /> Fatal Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 2);
                $fatal = true;
            break;

            /** Run-time warnings (non-fatal errors).
             * Execution of the script is not halted.
             */
			default:
            case E_WARNING:
                $error = 'PHP Warning : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** Compile-time parse errors.
             * Parse errors should only be generated by the parser.
             */
            case E_PARSE:
                $error = 'Parse Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** Run-time notices.
             * Indicate that the script encountered something that could indicate an error, but could also happen in the normal course of running a script.
             *
             * We do not handle notice errors
             * return null if this is perhaps ever called
             */
            case E_NOTICE:
                $error = 'PHP Notice : '.$errstr.' File: '.$errfile.'('.$errline.')';
                //parent::_logError($error, 1);
				return null;
            break;

            /** Fatal errors that occur during PHP's initial startup.
             * This is like an E_ERROR, except it is generated by the core of PHP.
             */
            case E_CORE_ERROR:
                $error = '--------- System Core Crashed -------- <br /> PHP Core Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 2);
                $fatal = true;
            break;

            /** Warnings (non-fatal errors) that occur during PHP's initial startup.
             * This is like an E_WARNING, except it is generated by the core of PHP.
             */
            case E_CORE_WARNING:
                $error = 'Core Compile Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** Fatal compile-time errors.
             * This is like an E_ERROR, except it is generated by the Zend Scripting Engine.
             */
            case E_COMPILE_ERROR:
                $error = '--------- System Core Crashed -------- <br /> Zend Scripting Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 2);
                $fatal = true;
            break;

            /** Compile-time warnings (non-fatal errors).
             * This is like an E_WARNING, except it is generated by the Zend Scripting Engine.
             */
            case E_COMPILE_WARNING:
                $error = 'Zend Compile Warning : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** User-generated error message.
             * This is like an E_ERROR, except it is generated in PHP code by
             * using the PHP function trigger_error().
             */
            case E_USER_ERROR:
                $error = 'User Generated Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** User-generated warning message.
             * This is like an E_WARNING, except it is generated in PHP code by
             * using the PHP function trigger_error().
             */
            case E_USER_WARNING:
                $error = 'User Generated Warning : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** User-generated notice message.
             * This is like an E_NOTICE, except it is generated in PHP code by
             * using the PHP function trigger_error().
             */
            case E_USER_NOTICE:
                $error = 'User Generated Notice : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** Enable to have PHP suggest changes to your code which will ensure the
             * best interoperability and forward compatibility of your code.
             */
            case E_STRICT:
                $error = 'Strict Mode : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** Catchable fatal error. It indicates that a probably dangerous
             * error occured, but did not leave the Engine in an unstable state.
             * If the error is not caught by a user defined handle (see also
             * set_error_handler()), the application aborts as it was an E_ERROR.
             */
            case E_RECOVERABLE_ERROR:
                $error = 'Catchable Fatal Error : '.$errstr.' File: '.$errfile.'('.$errline.')';
                // we log as a core error....as it may dangerous
                parent::_logError($error, 2);
                $fatal = true;
            break;

            /**
			 * Run-time notices. Enable this to receive warnings about code that
             * will not work in future versions.
             */
            case 8192:
                $error = 'Deprecating Code Warning : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 1);
            break;

            /** User-generated warning message. This is like an E_DEPRECATED, except it
             * is generated in PHP code by using the PHP function trigger_error().
             */
            case 16384:
                $error = 'User Deprecating Warning : '.$errstr.' File: '.$errfile.'('.$errline.')';
                parent::_logError($error, 2);
            break;

            case U_USER_ERROR:
                $error = 'Unus User Error : '.$errstr;
            break;

            case U_NOTICE:
                $error = 'Unus Notice : '.$errstr;
                parent::_logError($error, 2);
                break;
        }

		throw new Unus_Exception($error);
    }

    public static function exceptionHandler($exception)
    {
		try {
			parent::init($exception, get_class($exception));
		} catch (Exception $e) {
			print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
    }
}
