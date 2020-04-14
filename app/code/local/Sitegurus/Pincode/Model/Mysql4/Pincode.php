<?php

class Sitegurus_Pincode_Model_Mysql4_Pincode extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the pincode_id refers to the key field in your database table.
        $this->_init('pincode/pincode', 'pincode_id');
    }
}