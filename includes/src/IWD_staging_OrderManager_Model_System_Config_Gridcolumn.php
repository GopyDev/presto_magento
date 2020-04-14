<?php
class IWD_OrderManager_Model_System_Config_Gridcolumn
{
    public function toOptionArray()
    {
        $order_grid = Mage::getModel('iwd_ordermanager/order_grid');

        $selected = $order_grid->getSelectedColumnsArray();
        $columns = $order_grid->getOrderGridColumns();

        $options = array();
        foreach ($selected as $sel)
        {
            if(isset($columns[$sel]))
            {
                $options[] = array(
                    'value' => $sel,
                    'label' => $columns[$sel]
                );
                unset($columns[$sel]);
            }
        }

        foreach ($columns as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}