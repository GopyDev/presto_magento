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

class Jtech_Profitlossreport_Block_Manager_Revexp extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'profitlossreport';
		$this->_controller = 'manager_revexp';
		
		parent::__construct();
		
		$this->_headerText = Mage::helper('profitlossreport')->__('Create New & Manage Existing Revenue and Expense Items');
		$this->_removeButton('add');
		
		$this->_addButton('addrevenue', array(
            'label'     => Mage::helper('profitlossreport')->__('Create a new Revenue Item'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/newrevenue') .'\')',
            'class'     => 'add',
        ));
        
        $this->_addButton('addexpense', array(
            'label'     => Mage::helper('profitlossreport')->__('Create a new Expense Item'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/newexpense') .'\')',
            'class'     => 'add',
        ));
	}
}