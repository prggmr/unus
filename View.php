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

class Unus_View
{
    /**
     * Paths to use for finding templates
     */

	private $_path = array(
						   'scripts' => array(),
						   'base' => array());

	/**
	 * Instance of Unus_View
	 */
	private static $_instance = null;

    /**
     * Use template caching
     * TODO: Implement Unus_Cache
     */

	private $_useCache = true;

    /**
     * Current template being parsed
     * file => filname
     * path => full path to the template
     */

	private $_files = array();

    /**
     * Desc
     */

	private $_isAdmin = false;

    /**
     * Current file in the parser
     */

	private $_currentFile = null;

    /**
     * Template file extension
     */

	private $_templateExt = '.phtml';

    /**
     * Generated Path to currrent template
     */

	private $_parseTemplate = null;

    /**
     * Desc
     */

	private $_header = null;

    /**
     * String of files to parse within the <head>
     */

	private $_headerFiles = null;

    /**
     * Seperator used for header title
     */

	private $_headerSeperator = ' | ';

    /**
     * Auto Identify the path to view scripts based on the current controller/module
     */

	private $_autoIdentifyPaths = true;

    /**
     * Registers view path to admin
     */

	private $_adminScope = false;

	/**
	 *  Use theme support
	 */
	private $_useThemes = false;

	/**
	 * Current Theme
	 */
	private $_theme = false;

    /**
     * Identical to $_files except is holds the previous template parsed
     */

    private $_previousTemplate = array();

	/**
	 * Enforce singleton instance
	 */

	private function __construct()
	{
		// set default path
		$this->_addScriptPath(null);
	}

	/**
	 * Get instance of Unus_View
	 *
	 * @return Unus_View
	 */

	public static function getInstance()
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Sets or returns the status on using themes for loading templates
	 *
	 * @param  boolean  flag  Flag to use themes leave blank to return status
	 *
	 * @return  boolean
	 */

	public function useThemes($flag = null)
	{
		if (null != $flag) {
			$this->_useThemes = (boolean) $flag;
 		}
		return $this->_useThemes;
	}

	/**
	 * Returns current theme (if theme support is enabled)
	 */
	public function getTheme()
	{
		if ($this->useThemes()) {
			return $this->_theme;
		}
		return false;
	}

	/**
	 * Sets the name of the theme to use
	 *
	 * @param  string   theme  Name of theme to use for parsing templates
	 *
	 * @return  string
	 */
	public function setTheme($theme)
	{
		$this->_theme = (string) $theme;
	}


    /**
     * Set the path to find the view script used by render()
     *
     * @param string|array The directory (-ies) to set as the path. Note that
     * the concrete view implentation may not necessarily support multiple
     * directories.
     * @return void
     */
    public function setScriptPath($path)
	{
		if (!isset($this->_path['scripts'][$path])) {
			$this->_path['scripts'][$path] = $path;
		}
		return $this;
	}

	private function _addScriptPath($path) {
		if (!isset($this->_path['scripts'][$path])) {
			$this->_path['scripts'][$path] = $path;
		}
		return true;
	}

	private function _removeScriptPath($path) {
		if (!isset($this->_path['scripts'][$path])) {
			unset($this->_path['scripts'][$path]);
		}
		return true;
	}

    /**
     * Retrieve all view script paths
     *
     * @return array
     */
    public function getScriptPaths()
	{
		return $this->_path['scripts'];
	}

	// Overloading into the Unus:data object

	public function __set($name, $value)
	{
		Unus::register($name, $value);
	}

	// Overloading from the Unus:data object

	public function __get($name)
	{
		return Unus::registry($name);
	}

	// Overloading from the Unus:data object

	public function __unset($name)
	{
		Unus::unregister($name);
	}

	// Overloading from the Unus:data object

	public function __isset($name)
	{
		if (null == Unus::registry($name)) {
			return false;
		} else {
			return true;
		}
	}

