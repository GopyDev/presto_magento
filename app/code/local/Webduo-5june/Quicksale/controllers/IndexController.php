<?php

class Webduo_Quicksale_IndexController extends Mage_Core_Controller_Front_Action {
	
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}	
	
	public function testAction(){
		$orders = Mage::getModel('sales/order')->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToSort('increment_id', 'desc')
					->setPageSize(5);
		
		$ordered_items = array();
		foreach($orders as $order){
			foreach($order->getAllVisibleItems() as $item){
				echo $item->getId();
				echo '<br />';
				echo $product = $item->getProduct()->getName();
				echo '<br />';
				//$ordered_items[] = $item;
			}
		}
		echo '<br />';echo '<br />';
		
	}
}	