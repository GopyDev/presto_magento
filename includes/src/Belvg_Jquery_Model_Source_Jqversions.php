<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_jQuery
 * @version    1.9.1
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
 
class Belvg_Jquery_Model_Source_Jqversions
{
    
    /**
     * Getting array for select
     */
    public function toOptionArray()
    {    
        $result = array();
        foreach ((array)Mage::getConfig()->getNode('jquery/versions') as $key=>$item) {
            $tmp = (array)$item;
            $result[$key] = $tmp['label'];
        }

        foreach ((array)Mage::getConfig()->getNode('jquery/compatibility') as $key=>$item) {
            if (!empty($item)) {
                $result = array_intersect_key($result, (array)$item);         
            }
        }

        return $result;
    }
    
}