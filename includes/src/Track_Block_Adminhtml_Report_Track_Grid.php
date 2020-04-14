<?php
class Customer_Track_Block_Adminhtml_Report_Track_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridTrackReport');
		$this->setTemplate('report/grid.phtml');
		$this->setSubReportSize(0);
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('track/track_collection');
    }

    protected function _prepareColumns()
    {

		        
		$this->addColumn('id', array(
            'header'    => $this->__('id'),
            'sortable'  => false,
            'index'     => 'id'
        ));        
		$this->addColumn('email', array(
            'header'    => $this->__('email'),
            'sortable'  => false,
            'index'     => 'email'
        ));        
		$this->addColumn('trackdevice', array(
            'header'    => $this->__('trackdevice'),
            'sortable'  => false,
            'index'     => 'trackdevice'
        ));
		$this->addColumn('created_at', array(
            'header'    => $this->__('created_at'),
            'sortable'  => false,
            'index'     => 'created_at'
        ));
		$this->addColumn('updated_at', array(
            'header'    => $this->__('updated_at'),
            'sortable'  => false,
            'index'     => 'updated_at'
        ));

		/*

		//demo code

        $this->addColumn('county', array(
            'header'    => $this->__('County'),
            'sortable'  => false,
            'index'     => 'county'
        ));

        $this->addColumn('city', array(
            'header'    => $this->__('City'),
            'sortable'  => false,
            'index'     => 'city'
        ));

        $baseCurrencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('tax_rate', array(
            'header'    => $this->__('Tax Rate'),
            'align'     => 'right',
            'sortable'  => false,
			'index'     => 'tax_rate',
            'type'      => 'text',
        ));

        $this->addColumn('tax_collected_amount', array(
            'header'    => $this->__('Tax Collected'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'tax_collected_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('taxed_sales_amount', array(
            'header'    => $this->__('Taxed Sales'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'taxed_sales_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('out_of_state_sales_amount', array(
            'header'    => $this->__('Out of State Sales'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'out_of_state_sales_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('non_taxable_sales_amount', array(
            'header'    => $this->__('Non-Taxable Sales'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'non_taxable_sales_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency'
        ));

        $this->addColumn('total_order_amount_amount', array(
            'header'    => $this->__('Total Order Amount'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'total_order_amount_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency'
        ));

		//demo code

		*/

        $this->addExportType('*/*/exportTrackCsv', Mage::helper('track')->__('CSV'));
        $this->addExportType('*/*/exportTrackExcel', Mage::helper('track')->__('Excel'));

        return parent::_prepareColumns();
    }

}