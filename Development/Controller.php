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

class Unus_Development_Controller extends Unus_Controller
{
	/**
	 * Handle 404 Development Page errors
 	 */

	public function error404()
	{
		$this->view->addScriptPath(Unus::getLibraryPath().'Unus/Development/views/');
		$this->method = strtoupper($this->request->getRequestMethod());
		$this->urls = Unus_Router::getInstance()->getRoutes();
		$this->requestURL = str_replace(Unus::getPath(), '', $this->request->getRequestedUri());
		echo $this->view->getHtml('404');
		exit();
	}

	/**
	 * Handles 500 Server Errors
	 */
	public function error500(Unus_Exception $exception, $trace)
	{
		// DUMP ALL BUFFERS ...
		ob_end_clean();
		$this->view->addScriptPath(Unus::getLibraryPath().'Unus/Development/views/');
		$this->method = strtoupper($this->request->getRequestMethod());
		$this->urls = Unus_Router::getInstance()->getRoutes();
		$this->requestURL = str_replace(Unus::getPath(), '', $this->request->getRequestedUri());
		$this->exception = $exception;
		$trace = $exception->getTrace();
		$this->line = $exception->getLine();
		$this->file = $exception->getFile();
		$file = Unus_Helper_File_Read_Lines::parse($this->file, $this->line);
		$this->lineError = '<ol>';
		foreach ($file as $k => $v) {
			$this->lineError .= '<li value="'.$k.'">';
			if ($k == $this->line) {
				$this->lineError .= '<strong>';
			}
			$this->lineError .= $v;
			if ($k == $this->line) {
				$this->lineError .= '</strong>';
			}
			$this->lineError .= '</li>';
		}
		$this->lineError .= '</ol>';
		$this->trace = $trace;
		echo $this->view->getHtml('500');
		exit();
	}
}