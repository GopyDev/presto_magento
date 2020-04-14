<?php
/************************************************************************
 * 
 * jtechextensions @ J-Tech LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.jtechextensions.com/LICENSE-M1.txt
 *
 * @package    Inventory Report
 * @copyright  Copyright (c) 2012-2013 jtechextensions @ J-Tech LLC. (http://www.jtechextensions.com)
 * @license    http://www.jtechextensions.com/LICENSE-M1.txt
************************************************************************/

class Jtech_Profitlossreport_Block_Manager_View_Grid extends Mage_Adminhtml_Block_Widget
{
	protected $_currencyCode;
	protected $_currId;
	protected $_reportData;
	protected $_coreAttributes;
	protected $_totalRevenue;
	protected $_totalExpenses;
	protected $_revenueItems;
	protected $_expenseItems;
	
	protected function getTotalRevenue()
	{
		return $this->_totalRevenue;
	}
	
	protected function getTotalExpenses()
	{
		return $this->_totalExpenses;
	}
	
	protected function getCurrencyCode()
	{
		return $this->_currencyCode;
	}
	
	protected function getCurrId()
	{
		return $this->_currId;
	}
	
	protected function getReportData()
	{
		return $this->_reportData;
	}	
	
	protected function getCoreAttributes()
	{
		return $this->_coreAttributes;
	}	
	
	public function __construct()
	{
		parent::__construct();

		$this->setTemplate('profitlossreport/viewreport.phtml');

		$this->_currencyCode = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$this->_currId = $this->getRequest()->getParam('id');

		$this->buildReportData();
		$this->buildCoreAttributes();
	}
	
	protected function buildReportData()
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');

		$reportsTable = $resource->getTableName('jtech_pl_reports');
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		$AttributesTable = $resource->getTableName('jtech_pl_attributes');
		$salesFlatOrderItemTable = $resource->getTableName('sales_flat_order_item');
		$catalogProductEntityDecimalTable = $resource->getTableName('catalog_product_entity_decimal');
		if (Mage::helper('marcore')->checkMagentoVersion('1.4.0.1', '==') || Mage::helper('marcore')->checkMagentoVersion('1.4.1.0', '==')) {
			$salesFlatOrderTable = $resource->getTableName('sales_order');
		} else {
			$salesFlatOrderTable = $resource->getTableName('sales_flat_order');
		}
		$salesOrderVarcharTable = $resource->getTableName('sales_order_varchar');
		$eavAttributeTable = $resource->getTableName('eav_attribute');
		$eavEntityTypeTable = $resource->getTableName('eav_entity_type');
		$salesOrderTable = $resource->getTableName('sales_order');
				
		$currId = $this->getCurrId();
		
		$select = 'SELECT att_name, value
					FROM ' . $reportAttTable . '
					WHERE att_type_id = 1 AND report_id = ' . $currId;
		$reportAtt = $read->fetchAll($select);
		
		foreach ($reportAtt as $att)
		{
			$currAtt[$att['att_name']] = $att['value'];
		}
		
		// Parse store ID information
		if ($currAtt['store_id'] == '') {
			// All Stores
			$storeClause = '';
		} else {
			// Specific Stores
			$storeMatch = explode(',', $currAtt['store_id']);
			
			switch ($storeMatch[0]){
				case 'w':
					// Website level requirement
					$select = 'SELECT store_id
								FROM core_store
								WHERE is_active = 1 AND website_id = ' . $storeMatch[1]
					;
					$storeIds = $read->fetchAll($select);
					
					for ($i = 0; $i < count($storeIds); $i++)
					{
						if ($i == 0)
							$id = $storeIds[$i]['store_id'];
						else
							$id = $id . ', ' . $storeIds[$i]['store_id'];
					}
					
					$storeClause = ' AND e.store_id IN (' . $id . ')';
					break;
				case 'g':
					// Group level requirement
					$select = 'SELECT store_id
								FROM core_store
								WHERE is_active = 1 AND group_id = ' . $storeMatch[1]
					;
					$storeIds = $read->fetchAll($select);
					
					for ($i = 0; $i < count($storeIds); $i++)
					{
						if ($i == 0)
							$id = $storeIds[$i]['store_id'];
						else
							$id = $id . ', ' . $storeIds[$i]['store_id'];
					}
					
					$storeClause = ' AND e.store_id IN (' . $id . ')';
					break;
				case 's':
					// Specific store
					$storeClause = ' AND e.store_id = ' . $storeMatch[1];
					break;
			}
		}
		
		// Set the Match Period filter
		if ($currAtt['match_period'] == 'created_at_order') {
			$timeField = 'created_at';
		} else if ($currAtt['match_period'] == 'updated_at_order') {
			$timeField = 'updated_at';
		}
		
		// Parse period
		$orderNumCountField = 'Sum(orders.no_orders) as Orders,';
		
		switch ($currAtt['period']) {
		    case 'order':
		    	$periodSelect = 'orders.' . $timeField . ' as Period,';
		    	$groupBySelect = 'Group By orders.order_id';
		    	$orderNumCountField = 'orders.order_number as "Order Number", Sum(orders.no_orders) as Orders,';
		        break;
		    case 'day':
		    	$periodSelect = 'date_format(orders.' . $timeField . ', "%d/%m/%Y") as Period,';
		    	$groupBySelect = 'Group By day(orders.' . $timeField . '), month(orders.' . $timeField . '), year(orders.' . $timeField . ')';
		        break;
		    case 'week':
		    	$periodSelect = 'Concat("Week ",week(orders.' . $timeField . ')," of ",year(orders.' . $timeField . ')) as Period,';
		    	$groupBySelect = 'Group By weekofyear(orders.' . $timeField . '), year(orders.' . $timeField . ')';
		        break;
		    case 'fortnight':
		    	$periodSelect = 'Concat("Fortnight ",Round(weekofyear(orders.' . $timeField . ')/2)," of ",year(orders.' . $timeField . ')) as Period,';
		    	$groupBySelect = 'Group By Round(weekofyear(orders.' . $timeField . ')/2),year(orders.' . $timeField .  ')';
		        break;
		    case 'month':
		    	$periodSelect = 'date_format(orders.' . $timeField . ', "%M %Y") as Period,';
		    	$groupBySelect = 'Group By month(orders.' . $timeField . '), year(orders.' . $timeField . ')';
		        break;
		    case 'quarter':
		    	$periodSelect = 'Concat(date_format(concat("2000-01-",quarter(orders.' . $timeField . ')),"%D"), " Quarter of ",year(orders.' . $timeField . ')) as Period,';
		    	$groupBySelect = 'Group By quarter(orders.' . $timeField . '), year(orders.' . $timeField . ')';
		        break;
		    case 'bi-year':
		    	$periodSelect = 'Concat(date_format(concat("2000-01-",Round(quarter(orders.' . $timeField . ')/2)),"%D"), " Half of ",year(orders.' . $timeField . ')) as Period,';
		    	$groupBySelect = 'Group By Round(quarter(orders.' . $timeField . ')/2),year(orders.' . $timeField . ')';
		        break;
			case 'year':
				$periodSelect = 'date_format(orders.' . $timeField . ', "%Y") as Period,';
		    	$groupBySelect = 'Group By year(orders.' . $timeField . ')';
		        break;
		}

