<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    protected function _prepareCollection()
    {
        if (!Mage::helper("iwd_ordermanager")->enableCustomGrid()){
            return parent::_prepareCollection();
        }

        $filter = $this->prepareFilters();
        $collection = Mage::getModel('iwd_ordermanager/order_grid')->prepareCollection($filter);
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
    }

    protected function prepareFilters(){
        $filter   = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $filter = $this->helper('adminhtml')->prepareFilterString($filter);
        }

        return $filter;
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view'))
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function _toHtml()
    {
        $script = '<script type="text/javascript">
                      if(jQuery("#sales_order_grid_table").length) {
                        IWD.OrderManager.Grid.ColorGridRow();
                        if(jQuery.isFunction(jQuery.fn.stickyTableHeaders)){jQuery("#sales_order_grid_table").stickyTableHeaders();}
                      }
                 </script>';

        return parent::_toHtml() . $script .  $this->updateFilters();
    }

    protected function updateFilters(){
        $is_limit_period = Mage::getModel('iwd_ordermanager/order_grid')->isLimitPeriod();
        if(!$is_limit_period){
            return "";
        }

        $from = Mage::getSingleton('adminhtml/session')->getData("created_at_from");
        $to = Mage::getSingleton('adminhtml/session')->getData("created_at_to");

        return '<script type="text/javascript">
                      jQuery("input[name=\'created_at[from]\']").val("'.$from.'");
                      jQuery("input[name=\'created_at[to]\']").val("'.$to.'");
                 </script>';
    }
}