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

class Jtech_Profitlossreport_Block_Manager_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('reports_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('profitlossreport')->__('Report Properties'));
	}
	
	public function _beforeToHtml()
	{
		$this->addTab('filter_section', array(
			'label'		=> Mage::helper('profitlossreport')->__('Report Filter'),
			'title'		=> Mage::helper('profitlossreport')->__('Report Filter'),
			'content'	=> $this->getLayout()->getBlock('profitlossreport.manager_edit_tabs_filterform')->toHtml()
		));
		
		$this->addTab('add_rev_exp_section', array(
			'label'		=> Mage::helper('profitlossreport')->__('Additional Revenue & Expenses'),
			'title'		=> Mage::helper('profitlossreport')->__('Additional Revenue & Expenses'),
			'content'	=> $this->getLayout()->getBlock('profitlossreport.manager_edit_tabs_revexpform')->toHtml()
		));
		
		return parent::_beforeToHtml();
	}
}