<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Transaction Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Mysql4_Transaction_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/transaction');
    }
    
    /**
     * add availabel filter for transactions collection
     * 
     * @return Magestore_RewardPoints_Model_Mysql4_Transaction_Collection
     */
    public function addAvailableBalanceFilter()
    {
        $this->getSelect()->where('point_amount > point_used');
        return $this;
    }
    
    /**
     * get total by field of this collection
     * 
     * @param string $field
     * @return number
     */
    public function getFieldTotal($field = 'point_amount')
    {
        $this->_renderFilters();
        
        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(Zend_Db_Select::ORDER);
        $sumSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $sumSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $sumSelect->reset(Zend_Db_Select::COLUMNS);
        
        $sumSelect->columns("SUM(`$field`)");
        
        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }
}
