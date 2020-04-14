<?php

/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Gifts
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
 
class Belvg_Gifts_Block_Adminhtml_Gifts_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('giftGrid');
        $this->setDefaultSort('gift_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        /*$collection = Mage::getModel('gifts/gifts')->getCollection();
        if($this->getRequest()->getParam('store', 0))$collection->addFilter('store', $this->getRequest()->getParam('store', 0));*/
		
		
		$collection = Mage::getModel('gifts/gifts')->getCollection();
		
		$select = $collection->getSelect();
		$select->joinLeft(array('order' => Mage::getModel('core/resource')->getTableName('belvg_gifts_product')), 'order.gift_id=main_table.gift_id',
		array('product_id' => 'product_id'));
		/*$select->joinLeft(array('product' => Mage::getModel('core/resource')->getTableName('catalog_product_entity_varchar')), 'product.entity_id=main_table.gift_id',
		array('value' => 'value'));*/
		
		
		//echo $collection->getSelect()->__toString();
		//var_dump((string)$collection->getSelect());
		//exit;
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('gift_id', array(
                'header' => Mage::helper('gifts')->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'gift_id',
        ));

        $this->addColumn('title', array(
                'header' => Mage::helper('gifts')->__('Title'),
                'align' => 'left',
                'index' => 'title',
        ));
		
		 $this->addColumn('date', array(
                'header' => Mage::helper('gifts')->__('Date'),
                'align' => 'left',
                'index' => 'date',
				//'format'    => 'Y/m/d', // Format of the column 2014/06/03
				
        ));
		
		$this->addColumn('product_id', array(
			'header'    => Mage::helper('gifts')->__('Promotional Item Name'),
			'index'     =>  'product_id',
			'renderer' 	   => 'Mage_Adminhtml_Block_Catalog_Product_Renderer_Name',
		));
		
		
		
        $this->addColumn('status', array(
                'header' => Mage::helper('gifts')->__('Status'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'status',
                'type' => 'options',
                'options' => array(
                        1 => 'Enabled',
                        2 => 'Disabled',
                ),
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('gifts')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('gifts')->__('XML'));

        return parent::_prepareColumns();
    }
    
     protected function _prepareMassaction()
    {
        $this->setMassactionIdField('gift_id');
        $this->getMassactionBlock()->setFormFieldName('gifts');

        $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('gifts')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('gifts')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('gifts/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
                'label' => Mage::helper('gifts')->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
                'additional' => array(
                        'visibility' => array(
                                'name' => 'status',
                                'type' => 'select',
                                'class' => 'required-entry',
                                'label' => Mage::helper('gifts')->__('Status'),
                                'values' => $statuses
                        )
                )
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $row->getStore()));
    }
 
}

/*****************************************************
		Getting Product Name from ID
/****************************************************/            

class Mage_Adminhtml_Block_Catalog_Product_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				//return "Call function".$row;
				
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				
				$qry = "SELECT * FROM `mg_catalog_product_entity_varchar` where entity_id = '".$value."' order by attribute_id ASC limit 0,1 ";
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				
				/* GET SKU */
				$qry1 = "SELECT sku FROM `mg_catalog_product_entity` where entity_id = '".$value."' ";
				$ans1 = $connection->fetchAll($qry1);
				$sku = $ans1[0]['sku'];
				
				return $ans[0]['value'].'('.$sku.')';
				
				
				
		}
}   

class Mage_Adminhtml_Block_Catalog_Product_Renderer_SKU extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
		public function render(Varien_Object $row)
		{
				//return "Call function".$row;
				
				$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
				$value =  $row->getData($this->getColumn()->getIndex());
				
				$qry = "SELECT * FROM `mg_catalog_product_entity_varchar` where entity_id = '".$value."' order by attribute_id ASC limit 0,1 ";
				$ans = $connection->fetchAll($qry);
				//echo "<pre>";
				//print_r($ans);
				return $ans[0]['value'];
				
				
				
		}
}   


