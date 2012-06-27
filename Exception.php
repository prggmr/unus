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

class Unus_Exception extends Exception
{
    public static $exception = null;

    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
		
		try {
			self::init($this, get_class($this));
		} catch (Exception $e) {
			print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
    }

    public static function init($exception, $class)
    {
         // Build the Message
        self::$exception = 'Exception : '.$class;

        if (null != $exception->getMessage()) {
            self::$exception .= ' <br /> Message : '.$exception->getMessage().'';
        }

        self::$exception .= ' <br /> File : '.$exception->getFile().' <br /> Line : '.$exception->getLine().'';

        // Build StackTrace
        $trace = null;
        $traceRoute = array_reverse($exception->getTrace());
		$traceNumber = 0;


        foreach ($traceRoute as $k => $v) {
            $traceNumber = $k + 1;
			$file = (isset($v['file'])) ? $v['file'] : 'Unknown File';
			$line = (isset($v['line'])) ? $v['line'] : 'Unknown Line';
			$class = (isset($v['class'])) ? $v['class'] : '';
			$type = (isset($v['type'])) ? $v['type'] : '';
			$function = (isset($v['function'])) ? $v['function'] : 'Unknown Function or Method';
			$args = (isset($v['args'])) ? $v['args'] : '';
            @$trace .= '{#'.$traceNumber.'} '.$file.'('.$line.'): '.$class.$type.$function.'('.self::parseArgs($args).') <br />';
        }

        self::$exception .= '<br />Traceroute '.$traceNumber.' Trace(s)<br />-----------------<br />';

        self::$exception .= $trace;

        // log the error
        self::_logError(self::$exception, 3);

        if (Unus_Development::getDevMode()) {

            // Log Information to firePHP if enabled
            if (Unus_Development::useFirePHP()) {
                $firePHP = Unus_Development_FirePhp::getInstance();
                $firePHP->error($exception);
            }

			$controller = new Unus_Development_Controller();
			$controller->error500($exception, self::$exception);

            Unus_Dispatch_Header::triggerCode('500');

            return true;

        } else {

			// dispatch let the event handlers take care of business
            Unus_Dispatch_Header::triggerCode('500');
            exit;
        }
    }

    public static function _logError($error, $level)
    {
        if (Unus::logErrors() != 0 && ($level == Unus::logErrors() || Unus::logErrors() == 4) && is_writeable(Unus::errorLogFile())) {
            $contents = file_get_contents(Unus::errorLogFile());
            $log_message = str_replace('<br />', '
', $error);
            $log_message .= '
---------------------------
Recorded : '.date('m/d/y h:ia', time()).'


';
            $contents .= $log_message;
            @file_put_contents(Unus::errorLogFile(), $contents);
        }
    }

	public static function parseArgs($args)
	{
		if (count($args) == 0) {
			return '';
		}

		$return = null;

		foreach ($args as $k => $v) {
			if ($k == 0) {
				$return .= self::arg_encode($v);
			} else {
				$return .= ', '.self::arg_encode($v);
			}
		}

		return $return;
	}

	public static function arg_encode($arg)
	{
		$arg = json_encode($arg);

		$arg = preg_replace('(\{(.*)\})', 'array(\1)', $arg);
		$arg = preg_replace('(\"([\w+]+)\":)', '\1 => ', $arg);
		$arg = str_replace('\/', '/', $arg);
		$arg = str_replace('[]', 'array()', $arg);

		return $arg;
	}
}
