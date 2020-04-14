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
 * TheFind feed attribute map grid controller
 *
 * @category    Find
 * @package     Find_Feed
 */
class Activo_BulkImages_Adminhtml_BulkimagesController extends Mage_Adminhtml_Controller_Action
{
    const key = '7r7DrXvPwY98VTPxGQ';
    
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/bulkimages')
            ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('bulkimages')->__('Bulk Image Imports'), Mage::helper('bulkimages')->__('Bulk Image Imports'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('bulkimages/adminhtml_bulkimages'))
            ->renderLayout();
    }

 
    public function importnewAction()
    {
        Mage::getModel('bulkimages/import')->processImport(false);

        $this->indexAction();
    }
    
    public function importallAction()
    {
        Mage::getModel('bulkimages/import')->processImport();
        
        $this->indexAction();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/bulkimages');
    }
}
