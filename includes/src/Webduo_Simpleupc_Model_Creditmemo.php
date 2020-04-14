<?php
//discount amount
class Webduo_Simpleupc_Model_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo){
        //deprecated
		/*if($creditmemo->getFeeAmount()){
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getFeeAmount());
        	$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getBaseFeeAmount());
 		}*/
        return $this;
    }
}