<?php
class Valid_Zipcode_Block_Zipcode extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getZipcode()     
     { 
        if (!$this->hasData('zipcode')) {
            $this->setData('zipcode', Mage::registry('zipcode'));
        }
        return $this->getData('zipcode');
        
    }
}