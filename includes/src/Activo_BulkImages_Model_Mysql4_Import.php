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
 
class Activo_BulkImages_Model_Mysql4_Import extends Mage_Core_Model_Mysql4_Abstract
{
    protected $attrCodes = array();
    protected $attrMediaGallery;
    
    protected function _construct()
    {
        $this->_init('bulkimages/import', 'id');
        
        $eavConfig = Mage::getSingleton('eav/config');
        $this->attrCodes[] = $eavConfig->getAttribute('catalog_product', 'image')->getId();
        $this->attrCodes[] = $eavConfig->getAttribute('catalog_product', 'small_image')->getId();
        $this->attrCodes[] = $eavConfig->getAttribute('catalog_product', 'thumbnail')->getId();
        
        $this->attrMediaGallery = $eavConfig->getAttribute('catalog_product', 'media_gallery')->getId();
    }
    
    public function deleteMediaGalleryByProductId($pid)
    {
        $sql = "DELETE mg, mgv ";
        $sql.= "FROM ".$this->getTable(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Media::GALLERY_TABLE)." AS mg ";
        $sql.= "LEFT JOIN ".$this->getTable(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Media::GALLERY_VALUE_TABLE)." AS mgv ON mg.value_id=mgv.value_id ";
        $sql.= "WHERE mg.entity_id=".$pid;
        
        $this->_getWriteAdapter()->query($sql);
    }
    
    public function addImage($image, $product, $position, $excludefirst=1)
    {
        if ($position==1)
        {
            $this->setMainImages($image, $product);
            if ($excludefirst==1)
            {
                $this->setGalleryImage($product->getId(), $image, 1, 1);
            }
            else
            {
                $this->setGalleryImage($product->getId(), $image, 1);
            }
        }
        else
        {
            $this->setGalleryImage($product->getId(), $image, $position);
        }
    }
    
    public function setGalleryImage($entityId, $filename, $position, $disabled='0')
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableMG = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery');
        $tableMGV= Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value');
        
        $write->beginTransaction();
        try {
        
            $sql = "INSERT INTO $tableMG (entity_id, attribute_id, value) VALUES ";
            $sql.= "($entityId,{$this->attrMediaGallery},'$filename') ";
            $write->query($sql);
                        
            $sql = "INSERT INTO $tableMGV (value_id, position, disabled) VALUES ";
            $sql.= "({$write->lastInsertId()},$position,$disabled) ";
            $write->query($sql);

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            $m = "setGalleryImage: Error trying to set gallery image:\n";
            $m.= "Filename/Image = $filename\n";
            $m.= "product Id = ".$entityId."\n";
            $m.= "Error message from exception: ". $e->getMessage() . "\n";
            $m.= $e->getTraceAsString();
            Mage::log($m);
        }
    }
    
    public function setMainImages($image, $product)
    {
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
        $entityTypeId = $product->getEntityTypeId();
        
        // Get list of stores where the product is represented, also include the default store
		$storeIds = $product->getStoreIds();
		$storeId = $product->getStoreId();
		if (!in_array($storeId, $storeIds)) {
			array_unshift($storeIds, $storeId);
		}
        
        $write->beginTransaction();
        try {
			
			
			
			foreach ($storeIds as $storeId) {
	            foreach ($this->attrCodes as $attrCode) 
    	        {
        	        $sql = "INSERT INTO ".$table." (entity_type_id,attribute_id,store_id,entity_id,value) ";
            	    $sql.= "VALUES ($entityTypeId,$attrCode,$storeId,{$product->getId()},'$image') ";
                	$sql.= "ON DUPLICATE KEY UPDATE value='$image'";
	                //Mage::log('Activo BulkImages Insert catalog_product_entity_varchar: ' . $sql);
                
    	            $write->query($sql);
        	    }
			}
			
            //commit transaction or rollback
            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            $m = "setProductAttribute: Error trying to set attributes:\n";
            $m.= "image = $image\n";
            $m.= "product Id = ".$product->getId()."\n";
            $m.= "Error message from exception: ". $e->getMessage() . "\n";
            $m.= $e->getTraceAsString();
            Mage::log($m);
        }
    }
    
    public function addMediaGalleryAttributeToCollection(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $_productCollection) 
    {
        $_mediaGalleryAttributeId = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery')->getAttributeId();
        $_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $tableMG = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery');
        $tableMGV= Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_media_gallery_value');

        
        $_mediaGalleryData = $_read->fetchAll('
            SELECT
                main.entity_id, `main`.`value_id`, `main`.`value` AS `file`,
                `value`.`label`, `value`.`position`, `value`.`disabled`, `default_value`.`label` AS `label_default`,
                `default_value`.`position` AS `position_default`,
                `default_value`.`disabled` AS `disabled_default`
            FROM `'.$tableMG.'` AS `main`
                LEFT JOIN `'.$tableMGV.'` AS `value`
                    ON main.value_id=value.value_id AND value.store_id=' . Mage::app()->getStore()->getId() . '
                LEFT JOIN `'.$tableMGV.'` AS `default_value`
                    ON main.value_id=default_value.value_id AND default_value.store_id=0
            WHERE (
                main.attribute_id = ' . $_read->quote($_mediaGalleryAttributeId) . ') 
                AND (main.entity_id IN (' . $_read->quote($_productCollection->getAllIds()) . '))
            ORDER BY IF(value.position IS NULL, default_value.position, value.position) ASC    
        ');
        
        
        $_mediaGalleryByProductId = array();
        foreach ($_mediaGalleryData as $_galleryImage) {
            $k = $_galleryImage['entity_id'];
            unset($_galleryImage['entity_id']);
            if (!isset($_mediaGalleryByProductId[$k])) {
                $_mediaGalleryByProductId[$k] = array();
            }
            $_mediaGalleryByProductId[$k][] = $_galleryImage;
        }
        unset($_mediaGalleryData);
        foreach ($_productCollection as &$_product) {
            $_productId = $_product->getData('entity_id');
            if (isset($_mediaGalleryByProductId[$_productId])) {
                $_product->setData('media_gallery', array('images' => $_mediaGalleryByProductId[$_productId]));
            }
        }
        unset($_mediaGalleryByProductId);
        
        return $_productCollection;
    }

}
?>
