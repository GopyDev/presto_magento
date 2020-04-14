<?php
//dicount amount
class Webduo_Simpleupc_Model_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract {
 
    public function collect(Mage_Sales_Model_Quote_Address $address) {
            //deprecated
			/*if ($address->getData('address_type') == 'billing')
                return $this;
 
			$flexible_discount_amount = Mage::getStoreConfig('simpleupc/delivery_window/flexible_delivery_discount');
			$ordered_discount_amount = Mage::getStoreConfig('simpleupc/delivery_window/same_delivery_window_discount');
            $discount = 0;   
			   
			if((Mage::getSingleton('checkout/session')->getDeliveryType() == 'flexible_delivery') || (Mage::getSingleton('checkout/session')->getDeliveryType() == 'ordered_window')){
            	$grandTotal = $address->getGrandTotal();
            	$baseGrandTotal = $address->getBaseGrandTotal();
 	
 	           	$totals = array_sum($address->getAllTotalAmounts());
 	           	$baseTotals = array_sum($address->getAllBaseTotalAmounts());
 	
 	           	if(Mage::getSingleton('checkout/session')->getDeliveryType() == 'flexible_delivery'){
					$discount = $flexible_discount_amount;
				}elseif(Mage::getSingleton('checkout/session')->getDeliveryType() == 'ordered_window'){
					$discount = $ordered_discount_amount;
				}

				$address->setFeeAmount(-$discount);
           		$address->setBaseFeeAmount(-$discount);
 				
 	           	$address->setGrandTotal($grandTotal + $address->getFeeAmount());
 	           	$address->setBaseGrandTotal($baseGrandTotal + $address->getBaseFeeAmount());
				
				$address->save();
 			}
		return $this;*/
    }
 
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
         
            /*if ($address->getData('address_type') == 'billing')
                return $this;
 
            $amt = $address->getDiscountAmount();
            if ($amt != 0) {
                 
                 
                $address->addTotal(array(
                    'code' => 'Discount',
                    'title' => 'Discount',
                    'value' => $amt
                ));
            }
         
        return $address;*/
    }
 
}