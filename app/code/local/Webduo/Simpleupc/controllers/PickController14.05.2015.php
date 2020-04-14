<?php
class Webduo_Simpleupc_PickController extends  Mage_Adminhtml_Controller_Action {
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}
	
	public function at_table(){
		return Mage::getSingleton("core/resource")->getTableName('eav_attribute_option_value');
	}
	
	public function get_store_id(){
		return $store_id = Mage::app()->getStore()->getStoreId();
	}
	
	public function indexAction(){
		$this->loadLayout();
		
		$this->_setActiveMenu('simpleupc_menu/item_pick_report');  
		
		//prepare template
		$block = $this->getLayout()->createBlock('core/template');
        
		//pass data to template
		$block->setData('back', $this->getUrl('simpleupc/index/index'));
		$block->setData('posturl', $this->getUrl('simpleupc/pick/report'));
		
		$block->setTemplate('simple_upc/report.phtml');
		
		$this->getLayout()->getBlock('content')->append($block);	
		
        $this->renderLayout();
	
		//generate url with kay and args
		//$this->getUrl('simpleupc/index/index', array('id'=>27)).
	}
	
	public function reportAction(){
		
		$from = $this->getRequest()->getParam('from');
		$to = $this->getRequest()->getParam('to');
		if((!$from || $from=='') || (!$to || $to=='')){
			return false;
		}
		//	
		$report_date = $this->getRequest()->getParam('report_date');
		if($report_date == '')
		{
			$session = $this->_getSession();
			$session->setEscapeMessages(true); 
			$session->addError($this->__('Please select Your Date Format')); 
     		//$this->_redirectError(Mage::getUrl('*/*/', array('_secure' => true)));
			header('location: http://www.prestofreshgrocery.com/simpleupc/pick/index/key/4daad2137a1bc2c5ad3eaccb4f3d8b54');
		
		}else{
		if($report_date == "delivery_date"){
		$from = date('m/d/Y', strtotime($from));
		$to = date('m/d/Y', strtotime($to));
		 
		$orders = Mage::getModel('sales/order')->getCollection(); 
		$orders->addFieldToFilter("status", array("in" => array("pending","processing")));
		
		// Filter By Delivery date 
		//connection and query //
		$r = Mage::getSingleton('core/resource')->getConnection('core_read');
		$result = $r->query("SELECT * FROM `mg_deliverydate_order` WHERE `delivery_date` BETWEEN '$from' AND '$to'");
		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		 $list = array();
			$csv_headers = array("Order Number", "Item UPC Code", "Item Name", "Size" , "UOM" , "Vendor ID", "Ordered Qty", "List Price", "Delivery Date", "Delivery Time " ,"Is Flexible Shipping", "Is Unattended Shipping", "Customer Group Name","Purchase Customer Name", "Delivery Customer Name",  'Customer Contact Number', 'Customer Billing Street Address', 'Customer Billing City','Customer Billing  State', 'Customer Billing Zipcode','Customer Delivery Address','Delivery City ','Delivery State','Delivery Zipcode', 'Accept Substitute');
		
		foreach($row as $result1)
		// while($result1 = mysql_fetch_array($result))
		 {
						

			$order_id = $result1['order_id'];
			$order = Mage::getModel("sales/order")->load($order_id);
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		 	$result=$write->query("SELECT * FROM `mg_sales_flat_order` WHERE `increment_id` = '$order_id' ");
			$row = $result->fetch();
			$orders =  Mage::getModel('sales/order')->loadByIncrementId($order_id);
			$orderItems = $orders->getItemsCollection()->addAttributeToSelect('*')->addAttributeToFilter('order_id',$order_id)->load();
			$deliverywindow=$write->query("SELECT * FROM `mg_shipping_delivery_window` WHERE `order_number` = '$order_id' ");
			$rows = $deliverywindow->fetch();
			//display customer Information 
			$customer =  Mage::getModel("customer/customer")->load($orders->getCustomerId());
			$billing = $orders->getBillingAddress();
			$shipping = $orders->getShippingAddress();
			$customerGroupId = $customer->getData('group_id');
			$groupname = Mage::getModel('customer/group')->load($customerGroupId)->getCustomerGroupCode();
			
			foreach($orderItems as $sItem) {
				$list['Order Number'] 	= $order_id;
					$list['Item UPC Code'] 	= $sItem->getSku();
					$list['Item Name']		= $sItem->getName();
					$list['Size'] 			= $sItem->getProduct()->getSize();
					$list['UOM'] 			= $sItem->getProduct()->getUnits();
					//$list['Supplier'] 		= $sItem->getProduct()->getSupplier();
					$list['Vendor ID']		= $sItem->getProduct()->getVendorProductId();
					
					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($sItem->getProduct());
				
   					$max_available_quantity = (int)$stock->getQty(); 
					//$list['Qty in Stock'] 	= $max_available_quantity;
					$list['Ordered Qty']	= $sItem->getQtyOrdered();
					$list['List Price'] 	= $sItem->getPrice();
					$list['Delivery Date'] 	= $result1['delivery_date'];
					$d_time 				= $rows['window'];
					if(strstr($d_time, '|')){
						$time_date = explode('|', $d_time);
						$delivery_time = explode('_', $time_date[0]);
						$delivery_time = implode(' ', $delivery_time);
						$delivery_date = $time_date[1];
						$list['Delivery Time'] = $delivery_time;
					}else{
						$delivery_time = '';
						$delivery_date = $d_time;
						
						$list['Delivery Time'] = ' - ';
					} 
						
					
					$is_flexible 			= $rows['flexible_shipping'];
					$is_unattended 			= $rows['unattended_shipping'];
					
					$list['Is Flexible Shipping'] = $is_flexible;
					$list['Is Unattended Shipping'] = $is_unattended;
					//print('<pre>');print_r($billing->getData());
					
					//echo '**********************';
					//	print('<pre>');print_r($shipping->getData());
					
					//exit;
					Mage::log($billing->getData(),null,'billing.log');
					Mage::log(print_r($customer->getData(),true),null,'customerdata.log');
					$list['Customer Group Name']	= $groupname;
					$list['Purchase Customer Name'] = ''.iconv("UTF-8","ASCII//TRANSLIT",$billing->getData('firstname')) . ' ' . iconv("UTF-8","ASCII//TRANSLIT",$billing->getData('lastname')).'';
					$list['Delivery Customer Name'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping['firstname']) . ' ' . iconv("UTF-8","ASCII//TRANSLIT",$shipping['lastname']).'';
					$list['Customer Contact Number'] = $billing->getTelephone();
					$list['Customer Billing Street Address'] = iconv("UTF-8","ASCII//TRANSLIT",$billing->getStreet1());
					$list['Customer Billing City & State']	= iconv("UTF-8","ASCII//TRANSLIT",$billing->getCity());
					$list['Customer Billing  State']	 = iconv("UTF-8","ASCII//TRANSLIT",$billing->getRegion());
					$list['Customer Billing Zipcode']	= $billing->getPostcode();
					$list['Delivery Address'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping['street']);
					$list['Delivery City'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping['city']) ;
					$list['Delivery State'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping['region']);
					$list['Delivery Zipcode'] = ' '.$shipping['postcode'];	
													
				//	$list['Customer Billing Address'] = ''.$billing->getStreet1().', '.$billing->getRegion().' - '.$billing->getPostcode().', '.$billing->getCountry().'';
					//$list['Customer Shipping Address'] = ''.$shipping->getStreet1().', '.$shipping->getRegion().' - '.$shipping->getPostcode().', '.$shipping->getCountry().'';
					$list['Accept Substitute'] = 'No';
					if($customer->getAcceptSubstitutes()){
						$list['Accept Substitute'] = 'Yes';
					}
						$csv[] = $list;
				
				}
			}
		}
			//delivery date
			
			//orderdate
		if($report_date == "order_date"){
				$from = date('Y-m-d', strtotime($from));
					$to = date('Y-m-d', strtotime($to));
					
					$orders = Mage::getModel('sales/order')->getCollection();
					$orders->addFieldToFilter("status", array("in" => array("pending","processing")));
					
					$orders->addFieldToFilter("created_at", array("from" => $from));
					$orders->addFieldToFilter("created_at", array("to" => date('Y-m-d', strtotime( "$to + 1 day" ))));
					
					$list = array();
					/*$csv_headers = array("Order Number", "Item UPC Code", "Item Name", "Supplier", "Vendor ID", "Qty in Stock", "Ordered Qty", "List Price", "Extended Price", "Delivery Date", "Delivery Time", "Is Flexible Shipping", "Is Unattended Shipping", "Customer Name", 'Customer Contact Number', 'Customer Billing Address', 'Customer Shipping Address', 'Accept Substitute', 'Perishability');*/
					
					$csv_headers = array("Order Number", "Item UPC Code", "Item Name", "Size", "UOM", "Vendor ID", "Ordered Qty","List Price","Extended Price","discounted Price","Delivery fees", "Delivery Date", "Delivery Time", "Is Flexible Shipping", "Is Unattended Shipping", "Discount Code","Cusotmer Group", "Purchase Customer Name", "Delivery Customer Name", "Customer Contact Number", "Customer Billing Street Address", "Customer Billing City & State", "Customer Billing Zipcode", "Customer Delivery Address","Delivery City & State","Delivery Zipcode", "Accept Substitute");
					
					foreach($orders as $order){
						$order_number = $order->getIncrementId();
						
						$delivery_window_table = Mage::getSingleton('core/resource')->getTableName('shipping_delivery_window');
						
						$delivery_window = $this->_getConnection()->fetchRow("select * from ".$delivery_window_table." where quote_id = '".$order->getQuoteId()."'");
						//get Delviery fee extra
							$read = Mage::getSingleton('core/resource')->getConnection('core_read');
						$orders =  Mage::getModel('sales/order')->loadByIncrementId($order_number);
						$orderItems = $orders->getItemsCollection()->addAttributeToSelect('*')->addAttributeToFilter('order_id',$order_number)->load();
						$deliverywindow=$read->query("SELECT * FROM `mg_shipping_delivery_window` WHERE `order_number` = '$order_number' ");
						$rows1 = $deliverywindow->fetch();
						//print_r($rows1['shipcharge']);
						//
						$is_flexible = '';
						$is_unattended = '';
						
						if($delivery_window){
							
							$billing = $order->getBillingAddress();
							$shipping = $order->getShippingAddress();
							
							$customer =  Mage::getModel("customer/customer")->load($order->getCustomerId());
						
							$customer_code = $customer['group_id'];//$customer->getCustomerGroupId();
							$group_data = Mage::getModel('customer/group')->load($customer_code);
							//echo '<pre>';print_r($order->getAllVisibleItems());
							//exit;
							foreach($order->getAllVisibleItems() as $item){
								//echo '<pre>';print_r($item->getData());
								$list['Order Number'] = $order_number;
								$list['Item UPC Code'] = $item->getProduct()->getSku();
								$list['Item Name'] = $item->getProduct()->getName();
								$list['Size'] = $item->getProduct()->getSize();
								$list['UOM']  = $item->getProduct()->getUnits();
								/*$list['Supplier'] = $item->getProduct()->getSupplier();*/
								$list['Vendor ID'] 			= $item->getProduct()->getVendorProductId();
								
								/*$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProduct());
								$max_available_quantity = (int)$stock->getQty();
								$list['Qty in Stock'] = $max_available_quantity;*/
								$list['Ordered Qty'] 		= $item->getQtyOrdered();
								//$list['Actual Price'] 		= $item->getProduct()->getPrice();
								$list['List Price'] 		= $item->getPrice();
								$list['Extended Price']		= $item->getRowTotal();

								$list['discounted Price'] 	= $item->getProduct()->getPrice() - $item->getPrice();//$order->getBaseDiscountAmount();
								$list['Delivery fees'] 		= $order->getShippingAmount() + $rows1['shipcharge'];
								$d_time = $delivery_window['window'];
								if(strstr($d_time, '|')){
									$time_date = explode('|', $d_time);
									$delivery_time = explode('_', $time_date[0]);
									$delivery_time = implode(' ', $delivery_time);
									$delivery_date = $time_date[1];
									$list['Delivery Date'] = $delivery_date;
									$list['Delivery Time'] = $delivery_time;
								}else{
									$delivery_time = '';
									$delivery_date = $d_time;
									$list['Delivery Date'] = $d_time;
									$list['Delivery Time'] = ' - ';
								}
								
								$is_flexible = $delivery_window['flexible_shipping'];
								$is_unattended = $delivery_window['unattended_shipping'];
								
								$list['Is Flexible Shipping'] = $is_flexible;
								$list['Is Unattended Shipping'] = $is_unattended;
								$list['Discount Code'] = $order->getCouponCode();
								$list['Cusotmer Group'] = $group_data['customer_group_code'];
								$list['Purchase Customer Name'] = ''.iconv("UTF-8","ASCII//TRANSLIT",$billing->getFirstname()) . ' ' . iconv("UTF-8","ASCII//TRANSLIT",$billing->getLastname()).'';
								$list['Delivery Customer Name'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping->getFirstname()) . ' ' . iconv("UTF-8","ASCII//TRANSLIT",$shipping->getLastname()).'';
								$list['Customer Contact Number'] = $billing->getTelephone();
								$list['Customer Billing Street Address'] = iconv("UTF-8","ASCII//TRANSLIT",$billing->getStreet1());
								$list['Customer Billing City & State']	= iconv("UTF-8","ASCII//TRANSLIT",$billing->getCity()) .','.iconv("UTF-8","ASCII//TRANSLIT",$billing->getRegion());
								$list['Customer Billing Zipcode']	= $billing->getPostcode();			
								//$list['Customer Shipping Address'] = $shipping->getStreet1().', '.$shipping->getRegion().' - '.$shipping->getPostcode().', '.$shipping->getCountry();
								
								$list['Customer Delivery Address'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping['street']);
								$list['Delivery City & State'] = ' '.iconv("UTF-8","ASCII//TRANSLIT",$shipping->getData('city')) .' , '. iconv("UTF-8","ASCII//TRANSLIT",$shipping->getData('region'));
								$list['Delivery Zipcode'] = ' '.$shipping['postcode'];		
								
								$list['Accept Substitute'] = 'No';
								if($customer->getAcceptSubstitutes()){
									$list['Accept Substitute'] = 'Yes';
								}
								
								/*$list['Perishability'] = '';
								if($item->getProduct()->getPerishability()){
									//echo "select * from ".$this->at_table()." where option_id = '".$item->getProduct()->getPerishability()."' and store_id = '".$this->get_store_id()."'";
									$data = $this->_getConnection()->fetchRow("select * from ".$this->at_table()." where option_id = '".$item->getProduct()->getPerishability()."' and store_id = '".$this->get_store_id()."'");
									$list['Perishability'] = $data['value'];
								}*/
								
								$order->getQuoteId();					
								$csv[] = $list;
								//echo '<pre>';print_r($csv);
								
							}
							
							//$csv[] = $list;
						}
						unset($list);
					}
				
				}
			//end order date
			//
		
		

		$file_name = 'sales_pick_report'.date('m_d_Y_h_i').'.csv';

		$f = fopen('php://memory', 'w'); 
		
		fputcsv($f, $csv_headers, ','); 
		foreach($csv as $line) { 
			fputcsv($f, $line, ','); 
		}
		fseek($f, 0);
		
		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="'.$file_name.'"');
		header('Pragma: no-cache');
		fpassthru($f);
		}
	}
	
	public function vendorAction(){
		$orders = Mage::getModel('sales/order')->getCollection();
		
		$list = array();
		$csv_headers = array("Supplier", "Item UPC Code", "Item Name", "Qty", "Unit Price", "Total");
		
		$vendor_items = array();
		$products_count = array();
		$sku_products = array();
		foreach($orders as $order){
			echo $order_number = $order->getIncrementId();
			echo '<br />';
			echo count($order->getAllVisibleItems());
			echo '<br />';
			foreach($order->getAllVisibleItems() as $item){
				echo $item->getId();
				echo '<br />';
				echo $item->getProduct()->getSupplier();
				echo '<br />';
				echo $item->getSku();
				echo '<br />';
				
				$sku_products[$item->getProduct()->getSku()] = $item->getProduct()->getName();
				
				if(!in_array($item->getProduct()->getSku(), $vendor_items[$item->getProduct()->getSupplier()])){
					$vendor_items[$item->getProduct()->getSupplier()][] = $item->getProduct()->getSku();
				}
				
				if(array_key_exists($item->getProduct()->getSku(), $products_count)){
					$products_count[$item->getProduct()->getSku()] = ($products_count[$item->getProduct()->getSku()] + 1);
				}else{
					$products_count[$item->getProduct()->getSku()] = 1;
				}
			}
		}
		
		$csv = array();
		print_r($vendor_items);
		foreach($vendor_items as $supplier => $items){
			$list['Supplier'] = $supplier;
			$list['Item UPC Code'] = '';
			$list['Item Name'] = '';
			$list['Qty'] = '';
			$list['Unit Price'] = '';
			$list['Total'] = '';
			$csv[] = $list;
			unset($list);
					
			foreach($items as $item){
				if($item != ''){
					
					//$product = $sku_products[$item];
					echo $item;
					$list['Supplier'] = '';
					$list['Item UPC Code'] = $item;
					$list['Item Name'] = 'asdf';//"'".$product->getName()."'";
					$list['Qty'] = $products_count[$item];
					$list['Unit Price'] = 'asdf';//(float) number_format($product->getPrice(), 2);
					$list['Total'] = 'asdfds';//(float) number_format($product->getPrice() * (int)($products_count[$item]), 2);
					
					$csv[] = $list;
					unset($list);
				}
			}
		}
		
		print_r($csv);
		exit;
		
		$file_name = 'vendor_itms_'.date('m_d_Y_h_i').'.csv';

		$f = fopen('php://memory', 'w'); 
		
		fputcsv($f, $csv_headers, ','); 
		foreach($csv as $line) { 
			fputcsv($f, $line, ','); 
		}
		fseek($f, 0);
		
		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="'.$file_name.'"');
		header('Pragma: no-cache');
		fpassthru($f);
	}	
}	