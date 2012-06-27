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

class Unus_Form_Generate_Fieldset extends Unus_Form_Generate_Abstract
{
	
	/**
	 * HTML Tags for a fieldset
	 *
	 * Open
	 * Close
	 * Legend
	 *
	 * @var  string
	 */
	public $open_tag   = null;
	public $close_tag  = null;
	public $legend = null;

	/**
	 * Generates a html fieldset tag
	 *
	 * @return  string
	 */
	
    public function generate()
	{
		$legend  = $this->_element->getData('legend');
		$id    	 = $this->_element->getData('id');
		$options = $this->_element->getData('options');
		
		$array['open_tag'] = '<fieldset id="'.$id.'"';
		
		if (null != $options) {
			foreach ($options as $k => $v) {
				$array['open_tag'] .= ' '.$k.'="'.$v.'"';
			}
		}
		
		$array['open_tag'] .= '>';
		
		if (null != $legend) {
			$array['legend'] = '<legend>'.$legend.'</legend>';
		}
		
		$array['close_tag'] = '</fieldset>';
		
		$this->open_tag  = $array['open_tag'];
		$this->close_tag = $array['close_tag'];
		$this->legend 	 = $array['legend'];
		
		return $this;
	}
	
	/**
	 * Fieldset object cannot be converted to a string
	 *
	 * @return
	 * @throws Unus_Form_Generate_Fieldset_Exception
	 */
	
	public function __toString()
	{
		throw new Unus_Form_Generate_Fieldset_Exception('Fieldset generator cannot be converted to string; Use open_tag, close_tag, legend object properties');
	}
}
