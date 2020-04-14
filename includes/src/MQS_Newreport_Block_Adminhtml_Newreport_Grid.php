<?php
class MQS_Newreport_Block_Adminhtml_Newreport_Grid  extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('newreportGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }
 
    protected function _prepareCollection() {
        parent::_prepareCollection();
        $this->getCollection()->initReport('newreport/newreport');
        return $this;
    }
 
    protected function _prepareColumns() {
		
		 $this->addColumn('entity_id', array(
            'header' => Mage::helper('newreport')->__('Product ID'),
            'align' => 'right',
            'index' => 'entity_id',
            'type'  => 'number',
        ));
	
		$this->addColumn('sku', array(
            'header' => Mage::helper('newreport')->__('Product SKU'),
            'width' => '120px',
			'align' => 'right',
            'index' => 'sku',
        ));
		
		$this->addColumn('name', array(
            'header' => Mage::helper('newreport')->__('Product Name'),
            'width' => '120px',
			'align' => 'right',
            'index' => 'name',
        ));
		
		
		$this->addColumn('name', array(
            'header' => Mage::helper('newreport')->__('Product Name'),
            'width' => '120px',
			'align' => 'right',
            'index' => 'name',
        ));
		
		$this->addColumn('qty', array(
            'header' => Mage::helper('newreport')->__('Qty In Stock'),
            'width' => '120px',
			'align' => 'right',
            'index' => 'qty',
			'type'      =>'number'
        ));
		
		$this->addColumn('viewed', array(
            'header' => Mage::helper('newreport')->__('Product Viewes'),
            'width' => '120px',
			'align' => 'right',
            'index' => 'viewed',
			'type'  =>'number'
        ));
		
		$this->addColumn('ordered', array(
            'header' => Mage::helper('newreport')->__('Product Ordered'),
            'width' => '120px',
			'align' => 'right',
            'index' => 'ordered',
			'type'  =>'number'
        ));
		
       
       
        $this->addExportType('*/*/exportCsv', Mage::helper('newreport')->__('CSV'));
        return parent::_prepareColumns();
    }
 
    public function getRowUrl($row) {
        return false;
    }
 
    public function getReport($from, $to) {
        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }
        $totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);
        return $this->getCollection()->getReport($from, $to);
    }
}
