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
 
class Activo_BulkImages_Model_Mysql4_Import_Collection extends Varien_Data_Collection_Db
{
    protected $_synchTable;
    
    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');
        parent::__construct($resources->getConnection('bulkimages_read'));
        $this->_synchTable = $resources->getTableName('bulkimages/import');
        
        $this->_select->from($this->_synchTable)->order('id DESC');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('bulkimages/import'));
    }
}
?>
