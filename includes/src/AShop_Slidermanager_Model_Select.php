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
class AShop_Slidermanager_Model_Select extends Varien_Object {

    static public function getOptionArray() {
        $block = Mage::getSingleton('slidermanager/slidermanager')->getCollection();

        $data = array();
        foreach ($block as $row) {
            $data[] = array(
                'value' => $row->getId(),
                'label' => $row->getTitle(),
            );
        }
        return $data;
    }

    static public function getOptionHash() {
        $block = Mage::getSingleton('slidermanager/slidermanager')->getCollection();
        $data = array();
        foreach ($block as $row) {
            $data[$row->getId()] = $row->getTitle();
        }
        return $data;
    }

}
