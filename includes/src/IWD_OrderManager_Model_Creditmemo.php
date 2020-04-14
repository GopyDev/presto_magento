<?php
class IWD_OrderManager_Model_Creditmemo extends Mage_Sales_Model_Order_Creditmemo
{
    const XML_PATH_SALES_ALLOW_DEL_CREDITMEMOS = 'iwd_ordermanager/iwd_delete_creditmemos/allow_del_creditmemos';
    const XML_PATH_SALES_STATUS_CREDITMEMO = 'iwd_ordermanager/iwd_delete_creditmemos/creditmemo_status';

    public function isAllowDeleteCreditmemos()
    {
        $conf_allow = Mage::getStoreConfig(self::XML_PATH_SALES_ALLOW_DEL_CREDITMEMOS, Mage::app()->getStore());
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/creditmemo/actions/delete');
        $engine = Mage::helper('iwd_ordermanager')->CheckCreditmemoTableEngine();
        return ($conf_allow && $permission_allow && $engine);
    }
    public function isAllowCreateCreditmemo()
    {
        return true;
    }

    public function getCreditmemoStatusesForDeleteIds()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_SALES_STATUS_CREDITMEMO));
    }

    public function checkCreditmemoStatusForDeleting()
    {
        return (in_array($this->getState(), $this->getCreditmemoStatusesForDeleteIds()));
    }

    public function canDelete()
    {
        return ($this->isAllowDeleteCreditmemos() && $this->checkCreditmemoStatusForDeleting());
    }

    public function refund()
    {
        $this->setState(self::STATE_REFUNDED);
        $orderRefund = Mage::app()->getStore()->roundPrice(
            $this->getOrder()->getTotalRefunded()+$this->getGrandTotal()
        );
        $baseOrderRefund = Mage::app()->getStore()->roundPrice(
            $this->getOrder()->getBaseTotalRefunded()+$this->getBaseGrandTotal()
        );

        // can refunded > paid
        /*if ($baseOrderRefund > Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseTotalPaid())) {

            $baseAvailableRefund = $this->getOrder()->getBaseTotalPaid()- $this->getOrder()->getBaseTotalRefunded();

            Mage::throwException(
                Mage::helper('sales')->__('Maximum amount available to refund is %s', $this->getOrder()->formatBasePrice($baseAvailableRefund))
            );
        }*/

        $order = $this->getOrder();
        $order->setBaseTotalRefunded($baseOrderRefund);
        $order->setTotalRefunded($orderRefund);

        $order->setBaseSubtotalRefunded($order->getBaseSubtotalRefunded()+$this->getBaseSubtotal());
        $order->setSubtotalRefunded($order->getSubtotalRefunded()+$this->getSubtotal());

        $order->setBaseTaxRefunded($order->getBaseTaxRefunded()+$this->getBaseTaxAmount());
        $order->setTaxRefunded($order->getTaxRefunded()+$this->getTaxAmount());
        $order->setBaseHiddenTaxRefunded($order->getBaseHiddenTaxRefunded()+$this->getBaseHiddenTaxAmount());
        $order->setHiddenTaxRefunded($order->getHiddenTaxRefunded()+$this->getHiddenTaxAmount());

        $order->setBaseShippingRefunded($order->getBaseShippingRefunded()+$this->getBaseShippingAmount());
        $order->setShippingRefunded($order->getShippingRefunded()+$this->getShippingAmount());

        $order->setBaseShippingTaxRefunded($order->getBaseShippingTaxRefunded()+$this->getBaseShippingTaxAmount());
        $order->setShippingTaxRefunded($order->getShippingTaxRefunded()+$this->getShippingTaxAmount());

        $order->setAdjustmentPositive($order->getAdjustmentPositive()+$this->getAdjustmentPositive());
        $order->setBaseAdjustmentPositive($order->getBaseAdjustmentPositive()+$this->getBaseAdjustmentPositive());

        $order->setAdjustmentNegative($order->getAdjustmentNegative()+$this->getAdjustmentNegative());
        $order->setBaseAdjustmentNegative($order->getBaseAdjustmentNegative()+$this->getBaseAdjustmentNegative());

        $order->setDiscountRefunded($order->getDiscountRefunded()+$this->getDiscountAmount());
        $order->setBaseDiscountRefunded($order->getBaseDiscountRefunded()+$this->getBaseDiscountAmount());

        if ($this->getInvoice()) {
            $this->getInvoice()->setIsUsedForRefund(true);
            $this->getInvoice()->setBaseTotalRefunded(
                $this->getInvoice()->getBaseTotalRefunded() + $this->getBaseGrandTotal()
            );
            $this->setInvoiceId($this->getInvoice()->getId());
        }

        if (!$this->getPaymentRefundDisallowed()) {
            $order->getPayment()->refund($this);
        }

        Mage::dispatchEvent('sales_order_creditmemo_refund', array($this->_eventObject=>$this));
        return $this;
    }

    public function DeleteCreditmemo()
    {
        if (!$this->canDelete()) {

            $message = 'Maybe, you can not delete items with some statuses. Please, check <a href="'
            . Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit", array("section" => "iwd_ordermanager"))
            . '" target="_blank" title="System - Configuration - IWD Extensions - Order Manager">configuration</a> of IWD OrderManager';

            Mage::getSingleton('iwd_ordermanager/logger')->addNoticeMessage('check_credit_memo_status', $message);

            Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteError('creditmemo', $this->getIncrementId());
            return false;
        }

        Mage::dispatchEvent('iwd_ordermanager_sales_creditmemo_delete_after', array('creditmemo' => $this, 'creditmemo_items' => $this->getItemsCollection()));

        if ($this->getState() != Mage_Sales_Model_Order::STATE_CANCELED)
            $this->cancel()->save()->getOrder()->save();

        $orderId = $this->getOrderId();

        $order = Mage::getModel('sales/order')->load($orderId);

        Mage::getSingleton('iwd_ordermanager/report')
            ->addRefundedPeriod($this->getCreatedAt(), $this->getUpdatedAt(), $order->getCreatedAt());

        if ($order->hasInvoices() && $order->hasShipments())
            $state = Mage_Sales_Model_Order::STATE_COMPLETE;
        else if ($order->hasInvoices())
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
        else
            $state = $order->getState();

        $creditmemo_items = $this->getItemsCollection();
        foreach ($creditmemo_items as $creditmemo_item) {
            $creditmemo_item->getProductId();
            $order_items = Mage::getResourceModel('sales/order_item_collection')
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('product_id', $creditmemo_item->getProductId());

            foreach ($order_items as $order_item) {
                $amount_refunded = $order_item->getAmountRefunded() - $creditmemo_item->getRowTotal();
                $base_amount_refunded = $order_item->getBaseAmountRefunded() - $creditmemo_item->getRowTotal();

                $order_item->setAmountRefunded($amount_refunded);
                $order_item->setBaseAmountRefunded($base_amount_refunded);
                $order_item->save();
            }
        }

        $order->setData('state', $state);
        $order->setStatus($order->getConfig()->getStateDefaultStatus($state));
        $total_refunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $base_total_refunded = $order->getTotalRefunded() - $this->getBaseGrandTotal();
        $order->setTotalRefunded($total_refunded);
        $order->setBaseTotalRefunded($base_total_refunded);
        $order->save();

        Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('creditmemo', $this->getIncrementId());

        $items = $this->getItemsCollection();
        $obj = $this;

        $this->delete();

        Mage::dispatchEvent('iwd_ordermanager_sales_creditmemo_delete_before', array('creditmemo' => $obj, 'creditmemo_items' => $items));

        return true;
    }

    public function CreateCreditmemo($orderId, $qtys)
    {
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order->getId())
            Mage::throwException(Mage::helper('core')->__('Order not exists'));

        if (!$order->canCreditmemo())
            Mage::throwException(Mage::helper('core')->__('Cannot create creditmemo'));

        if (!is_array($qtys))
            Mage::throwException(Mage::helper('core')->__('Empty items for credit memo'));

        $service = Mage::getModel('sales/service_order', $order);

        $data = array('qtys' => $qtys,
            "do_offline" => 1,
            "shipping_amount" => 0.0,
            "adjustment_positive" => 0.0,
            "adjustment_negative" => 0.0,
        );

        $creditmemo = $service->prepareCreditmemo($data);

        $creditmemo->setPaymentRefundDisallowed(true)
            ->setAutomaticallyCreated(true)
            ->register()
            ->addComment(Mage::helper('sales')->__('Credit memo has been created automatically'));

        Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder())
            ->save();
    }

    public function CreateCreditmemoAdjustmentRefund($order_id, $refundedMoney)
    {
        if($refundedMoney <= 0)
            return;

        $order = Mage::getModel('sales/order')->load($order_id);

        if (!$order->getId())
            Mage::throwException(Mage::helper('core')->__('Order not exists'));

        $service = Mage::getModel('sales/service_order', $order);

        $creditmemo = Mage::getModel('sales/convert_order')->toCreditmemo($order);
        $creditmemo->setTotalQty(0);
        //$creditmemo->setAdjustmentPositive($refundedMoney);
        $creditmemo->setBaseShippingAmount(0.0);
        $creditmemo->setAdjustmentNegative(-1*$refundedMoney);
        $creditmemo->collectTotals();

        $creditmemo->setPaymentRefundDisallowed(true)
            ->setAutomaticallyCreated(true)
            ->register()
            ->addComment(Mage::helper('sales')->__('Credit memo has been created automatically'));

        Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder())
            ->save();
    }
}
