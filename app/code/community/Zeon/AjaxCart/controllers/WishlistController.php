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
class Zeon_AjaxCart_WishlistController extends Mage_Core_Controller_Front_Action
{

    const XML_PATH_ENABLED = 'zeon_ajaxcart/general/is_enabled';

    /**
     * If true, authentication in this controller (wishlist) could be skipped
     *
     * @var bool
     */
    protected $_skipAuthentication = false;

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * Filter to convert localized values to internal ones
     * @var Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter = null;

    /**
     * Processes localized qty (entered by user at frontend) into internal php format
     *
     * @param string $qty
     * @return float|int|null
     */
    protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter) {
            $this->_localFilter = 
                new Zend_Filter_LocalizedToNormalized(array('locale' => Mage::app()->getLocale()->getLocaleCode()));
        }
        $qty = $this->_localFilter->filter($qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $response = array();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $response["message"] = 
            $this->__('Cannot create wishlist. Please Enable the AjaxCart extension.');
            return;
        }

        if (!$this->_skipAuthentication && !Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->setFlag('', 'no-dispatch', true);
            $response["loginRequired"] = 1;
            $response["message"] = $this->__('Login required to create the wishlist.');
            ;
        }
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $response["message"] = $this->__('Cannot create wishlist.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Set skipping authentication in actions of this controller (wishlist)
     *
     * @return Mage_Wishlist_IndexController
     */
    public function skipAuthentication()
    {
        $this->_skipAuthentication = true;
        return $this;
    }

    /**
     * Retrieve wishlist object
     *
     * @return Mage_Wishlist_Model_Wishlist|bool
     */
    protected function _getWishlist()
    {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                    ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException(
                $e, 
                Mage::helper('wishlist')->__('Cannot create wishlist.')
            );
            return false;
        }

        return $wishlist;
    }

    /**
     * Adding new item
     */
    public function addAction()
    {
        $response = array();

        $session = Mage::getSingleton('customer/session');
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $response["message"] = $this->__('Cannot create wishlist.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $response["message"] = $this->__('Cannot add the item to wishlist.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $response["message"] = $this->__('Cannot specify product.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                $response["message"] = $result;
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product', array(
                'wishlist' => $wishlist,
                'product' => $product,
                'item' => $result
                    )
            );

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist.', $product->getName());
            $response["message"] = $message;

            //Get Layout update content
            $layout = $this->getLayout();
            $layout->getUpdate()
                    ->addHandle('default')
                    ->addHandle('customer_logged_in')
                    ->load();
            $layout->generateXml()->generateBlocks();
            $header= $layout->getBlock('header')->toHtml();
            $content = $layout->getBlock('content')->toHtml();
            if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                $response["header"] = 
                preg_replace(
                    "#<div class=\"nav-container\">(.*?)</div>#is", 
                    "", 
                    trim($header)
                );
            } else {
                $response["header"] = trim($header);
            }
            $response["content"] = trim($content);

            $sidebar = $this->getLayout()->createBlock('wishlist/customer_sidebar')
                            ->setTemplate('zeon/ajaxcart/wishlist/sidebar.phtml')->toHtml();

            $response["sidebar"] = 
            str_replace(
                '<div id="block-wishlist" class="block block-wishlist">', 
                '', 
                rtrim($sidebar, '</div>')
            );
            $response["block_id"] = 'block-wishlist';
        } catch (Mage_Core_Exception $e) {
            $message = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
            $response["message"] = $message;
        } catch (Exception $e) {
            $message = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
            $response["message"] = $message;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Action to accept new configuration for a wishlist item
     */
    public function updateItemOptionsAction()
    {
        $response = array();

        $session = Mage::getSingleton('customer/session');
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $response["message"] = $this->__('Cannot create wishlist.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $response["message"] = $this->__('Cannot add the item to wishlist.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $response["message"] = $this->__('Cannot specify product.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        try {
            $id = (int) $this->getRequest()->getParam('id');
            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)
                    ->save();

            Mage::helper('wishlist')->calculate();
            Mage::dispatchEvent(
                'wishlist_update_item', 
                array(
                    'wishlist' => $wishlist, 
                    'product' => $product, 
                    'item' => $wishlist->getItem($id)
                )
            );

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been updated in your wishlist.', $product->getName());
            $response["message"] = $message;
            $response["wishlist_item_id"] = $wishlist->getLastAddedItemId();

            //Get Layout update content
            $layout = $this->getLayout();
            $layout->getUpdate()
                    ->addHandle('default')
                    ->addHandle('customer_logged_in')
                    ->load();
            $layout->generateXml()->generateBlocks();
            $header = $layout->getBlock('header')->toHtml();
            $content = $layout->getBlock('content')->toHtml();
            if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                $response["header"] = 
                preg_replace(
                    "#<div class=\"nav-container\">(.*?)</div>#is", 
                    "", 
                    trim($header)
                );
            } else {
                $response["header"] = trim($header);
            }
            $response["content"] = trim($content);
        } catch (Mage_Core_Exception $e) {
            $message = $e->getMessage();
            $response["message"] = $message;
        } catch (Exception $e) {
            $message = $this->__('An error occurred while updating wishlist.');
            $response["message"] = $message;
            Mage::logException($e);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     *
     */
    public function cartAction()
    {
        $response = array();

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $response["message"] = $this->__('Cannot create wishlist.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('wishlist/item')->load($itemId);

        if (!$item->getId() || $item->getWishlistId() != $wishlist->getId()) {
            $response["message"] = $this->__('Cannot add item to shopping cart.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }

        // Set qty
        $qty = $this->getRequest()->getParam('qty');
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->_processLocalizedQty($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var $session Mage_Wishlist_Model_Session */
        $session = Mage::getSingleton('wishlist/session');
        $cart = Mage::getSingleton('checkout/cart');

        try {
            $options = Mage::getModel('wishlist/item_option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
                $this->getRequest()->getParams(), array('current_config' => $item->getBuyRequest())
            );

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            Mage::helper('wishlist')->calculate();

            $message = 
            $this->__(
                '%1$s has been added in your shopping cart.', 
                $item->getProduct()->getName()
            );
            $response["message"] = $message;

            //Get Layout update content
            $layout = $this->getLayout();
            $layout->getUpdate()
                    ->addHandle('default')
                    ->addHandle('customer_logged_in')
                    ->addHandle('wishlist_index_index')
                    ->load();
            $layout->generateXml()->generateBlocks();
            $header = $layout->getBlock('header')->toHtml();
            $content = $layout->getBlock('content')->toHtml();
            //$cart = $layout->getBlock('cart_sidebar')->toHtml();
            if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                $response["header"] = 
                preg_replace(
                    "#<div class=\"nav-container\">(.*?)</div>#is", 
                    "", 
                    trim($header)
                );
            } else {
                $response["header"] = trim($header);
            }
            $response["content"] = trim($content);
            //$response["cart"] = trim($cart);
            $sidebar = $this->getLayout()->createBlock('wishlist/customer_sidebar')
                            ->setTemplate('zeon/ajaxcart/wishlist/sidebar.phtml')->toHtml();

            $response["sidebar"] = 
            str_replace(
                '<div id="block-wishlist" class="block block-wishlist">', 
                '', 
                rtrim($sidebar, '</div>')
            );
            $response["block_id"] = 'block-wishlist';
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $message = Mage::helper('wishlist')->__('This product(s) is currently out of stock');
            } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $message = $e->getMessage();
            } else {
                $message = $e->getMessage();
            }
            $response["message"] = $message;
        } catch (Exception $e) {
            $message = $this->__('Cannot add item to shopping cart.');
            $response["message"] = $message;
            Mage::logException($e);
        }
        Mage::helper('wishlist')->calculate();
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Add all items from wishlist to shopping cart
     *
     */
    public function allcartAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $response["message"] = $this->__('Cannot create wishlist.');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
            return;
        }
        $isOwner = $wishlist->isOwner(Mage::getSingleton('customer/session')->getCustomerId());

        $messages = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        $cart = Mage::getSingleton('checkout/cart');
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();

        $qtys = $this->getRequest()->getParam('qty');
        foreach ($collection as $item) {
            /** @var Mage_Wishlist_Model_Item */
            try {
                $item->unsProduct();

                // Set qty
                if (isset($qtys[$item->getId()])) {
                    $qty = $this->_processLocalizedQty($qtys[$item->getId()]);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                }

                // Add to cart
                if ($item->addToCart($cart, $isOwner)) {
                    $addedItems[] = $item->getProduct();
                }
            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $notSalable[] = $item;
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $hasOptions[] = $item;
                } else {
                    $messages[] = 
                    $this->__('%s for "%s".', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $messages[] = Mage::helper('wishlist')->__('Cannot add the item to shopping cart.');
            }
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = 
            Mage::helper('wishlist')->__(
                'Unable to add the following product(s) to shopping cart: %s.', 
                join(', ', $products)
            );
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = 
            Mage::helper('wishlist')->__(
                'Product(s) %s have required options. Each of them can be added to cart separately only.', 
                join(', ', $products)
            );
        }

        if ($messages) {
            $isMessageSole = (count($messages) == 1);
            if ($isMessageSole && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                if ($isOwner) {
                    $item->delete();
                }
                $response["error"] = 
                Mage::helper('wishlist')->__('%s have required options.', $item->getProduct()->getName());
            } else {
                $response["error"] = implode("\n", $messages);
            }
        }

        if ($addedItems) {
            // save wishlist model for setting date of last update
            try {
                $wishlist->save();
            } catch (Exception $e) {
                $response["error"] = $this->__('Cannot update wishlist');
            }

            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            $response["message"] = 
            Mage::helper('wishlist')->__(
                '%d product(s) have been added to shopping cart: %s.', 
                count($addedItems), join(', ', $products)
            );
        }
        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();
        Mage::helper('wishlist')->calculate();

        //Get Layout update content
        $layout = $this->getLayout();
        $layout->getUpdate()
                ->addHandle('default')
                ->addHandle('customer_logged_in')
                ->addHandle('wishlist_index_index')
                ->load();
        $layout->generateXml()->generateBlocks();
        $header = $layout->getBlock('header')->toHtml();
        $content = $layout->getBlock('content')->toHtml();
        //$cart = $layout->getBlock('cart_sidebar')->toHtml();
        if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
            $response["header"] = 
            preg_replace(
                "#<div class=\"nav-container\">(.*?)</div>#is", 
                "", 
                trim($header)
            );
        } else {
            $response["header"] = trim($header);
        }
        $response["content"] = trim($content);
        //$response["cart"] = trim($cart);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Remove item
     */
    public function removeAction()
    {
        $response = array();
        $wishlist = $this->_getWishlist();
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);

        if ($item->getWishlistId() == $wishlist->getId()) {
            try {
                $item->delete();
                $wishlist->save();
                $message = $this->__('Item was removed from your wishlist.');
                $response["message"] = $message;

                //Get Layout update content
                $layout = $this->getLayout();
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_in')
                            ->addHandle('wishlist_index_index')
                            ->load();
                } else {
                    $layout->getUpdate()
                            ->addHandle('default')
                            ->addHandle('customer_logged_out')
                            ->addHandle('wishlist_index_index')
                            ->load();
                }
                $layout->generateXml()->generateBlocks();
                //$cartSideBar = $layout->getBlock('cart_sidebar')->toHtml();
                $content = $layout->getBlock('content')->toHtml();
                if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                $response["header"] = 
                preg_replace(
                    "#<div class=\"nav-container\">(.*?)</div>#is", 
                    "", 
                    trim($header)
                );
                } else {
                    $response["header"] = trim($header);
                }
                //$response["header"] = trim($cartSideBar);
                $response["content"] = trim($content);
            } catch (Mage_Core_Exception $e) {
                $message = 
                $this->__(
                    'An error occurred while deleting the item from wishlist: %s', 
                    $e->getMessage()
                );
                $response["message"] = $message;
                Mage::logException($e);
            } catch (Exception $e) {
                $message = $this->__('An error occurred while deleting the item from wishlist.');
                $response["message"] = $message;
                Mage::logException($e);
            }
        }

        Mage::helper('wishlist')->calculate();

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Update wishlist item comments
     */
    public function updateAction()
    {
        $response = array();
        if (!$this->_validateFormKey()) {
            return $this->_redirect('wishlist/index/');
        }
        $post = $this->getRequest()->getPost();
        if ($post && isset($post['description']) && is_array($post['description'])) {
            $wishlist = $this->_getWishlist();
            $updatedItems = 0;

            foreach ($post['description'] as $itemId => $description) {
                $item = Mage::getModel('wishlist/item')->load($itemId);
                if ($item->getWishlistId() != $wishlist->getId()) {
                    continue;
                }

                // Extract new values
                $description = (string) $description;
                if (!strlen($description)) {
                    $description = $item->getDescription();
                }

                $qty = null;
                if (isset($post['qty'][$itemId])) {
                    $qty = $this->_processLocalizedQty($post['qty'][$itemId]);
                }
                if (is_null($qty)) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $response["message"] = $this->__('Can\'t delete item from wishlist');
                    }
                }

                // Check that we need to save
                if (($item->getDescription() == $description) && ($item->getQty() == $qty)) {
                    continue;
                }
                try {
                    $item->setDescription($description)
                            ->setQty($qty)
                            ->save();
                    $updatedItems++;
                } catch (Exception $e) {
                    $response["message"] = 
                    $this->__('Can\'t save description %s', Mage::helper('core')->htmlEscape($description));
                    Mage::logException($e);
                }
            }

            // save wishlist model for setting date of last update
            if ($updatedItems) {
                try {
                    $wishlist->save();
                    Mage::helper('wishlist')->calculate();
                    $message = $this->__('Wishlist was updtaed successfully.');
                    $response["message"] = $message;

                    //Get Layout update content
                    $layout = $this->getLayout();
                    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                        $layout->getUpdate()
                                ->addHandle('default')
                                ->addHandle('customer_logged_in')
                                ->addHandle('wishlist_index_index')
                                ->load();
                    } else {
                        $layout->getUpdate()
                                ->addHandle('default')
                                ->addHandle('customer_logged_out')
                                ->addHandle('wishlist_index_index')
                                ->load();
                    }
                $layout->generateXml()->generateBlocks();
                $header = $layout->getBlock('header')->toHtml();
                $content = $layout->getBlock('content')->toHtml();
                if (!Mage::getConfig()->getNode('modules/Enterprise_PageCache/active')) {
                $response["header"] = 
                preg_replace(
                    "#<div class=\"nav-container\">(.*?)</div>#is", 
                    "", 
                    trim($header)
                );
                } else {
                    $response["header"] = trim($header);
                }
                $response["content"] = trim($content);
                } catch (Exception $e) {
                    $response["message"] = $this->__('Can\'t update wishlist');
                    Mage::logException($e);
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

}