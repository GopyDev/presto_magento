<?php
//dicount amount
class Webduo_Simpleupc_Model_Observer{
 
    public function clear_discount_session(Varien_Event_Observer $obj){
    	Mage::getSingleton('checkout/session')->unsetData('delivery_type');
		Mage::getSingleton('checkout/session')->unsetData('delivery_amount');
		
		$order = $obj->getOrder();
		$orderIncrementId = $order->getIncrementId();
		$quote_id = $order->getQuoteId();
		$table = Mage::getSingleton("core/resource")->getTableName('shipping_delivery_window');
		$check = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow('select * from '.$table.' where quote_id = "'.$quote_id.'"');
		/***********************************/
			//Create functionality for subscription
		/***********************************/
		
		$orderIncrementId = $order->getIncrementId();
		$orders = Mage::getModel("sales/order")->load($orderIncrementId, 'increment_id'); 
		$_items = $order->getItemsCollection();
		$customer_id     = $orders->getCustomerId();
		
		//exit;
 		$customer_name   = $orders->getCustomerName();
 		$customer_email  =  $orders->getCustomerEmail();
		$product_i = 0;
		foreach($_items as $p){ $product_i++;
			$productid = Mage::getModel('catalog/product')->load($p->getProductId());
			$product_type = $productid->getTypeId();
			
			$p = $p->getProductOptions();
	 		foreach($p['options'] as $option){
		
		 $label = $option['label'];
		 $value = $option['value'];
		 if($label == 'Start date'){
			 $startdate = $option['label'];
			 $startdatevalue = $option['value'];
			 $startdatevalue = date("m/d/y",strtotime($startdatevalue));

         }
		 
		 if($label == 'Expiry Date'){

			 $expirydate = $option['value'];
			$expirydatevalue = $option['value'];
		 	$expirydatevalue = date("m/d/y",strtotime($expirydatevalue));
         }
		 if($label == 'Year'){
			 
		 	$year = $option['value'];
			$yearvalue = $option['value'];
		}
		
			}
      
	  if($product_type == 'customproduct'){
			//Insert 
				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
				$connection->beginTransaction();
				$__fields = array();
				$__fields['customer_id'] = $customer_id;
				$__fields['customer_name'] = $customer_name;
				$__fields['customer_emailid'] = $customer_email;
				$__fields['start_date'] = $startdatevalue;
				$__fields['expiry_date'] = $expirydatevalue;
				$__fields['duration'] = $yearvalue;
				$__fields['amount'] = '89.00';
				$__fields['active'] = 'Yes';
				$connection->insert('mg_customer_subsciption', $__fields);
				$connection->commit();
		   //end
			}

	    }
			
		//
		/************* End *****************/
		
		/****************************/
			//Create Invoice programatically
		/****************************/
		$paymentcode = $order->getPayment()->getMethodInstance()->getCode();
		
		if($paymentcode == 'cashondelivery'){
			$invoiceId = Mage::getModel('sales/order_invoice_api')->create($orderIncrementId, array(),'',true,false);
			
			$invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
			//$invoice->sendEmail(true);
			if($invoice->canCapture()){
			   $invoice->capture()->save();  
			}
		}
		  /******************************/
		  //End Create Invoice programatically
		  /******************************/
		  
		/************************************	Add delivery date filed after place order in invoice and order table */
		// Get quote id 
		
		$resource = Mage::getSingleton('core/resource');
		$write = $resource->getConnection('core_write');
		$read = $resource->getConnection('core_read');
		$shippingwindow = $resource->getTableName('shipping_delivery_window');
		$query3 = "select window from  mg_shipping_delivery_window  WHERE quote_id LIKE ".$quote_id."";
     	$fetch = $read->fetchOne($query3);
$pos = strpos($fetch, '|');
		if ($pos === false) {
				list($dd,$mm,$yy) = explode('_',$fetch);
				$fetch = $dd.'_'.$mm.'_'.$yy;
			//echo "The string '$findme' was not found in the string '$mystring'";
		} else {
				list($d_time,$d_date) = explode('|',$fetch);
				list($dd,$mm,$yy) = explode('_',$d_date);
				$fetch = $dd.'_'.$mm.'_'.$yy ;
			//echo "The string '$findme' was found in the string '$mystring'";
			//echo " and exists at position $pos";
		}
Mage::log($order->getEntityId(),null,'order_entity.log');
		Mage::log($order->getId(),null,'order_id.log');
		
		
		$invoicetable = $resource->getTableName('sales_flat_invoice');
		$salesorder = $resource->getTableName('sales_flat_order');
		$query = "UPDATE {$invoicetable} SET delivery_date = '{$fetch}' WHERE order_id LIKE ".$order->getEntityId()."";
     	$write->query($query);
		$query2 = "UPDATE {$salesorder} SET delivery_date = '{$fetch}' WHERE quote_id LIKE ".$quote_id."";
     	$write->query($query2);
		
	
			Mage::log($query2,null,'query22.log');
	
		/************************************ End update delivery date*/
		
		
		if($check){
			try{
				$data['order_number'] = $order->getIncrementId();

				Mage::getSingleton('core/resource')->getConnection('core_write')->update($table, $data, ' quote_id = "'.$quote_id.'"');
			} catch (Exception $e) {
				Mage::logException($e);
        		$result['error'] = $this->__('Unable to update Delivery Window.');
			}
		}	
		return $order;
    }
	
