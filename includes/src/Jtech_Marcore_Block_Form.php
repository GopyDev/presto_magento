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

class Jtech_Marcore_Block_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_coreAtt;
	
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('marcore/form.phtml');
        
        $this->buildCoreAtt();
    }
    
    protected function getCoreAtt()
    {
    	return $this->_coreAtt;
    }
    
    protected function buildCoreAtt()
    {
    	$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
    	
		$coreAttTable = $resource->getTableName('jtech_core_attributes');
		
		$select = '
			SELECT	*
			FROM	' . $coreAttTable . '
		';
		
		$this->_coreAtt = $read->fetchAll($select);
    }
    
    protected function getCostStatement()
    {
    	$coreAtt = $this->getCoreAtt();
    	
    	foreach ($coreAtt as $att)
    	{
    		if ($att['code'] == 'cost_att_code') {
    			$costAttCode = $att['value'];
    		} else if ($att['code'] == 'cost_att_id') {
    			$costAttId = $att['value'];
    		}
    	}
    	
    	if ($costAttId != '') {
    		return '"' . $costAttCode . '" was found with the ID "' . $costAttId . '"!';
    	} else {
    		return '"' . $costAttCode . '" was not found!';
    	}
    }
    
    protected function getNameStatement()
    {
    	$coreAtt = $this->getCoreAtt();
    	
    	foreach ($coreAtt as $att)
    	{
    		if ($att['code'] == 'name_att_code') {
    			$nameAttCode = $att['value'];
    		} else if ($att['code'] == 'name_att_id') {
    			$nameAttId = $att['value'];
    		}
    	}
    	
    	if ($nameAttId != '') {
    		return '"' . $nameAttCode . '" was found with the ID "' . $nameAttId . '"!';
    	} else {
    		return '"' . $nameAttCode . '" was not found!';
    	}
    }
    
    protected function getCostAttCode()
    {
    	$coreAtt = $this->getCoreAtt();
    	
    	foreach ($coreAtt as $att)
    	{
    		if ($att['code'] == 'cost_att_code') {
    			return $att['value'];
    		}
    	}
    }
    
	protected function getNameAttCode()
    {
    	$coreAtt = $this->getCoreAtt();
    	
    	foreach ($coreAtt as $att)
    	{
    		if ($att['code'] == 'name_att_code') {
    			return $att['value'];
    		}
    	}
    }
}