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
class Zeon_AjaxCart_CompareController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_ENABLED = 'zeon_ajaxcart/general/is_enabled';

    public function preDispatch()
    {
        parent::preDispatch();

        $response = array();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $response["message"] = $this->__('Cannot compare product. Please Enable the AjaxCart extension.');
            return;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * Add item to compare list
     */
    public function addAction()
    {
        $response = array();
        $response["message"] = $this->__('Cannot compare product.');
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);

            if ($product->getId()/* && !$product->isSuper() */) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $response["message"] = 
                $this->__(
                    'The product %s has been added to comparison list.', 
                    Mage::helper('core')->escapeHtml($product->getName())
                );
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product' => $product));
            }

            $sidebar = $this->getLayout()->createBlock('catalog/product_compare_sidebar')
                            ->setTemplate('zeon/ajaxcart/catalog/product/compare/sidebar.phtml')->toHtml();

            $response["sidebar"] = 
            str_replace(
                '<div id="block-compare" class="block block-list block-compare">', 
                '', 
                rtrim($sidebar, '</div>')
            );
            $response["block_id"] = 'block-compare';
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}
