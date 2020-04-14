<?php

/**
 * zeonsolutions inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/shop/license-community.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * This package designed for Magento COMMUNITY edition
 * =================================================================
 * zeonsolutions does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * zeonsolutions does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Zeon
 * @package    Zeon_AjaxCart
 * @version    0.0.1
 * @copyright  @copyright Copyright (c) 2013 zeonsolutions.Inc. (http://www.zeonsolutions.com)
 * @license    http://www.zeonsolutions.com/shop/license-community.txt
 */
class Zeon_AjaxCart_Model_Adminhtml_System_Config_Source_Rule_Type
{
    const NONE             = 0;
    const RELATED_PRODUCTS = 1;
    const UP_SELLS         = 2;
    const CROSS_SELLS      = 3;
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array(
            'label' => Mage::helper('zeon_ajaxcart')->__('None'),
            'value' => self::NONE
        );
        /*$options[] = array(
                'label' => Mage::helper('zeon_ajaxcart')->__('Related Products'),
                'value' => self::RELATED_PRODUCTS
            );
        $options[] = array(
                'label' => Mage::helper('zeon_ajaxcart')->__('Up-sells'),
                'value' => self::UP_SELLS
            );*/
        $options[] = array(
                'label' => Mage::helper('zeon_ajaxcart')->__('Cross-sells'),
                'value' => self::CROSS_SELLS
            );
        return $options;
    }
}
