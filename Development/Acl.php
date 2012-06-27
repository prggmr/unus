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

class Unus_Development_Acl
{
	public static $firephp = true;

	/**
	 * Logs a resource acces check
	 *
	 * @param  string  resource  Name of resource
	 *
	 * @return
	 */

	public static function log($resource, $return)
	{
		if (self::$firephp) {

			$firephp = Unus_Development_FirePhp::getInstance(true);

			Unus::registry('acl')->isAllowed_trace($resource);

			$trace = Unus::registry('acl')->getTraceRoute($resource);

			$trace = explode('<br />', $trace);

			$table = array(array('#', 'Action'));

			foreach ($trace as $k => $v) {

				preg_match_all('/{([0-9]+)}/i', $v, $number);
				preg_match_all('/\}(.*)/i', $v, $string);
				//if (preg_match('/----/i', $string[1][0])) {} else {
					$table[] = @array($number[1][0], $string[1][0]);
				//}
			}

			$return = ($return) ? 'Allowed' : 'Denied';

			$firephp->table('ACL TraceRoute : '.$resource.' < '.$return .' >', $table);
		}
	}
}
