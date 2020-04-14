<?php
class Hs_Customcartdiscount_Model_Observer {
    
    const XML_PATH_MODULE_ENABLE    = 'customcartdiscount/customcartdiscount_group/enable';
    const XML_PATH_DISCOUNT_AMOUNT  = 'customcartdiscount/customcartdiscount_group/discount';
	 const XML_PATH_MAX_WINDOW  = 'customcartdiscount/customcartdiscount_group/maxdiscount';
	
    
    public function setDiscount($observer) {
        if(Mage::getStoreConfig(self::XML_PATH_MODULE_ENABLE)){
            $quote = $observer->getEvent()->getQuote();
            $quoteid = $quote->getId();
            $discountAmount = Mage::getStoreConfig(self::XML_PATH_DISCOUNT_AMOUNT);
			$maxwindow = Mage::getStoreConfig(self::XML_PATH_MAX_WINDOW);
			//echo "<pre>";
			//echo "Delivery window is ".$_SESSION['delivery_window']; 
			//exit;
			/********************************
				Checking Delivery Window
				Dated: 7/1/2016
			/********************************/
			//echo "Shipping charge Over night : ".$_SESSION['ship_charge'];
			$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
			$cart = Mage::getSingleton('checkout/cart');
		$quote = $cart->getQuote();
		$shippingAddress = $quote->getShippingAddress();
		$zip = $shippingAddress->getPostcode();
		$qry = "SELECT address.*, ord.*, wind.*
	FROM mg_sales_flat_order_address as address,
	mg_sales_flat_order as ord,
	mg_shipping_delivery_window as wind
	WHERE
	address.postcode = '".$zip."'
	AND
	address.parent_id = ord.entity_id
	AND ord.status = 'processing'
	AND ord.increment_id = wind.order_number GROUP BY (wind.order_number)";
	$datas = $connection->fetchAll($qry);
	$data_array = array();
	foreach($datas as $data){
		//echo $data['window'];
		list($time,$date) = explode("|",$data['window']);
		$originalDate = str_replace("_","-",date($date));
		$today_date =  date("Y-m-d");
		$compare_Date = date("Y-m-d",$originalDate );
		//echo "Today date is ".$today_date;
		//echo "<br/> Original date is ".$originalDate;
		
		$data_array[] .= $data['window'];
	}
		/*echo "<pre>";
		print_r($data_array);
		exit;*/
		$tempArray = array();
		foreach($data_array as $data){
			if($data == $_SESSION['delivery_window']){
				list($time,$date) = explode("|",$data);
				$originalDate = str_replace("_","-",date($date));
				$today_date =  date("Y-m-d");
				if( Date('Y-m-d', strtotime($originalDate)) >= $today_date ){
					$tempArray[] .=$data;
					
					$match_delivery_discount = 1;				
				}
		  }
		  
		}
		//echo "Selected window is ".$_SESSION['delivery_window'];
		//echo "<pre>";
		//print_r(array_count_values($tempArray));
		//echo "Match_Delivery Discount is ".$match_delivery_discount;
		//exit;
		/*echo  "Selected window ". $_SESSION['delivery_window'];
		exit;*/
		/*$checkingCount = checkCount($data_array);
		echo "<pre>";
		print_r($checkingCount);*/
		//exit;
		if( !empty($tempArray) && ($tempArray[$_SESSION['delivery_window']] <=$maxwindow) ){
			$match_delivery_discount = 1;
		}
		if( isset($match_delivery_discount)) {
			/*********************************
					End
			/*********************************/
            if ($quoteid) {
                if ($discountAmount > 0) {
                    $total = $quote->getBaseSubtotal();
					
                    $quote->setSubtotal(0);
                    $quote->setBaseSubtotal(0);

                    $quote->setSubtotalWithDiscount(0);
                    $quote->setBaseSubtotalWithDiscount(0);

                    $quote->setGrandTotal(0);
                    $quote->setBaseGrandTotal(0);


                    $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                    foreach ($quote->getAllAddresses() as $address) {

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

						// Set grand total with additional delivery
						$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal() + $_SESSION['ship_charge']) ;
						$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal() + $_SESSION['ship_charge']);
						
                        $quote->save();
						// Set overnight delivery charges..
						
							$quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge'])
                                ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge']) 
                                ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge']) 
                                ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge']) 
                                ->save();
						
                        


                        if ($address->getAddressType() == $canAddItems) {
                            //echo $address->setDiscountAmount; exit;
                            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount + $_SESSION['ship_charge']);
                            $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount + $_SESSION['ship_charge']);
                            $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
                            $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount + $_SESSION['ship_charge']);
                            if ($address->getDiscountDescription()) {
                                $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
                                $address->setDiscountDescription($address->getDiscountDescription() . ', Delivery Discount');
                                $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                            } else {
                                $address->setDiscountAmount(-($discountAmount));
                                $address->setDiscountDescription('Delivery Discount');
                                $address->setBaseDiscountAmount(-($discountAmount));
                            }
                            $address->save();
                        }//end: if
                    } //end: foreach
                    //echo $quote->getGrandTotal();
					//exit;
                    foreach ($quote->getAllItems() as $item) {
                        //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
						//echo "Total is ".$total;
						//exit;
                        $rat = $item->getPriceInclTax() / $total;
                        $ratdisc = $discountAmount * $rat;
                        $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                        $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
                    }
                }
            }
		
		} // Match_Delivery condition..
		/*else{
				$discountAmount = 0;
				if ($quoteid) {
					
                if ($discountAmount == 0) {
                    $total = $quote->getBaseSubtotal();
					
                    $quote->setSubtotal(0);
                    $quote->setBaseSubtotal(0);

                    $quote->setSubtotalWithDiscount(0);
                    $quote->setBaseSubtotalWithDiscount(0);

                    $quote->setGrandTotal(0);
                    $quote->setBaseGrandTotal(0);


                    $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                    foreach ($quote->getAllAddresses() as $address) {

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

						// Set grand total with additional delivery
						$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal() + $_SESSION['ship_charge']) ;
						$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal() + $_SESSION['ship_charge']);
						
                        $quote->save();
						// Set overnight delivery charges..
						
							$quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge'])
                                ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge']) 
                                ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge']) 
                                ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount + $_SESSION['ship_charge']) 
                                ->save();
						
                        


                        if ($address->getAddressType() == $canAddItems) {
                            //echo $address->setDiscountAmount; exit;
                            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount + $_SESSION['ship_charge']);
                            $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount + $_SESSION['ship_charge']);
                            $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
                            $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount + $_SESSION['ship_charge']);
                            if ($address->getDiscountDescription()) {
                                $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
                                $address->setDiscountDescription($address->getDiscountDescription() . ', Delivery Discount');
                                $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                            } else {
                                $address->setDiscountAmount(-($discountAmount));
                                $address->setDiscountDescription('Delivery Discount');
                                $address->setBaseDiscountAmount(-($discountAmount));
                            }
                            $address->save();
                        }//end: if
                    } //end: foreach
                    //echo $quote->getGrandTotal();
					//exit;
                    foreach ($quote->getAllItems() as $item) {
                        //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
						//echo "Total is ".$total;
						//exit;
                        $rat = $item->getPriceInclTax() / $total;
                        $ratdisc = $discountAmount * $rat;
                        $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                        $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
                    }
                }
            }
			
		} // Close else condition
		*/
        }
    }

	
	
}
