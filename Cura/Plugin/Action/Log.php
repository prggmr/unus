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
 * @category   Unus_Plugins
 * @package    Unus
 * @version    $Rev: 1$
 * @author     Nickolas Whiting <admin@nwhiting.com>
 * @copyright  Copyright 2009 Nickolas Whiting
 */


class Unus_Cura_Plugin_Action_Log extends Unus_Observer_Abstract
{
	private static $_modelLoaded = false;
	
    public function __construct() {
        parent::__construct();
    }

	public static function registerModel()
	{
		$model = Unus_Model::getInstance();
		$model->registerTable('admin_log');
		$model->setAdmin(false);
		$model->registerField('logId', Unus_Model_Table::PRIMARY);
		$model->registerField('userId', Unus_Model_Table::INTERGER);
		$model->registerField('userIp', Unus_Model_Table::CHAR);
		$model->registerField('action', Unus_Model_Table::TEXT);
		$model->registerField('objectId', Unus_Model_Table::TEXT);
		$model->registerField('classification', Unus_Model_Table::INTERGER);

		self::$_modelLoaded = true;
	}

    /**
    * Add a administration action to the log
    */

    public function log()
    {
        // Get Event Name
        $event = Unus_Event::getEvent();
        // userId
        $userId = $this->session->setNamespace('user')->id;

        $request = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $adminDir = Unus::getPath();
		$len = strlen($adminDir);
        $param = Zend_Controller_Front::getInstance()->getRequest()->getParam('objectId');
		$model = explode('/', substr($request, $len, strlen($request) - $len));
        $objectId = $model[0];
        switch($model[1]) {
            default:
            case 'view':
            case 'visit':
                $objectId .= ':v';
                break;
            case 'save':
            case 'update':
            case 'modify':
                $objectId .= ':s';
                break;
            case 'delete':
            case 'recycle':
                $objectId .= ':d';
                break;
        }

        $objectId .= (null != $param) ? ':'.$param : null;

        $eventArray = explode('_', $event);
        unset($eventArray[0]);
        $action = null;

        switch($eventArray[count($eventArray)]) {
            case 'view':
                $eventArray[count($eventArray)] = 'viewed';
                break;
            case 'visit':
                $eventArray[count($eventArray)] = 'visited';
                break;
            case 'add':
                $eventArray[count($eventArray)] = 'add';
                break;
            case 'save':
                $eventArray[count($eventArray)] = 'saved';
                break;
            case 'update':
                $eventArray[count($eventArray)] = 'updated';
                break;
            case 'modify':
                $eventArray[count($eventArray)] = 'modified';
                break;
            case 'delete':
                $eventArray[count($eventArray)] = 'deleted';
                break;
            case 'recycle':
               $eventArray[count($eventArray)] = 'recycled';
                break;
        }

        foreach ($eventArray as $k => $v) {
            $action .= ucfirst($v).' ';
        }

        $this->db->query('INSERT INTO '.ADMIN_LOG.' (userId, userIp, action, objectId, classification, timestamp)
                                         VALUES
                                         ("'.$userId.'", "'.$_SERVER['REMOTE_ADDR'].'", "'.__(($action).'",
                                         "'.$objectId.'", "1", "'.time().'")');
    }
}
