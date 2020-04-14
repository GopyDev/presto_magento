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

class Jtech_Profitlossreport_Block_Manager_Exp_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'profitlossreport';
		$this->_controller = 'manager_exp';
		
		$this->_updateButton('save', 'label', Mage::helper('profitlossreport')->__('Save Expense Item'));
		$this->_updateButton('delete', 'label', Mage::helper('profitlossreport')->__('Delete Expense Item'));
		$this->_updateButton('delete', 'onclick', 'deleteConfirm(\''. Mage::helper('profitlossreport')->__('Are you sure you want to do this? Doing this will delete any existing sub-items!').'\', \'' . $this->getDeleteUrl() . '\')');
	}
	
	public function getHeaderText()
	{
		if( Mage::registry('attribute_data') && Mage::registry('attribute_data')->getId() )
		{
			return Mage::helper('profitlossreport')->__('Edit Expense Item');
		} else {
			return Mage::helper('profitlossreport')->__('Create Expense Item');
		}
	}
	
	public function getBackUrl()
    {
        return $this->getUrl('*/*/viewattribute');
    }

	public function getDeleteUrl()
    {
        return $this->getUrl('*/*/deleteexpense', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}