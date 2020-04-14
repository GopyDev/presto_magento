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

class Jtech_Profitlossreport_Block_Manager_Edit_Tabs_Revexpform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('profitlossreport/revexpform.phtml');
    }
    
	protected function getAttItems($type, $attItems = array(), $currCat = 0, $stepLevel = 0)
	{
		if ($this->getRequest()->getParam('id') != '')
			$currId = $this->getRequest()->getParam('id');
		else
			$currId = -1;
		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$attributeTable = $resource->getTableName('jtech_pl_attributes');
		
		// Build where clause
		if ($type == 'revenue')
		{
			// Revenue Items Only
			$attType = '2';
		} else if ($type == 'expense') {
			// Expense Items Only
			$attType = '3';
		}
		$whereClause = 'att_type_id = ' . $attType;
		
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
		
		$reportAttTable = $resource->getTableName('jtech_pl_reports_attributes');
		
		if ($currId > 0) {
			$select = '
				SELECT att_id,
					value
				FROM ' . $reportAttTable . '
				WHERE att_type_id = ' . $attType . '
				AND report_id = ' . $currId . '
			';
			$reportAttOptions = $read->fetchAll($select);
	    	
	    	foreach ($reportAttOptions as $option) {
	    		$reportAtt[$option['att_id']] = $option['value'];
	    	}
		}

		foreach ($attributes as $attribute)
    	{
    		if ($attribute['is_parent'] == 1)
    		{
    			$inputBox = '<input type="text" class=" input-text" value="" name="' . $attribute['att_id'] . '" id="' . $attribute['att_id'] . '" style="text-align: right; background-color: #eee" disabled>';
    		} else {
    			$inputBox = '<input type="text" class=" input-text" value="' . (isset($reportAtt[$attribute['att_id']]) ? $reportAtt[$attribute['att_id']] : '' ) . '" name="' . $attribute['att_id'] . '" id="' . $attribute['att_id'] . '" style="text-align: right" onkeypress="return numbersonly(this, event, true);" >';
    		}
    		
    		if ($attribute['parent_id'] == NULL || $attribute['is_parent'] == '1') {
    			$attItems[$attribute['att_id']] = '<div><div style="display: inline-block; width: 140px">' . $inputBox . '</div><strong>' . $indent . ' ' . $attribute['name'] . '</strong></div>';
    		} else {
    			$attItems[$attribute['att_id']] = '<div><div style="display: inline-block; width: 140px">' . $inputBox . '</div>' . $indent . ' ' . $attribute['name'] . '</div>';
    		}

    		$attItems = $this->getAttItems($type, $attItems, $attribute['att_id'], $stepLevel);
    	}
    	
		return $attItems;
	
	}
}