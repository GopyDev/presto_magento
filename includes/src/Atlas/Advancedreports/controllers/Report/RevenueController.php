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
class Atlas_Advancedreports_Report_RevenueController extends Mage_Adminhtml_Controller_Action
{

    /**
     * init
     *
     * @return Mage_Adminhtml_Report_ProductController
     */
   protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'orders':
                return Mage::getSingleton('admin/session')->isAllowed('general/revenue/orders');
                break;
            case 'product':
                return Mage::getSingleton('admin/session')->isAllowed('general/revenue/product');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('general/revenue');
                break;
        }
    }
 
    public function _initAction()
    {
        $act = $this->getRequest()->getActionName();
        if (!$act)
            $act = 'default';
        if ($act == 'orders')
        {
            $this->loadLayout()
                    ->_addBreadcrumb(Mage::helper('advancedreports')->__('Reports'), Mage::helper('advancedreports')->__('Reports'))
                    ->_addBreadcrumb(Mage::helper('advancedreports')->__('Revenue'), Mage::helper('advancedreports')->__('Revenue'));
        }
        if ($act == 'product')
        {
            $this->loadLayout()
                    ->_addBreadcrumb(Mage::helper('advancedreports')->__('Reports'), Mage::helper('advancedreports')->__('Reports'))
                    ->_addBreadcrumb(Mage::helper('advancedreports')->__('Product'), Mage::helper('advancedreports')->__('Product'));
        }
        return $this;
    }

    public function _initReportAction($blocks)
    {

        if (!is_array($blocks))
        {
            $blocks = array($blocks);
        }

        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, array('from', 'to'));
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();

        foreach ($requestData as $key => $value)
        {
            if (!empty($value))
            {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block)
        {
            if ($block)
            {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }

    /**
     * Export Revenue product report to CSV format action
     *
     */
    public function ordersAction()
    {
        $this->_title($this->__('Reports'))
                ->_title($this->__('Revenue'))
                ->_title($this->__('Revenue Report'));

        $this->_initAction()
                ->_setActiveMenu('general/revenue/orders')
                ->_addBreadcrumb(Mage::helper('advancedreports')->__('Revenue Report'), Mage::helper('advancedreports')->__('Revenue Report'))
                ->_addContent($this->getLayout()->createBlock('advancedreports/report_revenue_orders'))
                ->renderLayout();
    }

    /**
     * Export Sold Products report to CSV format action
     *
     */
    public function exportOrdersCsvAction()
    {
        $fileName = 'revenue_report.csv';
        $content = $this->getLayout()
                ->createBlock('advancedreports/report_revenue_orders_grid')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportOrdersExcelAction()
    {
        $fileName = 'revenue_report.xml';
        $content = $this->getLayout()
                ->createBlock('advancedreports/report_revenue_orders_grid')
                ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function productAction()
    {
        $this->_title($this->__('Reports'))
                ->_title($this->__('Product'))
                ->_title($this->__('Product  Report'));

        $this->_initAction()
                ->_setActiveMenu('general/revenue/product')
                ->_addBreadcrumb(Mage::helper('advancedreports')->__('Product Report'), Mage::helper('advancedreports')->__('Product Report'))
                ->_addContent($this->getLayout()->createBlock('advancedreports/report_revenue_product'))
                ->renderLayout();
    }

    public function exportProductCsvAction()
    {
        $fileName = 'products_ordered.csv';
        $content = $this->getLayout()
                ->createBlock('advancedreports/report_revenue_product_grid')
                ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Sold Products report to XML format action
     *
     */
    public function exportProductExcelAction()
    {
        $fileName = 'products_ordered.xml';
        $content = $this->getLayout()
                ->createBlock('advancedreports/report_revenue_product_grid')
                ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check is allowed for report
     *
     * @return bool
     */
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/report_revenue_product_grid')->toHtml()
        );
    }

    public function gridorderAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('advancedreports/report_revenue_orders_grid')->toHtml()
        );
    }

}
