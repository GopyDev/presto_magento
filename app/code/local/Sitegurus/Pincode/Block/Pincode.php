<?php
class Sitegurus_Pincode_Block_Pincode extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getPincode()     
     { 
        if (!$this->hasData('pincode')) {
            $this->setData('pincode', Mage::registry('pincode'));
        }
        return $this->getData('pincode');
        
    }
}