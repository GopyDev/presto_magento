<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Checkout_OnepageController extends Mage_Checkout_Controller_Action
{
    protected $_sectionUpdateFunctions = array(
        'payment-method'  => '_getPaymentMethodsHtml',
        'shipping-method' => '_getShippingMethodsHtml',
        'review'          => '_getReviewHtml',
    );

    /** @var Mage_Sales_Model_Order */
    protected $_order;

    /**
     * @return Mage_Checkout_OnepageController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_preDispatchValidateCustomer();

        $checkoutSessionQuote = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkoutSessionQuote->getIsMultiShipping()) {
            $checkoutSessionQuote->setIsMultiShipping(false);
            $checkoutSessionQuote->removeAllAddresses();
        }

        if(!$this->_canShowForUnregisteredUsers()){
            $this->norouteAction();
            $this->setFlag('',self::FLAG_NO_DISPATCH,true);
            return;
        }

        return $this;
    }

    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->getOnepage()->getQuote()->hasItems()
            || $this->getOnepage()->getQuote()->getHasError()
            || $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Get shipping method step html
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_shippingmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
		return $output;
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    protected function _getAdditionalHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_additional');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        return $output;
    }

    /**
     * Get order review step html
     *
     * @return string
     */
    protected function _getReviewHtml()
    {
        return $this->getLayout()->getBlock('root')->toHtml();
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Checkout page
     */
   public function indexAction()
    { 
        if (!Mage::helper('checkout')->canOnepageCheckout()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $quote = $this->getOnepage()->getQuote();
					
		
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }
		//if(($quote->getGiftcertCode() == '' && $quote->getCouponCode() == '') || (!$quote->getCouponCode())  ){
			//if($quote->getGiftcertCode() == ''){
	$cart = Mage::getModel('checkout/cart')->getQuote();
	$nonNominalItems = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($quote);
	$quote = Mage::getSingleton('checkout/cart')->getQuote();
			$items = $quote->getAllVisibleItems();
			$typeid ='';
			foreach($items as $cartItem) {
					$typeid[] = $cartItem->getProductType(); 
					
			}
  if(!$nonNominalItems && $typeid[0] != 'ugiftcert'){ //Not validate minimum amount for giftcard and nominal product
//	if($quote->getGiftcertCode() == '' && $quote->getCouponCode() == ''){
		if($quote->getGiftcertCode() == '' ){
		
		$customerData = Mage::getSingleton('customer/session')->getCustomer();
							$customerId = $customerData->getId();
							$orders = Mage::getResourceModel("sales/order_collection")
                            ->addAttributeToSelect('customer_id')
                            ->addAttributeToSelect('customer_email')
                            ->addAttributeToSelect('increment_id');

                            ;
					$sql= "SELECT * FROM mg_aw_sarp2_profile WHERE customer_id = '".$customerData->getId()."' AND `status` LIKE 'active'";
					$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					$results = $connection->fetchAll($sql);
					
					
        if (!$quote->validateMinimumAmount() && $results == '' ) {
			
            $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error); 
            $this->_redirect('checkout/cart');
            return;
        }
		$customerData = Mage::getSingleton('customer/session')->getCustomer();
							//echo "Customer Id is ".$customerData->getId();
							$customerId = $customerData->getId();
							$orders = Mage::getResourceModel("sales/order_collection")
                            ->addAttributeToSelect('customer_id')
                            ->addAttributeToSelect('customer_email')
                            ->addAttributeToSelect('increment_id');

                            ;
					$sql= "SELECT * FROM mg_aw_sarp2_profile WHERE customer_id = '".$customerData->getId()."' AND `status` LIKE 'active'";
					$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					$results = $connection->fetchAll($sql);
					if($results){
						$jsonText = $results[0]['details'];
						$decodedText = html_entity_decode($jsonText);
						$orderArray = unserialize($decodedText);
						$itemID = $orderArray['subscription']['item']['subscription_type_id'];
						if($itemID == 1){
						//if($orderArray['order_item_info']['product_id'] == 25265){
						   //Basic 
							$basic = true;
						}
						if($itemID == 2){
						//if($orderArray['order_item_info']['product_id'] == 27592){
							// Premium Plus
							$premiumPlus = true;
						}
					}else{
							$noFreeShipping = true;
					}
					$subTotal = Mage::helper('checkout/cart')->getQuote()->getSubtotal();
					//echo "Sub total is ".$subTotal;
					//exit;
					if($basic == true){
						$minimumAmount = 50;
						if($subTotal < $minimumAmount){
						
							//return false;
							  $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error); 
            $this->_redirect('checkout/cart');
            return;
						}
					}
					else if($premiumPlus == true){
						$minimumAmount = 25;
						if($subTotal < $minimumAmount){
							//	return false;
							  $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
                Mage::getStoreConfig('sales/minimum_order/error_message') :
                Mage::helper('checkout')->__('Subtotal must exceed minimum order amount');

            Mage::getSingleton('checkout/session')->addError($error); 
            $this->_redirect('checkout/cart');
            return;
						}
					}
					else if($noFreeShipping == true){
							// Checking Group wise minimum order value
								$roleId = Mage::getSingleton('customer/session')->getCustomerGroupId();
								$role = Mage::getSingleton('customer/group')->load($roleId)->getData('customer_group_code');
								$role = strtolower($role);
								$newSql= "SELECT customer_orderlimit FROM mg_customer_group WHERE customer_group_id = '".$roleId."'";
								$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
								$NewResults = $connection->fetchAll($newSql);
								$GroupMinimumOrder = $NewResults[0]['customer_orderlimit'];
								//echo "Group minimum order is ".$GroupMinimumOrder;
								if($GroupMinimumOrder > 0){
									$minimumAmount = $GroupMinimumOrder;
								
								}else{
									//$minimumAmount = 50;
								}
								if($subTotal < $minimumAmount){
							//	return false;
							
							  $error = Mage::getStoreConfig('sales/minimum_order/error_message') ?
								Mage::getStoreConfig('sales/minimum_order/error_message') :
								Mage::helper('checkout')->__('Subtotal must exceed minimum order amount. Subtotal must be $'.$minimumAmount);
								Mage::getSingleton('checkout/session')->addError($error); 
								$this->_redirect('checkout/cart');
							return;
						}
					}
		
	} 
  }
		
		
		//}
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
        $this->getOnepage()->initCheckout();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));
        $this->renderLayout();
    }



    /**
     * Checkout status block
     */
    public function progressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Order success action
     */
    public function successAction()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }

    public function failureAction()
    {
        $lastQuoteId = $this->getOnepage()->getCheckout()->getLastQuoteId();
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();

        if (!$lastQuoteId || !$lastOrderId) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }


    public function getAdditionalAction()
    {
        $this->getResponse()->setBody($this->_getAdditionalHtml());
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = $this->getOnepage()->getAddress($addressId);

            if (Mage::getSingleton('customer/session')->getCustomer()->getId() == $address->getCustomerId()) {
                $this->getResponse()->setHeader('Content-type', 'application/x-json');
                $this->getResponse()->setBody($address->toJson());
            } else {
                $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            }
        }
    }

    /**
     * Save checkout method
     */
    public function saveMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            $result = $this->getOnepage()->saveCheckoutMethod($method);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
