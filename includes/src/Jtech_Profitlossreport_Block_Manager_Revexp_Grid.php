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

class Jtech_Profitlossreport_Block_Manager_Revexp_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		// Define my new template with tree view capability!
		$this->setTemplate('profitlossreport/manage_att_tree.phtml');
		
		$this->setId('attributesGrid');
		$this->setDefaultSort('name');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	protected function getAttItems($type, $attItems = array(), $currCat = 0, $stepLevel = 0)
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$attributeTable = $resource->getTableName('jtech_pl_attributes');
		
		// Build where clause
		if ($type == 'revenue')
		{
			// Revenue Items Only
			$whereClause = 'att_type_id = 2';
		} else if ($type == 'expense') {
			// Expense Items Only
			$whereClause = 'att_type_id = 3';
		}

		if ($currCat == 0)
		{
			$whereClause = $whereClause . ' AND parent_id IS NULL';
		} else {
			$whereClause = $whereClause . ' AND parent_id = ' . $currCat;
		}
		
		$indent = '';
		if ($stepLevel != 0)
		{
			for ($i = 0; $i < $stepLevel; $i++)
			{
				$indent = $indent . '----';
			}
		}
		$stepLevel++;
		
		$select = $read->select()
			->from($attributeTable, '*')
			->where($whereClause)
			->order('name ASC');
		$attributes = $read->fetchAll($select);
		
		foreach ($attributes as $attribute)
    	{
    		if ($attribute['parent_id'] == NULL || $attribute['is_parent'] == '1') {
    			$attItems[$attribute['att_id']] = '<strong>' . $indent . ' ' . $attribute['name'] . '</strong> (<a href="' . $this->getUrl('*/*/edit' . $type, array('id' => $attribute['att_id'])) . '">edit</a>)<br />';
    		} else {
    			$attItems[$attribute['att_id']] = $indent . ' ' . $attribute['name'] . ' (<a href="' . $this->getUrl('*/*/edit' . $type, array('id' => $attribute['att_id'])) . '">edit</a>)<br />';
    		}
    		
    		
    		$attItems = $this->getAttItems($type, $attItems, $attribute['att_id'], $stepLevel);
    	}
    	
		return $attItems;
	
	}
}