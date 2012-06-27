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
 * !! WARNING !!
 * ----------------------------------------------
 * THE CONTENTS OF THIS FILE OR CURRENTLY IN BETA
 *
 * IT IS NOT RECOMMENDED TO USE UNUS_CACHE UNTIL A PRODUCTION LEVEL RELEASE
 * HAS BEEN ESTABLISHED
 *
 * TODO: Intergrate APC|Speed up Unus Caching mechanism
 */

class Unus_Cache
{
    private static $_instance = null;

    /**
     * Cache Directory
     */

    private $_cache = null;

    /**
     * File type caching.
     * What kind of files will we be caching
     */

    private $_fileType = 'php';

    /**
     * File Extension to save with file
     * Defaults to .bin
     */

    private $_fileExt = 'bin';

    /**
     * Garbage collection probability
     * Probability that garbage collection will clean old cache files
     */

    private $_gcProbability = 1;

    /**
     * Divisor used to calculate chance that garbage collection will run
     */

    private $_gcDivisor = 100;

    /**
     * Length of time to keep files in cache dir
     * Defaults to 2 Weeks
     */

    private $_gcLength = 172800;  // 2 weeks

    /**
     * Instance of filetype cache object
     */
    private $_cacheObj = null;

    /**
     * Cache directory information
     */
    public $cacheData = null;

    /**
     * Information File that holds Cache directory information
     */
    private $_infoFile = null;

    /**
     * !! WARNING !!
     * ----------------------------------------------
     * THIS CLASS IS CURRENTLY EXPIREMENTAL
     *
     * IT IS NOT RECOMMENDED TO USE UNUS_CACHE UNTIL A PRODUCTION LEVEL RELEASE HAS BEEN TAGGED
     *
     * 
     * Initializes instance of Unus_Cache
     * Filetype will load a cache mechinism for specific files otherwise Unus_Cache will be used
     *
     * @param  string  cache      Path to directory to use for cache DO NOT APPEND /
     * @param  string  method     Type of file caching to use, must exist in Unus_Cache_*
     * @param  string  infoFile   File to use for storing cache file information
     */

    public static function getInstance($cache, $method = null, $infoFile = null)
    {
        if (null == self::$_instance) {
            self::$_instance = new self($cache, $method, $infoFile);
        }

        return self::$_instance;
    }

    /**
     * @see self::getInstance()
     */


    protected function __construct($cache, $method = null, $infoFile = null)
    {
        $this->_cache = $cache;
        $runGc = $this->calculateGc();

        if ($method != null) {
            if (file_exists('Cache/'.ucfirst($method).'.php')) {
                include 'Cache/'.ucfirst($method).'.php';
            } else {
                throw new Unus_Cache_Exception('Cache Type : '.$method.' is not a supported file caching type, or cannot be located');
            }

            $class = 'Unus_Cache_'.ucfirst($method);

            $this->_cacheObj = new $method($cache);
        }

        /**if ($runGc == true) {
            if (null != $this->_cacheObj) {
                $this->_cacheObj->gc();
            } else {
                $this->_gc();
            }
        }*/

        $infoFile = (null == $infoFile) ? 'cache-info.xml' : $infoFile;

        $this->readInformation($infoFile);
    }

    /**
     * Calculate wither or not to run gc based on
     * gcProbability and gcDivisor
     *
     * A probability of 10 and a gcDivisor of 100 will give
     * a 10% chance of garbage collection or once every 90 requests
     *
     * @return boolean
     */

    public function calculateGc()
    {
        // Get a random number from 0 to Garbage collection divisor
        $num = mt_rand(0, $this->getGcDivisor());

        // subtract each sum if <= 0 run garbage
        if (($this->getGcProbability() - $num) <= 0) {
            return true;
        }
        return false;
    }

    /**
     * Gets gc probability
     *
     * @return int
     */

    public function getGcProbability()
    {
        return $this->_gcProbability;
    }

    /**
     * Gets gc divisor
     *
     * @return int
     */

    public function getGcDivisor()
    {
        return $this->_gcDivisor;
    }

    /**
     * Sets gc probability, cannot be greater than gcDivisor
     *
     * @param  int  $int  Int for gc Probability
     *
     * @return this
     */

    public function setGcProbability($int)
    {
        if ($this->getGcDivisor() < $int) {
            throw new Unus_Cache_Exception('Garbage collection probability cannot be greater than the divisor');
        }
        $this->_gcProbability = $int;

        return $this;
    }

    /**
     * Sets gc divisor, cannot be less than gcProbability
     *
     * @return this
     */

    public function setGcDivisor()
    {
        if ($this->getProbability() > $int) {
            throw new Unus_Cache_Exception('Garbage collection divisor must be greater than the probability');
        }
        $this->_gcDivisor = $int;

        return $this;
    }

