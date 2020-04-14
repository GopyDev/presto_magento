<?php
 
class Discount_Orders_Block_Adminhtml_Sales_Orders extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'discount_orders';
        $this->_controller = 'adminhtml_sales_orders';
        $this->_headerText = Mage::helper('discount_orders')->__('Delivery Discount Report');
 
        parent::__construct();
        $this->_removeButton('add');
    }
}