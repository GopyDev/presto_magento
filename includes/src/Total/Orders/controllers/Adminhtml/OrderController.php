<?php
 
class Total_Orders_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Customer Detail Report'));
        $this->loadLayout();
        //$this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('total_orders/adminhtml_sales_order'));
        $this->renderLayout();
    }
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('total_orders/adminhtml_sales_order_grid')->toHtml()
        );
    }
 
    public function exportInchooCsvAction()
    {
        $fileName = 'customer_orders.csv';
        $grid = $this->getLayout()->createBlock('total_orders/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportInchooExcelAction()
    {
        $fileName = 'customer_orders.xml';
        $grid = $this->getLayout()->createBlock('total_orders/adminhtml_sales_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}