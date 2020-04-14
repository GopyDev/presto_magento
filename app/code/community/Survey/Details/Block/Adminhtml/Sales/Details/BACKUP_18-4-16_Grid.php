<?php
class Survey_Details_Block_Adminhtml_Sales_Details_Grid extends Mage_Adminhtml_Block_Widget_Grid
{	public function __construct()
	{
		parent::__construct();
		$this->setId('survey_details_grid');
		$this->setDefaultSort('survey_result_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(false);
		//$this->setUseAjax(true);
	} 
	
	protected function _prepareCollection()
	{
		
			$collection = Mage::getResourceModel('customer/customer_collection');
			$collection->getSelect()->reset('columns'); // remove all customer columns
			$collection->joinTable('mg_survey_result', 'customer_id=entity_id', array('*'));
			$collection->groupByAttribute('entity_id');
			
			
			/*$collection = Mage::getResourceModel('customer/customer_collection');
			$collection->getSelect()->reset('columns'); // remove all customer columns
			$collection->joinTable('mg_survey_result', 'customer_id=entity_id', array('*'));
			$collection->setOrder('survey_result_id', 'DESC');*/
			
			$this->setCollection($collection);
			//echo $collection->getSelect()->__toString();
			
			return parent::_prepareCollection();
			/*$this->setCollection($collection);
			parent::_prepareCollection();
			return $this;*/
			
		
		
		
	}
	protected function _prepareColumns()
	{
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$qry = "Select survey_qn_title FROM mg_survey_questions WHERE is_active = '1' ";
		$quetsionData = $connection->fetchAll($qry);
				
		$question1 = wordwrap($quetsionData[0]['survey_qn_title'], 25, "<br />\n");
		$question2 = wordwrap($quetsionData[1]['survey_qn_title'], 25, "<br />\n");
		$question3 = wordwrap($quetsionData[2]['survey_qn_title'], 25, "<br />\n");
		$question4 = wordwrap($quetsionData[3]['survey_qn_title'], 25, "<br />\n");
		
		
		$helper = Mage::helper('survey_details');
		$currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);  
		$this->addColumn('survey_result_id', array(
		'header' => $helper->__('Sr No #'), 
		'index'  => 'survey_result_id',
		'width'     => '50px'
		));
		
		$this->addColumn('customer_id', array(
		'header'       => $helper->__('Customer Name'),
		'type'   	   => 'text',
		'index'        => 'customer_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Red',
		));
		
		$this->addColumn('driver_id', array(
		'header'       => $helper->__('Driver Name'),
		'type'   	   => 'text',
		'index'        => 'driver_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Driver',
		));
		
		
		$this->addColumn('question1', array(
		'header' => $helper->__($question1),
		'type'   => 'text',
		'index'  => 'survey_result_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_First',
		));
		
		
		$this->addColumn('question2', array(
		'header' => $helper->__($question2),
		'type'   	   => 'text',
		'index'  		=> 'customer_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Second',
		));
		
		$this->addColumn('question3', array(
		'header' => $helper->__($question3),
		'type'   	   => 'text',
		'index'  		=> 'customer_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Third',
		));
		
		$this->addColumn('feedback', array(
		'header'        => $helper->__($question4),
		'type'   	   => 'text',
		'index'  		=> 'customer_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Fourth',
		));
		
		$this->addColumn('created_time', array(
		'header' => $helper->__('Survey Date/Time'),
		'type'   => 'datetime',
		'index'  => 'created_time'
		));
		
		
		/*$this->addColumn('details_description', array(
		'header'    => Mage::helper('sales')->__('Details Type'),
		'align'     =>'left',
		'width'     => '120px',
		'index'     => 'details_description',
		'align'		=>	'center',
		'filter_index' => 'order.details_description'
		));*/
		/*$this->addColumn('survey_delivery', array(
		'header'    => Mage::helper('sales')->__('Survey Delivery'),
		'align'     =>'left',
		'width'     => '50px',
		'index'     => 'survey_delivery',
		'filter_index' => 'order.survey_delivery'
		));*/
		
		/*$this->addColumn('coupon_code', array(
		'header'    => Mage::helper('sales')->__('Coupone Code'),
		'align'     =>'left',
		'width'     => '50px',
		'index'     => 'coupon_code',
		'filter_index' => 'order.coupon_code'
		));*/
		
		
		
		
		/*$this->addColumn('order_status', array(
		'header'  => $helper->__('Status'),
		'index'   => 'status',
		'type'    => 'options',
		'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
		));*/
		
		$this->addExportType('*/*/exportSurveyCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportSurveyExcel', $helper->__('Excel XML'));
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


class Mage_Adminhtml_Block_Catalog_Product_Renderer_Red extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "Select value FROM mg_customer_entity_varchar WHERE entity_id = '".$value."' AND attribute_id IN (5,7)";
				$customerData = $connection->fetchAll($qry);
				return $customerData[0]['value'].'&nbsp;'.$customerData[1]['value'];
		 
		}
 
}

// First Answer
class Mage_Adminhtml_Block_Catalog_Product_Renderer_First extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "select result.*, ans.* from mg_survey_result_value as result,mg_survey_answer as ans where result.survey_result_id = '".$value."' AND result.survey_result_value = ans.survey_ans_id";
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['survey_ans_title'];
		
		}
}

// second Answer
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Second extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "SELECT survey_result_id FROM mg_survey_result WHERE customer_id = '".$value."'";
				$records = $connection->fetchAll($qry);
				
				$qry = "SELECT result.*, value.*, ans.* FROM  mg_survey_result as result, mg_survey_result_value as value, mg_survey_answer as ans
				WHERE result.customer_id = '".$value."' AND result.survey_result_id = '".$records['1']['survey_result_id']."' 
				AND result.survey_result_id = value.survey_result_value_id 
				AND value.survey_result_value = ans.survey_ans_id
				";
								
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['survey_ans_title'];
		
		}
}

// Third Answer
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Third extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "SELECT survey_result_id FROM mg_survey_result WHERE customer_id = '".$value."'";
				$records = $connection->fetchAll($qry);
				$qry = "SELECT result.*, value.*, ans.* FROM  mg_survey_result as result, mg_survey_result_value as value, mg_survey_answer as ans
				WHERE result.customer_id = '".$value."' AND result.survey_result_id = '".$records['2']['survey_result_id']."' 
				AND result.survey_result_id = value.survey_result_value_id 
				AND value.survey_result_value = ans.survey_ans_id
				";
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				if($ans[0]['survey_ans_title'] == 'Bed'){
					return '<span style="color:red;">'.$ans[0]['survey_ans_title'].'</span>';
					
				}else{
					return $ans[0]['survey_ans_title'];					
				}
				
		
		}
}
// Fourth Answer
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Fourth extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "SELECT survey_result_id FROM mg_survey_result WHERE customer_id = '".$value."'";
				$records = $connection->fetchAll($qry);
				$qry = "SELECT result.*, value.* FROM mg_survey_result as result, mg_survey_result_value as value
				WHERE result.customer_id = '".$value."'
				AND result.survey_result_id = '".$records['3']['survey_result_id']."' 
				AND result.survey_result_id = survey_result_value_id
				";
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['survey_result_value'];
		
		}
}

// Get Driver Name
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Driver extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "SELECT name FROM mg_pickers where id = '".$value."' ";
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['name'];
		
		}
}
