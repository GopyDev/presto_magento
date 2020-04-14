<?php
class Customer_Track_Adminhtml_Report_TrackController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Reports'))
             ->_title($this->__('Track Report'));

        $this->loadLayout()
            ->_setActiveMenu('track/track')
            ->_addBreadcrumb(Mage::helper('reports')->__('Track Report'),
                Mage::helper('reports')->__('Track Report'))
            ->_addContent($this->getLayout()->createBlock('track/adminhtml_report_track'))
            ->renderLayout();
    }


    public function exportTrackCsvAction()
    {
        $fileName   = 'track_report.csv';
        $content    = $this->getLayout()->createBlock('track/adminhtml_report_track_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function exportTrackExcelAction()
    {
        $fileName   = 'track_report.xml';
        $content    = $this->getLayout()->createBlock('track/adminhtml_report_track_grid')
            ->getExcel($fileName);
        $this->_prepareDownloadResponse($fileName, $content);
    }

}