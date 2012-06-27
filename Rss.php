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

class Unus_Rss
{
    private $_title = null;
    
    private $_link = null;
    
    private $_description = null;
    
    private $_copyright = null;
    
    private $_image = array();
    
    private $_publishDate = null;
    
    private $_items = array();
    
    private $_cache = true;
    
    private $_atomLink = null;
    
    private $_cacheLife = 2;
    
    public function __construct($title, $link, $description)
    {
        $this->_title = $title;
        $this->_link  = $link;
        $this->_description = $description;
    }
    
    public function setImage($args = array())
    {
        $this->_image = $args;
    }
    
    public function setPublish($timestamp)
    {
        $this->_publishDate = $timestamp;
    }
    
    public function setCache($flag)
    {
        $this->_cache = (bool) $flag;
    }
    
    public function setCacheLife($int)
    {
        $this->_cacheLife = (int) $int;
    }
    
    public function setCopyright($str)
    {
        $this->_copyright = $str;
    }
    
    public function setAtom($str)
    {
        $this->_atomLink = $str;
    }
    
    // Reset all vars to defaults
    
    public function _default()
    {
        $this->_image = array();
        $this->_copyright = null;
        $this->_title = null;
        $this->_link  = null;
        $this->_description = null;
        $this->_items = array();
        $this->_cache = true;
        $this->_cacheLife = 2;
    }
    
    public function addItem($key, $array, $overload = false)
    {
        if (isset($this->_items[$key])) {
            if ($overload) {
                unset($this->_items[$key]);
            } else {
                return false;
            }
        } 
        
        // Build the item string based on array information giving
        
        $this->_items[$key] = array();
        /**Zend_Debug::Dump($array);
        exit*/;
        
        if (!array_key_exists('title', $array) ||  !array_key_exists('description', $array) || !array_key_exists('pubDate', $array)) {
            throw new Unus_Rss_Exception('Rss Item does not contain a valid title, description or publish date - itemKey : '.$key);
        }
        
        foreach ($array as $k => $v)
        {   
            if (is_array($v))
            {
                $this->_items[$key][$k] = array();
                
                foreach ($v as $k2 => $v2)
                {
                    $this->_items[$key][$k][$k2] = $v2;
                }
            }
            else
            {
                $this->_items[$key][$k] = $v;
            }
        }
        
        return true;
    }
    
    public function removeItem($key)
    {
        if (isset($this->_items[$key])) {
            unset($this->_items[$key]);
            return true;
        }
        
        return false;
    }
    
    public function buildRss()
    {
        // Default Header
        $str  = '<?xml version="1.0" encoding="UTF-8"?>
                 <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
                 <channel>
                 <title>'.$this->_title.'</title>
                 <link>'.$this->_link.'</link>
                 <description>'.$this->_description.'</description>
                 <language>en-us</language>
                 <ttl>30</ttl>
                 <atom:link href="'.$this->_atomLink.'" rel="self" type="application/rss+xml" />
                 <copyright>'.$this->_copyright.'</copyright>
                 <pubDate>'.$this->_publishDate.'</pubDate>
                 ';
                 
        if (count($this->_image) != 0)
        {
            $str .= '<image>
                     <title>'.$this->_image['title'].'</title>
                     <link>'.$this->_image['link'].'</link>
                     <url>'.$this->_image['url'].'</url>
                     </image>';
        }
        
        // Start building the items
        
        foreach ($this->_items as $k => $v)
        {
            // Item Start
            $str .= '<item>
            ';
            
            // Tags
            
            foreach ($v as $k2 => $v2)
            {
                // Start
                $str .= '<'.$k2.'';
                if (is_array($v2))
                {
                    // Tags Attributes
                    foreach ($v2 as $k3 => $v3)
                    {
                        if ($k3 != 'value') {
                            $str .= ' '.$k3.'="'.$v3.'"';
                        } else {
                            $content = $v3;
                        }
                    }
                    $str .= '>'.$content.'';
                }
                else
                {
                    $str .= '>'.$v2.'';
                }
                // End
                $str .= '</'.$k2.'>
                ';
            }
            
            // Item End
            $str .= '</item>
            ';
        }
        
        // default footer
        $str .= '</channel>
                 </rss>';
        
        return $str;
    }
}