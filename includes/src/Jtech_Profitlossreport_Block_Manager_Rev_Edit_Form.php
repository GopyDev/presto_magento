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

class Jtech_Profitlossreport_Block_Manager_Rev_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getUrl('*/*/saverevenue', array('id' => $this->getRequest()->getParam('id'))),
			'method'	=> 'post'
		));
		
		$fieldset = $form->addFieldset('attributes_form_', array('legend' => Mage::helper('profitlossreport')->__('Revenue Item Details')));
		
		$fieldset->addField('name', 'text', array(
			'name'		=> 'name',
			'label'		=> Mage::helper('profitlossreport')->__('Item Name'),
			'required'  => true
		));
        
        $parentItems = $this->getParentItems();
        
        $fieldset->addField('parent_id', 'select', array(
            'name'      => 'parent_id',
            'options'   => $parentItems,
            'label'     => Mage::helper('profitlossreport')->__('Does this Item have a Parent'),
        ));
		
		if ( Mage::getSingleton('adminhtml/session')->getAttributeData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getAttributeData());
			Mage::getSingleton('adminhtml/session')->setAttributeData(null);
		} elseif ( Mage::registry('attribute_data') ) {
			$form->setValues(Mage::registry('attribute_data')->getData());
		}
		
		$form->setUseContainer(true);
		$this->setForm($form);
		
		return parent::_prepareForm();
	}
	
	protected function getParentItems($parentItems = array(0 => 'No Parent Item'), $currCat = 0, $stepLevel = 0)
	{	
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		
		$attributeTable = $resource->getTableName('jtech_pl_attributes');
		
		// Build where clause
		$whereClause = 'att_type_id = 2';
		
		if ($currCat == 0)
		{
			$whereClause = $whereClause . ' AND parent_id IS NULL';
			
			if ($this->getRequest()->getParam('id') > 0)
				$whereClause = $whereClause . ' AND att_id <> ' . $this->getRequest()->getParam('id');
		} else {
			$whereClause = $whereClause . ' AND parent_id = ' . $currCat;
			
			if ($this->getRequest()->getParam('id') > 0)
				$whereClause = $whereClause . ' AND att_id <> ' . $this->getRequest()->getParam('id');
		}
		
		$indent = '';
		if ($stepLevel != 0)
		{
			for ($i = 0; $i < $stepLevel; $i++)
			{
				$indent = $indent . '--';
			}
		}
		$stepLevel++;
		
		$select = $read->select()
			->from($attributeTable, array('att_id', 'name', 'is_parent'))
			->where($whereClause)
			->order('name ASC');
		$attributes = $read->fetchAll($select);
		
    	foreach ($attributes as $attribute)
    	{
    		$parentItems[$attribute['att_id']] = $indent . ' ' . $attribute['name'];
    		$parentItems = $this->getParentItems($parentItems, $attribute['att_id'], $stepLevel);
    	}
    	
		return $parentItems;
	}
}