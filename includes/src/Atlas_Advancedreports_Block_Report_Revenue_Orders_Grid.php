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
class Atlas_Advancedreports_Block_Report_Revenue_Orders_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    /**
     * Sub report size
     *
     * @var int
     */
    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridRevenueOrders');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setTemplate('atlas/advancedreports/grid.phtml');
    }

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {
        self::_prepareCollectionPre();

        $this->getCollection()
                ->initReport('advancedreports/order_collection');

        return $this;
    }

    protected function _prepareCollectionPre()
    {

        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter))
        {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter))
        {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            if (!isset($data['report_from']))
            {
                $date = new Zend_Date(mktime(0, 0, 0, 1, 1, 2001));
                $data['report_from'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            if (!isset($data['report_to']))
            {
                $date = new Zend_Date();
                $data['report_to'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            $this->_setFilterValues($data);
        }
        else if ($filter && is_array($filter))
        {
            $this->_setFilterValues($filter);
        }
        else if (0 !== sizeof($this->_defaultFilter))
        {
            $this->_setFilterValues($this->_defaultFilter);
        }

        $collection = Mage::getResourceModel('advancedreports/report_collection');

        $collection->setPeriod($this->getFilter('report_period'));

        $collection->setIsUTC($this->getFilter('timezone'));


        if ($this->getFilter('report_from') && $this->getFilter('report_to'))
        {
            /**
             * Validate from and to date
             */
            try {
                $from = $this->getLocale()->date($this->getFilter('report_from'), Zend_Date::DATE_SHORT, null, false);
                $to = $this->getLocale()->date($this->getFilter('report_to'), Zend_Date::DATE_SHORT, null, false);

                $collection->setInterval($from, $to);
            } catch (Exception $e) {
                $this->_errors[] = Mage::helper('reports')->__('Invalid date specified.');
            }
        }

        /**
         * Getting and saving store ids for website & group
         */
        $storeIds = array();
        if ($this->getRequest()->getParam('store'))
        {
            $storeIds = array($this->getParam('store'));
        }
        elseif ($this->getRequest()->getParam('website'))
        {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        }
        elseif ($this->getRequest()->getParam('group'))
        {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        }
        $collection->setStoreIds($storeIds);

        if ($this->getSubReportSize() !== null)
        {
            $collection->setPageSize($this->getSubReportSize());
        }

        $this->setCollection($collection);

        Mage::dispatchEvent('adminhtml_widget_grid_filter_collection', array('collection' => $this->getCollection(), 'filter_values' => $this->_filterValues)
        );
    }

    public function getIsUTC()
    {
        return $this->getCollection()->getIsUTC();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('type', array(
            'header' => Mage::helper('advancedreports')->__('Transaction Type'),
            'index' => 'type',
        ));

        $this->addColumn('subtotal', array(
            'header' => Mage::helper('sales')->__('Order Subtotal'),
            'index' => 'subtotal',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));

        $this->addColumn('refunds', array(
            'header' => Mage::helper('sales')->__('Refunds Subtotal'),
            'index' => 'refunds',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));

        $this->addColumn('shipping_amount', array(
            'header' => Mage::helper('sales')->__('Order Shipping'),
            'index' => 'shipping_amount',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));

        $this->addColumn('tax_amount', array(
            'header' => Mage::helper('sales')->__('Order Tax'),
            'index' => 'tax_amount',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));
        $this->addColumn('discount_amount', array(
            'header' => Mage::helper('sales')->__('Order Discount'),
            'index' => 'discount_amount',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));

        $this->addColumn('refunds_adjustment', array(
            'header' => Mage::helper('sales')->__('Adjustment Refund'),
            'index' => 'refunds_adjustment',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('Grand Total'),
            'index' => 'grand_total',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'inline_css' => 'qty',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));


        $this->addColumn('refundtotal', array(
            'header' => Mage::helper('advancedreports')->__('Net Revenue'),
            'index' => 'refundtotal',
            'type' => 'currency',
            'header_css_class' => 'a-right',
            'currency_code' => $currencyCode,
            'total' => 'sum',
            'frame_callback' => array($this, 'decorateStatus'),
            'sortable' => false
        ));
        $this->addExportType('*/*/exportOrdersCsv', Mage::helper('advancedreports')->__('CSV'));
        $this->addExportType('*/*/exportOrdersExcel', Mage::helper('advancedreports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $pos = strpos($value, '-');

        $return = $value;

        if (empty($isExport))
        {
            if ($pos !== false)
            {
                // $return = '<span class="negative">' . $value.'</span>';
                $return = $value;
            }
        }

        return $return;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridorder', array('_current' => true));
    }

}
