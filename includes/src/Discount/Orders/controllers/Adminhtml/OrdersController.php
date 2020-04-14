<?php
 
class Discount_Orders_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Orders Discount'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('discount_orders/adminhtml_sales_orders'));
        $this->renderLayout();
    }
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('discount_orders/adminhtml_sales_orders_grid')->toHtml()
        );
    }
 
    public function exportDiscountCsvAction()
    {
        $fileName = 'orders_discount.csv';
        $grid = $this->getLayout()->createBlock('discount_orders/adminhtml_sales_orders_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportDiscountExcelAction()
    {
        $fileName = 'orders_discount.xml';
        $grid = $this->getLayout()->createBlock('discount_orders/adminhtml_sales_orders_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}