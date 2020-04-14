<?php

class Webskills_Shippingrestriction_Block_Adminhtml_Shippingzip_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("shippingzipGrid");
				$this->setDefaultSort("id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("shippingrestriction/shippingzip")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("id", array(
				"header" => Mage::helper("shippingrestriction")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "id",
				));
                
				$this->addColumn("zipcode", array(
				"header" => Mage::helper("shippingrestriction")->__("Zip Code"),
				"index" => "zipcode",
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
			$this->getMassactionBlock()->addItem('remove_shippingzip', array(
					 'label'=> Mage::helper('shippingrestriction')->__('Remove Shippingzip'),
					 'url'  => $this->getUrl('*/adminhtml_shippingzip/massRemove'),
					 'confirm' => Mage::helper('shippingrestriction')->__('Are you sure?')
				));
			return $this;
		}
			

}