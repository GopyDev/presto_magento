<?php
//discount amount
class Webduo_Simpleupc_Model_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice){
        //deprecated
		/*if($invoice->getFeeAmount()){
			$invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getFeeAmount());
        	$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getBaseFeeAmount());
 		}*/
        return $this;
    }
}