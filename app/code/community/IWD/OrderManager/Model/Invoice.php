<?php
class IWD_OrderManager_Model_Invoice extends Mage_Sales_Model_Order_Invoice
{
    const XML_PATH_SALES_ALLOW_DEL_INVOICES = 'iwd_ordermanager/iwd_delete_invoices/allow_del_invoices';
    const XML_PATH_SALES_ALLOW_DEL_RELATED_CREDITMEMOS = 'iwd_ordermanager/iwd_delete_invoices/allow_del_related_cm_for_invoices';
    const XML_PATH_SALES_STATUS_INVOICE = 'iwd_ordermanager/iwd_delete_invoices/invoice_status';
    const XML_PATH_SALES_CREATE_INVOICE = 'iwd_ordermanager/edit/create_invoice';

    public function isAllowDeleteInvoices()
    {
        $conf_allow = Mage::getStoreConfig(self::XML_PATH_SALES_ALLOW_DEL_INVOICES, Mage::app()->getStore());
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/invoice/actions/delete');
        $engine = Mage::helper('iwd_ordermanager')->CheckInvoiceTableEngine();
        return ($conf_allow && $permission_allow && $engine);
    }

    public function getInvoiceStatusesForDeleteIds()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_STATUS_INVOICE));
    }

    public function checkInvoiceStatusForDeleting()
    {
        return (in_array($this->getState(), $this->getInvoiceStatusesForDeleteIds()));
    }

    public function canDelete()
    {
        return ($this->isAllowDeleteInvoices() && $this->checkInvoiceStatusForDeleting());
    }

    public function allowDeleteRelatedCreditMemo()
    {
        return Mage::getStoreConfig(self::XML_PATH_SALES_ALLOW_DEL_RELATED_CREDITMEMOS);
    }

    public function DeleteInvoice()
    {
        $increment_id = $this->getIncrementId();

        if (!$this->canDelete()) {
            $message = 'Maybe, you can not delete items with some statuses. Please, check <a href="'
                . Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array("section" => "iwd_ordermanager"))
                . '" target="_blank" title="System - Configuration - IWD Extensions - Order Manager">configuration</a> of IWD OrderManager';

            Mage::getSingleton('iwd_ordermanager/logger')->addNoticeMessage('check_invoice_status', $message);

            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('invoice', $increment_id);
            return false;
        }

        Mage::dispatchEvent('iwd_ordermanager_sales_invoice_delete_after', array('invoice' => $this, 'invoice_items' => $this->getItemsCollection()));

        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        Mage::getSingleton('iwd_ordermanager/report')
            ->addInvoicedPeriod($this->getCreatedAt(), $this->getUpdatedAt(), $order->getCreatedAt());

        if ($order->hasCreditmemos()) {
            if (!$this->allowDeleteRelatedCreditMemo()) {
                Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('invoice', $increment_id);

                $message =  'Invoice has related credit memo(s). You must delete all related credit memo(s) after deleting invoice. Please, check <a href="'
                    . Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array("section" => "iwd_ordermanager"))
                    . '" target="_blank" title="System - Configuration - IWD Extensions - Order Manager - Delete Invoice">configuration</a> of IWD OrderManager';

                Mage::getSingleton('iwd_ordermanager/logger')->addNoticeMessage('related_credit_memo', $message);

                return false;
            }

            $credit_memos = $order->getCreditmemosCollection();
            foreach ($credit_memos as $credit_memo)
                $creditmemo_deleted = Mage::getModel('iwd_ordermanager/creditmemo')
                    ->load($credit_memo->getEntityId())
                    ->DeleteCreditmemo();

                if (!$creditmemo_deleted) {
                    Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('invoice', $increment_id);
                    return false;
                }
        }

        if (!$this->isCanceled()) //not Cancled
            $this->cancel()->save()->getOrder()->save();

        Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('invoice', $increment_id);

        $items = $this->getItemsCollection();
        $obj = $this;

        $this->delete();

        Mage::dispatchEvent('iwd_ordermanager_sales_invoice_delete_before', array('invoice' => $obj, 'invoice_items' => $items));

        return $increment_id;
    }

    public function CreateInvoice($order_id, $qtys = array(), $base_shipping_amount = 0, $base_shipping_incl_tax = 0)
    {
        $order = Mage::getModel("sales/order")->load($order_id);

        if (!$order->getId())
            Mage::throwException(Mage::helper('core')->__('Order not exists'));

        // if (!$order->canInvoice())
        //     Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));

        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($qtys);
	Mage::log($invoice->getTotalQty(),null,'invoiceqty2.log');
        if ($base_shipping_amount > 0) {

            $base_shipping_tax_amount = $base_shipping_incl_tax - $base_shipping_amount;

            $base_currency_code = $order->getBaseCurrencyCode();
            $order_currency_code = $order->getOrderCurrencyCode();
            $directory = Mage::helper('directory');
            if($base_currency_code === $order_currency_code){
                $shipping_amount = $base_shipping_amount;
                $shipping_incl_tax = $base_shipping_incl_tax;
                $shipping_tax_amount = $base_shipping_tax_amount;
            }else{
                $shipping_amount = $directory->currencyConvert($base_shipping_amount, $base_currency_code, $order_currency_code);
                $shipping_incl_tax = $directory->currencyConvert($base_shipping_incl_tax, $base_currency_code, $order_currency_code);
                $shipping_tax_amount = $directory->currencyConvert($base_shipping_tax_amount, $base_currency_code, $order_currency_code);
            }

            $invoice->setShippingTaxAmount($shipping_tax_amount);
            $invoice->setBaseShippingTaxAmount($base_shipping_tax_amount);

            $invoice->setShippingAmount($shipping_amount);
            $invoice->setBaseShippingAmount($base_shipping_amount);

            $invoice->setShippingInclTax($shipping_incl_tax);
            $invoice->setBaseShippingInclTax($base_shipping_incl_tax);

            $invoice->setGrandTotal($order->getGrandTotal());
            $invoice->setBaseGrandTotal($order->getBaseGrandTotal());
        }
		Mage::log($invoice->getTotalQty(),null,'invoiceqty.log');

        if (!$invoice->getTotalQty())
            Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products.'));

        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE); /*CAPTURE_ONLINE);*/
        $invoice->register();

        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($order)
            ->save();
    }

    public function UpdateInvoice($orderId, $shipping_amount = 0, $shipping_incl_tax = 0)
    {
        $order = Mage::getModel("sales/order")->load($orderId);

        $qtys = array();

        if ($order->hasInvoices()) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                if ($invoice->isCanceled())
                    continue;
                $invoice->cancel()->save()->getOrder()->save();
            }
        }/*try{
	$invoice->register();
				$transactionSave = Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder());
				
				$transactionSave->save();
					//echo $invoice->getid(); 
				}
				catch (Mage_Core_Exception $e) {
					//echo $e;
					Mage::log($e,null,'createinvoice_Exception.log');
				}
*/
	Mage::log($qtys,null,'qtys.log');
        $this->CreateInvoice($orderId, $qtys, $shipping_amount, $shipping_incl_tax);

        return true;
    }
}
