<?php
 
class Lifesotware_Orders_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
		parent::__construct();
        $this->setId('lifesotware_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setSaveParametersInSession(true);
		
    }
 
    protected function _prepareCollection()
    {
			
			
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
		 	->addAttributeToSelect('how_do_your_hear')
			->addAttributeToSelect('created_at')
			->addAttributeToSelect('group_id');
			//->addFieldToFilter('entity_id',array('in'=>$customer_ids));
			// pass customer id as a array	 
			$this->setCollection($collection );
		 
			return parent::_prepareCollection();
	}
 
    protected function _prepareColumns()
    {
     //  Registration Date, Customer ID, Customer Name, Customer Zipcode, # of orders placed, how they heard about us
		 
		 
		 
		 $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('Customer ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
         $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Registration Date'),
            'type'      => 'datetime',
			'index'     => 'created_at',
            'gmtoffset' => true
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Customer Name'),
            'index'     => 'name'
        ));
      
		$this->addColumn('Zipcode', array(
               'header'=> $this->__('Customer Zipcode'),
                'index' => 'customerzipcode',
				 'sortable'  => false,
                'type'  => 'text',
				'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Customerzipcode',
        ));
        $this->addColumn('Total Order', array(
               'header'=> $this->__('# of orders placed'),
               'index' => 'totalorder',
               'sortable'  => false,
                'width' => '70',
                'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Totalorder',
        ));
		 
    $this->addColumn('How do you hear', array(
            'header'    => Mage::helper('customer')->__('How did they hear'),
            'width'     => '150',
           
			 'index' => 'howdoyouhear',
				'sortable'  => false,
				'type' => 'options',
                'renderer' => 'Mage_Adminhtml_Block_Customer_Grid_Renderer_Howdoyouhear',
				'options' => array(
					'0' => Mage::helper('customer')->__('Please Select'),
					'1' => Mage::helper('customer')->__('Brochure in Mail'),
					'2' => Mage::helper('customer')->__('Facebook Advertisement/Post'),
					'3' => Mage::helper('customer')->__('Friends/Family'),
					'4' => Mage::helper('customer')->__('Saw Vehicle'),
					'5' => Mage::helper('customer')->__('Web Search (e.g. Google, Yahoo, etc)'),
					'6' => Mage::helper('customer')->__('Other'),
				 ),
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