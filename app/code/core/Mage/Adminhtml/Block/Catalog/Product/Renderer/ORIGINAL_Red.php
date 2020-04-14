<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Red extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
     {
		
         return $this->_getValue($row);
     }

     public function _getValue(Varien_Object $row)
     {
		/*echo "<pre>";
		print_r($row);
		exit;*/
		//echo "SKU is ".$row->sku;
		$sku = $row->sku;
		$productReportsCollection = Mage::getResourceModel('reports/product_collection')
		->addOrderedQty()
		->addAttributeToFilter('sku', $sku);
		$productReport = $productReportsCollection->getFirstItem();

		//echo 'Already Bought ' . (int)$productReport->ordered_qty;
		$val 	=  (int)$productReport->ordered_qty;
		if(isset($val)){
			return intval($val);
		}else{
			$val = 0;
			return intval($val);
		}
         //$val = $row->getData($this->getColumn()->getIndex());
		// echo "Val is ".$val;
         

     } 
 
}

/*class MageIgniter_RemoveQtyDecimals_Block_Adminhtml_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
 {
    public function render(Varien_Object $row)
     {
         return $this->_getValue($row);
     }

     public function _getValue(Varien_Object $row)
     {
         $val = $row->getData($this->getColumn()->getIndex());
         return intval($val);

     } 
 }*/
?>