	public function setAutoIdentifier($flag)
	{
		$this->_autoIdentifyPaths = $flag;
	}

	public function getAutoIdentifier()
	{
		return $this->_autoIdentifyPaths;
	}

	private function _getAutoIdentifiedPath()
	{
		//return Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	}

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or
     * property overloading ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars()
	{
		$vars   = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ($this->isPublic($key)) {
                unset($this->$key);
            }
        }
	}

	public function scriptPathExists($path)
	{
		return isset($this->_path['scripts'][$path]);
	}

	public function addScriptPath($path)
	{
		if (is_array($path)) {
			foreach ($path as $k) {
				$this->addScriptPath($path);
			}
		} else {
			$this->_addScriptPath($path);
		}
		return $this;
	}

	private function _getIncludePath($path)
	{
		$prepend = null;

		return $prepend.$path.'/';
	}

	/**
	 * TODO: Add custom template directory path instead of defaulted /app/skin/
	 */

	private function _include($file = null, $return = false)
	{
		if ($file == null) {
			$name = $this->_getTemplate() . $this->_getTemplateExt();
		} else {
			$name = $file . $this->_getTemplateExt();
		}

		$look = array();

		if ($this->getAutoIdentifier()) {
			$this->_addScriptPath($this->_getAutoIdentifiedPath());
		}

		foreach ($this->_path['scripts'] as $k => $v)
		{
			if ( strpos($this->_getTemplate(), '/') ) {
				$dir = explode('/', $this->_getTemplate());
				$path = $k;
				$key = 0;
				for ($i = 1; $i != count($dir); $i++) {
					$path .= '/'.$dir[$key];
					$key++;
				}
				$name = $dir[$key].$this->_getTemplateExt();
				$fullPath = $this->_getIncludePath($path).$name;
				$look[] = $this->_getIncludePath($path);
                // set final path
                $k = $path;
			} else {
				/**
				 * This will render the view path
				 */

				$fullPath = $this->_getIncludePath($k).$name;

				$look[] = $this->_getIncludePath($k);
			}

			if (is_readable($fullPath)) {
				$this->_setTemplate(array('file' => $this->_getTemplate(),
										  'path' => $k));
                // Set previous template information
                $this->_previousTemplate = array('file' => $this->_getTemplate(),
                                                 'path' => $k);

				$this->_parseTemplate = $fullPath;
				return true;
			}
		}

		if ($return == false) {
			$message = "script '$name' not found in path ("
					 . implode(PATH_SEPARATOR, $look)
					 . ")";
			throw new Unus_View_Exception($message, U_INVALID_VIEW);
		} else {
			return false;
		}
	}

	public function fileExists($file)
	{
		return $this->_include($file, true);
	}

	private function _render()
	{
		ob_start();
		include ($this->_parseTemplate);
		$file = ob_get_clean();
		return $file;
		ob_flush();
	}

	/**
	*/

	public function getHtml($name)
	{
		// Extension?
		if (strpos($name, '.')) {
			throw new Unus_View_Exception('File extensions are prohibited; to customize extensions use Unus_View::setTemplateExt()');
		} else {
			$this->_setTemplate($name);
			$templateFile = $this->_include();
			return $this->_render($templateFile);
		}
	}

	public function getChildHtml($name, $saveParent = false)
	{
		if (is_array($this->_getTemplate())) {
			// add script path for current child
			$parent = $this->_getTemplate();
			$this->_addScriptPath($parent['path'].'/'.$name);
			$fileContents = $this->getHtml($name);
			if ($saveParent) {
				$this->_setTemplate(array('file' => $parent['file'],
										  'path' => $parent['path']));
			}
			return $fileContents;
		} else {
			throw new Unus_View_Exception('A parent template has not been called; Cannot get children');
		}
	}

