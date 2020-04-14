<?php
class MQS_Newreport_Block_Adminhtml_Newreport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_controller = 'adminhtml_newreport';
        $this->_blockGroup = 'newreport';
        $this->_headerText = Mage::helper('newreport')->__('Advace Reports');
        parent::__construct();
        $this->_removeButton('add');
    }
}
