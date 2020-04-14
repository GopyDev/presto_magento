<?php
 
class Survey_Details_Adminhtml_DetailsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Order Details'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('survey_details/adminhtml_sales_details'));
        $this->renderLayout();
    }
 
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('survey_details/adminhtml_sales_details_grid')->toHtml()
        );
    }
 
    public function exportSurveyCsvAction()
    {
        $fileName = 'details_survey.csv';
        $grid = $this->getLayout()->createBlock('survey_details/adminhtml_sales_details_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportSurveyExcelAction()
    {
        $fileName = 'orders_survey.xml';
        $grid = $this->getLayout()->createBlock('survey_details/adminhtml_sales_details_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}