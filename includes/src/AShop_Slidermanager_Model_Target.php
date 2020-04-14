<?php 
/**
 * AShop Slider
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the AccessshopThemes.com license that is
 * available through the world-wide-web at this URL:
 * http://www.accessshopThemes.com
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @copyright 	Copyright (c) 2015 Accessshop (http://www.accessshopThemes.com)
 * @license     http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Accessshop Block
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @author  	AccessShop Developer
 */
class AShop_slidermanager_Model_Target
{
    public function getTargetOption()
    {
        return array(
            array('value' => '_blank', 'label' => Mage::helper('slidermanager')->__('_blank')),
            array('value' => '_self', 'label' => Mage::helper('slidermanager')->__('_self')),
            array('value' => '_parent', 'label' => Mage::helper('slidermanager')->__('_parent')),
            array('value' => '_top', 'label' => Mage::helper('slidermanager')->__('_top'))
            );
    }
}
