<?php
class Survey_Details_Block_Adminhtml_Sales_Details_Grid extends Mage_Adminhtml_Block_Widget_Grid
{	public function __construct()
	{
		parent::__construct();
		$this->setId('survey_details_grid');
		$this->setDefaultSort('survey_result_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(false);
		$this->getRequest()->setparam('limit', 5000);
		//$this->setUseAjax(true);
	} 
	
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('customer/customer_collection');
		$collection->getSelect()->reset('columns');
		$collection->getSelect()
			->joinLeft(array('survey'=>'mg_survey_result'),
			'e.entity_id = survey.customer_id');
		$collection->getSelect()->order('survey.record_id DESC');
		$collection->getSelect()->limit(5000);
		$collection->getSelect()
			->where(new Zend_Db_Expr("e.entity_id = survey.customer_id") );
		$collection->getSelect()
			->group('survey.record_id');
			
			$this->setCollection($collection);
			parent::_prepareCollection();
			return $this;
			
	}
	protected function _prepareColumns()
	{
		
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$qry = "Select survey_qn_title FROM mg_survey_questions WHERE is_active = '1' ";
		$quetsionData = $connection->fetchAll($qry);
				
		$question1 = wordwrap($quetsionData[0]['survey_qn_title'], 25, "<br />\n");
		$question2 = wordwrap($quetsionData[1]['survey_qn_title'], 25, "<br />\n");
		$question3 = wordwrap($quetsionData[2]['survey_qn_title'], 35, "<br />\n");
		$question4 = wordwrap($quetsionData[3]['survey_qn_title'], 25, "<br />\n");
		//$question4 = wordwrap($quetsionData[3]['survey_qn_title'], 25, "<br />\n");
		
		
		$helper = Mage::helper('survey_details');
		$currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);  
		$this->addColumn('record_id', array(
		'header' => $helper->__('Sr No #'), 
		'index'  => 'record_id',
		'width'     => '50px'
		));
		
		$this->addColumn('customer_id', array(
		'header'       => $helper->__('Customer Name'),
		'type'   	   => 'text',
		'index'        => 'customer_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Red',
		'width'     => '50px',
		));
		
		$this->addColumn('driver_id', array(
		'header'       => $helper->__('Driver Name'),
		'type'   	   => 'text',
		'index'        => 'driver_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Driver',
		'width'     => '50px',
		));
		
		$this->addColumn('picker_id', array(
		'header'       => $helper->__('Picker Name'),
		'type'   	   => 'text',
		'index'        => 'picker_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Picker',
		'width'     => '50px',
		));
		
		
		$this->addColumn('question1', array(
		'header' => $helper->__($question1),
		'type'   => 'text',
		'index'  => 'survey_result_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_First',
		'width'     => '50px',
		));
		
		
		$this->addColumn('question2', array(
		'header' => $helper->__($question2),
		'type'   	   => 'text',
		'index'  		=> 'record_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Second',
		'width'     => '50px',
		));
		
		$this->addColumn('question4', array(
		'header' => $helper->__($question4),
		'type'   	   => 'text',
		'index'  		=> 'record_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Fourth',
		'width'     => '50px',
		));
		
		$this->addColumn('question3', array(
		'header' => $helper->__($question3),
		'type'   	   => 'text',
		'index'  		=> 'record_id',
		'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Third',
		'width'     => '500px',
		));
		
		
		$this->addColumn('created_time', array(
		'header' => $helper->__('Survey Date/Time'),
		'type'   => 'datetime',
		'index'  => 'created_time'
		));
		
		
		$this->addColumn('order_id', array(
		'header' => $helper->__('Order Number'),
		'type'   => 'text',
		'index'  => 'order_id'
		));
		
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
				
				$qry = "SELECT survey_result_id FROM mg_survey_result WHERE record_id = '".$value."'";
				$records = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($records);
				$qry = "SELECT result.*, value.*, ans.* FROM  mg_survey_result as result, mg_survey_result_value as value, mg_survey_answer as ans
				WHERE result.record_id = '".$value."' AND result.survey_result_id = '".$records['1']['survey_result_id']."' 
				AND result.survey_result_id = value.survey_result_value_id 
				AND value.survey_result_value = ans.survey_ans_id
				";
								
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['survey_ans_title'];
		
		}
}

// thid Answer
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Fourth extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				
				$qry = "SELECT survey_result_id FROM mg_survey_result WHERE record_id = '".$value."'";
				$records = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($records);
				$qry = "SELECT result.*, value.*, ans.* FROM  mg_survey_result as result, mg_survey_result_value as value, mg_survey_answer as ans
				WHERE result.record_id = '".$value."' AND result.survey_result_id = '".$records['2']['survey_result_id']."' 
				AND result.survey_result_id = value.survey_result_value_id 
				AND value.survey_result_value = ans.survey_ans_id
				";
								
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['survey_ans_title'];
		
		}
}
// Fourth Answer
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Third extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				$qry = "SELECT record_id FROM mg_survey_result WHERE record_id = '".$value."'";
				$records = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($records);  
				
				$qry = "SELECT result.*,value.* FROM mg_survey_result as result, mg_survey_result_value as value WHERE result.record_id = '".$value."' AND result.survey_result_id = value.survey_result_value_id ORDER BY result.survey_result_id DESC LIMIT 0,1
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

// Get Picker Name
class Mage_Adminhtml_Block_Catalog_Product_Renderer_Picker extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
