<?php
class Mage_Adminhtml_Block_Customer_Grid_Renderer_Howdoyouhear extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		 $customer_id = $row->entity_id;
		
		$customerid = $customer_id;
		$visitorData = Mage::getModel('customer/customer')->load($customerid);
		 $visitorData->getData();
		//$addressdata['telephone'];
		if($visitorData['how_do_your_hear']==1){
			$cust = 'Brochure in Mail';
		}
		if($visitorData['how_do_your_hear']==2){
			$cust = 'Facebook Advertisement/Post';
		}
		if($visitorData['how_do_your_hear']==3){
			$cust = 'Friends/Family';
		}
		if($visitorData['how_do_your_hear']==4){
			$cust = 'Saw Vehicle';
		}
		if($visitorData['how_do_your_hear']==5){
			$cust = 'Web Search (e.g. Google, Yahoo, etc)';
		}
		if($visitorData['how_do_your_hear']==6){
			$cust = 'Other'.', '.$visitorData['other_specify'];
			
		}
		//$cust= $visitorData->getData()->getAttributeText(how_do_your_hear);
		
		return $cust;
	}
 
}
?>