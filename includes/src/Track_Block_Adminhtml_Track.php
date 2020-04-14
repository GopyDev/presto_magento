<?php


class Customer_Track_Block_Adminhtml_Track extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_track";
	$this->_blockGroup = "track";
	$this->_headerText = Mage::helper("track")->__("Track Manager");
	$this->_addButtonLabel = Mage::helper("track")->__("Add New Item");
	parent::__construct();
	$this->_removeButton('add');
	}

}