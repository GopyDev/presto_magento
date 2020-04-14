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
 * TheFind feed attribute map grid container
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Activo_BulkImages_Block_Adminhtml_Bulkimages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container settings
     *
     */
    public function __construct()
    {
        $this->_blockGroup      = 'bulkimages';
        $this->_controller      = 'adminhtml_bulkimages';
        $this->_headerText      = Mage::helper('bulkimages')->__('Manage Bulk Images');

        parent::__construct();
        
        $this->removeButton('add');

        $this->_addButton('importall',array(
            'label'     => 'Import All Images',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/importall') .'\')',
            'class'     => 'add',
        ));

        $this->_addButton('importnew',array(
            'label'     => 'Import New Images',
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/importnew') .'\')',
            'class'     => 'add',
        ));
        
    }
}
