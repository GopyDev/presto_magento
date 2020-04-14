<?php
class MQS_Newreport_Model_Newreport extends Mage_Catalog_Model_Resource_Product_Collection
{
  function __construct() {
        parent::__construct();
        //$this->setResourceModel('sales/order_item');
        //$this->_init('sales/order_item','item_id');
   }
 
    public function setDateRange($from, $to) {
        $this->_reset();
		
		$prefix 			= 	Mage::getConfig()->getTablePrefix();
		
		$inventoryTable		=	$prefix.'cataloginventory_stock_item';
		$reportViewTable	=	$prefix.'report_viewed_product_index';
		$salesOrderedItem	=	$prefix.'sales_flat_order_item';
		$catalogProductEntityVar	=	$prefix.'catalog_product_entity_varchar';
		
        $this->getSelect()
			->joinLeft(
					array('evar' => $catalogProductEntityVar),
					'e.entity_id = evar.entity_id AND evar.attribute_id = 71',
					array('name' => 'evar.value')
					)
			->joinLeft(
					array('item' => $salesOrderedItem),
					'evar.entity_id = item.product_id AND item.created_at BETWEEN "'.$from.'" AND "'.$to.'"',
					array('ordered' => 'COUNT(DISTINCT item.item_id)')
				)
			 ->joinLeft(
					array('inventory' => $inventoryTable),
					'e.entity_id = inventory.product_id',
					array('qty' => 'inventory.qty')
			 )
			 ->joinLeft(
					array('report' => $reportViewTable),
					'inventory.product_id = report.product_id AND report.added_at BETWEEN "'.$from.'" AND "'.$to.'"',
					array('viewed' => 'COUNT( DISTINCT report.index_id)')
			 )
			 //->where("report.added_at BETWEEN '".$from."' AND '".$to."' OR item.created_at BETWEEN '".$from."' AND '".$to."'")
			 
			  ->group('e.entity_id')
			  //->having('COUNT(DISTINCT item.item_id) > ?', 2)
			  ->having('COUNT(DISTINCT report.index_id) > ?', 2);
			  
			  
		
        return $this;
    }
 
    public function setStoreIds($storeIds)
    {
        return $this;
    }
 
}
