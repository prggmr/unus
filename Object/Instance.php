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

/**
 * Is the same class as Unus_Object only it contains a private __construct
 * for extending children
 *
 * TODO: Find workaround to avoid having two existances of the same class
 */

class Unus_Object_Instance
{
    /**
      *  Unus Datastore Array
      */
    protected $_data = array();

    private function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = array();
            $this->_data = $args[0];
        }
    }

    public function addData(array $arg)
    {
        foreach ($arg as $k => $v) {
            $this->setData($k, $v);
        }

        return $this;
    }

    public function setData($key, $value = null, $override = true, $append = false)
    {
        if (is_array($key)) {
            $this->_data = $key;
        } else {
            $this->_data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get data from _data
     *
     * '/' seperator loads array children
     * a/b/c/d -> [a][b][c][d]
     *
     */

    public function getData($key = null)
    {
        // non-specfic entire data
        if ($key == null) {
            return $this->_data;
        }

        $default = null;

        if (stripos($key, '/')) {
            $keyArray = explode('/', $key);
            $count = count($keyArray) - 1;
            $last = $keyArray[$count];
            $data = $this->_data;
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
					} elseif ($data[$k] instanceof Unus_Object) {
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
                return null;
            }
        }
    }

    public function unsetData($key)
    {
        if(isset($this->_data[$key]))
        {
            if (is_object($this->_data[$key]) && method_exists($this->_data[$key], '__destruct'))
            {
               $this->_data[$key]->__destruct();
            }
            unset($this->_data[$key]);
        } else {
            return false;
        }
    }
}
