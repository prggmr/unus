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

/**
 * @see Portfolio_Controller
 */

require_once (Unus::getCorePath().'Controller/Admin/UnusController.php');

class Unus_Message
{
	public $unus = null;
	
	public function __construct()
	{
		$this->view = Unus::registry('view');
	}
	
	public function dump($message, $location, $options = array('redirect' => true))
	{
		if ($options['redirect'] == true)
		{
			// Bug Fix
			if ($location == '/' && Unus::getPath() == '/') {
			    $location = '/';
			} else {
			    $location = WEBPATH.$location;
			}
			header('refresh: 3; url='.$location);
		}

		$this->view->message = $message;
		
		$this->view->link = $location;
		
		echo $this->view->getHtml('index/messages/index');
		
		if ($options['exit']) {
			exit;
		}
	}
	
	public function debug($message) {
		
		$this->view->message = $message;
		
		echo $this->view->getHtml('index/messages/index');
		exit;
	}
	
	public function adminDump($message, $location, $redirect = 3)
	{

	    header('refresh: '.$redirect.'; url='.ADMINPATH.$location);
	
		$this->view->message = $message;
		
		$this->view->link = $location;
		
		echo $this->view->getHtml('index/messages/index');
	}
}
