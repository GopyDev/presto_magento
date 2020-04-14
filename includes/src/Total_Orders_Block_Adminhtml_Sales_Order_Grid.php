<?php
 
class Total_Orders_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
		parent::__construct();
        $this->setId('total_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
		
    }
 
    protected function _prepareCollection()
    {
			/*$collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
			*/
			
			$customer_ids = array();
			$salesModel=Mage::getModel("sales/order");
			$salesCollection = $salesModel->getCollection();
			foreach($salesCollection as $order)
			{
				$customer_id = $order->getCustomerId();
				array_push($customer_ids,$customer_id);
				// store order id in array
			}
			$collection = Mage::getResourceModel('customer/customer_collection')
			->addNameToSelect()
			->addAttributeToSelect('email')
			->addAttributeToSelect('created_at')
			->addAttributeToSelect('group_id')
			->addFieldToFilter('entity_id',array('in'=>$customer_ids));
			// pass customer id as a array	 
			$this->setCollection($collection );
		 
			return parent::_prepareCollection();
	}
 
    protected function _prepareColumns()
    {
       
		 $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
      /* $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));
		*/
		$this->addColumn('Street Address', array(
               'header'=> $this->__('Street Address'),
                'index' => 'customeraddress',
				 'sortable'  => false,
                'type'  => 'text',
				'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Customeraddress',
        ));
		$this->addColumn('City/State', array(
               'header'=> $this->__('City/State'),
                'index' => 'customercitystate',
				 'sortable'  => false,
                'type'  => 'text',
				'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Customercitystate',
        ));
		$this->addColumn('Zipcode', array(
               'header'=> $this->__('Zipcode'),
                'index' => 'customerzipcode',
				 'sortable'  => false,
                'type'  => 'text',
				'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Customerzipcode',
        ));
        $this->addColumn('Total Order', array(
               'header'=> $this->__('Total Order'),
               'index' => 'totalorder',
               'sortable'  => false,
                'width' => '70',
                'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Totalorder',
        ));
		 $this->addColumn('Total Revenue', array(
               'header'=> $this->__('Total Revenue'),
                'index' => 'totalrevenue',
				'sortable'  => false,
                'width' => '70',
                'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Totalrevenue',
        ));
		
        /*$this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '100',
            'index'     => 'billing_telephone'
        ));
		*/
       /* $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '90',
            'index'     => 'billing_postcode',
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '100',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '100',
            'index'     => 'billing_region',
        ));
		*/
        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Joining Date'),
            'type'      => 'datetime',
			'index'     => 'created_at',
            'gmtoffset' => true
        ));

       
		$this->addExportType('*/*/exportInchooCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportInchooExcel', Mage::helper('customer')->__('Excel XML'));
 
        return parent::_prepareColumns();
    }
 
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}