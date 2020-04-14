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

class Jtech_Profitlossreport_Block_Manager_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('reportsGrid');
		$this->setDefaultSort('date_updated');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('profitlossreport/report')->getCollection();
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('name', array(
			'header'	=> Mage::helper('profitlossreport')->__('Report Name'),
			'align'		=> 'left',
			'index'		=> 'name'
		));
		
		$this->addColumn('date_created', array(
			'header'	=> Mage::helper('profitlossreport')->__('Created On'),
			'align'		=> 'right',
			'width'		=> '150px',
			'index'		=> 'date_created',
			'type'		=> 'datetime'
		));
		
		$this->addColumn('date_updated', array(
			'header'	=> Mage::helper('profitlossreport')->__('Last Updated On'),
			'align'		=> 'right',
			'width'		=> '150px',
			'index'		=> 'date_updated',
			'type'		=> 'datetime'
		));
		
		$this->addColumn('action_edit', array(
			'header'	=> Mage::helper('profitlossreport')->__(' '),
			'width'		=> '100px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption'	=> Mage::helper('profitlossreport')->__('Edit'),
					'url'		=> array('base' => '*/*/edit'),
					'field'		=> 'id'
				)
			),
			'filter'	=> false,
			'sortable'	=> false,
			'index'		=> 'stores',
			'is_system'	=> true
		));
		
		$this->addColumn('action_delete', array(
			'header'	=> Mage::helper('profitlossreport')->__(' '),
			'width'		=> '100px',
			'type'		=> 'action',
			'getter'	=> 'getId',
			'actions'	=> array(
				array(
					'caption'	=> Mage::helper('profitlossreport')->__('Delete'),
					'url'		=> array('base' => '*/*/delete'),
					'field'		=> 'id'
				)
			),
			'filter'	=> false,
			'sortable'	=> false,
			'index'		=> 'stores',
			'is_system'	=> true
		));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/view', array('id' => $row->getId()));
	}
}