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
class AShop_Slidermanager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getBannerImage($image) 
    {
        $name = $this->reImageName($image);
        $mediaUrl = Mage::getBaseUrl('media');
        return $mediaUrl.'slidermanager/'.$name;
    }
    
    public function reImageName($imageName) 
    {

        $subname = substr($imageName, 0, 2);
        $array = array();
        $subDir1 = substr($subname, 0, 1);
        $subDir2 = substr($subname, 1, 1);
        $array[0] = $subDir1;
        $array[1] = $subDir2;
        $name = $array[0] . '/' . $array[1] . '/' . $imageName;
        return strtolower($name);
    }
    
    public function getOptionSliderId() {
        $option = array();
        $option[] = array(
            'value' => '',
            'label' => Mage::helper('slidermanager')->__('<<<< Please Select A Slider >>>>'),
        );
        $slider = Mage::getModel('slidermanager/bannermanager')->getCollection();
        foreach ($slider as $value) {
            $option[] = array(
                'value' => $value->getId(),
                'label' => $value->getSlidername(),
            );
        }

        return $option;
    }
    
    function getId()
    {
        return $this->getBannermanagerId();
    }

}