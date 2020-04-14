<?php
//dicount amount
class Webduo_Simpleupc_Model_Observer{
 
    public function clear_discount_session(Varien_Event_Observer $obj){
    	Mage::getSingleton('checkout/session')->unsetData('delivery_type');
		Mage::getSingleton('checkout/session')->unsetData('delivery_amount');
		
		$order = $obj->getOrder();
		$quote_id = $order->getQuoteId();
		$table = Mage::getSingleton("core/resource")->getTableName('shipping_delivery_window');
		$check = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchRow('select * from '.$table.' where quote_id = "'.$quote_id.'"');

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
		//$discountAmount = 3;
		
		$quote = $observer->getEvent()->getQuote();
       	$quoteid = $quote->getId();
        
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
				foreach($quote->getAllAddresses() as $address){
				
								
				}
					if(($fdate == $today && $hour < 7.00) || ($fdate == $nextday && $hour >= 19.00) ){
					$fee = 12.95 - $address->getShippingAmount();
					$address->setGrandTotal((float)$address->getGrandTotal() + $fee);
					$address->setBaseGrandTotal((float)$address->getGrandTotal());
				
					}
				
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
    
	               $quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
    		           	->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
            		   	->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               			->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
               			->save(); 
               
	                if($address->getAddressType()==$canAddItems) {
        	            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
						$address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
                	    $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
                    	$address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
                    	if($address->getDiscountDescription()){
                    		$address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
                    		$address->setDiscountDescription($address->getDiscountDescription().', Delivery Discount');
                    		$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
                    	}else{
							
                    		$address->setDiscountAmount(-($discountAmount));
                   	 		$address->setDiscountDescription('Delivery Discount');
                    		$address->setBaseDiscountAmount(-($discountAmount));
							if(($fdate == $today && $hour < 7.00) || ($fdate == $nextday && $hour >= 19.00) ){
								$fees = 12.95 - $address->getShippingAmount();
								$address->setGrandTotal((float)$address->getGrandTotal() + $fees);	
								//$address->setBaseGrandTotal((float)$address->getGrandTotal());
								
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
