<?php
 
class Flexible_Discount_Adminhtml_DiscountController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Order Discount'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('flexible_discount/adminhtml_sales_discount'));
        $this->renderLayout();
    }
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('flexible_discount/adminhtml_sales_discount_grid')->toHtml()
        );
    }
 
    public function exportFlexibleCsvAction()
    {
        $fileName = 'discount_flexible.csv';
        $grid = $this->getLayout()->createBlock('flexible_discount/adminhtml_sales_discount_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportFlexibleExcelAction()
    {
        $fileName = 'orders_flexible.xml';
        $grid = $this->getLayout()->createBlock('flexible_discount/adminhtml_sales_discount_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}