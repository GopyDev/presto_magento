<?php
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Red extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		/*$value =  $row->getData($this->getColumn()->getIndex());
		return '<span style="color:red;">'.$value.'</span>';*/
		/*$sku = $row->sku;
		$productReportsCollection = Mage::getResourceModel('reports/product_collection')
		->addOrderedQty()
		->addAttributeToFilter('sku', $sku);
		$productReport = $productReportsCollection->getFirstItem();

		//echo 'Already Bought ' . (int)$productReport->ordered_qty;
		$val 	=  $productReport->ordered_qty;
		return $val;*/
		
		
		$sku = nl2br($row->sku);
		//echo "SKU is ".$sku;
		//exit;
		$_product = Mage::getResourceModel('reports/product_collection');
		$to = $_product->getResource()->formatDate(time());
		//$from = $_product->getResource()->formatDate(time() - 60 * 60 * 24 * 1);
		$from = $_product->getResource()->formatDate(time() - 60 * 60 * 24 * 30);
		
		$product = Mage::getResourceModel('reports/product_collection')
		->addOrderedQty($from, $to, true)
		->addAttributeToFilter('sku', $sku)
		->setOrder('ordered_qty', 'asc')
		->getFirstItem();
		//echo 'Quantity Ordered Months '.(int)$product->ordered_qty; 
		echo (int)$product->ordered_qty; 
		//exit;
			}
 
}
?>