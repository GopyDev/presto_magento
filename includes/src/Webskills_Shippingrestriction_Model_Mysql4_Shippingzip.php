<?php
class Webskills_Shippingrestriction_Model_Mysql4_Shippingzip extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("shippingrestriction/shippingzip", "id");
    }
}