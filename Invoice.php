<?php
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);
 
require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

//02/12/2015
//mm/dd/yy
	//$from = date('Y-m-d', strtotime($from));
date_default_timezone_set('America/New_York');
	$to_date = date('m/d/Y');
//echo date('m-d-y h:i:a');
	$to = date('m/d/Y', strtotime($to_date));
	$from = $to;
	$hour = date('H');
	//Get all orders which have delivery today.
//if($hour >= 07.00 && $hour <= 08.00){
	
	$read = Mage::getSingleton('core/resource')->getConnection('core_read');
	$result = $read->query("SELECT * FROM `mg_deliverydate_order` WHERE `delivery_date` = '$from' ");
	$row = $result->fetchAll(PDO::FETCH_ASSOC);
	foreach($row as $result1)
	{
		echo $order_id = $result1['order_id'];echo '<br />';
		$ordercol = Mage::getModel("sales/order")->load($order_id);
		if($ordercol->hasInvoices()){ 
		echo "Can't create invoice";	
		Mage::log('cant create invoice',null,'invoice_error.log');			
	}else{
		Mage::log($order_id,null,'createinvoice.log');
		//$order = Mage::getModel('sales/order')->getCollection();
		$orders = Mage::getModel('sales/order')->getCollection();
		$orders->addFieldToFilter("status", array("in" => array("pending","processing","Ordered")));
		$orders->addFieldToFilter("created_at", array("from" => $result1['delivery_date']));
		//$orders->addFieldToFilter("created_at", array("to" => date('Y-m-d', strtotime( "$to + 1 day" ))));
		foreach($orders as $order){
	
			$payment = $order->getPayment()->getMethodInstance()->getCode();
			Mage::log($payment,null,'payment.log');
				try {
					if(!$order->canInvoice())
					{
						echo Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
					}
				
				$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
				
				if (!$invoice->getTotalQty()) {
					echo Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
				}
				
				$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
				//Or you can use
				//$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
				$invoice->register();
				$transactionSave = Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder());
				
				$transactionSave->save();
					//echo $invoice->getid(); 
				}
				catch (Mage_Core_Exception $e) {
					//echo $e;
					Mage::log($e,null,'createinvoice_Exception.log');
				}
	}
	
	}
	//Create Invoice ********************************
	/**/
	//End Invoice ***********************************
	}
	
	
	//	echo $hour .'Is grater than 7.00';	

//}
	//else{
	//echo $hour .'Is less than 7.00';	
	//}
	
	
	
	
	
	
	
	
	
	
	//END


	
	/*$orders = Mage::getModel('sales/order')->getCollection();
	$orders->addFieldToFilter("status", array("in" => array("pending","processing","Ordered")));
	$orders->addFieldToFilter("created_at", array("from" => $from));
	$orders->addFieldToFilter("created_at", array("to" => date('Y-m-d', strtotime( "$to + 1 day" ))));
//echo '<pre>';	print_r(count($orders));
	foreach($orders as $order){
		echo $order->getIncrementId();echo '<br>';	
		$payment = $order->getPayment()->getMethodInstance()->getCode();
		try {
			if(!$order->canInvoice())
			{
				echo Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
			}
			 
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			 
			if (!$invoice->getTotalQty()) {
				echo Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));
			}
			 
			$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
			//Or you can use
			//$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
			$invoice->register();
			$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder());
			 
			$transactionSave->save();
			
			}
			catch (Mage_Core_Exception $e) {
			 
		}
//echo '<pre>';		print_r($payment);
	}*/
	/****************************/
	//Create Invoice programatically
	/****************************/
//	$paymentcode = $order->getPayment()->getMethodInstance()->getCode();
//	
//	if($paymentcode == 'cashondelivery'){
//		$invoiceId = Mage::getModel('sales/order_invoice_api')->create($orderIncrementId, array(),'',true,false);
//		$invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
//		//$invoice->sendEmail(true);
//		if($invoice->canCapture()){
//		$invoice->capture()->save();  
//		}
//	}



	/******************************/
	//End Create Invoice programatically
	/******************************/
$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		$headers .= 'From: Presto fresh grocery<no-reply@presto.com>' . "\r\n"; 
		"Reply-To: no-reply@blocblinds.com" . "\r\n" .
		"X-Mailer: PHP/" . phpversion();
		
		// Mail it
		$to = 'monika@xoomsolutions.com';
		$subject = 'Presto staging Invoice genrated';
		$message = 'Generated Your invocie';
		mail($to, $subject, $message, $headers);?>