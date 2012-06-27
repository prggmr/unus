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

class Unus_Model_Analyze extends Unus_Data
{
    /**public function __construct(Unus_Model $models) {
        $this->setData('models', $models);
        // Analyze Main Data Structure
        $this->analyzeStructure();
    }

    /**
     * Analyzes Model Structures
     *
     *

    public function analyzeStructure($models = array())
    {
        $pages = array();
        foreach ($models as $k => $v)
        {
            foreach ($v as $t => $f) {
                $pages[$t] = new Unus_Data();
                $pages[$t]->setData('config', $f->getData('config'));
                foreach ($f->getData('fields') as $fn => $ft) {
                    $pages[$t]->setData($fn, new Unus_Data());
                    $pages[$t]->getData($fn)->setData('name', $ft['name']);
                    $pages[$t]->getData($fn)->setData('type', $ft['type']);
                    $options = new Unus_Data();
                    $pages[$t]->getData($fn)->setData('options', $options->addData($ft['options']));
                }
            }
        }

        $this->setData('structure', $pages);
    }*/
}
