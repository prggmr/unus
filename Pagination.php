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

class Unus_Pagination
{
	/**
	 * Total Number of items that are being sorted by the SQL Query
	 *
	 */

	protected $_totalItems = null;

	/**
	 * Url Link that will be used for pagination
	 *
	 */

	protected $_pagiUrl = null;

	/**
	 * Total Number of items per page
	 *
	 */

	protected $_perPage = 10;

	/**
	 * Total Number of lines to display half on each side of current page
	 *
	 */

	protected $_range = 5;

	/**
	 * Use mod_rewrite style links
	 *
	 */

	protected $_rewrite = false;

	/**
	 * Use page Jumping links ( 3, 7, 13, 25 )
	 *
	 */

	protected $_pageJump = true;

	/**
	 * Total Number of jumps to perform per side
	 *
	 */

	protected $_jumpCount = 5;

	/**
	 * Calculation number for a jump link ( currentPageLink * number = jumpLink)
	 *
	 * Lower numbers are recommended for foward jumping Anything over 1.25 will cause the links to jump
	 * at a very high rate
	 *
	 */

	protected $_jumpCalcFoward = 1.15;

	/**
	 * Calculation number for a jump link ( currentPageLink * number = jumpLink)
	 *
	 * Lower numbers are not recommended for backwards jumping Anything under 1.25 will cause the links allmost to completely not jump
	 *
	 */

	protected $_jumpCalcBackward = 1.25;

	/**
	 * GET Variable that the current page number is stored in
	 *
	 */

	protected $_pageIdentifer = 'p';

	/**
	 * Automatically extract the page number based on the pageIdenitifer
	 *
	 */

	protected $_useAutoIdentifier = true;

	/**
	 * Total Number of pages based on totalItems / perPage
	 *
	 */

	public $totalPages = null;

	/**
	* File to be used for styleing the pagination
	*/

	public $styleFile = null;

	/**
	 * Styleization Array
	 *
	 * @static
	 */

	public static $styleArray = array(
									  'start' 		  => '',
									  'end'			  => '',
									  'first_start'   => '',
									  'first_end'     => '',
									  'last_start'    => '',
									  'last_end'      => '',
									  'current_start' => '',
									  'current_end'   => '',
									  'link_start'    => '',
									  'link_end'      => '',
									  'next_start' 	  => '',
									  'next_end'      => '',
									  'prev_start'    => '',
									  'prev_end'      => ''
									  );
	
	/**
	 * Method of finding the current page number avaliable
	 * GET | POST | REQUEST
	 */
	
	public $identifyMethod = 'GET';

	/**
	 * Current Page
	 *
	 */

	public $currentPage = null;

	/**
	 * Option to show the link to the first page results
	 *
	 */

	protected $_displayLinkFirst = true;

	/**
	 * Option to show link for the previous page results
	 *
	 */

	protected $_displayLinkPrev = true;

	/**
	 * Option to show the link to the last page results
	 *
	 */

	protected $_displayLinkLast = true;

	/**
	 * Option to show the link to the next page results
	 *
	 */

	protected $_displayLinkNext = true;

	/**
	 * Text to be displayed for page 1 link
	 *
	 */

	public $linkPageOne = '[&laquo;]';

	/**
	 * Text to be displayed for prev page link
	 *
	 */

	public $linkPagePrev = '&laquo;';

	/**
	 * Text to be displayed for Last page link
	 *
	 */

	public $linkPageLast = '[&raquo;]';

	/**
	 * Text to be displayed for next page link
	 *
	 */

	public $linkPageNext = '&raquo;';

	/**
	 * Flag to load preset style assigned in template file
	 *
	 */

	private $customStyle = false;

	/**
	 * Constructor
	 *
	 * Set the total number of items and the pageURL, also check for pageIdentifier and try and get current page number
	 * if not set
	 *
	 * @param  int     $totalItems
	 * @param  string  $pagiUrl
	 */

	public function __construct($totalItems, $pagiUrl)
	{
		$this->setTotalItems($totalItems);

		$this->setPageUrl($pagiUrl);
	}

