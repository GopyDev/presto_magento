<?php
class Mage_Adminhtml_Block_Customer_Grid_Renderer_Customerzipcode extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		 $customer_id = $row->entity_id;
		
		$customerid = $customer_id;
		$visitorData = Mage::getModel('customer/customer')->load($customerid);
		$billingaddress = Mage::getModel('customer/address')->load($visitorData->default_billing);
		$addressdata = $billingaddress ->getData();
		$address = $addressdata['postcode'];
		
		//$addressdata['telephone'];
		
		return $address;
	}
 
}
?>