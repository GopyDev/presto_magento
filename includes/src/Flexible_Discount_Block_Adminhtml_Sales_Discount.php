<?php
 
class Flexible_Discount_Block_Adminhtml_Sales_discount extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'flexible_discount';
        $this->_controller = 'adminhtml_sales_discount';
        $this->_headerText = Mage::helper('flexible_discount')->__('Flexible Delivery Discount Report');
 
        parent::__construct();
        $this->_removeButton('add');
    }
}