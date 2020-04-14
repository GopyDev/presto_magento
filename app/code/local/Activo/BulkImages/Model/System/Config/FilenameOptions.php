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
 
class Activo_BulkImages_Model_System_Config_FilenameOptions
{
    public function toOptionArray()
    {
        return array(
            //array('value'=>'', 'label'=>''),
            array('value'=>Activo_BulkImages_Model_Import::FILENAME_OPTIONS_NOCHANGE, 
                    'label'=>Mage::helper('bulkimages')->__('No Change')),
            array('value'=>Activo_BulkImages_Model_Import::FILENAME_OPTIONS_NAME, 
                    'label'=>Mage::helper('bulkimages')->__('[PRODUCT NAME]')),
            array('value'=>Activo_BulkImages_Model_Import::FILENAME_OPTIONS_NAME_SKU, 
                    'label'=>Mage::helper('bulkimages')->__('[PRODUCT NAME]-[SKU]')),
            array('value'=>Activo_BulkImages_Model_Import::FILENAME_OPTIONS_SKU_NAME, 
                    'label'=>Mage::helper('bulkimages')->__('[SKU]-[PRODUCT NAME]')),
        );
    }
}
