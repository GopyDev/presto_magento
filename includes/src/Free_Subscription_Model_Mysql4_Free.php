<?php
class Free_Subscription_Model_Mysql4_Free extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("subscription/free", "id");
    }
}