<?php

/**
 * Atlas Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Atlas Commercial License
 * that is available through the world-wide-web at this URL:
 *
 * @copyright   Copyright (c) 2015 Atlas Extensions
 * @license     Commercial
 */
class Atlas_Advancedreports_Model_Mysql4_Report_Revenue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_from = null;
    protected $_to = null;
    protected $_orderStatus = null;
    protected $_period = null;
    protected $_storesIds = 0;
    protected $_applyFilters = true;
    protected $_isTotals = false;
    protected $_isSubTotals = false;
    protected $_aggregatedColumns = array();
    protected $_periodFormat;
    protected $_selectedColumns = array();
    protected $_timezone = null;
    protected $_locale = null;
    protected $_payment_method = null;

    /**
     * Initialize custom resource model
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/order');
        $this->setConnection($this->getResource()->getReadConnection());
    }

    /**
     * Set array of columns that should be aggregated
     *
     * @param array $columns
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }

    /**
     * Retrieve array of columns that should be aggregated
     *
     * @return array
     */
    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    /**
     * Set date range
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function setDateRange($from = null, $to = null)
    {
        $dateStart = $this->getLocale()->date($from, Zend_Date::ISO_8601, null, false);
        $this->_from = $this->timeshift($dateStart->toString('yyyy-MM-dd HH:mm:ss'));
        $dateEnd = $this->getLocale()->date($to, Zend_Date::ISO_8601, null, false);
        $this->_to = $this->timeshift($dateEnd->toString('yyyy-MM-dd 23:59:59'));
        return $this;
    }

    public function timeShift($datetime)
    {
        if ($this->_timezone == 'PST')
        {
            $dateObj = new Zend_Date($datetime, null, Mage::app()->getLocale()->getLocaleCode());
            $dateObj->setTimezone('America/Los_Angeles');
            $dateObj->set($datetime, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $dateObj->setTimezone('UTC');
            return $dateObj->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        elseif ($this->_timezone == 'PST4PM')
        {
            $dateObj = new Zend_Date($datetime, null, Mage::app()->getLocale()->getLocaleCode());
            $dateObj->setTimezone('EST');
            $dateObj->set($datetime, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $dateObj->setTimezone('America/Denver');
            return $dateObj->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        }
        else
        {//UTC
            return $datetime;
        }
    }

    /**
     * Set period
     *
     * @param string $period
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    protected function _applyUnionToSelect()
    {
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $this->getSelect()->columns(array('increment_id', 'created_at', 'subtotal', 'refunds' => '(0)', 'discount_amount', 'shipping_amount', 'tax_amount', 'grand_total'));
        $this->getSelect()->columns("('CHARGE') AS type");
        $this->getSelect()->columns(array("total_charges" => '(1)'));
        $this->getSelect()->columns(array("total_refunds" => '(0)'));
        //LEFT JOIN `sales_flat_order_payment` AS `payment_table` ON (`payment_table`.parent_id=`main_table`.entity_id)
        $this->getSelect()->joinLeft(array('payment_table' => 'sales_flat_order_payment'), 'payment_table.parent_id=main_table.entity_id', array());
        $this->getSelect()->where('created_at >= ?', $this->_from)
                ->where('created_at <= ?', $this->_to)
                ->where('status <> (?)', Mage_Sales_Model_Order::STATE_CANCELED)
                ->where('payment_table.method=?', $this->_payment_method);

        //Union with the credit memos
        $res = Mage::getSingleton('core/resource');
        $read = $this->getConnection('core_read');
        $mainTable = $res->getTableName('sales/creditmemo');

        $creditMemos = clone $this->getSelect();
        $creditMemos->reset()
                ->from(array('main' => $mainTable), array('increment_id',
                    'created_at',
                    'subtotal' => '(0)',
                    'refunds' => '(-main.subtotal)',
                    'discount_amount' => '(main.discount_amount)',
                    // 'giftcert_amount'=>'(-main.giftcert_amount)',
                    'main.shipping_amount' => '(-main.shipping_amount)',
                    'main.tax_amount' => '(-main.tax_amount)',
                    'main.grand_total' => '(-main.grand_total)'));

        $creditMemos->joinLeft(array('order_table' => 'sales_flat_order'), 'order_table.entity_id=main.order_id', array());
        $creditMemos->joinLeft(array('payment_table' => 'sales_flat_order_payment'), 'payment_table.parent_id=order_table.entity_id', array());

        $creditMemos->where('main.created_at >= ?', $this->_from)
                ->where('main.created_at <= ?', $this->_to)
                ->where('payment_table.method=?', $this->_payment_method);

        $creditMemos->columns("('REFUND') AS type");
        $creditMemos->columns(array("total_charges" => '(0)'));
        $creditMemos->columns(array("total_refunds" => '(1)'));

        $cloneOrders = clone $this->getSelect();
        $unionParts[] = '(' . $cloneOrders . ')';
        $unionParts[] = '(' . $creditMemos . ')';

        $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
        $this->getSelect()->order('created_at');

        if ($this->isTotals())
        {
            $union = clone $this->getSelect();

            $select = $this->getSelect()
                    ->reset()
                    ->from(array('tmp' => new Zend_Db_Expr('(' . (string) $union . ')')), $this->_aggregatedColumns);
        }
        $this->getSelect()->__toString();
        exit;


        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    protected function _applyDateRangeFilter()
    {
        return $this;
    }

    /**
     * Set store ids
     *
     * @param mixed $storeIds (null, int|string, array, array may contain null)
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    /**
     * Apply stores filter to select object
     *
     * @param Zend_Db_Select $select
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;

        if (!is_array($storeIds))
        {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds))
        {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck)
        {
            $select->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        }
        else
        {
            $select->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    protected function _applyStoresFilter()
    {
        return $this->_applyStoresFilterToSelect($this->getSelect());
    }

    /**
     * Set status filter
     *
     * @param string|array $state
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    protected function _applyOrderStatusFilter()
    {
        return $this;
    }

    /**
     * Set apply filters flag
     *
     * @param boolean $flag
     * @return Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function setApplyFilters($flag)
    {
        $this->_applyFilters = $flag;
        return $this;
    }

    /**
     * Getter/Setter for isTotals
     *
     * @param null|boolean $flag
     * @return boolean|Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag))
        {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;
        return $this;
    }

    /**
     * Getter/Setter for isSubTotals
     *
     * @param null|boolean $flag
     * @return boolean|Mage_Sales_Model_Mysql4_Report_Collection_Abstract
     */
    public function isSubTotals($flag = null)
    {
        if (is_null($flag))
        {
            return $this->_isSubTotals;
        }
        $this->_isSubTotals = $flag;
        return $this;
    }

    /**
     * Load data
     * Redeclare parent load method just for adding method _beforeLoad
     *
     * @return  Varien_Data_Collection_Db
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded())
        {
            return $this;
        }
        $this->_initSelect();
        if ($this->_applyFilters)
        {
            $this->_applyUnionToSelect();
            $this->_applyDateRangeFilter();
            //$this->_applyStoresFilter();
            $this->_applyOrderStatusFilter();
        }
        return parent::load($printQuery, $logQuery);
    }

    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $subSelect = clone $this->getSelect();
        $subSelect->reset(Zend_Db_Select::ORDER);

        $countSelect = clone $this->getSelect()->reset()->from(
                        array(
                            'tmp' => new Zend_Db_Expr('(' . (string) $subSelect . ')') //
                        )
        );

        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns('COUNT(*)');

        return $countSelect;
    }

    /**
     * Retrieve locale
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale)
        {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

    public function setTimezone($timezone)
    {
        $this->_timezone = $timezone;
        return $this;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->_payment_method = $paymentMethod;
        return $this;
    }

}