	/**
	 * Sets option to display 1st page link
	 *
	 * @param  bool  $flag
	 */

	public function setDisplayLinkFirst($flag)
	{
		$this->_displayLinkFirst = $flag;
	}

	/**
	 * Sets option to display Prev Page link
	 *
	 * @param  string  $str
	 */

	public function setDisplayLinkPrev($str)
	{
		$this->_displayLinkPrev = $str;
	}
	
	/**
	 * Sets the identifing method for finding current page
	 *
	 * @param  string  str  Method to identify current page may be (GET|POST|REQUEST)
	 *
	 * @return
	 */
	
	public function setIdentifyMethod($str)
	{
		$this->identifyMethod = $str;
		
		return $this;
	}
	
	/**
	 * Returns the auto identifier method
	 *
	 * @return string
	 */
	
	public function getIdentifyMethod()
	{
		if ($this->identifyMethod == 'POST' || 'GET' || 'REQUEST') {
			return $this->identifyMethod;
		} else {
			return 'GET';
		}
	}

	/**
	 * Sets option to display Last page link
	 *
	 * @param  bool  $flag
	 */

	public function setDisplayLinkLast($flag)
	{
		$this->_displayLinkLast = $flag;
	}

	/**
	 * Sets option to display Next page link
	 *
	 * @param  string  $str
	 */

	public function setDisplayLinkNext($str)
	{
		$this->_displayLinkNext = $flag;
	}

	/**
	 * Sets text that will be displayed for 1st page link
	 *
	 * @param  string  $str
	 */

	public function setLinkFirst($str)
	{
		$this->linkPageOne = $str;
	}

	/**
	 * Sets text that will be displayed for next page link
	 *
	 * @param  string  $str
	 */

	public function setLinkNext($str)
	{
		$this->linkPageNext = $str;
	}

	/**
	 * Sets text that will be displayed for last page link
	 *
	 * @param  string  $str
	 */

	public function setLinkLast($str)
	{
		$this->linkPageLast = $str;
	}

	/**
	 * Sets text that will be displayed for prev page link
	 *
	 * @param  string  $str
	 */

	public function setLinkPrev($str)
	{
		$this->linkPagePrev = $str;
	}

	/**
	 * Sets the pageIdentifier
	 *
	 * @param  string  $str
	 */

	public function setIdentifier($str)
	{
		$this->_pageIdentifer = $str;
	}

	/**
	 * Sets the current Page
	 *
	 * @param  int  $int
	 */

	public function setCurrentPage($int)
	{
		$this->currentPage = $int;
	}

	/**
	 * Register the AutoIdentifier on|off
	 *
	 * @param  bool  $flag
	 */

	public function setAutoIdentifier($flag)
	{
		$this->_useAutoIdentifier = $flag;
	}

	/**
	 * Sets the style that will be used to parse out the pagination
	 *
	 * @param  array  $style
	 */

	public function setStyleArray($style)
	{
		self::$styleArray = $style;
		$this->customStyle = true;
	}


	/**
	 * Checks and returns if the page number
	 *
	 * @return int
	 */

	public function getCurrentPage()
	{
		$return = null;
		// Check if auto identify is disabled if so we dont worry if the page count is correct
		if ( $this->_useAutoIdentifier == false ) {
			$return = (int) $this->currentPage;
		}
		else {
			if ($this->getIdentifyMethod() == 'REQUEST') {
				// Framework Request....
				//$request = Zend_Controller_Front::getInstance()->getRequest();
	
				if ($request->getParam($this->getPageIdentifier())) {
					$this->currentPage = $request->getParam($this->getPageIdentifier());
					$return = (int) $this->currentPage;
				}
			} elseif ($this->getIdentifyMethod() == 'POST') {
				if ($_POST[$this->getPageIdentifier()]) {
					$return = (int) $_POST[$this->getPageIdentifier()];
				}
			} elseif ($this->getIdentifyMethod() == 'GET') {
				if ($_GET[$this->getPageIdentifier()]) {
					$return = (int) $_GET[$this->getPageIdentifier()];
				}
			}
		}
		
		if ($return == 0) {
			$return = 1;
		}
		
		return $return;
	}

