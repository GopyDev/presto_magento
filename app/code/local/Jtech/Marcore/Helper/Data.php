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

class Jtech_Marcore_Helper_Data extends Mage_Core_Helper_Abstract {
	public function checkMagentoVersion($version, $operator)
	{
		$mageVersion = Mage::getVersion();
		
		$firstDecimal = strpos($mageVersion, '.');
		$secondDecimal = strpos($mageVersion, '.', $firstDecimal + 1);
		$thirdDecimal = strpos($mageVersion, '.', $secondDecimal + 1);
		
		// Gets the true 4 digit version number of magento without any extra data on end
		$cleanVersion = substr($mageVersion, 0, $thirdDecimal + 2);
		
		return version_compare($cleanVersion, $version, $operator);
	}
	
	public function getCostAttributeCode() 
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
    	
		$coreAttTable = $resource->getTableName('jtech_core_attributes');
		
		$select = '
			SELECT	value
			FROM	' . $coreAttTable . '
			WHERE code = "cost_att_code"
		';
		
		$costCode = $read->fetchRow($select);
		
		return $costCode['value'];
	}
	
	public function getCostAttributeId() 
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
    	
		$coreAttTable = $resource->getTableName('jtech_core_attributes');
		
		$select = '
			SELECT	value
			FROM	' . $coreAttTable . '
			WHERE code = "cost_att_id"
		';
		
		$costId = $read->fetchRow($select);
		
		return (int)$costId['value'];
	}
	
	public function getNameAttributeCode() 
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
    	
		$coreAttTable = $resource->getTableName('jtech_core_attributes');
		
		$select = '
			SELECT	value
			FROM	' . $coreAttTable . '
			WHERE code = "name_att_code"
		';
		
		$nameCode = $read->fetchRow($select);
		
		return $nameCode['value'];
	}
	
	public function getNameAttributeId() 
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
    	
		$coreAttTable = $resource->getTableName('jtech_core_attributes');
		
		$select = '
			SELECT	value
			FROM	' . $coreAttTable . '
			WHERE code = "name_att_id"
		';
		
		$nameId = $read->fetchRow($select);
		
		return (int)$nameId['value'];
	}
}