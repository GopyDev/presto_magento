<?php
class IWD_OrderManager_Model_Customer_Order extends Mage_Sales_Model_Order
{
    const XML_PATH_CUSTOMER_ORDERS_RESENT_ORDER_GRID_COLUMN = 'iwd_ordermanager/customer_orders/resent_orders_grid_columns';
    const XML_PATH_CUSTOMER_ORDERS_ORDER_GRID_COLUMN = 'iwd_ordermanager/customer_orders/orders_grid_columns';

    public function getSelectedColumnsForRecentOrderGrid()
    {
        $selected_columns = Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ORDERS_RESENT_ORDER_GRID_COLUMN);
        return explode(",", $selected_columns);
    }

    public function getSelectedColumnsForOrderGrid()
    {
        $selected_columns = Mage::getStoreConfig(self::XML_PATH_CUSTOMER_ORDERS_ORDER_GRID_COLUMN);
        return explode(",", $selected_columns);
    }

    public function getRecentOrdersCollectionForCurrentCustomer()
    {
        $selected_columns = $this->getSelectedColumnsForRecentOrderGrid();

        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->setIsCustomerMode(true);

        return Mage::getModel('iwd_ordermanager/order_grid')->getOrdersCollection($selected_columns, $collection);
    }

    public function getOrdersCollectionForCurrentCustomer()
    {
        $selected_columns = $this->getSelectedColumnsForOrderGrid();

        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
            ->setIsCustomerMode(true);

        return Mage::getModel('iwd_ordermanager/order_grid')->getOrdersCollection($selected_columns, $collection);
    }
}