<?php
/**
 * Activo Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Activo Commercial License
 * that is available through the world-wide-web at this URL:
 * http://extensions.activo.com/license_professional
 *
 * @copyright   Copyright (c) 2013 Activo Extensions (http://extensions.activo.com)
 * @license     Commercial
 */


/**
 * TheFind feed attribute map Grid
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Activo_BulkImages_Block_Adminhtml_Bulkimages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('bulkimages');
    }

    /**
     * Prepare codes collection
     *
     * @return Activo_BulkImages_Block_Adminhtml_BulkImages_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('bulkimages/import_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'        =>  Mage::helper('bulkimages')->__('ID'),
            'align'         =>  'right',
            'width'         =>  '50px',
            'filter_index'  =>  'id',
            'index'         =>  'id',
        ));

        $this->addColumn('num_images', array(
            'header'        =>  Mage::helper('bulkimages')->__('Total Image Files'),
            'align'         =>  'right',
            'width'         =>  '70px',
            'filter_index'  =>  'num_images',
            'index'         =>  'num_images',
        ));

        $this->addColumn('num_images_unmatched', array(
            'header'        =>  Mage::helper('bulkimages')->__('Unmatched Image Files'),
            'align'         =>  'right',
            'width'         =>  '70px',
            'filter_index'  =>  'num_images_unmatched',
            'index'         =>  'num_images_unmatched',
        ));

        $this->addColumn('num_skus', array(
            'header'        =>  Mage::helper('bulkimages')->__('Total Visible Skus'),
            'align'         =>  'right',
            'width'         =>  '70px',
            'filter_index'  =>  'num_skus',
            'index'         =>  'num_skus',
        ));

        $this->addColumn('num_skus_unmatched', array(
            'header'        =>  Mage::helper('bulkimages')->__('Unmatched Visible Skus'),
            'align'         =>  'right',
            'width'         =>  '70px',
            'filter_index'  =>  'num_skus_unmatched',
            'index'         =>  'num_skus_unmatched',
        ));

        $this->addColumn('num_matches', array(
            'header'        =>  Mage::helper('bulkimages')->__('Skus Matches'),
            'align'         =>  'right',
            'width'         =>  '70px',
            'filter_index'  =>  'num_matches',
            'index'         =>  'num_matches',
        ));
        
        $this->addColumn('created_at', array(
            'header'        =>  Mage::helper('bulkimages')->__('Created At'),
            'width'         =>  '160px',
            'index'         =>  'created_at',
            'type'          =>  'datetime',
        ));

        $this->addColumn('ok', array(
            'header'    => Mage::helper('bulkimages')->__('Ok?'),
            'width'         =>  '40px',
            'index'     => 'successfull',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('bulkimages')->__('No'),
                1 => Mage::helper('bulkimages')->__('Yes')
            ),
        ));
        
        return parent::_prepareColumns();
    }
}
