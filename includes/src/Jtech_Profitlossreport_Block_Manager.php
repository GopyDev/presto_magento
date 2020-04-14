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

class Jtech_Profitlossreport_Block_Manager extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'profitlossreport';
		$this->_controller = 'manager';
		
		$this->_headerText = Mage::helper('profitlossreport')->__('Create New & Manage Existing Reports');
		$this->_addButtonLabel = Mage::helper('profitlossreport')->__('Create a Report');
		
		parent::__construct();
	}
}