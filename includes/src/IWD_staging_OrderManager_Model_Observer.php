<?php
class IWD_OrderManager_Model_Observer
{
    /************************ CHECK REQUIRED MODULES *************************/
    public function checkRequiredModules($observer)
    {
        $cache = Mage::app()->getCache();

        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                if ($cache->load("iwd_order_manager") === false) {
                    $message = 'Important: Please setup IWD_ALL in order to finish <strong>IWD Order Manager</strong> installation.<br />
					Please download <a href="http://iwdextensions.com/media/modules/iwd_all.tgz" target="_blank">IWD_ALL</a> and set it up via Magento Connect.<br />
					Please refer link to <a href="https://docs.google.com/document/d/1UjKKMBoJlSLPru6FanetfEBI5GlOdVjQOM_hb3kn8p0/edit" target="_blank">installation guide</a>';

                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_order_manager', array("iwd_order_manager"), $lifeTime = 5);
                }
            }
        }
    }
    /******************************************* end CHECK REQUIRED MODULES **/


    /************************** MASSACTION EVENT *****************************/
    public function orderManagerObserver($observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($this->_orderDelete($block) | $this->_orderArchive($block)) return;
        if ($this->_invoiceDelete($block)) return;
        if ($this->_shipmentDelete($block)) return;
        if ($this->_creditmemoDelete($block)) return;
    }
    /************************************************* end MASSACTION EVENT **/


    /****************************** DELETE ***********************************/
    private function _orderDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/order')->isAllowDeleteOrders()) {
            if ($block->getId() == 'sales_order_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem('iwd_delete_orders', array(
                        'label' => Mage::helper('iwd_ordermanager')->__('Delete selected order(s)'),
                        'url' => Mage::helper('adminhtml')->getUrl('*/sales_orderr/delete', array('redirect' => 'sales_order')),
                        'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure to delete the selected sales order(s)?  (Related Invoices, Shipments & Credit memos will be deleted too!)'),
                    ));
                }

                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_View') {
                $order_id = $block->getRequest()->getParam('order_id');
                if (Mage::getModel('iwd_ordermanager/order')->load($order_id)->canDelete()) {
                    $block->addButton('delete', array(
                        'label' => Mage::helper('adminhtml')->__('Delete'),
                        'class' => 'delete',
                        'onclick' =>
                            'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure to delete this order? (Related Invoices, Shipments & Credit memos will be deleted too!)') . '\', \''
                            . Mage::helper('adminhtml')->getUrl('*/sales_orderr/delete', array('order_ids' => $order_id, 'redirect' => 'sales_order')) . '\')',
                    ), -1, 11);
                }
                return true;
            }
        }
        return false;
    }

    private function _invoiceDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/invoice')->isAllowDeleteInvoices()) {
            if ($block->getId() == 'sales_invoice_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $confirm = (Mage::getModel('iwd_ordermanager/invoice')->allowDeleteRelatedCreditMemo()) ?
                        Mage::helper('adminhtml')->__('Are you sure to delete the selected invoice(s)? Attention: All related credit memo(s) will be removed too!') :
                        Mage::helper('adminhtml')->__('Are you sure to delete the selected invoice(s)? Invoice(s) with related credit memo(s) will not be removed.');
                    $massactionBlock->addItem('iwd_delete_invoices', array(
                        'label' => Mage::helper('iwd_ordermanager')->__('Delete selected invoice(s)'),
                        'url' => Mage::helper('adminhtml')->getUrl('*/sales_invoice/delete'),
                        'confirm' => $confirm,
                    ));
                }
                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Invoice_View') {
                $invoice_id = $block->getRequest()->getParam('invoice_id');
                if (Mage::getModel('iwd_ordermanager/invoice')->load($invoice_id)->canDelete()) {
                    $block->addButton('delete', array(
                        'label' => Mage::helper('adminhtml')->__('Delete'),
                        'class' => 'delete',
                        'onclick' =>
                            'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure to delete this invoice?') . '\', \''
                            . Mage::helper('adminhtml')->getUrl('*/sales_invoice/delete', array('invoice_ids' => $invoice_id)) . '\')',
                    ), -1, 11);
                }
                return true;
            }
        }
        return false;
    }

    private function _creditmemoDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/creditmemo')->isAllowDeleteCreditmemos()) {
            if ($block->getId() == 'sales_creditmemo_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem('iwd_delete_creditmemo', array(
                        'label' => Mage::helper('iwd_ordermanager')->__('Delete selected credit memo(s)'),
                        'url' => Mage::helper('adminhtml')->getUrl('*/sales_creditmemo/delete'),
                        'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure to delete the selected credit memo(s)?'),
                    ));
                }
                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Creditmemo_View') {
                $creditmemo_id = $block->getRequest()->getParam('creditmemo_id');
                if (Mage::getModel('iwd_ordermanager/creditmemo')->load($creditmemo_id)->canDelete()) {
                    $block->addButton('delete', array(
                        'label' => Mage::helper('adminhtml')->__('Delete'),
                        'class' => 'delete',
                        'onclick' =>
                            'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure to delete this credit memo?') . '\', \''
                            . Mage::helper('adminhtml')->getUrl('*/sales_creditmemo/delete', array('creditmemo_ids' => $creditmemo_id)) . '\')',
                    ), -1, 11);
                }
                return true;
            }
        }
        return false;
    }

    private function _shipmentDelete($block)
    {
        if (Mage::getModel('iwd_ordermanager/shipment')->isAllowDeleteShipments()) {
            if ($block->getId() == 'sales_shipment_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem('iwd_delete_orders', array(
                        'label' => Mage::helper('iwd_ordermanager')->__('Delete selected shipment(s)'),
                        'url' => Mage::helper('adminhtml')->getUrl('*/sales_shipment/delete'),
                        'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure to delete the selected shipment(s)?'),
                    ));
                }
                return true;
            }

            if (get_class($block) == 'Mage_Adminhtml_Block_Sales_Order_Shipment_View') {
                $shipment_id = $block->getRequest()->getParam('shipment_id');
                $block->addButton('delete', array(
                    'label' => Mage::helper('adminhtml')->__('Delete'),
                    'class' => 'delete',
                    'onclick' =>
                        'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure to delete this shipment?') . '\', \''
                        . Mage::helper('adminhtml')->getUrl('*/sales_shipment/delete', array('shipment_ids' => $shipment_id)) . '\')',
                ), -1, 11);

                return true;
            }
        }
        return false;
    }
    /*********************************************************** end DELETE **/


    /****************************** ARCHIVE **********************************/
    private function _orderArchive($block)
    {
        if (Mage::getModel('iwd_ordermanager/archive_order')->isAllowArchiveOrders()) {
            if ($block->getId() == 'sales_order_grid') {
                $massactionBlock = $block->getMassactionBlock();
                if ($massactionBlock) {
                    $massactionBlock->addItem('iwd_archive_orders', array(
                        'label' => Mage::helper('iwd_ordermanager')->__('Archive selected order(s)'),
                        'url' => Mage::helper('adminhtml')->getUrl('*/sales_archive/archive'),
                        'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure archive orders?'),
                    ));
                }

                return true;
            }
        }
        return false;
    }
    /********************************************************** end ARCHIVE **/


    /*********************** DELETE: CREATE BACKUPS **************************/
    public function orderDelete($observer)
    {
        $obj = $observer->getEvent()->getOrder();
        $items = $observer->getEvent()->getOrderItems();
        Mage::getModel('iwd_ordermanager/backup_sales')->SaveBackup($obj, $items, 'order');
    }

    public function invoiceDelete($observer)
    {
        $obj = $observer->getEvent()->getInvoice();
        $items = $observer->getEvent()->getInvoiceItems();
        Mage::getModel('iwd_ordermanager/backup_sales')->SaveBackup($obj, $items, 'invoice');
    }

    public function creditmemoDelete($observer)
    {
        $obj = $observer->getEvent()->getCreditmemo();
        $items = $observer->getEvent()->getCreditmemoItems();
        Mage::getModel('iwd_ordermanager/backup_sales')->SaveBackup($obj, $items, 'creditmemo');
    }

    public function shipmentDelete($observer)
    {
        $obj = $observer->getEvent()->getShipment();
        $items = $observer->getEvent()->getShipmentItems();
        Mage::getModel('iwd_ordermanager/backup_sales')->SaveBackup($obj, $items, 'shipment');
    }

    public function commentDelete($observer)
    {
        $comment = $observer->getEvent()->getComment();
        $type = $observer->getEvent()->getType();
        Mage::getModel('iwd_ordermanager/backup_comments')->SaveBackup($comment, $type);
    }
    /******************************************* end DELETE: CREATE BACKUPS **/


    /**************************** CRON ARCHIVE ORDERS ************************/
    public function scheduledArchiveOrders(){
        $archive_order = Mage::getModel('iwd_ordermanager/archive_order');

        $order_ids = $archive_order->getOldOrdersForArchive();
        $order_ids = array_chunk($order_ids, IWD_OrderManager_Model_Archive_Order::MAX_ORDERS_IN_ONE_TRANSACTION);

        foreach($order_ids as $ids){
            $archive_order->addOrdersToArchive($ids, false);
        }
    }
    /********************************************** end CRON ARCHIVE ORDERS **/
}