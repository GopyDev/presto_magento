<?php


class Webskills_Shippingrestriction_Block_Adminhtml_Shippingzip extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_shippingzip";
	$this->_blockGroup = "shippingrestriction";
	$this->_headerText = Mage::helper("shippingrestriction")->__("Shippingzip Manager");
	$this->_addButtonLabel = Mage::helper("shippingrestriction")->__("Add New Item");
	parent::__construct();
	
	}

}