    /**
     * Returns current cache directory
     *
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->_cache;
    }

    /**
     * Returns length a file is keep in cache before gc will remove it
     *
     *
     * @return int
     */
    public function getGcLength()
    {
        return $this->_gcLength;
    }

    /**
     * Runs Garbage collection
     *
     * @return this
     */
    protected function _gc()
    {
        $dir = scandir($this->getCacheDir());

        foreach ($dir as $k => $v) {
            if (strpos($str,'.') > 0) {
                $mTime =  filemtime($this->getCacheDir().DIRECTORY_SEPARATOR.$v);
                if ($mtime != false) {
                    if ($mtime < (time() - $this->getGcLength())) {
                        @unlink($this->getCacheDir().DIRECTORY_SEPARATOR.$v);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Touches a file in cache dir
     *
     * @param  string  file  File in cache directory to touch
     *
     * @return this
     */

    public function touchFile($file)
    {
        $file = $this->getCacheDir().DIRECTORY_SEPARATOR.$file;
        if ($this->cacheData->files->$file != null) {
            //surpress touch file errors
            $touch = time();
            @touch($file, $touch);
            $this->cacheData->files->$file->last_modified = $touch;
            $xmltext = $this->cacheData->asXML();
            // Write new XML data and LOCK file
            file_put_contents($this->_infoFile, $xmltext, LOCK_EX);
        }

        return $this;
    }

    public function readInformation($file)
    {
        $file = $this->getCacheDir().DIRECTORY_SEPARATOR.$file;
        $this->_infoFile = $file;
        // Cache contents is stored as a XML Data file
        if (file_exists($file)) {
            $cache = simplexml_load_file($file);
            /**if (null == $cache->files) {
                $cache->files = new SimpleXMLElement(null);
                //$cache = new simplexml_load_file();
            }*/
        } else {
            $xml = new XMLWriter();
            $xml->openMemory();
            $xml->startElement('data');
            $xml->startElement('files');
            $xml->startElement('xml-cache-file');
            $xml->writeElement('last_modified', time());
            $xml->endElement();
            $xml->endElement();
            $xml->endElement();
            file_put_contents($file, $xml->outputMemory(true));
            $cache = simplexml_load_file($file);
        }
        $this->cacheData = $cache;
        return $this;
    }

    /** Adds a file to the cache */
    public function addFile($file)
    {
        // adds a new file to the cache
        //$newFile = $this->getCacheDir().DIRECTORY_SEPARATOR.$file;
        $include = str_replace('-', '/', $file);
        $data = file_get_contents($include, true);
        $newFile = rand(1000, 1000000);
        $data = $this->compileFile($data);
        file_put_contents($this->getCacheDir().DIRECTORY_SEPARATOR.$newFile.'.'.$this->_fileExt, $data);
        // write creation time
        $fileName = str_replace('/', '-', $include);
        $this->cacheData->files->addChild($fileName);
        $this->cacheData->files->$fileName->addChild('last_modified', time());
        // write file contents
        $this->cacheData->files->$fileName->addChild('file', $this->getCacheDir().DIRECTORY_SEPARATOR.$newFile.'.'.$this->_fileExt);
        $xmltext = $this->cacheData->asXML();
        // Write new XML data and LOCK file
        file_put_contents($this->_infoFile, $xmltext);
    }

    /** Fetch cached file */

    public function fetchFile($file) {
        $file = str_replace('/', '-', $file);
        if ($this->cacheData->files->$file->file == null) {
            $this->addFile($file);
        }
        include($this->cacheData->files->$file->file);
    }

    /** Delete cached file */

    public function deleteFile($file) {
        $file = $this->getCacheDir().DIRECTORY_SEPARATOR.$file;
        unset($this->cacheData->files->$file);
        $xmltext = $this->cacheData->asXML();
        // Write new XML data and LOCK file to prevent cache deletion overwrite
        file_put_contents($this->_infoFile, $xmltext, LOCK_EX);

    }

    /** Compile cached file */

    public function compileFile($str) {
        // we will not compile anything in this as we will parse ALL just return simple string
        if (!defined('T_ML_COMMENT')) {
            define('T_ML_COMMENT', T_COMMENT);
         } else {
            define('T_DOC_COMMENT', T_ML_COMMENT);
         }

         $return = null;
         $tokens = token_get_all($str);

         foreach ($tokens as $token) {
            if (is_string($token)) {
                // simple 1-character token
                $return .= trim($token). ' ';
            } else {
                // token array
                list($id, $text) = $token;

                switch ($id) {
                    case T_COMMENT:
                    case T_ML_COMMENT: // we've defined this
                    case T_DOC_COMMENT: // and this
                        // no action on comments
                        break;

                    default:
                        // anything else -> output "as is"
                        $return .= trim($text).' ';
                        break;
                }
            }
        }

        return $return;
    }
}
