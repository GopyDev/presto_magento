<?php

class Sitegurus_Pincode_Model_Pincode extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('pincode/pincode');
    }
}