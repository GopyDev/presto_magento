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
class Zeon_AjaxCart_ProductController extends Mage_Core_Controller_Front_Action
{

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    /**
     * Product view action
     */
    public function viewAction()
    {
        // Get initial data from request
        $product = $this->_initProduct();

        if ($product && $product->getId()) {
            Mage::register('current_product', $product);
            Mage::register('product', $product);

            $layout = $this->getLayout();

            $options = $layout->createBlock('catalog/product_view_options', 'container2')
                    ->setTemplate('catalog/product/view/options.phtml')
                    ->addOptionRenderer(
                        'text', 
                        'catalog/product_view_options_type_text', 
                        'catalog/product/view/options/type/text.phtml'
                    )
                    ->addOptionRenderer(
                        'select', 
                        'catalog/product_view_options_type_select', 
                        'catalog/product/view/options/type/select.phtml'
                    )
                    ->addOptionRenderer(
                        'file', 
                        'catalog/product_view_options_type_file', 
                        'catalog/product/view/options/type/file.phtml'
                    )
                    ->addOptionRenderer(
                        'date', 
                        'catalog/product_view_options_type_date', 
                        'catalog/product/view/options/type/date.phtml'
                    );

            $price = $layout->createBlock('catalog/product_view', 'prices')
                    ->setTemplate('catalog/product/view/price_clone.phtml');

            $js = $layout->createBlock('core/template', 'options_js')
                    ->setTemplate('catalog/product/view/options/js.phtml');

            $calendar = $layout->createBlock('core/html_calendar', 'html_calendar')
                    ->setTemplate('page/js/calendar.phtml');

            $productView = $layout->createBlock('catalog/product_view')
                    ->setTemplate('zeon/ajaxcart/catalog/product/view.phtml')
                    ->append($options);

            if ($product->getTypeId() == 'simple') {
                $simple = $layout->createBlock('catalog/product_view_type_simple', 'product_type_data')
                        ->setTemplate('catalog/product/view/type/default.phtml');
                $productView->append($simple);
            }

            if ($product->isConfigurable()) {
                $configurable = $layout->createBlock('catalog/product_view_type_configurable', 'container1')
                        ->setTemplate('catalog/product/view/type/options/configurable.phtml');
                $productView->append($configurable);

                $configurableOptions = 
                $layout->createBlock('catalog/product_view_type_configurable', 'product_type_data')
                        ->setTemplate('catalog/product/view/type/default.phtml');
                $productView->append($configurableOptions);
            }

            if ($product->getTypeId() == 'grouped') {
                $grouped = $layout->createBlock('catalog/product_view_type_grouped', 'product_type_data')
                        ->setTemplate('catalog/product/view/type/grouped.phtml');
                $productView->append($grouped);
            }

            if ($product->getTypeId() == 'virtual') {
                $virtual = $layout->createBlock('catalog/product_view_type_virtual', 'product_type_data')
                        ->setTemplate('catalog/product/view/type/default.phtml');
                $productView->append($virtual);
            }

            if ($product->getTypeId() == 'giftcard') {
                $giftCard = 
                $layout->createBlock('enterprise_giftcard/catalog_product_view_type_giftcard', 'product_type_data')
                        ->setTemplate('giftcard/catalog/product/view/type/giftcard.phtml');
                $giftCard->addPriceBlockType(
                    'giftcard', 
                    'enterprise_giftcard/catalog_product_price', 
                    'giftcard/catalog/product/price.phtml'
                );
                $productView->append($giftCard);

                $price->addPriceBlockType(
                    'giftcard', 
                    'enterprise_giftcard/catalog_product_price', 
                    'giftcard/catalog/product/price.phtml'
                );
            }

            if ($product->getTypeId() == 'downloadable') {
                $downloadable = $layout->createBlock('downloadable/catalog_product_links', 'container1')
                        ->setTemplate('downloadable/catalog/product/links.phtml');
                $productView->append($downloadable);

                $downloadableOptions = 
                $layout->createBlock('downloadable/catalog_product_view_type', 'product_type_data')
                        ->setTemplate('downloadable/catalog/product/type.phtml');
                $productView->append($downloadableOptions);
            }

            if ($product->getTypeId() == 'bundle') {
                $bundle = $layout->createBlock('bundle/catalog_product_view_type_bundle', 'product_type_data')
                        ->setTemplate('zeon/ajaxcart/bundle/catalog/product/view/type/bundle.phtml');
                $productView->append($bundle);

                $bundleOptions = $layout->createBlock('bundle/catalog_product_view_type_bundle', 'container3')
                        ->setTemplate('bundle/catalog/product/view/type/bundle/options.phtml');
                $bundleOptions->addRenderer('select', 'bundle/catalog_product_view_type_bundle_option_select');
                $bundleOptions->addRenderer('multi', 'bundle/catalog_product_view_type_bundle_option_multi');
                $bundleOptions->addRenderer('radio', 'bundle/catalog_product_view_type_bundle_option_radio');
                $bundleOptions->addRenderer('checkbox', 'bundle/catalog_product_view_type_bundle_option_checkbox');
                $productView->append($bundleOptions);

                $tierPrices = $layout->createBlock('bundle/catalog_product_view', 'tierprices')
                        ->setTemplate('bundle/catalog/product/view/tierprices.phtml');
                $productView->append($tierPrices);

                $bundlePrices = $layout->createBlock('bundle/catalog_product_price', 'bundle_prices')
                        ->setTemplate('bundle/catalog/product/view/price.phtml');
                $productView->append($bundlePrices);

                $price->addPriceBlockType(
                    'bundle', 'bundle/catalog_product_price', 
                    'bundle/catalog/product/view/price.phtml'
                );
                $price->setMAPTemplate('catalog/product/price_msrp_item.phtml');
            } else {
                $tierPrices = $layout->createBlock('catalog/product_view', 'tierprices')
                        ->setTemplate('catalog/product/view/tierprices.phtml');
                $productView->append($tierPrices);
            }

            $addToCart = $layout->createBlock('catalog/product_view', 'addtocart')
                    ->setTemplate('zeon/ajaxcart/catalog/product/view/addtocart.phtml');
            $productView->append($addToCart);

            $media = $layout->createBlock('catalog/product_view_media', 'media')
                    ->setTemplate('zeon/ajaxcart/catalog/product/view/media.phtml');
            $productView->append($media);

            $productView->append($js)->append($price)->append($calendar);
            $this->getResponse()->setBody($productView->renderView());
        }
        return false;
    }

}
