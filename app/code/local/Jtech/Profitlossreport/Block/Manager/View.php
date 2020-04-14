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

class Jtech_Profitlossreport_Block_Manager_View extends Mage_Adminhtml_Block_Widget_Container // Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'profitlossreport';
		$this->_controller = 'manager_view';
		
		parent::__construct();
		
		$this->setTemplate('profitlossreport/viewcontainer.phtml');
		
		$this->_headerText = Mage::helper('profitlossreport')->__('Viewing a Profit & Loss Report');
		
		$this->addButton('showall', array(
			'label'     => 'Show Another Report',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/viewall') .'\')'
		));
	}
	
	public function addButton($id, $data, $level = 0, $sortOrder = 100, $area = 'header')
    {
        return $this->_addButton($id, $data, $level, $sortOrder, $area);
    }
}