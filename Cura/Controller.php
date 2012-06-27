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


class Cura_Controller extends Unus_Controller
{
	public function init()
	{
		// Before we do anything lets see if they are logged in and have admin rights
		if ((!$this->user->isLoggedIn() || !$this->acl->isAllowed('admin'))) {
			if ($this->request->getAction() != 'lostPassword' && $this->request->getAction() != 'login' && $this->request->getAction() != 'logout') {
				$this->redirect(Unus::getAdminPath().'login');
			}
		}

		// Get Requested URL and Get the model from it
		$adminDir = Unus::adminPath();
		$len = strlen($adminDir);
		$request = $this->request->getRequestedUrl();
		$this->modelArray = explode('/', substr($request, $len, strlen($request) - $len));

		foreach (Unus::registry('models')->getData('registry')->getData() as $k => $v) {
			if ($k == $this->modelArray[0]) {
				$this->loadModel = $k;
			}
		}

		$this->view->addScriptPath(Unus::getLibraryPath().'Unus/Cura/skin');
		$this->view->headerTitle(__('Administration'), 'SET');

		if (!$this->acl->isAllowed('admin_'.$this->loadModel.'_main')) {
			$this->session->setErrorMessage(__('Your account does not meet the requirements to access the requested pages'));
			$this->redirect(Unus::getAdminPath());
		}
	}

	/**
	 * Administration Index
	 */

	public function index()
	{
		// Get Requested URL and Get the model from it
		#Unus::dispatchEvent('admin_'.$this->loadModel.'_dashboard_visit');
		echo $this->view->getHtml('dashboard/index');
	}

	/**
	 * Administration Login
	 */
	public function login()
	{
		$this->view->headerTitle(__('Login'));
		
		if ($this->user->isLoggedIn()) {
			if (!$this->acl->isAllowed('admin')) {
				// Logout the user with permissions that they are not allowed to access the admin
				$this->session->sessionDestroy('user');
			} else {
				$this->redirect(Unus::getAdminPath());
			}
		}	
		
		if (!$this->session->sessionExists('admin_login_lock')) {
			$this->session->startSession('admin_login_lock');
		}

		if ($this->session->sessionExists('admin_login_lockout')) {
			$this->lock_out = true;
			$this->session->sessionDestroy('admin_login_lock');
			echo $this->view->getHtml('login');
		}
	
		#$this->form = new Unus_Form();
		
		Unus::dump(new Unus_Form());
		
		if (!$this->request->isPost() || !$this->form->isValid()) {
			echo $this->view->getHtml('login');
		} else {

			$this->db->_use('user');

			$result = $this->db->where(array('username' => $_POST['username'],
											'password' => Unus_Helper_Hash_Password::hash($_POST['password'])
											)
									 )->select('userId, username');

			$result = $result->fetch(PDO::FETCH_OBJ);

			if (!$result) {

				if (null == $this->session->failed_attempts) {
					$this->session->failed_attempts = 1;
				} else {
					if ($this->session->failed_attempts >= 5) {
						$this->session->startSession('admin_login_lockout',
													 array('lockout' => true),
													 array('lifetime' => (60 * 15) // 15 minutes 60 seconds * 15
														   )
													 );
					} else {
						$this->session->failed_attempts++;
					}
				}
				$this->session->setErrorMessage(__('You have specified an invalid username or password please try again <br /><br /> You have used <strong>'.$this->session->failed_attempts.'</strong> of <strong>5</strong> allowed failure attempts <br /><br /> After 5 attempts you will be locked out for 15 minutes'));
				$this->redirect(Unus::getAdminPath().'login');
			} else {
				$this->session->startSession('user', array(), array('lifetime' => 86400));
				$this->session->id = $result->userId;
				$this->session->username = $result->username;
				$this->redirect(Unus::getAdminPath());
			}
		}
	}

    /**
     * Lost Password Request Action
     */

    public function lostPassword()
    {
		$this->view->headerTitle(__('Forgot Password'));
		
		$this->form = new Unus_Form('password_request', '');
		$this->form->setjQueryValidate(true);
		$username = $this->form->addElement(new Unus_Form_Elements_Unus_Username());
		$email = $this->form->addElement(new Unus_Form_Elements_Unus_Email());
		$submit = $this->form->addElement('submit', 'submit');
		$submit->setClass('key')->setValue(__('Reset'))->setLabel(false);
		$button = $this->form->addElement('button', 'back_login');
		$button->setClass('help')->setValue(__('Back to Login'))->setLabel(false);

		$this->form->setDecorator(array(
		   'form_start' => false,
		   'form_end' => false,
		   'row_start' => false,
		   'row_end' => false,
		   'label_start' => false,
		   'label_end' => false,
		   'element_start' => false,
		   'element_end' => false,
		   'element_submit_label_start' => false,
		   'element_submit_label_end' => false,
		   'element_submit_start' => '<div class="clear"></div>',
		   'element_submit_end' => ' ',
		));

        if (!$this->request->isPost()) {

			echo $this->view->getHtml('lost_password');

        } else {
            $this->db->_use('user');

            $find = $this->db->order(array('username'))->find(array('username' => $_POST['username'], 'email' => $_POST['email']));
            $fetch = $find->fetch(PDO::FETCH_OBJ);

            if (!$fetch) {
                $this->session->setErrorMessage(__('The account details you have entered are incorrect. <br /><strong>Please try again</strong>'));
              //  $this->redirect(Unus::getAdminPath().'lost-password');
            } else {
                // Email user a key
                $key = uniqid();
                $this->session->startSession('passwordReset', array(), array('lifetime' => (60 * 30)));
                $this->session->setMessage(__('You have been sent an email with instructions to reset your account password'));
                //$this->redirect(Unus::getAdminPath().'lost-password');

                echo 'Under Construction';

                /*try {
                    mail($fetch->email, 'TEST', 'This is a test') or die('fFUCK');
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }*/
            }
        }
    }

    /**
     * Administration Logout Action
     */

	public function logout()
	{
		if ($this->user->isLoggedIn() && $this->acl->isAllowed('admin')) {
			$this->session->sessionDestroy('user');
		} else {
			$this->redirect(Unus::getPath());
		}
		
		// redirect them to home page
		
		$this->redirect(Unus::getAdminPath());
	}

	/**
	 * Update Admin Sticky Note
	 */

	public function stickynoteAction()
	{
		Unus::dispatchEvent('admin_sticky_note_save');
		$note = Unus_Helper_Text_Html_Parse::cure($_GET['note']);
		$this->db->query('UPDATE '.SETTINGS.' SET value = "'.$note.'" WHERE name = "admin_sticky"');
		exit;
	}
}
?>
