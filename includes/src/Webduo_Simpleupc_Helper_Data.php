<?php

class Webduo_Simpleupc_Helper_Data extends Mage_Core_Helper_Abstract
{
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}
	
	function upc_auth_code(){
		return 'obefin2W2vlMNjr0xwnGqEDltuDIDNJn';
	}
	
	function upc_url(){
		return 'http://api.simpleupc.com/v1.php';
	}
	
	//fetching method
	function fetch_upc($request, $check = true){
		{//prepare request
			$auth_code = $this->upc_auth_code();
			$upc_url = $this->upc_url();
			
			//add auth code in request array
			$request['auth'] = $auth_code;
			
			$json = json_encode($request);
	
			$ch = curl_init($upc_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$json");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			$output = json_decode($output, true);
			
			//return output if not to check the image or nutrition
			if(!$check){
				return $output;
			}else{
				if($output['result']['ProductHasImage'] == 1){
					$_request = array("method"=>'FetchImageByUPC', "params"=>array("upc"=>$request['params']['upc']));
					$images = $this->fetch_upc($_request, false);
					$output['images'] = $images;
					return $output;
				}
				return $output;
			}
		}
	}
	
	//fetching method
	function search_upc($request){
		{//prepare request
			$auth_code = $this->upc_auth_code();
			$upc_url = $this->upc_url();
			
			//add auth code in request array
			$request['auth'] = $auth_code;
			
			$json = json_encode($request);
	
			$ch = curl_init($upc_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$json");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			$output = json_decode($output, true);
			
			$products = array();
			foreach($output['results'] as $product){
				$_product = array();
				$_product['success'] = true;
				if($product['productHasImage'] == 1){
					$_request = array("method"=>'FetchImageByUPC', "params"=>array("upc"=>$product['upc']));
					$images = $this->fetch_upc($_request, false);
					$_product['images'] = $images;
				}
				$_product['result'] = $product;
				$products[] = $_product;
			}
		}
		return $products;
	}
	
	//fetching method
	function nutrition_fact_by_upc($upc){
		{//prepare request
			$auth_code = $this->upc_auth_code();
			$upc_url = $this->upc_url();
			
			//add auth code in request array
			$request = array("auth"=>$auth_code, "method"=>'FetchNutritionFactsByUPC', "params"=>array("upc"=>$upc));
			
			$json = json_encode($request);
	
			$ch = curl_init($upc_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$json");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			return $output = json_decode($output, true);
		}
	}
	
	function menu(){
		$view = new Webduo_Simpleupc_Block_Html_Topmenu;
		return $view->getList();
	}
	
	function save_delivery_window($post){
		$quote_id = Mage::getSingleton('checkout/session')->getQuote()->getId();
		//Add by xoom for customer group CORP.PROG functionality
		$quotecollection= Mage::getModel('sales/quote')->load($quote_id); 
		$customer_id =  $quotecollection['customer_id'];
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		$customer_code = $customer['group_id'];//$customer->getCustomerGroupId();
		$group_data = Mage::getModel('customer/group')->load($customer_code);
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read'); 
		$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$productTable = 'mg_customer_subsciption';
		$query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
		$result = $connection->query($query);
		$row = $result->fetch();
		$query2 = "select status from mg_aw_sarp2_profile where `customer_id` LIKE '$customer_id' AND `status` LIKE 'active'";
		$result2 = $connection->query($query2);
		$row2 = $result2->fetch();
				// Mage::log($row2,null,'row2.log') ;
		 //
		//$cart = Mage::getSingleton('checkout/cart');
		$quote=Mage::getModel('checkout/session')->getQuote(); 
		$shippingMethod = $quote->getShippingAddress(); 
		$shippingcharge=$shippingMethod['shipping_amount'];
		$cart = Mage::getModel('checkout/session')->getQuote();	
		//$address = $cart->getQuote()->getShippingAddress();
		//$address->getShippingAmount();
		if($shippingcharge == '' || $shippingcharge == 0){
			$shippingcharge = $_SESSION['flat_rate_Charge'];	
		//}else if($address->getShippingAmount()){
		//	$shippingcharge = $address->getShippingAmount();//$shippingcharge = $shippingMethod['shipping_amount'];
		}else{
			$shippingcharge = $shippingMethod['shipping_amount'];
				
		}
		
		
		//$shippingcharge = $address->getShippingAmount();
		Mage::log($cart['coupon_code'],null,'$cartcoupon_code.log');
	
		// 
		if($group_data['is_shipping'] == 0 && $group_data['choose_delivery_address'] == 1 && $group_data['is_shipping'] !=  ''){
			$discount_amount = 0;
			
		}else if($row['active'] == 'Yes' || $row2['status'] == 'active'){
			$discount_amount = 0;
			
		}else{	
			$discount_amount = $post['discount_amount'];
			}
		//Add by xoom for customer group CORP.PROG functionality
		$data = array();
		$data['unattended_shipping'] = 'no';
		$data['flexible_shipping'] = 'no';
		
		if($post['delivery_window'] != ''){
			$data['window'] = $post['delivery_window'];
			
		}
		if($post['unattended_shipping'] != ''){
			$data['unattended_shipping'] = $post['unattended_shipping'];
		}
		if($post['flexible_shipping'] != ''){
			$data['flexible_shipping'] = $post['flexible_shipping'];
		
		}
		if(Mage::getSingleton('customer/session')->getId()){
			$customer_id = Mage::getSingleton('customer/session')->getId();
			$data['customer_id'] = $customer_id;
		}
		//Mage::log($post['discount_amount'],null,'discountamdount_deliverys.log');
		if($post['discount_amount'] != ''){
			$data['discount_amount'] = $discount_amount;//$post['discount_amount'];
		}
		if($post['ordered_window'] != ''){
			$data['ordered_window'] = $post['ordered_window'];
		}
		
		
		if($row['active'] == 'Yes' || $cart['coupon_code'] == 'Free2Brecks' || $row2['status'] == 'active'){
			$shipcharge_val = '';

		}else{
						
			$shipcharge_val = $_SESSION['ship_charge'];	
			
		}
		if($_SESSION['ship_charge'] != ''){
			$data['shipcharge'] = $shipcharge_val;
		}
		if($quote_id){
			$data['quote_id'] = $quote_id;
			
		}
		$data['added_on'] = date('Y-m-d h:i:s');
		if($_SESSION['ship_charge'] == ''){
		$data['shipcharge'] = '';}
			
		$table = Mage::getSingleton("core/resource")->getTableName('shipping_delivery_window');
		// $this->getOnepage()->getQuote()->collectTotals()->save();
		
		$check = $this->_getConnection('core_read')->fetchRow('select * from '.$table.' where quote_id = "'.$quote_id.'"');
		if($check){
			try{
				unset($data['added_on'], $data['quote_id']);
				//unset($data['shipcharge'], $data['quote_id']);
				$this->_getConnection('core_write')->update($table, $data, ' quote_id = "'.$quote_id.'"');
				
				return true;
			} catch (Exception $e) {
				Mage::logException($e);
        		$result['error'] = $this->__('Unable to update Delivery Window.');
			}
		}else{
			try{
				$this->_getConnection('core_write')->insert($table, $data);
				return true;
			} catch (Exception $e) {
				Mage::logException($e);
        		$result['error'] = $this->__('Unable to set Delivery Window.');
			}
		}
	}
	
	public function get_delivery_by_quote_id($quote_id = false){
		if($quote_id){
			$table = Mage::getSingleton("core/resource")->getTableName('shipping_delivery_window');
		
			$data = $this->_getConnection('core_read')->fetchRow('select * from '.$table.' where quote_id = "'.$quote_id.'"');
			return $data;
		}
		return false;
	}
	
	public function zipcodes(){
		$zipcodes = Mage::getStoreConfig('simpleupc/options/zipcodes');
		
		$zipcodes = explode('<br />', nl2br(trim($zipcodes)));
		
		$zips = array();
		foreach($zipcodes as $zipcode){
			$zips[trim($zipcode)] = trim($zipcode);
		}
		return $zips;
	}
	
	public function holidays(){
		$holidays = Mage::getStoreConfig('simpleupc/delivery_window/holidays');
		
		$holidays = explode('<br />', nl2br(trim($holidays)));
		
		$holis = array();
		foreach($holidays as $holiday){
			$holis[trim($holiday)] = trim($holiday);
		}
		return $holis;
	}
}