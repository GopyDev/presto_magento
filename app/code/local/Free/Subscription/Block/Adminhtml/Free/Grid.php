<?php

class Free_Subscription_Block_Adminhtml_Free_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("freeGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("subscription/free")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("subscription")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("customer_name", array(
				"header" => Mage::helper("subscription")->__("Cusomer Name"),
				"index" => "customer_name",
				));
				$this->addColumn("customer_emailid", array(
				"header" => Mage::helper("subscription")->__("Customer Email-id"),
				"index" => "customer_emailid",
				));
				$this->addColumn("start_date", array(
				"header" => Mage::helper("subscription")->__("Start Date"),
				"index" => "start_date",
				));
				$this->addColumn("expiry_date", array(
				"header" => Mage::helper("subscription")->__("Expiry Date"),
				"index" => "expiry_date",
				));
				$this->addColumn("duration", array(
				"header" => Mage::helper("subscription")->__("Duration"),
				"index" => "duration",
				));
				$this->addColumn("active", array(
				"header" => Mage::helper("subscription")->__("Active"),
				"index" => "active",
				));
				
				$this->addColumn("amount", array(
				"header" => Mage::helper("subscription")->__("Amount"),
				"index" => "amount",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

			public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}

		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('id');
			$this->getMassactionBlock()->setFormFieldName('ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_free', array(
					 'label'=> Mage::helper('subscription')->__('Remove Free'),
					 'url'  => $this->getUrl('*/adminhtml_free/massRemove'),
					 'confirm' => Mage::helper('subscription')->__('Are you sure?')
				));
			return $this;
		}
			

}