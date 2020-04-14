<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Backup_Sales_Renderer_Items extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private $result;

    public function render(Varien_Object $row)
    {
        $deleted_object_items = unserialize($row['object_items']);
        $this->result = "<table>";
        $this->addRow(array(
                '<b>Name</b>',
                '<b>O</b>',
                '<b>I</b>',
                '<b>C</b>',
                '<b>R</b>',
                '<b>Sh</b>',
                '<b>Row&nbsp;Total</b>'
            )
        );

        foreach ($deleted_object_items as $item) {
            $this->addRow(array(
                    $item['name'] . ' [' . $item['sku'] . ']',
                    $item['qty_ordered'] * 1,
                    $item['qty_invoiced'] * 1,
                    $item['qty_canceled'] * 1,
                    $item['qty_refunded'] * 1,
                    $item['qty_shipped'] * 1,
                    Mage::helper('core')->currency($item['row_total'], true, false),
                )
            );
        }

        return $this->result .= "</table>";
    }

    protected function addRow($items)
    {
        $this->result .= "<tr>";
        foreach ($items as $item)
            $this->result .= "<td>" . $item . "</td>";
        $this->result .= "</tr>";
    }
}
