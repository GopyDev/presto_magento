<?php

class Demac_ProductOrders_Block_Adminhtml_Catalog_Product_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('productordersGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('created_at');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Get the current product
	 *
	 * @return mixed Mage_Catalog_Model_Product
	 *
	 */
	protected function _getProduct()
	{
		return Mage::registry('current_product');
	}

	/**
	 * Prepare grid collection object
	 *
	 * @return $this|Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareCollection()
	{
		$product = $this->_getProduct();
		$productId = $product->getId();
		$collection = Mage::getResourceModel('sales/order_item_collection');
		//echo "<pre>";
		//print_r($productId);
		if (isset($productId) && !empty($productId)) {
			$collection
				->addFieldToFilter('product_id', $productId)
				
				->getSelect()->joinInner(array(
						'order' => $collection->getTable('sales/order')),
					'order.entity_id = main_table.order_id',
					array('increment_id', 'customer_firstname', 'customer_lastname', 'status', 'order_currency_code')
				);
				/*echo "<pre>";
				print_r($collection);*/
		}

		$this->setCollection($collection);

		parent::_prepareCollection();

		return $this;
	}

	/**
	 * Prepare the grid columns
	 *
	 * @return mixed $this|void
	 *
	 */
	protected function _prepareColumns()
	{
		 //$store = $this->_getStore();
		$this->addColumn('real_order_id', array(
			'header' => Mage::helper('demac_productorders')->__('Order #'),
			'width'  => '80px',
			'type'   => 'text',
			'index'  => 'increment_id',
		));

		/*if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'          => Mage::helper('demac_productorders')->__('Purchased From (Store)'),
				'index'           => 'store_id',
				'type'            => 'store',
				'store_view'      => true,
				'display_deleted' => true,
			));
		}*/

		$this->addColumn('created_at', array(
			'header' => Mage::helper('demac_productorders')->__('Purchased On'),
			'index'  => 'created_at',
			'type'   => 'datetime',
			'width'  => '150px',
		));

		$this->addColumn('customer_firstname', array(
			'header' => Mage::helper('demac_productorders')->__('Customer First Name'),
			'index'  => 'customer_firstname',
			
		));

		$this->addColumn('customer_lastname', array(
			'header' => Mage::helper('demac_productorders')->__('Customer Last Name'),
			'index'  => 'customer_lastname',
		));

		$this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Original Price'),
            'type'  => 'currency',
            'width'     => '1',
            'index'     => 'price',
			'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
			'editable' =>false
            ));
		
		
		$this->addColumn('qty_ordered', array(
			'header' => Mage::helper('demac_productorders')->__('Qty Ordered'),
			'width'  => '50px',
			'type'   => 'number',
			'index'  => 'qty_ordered',
		));

		$this->addColumn('row_total', array(
			'header'   => Mage::helper('demac_productorders')->__('Row Total'),
			'index'    => 'row_total',
			'type'     => 'currency',
			'currency' => 'order_currency_code',
			
		));

		$this->addColumn('status', array(
			'header'  => Mage::helper('demac_productorders')->__('Status'),
			'index'   => 'status',
			'type'    => 'options',
			'width'   => '70px',
			'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
		));
		/*$this->addColumn('action', array(
			'header'  => Mage::helper('demac_productorders')->__('Action'),
			'width'   => '70px',
			'type'    => 'action',
			'getter'  => 'getOrderId',
			
		));*/
		$this->addColumn('action',
			array(
			'header'    => Mage::helper('catalog')->__('Action'),
			'width'     => '80px',
			'type'      => 'action',
			'getter'     => 'getOrderId',
			'actions'   => array(
				array(
				'caption' => 'View Order',
				'url'     => array('base'=>'*/sales_order/view'),
                'field'   => 'order_id',
				'target'  =>'_blank',				
				)
			),
			'align' 	=> 'center',
			'filter'    => false,
			'sortable'  => false
		));
		
		

		$this->addExportType('*/*/exportCsv', Mage::helper('demac_productorders')->__('CSV'));

		return parent::_prepareColumns();
	}

	/**
	 * @return mixed|string
	 */
	public function getGridUrl()
	{
		return $this->_getData('grid_url')
			? $this->_getData('grid_url')
			: $this->getUrl('*/*/productordersGrid', array('_current' => true));
	}

	/**
	 * @param $item
	 *
	 * @return bool|string
	 */
	public function getRowUrl($item)
	{
		if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			$orderId = $item->getOrderId();
			
			if (isset($orderId) && !empty($orderId)) {
				//return $this->getUrl('*/sales_order/view', array('order_id' => $orderId));
				
			} else {
				return false;
			}
		}

		return false;
	}
}