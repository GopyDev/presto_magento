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

class Zeon_AjaxCart_Model_Wishlist extends Mage_Wishlist_Model_Wishlist
{
    /**
     * Shared store ids (website stores)
     *
     * @var int
     */
    protected $_lastAddedItemId = null;

    /**
     * Update wishlist Item and set data from request
     *
     * $params sets how current item configuration must be taken into account and additional options.
     * It's passed to Mage_Catalog_Helper_Product->addParamsToBuyRequest() to compose resulting buyRequest.
     *
     * Basically it can hold
     * - 'current_config', Varien_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] 
     * - prefix that was added at frontend to names of file options (file inputs), so they won't
     *   intersect with other submitted options
     *
     * For more options see Mage_Catalog_Helper_Product->addParamsToBuyRequest()
     *
     * @param int $itemId
     * @param Varien_Object $buyRequest
     * @param null|array|Varien_Object $params
     * @return Mage_Wishlist_Model_Wishlist
     *
     * @see Mage_Catalog_Helper_Product::addParamsToBuyRequest()
     */
    public function updateItem($itemId, $buyRequest, $params = null)
    {
        $item = $this->getItem((int)$itemId);
        if (!$item) {
            Mage::throwException(Mage::helper('wishlist')->__('Cannot specify wishlist item.'));
        }

        $product = $item->getProduct();
        $productId = $product->getId();
        if ($productId) {
            if (!$params) {
                $params = new Varien_Object();
            } else if (is_array($params)) {
                $params = new Varien_Object($params);
            }
            $params->setCurrentConfig($item->getBuyRequest());
            $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest($buyRequest, $params);

            $product->setWishlistStoreId($item->getStoreId());
            $items = $this->getItemCollection();
            $isForceSetQuantity = true;
            foreach ($items as $_item) {
                /* @var $_item Mage_Wishlist_Model_Item */
                if ($_item->getProductId() == $product->getId()
                    && $_item->representProduct($product)
                    && $_item->getId() != $item->getId()) {
                    // We do not add new wishlist item, but updating the existing one
                    $isForceSetQuantity = false;
                }
            }
            $resultItem = $this->addNewItem($product, $buyRequest, $isForceSetQuantity);
            /**
             * Error message
             */
            if (is_string($resultItem)) {
                Mage::throwException(Mage::helper('checkout')->__($resultItem));
            }

            if ($resultItem->getId() != $itemId) {
                if ($resultItem->getDescription() != $item->getDescription()) {
                    $resultItem->setDescription($item->getDescription())->save();
                }
                $item->isDeleted(true);
                $this->setDataChanges(true);
            } else {
                $resultItem->setQty($buyRequest->getQty() * 1);
                $resultItem->setOrigData('qty', 0);
            }
            //Set the last added item id
            $this->_lastAddedItemId = $resultItem->getId();
        } else {
            Mage::throwException(Mage::helper('checkout')->__('The product does not exist.'));
        }
        return $this;
    }
    
    /**
     * Retrieve last added item id
     *
     * @return int
     */
    function getLastAddedItemId()
    {
        return $this->_lastAddedItemId;
    }
}
