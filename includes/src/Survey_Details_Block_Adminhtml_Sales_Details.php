<?php
 
class Survey_Details_Block_Adminhtml_Sales_details extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'survey_details';
        $this->_controller = 'adminhtml_sales_details';
        $this->_headerText = Mage::helper('survey_details')->__('Customer Survey Report');
 
        parent::__construct();
        $this->_removeButton('add');
    }
}