<?php
class IWD_OrderManager_Model_Payment_Payment extends Mage_Core_Model_Abstract
{
    protected $params;

    public function updateOrderPayment($params)
    {
        $this->init($params);

        if (isset($params['confirm_edit']) && !empty($params['confirm_edit'])) {
            $this->addChangesToConfirm();
        } else {
            $this->editPaymentMethod($params['payment'], $params['order_id']);
            $this->addChangesToLog();
        }
    }

    protected function init($params)
    {
        if (!isset($params['order_id'])) {
            throw new Exception("Order id is not defined");
        }
        $this->params = $params;
    }

    protected function addChangesToLog()
    {
        $logger = Mage::getSingleton('iwd_ordermanager/logger');
        $order_id = $this->params['order_id'];

        $logger->addCommentToOrderHistory($order_id);
        $logger->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::PAYMENT, $order_id);
    }

    protected function addChangesToConfirm()
    {
        $this->estimatePaymentMethod($this->params['order_id'], $this->params['payment']);

        Mage::getSingleton('iwd_ordermanager/logger')->addLogToLogTable(IWD_OrderManager_Model_Confirm_Options_Type::PAYMENT, $this->params['order_id'], $this->params);

        $message = Mage::helper('iwd_ordermanager')
            ->__('Order update not yet applied. Customer has been sent an email with a confirmation link. Updates will be applied after confirmation.');
        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }


    public function isAllowEditPayment()
    {
        $permission_allow = Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_payment');
        return $permission_allow;
    }

    public function ReauthorizePayment($orderId, $pay_now = null)
    {
        try {
            $order = Mage::getModel('sales/order')->load($orderId);

            $paid = ($pay_now === null) ? $order->getGrandTotal() : $pay_now;

            $payment = $order->getPayment();
            $orderMethod = $payment->getMethod();

            if ($orderMethod == 'free' && $orderMethod == 'checkmo' && $orderMethod == 'purchaseorder')
                return 1;
            /**** Authorize.net ****/
            if ($orderMethod == 'authorizenet') {
                if (!$payment->authorize(1, $paid)) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
                    return -1;
                } else {
                    $payment->save();
                }
            }

            /**** PayPal ****/
            /*** Payflow Pro Gateway ***/
            if ($orderMethod == Mage_Paypal_Model_Config::METHOD_PAYFLOWPRO) {
                $method = $payment->getMethodInstance()->setStore($order->getStoreId()); //Mage_Paypal_Model_Payflowpro

                if (!$method->reauthorize($payment, $paid)) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in re-authorizing payment."));
                    return -1;
                } else {
                    $payment->save();
                }
            }

            //TODO: add PayPal reauthorize
            /*** Payflow Link Gateway ***/

        } catch (Exception $e) {
            Mage::log($e, null, 'iwd_order_manager.log');
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iwd_ordermanager')->__("Error in update payment: " . $e->getMessage()));
            return -1;
        }

        return 1;
    }

    public function GetActivePaymentMethods()
    {
        $payments = Mage::getModel('payment/config')->getActiveMethods();
        return $this->getMethodsTitle($payments);
    }

    public function GetAllPaymentMethods()
    {
        $payments = Mage::getModel('payment/config')->getAllMethods();
        return $this->getMethodsTitle($payments);
    }

    public function GetPaymentMethods()
    {
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tableName = Mage::getSingleton('core/resource')->getTableName('sales/order_payment');
        $results = $resource->fetchAll("SELECT DISTINCT `method` FROM `$tableName`");

        $methods = array();

        foreach ($results as $paymentCode) {
            $code = $paymentCode['method'];
            $methods[$code] = Mage::getStoreConfig('payment/' . $code . '/title');
        }

        return $methods;
    }

    private function getMethodsTitle($payments)
    {
        $methods = array();

        foreach ($payments as $paymentCode => $paymentModel)
            $methods[$paymentCode] = Mage::getStoreConfig('payment/' . $paymentCode . '/title');

        return $methods;
    }

    public function canUpdatePaymentMethod($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);
        if (empty($order))
            return false;

        return !$order->hasInvoices();
    }

    public function editPaymentMethod($payment_data, $order_id)
    {
        if ($order_id) {
            $order = Mage::getModel('sales/order')->load($order_id);

            if (!empty($order) && $order->getEntityId()) {
                $old_payment = $order->getPayment()->getMethodInstance()->getTitle();

                $payment = $order->getPayment();
                $payment->addData($payment_data)->save();
                $method = $payment->getMethodInstance();
                $method->prepareSave()->assignData($payment_data);
                $order->getPayment()->save();
                $order->getPayment()->getOrder()->save();

                $order = Mage::getModel('sales/order')->load($order_id);
                $payment = $order->getPayment();
                $payment->addData($payment_data)->save();
                $payment->unsMethodInstance();
                $method = $payment->getMethodInstance();
                $method->prepareSave()->assignData($payment_data);
                $order->place();
                $order->getPayment()->save();
                $order->getPayment()->getOrder()->save();

                $new_payment = Mage::getModel('sales/order')->load($order_id)->getPayment()->getMethodInstance()->getTitle();

                Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog("payment_method", $old_payment, $new_payment);
                return 1;
            }
        }
        return 0;
    }

    public function estimatePaymentMethod($order_id, $payment_data)
    {
        $order = Mage::getModel('sales/order')->load($order_id);

        $old_payment = $order->getPayment()->getMethodInstance()->getTitle();
        $new_payment = Mage::helper('payment')->getMethodInstance($payment_data['method'])->getTitle();

        $totals = array(
            'grand_total' => $order->getGrandTotal(),
            'base_grand_total' => $order->getBaseGrandTotal(),
        );

        Mage::getSingleton('iwd_ordermanager/logger')->addNewTotalsToLog($totals);
        Mage::getSingleton('iwd_ordermanager/logger')->addChangesToLog("payment_method", $old_payment, $new_payment);
        Mage::getSingleton('iwd_ordermanager/logger')->addCommentToOrderHistory($order_id, 'wait');
    }
}