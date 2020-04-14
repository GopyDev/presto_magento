<?php

class Valid_Zipcode_Model_Mysql4_Zipcode extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the zipcode_id refers to the key field in your database table.
        $this->_init('zipcode/zipcode', 'id');
    }
}