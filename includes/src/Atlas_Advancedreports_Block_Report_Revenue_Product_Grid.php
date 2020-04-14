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
class Atlas_Advancedreports_Block_Report_Revenue_Product_Grid extends Mage_Adminhtml_Block_Report_Grid
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
        $this->setFilterVisibility(true);
        $this->setPagerVisibility(true);
        $this->setId('revanueProductsSold');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(1);
        $this->setTemplate('atlas/advancedreports/gridproduct.phtml');
    }

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('advancedreports/product_collection');
        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
    	$this->addColumn('sku', array(
            'header' => Mage::helper('reports')->__('Product SKU'),
            'index' => 'order_items_sku'
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('reports')->__('Product Name'),
            'index' => 'order_items_name'
        ));

        $this->addColumn('ordered_qty', array(
            'header' => Mage::helper('reports')->__('Quantity Ordered'),
            'width' => '100px',
            'align' => 'right',
            'header_css_class' => 'a-right',
            'index' => 'ordered_qty',
            'total' => 'sum',
            'type' => 'number'
        ));
        $this->addColumn('invoiced_qty', array(
            'header' => Mage::helper('reports')->__('Quantity Invoiced'),
            'width' => '100px',
            'header_css_class' => 'a-right',
            'align' => 'right',
            'index' => 'invoiced_qty',
            'total' => 'sum',
            'type' => 'number'
        ));
        $this->addColumn('refunded_qty', array(
            'header' => Mage::helper('reports')->__('Quantity Refunded'),
            'width' => '100px',
            'header_css_class' => 'a-right',
            'align' => 'right',
            'index' => 'refunded_qty',
            'total' => 'sum',
            'type' => 'number'
        ));
        $this->addColumn('shipped_qty', array(
            'header' => Mage::helper('reports')->__('Quantity Shipped'),
            'width' => '100px',
            'header_css_class' => 'a-right',
            'align' => 'right',
            'index' => 'shipped_qty',
            'total' => 'sum',
            'type' => 'number'
        ));
        $this->addColumn('base_price', array(
            'header' => Mage::helper('reports')->__('Product Base Price'),
            'width' => '100px',
            'header_css_class' => 'a-right',
            'index' => 'base_price',
            'align' => 'right',
            'type' => 'number'
        ));
        $this->addColumn('uniquepurchase', array(
            'header' => Mage::helper('reports')->__('Unique Product Purchased'),
            'width' => '150px',
            'header_css_class' => 'a-right',
            'index' => 'uniquepurchase',
            'align' => 'right',
            'type' => 'number'
        ));

        $this->addColumn('base_price_total', array(
            'header' => Mage::helper('reports')->__('Total'),
            'width' => '60px',
            'header_css_class' => 'a-right',
            'index' => 'base_price_total',
            'align' => 'right',
            'total' => 'sum',
            'type' => 'number'
        ));

        $this->addExportType('*/*/exportProductCsv', Mage::helper('advancedreports')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('advancedreports')->__('Excel XML'));



        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
