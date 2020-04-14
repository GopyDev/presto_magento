<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Order_Invoice_Total_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $invoice->setDiscountAmount(0);
        $invoice->setBaseDiscountAmount(0);

        $totalDiscountAmount     = 0;
        $baseTotalDiscountAmount = 0;

        /**
         * Checking if shipping discount was added in previous invoices.
         * So basically if we have invoice with positive discount and it
         * was not canceled we don't add shipping discount to this one.
         */
        $addShippingDicount = true;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getDiscountAmount()) {
                $addShippingDicount = false;
            }
        }

        if ($addShippingDicount) {
			
			
			
			//print_r($previusInvoice);
            $totalDiscountAmount     = $totalDiscountAmount + $invoice->getOrder()->getShippingDiscountAmount();
            $baseTotalDiscountAmount = $baseTotalDiscountAmount + $invoice->getOrder()->getBaseShippingDiscountAmount();
        }

        /** @var $item Mage_Sales_Model_Order_Invoice_Item */
        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                 continue;
            }

            $orderItemDiscount      = (float) $orderItem->getDiscountAmount();
            $baseOrderItemDiscount  = (float) $orderItem->getBaseDiscountAmount();
            $orderItemQty       = $orderItem->getQtyOrdered();

            if ($orderItemDiscount && $orderItemQty) {
                /**
                 * Resolve rounding problems
                 */
                $discount = $orderItemDiscount - $orderItem->getDiscountInvoiced();
                $baseDiscount = $baseOrderItemDiscount - $orderItem->getBaseDiscountInvoiced();
                if (!$item->isLast()) {
                    $activeQty = $orderItemQty - $orderItem->getQtyInvoiced();
                    $discount = $invoice->roundPrice($discount / $activeQty * $item->getQty(), 'regular', true);
                    $baseDiscount = $invoice->roundPrice($baseDiscount / $activeQty * $item->getQty(), 'base', true);
                }

                $item->setDiscountAmount($discount);
                $item->setBaseDiscountAmount($baseDiscount);

                $totalDiscountAmount += $discount;
                $baseTotalDiscountAmount += $baseDiscount;
            }
		
        }
		//get base Discount amount and store in variable custom 
		$totalDiscountAmount = $invoice->getOrder()->getBaseDiscountAmount();
		$baseTotalDiscountAmount = $invoice->getOrder()->getBaseDiscountAmount(); 
		
        $invoice->setDiscountAmount($totalDiscountAmount);
        $invoice->setBaseDiscountAmount($baseTotalDiscountAmount);
		//End
		//Get Entity id from order collection
		$entity_id = $orderItem['order_id'];
		
		//
		// Get Order id from order table and retrive all data from db with shiping charge
		
		
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$table = $resource->getTableName('sales_flat_order');
		$sql1 = "select * from mg_sales_flat_order where entity_id='".$entity_id."'";
		$orderdata = $readConnection->fetchAll($sql1);
		$giftcertcode = $orderdata['giftcert_code'];
		
	 	$nextdayfee = $_SESSION['ship_charge'];				
		//end
		$increment_id = $orderdata['increment_id'];
		//get extra Shiping charge for nextday delivery query
		
		$resources = Mage::getSingleton('core/resource');
		$readConnections = $resources->getConnection('core_read');
		$tables = $resource->getTableName('sales_flat_order');
		$sql2 = "select * from mg_shipping_delivery_window where order_number='".$increment_id."'";
		$shipchargeval = $readConnections->fetchAll($sql2);
		
		//
		// Check discount amount in minus
	
		$pos1 = strpos($totalDiscountAmount,'-');
				if ($pos1 === false) {
						$discountvalue1 = $totalDiscountAmount; 
				}
				else{
						$discountvalue1 = str_replace('-','',$totalDiscountAmount);
				}
		//	
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$productTable = 'mg_customer_subsciption';
		$query = "SELECT * FROM " . $productTable . " WHERE customer_id = '".$customer_id."'";
		$result = $connection->query($query);
		$row = $result->fetch();	
		//ARB
		$query2 = "select status from mg_aw_sarp2_profile where  `customer_id` LIKE '$customer_id' AND `status` LIKE 'active'";
		$result2 = $connection->query($query2);
		$row2 = $result2->fetch();
		//End
			
		if($row['active'] == 'Yes' || $row2['active'] == 'active'){
			$nextdayfee = '';
		}
		$subtotal = $invoice->getGrandTotal();
		$grandtotal = ($subtotal + $nextdayfee);
		
		//echo 'nextdayfee'.$nextdayfee;
		if(!empty($nextdayfee)){

			 if($orderdata['coupon_code'] == 'chagrinfalls'){
					$invoice->setGrandTotal($grandtotal - $discountvalue1);
					$invoice->setBaseGrandTotal($grandtotal -  $discountvalue1);
					$invoice->setBaseTotalInvoiced($grandtotal);
					$invoice->setTotalInvoiced($grandtotal);
					$invoice->setAmountPaid($grandtotal);
					$invoice->setBaseAmountPaid($grandtotal);
					$invoice->setTotalDue($grandtotal);
			
				 }else{
 				
					$invoice->setGrandTotal($grandtotal  - $discountvalue1);
					$invoice->setBaseGrandTotal($grandtotal  - $discountvalue1);
					$invoice->setBaseTotalInvoiced($grandtotal);
					$invoice->setTotalInvoiced($grandtotal);
					$invoice->setAmountPaid($grandtotal);
					$invoice->setBaseAmountPaid($grandtotal);
					$invoice->setTotalDue($grandtotal);
			}
		} else{
			 		$invoice->setGrandTotal($grandtotal - $discountvalue1);
					$invoice->setBaseGrandTotal($grandtotal -  $discountvalue1);
					$invoice->setBaseTotalInvoiced($grandtotal);
					$invoice->setTotalInvoiced($grandtotal);
					$invoice->setAmountPaid($grandtotal);
					$invoice->setBaseAmountPaid($grandtotal);
					$invoice->setTotalDue($grandtotal);
							
			
			}
			
			
		//	Mage::log('Your Data :'.print_r($invoice->debug(), true), Zend_Log::DEBUG, 'invoicedata.log');

		//}

	//mysql_close($con);
        return $this;
    }
}
