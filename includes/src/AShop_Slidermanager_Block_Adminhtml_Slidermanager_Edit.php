<?php
/**
 * AShop Slider
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the AccessshopThemes.com license that is
 * available through the world-wide-web at this URL:
 * http://www.accessshopThemes.com
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @copyright 	Copyright (c) 2015 Accessshop (http://www.accessshopThemes.com)
 * @license     http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Accessshop Block
 * 
 * @category 	AShop Slider
 * @package 	AShop_Slidermanager
 * @author  	AccessShop Developer
 */
class AShop_Slidermanager_Block_Adminhtml_Slidermanager_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'slidermanager';
        $this->_controller = 'adminhtml_slidermanager';
        
        $this->_updateButton('save', 'label', Mage::helper('slidermanager')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('slidermanager')->__('Delete Banner'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('slidermanager_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'slidermanager_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'slidermanager_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('slidermanager_data') && Mage::registry('slidermanager_data')->getId() ) {
            return Mage::helper('slidermanager')->__("Edit Banner '%s'", ucwords($this->htmlEscape(Mage::registry('slidermanager_data')->getTitle())));
        } else {
            return Mage::helper('slidermanager')->__('Add Banner');
        }
    }
}