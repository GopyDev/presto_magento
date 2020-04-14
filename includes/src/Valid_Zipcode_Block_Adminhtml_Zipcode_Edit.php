<?php

class Valid_Zipcode_Block_Adminhtml_Zipcode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'zipcode';
        $this->_controller = 'adminhtml_zipcode';
        
        $this->_updateButton('save', 'label', Mage::helper('zipcode')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('zipcode')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('zipcode_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'zipcode_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'zipcode_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('zipcode_data') && Mage::registry('zipcode_data')->getId() ) {
            return Mage::helper('zipcode')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('zipcode_data')->getTitle()));
        } else {
            return Mage::helper('zipcode')->__('Add Item');
        }
    }
}