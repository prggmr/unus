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
 * @version    $Rev: 2$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */

class Unus_Data
{
    /**
     *  Object registry data
     */
    
    protected $_data = array();

    /**
     * Sets data in the current held data
     *
     * @param  string  key    Key for data
     * @param  mixed   value  Value of data
     * @param  boolean merge  Intelligently merge the data together if exists in the data held otherwise overwrite
     *
     * @return this
     */

    public function setData($key, $value = null, $merge = false)
    {
        if (array_key_exists($key, $this->_data) && $merge) {
            $data = $this->_data[$key];
            switch($value) {
                case is_string($value):
                    switch ($data) {
                        case is_string($data):
                            $data = $data.$value;
                            break;
                        case is_array($data):
                            if (array_key_exists($key, $data)) {
                                if (is_array($value)) {
                                    $data[$key] = array_merge($data[$key], $value);
                                } else {
                                    $data[$key][] = $array;
                                }
                            } else {
                                $data[] = $value;
                            }
                            break;
                        case is_object($data):
                            if ($data instanceof Unus_Data) {
                                $data = $data->setData($key, $value);
                            } else {
                                throw new Unus_Data_Exception('Attempted to merge string data to key '.$key.' and found object '.get_class($data).'');
                            }
                            break;
                        default:
                        case is_null($data):
                        case is_int($data):
                        case is_float($data):
                        case is_bool($data):
                        /**
                         * @todo  We need to figure something out to do with string and booleans, floats and intergers ... other than overwriting
                         */
                            $data = $value;
                            break;
                    }
                    break;
                case is_float($value):
                case is_int($value):
                    switch($data) {
                        case is_float($data):
                        case is_int($data):
                            $data = $data + $value;
                            break;
                        case is_object($data):
                            if ($data instanceof Unus_Data) {
                                $data = $data->setData($key, $value);
                            } else {
                                throw new Unus_Data_Exception('Attempted to merge int/float data to key '.$key.' and found object '.get_class($data).'');
                            }
                            break;
                        case is_array($data):
                            if (array_key_exists($key, $data)) {
                                $array      = array($data[$key], $value);
                                $data[$key] = $array;
                            } else {
                                $data[] = $value;
                            }
                            break;
                        case is_null($data):
                        case is_bool($data):
                        case is_string($data):
                        default:
                            /**
                             * @todo  We need to figure out what to do with this ... other than overwriting
                             */
                            $data = $value;
                            break;                        
                    }
                    break;
                case is_object($value):
                    if ($data instanceof Unus_Data) {
                        $data = $data->setData($key, $value);
                    } else {
                        throw new Unus_Data_Exception('Attempted to merge object data to key '.$key.' and found object '.get_class($data).'');
                    }
                    break;
                case is_array($value):
                    switch($data) {
                        case is_null($data):
                            $data = $value;
                            break;
                        case is_array($data):
                            $data = array_merge($data, $value);
                            break;
                        case is_object($data):
                            if ($data instanceof Unus_Data) {
                                $data = $data->setData($key, $value);
                            } else {
                                throw new Unus_Data_Exception('Attempted to merge object data to key '.$key.' and found object '.get_class($data).'');
                            }
                            break;
                        default:
                        case is_string($data):
                        case is_bool($data):
                        case is_int($data):
                        case is_float($data):
                            $data = array($data);
                            $data = array_merge($data, $value);
                            break;
                    }
                    break;
                default:
                case is_bool($value):
                case is_null($value):
                    $data = $value;
                    break;
            }
            
            $this->_data[$key] = $data;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Loads data from the _data array
     * 
     * Direct child data can be loaded in the format
     * parent/child/grandchild -> parent[child][grandchild]
     *
     * getData will transverse threw arrays and or instances of Unus_Data
     * to locate data
     *
     * getData can descend into any multi-level array as long as the data
     * has been properly set
     *
     * @param  string  key      Key and or key string for data | Leave blank to return all data held
     * @param  mixed   default  Default return if data is not found
     *
     * @return  mixed
     */

    public function getData($key = null, $default = null)
    {
        // non-specfic entire data
        if ($key == null) {
            return $this->_data;
        }

        if (stripos($key, '/')) {
            $keyArray = explode('/', $key);
            $count    = count($keyArray) - 1;
            $last     = $keyArray[$count];
            $data     = $this->_data;
            foreach ($keyArray as $a => $k) {
                if ($k === '') {
                    return $default;
                }

                if (array_key_exists($k, $data)) {
					if ($k == $last) {
						return $data[$k];
					}

					if (is_array($data[$k])) {
						if (!isset($data[$k])) {
							return $default;
						}
						$data = $data[$k];
					} elseif ($data[$k] instanceof Unus_Data) {
						$data = $data[$k]->getData();
					}
				} else {
					return $default;
				}
            }
        } else {
            if (array_key_exists($key, $this->_data)) {
                return $this->_data[$key];
            } else {
                return $default;
            }
        }
    }
    
    /**
     * Mirror shorthand of getData()
     *
     * @see getData();
     *
     * @return  mixed
     **/
    
    
    public function get($key = null, $default = null)
    {
        return $this->getData($key, $default);
    }
}
