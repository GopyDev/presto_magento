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

class Jtech_Marcore_SettingsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
        	->_setActiveMenu('report/marcore')
        	->_addBreadcrumb(Mage::helper('marcore')->__('Reports'), Mage::helper('marcore')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('marcore')->__('Jtech Adavanced Reporting'), Mage::helper('marcore')->__('Jtech Adavanced Reporting'))
            ->_addBreadcrumb(Mage::helper('marcore')->__('Settings'), Mage::helper('marcore')->__('Settings'));
        
        $this->renderLayout();
    }

	public function saveAction()
	{
		$postData = $this->getRequest()->getPost();

		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$write = $resource->getConnection('core_write');
		
		$coreAttTable = $resource->getTableName('jtech_core_attributes');
		$eavAttTable = $resource->getTableName('eav_attribute');
		$eavEntityTypeTable = $resource->getTableName('eav_entity_type');
		
		try {
			$i = 0;
			foreach ($postData as $key => $value) {
				if ($i != 0) {
					// Update code values
					$write->query('UPDATE ' . $coreAttTable . ' SET value = "' . $value . '" WHERE code = "' . $key . '"');
					
					// Update ID values
					$select = '
						Select attribute_id
						From ' . $eavAttTable . '
						Where attribute_code = "' . $value . '" and entity_type_id = (
							Select entity_type_id
							From ' . $eavEntityTypeTable . ' 
							Where entity_type_code = "catalog_product")
					';
					$attID = $read->fetchRow($select);
					$attID = $attID['attribute_id'];
					
					if ($key == 'cost_att_code') {
						$write->query('UPDATE ' . $coreAttTable . ' SET value = "' . $attID . '" WHERE code = "cost_att_id"');
					} else if ($key == 'name_att_code') {
						$write->query('UPDATE ' . $coreAttTable . ' SET value = "' . $attID . '" WHERE code = "name_att_id"');
					}
				} else {
					// Do nothing as the first element is the encrypted key
				}
				$i++;
			}
			
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The settings were updated'));
			$this->_redirect('*/*');
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*');
		}
	}
}