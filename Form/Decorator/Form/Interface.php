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

interface Unus_Form_Decorator_Form_Interface
{
    /**
     * Parses a new element and returns a decorated string
     *
     * @param  object  element  Unus_Form_Elements_Abstract
     *
     * @return
     */
    
    public function __construct(Unus_Form $form);
    
	/**
	 * Renders the beginning body of the form
	 *
	 */
	
    public function renderStart();
    
	/**
	 * Renders the end body of the form
	 *
	 * @param  type  name  desc
	 *
	 * @return
	 */
    public function renderEnd();
}
