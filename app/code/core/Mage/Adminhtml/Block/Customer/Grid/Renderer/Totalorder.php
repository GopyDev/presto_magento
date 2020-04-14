<?php
class Mage_Adminhtml_Block_Customer_Grid_Renderer_Totalorder extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$customer_id = $row->entity_id;
		$_orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id',$customer_id);                      
		//echo $_orderCnt = $_orders->count(); //orders count
		//echo $customer_id.' &nbsp; '.$_orderCnt;
		$_orderCnt = $_orders->count();
		
		if($_orderCnt >= 1)
		{	
			return   $_orderCnt;
		}
		return "0";
	}
 
}
?>