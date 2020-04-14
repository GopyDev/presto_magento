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

class Activo_BulkImages_Model_System_Config_FilterOptions
{
    public function toOptionArray()
    {
        return array(
            //array('value'=>'', 'label'=>''),
            array('value'=>Activo_BulkImages_Model_Import::FILTER_OPTIONS_ALL, 'label'=>Mage::helper('bulkimages')->__('All Products')),
            array('value'=>Activo_BulkImages_Model_Import::FILTER_OPTIONS_VISIBLE_SEARCH_CATALOG, 'label'=>Mage::helper('bulkimages')->__('Visible in Search and Catalog')),
            array('value'=>Activo_BulkImages_Model_Import::FILTER_OPTIONS_VISIBLE_SEARCH, 'label'=>Mage::helper('bulkimages')->__('Visible in Search')),
            array('value'=>Activo_BulkImages_Model_Import::FILTER_OPTIONS_VISIBLE_CATALOG, 'label'=>Mage::helper('bulkimages')->__('Visible in Catalog')),
        );
    }
}
