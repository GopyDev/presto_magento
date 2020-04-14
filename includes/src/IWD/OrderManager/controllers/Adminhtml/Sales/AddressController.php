<?php
class IWD_OrderManager_Adminhtml_Sales_AddressController extends Mage_Adminhtml_Controller_Action
{
    public function editAddressAction()
    {
        $result = array('status' => 1);

        try {
            $address = $this->getRequest()->getPost();

            Mage::getModel('iwd_ordermanager/address')->updateOrderAddress($address);

            $result['address'] = "";/* $this->getLayout()
                ->createBlock('iwd_ordermanager/adminhtml_sales_order_address_text')
                ->setData('address', $address)
                ->toHtml();*/

        } catch (Exception $e) {
            Mage::log($e, null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getEditAddressFormAction()
    {
        $result = array('status' => 1);

        try {
            $address_id = $this->getRequest()->getPost('address_id');
            $address = Mage::getModel('sales/order_address')->load($address_id);
            $is_allow = Mage::getModel('iwd_ordermanager/address')->isAllowEditAddress();

            if ($address && $is_allow) {
                $result['form'] = $this->getLayout()
                    ->createBlock('iwd_ordermanager/adminhtml_sales_order_address_form')
                    ->setData('address_id', $address_id)
                    ->setData('address', $address)
                    ->toHtml();
            }
        } catch (Exception $e) {
            Mage::log($e, null, 'iwd_order_manager.log');
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}