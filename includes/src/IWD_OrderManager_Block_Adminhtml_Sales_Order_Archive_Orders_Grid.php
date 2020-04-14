<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Archive_Orders_Grid extends IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_archive_grid');
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_archive_orders';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if (!Mage::helper("iwd_ordermanager")->enableCustomGrid()){
            $collection = Mage::getModel('iwd_ordermanager/archive_order')->getArchiveOrdersCollection();
        } else {
            $filter = $this->prepareFilters();
            $collection = Mage::getModel('iwd_ordermanager/order_grid')->prepareCollection($filter, 'iwd_ordermanager/archive_order');
        }

        $this->setCollection($collection);
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {

        if (!Mage::helper("iwd_ordermanager")->enableCustomGrid()) {
            return parent::_prepareColumns();
        } else {
            $helper = Mage::helper('iwd_ordermanager');
            $grid = Mage::getModel('iwd_ordermanager/order_grid')->prepareColumns($this);
            $grid = Mage::getModel('iwd_ordermanager/order_grid')->addHiddenColumnWithStatus($grid);

            $grid->addRssList('rss/order/new', $helper->__('New Order RSS'));
            $grid->addExportType('*/*/exportCsv', $helper->__('CSV'));
            $grid->addExportType('*/*/exportExcel', $helper->__('Excel XML'));
            $grid->sortColumnsByOrder();
            return $grid;
        }
        /*

        $selected_columns_array = Mage::getModel('iwd_ordermanager/order')->SelectedColumnsArray();

        if (!Mage::helper("iwd_ordermanager")->enableCustomGrid()) {
            $selected_columns_array = array('real_order_id', 'ordered_products', 'store_id', 'created_at', 'billing_name', 'shipping_name', 'base_grand_total', 'grand_total', 'status', 'action');
        }

        Mage::getModel('iwd_ordermanager/grid_preparecolumns')->prepareColumns($selected_columns_array, $this);

        $this->addColumn('status-row', array(
            'index' => 'status',
            'type' => 'text',
            'filter' => false,
            'sortable' => false,
            'width' => '0px',
            'column_css_class' => 'no-display status-row',
            'header_css_class' => 'no-display',
        ));

        $this->addExportType('* / * /exportCsv/type/order', Mage::helper("iwd_ordermanager")->__('CSV'));
        $this->addExportType('* /* /exportExcel/type/order', Mage::helper("iwd_ordermanager")->__('Excel XML'));
        $this->sortColumnsByOrder();
        return $this;*/
    }


    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        if (Mage::getModel('iwd_ordermanager/order')->isAllowDeleteOrders()) {
            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('iwd_ordermanager')->__('Delete selected order(s)'),
                'url' => $this->getUrl('*/sales_orderr/delete', array('redirect' => 'sales_archive')),
                'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure?')
            ));
        }

        if (Mage::getModel('iwd_ordermanager/archive_order')->isAllowRestoreOrders()) {
            $this->getMassactionBlock()->addItem('restore', array(
                'label' => Mage::helper('iwd_ordermanager')->__('Restore selected order(s)'),
                'url' => $this->getUrl('*/*/restore'),
                'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure?')
            ));
        }

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/ordersGrid', array('_current' => true));
    }

    public function _toHtml(){
        $script = '<script type="text/javascript">
                      if(jQuery("#sales_order_archive_grid").length){
                        IWD.OrderManager.Grid.ColorGridRow();
                        if(jQuery.isFunction(jQuery.fn.stickyTableHeaders)){jQuery("#sales_order_archive_grid").stickyTableHeaders();}
                    }
                 </script>';

        return parent::_toHtml() . $script;
    }
}