	public function headerTitle($str = null, $location = 'APPEND')
	{
		if ($str == null) {
			return $this->_header;
		} else {
			if (is_array($str)) {
				foreach ($str as $v) {
					$this->headerTitle($v, $location);
				}
			} else {
				if ($location == 'SET') {
					$this->_header = $str;
				} elseif ($location == 'PREPEND') {
					$this->_header = $str . $this->_getHeadersSeperator() . $this->_header;
				} else {
					$this->_header = $this->_header . $this->_getHeadersSeperator() . $str;
				}
			}
		}
	}

	public function getFooter()
	{
		if (!$this->scriptPathExists('body')) {
			$this->_addScriptPath('body');
		}

		if ($this->getAdminScope() == true) {
			$this->setAdminScope(false);
		    $render = $this->getHtml('footer');
			$this->setAdminScope(true);
			return $render;
		} else {
			return $this->getHtml('footer');
		}
	}

	public function getAdminFooter()
	{
		if (!$this->scriptPathExists(Unus::getLibraryPath().'Unus/Cura/skin/body')) {
			$this->_addScriptPath(Unus::getLibraryPath().'Unus/Cura/skin/body');
		}

		return $this->getHtml('footer');
	}

	public function headerFile($str = null, $location = 'APPEND')
	{
		if ($str == null && $this->_headerFiles != null) {
			return $this->_headerFiles;
		} else {
			if (is_array($str)) {
				foreach ($str as $v) {
					$this->headerFile($v, $location);
				}
			} else {
				if ($location == 'SET') {
					$this->_headerFiles = $str;
				} elseif ($location == 'PREPEND') {
					$this->_headerFiles = $str . ' ' . $this->_headerFiles;
				} else {
					$this->_headerFiles = $this->_headerFiles . ' ' . $str;
				}
			}
		}
	}

	public function getAdminHeader()
	{
		if (!$this->scriptPathExists(Unus::getLibraryPath().'Unus/Cura/skin/body')) {
			$this->_addScriptPath(Unus::getLibraryPath().'Unus/Cura/skin/body');
		}

		return $this->getHtml('header');
	}

	private function _getHeadersSeperator()
	{
		return $this->_headerSeperator;
	}

	public function setHeaderSeperator($str)
	{
		$this->_headerSeperator = $str;
	}

	public function getHead()
	{
		return $this->getChildHtml('head', false);
	}

	public function getHeader()
	{
        $previousTemplate = $this->_previousTemplate;
		if ($this->getAdminScope() == true) {
			$this->setAdminScope(false);
			$file = $this->getHtml('header');
			$this->setAdminScope(true);
		} else {
			$this->_addScriptPath('body');
			$file = $this->getHtml('header');
		}
        // after we have parsed the header jump back to the previous view path
        $this->_setTemplate($previousTemplate);
		return $file;
	}

	private function _setTemplate($name)
	{
		$this->_currentFile = $name;
		return $this;
	}

	private function _getTemplate()
	{
		return $this->_currentFile;
	}

	public function getTemplate()
	{
		return $this->_currentFile;
	}

	public function setTemplateExt($str)
	{
		if (strpos('.')) {
			$this->_templateExt = $str;
			return $this;
		}
		return false;
	}

	private function _getTemplateExt()
	{
		return $this->_templateExt;
	}

	public function getTemplateExt()
	{
		return $this->_templateExt;
	}

	private function isPublic($str)
	{
		if ('_' != substr($str, 0, 1)) {
			return true;
		}
		return false;
	}

	public function setAdmin($flag)
	{
		if (!$this->getAdminScope()) {
			throw new Unus_View_Exception('Administration view templates cannot be loaded from non-administrative areas');
		}
		$this->_isAdmin = (bool) $flag;
		return $this;
	}

	public function setAdminScope($flag) {
		$this->_adminScope = (bool) $flag;
	}

	private function getAdminScope() {
		return $this->_adminScope;
	}

	public function getAdmin()
	{
		return $this->_isAdmin;
	}
}
