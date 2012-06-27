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

class Unus_Helper_Index_Build_Parent_Array
{
    
    /**
     * Rearranges a array and prepares it for buildParentArray
     * 
     * @param  array  $array
     * @return string
     */ 
    
    public function prepareArrayBuild(array $array)
    {   
        foreach ($array as $k => $v)
        {
            $buildArray[$v['parentId']][$k] = $v;
        }
        
        return $buildArray;
    }
    
    /**
     * Builds a recursive array for parent->child relationships
     * 
     * @param  array  $array
     * @param  int    $id
     * @return array
     */
    
    public function buildParentArray(array $array, $id = 0)
    {
        $parentArray = array();
        
        foreach ($array[$id] as $k =>$v)
        {
            if (isset($array[$k])) $v['children'] = $this->buildParentArray($array, $k);
            
            $parentArray[$k] = $v;
        }
        
        return $parentArray;
    }
}

?>