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

class Unus_Development_Database
{
	
	/**
	 * Logs a SELECT PDOSTATMENT to FirePHP
	 */
	
	public static function addBuild(PDOStatement $result, $where)
	{
		$firephp = self::_buildTrace();
		
		if (null != $where) {
			foreach ($where as $k => $v) {
				if (!array_key_exists('noval', $v) || $v['noval'] == false) {
					$result->bindParam(':'.$k, $v['value']);
				}
			}
		}
		
		$result->execute();
		
		if ($result->rowCount() == 0) {
			$array = array();
			$array[] = array('Query returned no results');
			$firephp->error('No Results Found For Query : '.$result->queryString.'');
		} else {
			
			// Build SQL Result FireBug Table
			$cols = array();
			
			
			foreach ($result as $k => $v) {
				$a = 0;
				foreach ($v as $c => $d) {
					$a++;
					if ($a % 2) {
						$cols[] = $c;	
					}
				}
				break;
			}
			
			
			$result->execute();
			
			$table[] = $cols;

			//$result = Unus::registry('db')->query($result->queryString, PDO::FETCH_ASSOC);
			
			foreach ($result as $k => $v) {
				$data = array();
				$c = 0;
				foreach ($v as $a => $b) {
					/*
					 * Hide unserialize trying to convert real text into an offset byte code
					 */
					$c++;
					if ($c % 2) {
						//if (null != $c && is_array(@unserialize($b))) {
						//	Unus_Development_FirePHP::log(Unus::dump($b));
						//	//$b = unserialize($b);
						//}
						$data[] = $b;
					}
				}
				$table[] = $data;
			}

			$firephp->table('('.$result->rowCount().' Results) - '.$result->queryString, $table);
		}
	}
	
	private static function _buildTrace()
	{
		$firephp = Unus_Development_FirePhp::getInstance(true);

		$table1 = array();

		$debug = debug_backtrace();
        $traceRoute = array_reverse($debug);

		/*
		 * Pop off the last 2 development calls
		 */
		array_pop($traceRoute);
		array_pop($traceRoute);

		$trace = array(array('#', 'File (Line)', 'Call', 'Arguments'));

        foreach ($traceRoute as $k => $v) {
			$v['file'] = explode('/', $v['file']);
			$count = count($v['file']) - 1;
            $traceNumber = $k + 1;
			/*
			 * Hide Notices from no index on the class and type
			 */
			$trace[] = array($traceNumber, $v['file'][$count].'('.$v['line'].')', @$v['class'].@$v['type'].$v['function'], $v['args']);
		}
		
		//$firephp->table('SQL Traceroute ('.number_format($traceNumber).' Traces)', $trace);
		
		return $firephp;
	}
	
	public static function addResult(PDOStatement $result)
	{
		$firephp = self::_buildTrace();
		
		$queryString = $result->queryString;
		
		$rowCount = $result->rowCount();
		$explode = explode(' ', $queryString, 2);
		$function = strtolower($explode[0]);
		if ($rowCount == 0) {
			$firephp->error('No Rows Affected for '.$queryString.'');	
		} else {
			$rowCount = number_format($rowCount);
			switch ($function) {
				case 'insert':
					$firephp->info($rowCount.' row(s) inserted : '.$queryString);
					break;
				case 'update':
					$firephp->info($rowCount.' row(s) updated : '.$queryString);
					break;
				case 'delete':
					$firephp->info($rowCount.' row(s) deleted : '.$queryString);
					break;
			}
		}
	}
}