	public function setdiscountamount($observer){
  		
		$flexible_discount_amount = Mage::getStoreConfig('simpleupc/delivery_window/flexible_delivery_discount');
		$ordered_discount_amount = Mage::getStoreConfig('simpleupc/delivery_window/same_delivery_window_discount');
        $discountAmount = 0;   
			   
		if((Mage::getSingleton('checkout/session')->getDeliveryType() == 'flexible_delivery') || (Mage::getSingleton('checkout/session')->getDeliveryType() == 'ordered_window')){
           	
			if(Mage::getSingleton('checkout/session')->getDeliveryType() == 'flexible_delivery'){
				$discountAmount = $flexible_discount_amount;
				
			}elseif(Mage::getSingleton('checkout/session')->getDeliveryType() == 'ordered_window'){
				$discountAmount = $ordered_discount_amount;
							
			}
		}
		
		
		$quote = $observer->getEvent()->getQuote();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$query2 = "select status from mg_aw_sarp2_profile where  `customer_id` LIKE '$customer_id' AND `status` LIKE 'active'";
		$result2 = $connection->query($query2);
		$row2 = $result2->fetch();
       	$quoteid = $quote->getId();
        $cart = Mage::getModel('checkout/session')->getQuote();	
	
		//$discountAmount=10;
		//-----------------------------------------------------  
			$timezone = date_default_timezone_set('America/New_York');
			$date = new DateTime();
			$today = date('d-m-Y'); 
			$nextday =  date('d-m-Y', strtotime($date .' +1 day'));
			$nextday = strtolower($nextday); 
			$date->setTimezone($timezone);
		   	$hour =  $date->format('H');
			$_SESSION['delivery_window'];
			$_SESSION['flexible_shipping'];
			if($_SESSION['flexible_shipping'] == 'yes'){
				$fdate = str_replace('_','-',$_SESSION['delivery_window']);		
				
			}
				else 
			{
				$select_delivery_window = $_SESSION['delivery_window'];
				$sepratedate = explode('|',$select_delivery_window);
				$fdate = str_replace('_','-',$sepratedate[1]);
					if(($fdate == $today && $hour < 07.00) || ($fdate == $nextday && $hour >= 19.00) ){
					
					$cart = Mage::getModel('checkout/session')->getQuote();	
					
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
					//Start Add from xoom 25-11-2015
					$roleId_cust = Mage::getSingleton('customer/session')->getCustomerGroupId();
					$role_cust = Mage::getSingleton('customer/group')->load($roleId_cust)->getData('customer_group_code');
					$role_cust = strtolower($role_cust);
					$sql_cust= "SELECT is_shipping FROM mg_customer_group WHERE customer_group_id  = '".$roleId_cust."' ";
					$connection_cust = Mage::getSingleton('core/resource')->getConnection('core_read');
					$Custresults_cust = $connection_cust->fetchAll($sql_cust);
					$CustValue_cust = $Custresults_cust[0]['is_shipping'];
					
					if($CustValue_cust == 1 ) // apply if condition from xoom 25-11-2015 // 
					{
						// End add from xoom 25-11-2015
				
						if(!$cart['coupon_code']){
					
							$fee = 12.95 - $shippingcharge;
						
						}else{
						if($cart['coupon_code'] != 'Contest2014' || $cart['coupon_code'] != 'parma14'){
								$fee = 12.95 - $shippingcharge ;
								$_SESSION['ship_charge']; 	
						}else{
								$fee = 0.00 ;
								$_SESSION[''] = $_SESSION['ship_charge']; 	
						}
						}
						//
					} 
					

					foreach($quote->getAllAddresses() as $address){
						$variable = $address->getGrandTotal();
						$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
						$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
					 	$productTable = 'mg_customer_subsciption';
					 	$query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
						$result = $connection->query($query);
					 	$row = $result->fetch();
						if($row['active'] != 'Yes' &&   $row2['status'] != 'active'){
							$address->setGrandTotal($address->getGrandTotal() + $fee);
							
						}else{
								$address->setGrandTotal($address->getGrandTotal());
						}
					$address->setBaseGrandTotal((float)$address->getGrandTotal());
					$address->setTotalDue((float)$address->getGrandTotal());
					$address->save();	
							
					}	}

						
			}
				
				
				
     //----------------------------------------------------- 
		if($quoteid){
        	if($discountAmount>0){
        		
				$total = $quote->getBaseSubtotal();
            	$quote->setSubtotal(0);
            	$quote->setBaseSubtotal(0);

            	$quote->setSubtotalWithDiscount(0);
            	$quote->setBaseSubtotalWithDiscount(0);

            	$quote->setGrandTotal(0);
            	$quote->setBaseGrandTotal(0);
        
             
            	$canAddItems = $quote->isVirtual()? ('billing') : ('shipping');    
            	foreach($quote->getAllAddresses() as $address){
					$shipamount = $address->getShippingAmount();
            		$address->setSubtotal(0);
            		$address->setBaseSubtotal(0);

            		$address->setGrandTotal(0);
            		$address->setBaseGrandTotal(0);

            		$address->collectTotals();

            		$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
            		$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

            		$quote->setSubtotalWithDiscount(
                		(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
            		);
            		$quote->setBaseSubtotalWithDiscount(
                		(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
            		);

            		$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
            		$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
            		$quote->save(); 
    
	               $quote//->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
    		           	//->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
            		  	->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               			->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               			->save(); 

           			    $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
						$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
					 	$productTable = 'mg_customer_subsciption';
					 	$query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
						$result = $connection->query($query);
					 	$row = $result->fetch();
						
	                if($address->getAddressType()==$canAddItems) {
					 //Subscription discount 
						if($row['active'] == 'Yes' || $shipamount == 0){

						$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount());
						$address->setGrandTotal((float) $address->getGrandTotal());
                	    $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount());
                    	$address->setBaseGrandTotal((float) $address->getBaseGrandTotal());
						}else{
												
        	            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
						$address->setGrandTotal(((float) $address->getGrandTotal()-$discountAmount) );
                	    $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
                    	$address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
						}
						//Add by xoom Free shipping for group customer
						$customer_id =  $quote['customer_id'];
						$customer = Mage::getModel('customer/customer')->load($customer_id);
						$customer_code = $customer['group_id'];//$customer->getCustomerGroupId();
						$group_data = Mage::getModel('customer/group')->load($customer_code);
						//Mage::log($group_data['is_shipping'],null,'isshiping.log');

						if(($group_data['is_shipping'] == '0'  && $group_data['choose_delivery_address']  == '1' && $group_data['is_shipping'] != '' ) ||  $shipamount == 0){
							// 
							if($shipamount == 0){
								$address->setGrandTotal((float)$address->getGrandTotal());	
								$address->setBaseGrandTotal((float)$address->getGrandTotal());	
							}else{
								$address->setGrandTotal((float)$address->getGrandTotal()+$discountAmount);	
								$address->setBaseGrandTotal((float)$address->getGrandTotal());
							}
						} else{						
                    		if($address->getDiscountDescription()){
								if($row['active'] != 'Yes' || $shipamount != 0){						
								$address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
								$address->setDiscountDescription($address->getDiscountDescription().', Delivery Discount');
								$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
								if(($fdate == $today && $hour < 7.00) || ($fdate == $nextday && $hour >= 19.00) ){
								
								$coupon = Mage::getModel('salesrule/rule')->load(14);
								$validcode = $coupon->getData('coupon_code');
								if($shipamount == '0'){
										$fees = 0;
								}else{
										$fees = $_SESSION['ship_charge'];	
								}
								/*if(!$cart['coupon_code']){
								
								}else{
									if($cart['coupon_code'] != 'Contest2014' || $cart['coupon_code'] != 'parma14'){
										$fees = 12.95 - $shippingcharge ;
									}else{
										$fees = 0.00;
										$_SESSION[''] = $_SESSION['ship_charge']; 	
									}
								}*/
								//
								$address->setGrandTotal((float)$address->getGrandTotal() + $fees);	
								$address->setBaseGrandTotal((float)$address->getGrandTotal());
								$address->setTotalDue((float)$address->getGrandTotal());
							}
							}
                    	}else{ 
							if($row['active'] != 'Yes' || $shipamount != 0){	
					
                    		$address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
                   	 		$address->setDiscountDescription($address->getDiscountDescription().', Delivery Discount');
                    		$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
												
							if(($fdate == $today && $hour < 07.00) || ($fdate == $nextday && $hour >= 19.00) ){
								//
								$coupon = Mage::getModel('salesrule/rule')->load(14);
								$validcode = $coupon->getData('coupon_code');
							
								//if(!$cart['coupon_code']){
									if($shipamount == '0'){
										$fees = 0;
										
										}else{
										$fees = $_SESSION['ship_charge'];	
										}
								//}
								/*else{
									if($cart['coupon_code'] != 'Contest2014' || $cart['coupon_code'] != 'parma14'){

										$fees = 12.95 - $shippingcharge ;
									}else{
										$fees = 0.00;
										$_SESSION[''] = $_SESSION['ship_charge']; 	
											
									}
								}*/
								//
								
								$address->setGrandTotal((float)$address->getGrandTotal() + $fees);	
								$address->setBaseGrandTotal((float)$address->getGrandTotal());
								$address->setTotalDue((float)$address->getGrandTotal());
								
							} 
							}
                    	}
						}
                    	$address->save();
						
                	}//end: if
            	} //end: foreach
            	//echo $quote->getGrandTotal();
        
        		foreach($quote->getAllItems() as $item){
            	    //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                	//$rat=$item->getPriceInclTax()/$total;
                 //	$ratdisc=$discountAmount*$rat;
                 //	$item->setDiscountAmount(($item->getDiscountAmount()+$ratdisc) * $item->getQty());
                 //	$item->setBaseDiscountAmount(($item->getBaseDiscountAmount()+$ratdisc) * $item->getQty())->save();
               }
		   }
		}
		/*$session = Mage::getSingleton('checkout/session');
		if(Mage::helper('customer')->isLoggedIn()) {
  			$customer = Mage::getSingleton('customer/session')->getCustomer();
  			$customer_group = Mage::getModel('customer/group')->load(Mage::getSingleton('customer/session')->getCustomerGroupId())->getCustomerGroupCode();
  
  			$quote = $observer->getEvent()->getQuote();
  			$isvirtual=0;
    		
			foreach($quote->getAllItems() as $item){
     			if($item->getIsVirtual() == 1){
     				$isvirtual = 1;
     			}
     			if(Mage::getModel('catalog/product')->load($item->getProductId())->getTypeId()=='customproduct'){
     				$isvirtual=1;
     			}
    		}
       
   			if($discountAmount > 0){
			   $quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
			   		->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
					->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
					->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
					->save();
   
   				$canAddItems = $quote->isVirtual()? ('billing') : ('shipping'); 
   				foreach ($quote->getAllAddresses() as $address) {
    				$address->setSubtotal(0);
    				$address->setBaseSubtotal(0);
 
    				$address->setGrandTotal(0);
    				$address->setBaseGrandTotal(0);
 
    				$address->collectTotals();
    
    				if($address->getAddressType()==$canAddItems) {
 
     					$address->setSubtotal((float) $quote->getSubtotal());
     					$address->setBaseSubtotal((float) $quote->getBaseSubtotal());
     					$address->setSubtotalWithDiscount((float) $quote->getSubtotalWithDiscount());
     					$address->setBaseSubtotalWithDiscount((float) $quote->getBaseSubtotalWithDiscount());
     					$address->setGrandTotal((float) $quote->getGrandTotal());
     					$address->setBaseGrandTotal((float) $quote->getBaseGrandTotal());
     					$address->setDiscountAmount(-$discountAmount);
     					$address->setDiscountDescription('Discount');
     					$address->setBaseDiscountAmount(-$discountAmount);
     					$address->save();
    				}//end: if
   				} //end: foreach
    
				foreach($quote->getAllItems() as $item){
     
     				if($item->getBaseRowTotal() < $discountAmount){
     					$item->setDiscountAmount($item->getBaseRowTotal());
						
					    $item->setBaseDiscountAmount($item->getBaseRowTotal())->save();
     					$discountAmount=$discountAmount-$item->getBaseRowTotal();
     				}else{
     	 				$item->setDiscountAmount($discountAmount);
      					$item->setBaseDiscountAmount($discountAmount)->save();
      					break;
    				}    
   				} //end: foreach
   			}
	 	}*/
	}
}
