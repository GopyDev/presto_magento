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
class Zeon_AjaxCart_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_ENABLED = 'zeon_ajaxcart/general/is_enabled';
    const RELATED_PRODUCTS = 1;
    const UP_SELLS = 2;
    const CROSS_SELLS = 3;
    const XML_PATH_AUTO_IS_ENABLED = 'zeon_ajaxcart/frontend/is_enabled';
    const XML_PATH_AUTO_CART_ENABLED = 'zeon_ajaxcart/frontend/cart_enabled';
    /* const XML_PATH_AUTO_WHISHLIST_ENABLED = 'zeon_ajaxcart/frontend/whishlist_enabled';
      const XML_PATH_AUTO_COMPARE_ENABLED = 'zeon_ajaxcart/frontend/compare_enabled'; */
    const XML_PATH_AUTO_HIDE_POPUP = 'zeon_ajaxcart/frontend/auto_hide_popup';
    const XML_PATH_RULE_PRODUCTS_BLOCK = 'zeon_ajaxcart/frontend/show_rule_products_block';

    public function setIsModuleEnabled($value)
    {
        Mage::getModel('core/config')->saveConfig(self::XML_PATH_ENABLED, $value);
    }

    /**
     * Enable/Disable module
     *
     * @return int
     */
    public function getIsEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_AUTO_IS_ENABLED);
    }

    /**
     * Enable/Disable cart
     *
     * @return int
     */
    public function isCartEnabled()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_AUTO_CART_ENABLED);
    }

    /**
     * Enable/Disable whishlist
     *
     * @return int
     */
    /*  public function isWhishlistEnabled()
      {
      return (int) Mage::getStoreConfig(self::XML_PATH_AUTO_WHISHLIST_ENABLED);
      } */

    /**
     * Enable/Disable compare
     *
     * @return int
     */
    /*  public function isCompareEnabled()
      {
      return (int) Mage::getStoreConfig(self::XML_PATH_AUTO_COMPARE_ENABLED);
      } */

    /**
     * Retrieve product block type
     *
     * @return string
     */
    public function getRuleProductBlockType()
    {
        return Mage::getStoreConfig(self::XML_PATH_RULE_PRODUCTS_BLOCK);
    }

    /**
     * Retrieve auto hide popup flag
     *
     * @return string
     */
    public function getIsAutoHidePopup()
    {
        return Mage::getStoreConfig(self::XML_PATH_AUTO_HIDE_POPUP);
    }

    public function getAdditionalProductBlock($blockType)
    {
        switch ($blockType) {
            case self::RELATED_PRODUCTS:
                $additionalBlock = '';
                break;
            case self::UP_SELLS:
                $additionalBlock = '';
                break;
            case self::CROSS_SELLS:
            default:
                $additionalBlock = \
                Mage::app()->getLayout()->createBlock('enterprise_targetrule/checkout_cart_crosssell', 'crosssell')
                                ->setTemplate('zeon/ajaxcart/targetrule/checkout/cart/popup/crosssell.phtml')->toHtml();
                break;
        }
        return $additionalBlock;
    }

}