<?php

class Customer_Track_Block_Adminhtml_Track_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("trackGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("track/track")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("track")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("email", array(
				"header" => Mage::helper("track")->__("Email"),
				"index" => "email",
				));
				$this->addColumn("trackdevice", array(
				"header" => Mage::helper("track")->__("User Registered Device"),
				"index" => "trackdevice",
				));
				$this->addColumn("created_at", array(
				"header" => Mage::helper("track")->__("created_at"),
				"index" => "created_at",
				));
				$this->addColumn("updated_at", array(
				"header" => Mage::helper("track")->__("updated_at"),
				"index" => "updated_at",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return '#';
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_track', array(
					 'label'=> Mage::helper('track')->__('Remove Track'),
					 'url'  => $this->getUrl('*/adminhtml_track/massRemove'),
					 'confirm' => Mage::helper('track')->__('Are you sure?')
				));
			return $this;
		}
			

}