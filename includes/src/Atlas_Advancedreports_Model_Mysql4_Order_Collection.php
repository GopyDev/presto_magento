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
class Atlas_Advancedreports_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection
{

    public function setDateRange($from, $to)
    {

        $storeidsarr = Mage::helper('advancedreports')->getNewstoreids();
        $storeidsarrstring = implode("','", $storeidsarr);
        $storeidsarrstringnew = "'$storeidsarrstring'";
        $this->_reset()
                ->addFieldToFilter('created_at', array('from' => $from, 'to' => $to))
                ->addFieldToFilter('state', array('nin' => array(Mage_Sales_Model_Order::STATE_CANCELED, Mage_Sales_Model_Order::STATE_HOLDED, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage_Sales_Model_Order::STATE_NEW)));
        if (!empty($storeidsarr) && isset($storeidsarr))
        {
            $this->addFieldToFilter('store_id', array('in' => array($storeidsarr)));
        }
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $this->getSelect()->columns(array('created_at', 'subtotal' => 'IFNULL(SUM(main_table.base_subtotal), 0)', 'shipping_amount' => 'IFNULL(SUM(main_table.base_shipping_amount), 0)', 'tax_amount' => 'IFNULL(SUM(main_table.base_tax_amount), 0)', 'discount_amount' => 'IFNULL(SUM(main_table.base_grand_total)-SUM(main_table.base_subtotal +  main_table.base_shipping_amount + main_table.base_tax_amount),0)', 'refunds' => '(0)', 'refunds_adjustment' => '(0)', 'grand_total' => 'IFNULL(SUM(main_table.base_grand_total), 0)'));
        $this->getSelect()->columns("('ORDER REVENUE') AS type");
        $this->getSelect()->columns("('-') AS refundtotal");

        //Union with the credit memos
        $res = Mage::getSingleton('core/resource');
        $read = $this->getConnection('core_read');
        $mainTable = $res->getTableName('sales/creditmemo');
        $rescm = Mage::getSingleton('core/resource');
        $readConnectioncm = $rescm->getConnection('core_read');
        $mainTablecm = $rescm->getTableName('sales/creditmemo');
        $mainTablesf = $rescm->getTableName('sales/order');
        if (!empty($storeidsarr) && isset($storeidsarr))
        {
            $querysfgrandtotal = "SELECT IFNULL(SUM(base_grand_total), 0) AS grandtotalorder FROM  $mainTablesf WHERE created_at >= '$from' AND created_at < '$to' AND (state NOT IN('canceled', 'holded', 'pending_payment', 'new')) AND (store_id IN ($storeidsarrstringnew))";
        }
        else
        {
            $querysfgrandtotal = "SELECT IFNULL(SUM(base_grand_total), 0) AS grandtotalorder FROM  $mainTablesf WHERE created_at >= '$from' AND created_at < '$to' AND (state NOT IN('canceled', 'holded', 'pending_payment', 'new'))";
        }

        if (!empty($storeidsarr) && isset($storeidsarr))
        {
            $querycreditmemograndtotal = "SELECT IFNULL(SUM(base_grand_total), 0) AS grandtotal FROM  $mainTablecm WHERE created_at >= '$from' AND created_at < '$to' AND (store_id IN ($storeidsarrstringnew)) ";
        }
        else
        {
            $querycreditmemograndtotal = "SELECT IFNULL(SUM(base_grand_total), 0) AS grandtotal FROM  $mainTablecm WHERE created_at >= '$from' AND created_at < '$to'";
        }
        $ordergrandtotal = $readConnectioncm->fetchOne($querysfgrandtotal);
        $creditmemograndtotal = $readConnectioncm->fetchOne($querycreditmemograndtotal);
        $netrevnue = $ordergrandtotal - $creditmemograndtotal;
        if ($netrevnue == 0)
        {
            $netrevnue = 'null';
        }
        $creditMemos = clone $this->getSelect();
        $creditMemos->reset()
                ->from(array('main' => $mainTable), array('created_at', 'subtotal' => '(0)', 'shipping_amount' => 'IFNULL(SUM(main.base_shipping_amount), 0)', 'tax_amount' => 'IFNULL(SUM(main.base_tax_amount), 0)', 'discount_amount' => 'IFNULL(SUM(main.base_discount_amount), 0)', 'refunds' => 'IFNULL(SUM(main.base_subtotal), 0)', 'refunds_adjustment' => 'IFNULL(SUM(main.base_adjustment_positive) - SUM(main.base_adjustment_negative),0 )', 'grand_total' => 'IFNULL(SUM(-main.base_grand_total), 0)'))
                ->where('created_at > ?', $from)
                ->where('created_at < ?', $to);
        if (!empty($storeidsarr) && isset($storeidsarr))
        {
            $creditMemos->where('store_id IN (?)', (array) $storeidsarr);
        }

        $creditMemos->columns("('REFUND') AS type");
        $creditMemos->columns("('$netrevnue') AS refundtotal");
        $cloneOrders = clone $this->getSelect();
        $unionParts[] = '(' . $cloneOrders . ')';
        $unionParts[] = '(' . $creditMemos . ')';
        $this->getSelect()->reset()->union($unionParts, Zend_Db_Select::SQL_UNION_ALL);
        $this->getSelect()->order('type', 'ASC');

        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Mysql4_Product_Collection
     */
    public function setStoreIds($storeIds)
    {
        return $this;
    }

}
