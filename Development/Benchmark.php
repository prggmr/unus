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

class Unus_Development_Benchmark
{
    private static $_instance = null;

    /**
     * String
     */

    protected static $_data = null;

    /**
     * Enforce singleton instance
     *
     * @param  type  ID  Current unus process we are monitoring
     *
     * @return this
     */

    private function __construct()
    {
        self::$_data = new Unus_Data();

        return $this;
    }

    /**
     * Returns current memory usage in bytes.
     * Default is to use PHP's memory_get_usuage.
     * If on *NIX Systems and memory_get_usage unavliable we will run a
     * shell_exec. This does not work on Windows Systems ... and may not produce accurate results
     *
     * @return int
     */
    public static function getMemoryUsage()
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage();
        } else {
            return 0;
        }
    }

    /**
     * Attempts to get the size in bytes of a variable
     *
     * This function is a somewhat experimental and to be taken with a grain of salt
     * Uses var_dump() to get variable data and then strips all human
     * accessiable information then uses strlen.
     *
     * This is merely the text byte size representation of variables
     *
     *
     * @param  mixed  var  Variable to retrieve bytes size
     *
     * @return  int
     */
    public static function getVariableSize($var)
    {
        ob_start();
        var_dump($var);
        $var = ob_get_contents();
        ob_clean();

        /*
         * Strip a string down to the bear minimum
         *
         * Although this would be compiled into bytecode this still alows a rough estimate
         */

        /*
         * Remove Object, String, Array definitions
         */
        $var = preg_replace('/object\([a-z_]+\)#[0-9]+/i', '', $var);
        $var = preg_replace('/string\([0-9]+\)/i', '', $var);
        $var = preg_replace('/array\([0-9]+\)/i', '', $var);
        /*
         * Remove Operators
         */
        $var = str_replace('=>', '', $var);
        $var = str_replace('}', '', $var);
        $var = str_replace('{', '', $var);
        $var = str_replace('[', '', $var);
        $var = str_replace(']', '', $var);
        /*
         * Remove New line characters
         */
        $var = str_replace('\n', '', $var);
        /*
         * Remove Data Definitions
         */
        $var = preg_replace('/"_data:([a-z]+)"/i', '', $var);
        /*
         * Remove Quotes
         */
        $var = str_replace('"', '', $var);
        /*
         * Replace NULL data with a 0 as it is counted as 1byte intead of 4
         */
        $var = str_replace('NULL', '0', $var);
        //$var = preg_replace('/([0-9]+)/i)', '', $var);
        /*
         * Remove Public Private Protected access hints
         */
        $var = preg_replace('/:([protected|public|private]+)/i', '1', $var);
        /*
         * Remove Boolean definitions
         */
        $var = preg_replace('/bool\([true|false]+\)/i', '1', $var);
        /*
         * Remove Interger definitions
         */
        $var = preg_replace('/int\(([0-9]+)\)/i', '$1', $var);
        return (int) mb_strlen($var);
    }


    /**
     * Returns current CPU usage for *NIX System in bytes
     *
     * @todo Find workaround for WINDOWS system
     *
     * @return int
     */
    public static function getCpuUsage()
    {
        // This is a fairly inaccurate represntation but better than nothing
        $ex = shell_exec('ps up 1');
        $ex = explode(' ', $ex);
        return (int) $ex[44];
    }

    /**
     * Returns microtime in format for time parsing
     *
     * @return int
     */
    public static function getMicrotime()
    {
        $time = explode(" ",microtime());
        return $time[0] + $time[1];
    }


    /**
     * Starts a system bechnmark
     * Logs the memory, starttime and CPU Usage for System ID
     *
     * @param  string  Id  Name of Unus process to activate system monitor
     *
     * @return this
     */

    public static function start($id)
    {
		if (Unus_Development::getDevMode()) {
			if (self::$_data == null) {
				self::$_data = new Unus_Data();
			}

			$memory = self::getMemoryUsage();
			//$cpu = self::getCpuUsage();
			$time = self::getMicrotime();

			$data = self::$_data->getData($id);
			$data['start_time'] = $time;
			//$data['start_cpu'] = $cpu;
			$data['start_memory'] = $memory;

			self::$_data->setData($id, $data);
		}
    }

    /**
     * Adds a transaction benchmark to a current set benchmark .. only if it has not been dumped
     *
     * @param  string  Id  Name of Unus process to add transaction
     *
     * @return this
     */
    public static function start_add($id)
    {
		if (Unus_Development::getDevMode()) {

			if (null == self::$_data || null == self::$_data->getData($id)) {
				self::start($id);
			}

			$memory = self::getMemoryUsage();
			//$cpu = self::getCpuUsage();
			$time = self::getMicrotime();

			$data = self::$_data->getData($id);
			$data['start_add_time'] = $time;
		   // $data['start_add_cpu'] = $cpu;
			$data['start_add_memory'] = $memory;

			self::$_data->setData($id, $data);
		}
    }

    /**
     * Stops a transaction benchmark addition .. only if it has not been dumped
     *
     * @param  string  Id  Name of Unus process to add transaction
     *
     * @return this
     */
    public static function stop_add($id)
    {
		if (Unus_Development::getDevMode()) {
			$memory = self::getMemoryUsage();
			//$cpu = self::getCpuUsage();
			$time = self::getMicrotime();

			$data = self::$_data->getData($id);
			// execution time is now added time minus start ... this will add the result :)
			$data['start_time'] = $data['start_time'] - ($time - $data['start_add_time']);
		   // $data['start_cpu'] = $data['start_cpu'] + $cpu + $data['start_add_cpu'];
			$data['start_memory'] = $data['start_memory'] + $memory + $data['start_add_memory'];

			self::$_data->setData($id, $data);
		}
    }

    /**
     * Ends a system bechnmark
     * Logs the memory, starttime and CPU Usage for System ID
     * Either send to firePHP or return
     *
     * @param  string  Id       Name of Unus process to activate system monitor
     * @param  string  action   Action to do once finished firephp -> Prints to FirePHP in table, return -> returns Array of information
     *
     * @return this
     */

    public static function stop($id, $action = 'firephp')
    {
		if (Unus_Development::getDevMode()) {
			$memory = self::getMemoryUsage();
			//$cpu = self::getCpuUsage();
			$time = self::getMicrotime();

			$data = self::$_data->getData($id);
			$data['end_time'] = $time;
		   /// $data['end_cpu'] = $cpu;
			$data['end_memory'] = $memory;

			// turn this data we have into some real numbers..

			// MEMORY
			$data['total_memory'] = ($data['start_memory'] > $data['end_memory']) ? $data['start_memory'] - $data['end_memory'] : $data['end_memory'] - $data['start_memory'];
		   // $data['total_cpu'] = ($data['start_cpu'] > $data['end_cpu']) ? $data['start_cpu'] - $data['end_cpu'] : $data['end_cpu'] - $data['start_cpu'];
			$data['execution_time'] = $data['end_time'] - $data['start_time'];

			self::$_data->setData($id, $data);

			switch($action) {
				case 'firephp':
					default:
					// print the information to firePHP
					$firephp = Unus_Development_FirePhp::getInstance(true);
					$table   = array();
					$table[] = array('Benchmark','Results');
					$table[] = array('Execution Time', $data['execution_time']);
					$table[] = array('Memory Usage', Unus_Encode_Bytes::encode($data['total_memory'], 3));
					$table[] = array('Start Memory Usage', Unus_Encode_Bytes::encode($data['start_memory'], 3));
					$table[] = array('End Memory Usage', Unus_Encode_Bytes::encode($data['end_memory'], 3));
					$table[] = array('Peak Memory Usage', Unus_Encode_Bytes::encode(memory_get_peak_usage(), 3));
                    if ($id == 'unus core') {
                        $table[] = array('Registry Size', Unus_Encode_Bytes::encode(self::getVariableSize(Unus::getRegistry()), 3));
                    }
					$firephp->table('Benchmark Results for '.ucfirst($id).'', $table);
					$return = true;
					break;
				case 'return':
					$return = $data;
					break;
			}

			return $return;
		}
    }
}