	/**
	 * Set the page range
	 *
	 * @param  int  $count
	 */

	public function setRange($count)
	{
		$this->_range = $count;
	}

	/**
	 * Sets the number of jump Links
	 *
	 * @param  int  $count
	 */

	public function setPageJumpCount($count)
	{
		$this->_jumpCount = $count;
	}

	/**
	 * Set page jumping on|off
	 *
	 * @param  bool  $flag
	 */

	public function setPageJump($flag)
	{
		$this->_pageJump = $flag;
	}

	/**
	 * Sets the use of mod_rewrite style links
	 * @param  bool  $flag
	 */

	public function setRewrite($flag)
	{
		$this->_rewrite = $flag;
	}

	/**
	 * Sets the total number of items
	 *
	 * @param  int  $int
	 */

	public function setTotalItems($int)
	{
		$this->_totalItems = $int;
	}

	/**
	 * Sets the Jump Forward Calculator
	 *
	 * @param  int  $int
	 */

	public function setJumpCalForward($int)
	{
		$this->_jumpCalcFoward = $int;
	}

	/**
	 * Sets the Jump Forward Calculator
	 *
	 * @param  int  $int
	 */

	public function setJumpCalBackward($int)
	{
		$this->_jumpCalcBackward = $int;
	}

	/**
	 * Sets the pageUrl
	 *
	 * @param  string  $url
	 */

	public function setPageUrl($url)
	{
		$this->_pagiUrl = $url;
	}

	/**
	 * Sets the number of items per page
	 *
	 * @param  int  $int
	 */

	public function setPerPage($int)
	{
		$this->_perPage = $int;
	}

	/**
	 * Returns the number of items that are displayed per page
	 *
	 * @return int
	 */

	public function getPerPage()
	{
		return $this->_perPage;
	}

	public function getStart()
	{
		return ($this->getCurrentPage() == 1 || $this->getCurrentPage() == 0) ? 0 : ($this->getCurrentPage() - 1) * $this->getPerPage();
	}

	/**
	 * Calculates the total number of pages based on the totalItems and Perpage
	 *
	 * @return int
	 */

	public function calculatePageTotal()
	{
		$calc = floor(($this->_totalItems) / ($this->_perPage));
		$this->totalPages = ($calc == 0) ? 1 : $calc;
		return $this->totalPages;
	}

	/**
	 * Returns the total number of pages
	 *
	 * @return int
	 */

	public function getTotalPages()
	{
		return $this->calculatePageTotal();
	}

	/**
	 * Returns the total number of items
	 *
	 * @return int
	 */

	public function getTotalItems()
	{
		return $this->_totalItems;
	}

	/**
	 * Sets the page unmber URL Param
	 *
	 * @return int
	 */

	public function setPageIdentifier($str = 'p')
	{
		$this->_pageIdentifer = $str;
	}

	public function getPageIdentifier()
	{
		return $this->_pageIdentifer;
	}

	/**
	 * This will attempt to load a default config
	 * for the stylizer
	 *
	 * @return int
	 */

	private function loadDefaultStyle()
	{
		if (!$this->customStyle && file_exists(THEMEDIR.'etc/pagination_style.php')) {
			include(THEMEDIR.'etc/pagination_style.php');
		}
	}

	/**
	 * Build the pagination
	 *
	 * @return string
	 */

