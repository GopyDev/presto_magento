<?php

class Customer_Track_Block_Adminhtml_Report_Track extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_track';
		$this->_blockGroup = "track";
        $this->_headerText = Mage::helper('track')->__('Track Report');
        parent::__construct();
        $this->_removeButton('add');
    }
}