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
class Zeon_AjaxCart_CartController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_ENABLED = 'zeon_ajaxcart/general/is_enabled';

    public function preDispatch()
    {
        parent::preDispatch();
        $response = array();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $response["message"] = 
            $this->__('Cannot add/delete product from shopping cart. Please Enable the AjaxCart extension.');
            return;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Initialize product instance from request data
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
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
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $response = array();

        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $message = $this->__('Cannot add the item to shopping cart.');
                $response["message"] = $message;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent(
                'checkout_cart_add_product_complete', 
                array(
                    'product' => $product, 
                    'request' => $this->getRequest(), 
                    'response' => $this->getResponse()
                )
            );


            if (!$cart->getQuote()->getHasError()) {
                $message = 
                $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                $response["message"] = $message;

                //Get Layout update content
                $layout = $this->getLayout();
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_in')
                            ->load();
                } else {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_out')
                            ->load();
                }
                $layout->generateXml()->generateBlocks();
                $header = $layout->getBlock('header')->toHtml();
                $cart = $layout->getBlock('cart_sidebar')->toHtml();
                if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                    $response["header"] = 
                    preg_replace(
                        "#<div class=\"nav-container\">(.*?)</div>#is", 
                        "", trim($header)
                    );
                } else {
                    $response["header"] = trim($header);
                }
                $response["cart"] = trim($cart);
                if ($blockType = Mage::helper('zeon_ajaxcart')->getRuleProductBlockType()) {
                    $response["additional"] = 
                    Mage::helper('zeon_ajaxcart')->getAdditionalProductBlock($blockType);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
            $response["message"] = $message;
        } catch (Exception $e) {
            $message = $this->__('Cannot add the item to shopping cart.');
            $response["message"] = $message;
            // log exception to exceptions log
            $error = sprintf('Exception message: %s%sTrace: %s', $e->getMessage(), "\n", $e->getTraceAsString());
            $file = Mage::getStoreConfig(self::XML_PATH_LOG_EXCEPTION_FILE);
            Mage::log($error, Zend_Log::DEBUG, $file);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction()
    {
        $response = array();

        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                $message = $this->__('Quote item is not found.');
                $response["message"] = $message;
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                $message = $item;
                $response["message"] = $message;
            }
            if ($item->getHasError()) {
                $message = $item->getMessage();
                $response["message"] = $message;
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent(
                'checkout_cart_update_item_complete', 
                array('item' => $item, 
                      'request' => $this->getRequest(), 
                      'response' => $this->getResponse()
                )
            );
            if (!$cart->getQuote()->getHasError()) {
                $message = 
                $this->__(
                    '%s was updated in your shopping cart.', 
                    Mage::helper('core')->htmlEscape($item->getProduct()->getName())
                );
                $response["message"] = $message;
                $response["item_id"] = $item->getId();

                //Get Layout update content
                $layout = $this->getLayout();
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_in')
                            ->load();
                } else {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_out')
                            ->load();
                }
                $layout->generateXml()->generateBlocks();
                $header = $layout->getBlock('header')->toHtml();
                if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                    $response["header"] = preg_replace(
                        "#<div class=\"nav-container\">(.*?)</div>#is", 
                        "", trim($header)
                    );
                } else {
                    $response["header"] = trim($header);
                }
            }
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
            $response["message"] = $message;
        } catch (Exception $e) {
            $message = $this->__('Cannot update the item.');
            $response["message"] = $message;
            Mage::logException($e);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
        $response = array();
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                        ->save();
                $message = $this->__('Item was removed from your shopping cart.');
                $response["message"] = $message;

                //Get Layout update content
                $layout = $this->getLayout();
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_in')
                            ->addHandle('checkout_cart_index')
                            ->load();
                } else {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_out')
                            ->addHandle('checkout_cart_index')
                            ->load();
                }
                $layout->generateXml()->generateBlocks();
                $header = $layout->getBlock('header')->toHtml();
                $content = $layout->getBlock('content')->toHtml();
                if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                    $response["header"] = 
                    preg_replace(
                        "#<div class=\"nav-container\">(.*?)</div>#is", 
                        "", trim($header)
                    );
                } else {
                    $response["header"] = trim($header);
                }
                $response["content"] = trim($content);
            } catch (Exception $e) {
                $message = $this->__('Cannot remove the item.');
                $response["message"] = $message;
                Mage::logException($e);
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Update shoping cart data action
     */
    public function updatePostAction()
    {
        $response = array();
        try {
            $cart = $this->_getCart();
            $cartData = $this->getRequest()->getParam('cart');
            $updateAction = (string) $this->getRequest()->getParam('clear_cart_action');
            if (isset($updateAction) && $updateAction == 'empty_cart') {

                $cart->truncate();
                $cart->save();
                $this->_getSession()->setCartWasUpdated(true);
                $message = $this->__('Shopping cart is empty.');
                $response["message"] = $message;
                //Get Layout update content
                $layout = $this->getLayout();
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_in')
                            ->addHandle('checkout_cart_index')
                            ->load();
                } else {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_out')
                            ->addHandle('checkout_cart_index')
                            ->load();
                }
                $layout->generateXml()->generateBlocks();
                $header = $layout->getBlock('header')->toHtml();
                $content = $layout->getBlock('content')->toHtml();
                if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                    $response["header"] = 
                    preg_replace(
                        "#<div class=\"nav-container\">(.*?)</div>#is", 
                        "", trim($header)
                    );
                } else {
                    $response["header"] = trim($header);
                }
                $response["content"] = trim($content);
            } else {
                if (is_array($cartData)) {
                    $filter = new Zend_Filter_LocalizedToNormalized(
                        array('locale' => Mage::app()->getLocale()->getLocaleCode())
                    );
                    foreach ($cartData as $index => $data) {
                        if (isset($data['qty'])) {
                            $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                        }
                    }
                    $cart = $this->_getCart();
                    if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                        $cart->getQuote()->setCustomerId(null);
                    }

                    $cartData = $cart->suggestItemsQty($cartData);
                    $cart->updateItems($cartData)
                            ->save();
                    $message = $this->__('Shopping cart was updated.');
                    $response["message"] = $message;

                    //Get Layout update content
                    $layout = $this->getLayout();
                    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                        $layout->getUpdate()
                                ->addHandle('default')
                                ->addHandle('customer_logged_in')
                                ->addHandle('checkout_cart_index')
                                ->load();
                    } else {
                        $layout->getUpdate()
                                ->addHandle('default')
                                ->addHandle('customer_logged_out')
                                ->addHandle('checkout_cart_index')
                                ->load();
                    }
                    $layout->generateXml()->generateBlocks();
                    $header = $layout->getBlock('header')->toHtml();
                    $content = $layout->getBlock('content')->toHtml();
                    if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                        $response["header"] = 
                        preg_replace(
                            "#<div class=\"nav-container\">(.*?)</div>#is", 
                            "", trim($header)
                        );
                    } else {
                        $response["header"] = trim($header);
                    }
                    $response["content"] = trim($content);
                }
            }
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
            $response["message"] = $message;
            Mage::logException($e);
        } catch (Exception $e) {
            $message = $this->__('Cannot update shopping cart.');
            $response["message"] = $message;
            Mage::logException($e);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}