	public function build()
	{
		// Attempt to force a style

		$this->loadDefaultStyle();

		$return = '';

		$back = '';

		// Figure out the total number of pages. Always round up :)>
		$totalPages = $this->getTotalPages();

		$range = ceil($this->_range / 2);

		$jumpRange = ceil($this->_jumpCount / 2);

		$return .= self::$styleArray['start'];

		$currentPage = $this->getCurrentPage();

		if ($this->_rewrite == true)
		{
			$length = strlen($this->_pagiUrl);
			$last = $length - 1;
			$lastChar = substr($this->_pagiUrl, $last, 1);
			$this->_pagiUrl = ($lastChar == '/') ? substr($this->_pagiUrl, 0, $length - 1) : $this->_pagiUrl;
			$append = '/';
		}
		elseif ( strpos($this->_pagiUrl, '?') === false )
		{
			$append = '?'.$this->getPageIdentifier().'=';
		}
		else
		{
			$append = '&'.$this->getPageIdentifier().'=';
		}

		if ($currentPage != 1 && $this->_displayLinkFirst == true)
		{
			$return .= ' '.self::$styleArray['first_start'].'<a href="'.$this->_pagiUrl.$append.'1" title="Go to page 1  of '.$totalPages.'">'.$this->linkPageOne.'</a>'.self::$styleArray['first_end'];
		}

		if ($currentPage > 1 && $this->_displayLinkPrev == true)
		{
			$prev = ($currentPage - 1);
			$return .= ' '.self::$styleArray['prev_start'].' <a href="'.$this->_pagiUrl.$append.$prev.'" title="Go to page '.$prev.' of '.$totalPages.'">'.$this->linkPagePrev.'</a> '.self::$styleArray['prev_end'];
		}

		// Build Backward Links
		for ($i = 1; $i <= $range; $i++)
		{
			$p = $currentPage - $i;
			if ($p > 0)
			{
				$back .= $p.',';
			}
		}

		$lastBack = $currentPage - $range - 1;

		// Backwards Jumps!

		// 1.2 Update
		// When jumping backwards we need to use floor....not ceil.....

		if ($lastBack > 2 && $this->_pageJump == true)
		{
			for ($i = 1; $i <= $jumpRange; $i++)
			{

				$lastBack = floor($lastBack / $this->_jumpCalcBackward);

				if ($lastBack >= 2)
				{
					$back .= $lastBack . ',';
				}
			}
		}

		$back = strrev($back);

		$backJump = explode(',', $back);


		foreach ($backJump as $rev)
		{
			if( $rev != null )
			{
				$rev = strrev($rev);
				$return .= ' '.self::$styleArray['link_start'].'<a href="'.$this->_pagiUrl.$append.$rev.'" title="Go to page '.$rev.' of '.$totalPages.'">'.$rev.'</a>'.self::$styleArray['link_end'];
			}
		}

		$return .=  self::$styleArray['current_start'].$currentPage.self::$styleArray['current_end'];


		for ($i = 1; $i <= $range; $i++)
		{

			$p = $currentPage + $i;
			if($p <= $totalPages)
			{
				$return .= ' '.self::$styleArray['link_start'].'<a href="'.$this->_pagiUrl.$append.$p.'" title="Go to page '.$p.' of '.$totalPages.'">'.$p.'</a>'.self::$styleArray['link_end'];
			}
		}

		$lastPage = $currentPage + $range;

		if ($this->_pageJump == true)
		{
			for ($i = 1; $i <= $jumpRange; $i++)
			{
				$lastPage = ceil($lastPage * $this->_jumpCalcFoward);

				if ($lastPage <= $totalPages)
				{

					$return .= ' '.self::$styleArray['link_start'].'<a href="'.$this->_pagiUrl.$append.$lastPage.'" title="Go to page '.$lastPage.' of '.$totalPages.'">'.$lastPage.'</a>'.self::$styleArray['link_end'];
				}
			}
		}

		// Build Next Link
		if ($currentPage < $totalPages && $totalPages != '0' && $this->_displayLinkNext == true)
		{
			$next = $currentPage + 1;
		    $return .= ' '.self::$styleArray['next_start'].'<a href="'.$this->_pagiUrl.$append.$next.'" title="Go to page '.$next.' of '.$totalPages.'">'.$this->linkPageNext.'</a>'.self::$styleArray['next_end'];
		}

		if ($currentPage != $totalPages && $totalPages != '0' && $this->_displayLinkLast == true)
		{
			$return .= ' '.self::$styleArray['last_start'].'<a href="'.$this->_pagiUrl.$append.$totalPages.'" title="Go to page '.$totalPages.' of '.$totalPages.'">'.$this->linkPageLast.'</a>'.self::$styleArray['last_end'];
		}

		$return .= self::$styleArray['end'];

		return  $return;
	}
}



