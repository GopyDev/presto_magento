<?php
class Mage_Adminhtml_Block_Customer_Grid_Renderer_Totalrevenue extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$customer_id = $row->entity_id;
		$_orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id',$customer_id);                      
	//	print_r($_orders);	
		//echo $_orderCnt = $_orders->count(); //orders count
		
	
			$orderTotals = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('customer_id',$customer_id)
			->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
			->setOrder('created_at', 'desc') 
			->addAttributeToSelect('grand_total')
			->getColumnValues('grand_total');

		$totalSum = array_sum($orderTotals);
		//echo $totalSum = Mage::helper('core')->currency($totalSum, true, false);
		return $totalSum = Mage::helper('core')->currency($totalSum, true, false);
		
	
	}
 
}
?>