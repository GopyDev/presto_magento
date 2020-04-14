<?php

require_once 'Mage/Checkout/controllers/CartController.php';
class MD_Giftcards_CartController extends Mage_Checkout_CartController
{
    public function giftcardPostAction()
    {
        if (!Mage::helper('customer')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('To redeem your gift card or to use your gift card balance you need to be logged in.')
            );
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        $giftcardCode = trim((string) $this->getRequest()->getParam('giftcard_code'));
        $card = Mage::getModel('giftcards/giftcards')->load($giftcardCode, 'card_code');

        if ($card->getId() && $card->getCardStatus() == 2) {
            $card->activateCardForCustomer($customerId);
            $this->_getSession()->addSuccess(
                $this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($giftcardCode))
            );
            Mage::getSingleton('giftcards/session')->setActive('1');
        } else {
            $this->_getSession()->addError(
                $this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftcardCode))
            );
        }
        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->collectTotals()->save();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_goBack();
    }

    public function giftcardActiveAction()
    {
        if (!Mage::helper('customer')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('To redeem your gift card or to use your gift card balance you need to be logged in.')
            );
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }
        if ((string)$this->getRequest()->getParam('giftcard_use') == '1') {
            Mage::getSingleton('giftcards/session')->setActive('1');
        } else {
            Mage::getSingleton('giftcards/session')->setActive('0');
        }
        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->collectTotals()->save();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_goBack();
    }	
	public function adddonationAction()   {		
	$v = $this->getRequest()->getParam('v');		
	if($v){						
	$qty = $v;			$_sku = "DON-OTH-USD";			$product_model = Mage::getModel('catalog/product');			$productid = $product_model->loadByAttribute('sku',$_sku)->getId();			$my_product = $product_model->load($productid);			$qty_value = $qty;			$cart = Mage::getModel('checkout/cart');			$cart->init();			$cart->addProduct($my_product, array('qty' => $qty_value));			$cart->save();			Mage::getSingleton('checkout/session')->setCartWasUpdated(true);			
	$url = "http://americanchristianministries.org/index.php/checkout/cart/";
         Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}	}	
	public function checkIfCartHasDonation()    {			}
	public function getdonationamountAction(){
		$quote = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllVisibleItems();
        foreach ($cartItems as $item)
        {
			$sku = $item->getSku();
			if($sku == 'DON-OTH-USD'):
				$prod = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
				$qty = $item->getQty();
				$amount = $qty * $prod->getPrice();
				//echo Mage::helper('core')->currency($amount);
				//echo $amount;
			endif;
		}
		//$tot = ($quote->getSubtotal() - $amount) * (30/100);
		echo Mage::helper('core')->currency($amount);
	}
}
