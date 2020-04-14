<?php
class Discount_Orders_Block_Adminhtml_Sales_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{	public function __construct()
	{
		parent::__construct();
		$this->setId('discount_orders_grid');
		$this->setDefaultSort('increment_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('sales/order_collection')
		->addAttributeToSelect('*') 
		->addFieldToFilter('main_table.discount_amount',
		array('neq' => 0)                    
		) 
		->join(array('a' => 'sales/order_address'), 'main_table.entity_id = a.parent_id AND a.address_type != \'billing\'', array(
		'city'       => 'city',
		'country_id' => 'country_id',
		'postcode'	 =>	'postcode',
		
		))
		
		->join(array('c' => 'customer/customer_group'), 'main_table.customer_group_id = c.customer_group_id', array(
		'customer_group_code' => 'customer_group_code'
		))
		
		/*->join(array('f' => 'sales/shipping_delivery_window'), 'main_table.entity_id = f.order_number AND f.flexible_shipping = \'yes\'', array(
		'flexible_shipping'       => 'flexible_shipping',
			
		))*/ 
				
		->addExpressionFieldToSelect(
		'fullname',
		'CONCAT({{customer_firstname}}, \' \', {{customer_lastname}})',
		array('customer_firstname' => 'main_table.customer_firstname', 'customer_lastname' => 'main_table.customer_lastname'));
		
		
		
		/*$collection->getSelect()->joinLeft(array('shipping'=>'mg_sales_flat_order_address'),
		'main_table.entity_id = shipping.parent_id AND shipping.address_type="shipping" ',array('shipping.postcode AS sp'));*/
		
		/*$collection->getSelect()->joinLeft(array('flexible'=>'mg_shipping_delivery_window'),
		'main_table.entity_id = flexible.order_number AND flexible.flexible_shipping="yes" ',array('flexible.flexible_shipping AS fp'));*/
		
		
		$select = $collection->getSelect();
		$select->joinLeft(array('order' => Mage::getModel('core/resource')->getTableName('sales/order')), 'order.entity_id=main_table.entity_id',
		array('coupon_code' => 'coupon_code'));
				
		//var_dump((string)$collection->getSelect());
		
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
		
		
	}
	protected function _prepareColumns()
	{
		$helper = Mage::helper('discount_orders');
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
		'header' => $helper->__('Discount'),
		'index'  => 'discount_amount',
		'currency_code' => $currency,
		'type'          => 'currency',
		'align'	 =>	'right',
		 'renderer'=> new Dts_Banners_Block_Adminhtml_Collisions_Grid_Renderer_Collisiontype(),
		));
		
		$this->addColumn('discount_description', array(
		'header'    => Mage::helper('sales')->__('Discount Type'),
		'align'     =>'left',
		'width'     => '120px',
		'index'     => 'discount_description',
		'align'		=>	'center',
		'filter_index' => 'order.discount_description'
		));
		/*$this->addColumn('flexible_delivery', array(
		'header'    => Mage::helper('sales')->__('Flexible Delivery'),
		'align'     =>'left',
		'width'     => '50px',
		'index'     => 'flexible_delivery',
		'filter_index' => 'order.flexible_delivery'
		));*/
		
		$this->addColumn('coupon_code', array(
		'header'    => Mage::helper('sales')->__('Coupone Code'),
		'align'     =>'left',
		'width'     => '50px',
		'index'     => 'coupon_code',
		'filter_index' => 'order.coupon_code'
		));
		
		
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
		$this->addExportType('*/*/exportDiscountCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportDiscountExcel', $helper->__('Excel XML'));
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

class Dts_Banners_Block_Adminhtml_Collisions_Grid_Renderer_Collisiontype extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) {
        $value =  $row->getData($this->getColumn()->getIndex());
		$temp_value =  str_replace('-','',$value);
		$real_value =  number_format($temp_value,2);
		return '$'.$real_value;
    }
}