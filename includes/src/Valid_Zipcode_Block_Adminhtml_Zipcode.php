<?php
class Valid_Zipcode_Block_Adminhtml_Zipcode extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
	 
    $this->_controller = 'adminhtml_zipcode';
    $this->_blockGroup = 'zipcode';
    $this->_headerText = Mage::helper('zipcode')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('zipcode')->__('Add Item');
    parent::__construct();
  }
}