		// Check status value
		if ($currAtt['status'] == '')
		{
			$statusClause = '';
		} else {
			$currAtt['status'] = str_replace(', ', '", "', $currAtt['status']);
			$statusClause = ' AND a.status IN ("' . $currAtt['status'] . '") ';
		}
		
		// Set the shipping flag
		if ($currAtt['shipping_flag'] == 'true') {
			$shippingSelect = 'Sum(orders.sales_shipping) as "Sales Shipping", 
			Sum(orders.shipping) as Shipping, ';
			$shippingValue = 'Sum(orders.shipping)';
		} else {
			$shippingSelect = '';
			$shippingValue = 0;
		}
		
		if ($currAtt['cost_source'] == 'order') {
			$cogsSelect = 'Sum(orders.cogs) as "Cost of Goods",';
			$cogsValue = 'Sum(orders.cogs)';
		} else if ($currAtt['cost_source'] == 'product') {
			$cogsSelect = 'Sum(orders.cogs_prod_att) as "Cost of Goods",';
			$cogsValue = 'Sum(orders.cogs_prod_att)';
		} else {
			$cogsSelect = 'IF(Sum(orders.cogs) > 0 Or Sum(orders.cogs) is null, Sum(orders.cogs), Sum(orders.cogs_prod_att)) as "Cost of Goods",';
			$cogsValue = '(IF(Sum(orders.cogs) > 0 Or Sum(orders.cogs) is null, Sum(orders.cogs), Sum(orders.cogs_prod_att)))';
		}
		
		// Determin the product cost attribute code
		$select = '
			SELECT value
			FROM ' . $resource->getTableName('jtech_core_attributes') . '
			WHERE code = "cost_att_id"
		';		
		$costAttCode = $read->fetchRow($select);
		$costAttCode = $costAttCode['value'];
		
