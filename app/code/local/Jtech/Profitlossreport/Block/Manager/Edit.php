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

class Jtech_Profitlossreport_Block_Manager_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'profitlossreport';
		$this->_controller = 'manager';
		
		$this->_updateButton('save', 'label', Mage::helper('profitlossreport')->__('Save Report'));
		$this->_updateButton('save', 'onclick', 'justsave();');
		$this->_updateButton('delete', 'label', Mage::helper('profitlossreport')->__('Delete Report'));
		
		$this->_addButton('saveview', array(
            'label'     => Mage::helper('adminhtml')->__('Save & View Report'),
            'onclick'   => 'saveview();',
            'class'     => 'save',
        ), 1);
	}
	
	public function getHeaderText()
	{
		if( Mage::registry('report_data') && Mage::registry('report_data')->getId() )
		{
			return Mage::helper('profitlossreport')->__('Edit Report');
		} else {
			return Mage::helper('profitlossreport')->__('Create Report');
		}
	}
}