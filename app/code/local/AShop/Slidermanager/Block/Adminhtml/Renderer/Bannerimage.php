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
class AShop_Slidermanager_Block_Adminhtml_Renderer_Bannerimage extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $image_id = $row->getId();
        $image = Mage::getModel('slidermanager/slidermanager')->load($image_id)->getFilename();
        $mediaUrl = Mage::getBaseUrl('media');
        $imagename = $mediaUrl.'slidermanager/'.$image;
        return '<img id="image_banner' . $image_id . '" src="' . $imagename . '" height="50px"/>';
    }

}