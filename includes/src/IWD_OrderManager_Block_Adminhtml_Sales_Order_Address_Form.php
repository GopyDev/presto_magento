<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Address_Form extends Mage_Adminhtml_Block_Sales_Order_Address_Form //Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/address/form.phtml');
    }

    protected function _getAddress()
    {
        return Mage::getModel('sales/order_address')->load($this->getData('address_id'));
    }

    public function isShippingAddress()
    {
        try {
            $type = Mage::getModel('sales/order_address')->load($this->getAddressId())->getAddressType();
            return ($type == Mage_Sales_Model_Order_Address::TYPE_SHIPPING);
        } catch (Exception $e) {
            Mage::log($e, null, 'iwd_order_manager.log');
            return false;
        }
    }

    public function getAddressId()
    {
        return $this->getData('address_id');
    }

    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_form->setId('edit_form_' . $this->getData('address_id'));
        $this->_form->setMethod('post');
        $this->_form->setUseContainer(false);

        $email_element = $this->_form->getElement('email');
        if (empty($email_element)) {
            $this->_form->getElement('main')->addField('email', 'text',
                array(
                    'name' => 'email',
                    'label' => Mage::helper('iwd_ordermanager')->__('E-mail'),
                    'class' => 'email',
                    'required' => false,
                ),
                'fax'
            );
        }

        $this->_form->getElement('email')->setValue($this->_getAddress()->getEmail());

        return $this;
    }

    public function getCustomerGroupId()
    {
        $order_id = $this->_getAddress()->getParentId();
        return Mage::getModel('sales/order')->load($order_id)->getCustomerGroupId();
    }

    public function getOrderDataJson()
    {
        $address = $this->_getAddress();

        $order_id = $address->getParentId();
        $order = Mage::getModel('sales/order')->load($order_id); 

        $customer_address_id = $address->getCustomerAddressId();

        $data = array();
        $data['customer_id'] = $address->getCustomerId();
        $data['addresses'][$customer_address_id] = $address->getData();

        $data['store_id'] = $order->getStoreId();
        $currency = Mage::app()->getLocale()->currency($this->getStore()->getCurrentCurrencyCode());
        $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
        $data['currency_symbol'] = $symbol;
//Mage::log($data,null,'data.log');
        return Mage::helper('core')->jsonEncode($data);
    }

    public function getLoadBlockUrl()
    {
        return $this->getUrl('*/*/loadBlock');
    }
}