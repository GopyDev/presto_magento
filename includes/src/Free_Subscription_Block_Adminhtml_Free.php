<?php


class Free_Subscription_Block_Adminhtml_Free extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_free";
	$this->_blockGroup = "subscription";
	$this->_headerText = Mage::helper("subscription")->__("Free Manager");
	$this->_addButtonLabel = Mage::helper("subscription")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}