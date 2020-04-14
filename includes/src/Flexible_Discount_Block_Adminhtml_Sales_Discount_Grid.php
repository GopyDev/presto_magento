<?php
class Flexible_Discount_Block_Adminhtml_Sales_Discount_Grid extends Mage_Adminhtml_Block_Widget_Grid
{	public function __construct()
	{
		parent::__construct();
		$this->setId('flexible_discount_grid');
		$this->setDefaultSort('increment_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('sales/order_collection')
		->addAttributeToSelect('*')
		/*->addFieldToFilter('main_table.discount_amount',
		array('neq' => 0)                    
		) */
		
		->join(array('a' => 'sales/order_address'), 'main_table.entity_id = a.parent_id AND a.address_type != \'billing\'', array(
		'city'       => 'city',
		'country_id' => 'country_id',
		'postcode'	 =>	'postcode',
		
		))
		
		->join(array('c' => 'customer/customer_group'), 'main_table.customer_group_id = c.customer_group_id', array(
		'customer_group_code' => 'customer_group_code'
		))
		
						
		->addExpressionFieldToSelect(
		'fullname',
		'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
		array('customer_firstname' => 'main_table.customer_firstname', 'customer_lastname' => 'main_table.customer_lastname'));
		
		$collection->getSelect()->join(array('fd'=>'mg_shipping_delivery_window'),
		'fd.quote_id = main_table.quote_id AND fd.flexible_shipping = \'yes\' AND fd.discount_amount != \'0\' ',
		array(
		'flexible_shipping'=> 'fd.flexible_shipping',
		//'status' => 'IF(gf.status IS NULL,"Unsend","Send")',
		'discount_amount' => 'fd.discount_amount'
		));
					
					
				
		//var_dump((string)$collection->getSelect());
		
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
		
		
	}
	protected function _prepareColumns()
	{
		$helper = Mage::helper('flexible_discount');
		$currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
		$this->addColumn('increment_id', array(
		'header' => $helper->__('Order #'),
		'index'  => 'increment_id'
		));
		$this->addColumn('purchased_on', array(
		'header' => $helper->__('Purchased On'),
		'type'   => 'datetime',
		'index'  => 'created_at'
		));
		
		$this->addColumn('fullname', array(
		'header'       => $helper->__('Name'),
		'index'        => 'fullname',
		'filter_index' => 'CONCAT(customer_firstname, \' \', customer_lastname)'
		));
		
		
		$this->addColumn('postcode', array(
		'header' => $helper->__('Zipcode'),
		'index'  => 'postcode'
		));
		
		
		$this->addColumn('customer_group', array(
		'header' => $helper->__('Customer Group'),
		'index'  => 'customer_group_code'
		));
		
		$this->addColumn('discount_amount', array(
		'header' => $helper->__('Discount Amount'),
		'index'  => 'discount_amount',
		'currency_code' => $currency,
		'type'          => 'currency',
		));
		
		/*$this->addColumn('discount_description', array(
		'header'    => Mage::helper('sales')->__('Discount Type'),
		'align'     =>'left',
		'width'     => '120px',
		'index'     => 'discount_description',
		'align'		=>	'center',
		'filter_index' => 'order.discount_description'
		));*/
		/*$this->addColumn('flexible_delivery', array(
		'header'    => Mage::helper('sales')->__('Flexible Delivery'),
		'align'     =>'left',
		'width'     => '50px',
		'index'     => 'flexible_delivery',
		'filter_index' => 'order.flexible_delivery'
		));*/
		
		/*$this->addColumn('coupon_code', array(
		'header'    => Mage::helper('sales')->__('Coupone Code'),
		'align'     =>'left',
		'width'     => '50px',
		'index'     => 'coupon_code',
		'filter_index' => 'order.coupon_code'
		));*/
		
		
		$this->addColumn('base_grand_total', array(
		'header'        => $helper->__('Grand Total'),
		'index'         => 'base_grand_total',
		'type'          => 'currency',
		'currency_code' => $currency
		));
		
		$this->addColumn('order_status', array(
		'header'  => $helper->__('Status'),
		'index'   => 'status',
		'type'    => 'options',
		'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
		));
		
		$this->addExportType('*/*/exportFlexibleCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportFlexibleExcel', $helper->__('Excel XML'));
		return parent::_prepareColumns();
	}
	
	public function render(Varien_Object $row)
	{
		$id = $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load($id); //customer id
		$data = "";
		if ( $customer->getDefaultShippingAddress() != null ) {
			$shipping_address = $customer->getDefaultShippingAddress();
			if ( $shipping_address->getFirstname() != null ) {
				$data .= $shipping_address->getFirstname();
			}
			if ( $shipping_address->getLastname() != null ) {
				if ( $shipping_address->getFirstname() != null ) {
					$data .= " ";
				}
				$data .= $shipping_address->getLastname();
			}
		}
		return $data;
	}
	public function getGridUrl()
	{
		//return $this->getUrl('*/*/grid', array('_current'=>true));
	}
}