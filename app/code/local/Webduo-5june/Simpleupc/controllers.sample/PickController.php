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
		
		$from = date('Y-m-d', strtotime($from));
		$to = date('Y-m-d', strtotime($to));
		
		$orders = Mage::getModel('sales/order')->getCollection();
		//
		
		//
		$orders->addFieldToFilter("status", array("in" => array("pending","processing")));
		$orders->addFieldToFilter("created_at", array("from" => $from));
		$orders->addFieldToFilter("created_at", array("to" => date('Y-m-d', strtotime( "$to + 1 day" ))));
		
	
		$list = array();
		$csv_headers = array("Order Number", "Item UPC Code", "Item Name", "Supplier", "Vendor ID", "Qty in Stock", "Ordered Qty", "List Price", "Extended Price", "Delivery Date", "Delivery Time", "Is Flexible Shipping", "Is Unattended Shipping", "Customer Name", 'Customer Contact Number', 'Customer Billing Address', 'Customer Shipping Address', 'Accept Substitute', 'Perishability');
		
		foreach($orders as $order){
			$order_number = $order->getIncrementId();
			
			$delivery_window_table = Mage::getSingleton('core/resource')->getTableName('shipping_delivery_window');
			
			$delivery_window = $this->_getConnection()->fetchRow("select * from ".$delivery_window_table." where quote_id = '".$order->getQuoteId()."'");
			
			$is_flexible = '';
			$is_unattended = '';
			
			if($delivery_window){
				
				$billing = $order->getBillingAddress();
				$shipping = $order->getShippingAddress();
				
				$customer =  Mage::getModel("customer/customer")->load($order->getCustomerId());
				
				/*$all_items = array();
				foreach($order->getAllVisibleItems() as $item){
					echo $order_number;
					echo '<br />';
					echo $item->getProduct()->getSku();
					echo '<br />';
					echo $item->getProduct()->getName();
					echo '<br />';
					/*if(array_key_exists($item->getProduct()->getSku(), $all_items)){
						echo '<br />qty - '.$all_items[$item->getProduct()->getSku()]['qty'];
						$qty = ($all_items[$item->getProduct()->getSku()]['qty']) + $item->getQtyOrdered();
						$all_items[$item->getProduct()->getSku()] = array('qty'=>$qty);//, 'item'=>$item); 
					}else{
						$all_items[$item->getProduct()->getSku()] = array('qty'=>$item->getQtyOrdered());//, 'item'=>$item); 
					}*/
				//}
//				echo '<br /><br />';*/
				
				foreach($order->getAllVisibleItems() as $item){
					
					$list['Order Number'] = $order_number;
					$list['Item UPC Code'] = $item->getProduct()->getSku();
					$list['Item Name'] = $item->getProduct()->getName();
					$list['Supplier'] = $item->getProduct()->getSupplier();
					$list['Vendor ID'] = $item->getProduct()->getVendorProductId();
					
					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProduct());
   					$max_available_quantity = (int)$stock->getQty();
					$list['Qty in Stock'] = $max_available_quantity;
					$list['Ordered Qty'] = $item->getQtyOrdered();
					$list['List Price'] = $item->getProduct()->getPrice();
					$list['Extended Price'] = $item->getProduct()->getPrice();
					$list['Extended Price'] = $item->getProduct()->getPrice();
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
						$list['delivery_date'] = $d_time;
						$list['delivery_time'] = ' - ';
					}
					
					$is_flexible = $delivery_window['flexible_shipping'];
					$is_unattended = $delivery_window['unattended_shipping'];
					
					$list['Is Flexible Shipping'] = $is_flexible;
					$list['Is Unattended Shipping'] = $is_unattended;
					
					$list['Customer Name'] = '"'.$billing->getFirstname() . ' ' . $billing->getLastname().'"';
					$list['Customer Contact Number'] = $billing->getTelephone();
					$list['Customer Billing Address'] = '"'.$billing->getStreet1().', '.$billing->getRegion().' - '.$billing->getPostcode().', '.$billing->getCountry().'"';
					$list['Customer Shipping Address'] = '"'.$shipping->getStreet1().', '.$shipping->getRegion().' - '.$shipping->getPostcode().', '.$shipping->getCountry().'"';
					
					$list['Accept Substitute'] = 'no';
					if($customer->getAcceptSubstitutes()){
						$list['Accept Substitute'] = 'yes';
					}
					
					$list['Perishability'] = '';
					if($item->getProduct()->getPerishability()){
						//echo "select * from ".$this->at_table()." where option_id = '".$item->getProduct()->getPerishability()."' and store_id = '".$this->get_store_id()."'";
						$data = $this->_getConnection()->fetchRow("select * from ".$this->at_table()." where option_id = '".$item->getProduct()->getPerishability()."' and store_id = '".$this->get_store_id()."'");
						$list['Perishability'] = $data['value'];
					}
					
					$order->getQuoteId();					
					$csv[] = $list;

				}
				
				//$csv[] = $list;
			}
			unset($list);
		}
		
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
		
		//print_r($csv);
		//exit;
		
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