//            $postData = $this->getRequest()->getPost('billing', array());
//            $data = $this->_filterPostData($postData);
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            } 
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $result['goto_section'] = 'shipping_method';
                    $result['update_section'] = array(
                        'name' => 'shipping-method',
                        'html' => $this->_getShippingMethodsHtml()
                    );

                    $result['allow_sections'] = array('shipping');
					//$result['goto_section'] = 'shipping';
                    $result['duplicateBillingInfo'] = 'true';
					
                } else {
                    $result['goto_section'] = 'shipping'; 
					
                } 
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping address save action
     */
    public function saveShippingAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );
            }
		
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
         if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
			//print('<pre>');print_r($data);print('</pre>');
			$_SESSION['delivery_window'] = $_POST['delivery_window'];
			$_SESSION['flexible_shipping'] = $_POST['flexible_shipping'];
			// Save shippingcharge in Delivery window //

			//set shipping price programatically calculating by subtotal
			$cart = Mage::getModel('checkout/session')->getQuote();
			//$address = $cart->getQuote()->getShippingAddress();
			//$shipping_charge1 = $address->getShippingAmount();
			$quote=Mage::getModel('checkout/session')->getQuote(); 
			$a = $quote->getShippingAddress(); 
			$flatrate=$a['shipping_amount'];
		
			$_SESSION['flatrate'] = $flatrate;
			
			
			//
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
			$query2 = "select status from mg_aw_sarp2_profile where  `customer_id` LIKE '$customer_id' AND `status` LIKE 'active'";
			$result2 = $connection->query($query2);
			$row2 = $result2->fetch();
			//
			if($cart['subtotal'] >= 50 && $cart['subtotal'] < 75)
			{
				
				$shippingcharge = 9.95;
				
			}
			else if($cart['subtotal'] >= 75 && $cart['subtotal'] < 100)
			{
				
				$shippingcharge = 8.95;
			}
			else
			{	
				$shippingcharge = 7.95;
			}
			//
			
			$coupon = Mage::getModel('salesrule/rule')->load(14);
	  		$validcode = $coupon->getData('coupon_code');
			
			if(!$cart['coupon_code']){
			
				$nextday_charge = 12.95 - $shippingcharge ;
				
				}else{
					
				if($cart['coupon_code'] != 'Free2Brecks'){ // Free shipping 
						$nextday_charge = 12.95 - $shippingcharge ;
						 $_SESSION['ship_charge']; 	
					}else{
						$nextday_charge = 0.00 ;
						//$_SESSION[''] = $_SESSION['ship_charge']; 	
					}
				}

			//set shipping price programatically
			$timezone = date_default_timezone_set('America/New_York');
			$date = new DateTime();
			$today = date('d-m-Y');
			$nextday =  date('d-m-Y', strtotime($date .' +1 day'));
			$nextday = strtolower($nextday);   
			$date->setTimezone($timezone);
		   	$hour =  $date->format('H');
			if($_SESSION['flexible_shipping'] == 'yes')
			{
				$fdate = str_replace('_','-',$_SESSION['delivery_window']);		
				if(($fdate == $today && $hour < 7.00) || ($fdate == $nextday && $hour >= 19.00) ){
					if($row2['status'] == 'active'){
						unset($_SESSION['ship_charge']);
					}else{
						$_SESSION['ship_charge'] = $nextday_charge;
					}
				
				}else{
					unset($_SESSION['ship_charge']);
					
				}	
			}
				else 
			{
				$select_delivery_window = $_SESSION['delivery_window'];
				$sepratedate = explode('|',$select_delivery_window);
				$fdate = str_replace('_','-',$sepratedate[1]);
					if(($fdate == $today && $hour < 7.00) || ($fdate == $nextday && $hour >= 19.00) ){
						if($row2['status'] == 'active'){
							unset($_SESSION['ship_charge']);
						}else{
							$_SESSION['ship_charge'] = $nextday_charge;
						}
					}else{
								unset($_SESSION['ship_charge']);
								
					}
			}
			
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
				//For free shipping 
				
				if(Mage::getSingleton('customer/session')->isLoggedIn()){
				//				$customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
				$customerData = Mage::getSingleton('customer/session')->getCustomer();
				$customer_id = $customerData->getId();
				$customerData['group_id'];
				$groupdata = Mage::getModel('customer/group')->load($customerData['group_id']);
				}
				$is_shipping = $groupdata['is_shipping'] ;
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
				$productTable = 'mg_customer_subsciption';
				$query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
				$result = $connection->query($query);
				$row = $result->fetch();
				
				//For ARB Services
				$nonNominalItems = Mage::helper('aw_sarp2/quote')->getAllSubscriptionItemsFromQuote($quote);
				
				//End
			
				if($row2['status'] == 'active' || ($is_shipping == 0 && $is_shipping !='') || $row['active'] == 'Yes' || $nonNominalItems){
				Mage::log('yes',null,'yes.log');
				$method = 'freeshipping_freeshipping';
				$result = $this->getOnepage()->saveShippingMethod($method);
				}else{					Mage::log('yes',null,'no.log');			
				$method = 'tablerate_bestway';
				$result = $this->getOnepage()->saveShippingMethod($method);
				
				}
				//
                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
         	
			{//save shipping window
				$flexible_discount_amount = Mage::getStoreConfig('simpleupc/delivery_window/flexible_delivery_discount');
				
				$ordered_discount_amount = Mage::getStoreConfig('simpleupc/delivery_window/same_delivery_window_discount');
								
				if($this->getRequest()->getPost('flexible_shipping') == 'yes'){
					Mage::getSingleton('checkout/session')->setDeliveryType('flexible_delivery');
					Mage::getSingleton('checkout/session')->setDeliveryType('flexible_delivery');
					Mage::getSingleton('checkout/session')->setDeliveryWindow('delivery_window');
					Mage::getSingleton('checkout/session')->setDeliveryAmount($flexible_discount_amount);
					$this->getRequest()->setParam('discount_amount', $flexible_discount_amount);
				}elseif($this->getRequest()->getPost('ordered_window') == 'yes'){
					Mage::getSingleton('checkout/session')->setDeliveryType('ordered_window');
					Mage::getSingleton('checkout/session')->setDeliveryType('ordered_window');
					Mage::getSingleton('checkout/session')->setDeliveryAmount($ordered_discount_amount);
					$this->getRequest()->setParam('discount_amount', $ordered_discount_amount);
					$this->getRequest()->setParam('ordered_window', 'yes');
				}else{
					Mage::getSingleton('checkout/session')->unsetData('delivery_type');
					Mage::getSingleton('checkout/session')->unsetData('delivery_amount');
				}
				//$simpleups = new Webduo_Simpleupc_Helper_Data;
				//$simpleups->save_delivery_window($_post);
				//$quote_id = Mage::getSingleton('checkout/session')->getQuote()->getId();
			
				/***********************************************
					Additional Delivery Changes 0
					When Any Corporate group customer 
					is loggedin and Unchecked 
					Shipping Charges from Admin			
				
			/*********************************************/
			
			$roleId = Mage::getSingleton('customer/session')->getCustomerGroupId();
			$role = Mage::getSingleton('customer/group')->load($roleId)->getData('customer_group_code');
			$role = strtolower($role);
			$sql= "SELECT is_shipping FROM mg_customer_group WHERE customer_group_id  = '".$roleId."' AND is_shipping = 0";
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$Custresults = $connection->fetchAll($sql);
			//$CustValue = $results[0]['is_shipping'];
			/*echo "<pre>";
			print_r($results);
			exit;*/
			if($Custresults){
				unset($_SESSION['ship_charge']);
			}
			/****************************************
						END
			/****************************************/
				Mage::helper('simpleupc')->save_delivery_window($this->getRequest()->getParams());
			
				
				
			}
			
		    $this->getOnepage()->getQuote()->collectTotals()->save();
			
			
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			
       }
    } 

    /**
     * Save payment ajax action
     *
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

		//Custome code for test a cod code in payment method
		 $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

			// Coupancode
		$coupon = Mage::getModel('salesrule/rule')->load(10);
  		$validcode = $coupon->getData('coupon_code');
		//$data = $this->getRequest()->getPost('payment', false);
		if($data['method'] == 'cashondelivery'){
			
			if($_POST['answer'] == $validcode){
			try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }
            error_reporting(E_ALL);
            // set payment to quote
           
            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();

            if (empty($result['error']) && !$redirectUrl) {	
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
			
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
		}else{
			 $result['error'] = $this->__('Unable to set Payment Method please check Your COD Code .');
			}
		
		}else{
		//end 
		//Customer select option to save information 
		 	$result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);
		
			if($data['remember'] == 'yes'){
				$date = date('m/d/Y');
				$con  = mysql_connect('localhost','prestofr_stage','GiAZ8sc7gpJS');
				$sqlconnect = mysql_select_db('prestofr_stage',$con) or die(mysql_error()); 
					//Customer creditcard Information Save
						$customer_id = $data['customer_id'];
						$cc_type = $data['cc_type'];
						$cc_number = $data['cc_number'];
						$cc_exp_month = $data['cc_exp_month'];
						$cc_exp_year = $data['cc_exp_year'];
						$cc_cid = $data['cc_cid'];
						//Fetch customer customer information
							$query = mysql_query("select * from mg_save_creditcarddetail where customer_id = '$customer_id'") or die(mysql_error());
							$queryresult = mysql_fetch_array($query);
						//End
							if($queryresult>0){
								$query2 = "UPDATE `mg_save_creditcarddetail` SET cc_type='$cc_type', cc_number='$cc_number', cc_exp_month='$cc_exp_month', cc_exp_year='$cc_exp_year', cc_cid='$cc_cid', date='$date' where customer_id = '$customer_id'";
								$result1 = mysql_query($query2) or die(mysql_error());					
								}else{
								//if($customer_id != 0){
								$sql1 = "INSERT INTO `mg_save_creditcarddetail` (`id`, `customer_id`, `cc_type`, `cc_number`, `cc_exp_month`, `cc_exp_year`, `cc_cid`, `date`) VALUES ('','$customer_id','$cc_type','$cc_number','$cc_exp_month','$cc_exp_year','$cc_cid','$date')";
								$result2 = mysql_query($sql1) or die(mysql_error());
								}
								//}
					//Customer creditcard Information Save
					$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            
				}
		//Customer select option to save information End
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
			
            $result = $this->getOnepage()->savePayment($data); 

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
		}
		
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		
		
		//End 
			 
       /* try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

           //  set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                );
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));*/
    }

    /**
     * Get Order by quoteId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOnepage()->getQuote()->getId(), 'quote_id');
            if (!$this->_order->getId()) {
                throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Can not create invoice. Order was not found."));
            }
        }
        return $this->_order;
    }

    /**
     * Create invoice
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _initInvoice()
    {
        $items = array();
        foreach ($this->_getOrder()->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        /* @var $invoice Mage_Sales_Model_Service_Order */
        $invoice = Mage::getModel('sales/service_order', $this->_getOrder())->prepareInvoice($items);
        $invoice->setEmailSent(true)->register();

        Mage::register('current_invoice', $invoice);
        return $invoice;
    }

    /**
     * Create order action
     */
    public function saveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['success'] = false;
                    $result['error'] = true;
                    $result['error_messages'] = $this->__('Please agree to all the terms and conditions before placing the order.');
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getOnepage()->getQuote()->getPayment()->importData($data);
				
            }
      		$this->getOnepage()->saveOrder();
			//Pick oder detail with delivery date
			$quoteid = $this->getOnepage()->getQuote()->getData('entity_id');
			$orderid = $this->getOnepage()->getQuote()->getData('reserved_order_id');
			$deliverywindow = $_SESSION['delivery_window'];
			
			if($_SESSION['flexible_shipping'] == 'yes'){
				$fdate = str_replace('_','/',$_SESSION['delivery_window']);			
				list($dd,$mm,$yy) = explode("/", $fdate); 
				$yy = substr($yy,0,4);
				$delivery_date = ''.$mm.'/'.$dd.'/'.$yy;	
			
			}
			else{
				$select_delivery_window = $_SESSION['delivery_window'];
				$sepratedate = explode('|',$select_delivery_window);
				$fdate = str_replace('_','/',$sepratedate[1]);
				list($dd,$mm,$yy) = explode("/", $fdate); 
				$yy = substr($yy,0,4);
				$delivery_date = ''.$mm.'/'.$dd.'/'.$yy;
				
			}
			//Pick oder detail with delivery date
			//Insert Query for deliverydate by order report 
			$con  = mysql_connect('localhost','prestofr_stage','GiAZ8sc7gpJS');
			$sqlconnect = mysql_select_db('prestofr_stage',$con) or die(mysql_error()); 
			$sql2 = "INSERT INTO `mg_deliverydate_order` (`entity_id`, `order_id`, `delivery_date`) VALUES ('$quoteid','$orderid','$delivery_date')";
			$result3 = mysql_query($sql2) or die(mysql_error());
			
			//End
			
            $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();

            $result['success'] = true;
            $result['error']   = false;
        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            if( !empty($message) ) {
                $result['error_messages'] = $message;
            }
            $result['goto_section'] = 'payment';
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success'] = false;
            $result['error'] = true;
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getOnepage()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array( 
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
            $result['success']  = false;
            $result['error']    = true;

            $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
        }
        $this->getOnepage()->getQuote()->save();
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $result['redirect'] = $redirectUrl;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('dob'));
        return $data;
    }

    /**
     * Check can page show for unregistered users
     *
     * @return boolean
     */
    protected function _canShowForUnregisteredUsers()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn()
            || $this->getRequest()->getActionName() == 'index'
            || Mage::helper('checkout')->isAllowedGuestCheckout($this->getOnepage()->getQuote())
            || !Mage::helper('checkout')->isCustomerMustBeLogged();
    }
}