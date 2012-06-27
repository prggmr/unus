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

class Unus_Model_Registry extends Unus_Data
{
    private $_currentModel = null;

    public function construct() {
        $this->setData('registry', null);
    }

    public function addModel($name, $data)
    {
        $this->setData($name, $data);
    }

    public function getModel($name = null)
    {
        if(null == $name) {
            if (null == $this->_currentModel) {
                throw new Unus_Model_Registry_Exception('Tried to set current model but current model is not set');
            }
            return $this->getData($this->_currentModel);
        } else {
            return $this->getData($name);
        }
    }

    public function modelExists($name)
    {
        if ($this->getData($name) == false) {
            return false;
        }
        return true;
    }

    public function setModel($name) {
        if ($this->modelExists($name)) {
            $this->_currentModel = $name;
        } else {
            throw new Unus_Model_Registry_Exception('Called to set undefined model -> '.$name);
        }
    }
}
