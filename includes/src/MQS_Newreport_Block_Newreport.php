<?php
class MQS_Newreport_Block_Newreport extends Mage_Core_Block_Template
{
  public function _prepareLayout() {
        return parent::_prepareLayout();
    }
 
    public function getReports() {
        if (!$this->hasData('newreport')) {
            $this->setData('newreport', Mage::registry('newreport'));
        }
        return $this->getData('newreport');
    }
}