		// Convert time to GMT
		$gmtFrom = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $currAtt['date_from']);
		$gmtTo = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $currAtt['date_to']);
		
		if (Mage::helper('marcore')->checkMagentoVersion('1.4.0.0', '<')) {
			$select = '
				Select    ' . $periodSelect . '
					' . $orderNumCountField . '
				    Sum(orders.sales_items) as "Sales Items",
	                Sum(orders.items) as "Items",
	                Sum(orders.sales_total) as "Sales Total",
	                Sum(orders.revenue) as "Revenue",
	                ' . $cogsSelect . '
	                Sum(orders.invoiced) as "Invoiced",
	                Sum(orders.paid) as "Paid",
	                Sum(orders.refunded) as "Refunded",
	                Sum(orders.sales_tax) as "Sales Tax",
	                Sum(orders.tax) as "Tax",
	                ' . $shippingSelect . '
	                Sum(orders.sales_discount) as "Sales Discount",
	                Sum(orders.discount) as "Discount",
	                Sum(orders.canceled) as "Canceled",
	                
	                (Sum(orders.revenue) - ' . $cogsValue . ' - ' . $shippingValue . ' - Sum(orders.tax)) as "Store Profit"
	
					From
					(SELECT e.order_id,
					                a.increment_id as order_number, 
					               	a.created_at,
					                a.updated_at,
					                order_status.status,
					                a.store_id,
					                1 as "no_orders",
					                SUM(IFNULL(e.qty_ordered, 0) - IFNULL(e.qty_canceled, 0)) as "sales_items",
					                SUM(IFNULL(e.qty_invoiced, 0)) as "items",
					                IFNULL(a.base_grand_total, 0) - (IFNULL(a.total_canceled,0)) as "sales_total",
					                IFNULL(a.total_paid, 0) - (IFNULL(a.total_refunded, 0)) as "revenue",
					                SUM(IFNULL(e.cost * e.qty_invoiced, 0)) as "cogs",
					                SUM(IFNULL(prod_cost.cost * e.qty_invoiced, 0)) as "cogs_prod_att",
					                IFNULL(a.total_invoiced, 0) as "invoiced",
					                IFNULL(a.total_paid, 0) as "paid",
					                IFNULL(a.total_refunded, 0) as "refunded",
					                IFNULL(a.tax_amount, 0) - IFNULL(a.tax_canceled, 0) as "sales_tax",
					                IFNULL(a.tax_invoiced, 0) - IFNULL(a.tax_refunded, 0) as "tax",
					                IFNULL(a.shipping_amount, 0) - IFNULL(a.shipping_canceled, 0) as "sales_shipping",
					                IFNULL(a.shipping_invoiced, 0) - IFNULL(a.shipping_refunded, 0) as "shipping",
					                IFNULL(a.discount_amount, 0) - IFNULL(a.discount_canceled, 0) as "sales_discount",
					                IFNULL(a.discount_invoiced, 0) - IFNULL(a.discount_refunded, 0) as "discount",
					                IFNULL(a.total_canceled, 0) as "canceled"
					FROM ' . $salesFlatOrderItemTable . ' e
					LEFT JOIN (
					Select entity_id, value as status
					From ' . $salesOrderVarcharTable . '
					Where attribute_id = 
					(
					Select attribute_id
					From ' . $eavAttributeTable . '
					Where attribute_code = "status" and entity_type_id = (Select entity_type_id
					From ' . $eavEntityTypeTable . '
					Where entity_type_code = "order")
					)
					) as order_status
					ON order_status.entity_id = e.order_id
					
					LEFT JOIN (
					Select entity_id, value as cost 
					From ' . $catalogProductEntityDecimalTable . '
					Where attribute_id = ' . $costAttCode . ') as prod_cost ON prod_cost.entity_id = e.product_id
					LEFT JOIN ' . $salesOrderTable . ' a ON e.order_id = a.entity_id
					WHERE e.product_type IN ("simple", "virtual", "grouped", "downloadable")
					AND order_status.status != "canceled"
					AND a.' . $timeField . ' BETWEEN "' . $gmtFrom . '" AND "' . $gmtTo . '"
					GROUP BY e.order_id) orders
					' . $groupBySelect . '
					Order By orders.' . $timeField . ' asc
			';
		} else {
			// Greter than or equal to 1.4 or any new developer versions.
			$select = '
				Select ' . $periodSelect . '
				' . $orderNumCountField . '
				orders.customer_firstname as "Customer First Name",
				orders.customer_lastname as "Customer Last Name",
				Sum(orders.sales_items) as "Sales Items",
				Sum(orders.items) as Items,
				Sum(orders.sales_total) as "Sales Total",
				Sum(orders.revenue) as "Revenue",
				' . $cogsSelect . '
				Sum(orders.invoiced) as Invoiced,
				Sum(orders.paid) as Paid,
				Sum(orders.refunded) as Refunded,
				Sum(orders.sales_tax) as "Sales Tax",
				Sum(orders.tax) as Tax, 
				' . $shippingSelect . ' 
				Sum(orders.sales_discount) as "Sales Discount",
				Sum(orders.discount) as Discount,
				Sum(orders.canceled) as Canceled,
				
				(Sum(orders.revenue) - ' . $cogsValue . ' - ' . $shippingValue . ' - Sum(orders.tax)) as "Store Profit"
			
				From
				(SELECT e.order_id,
					a.increment_id as order_number,
					a.created_at,
					a.updated_at,
					a.customer_firstname,
					a.customer_lastname,
					a.status,
					a.store_id,
					1 as "no_orders",
					SUM(IFNULL(e.qty_ordered, 0) - IFNULL(e.qty_canceled, 0)) as "sales_items",
					SUM(IFNULL(e.qty_invoiced, 0)) as "items",
					IFNULL(a.base_grand_total, 0) - (IFNULL(a.total_canceled,0)) as "sales_total",
					IFNULL(a.total_paid, 0) - (IFNULL(a.total_refunded, 0)) as "revenue",
					SUM(IFNULL(e.base_cost * e.qty_invoiced, 0)) as "cogs",
					SUM(IFNULL(prod_cost.cost * e.qty_invoiced, 0)) as "cogs_prod_att", 
					IFNULL(a.total_invoiced, 0) as "invoiced",
					IFNULL(a.total_paid, 0) as "paid",
					IFNULL(a.total_refunded, 0) as "refunded",
					IFNULL(a.tax_amount, 0) - IFNULL(a.tax_canceled, 0) as "sales_tax",
					IFNULL(a.tax_invoiced, 0) - IFNULL(a.tax_refunded, 0) as "tax",
					IFNULL(a.shipping_amount, 0) - IFNULL(a.shipping_canceled, 0) as "sales_shipping",
					IFNULL(a.shipping_invoiced, 0) - IFNULL(a.shipping_refunded, 0) as "shipping",
					IFNULL(a.discount_amount, 0) - IFNULL(a.discount_canceled, 0) as "sales_discount",
					IFNULL(a.discount_invoiced, 0) - IFNULL(a.discount_refunded, 0) as "discount",
					IFNULL(a.total_canceled, 0) as "canceled"
				FROM ' . $salesFlatOrderItemTable . ' e 
				LEFT JOIN (	Select entity_id, value as cost 
							From ' . $catalogProductEntityDecimalTable . ' 
							Where attribute_id = ' . $costAttCode . ') as prod_cost ON prod_cost.entity_id = e.product_id
				LEFT JOIN ' . $salesFlatOrderTable . ' a ON e.order_id = a.entity_id 										
				WHERE e.product_type IN ("simple", "virtual", "grouped", "downloadable")
				AND a.status != "canceled"
				AND a.' . $timeField . ' BETWEEN "' . $gmtFrom . '" AND "' . $gmtTo . '" 
				' . $storeClause . ' 
				' . $statusClause . ' 
				GROUP BY e.order_id) orders 
				' . $groupBySelect . '
				ORDER BY orders.' . $timeField . ' ASC'
			;
		}
		
		$this->_reportData = $read->fetchAll($select);
	}
	
	protected function getReportHeader()
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');

		$reportsTable = $resource->getTableName('jtech_pl_reports');
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		$AttributesTable = $resource->getTableName('jtech_pl_attributes');
		$currId = $this->getCurrId();
		
		$select = $read->select()
			->from($reportsTable, array('name'))
			->where('report_id = ' . $currId);
		$reportName = $read->fetchRow($select);
		$reportName = $reportName['name'];
		
		$select = 'SELECT b.name as att, a.value as value
			FROM ' . $reportAttTable . ' a
			LEFT JOIN ' . $AttributesTable . ' b on a.att_id = b.att_id
			WHERE a.report_id = ' . $currId;
		foreach ($read->fetchAll($select) as $attribute)
			$reportAttributes[$attribute['att']] = $attribute['value'];
		
		if ($reportAttributes['match_period'] == 'created_at_order')
			$reportAttributes['match_period'] = 'Order Created Date';
		else
			$reportAttributes['match_period'] = 'Order Updated Date';
			
		if ($reportAttributes['status'] == null){
			$reportAttributes['status'] = 'Any';
		}
		
		if ($reportAttributes['shipping_flag'] == 'true') {
			$shippingMsg = 'Expense';
		} else {
			$shippingMsg = 'Revenue';
		}

		if ($reportAttributes['cost_source'] == 'order') {
			$costNote = 'Order Table';
		} else if ($reportAttributes['cost_source'] == 'product') {
			$costNote = 'Product Attribute';
		} else {
			$costNote = 'Hybrid';
		}
		
		$htmlOutput = '
			<table>
				<tr>
					<td colspan="6"><h3 class="icon-head head-manager-view">' . ucwords($reportName) . '</h3></td>
				</tr>
				<tr>
					<td style="width: 10px; padding-left: 10px">From:</td>
					<td style="width: 125px; padding-left: 10px"><strong>' . $reportAttributes['date_from'] . '</strong></td>
					<td style="width: 115px; padding-left: 10px">Period:</td>
					<td style="width: 120px; padding-left: 10px"><strong>' . ucfirst($reportAttributes['period']) . '<strong></td>
					<td style="width: 115px; padding-left: 10px">Cost Source:</td>
					<td style="width: 100px; padding-left: 10px"><strong>' . $costNote . '</strong></td>
				</tr>
				<tr>
					<td style="; padding-left: 10px">To:</td>
					<td style="; padding-left: 10px"><strong>' . $reportAttributes['date_to'] . '<strong></td>
					<td style="; padding-left: 10px">Matched Period To:</td>
					<td style="; padding-left: 10px"><strong>' . $reportAttributes['match_period'] . '<strong></td>
					<td style="; padding-left: 10px">Magento Shipping:</td>
					<td style="; padding-left: 10px"><strong>' . $shippingMsg . '</strong></td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" style="; padding-left: 10px">Status Matching:</td>
					<td colspan="4" style="; padding-left: 10px"><strong>' . ucwords($reportAttributes['status']) . '</strong></td>
				</tr>
			</table>
		';
		
		return $htmlOutput;
	}
	
	protected function getRevenueItems()
	{
		// Core magento Items
		$dirtyCoreRevenueItems = $this->getCoreAttributes();
		
		if ($dirtyCoreRevenueItems != null)
		{
			$coreRevenueSumTotal = $this->organiseCoreItems('revenue', $dirtyCoreRevenueItems);
			
			echo '<tr><td clospan="2">&nbsp;</td></tr>';
			echo '<tr><td><strong><em>Store Revenue Total</em><strong></td><td><div style="text-align: right; width: 200px"><strong><em>' . Mage::helper('core')->currency($coreRevenueSumTotal) . '</em></strong></div></td></tr>';
		} else {
			echo '<tr><td colspan="2" style="text-align: center; padding-top: 10px">There are no store revenue items</td></tr>';
		}
		
		// Additional Items
		echo '<tr><td colspan="2">&nbsp;</td></tr>';
		echo '<tr><th colspan="2" style="text-align: left; border-bottom: 1px solid #6F8992; text-align: center; font-style: italic">Additional Revenue</th></tr>';
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		
		$select = 'SELECT SUM(value) as total
					FROM ' . $reportAttTable . ' 
					WHERE att_type_id = 2
					AND report_id = ' . $this->getCurrId() . '
		';
		$result = $read->fetchRow($select);
		
		$revenueSumTotal = 0;		
		if ($result['total'] != 0)
		{
			$dirtyItems = $this->buildAttList('revenue');
		
			$revenueItems = $this->organiseItems($dirtyItems);
			
			$revenueSumTotal = $this->findParentSum($dirtyItems, null);
			
			echo '<tr><td clospan="2">&nbsp;</td></tr>';
			echo '<tr><td><strong><em>Additional Revenue Total</em><strong></td><td><div style="text-align: right; width: 200px"><strong><em>' . Mage::helper('core')->currency($revenueSumTotal) . '</em></strong></div></td></tr>';
		} else {
			echo '<tr><td colspan="2" style="text-align: center; padding-top: 10px">There are no set additional revenue items</td></tr>';
		}
		
		$this->_totalRevenue = $coreRevenueSumTotal + $revenueSumTotal;
		
		echo '<tr><th colspan="2" style="border-bottom: 1px solid #6F8992; font-style: italic">&nbsp;</th></tr>';
		echo '<tr><th style="text-align: center; font-style: italic">Total Revenue</th><th style="text-align: center; font-style: italic">' . Mage::helper('core')->currency($this->getTotalRevenue()) . '</th></tr>';
	}
	
	protected function organiseCoreItems($revexpType, $items)
	{
		$cogsTotal = 0;
		$taxTotal = 0;
		$shippingTotal = 0;
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		$currId = $this->getCurrId();
		
		$select = 'SELECT att_name, value
					FROM ' . $reportAttTable . '
					WHERE att_type_id = 1 AND report_id = ' . $currId;
		$reportAtt = $read->fetchAll($select);
		
		foreach ($reportAtt as $att)
		{
			$currAtt[$att['att_name']] = $att['value'];
		}
		
		$completedRows = array();
		
		if ($revexpType == 'revenue'){
			$revenueTotal = 0;
			foreach ($items as $key => $item)
			{
				if (!(in_array($key, $completedRows))) {
					// Website Level
					$websiteTotal = 0;
					foreach ($items as $value)
					{
						if ($value['website_id'] == $item['website_id'])
							$websiteTotal += $value['revenue'];
					}
						
					echo '<tr><td><strong>' . $item['website_name'] . '</strong></td><td><div style="width: 200px; text-align: right"><strong>' . Mage::helper('core')->currency($websiteTotal) . '</strong><div></td></tr>';
					$this->_revenueItems[$item['website_name']] = $websiteTotal;
					foreach ($items as $subKey => $subitem)
					{
						if (!(in_array($subKey, $completedRows))) {
							if ($subitem['website_id'] == $item['website_id'])
							{
								// Group Level
								$groupTotal = 0;
								foreach ($items as $value)
								{
									if ($value['group_id'] == $subitem['group_id'])
										$groupTotal += $value['revenue'];
								}
								
								echo '<tr><td style="padding-left: 25px"><em>' . $subitem['group_name'] . '</em></td><td style="padding-left: 25px"><div style="width: 200px; text-align: right"><em>' . Mage::helper('core')->currency($groupTotal) . '</em><div></td></tr>';
								foreach ($items as $baseKey => $baseitem)
								{
									if ($baseitem['group_id'] == $subitem['group_id'])
									{
										// Store Level
										echo '<tr><td style="padding-left: 50px">' . $baseitem['store_name'] . '</td><td style="padding-left: 50px"><div style="width: 200px; text-align: right">' . Mage::helper('core')->currency($baseitem['revenue']) . '</div></td></tr>';
										array_push($completedRows, $baseKey);
									}
								} 
							}
						}
					}
					$revenueTotal += $item['revenue'];
				}
			}
			return $revenueTotal;
		} else if ($revexpType == 'expense'){
			// Cost Of Goods
			$cogsTotal = 0;
			foreach ($items as $value){
				$cogsTotal += $value['cogs'];
			}
			echo '<tr><td><strong>Cost of Goods</strong></td><td><div style="width: 200px; text-align: right"><strong>' . Mage::helper('core')->currency($cogsTotal) . '</strong><div></td></tr>';
			$this->_expenseItems['Cost of Goods'] = $cogsTotal;
			
			$completedRows = array();
			
			foreach ($items as $key => $item)
			{
				if (!(in_array($key, $completedRows))) {
					// Website Level
					$websiteTotal = 0;
					foreach ($items as $value)
					{
						if ($value['website_id'] == $item['website_id'])
							$websiteTotal += $value['cogs'];
					}
						
					echo '<tr><td style="padding-left: 25px"><strong><em>' . $item['website_name'] . '</em></strong></td><td style="padding-left: 25px"><div style="width: 200px; text-align: right"><strong><em>' . Mage::helper('core')->currency($websiteTotal) . '</em></strong><div></td></tr>';
					foreach ($items as $subKey => $subitem)
					{
						if (!(in_array($subKey, $completedRows))) {
							if ($subitem['website_id'] == $item['website_id'])
							{
								// Group Level
								$groupTotal = 0;
								foreach ($items as $value)
								{
									if ($value['group_id'] == $subitem['group_id'])
										$groupTotal += $value['cogs'];
								}
								
								echo '<tr><td style="padding-left: 50px"><em>' . $subitem['group_name'] . '</em></td><td style="padding-left: 50px"><div style="width: 200px; text-align: right"><em>' . Mage::helper('core')->currency($groupTotal) . '</em><div></td></tr>';
								foreach ($items as $baseKey => $baseitem)
								{
									if ($baseitem['group_id'] == $subitem['group_id'])
									{
										// Store Level
										echo '<tr><td style="padding-left: 75px">' . $baseitem['store_name'] . '</td><td style="padding-left: 75px"><div style="width: 200px; text-align: right">' . Mage::helper('core')->currency($baseitem['cogs']) . '</div></td></tr>';
										array_push($completedRows, $baseKey);
									}
								} 
							}
						}
					}
				}
			}
			
			echo '<tr><td clospan="2">&nbsp;</td></tr>';
			
			// Tax
			$taxTotal = 0;
			foreach ($items as $value){
				$taxTotal += $value['tax'];
			}
			echo '<tr><td><strong>Tax</strong></td><td><div style="width: 200px; text-align: right"><strong>' . Mage::helper('core')->currency($taxTotal) . '</strong><div></td></tr>';
			$this->_expenseItems['Tax'] = $taxTotal;
			
			$completedRows = array();
			
			foreach ($items as $key => $item)
			{
				if (!(in_array($key, $completedRows))) {
					// Website Level
					$websiteTotal = 0;
					foreach ($items as $value)
					{
						if ($value['website_id'] == $item['website_id'])
							$websiteTotal += $value['tax'];
					}
						
					echo '<tr><td style="padding-left: 25px"><strong><em>' . $item['website_name'] . '</em></strong></td><td style="padding-left: 25px"><div style="width: 200px; text-align: right"><strong><em>' . Mage::helper('core')->currency($websiteTotal) . '</em></strong><div></td></tr>';
					foreach ($items as $subKey => $subitem)
					{
						if (!(in_array($subKey, $completedRows))) {
							if ($subitem['website_id'] == $item['website_id'])
							{
								// Group Level
								$groupTotal = 0;
								foreach ($items as $value)
								{
									if ($value['group_id'] == $subitem['group_id'])
										$groupTotal += $value['tax'];
								}
								
								echo '<tr><td style="padding-left: 50px"><em>' . $subitem['group_name'] . '</em></td><td style="padding-left: 50px"><div style="width: 200px; text-align: right"><em>' . Mage::helper('core')->currency($groupTotal) . '</em><div></td></tr>';
								foreach ($items as $baseKey => $baseitem)
								{
									if ($baseitem['group_id'] == $subitem['group_id'])
									{
										// Store Level
										echo '<tr><td style="padding-left: 75px">' . $baseitem['store_name'] . '</td><td style="padding-left: 75px"><div style="width: 200px; text-align: right">' . Mage::helper('core')->currency($baseitem['tax']) . '</div></td></tr>';
										array_push($completedRows, $baseKey);
									}
								} 
							}
						}
					}
				}
			}
			
			echo '<tr><td clospan="2">&nbsp;</td></tr>';
			
			// Shipping
			if ($currAtt['shipping_flag'] == 'true')
			{
				$shippingTotal = 0;
				foreach ($items as $value){
					$shippingTotal += $value['shipping'];
				}
				echo '<tr><td><strong>Shipping</strong></td><td><div style="width: 200px; text-align: right"><strong>' . Mage::helper('core')->currency($shippingTotal) . '</strong><div></td></tr>';
				$this->_expenseItems['Shipping'] = $shippingTotal;
				
				$completedRows = array();
				
				foreach ($items as $key => $item)
				{
					if (!(in_array($key, $completedRows))) {
						// Website Level
						$websiteTotal = 0;
						foreach ($items as $value)
						{
							if ($value['website_id'] == $item['website_id'])
								$websiteTotal += $value['shipping'];
						}
							
						echo '<tr><td style="padding-left: 25px"><strong><em>' . $item['website_name'] . '</em></strong></td><td style="padding-left: 25px"><div style="width: 200px; text-align: right"><strong><em>' . Mage::helper('core')->currency($websiteTotal) . '</em></strong><div></td></tr>';
						foreach ($items as $subKey => $subitem)
						{
							if (!(in_array($subKey, $completedRows))) {
								if ($subitem['website_id'] == $item['website_id'])
								{
									// Group Level
									$groupTotal = 0;
									foreach ($items as $value)
									{
										if ($value['group_id'] == $subitem['group_id'])
											$groupTotal += $value['shipping'];
									}
									
									echo '<tr><td style="padding-left: 50px"><em>' . $subitem['group_name'] . '</em></td><td style="padding-left: 50px"><div style="width: 200px; text-align: right"><em>' . Mage::helper('core')->currency($groupTotal) . '</em><div></td></tr>';
									foreach ($items as $baseKey => $baseitem)
									{
										if ($baseitem['group_id'] == $subitem['group_id'])
										{
											// Store Level
											echo '<tr><td style="padding-left: 75px">' . $baseitem['store_name'] . '</td><td style="padding-left: 75px"><div style="width: 200px; text-align: right">' . Mage::helper('core')->currency($baseitem['shipping']) . '</div></td></tr>';
											array_push($completedRows, $baseKey);
										}
									} 
								}
							}
						}
					}
				}
			}
			
			return ($cogsTotal + $taxTotal + $shippingTotal);
		}
	}
	
	protected function buildCoreAttributes()
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$salesFlatOrderItem = $resource->getTableName('sales_flat_order_item');
		$catalogProductEntityDecimal = $resource->getTableName('catalog_product_entity_decimal');
		if (Mage::helper('marcore')->checkMagentoVersion('1.4.0.1', '=') || Mage::helper('marcore')->checkMagentoVersion('1.4.1.0', '=')) {
			$salesFlatOrder = $resource->getTableName('sales_order');
		} else {
			$salesFlatOrder = $resource->getTableName('sales_flat_order');
		}
		$coreStore = $resource->getTableName('core_store');
		$coreStoreGroup = $resource->getTableName('core_store_group');
		$coreWebsite = $resource->getTableName('core_website');
		$salesOrder = $resource->getTableName('sales_order');
		$salesOrderVarchar = $resource->getTableName('sales_order_varchar');
		$eavAttribute = $resource->getTableName('eav_attribute');
		$eavEntityType = $resource->getTableName('eav_entity_type');

		
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		$currId = $this->getCurrId();
		
		$select = 'SELECT att_name, value
					FROM ' . $reportAttTable . '
					WHERE att_type_id = 1 AND report_id = ' . $currId;
		$reportAtt = $read->fetchAll($select);
		
		foreach ($reportAtt as $att)
		{
			$currAtt[$att['att_name']] = $att['value'];
		}
		
		// Change depending on whether using order cost, product cost or hybrid
		switch ($currAtt['cost_source'])
		{
			case 'order':
				$costHybridOption = 'Sum(orders.cogs) as cogs, '; //Cost from Orders Table Only
				break;
			case 'product':
				$costHybridOption = 'Sum(orders.cogs_prod_att) as cogs, '; //Cost from Product Attributes Only
				break;
			case 'hybrid':
				$costHybridOption = 'IF(Sum(orders.cogs) > 0 Or Sum(orders.cogs) is null, Sum(orders.cogs), Sum(orders.cogs_prod_att)) as cogs, '; //Cost from Both 
				break;
		}
		
		// Determin the product cost attribute code
		$select = '
			SELECT value
			FROM ' . $resource->getTableName('jtech_core_attributes') . '
			WHERE code = "cost_att_id"
		';		
		$costAttCode = $read->fetchRow($select);
		$costAttCode = $costAttCode['value'];
		
		// Set the Match Period
		if ($currAtt['match_period'] == 'created_at_order')
			$matchPeriod = 'a.created_at';
		else
			$matchPeriod = 'a.updated_at';
		
		// Convert time to GMT
		$from = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $currAtt['date_from']);
		$to = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $currAtt['date_to']);
		
		// Parse store ID information
		if ($currAtt['store_id'] == '') {
			// All Stores
			$storeClause = '';
		} else {
			// Specific Stores
			$storeMatch = explode(',', $currAtt['store_id']);
			
			switch ($storeMatch[0]){
				case 'w':
					// Website level requirement
					$select = 'SELECT store_id
								FROM core_store
								WHERE is_active = 1 AND website_id = ' . $storeMatch[1]
					;
					$storeIds = $read->fetchAll($select);
					
					for ($i = 0; $i < count($storeIds); $i++)
					{
						if ($i == 0)
							$id = $storeIds[$i]['store_id'];
						else
							$id = $id . ', ' . $storeIds[$i]['store_id'];
					}
					
					$storeClause = 'Where orders.store_id IN (' .$id . ')';
					break;
				case 'g':
					// Group level requirement
					$select = 'SELECT store_id
								FROM core_store
								WHERE is_active = 1 AND group_id = ' . $storeMatch[1]
					;
					$storeIds = $read->fetchAll($select);
					
					for ($i = 0; $i < count($storeIds); $i++)
					{
						if ($i == 0)
							$id = $storeIds[$i]['store_id'];
						else
							$id = $id . ', ' . $storeIds[$i]['store_id'];
					}
					
					$storeClause = 'Where orders.store_id IN (' .$id . ')';
					break;
				case 's':
					// Specific store
					$storeClause = 'Where orders.store_id = ' . $storeMatch[1];
					break;
			}
		}
		
		// Set the shipping flag
		if ($currAtt['shipping_flag'] == 'true') {
			$shippingSelect = ', Sum(orders.shipping) as shipping';
		} else {
			$shippingSelect = '';
		}
		
		if (Mage::helper('marcore')->checkMagentoVersion('1.4.0.0', '<')) {
			$select = 'Select  websites.website_id,
	                websites.name as website_name,
	                groups.group_id,
	                groups.name as group_name,
	                orders.store_id as store_id,
	                stores.name as store_name,
	                Sum(orders.revenue) as revenue,
					' . $costHybridOption . '
	                Sum(orders.tax) as tax
	                ' . $shippingSelect . '
					From
					(SELECT e.order_id,
			                a.created_at,
			                a.updated_at,
			                order_status.status,
			                a.store_id,
			                IFNULL(a.total_paid, 0) - (IFNULL(a.total_refunded, 0)) as "revenue",
			                SUM(IFNULL(e.cost * e.qty_invoiced, 0)) as "cogs",
			                SUM(IFNULL(prod_cost.cost * e.qty_invoiced, 0)) as "cogs_prod_att",
			                IFNULL(a.tax_invoiced, 0) - IFNULL(a.tax_refunded, 0) as "tax",
			                IFNULL(a.shipping_invoiced, 0) - IFNULL(a.shipping_refunded, 0) as "shipping"
					FROM ' . $salesFlatOrderItem . ' e 
					LEFT JOIN (
					Select entity_id, value as status
					From ' . $salesOrderVarchar . '
					Where attribute_id = 
					(
					Select attribute_id
					From ' . $eavAttribute . '
					Where attribute_code = "status" and entity_type_id = (Select entity_type_id
					From ' . $eavEntityType . ' 
					Where entity_type_code = "order")
					)
					) as order_status
					ON order_status.entity_id = e.order_id
					
					LEFT JOIN (
					Select entity_id, value as cost 
					From ' . $catalogProductEntityDecimal . '   
					Where attribute_id = ' . $costAttCode . ') as prod_cost ON prod_cost.entity_id = e.product_id
					LEFT JOIN ' . $salesOrder . ' a ON e.order_id = a.entity_id  
					WHERE e.product_type IN ("simple", "virtual", "grouped", "downloadable")
					AND order_status.status != "canceled"
					AND ' . $matchPeriod . ' BETWEEN "' . $from . '" AND "' . $to . '"
					GROUP BY e.order_id) orders
					Left Join ' . $coreStore . ' as stores ON stores.store_id = orders.store_id 
					Left Join ' . $coreStoreGroup . ' as groups ON groups.group_id = stores.group_id     
					Left Join ' . $coreWebsite . ' as websites ON websites.website_id = stores.website_id             
					' . $storeClause . '
					Group By orders.store_id
			';
		} else {
			// Magento versions greater than or equal to 1.4 or any newer developer versions
			$select = 'Select websites.website_id,
	                websites.name as website_name,
	                groups.group_id,
	                groups.name as group_name,
	                orders.store_id as store_id,
	                stores.name as store_name,
	                Sum(orders.revenue) as revenue,
	                ' . $costHybridOption . '
	                Sum(orders.tax) as tax 
	                ' . $shippingSelect . '
					From
					(SELECT e.order_id,
			                a.created_at,
			                a.updated_at,
			                a.status,
			                a.store_id,
			                IFNULL(a.total_paid, 0) - (IFNULL(a.total_refunded, 0)) as "revenue",
			                SUM(IFNULL(e.base_cost * e.qty_invoiced, 0)) as "cogs",
			                SUM(IFNULL(prod_cost.cost * e.qty_invoiced, 0)) as "cogs_prod_att",
			                IFNULL(a.tax_invoiced, 0) - IFNULL(a.tax_refunded, 0) as "tax",
			                IFNULL(a.shipping_invoiced, 0) - IFNULL(a.shipping_refunded, 0) as "shipping"
					FROM ' . $salesFlatOrderItem . ' e
					LEFT JOIN (
					Select entity_id, value as cost 
					From ' . $catalogProductEntityDecimal . ' 
					Where attribute_id = ' . $costAttCode . ') as prod_cost ON prod_cost.entity_id = e.product_id 
					LEFT JOIN ' . $salesFlatOrder . ' a ON e.order_id = a.entity_id 
					WHERE e.product_type IN ("simple", "virtual", "grouped", "downloadable")
					AND a.status != "canceled"
					AND ' . $matchPeriod . ' BETWEEN "' . $from . '" AND "' . $to . '"  
					GROUP BY e.order_id) orders
					Left Join ' . $coreStore . ' as stores ON stores.store_id = orders.store_id 
					Left Join ' . $coreStoreGroup . ' as groups ON groups.group_id = stores.group_id 
					Left Join ' . $coreWebsite . ' as websites ON websites.website_id = stores.website_id 
					' . $storeClause . '  
					Group By orders.store_id
			';
		}
		
		$this->_coreAttributes = $read->fetchAll($select);
	}
	
	protected function organiseItems($items, $target = null, $stepLevel = 0)
	{
		if ($stepLevel != 0)
		{
			$leftPadding = '; padding-left: ' . (25 * $stepLevel) . 'px';
			$rightPadding = '; padding-right: ' . (25 * $stepLevel) . 'px';
		} else {
			$leftPadding = $rightPadding = '';
		}
		$stepLevel++;
		
		foreach ($items as $item)
		{
			if ($item['parent_att_id'] == $target)
			{
				if ($item['is_parent'] == 1)
				{
					$sumValue = $this->findParentSum($items, $item['att_id']);
					echo '<tr><td style="width:50%' . $leftPadding . '"><strong>' . $item['att_name'] . '</strong></td><td style="width:50%; text-align:left' . $leftPadding . '"><div style="text-align: right; width: 200px"><strong>' . Mage::helper('core')->currency($sumValue) . '</strong></div></td></tr>';
					
					if ($item['parent_att_id'] == null && $item['att_type_id'] == 2)
						$this->_revenueItems[$item['att_name']] = $sumValue;
					else if ($item['parent_att_id'] == null && $item['att_type_id'] == 3)
						$this->_expenseItems[$item['att_name']] = $sumValue;
						
				} elseif ($item['parent_att_id'] == NULL) {
					echo '<tr><td style="width:50%' . $leftPadding . '"><strong>' . $item['att_name'] . '</strong></td><td style="width:50%; text-align:left' . $leftPadding . '"><div style="text-align: right; width: 200px"><strong>' . Mage::helper('core')->currency($item['value']) . '</strong></div></td></tr>';
				
					if ($item['att_type_id'] == 2)
						$this->_revenueItems[$item['att_name']] = $item['value'];
					else if ($item['att_type_id'] == 3)
						$this->_expenseItems[$item['att_name']] = $item['value'];
				} else {
					echo '<tr><td style="width:50%' . $leftPadding . '">' . $item['att_name'] . '</td><td style="width:50%; text-align:left' . $leftPadding . '"><div style="text-align: right; width: 200px">' . Mage::helper('core')->currency($item['value']) . '</div></td></tr>';
				}
				$this->organiseItems($items, $item['att_id'], $stepLevel);
			}
		}
	}
	
	protected function findParentSum($items, $target)
	{
		$sum = 0;
		
		foreach ($items as $item)
		{
			if ($item['parent_att_id'] == $target)
			{
				if ($item['value'] != '')
					$sum += $item['value'];
				else
					$sum += $this->findParentSum($items, $item['att_id']);
			}
		}
		return $sum;
	}
	
	protected function buildAttList($type)
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		
		if ($type == 'revenue')
		{
			$attType = 2;
		} else if ($type == 'expense') {
			$attType = 3;
		}
		
		$select = 'Select a.*
				From (SELECT *
				FROM ' . $reportAttTable . ' r
				WHERE r.att_type_id <> 1 AND (is_parent = 0 And value = "") is false AND report_id = ' . $this->getCurrId() . ') a
				Left Join
				(SELECT Distinct r.parent_att_id
				FROM ' . $reportAttTable . ' r
				WHERE r.att_type_id <> 1 AND (is_parent = 0 And value = "") is false AND parent_att_id is not null AND report_id = ' . $this->getCurrId() . ') b
				On a.att_id = b.parent_att_id
				Where (a.is_parent = 1 and b.parent_att_id is null) is false  AND att_type_id = ' . $attType . '
				ORDER BY a.att_name ASC';
		
		$result = $read->fetchAll($select);
		
		if ($result == null)
			return false;
		else
			return $read->fetchAll($select);
	}
	
	protected function getExpenseItems()
	{
		// Core magento Items
		$dirtyCoreExpenseItems = $this->getCoreAttributes();
		
		if ($dirtyCoreExpenseItems != null)
		{
			$coreExpenseSumTotal = $this->organiseCoreItems('expense', $dirtyCoreExpenseItems);
			
			echo '<tr><td clospan="2">&nbsp;</td></tr>';
			echo '<tr><td><strong><em>Store Expenses Total</em><strong></td><td><div style="text-align: right; width: 200px"><strong><em>' . Mage::helper('core')->currency($coreExpenseSumTotal) . '</em></strong></div></td></tr>';
		} else {
			echo '<tr><td colspan="2" style="text-align: center; padding-top: 10px">There are no store expense items</td></tr>';
		}
		
		// Additional Items
		echo '<tr><td colspan="2">&nbsp;</td></tr>';
		echo '<tr><th colspan="2" style="text-align: left; border-bottom: 1px solid #6F8992; text-align: center; font-style: italic">Additional Expenses</th></tr>';
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		
		$select = 'SELECT SUM(value) as total
					FROM ' . $reportAttTable . ' 
					WHERE att_type_id = 3
					AND report_id = ' . $this->getCurrId() . '
		';
		$result = $read->fetchRow($select);
		
		$expenseSumTotal = 0;
		if ($result['total'] != 0)
		{
			$dirtyItems = $this->buildAttList('expense');
	
			$expenseItems = $this->organiseItems($dirtyItems);
			
			$expenseSumTotal = $this->findParentSum($dirtyItems, null);
			
			echo '<tr><td clospan="2">&nbsp;</td></tr>';
			echo '<tr><td><strong><em>Additional Expenses Total</em><strong></td><td><div style="text-align: right; width: 200px"><strong><em>' . Mage::helper('core')->currency($expenseSumTotal) . '</em></strong></div></td></tr>';
		} else {
			echo '<tr><td colspan="2" style="text-align: center; padding-top: 10px">There are no set additional expense items</td></tr>';
		}
		
		$this->_totalExpenses = $coreExpenseSumTotal + $expenseSumTotal;
		
		echo '<tr><th colspan="2" style="border-bottom: 1px solid #6F8992; font-style: italic">&nbsp;</th></tr>';
		echo '<tr><th style="text-align: center; font-style: italic">Total Expenses</th><th style="text-align: center; font-style: italic">' . Mage::helper('core')->currency($this->getTotalExpenses()) . '</th></tr>';
	}
	
	protected function getReportSummary()
	{
		$profitFigure = ($this->getTotalRevenue() - $this->getTotalExpenses());
		
		if ($profitFigure > 0) {
			$profitStatement = '<td style="width: 50%; text-align: right; color: green; font-size: medium; font-weight: bold">' . Mage::helper('core')->currency($profitFigure) . '</td>';
		} else {
			$profitStatement = '<td style="width: 50%; text-align: right; color: red; font-size: medium; font-weight: bold">' . Mage::helper('core')->currency($profitFigure) . '</td>';
		}
		
		$profitMargin = round(($profitFigure / $this->getTotalRevenue()) * 100);
		
		if ($profitMargin > 0) {
			$profitMarginStatement = '<td style="width: 50%; text-align: right; color: green; font-size: medium; font-weight: bold">' . $profitMargin . '&#37;</td>';
		} else {
			$profitMarginStatement = '<td style="width: 50%; text-align: right; color: red; font-size: medium; font-weight: bold">' . $profitMargin . '&#37;</td>';
		}
		
		$htmlOutput = '
			<table style="width: 100%">
				<tr>
					<td style="width: 50%"><strong>Total Expenses</strong></td>
					<td style="width: 50%; text-align: right"><strong>' .  Mage::helper('core')->currency($this->getTotalExpenses()) . '</strong></td>
				</tr>
				<tr>
					<td style="width: 50%"><strong>Total Revenue</strong></td>
					<td style="width: 50%; text-align: right"><strong>' . Mage::helper('core')->currency($this->getTotalRevenue()) . '</strong></td>
				</tr>
				<tr><th colspan="2" style="border-bottom: 1px solid #6F8992; font-style: italic">&nbsp;</th></tr>
				<tr>
					<td style="width: 50%"><strong>Profit&nbsp;&nbsp;&nbsp;&nbsp;<em>( Total Revenue - Total Expenses )</em></strong></td>
					' . $profitStatement . '
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td style="width: 50%"><strong>Profit Margin</strong></td>
					' . $profitMarginStatement . '
				</tr>
				<tr><th colspan="2" style="border-top: 1px solid #6F8992; font-style: italic">&nbsp;</th></tr>
			</table>
		';
		
		return $htmlOutput;
	}
	
	protected function displayDataGrid()
	{
		$reportData = $this->getReportData();
		$timezone = new DateTimeZone(Mage::app()->getStore()->getConfig('general/locale/timezone'));
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');

		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		$currId = $this->getCurrId();
		
		$select = 'SELECT att_name, value
					FROM ' . $reportAttTable . '
					WHERE att_type_id = 1 AND report_id = ' . $currId;
		$reportAtt = $read->fetchAll($select);
		
		foreach ($reportAtt as $att)
		{
			$currAtt[$att['att_name']] = $att['value'];
		}		
		
		// First Record
		echo '<tr class="headings">';
		foreach ($reportData[0] as $key => $value) {
			echo '<th class=" no-link"><span class="nobr">' . $key . '</span></th>';
			$totals[$key] = 0;
		}
		echo '</tr>';

		$i = 0;
		foreach ($reportData as $period)
		{
			if ($i >= 1 && $i & 1)
			{
				// Even Row
				echo '<tr class="even">';
			} else {
				// Odd Row
				echo '<tr>';
			}
			$y = 0;
			foreach ($period as $key => $value) {
				if ($y == 0) {
					if ($currAtt['period'] == 'order') {
						$date = new DateTime($value);
						$date->setTimeZone($timezone);
						
						echo '<td rowspan="1"><span class="nobr">' . date_format($date, 'd/m/Y g:i:s A') . '<span></td>';
					} else {
						echo '<td rowspan="1"><span class="nobr">' . $value . '<span></td>';
					}
				} else {
					if ($key == 'Customer First Name' || $key == 'Customer Last Name')
					{
						echo '<td class=" a-right ">' . $value . '</td>';
					} else if ($key == 'Orders' || $key == 'Sales Items' || $key == 'Items' || $key == 'Order Number')
					{
						echo '<td class=" a-right ">' . round($value) . '</td>';
					} else {
						echo '<td class=" a-right ">' . Mage::helper('core')->currency($value) . '</td>';
					}
				}
				$totals[$key] += $value;
				$y++;
			}
			echo '</tr>';
			
			$i++;
		}
		
		// Last Record
		echo '<tfoot><tr class="totals">';
		$y = 0;
		foreach ($totals as $key => $value) {
			if ($y == 0){
				echo '<th>Totals</th>';
			} else {
				if ($key == 'Customer First Name' || $key == 'Customer Last Name')
				{
					echo '<th class=" a-right">&nbsp;</td>';
				} else if ($key == 'Orders' || $key == 'Sales Items' || $key == 'Items' || $key == 'Order Number')
					echo '<th class=" a-right">' . round($value) . '</th>';
				else
					echo '<th class=" a-right">' . Mage::helper('core')->currency($value) . '</th>';
			}
				
			$y++;
		}
		echo '</tr></tfoot>';
	}
	
	protected function buildGraphImage($type)
	{
		$chartUrl = 'http://chart.apis.google.com/chart?';
		
		if ($type == 'revenue') {
			if ($this->getTotalRevenue() == 0) {
				return 'There are no revenue items';
			}
		
			$chartType = 'cht=p3';
			$chartSizeWH = 'chs=640x225';
			$chartColour = 'chco=0079C2';
			$chartLabels = 'chl=';
			$chartData = 'chd=t:';
			
			$i = 0;
			foreach ($this->_revenueItems as $key => $value)
			{
				if ($i == 0)
				{
					$chartLabels = $chartLabels . $key;
					$chartData = $chartData . ROUND((($value / $this->getTotalRevenue()) * 100));
					$i++;
				} else {
					$chartLabels = $chartLabels . '|' . $key;
					$chartData = $chartData . ',' . ROUND((($value / $this->getTotalRevenue()) * 100));
				}
			}
		} else if ($type == 'expense') {
			if ($this->getTotalExpenses() == 0) {
				return 'There are no expense items';
			}
		
			$chartType = 'cht=p3';
			$chartSizeWH = 'chs=640x225';
			$chartColour = 'chco=F25E00';
			$chartLabels = 'chl=';
			$chartData = 'chd=t:';
			
			$i = 0;
			foreach ($this->_expenseItems as $key => $value)
			{
				if ($i == 0)
				{
					$chartLabels = $chartLabels . $key;
					$chartData = $chartData . ROUND((($value / $this->getTotalRevenue()) * 100));
					$i++;
				} else {
					$chartLabels = $chartLabels . '|' . $key;
					$chartData = $chartData . ',' . ROUND((($value / $this->getTotalRevenue()) * 100));
				}
			}			
		} else if ($type == 'summary') {
			// Gather Data
			$expenses = round($this->getTotalExpenses());
			$revenue = round($this->getTotalRevenue());
			$profit = round($this->getTotalRevenue() - $this->getTotalExpenses());
			
			if ($profit >= 0)
				$startRange = 0;
			else 
				$startRange = ($profit * 1.1);
				
			if ($revenue > $expenses)
				$endRange = ($revenue * 1.1);
			else
				$endRange = ($expenses * 1.1);
			
			if ($profit > 0)
				$profitColour = '008000';
			else 
				$profitColour = 'FF0000';
				
			$chartType = 'cht=bhg';
			$chartSizeWH = 'chs=640x200';
			$chartLabels = 'chxl=1:|Profit|Revenue|Expenses';
			$chartColour = 'chco=F25E00|0079C2|' . $profitColour . '&chxt=x,y&chxr=0,' . $startRange . ',' . $endRange . '&chds=' . $startRange . ',' . $endRange . '&chbh=35,0,15&chg=8.33,0,5,5';

			$chartData = 'chd=t:' . $expenses . ',' . $revenue . ',' . $profit;
		} else if ($type == 'salesovertime') {
			$reportData = $this->getReportData();
			
			$chartType = 'cht=lc';
			$chartSizeWH = 'chs=1000x250';
			$chartLabels = 'chdl=Revenue|Expense|Profit';

			$labelPerChart = floor(965 / 100);
			$reportLen = count($reportData);
			
			if ($reportLen < $labelPerChart)
			{
				// Show all labels
				$labelInterval = 1;
			} else {
				// Show only intervals
				$labelInterval = ceil($reportLen / $labelPerChart);
			}
			
			$i = 0;
			foreach ($reportData as $period)
			{
				if ($i == 0)
				{
					$largestRev = $revData = round($period['Revenue']);
					
					if (isset($period['Shipping']))
						$largestExp = $expData = round(($period['Cost of Goods'] + $period['Tax'] + $period['Shipping']));
					else
						$largestExp = $expData = round(($period['Cost of Goods'] + $period['Tax']));
						
					$lowestProfit = $profData = $revData - $expData;
					
					$periodData = $period['Period'];
				} else {
					$revData = $revData . ',' . round($period['Revenue']);
					
					if (isset($period['Shipping']))
						$tempExp = round(($period['Cost of Goods'] + $period['Tax'] + $period['Shipping']));
					else
						$tempExp = round(($period['Cost of Goods'] + $period['Tax']));
					
					$expData = $expData . ',' . $tempExp;	
						
					$profData = $profData . ',' . (round($period['Revenue']) - $tempExp);
					
					if ($largestRev < (round($period['Revenue'])))
						$largestRev = (round($period['Revenue']));
						
					if ($largestExp < $tempExp)
						$largestExp = $tempExp;
						
					if ($lowestProfit > (round($period['Revenue']) - $tempExp))
						$lowestProfit = (round($period['Revenue']) - $tempExp);
					
					if ($i % $labelInterval)
					{
						$periodData = $periodData . '|+';
					} else {
						$periodData = $periodData . '|' . $period['Period'];
					}
				}
				$i++;
			}
			$chartData = 'chd=t:' . $revData . '|-1|' . $profData . '|-1|' . $expData;
			
			$chartRange = 'chxr=1,';
			
			if ($lowestProfit >= 0)
				$startRange = 0;
			else 
				$startRange = ($lowestProfit * 1.1);
				
			if ($largestRev > $largestExp)
				$endRange = ($largestRev * 1.1);
			else
				$endRange = ($largestExp * 1.1);
				
			$periodData = str_replace(' ', '+', $periodData);
			
			$chartColour = 'chco=008000,FF0000,0079C2&' . $chartRange . $startRange . ',' . $endRange . '&chds=' . $startRange . ',' . $endRange . '&chls=3,1,0&chxt=x,y' . '&chxl=0:|' . $periodData;

		}
		
		$finalUrl = $chartUrl . $chartType . '&' . $chartSizeWH . '&' . $chartData . '&' . $chartLabels . '&' . $chartColour;
		
		if (strlen($finalUrl) < 2000)
		{
			$imgObj = '<img src="' . $finalUrl . '" />';
		
			return $imgObj;
		} else
		{
			return 'The number of records to graph is too great<br />please increase the period length or decrease the date range.';
		}
	}
}