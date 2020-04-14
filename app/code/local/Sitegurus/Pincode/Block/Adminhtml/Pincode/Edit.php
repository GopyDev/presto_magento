<?php

class Sitegurus_Pincode_Block_Adminhtml_Pincode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'pincode';
        $this->_controller = 'adminhtml_pincode';
        
        $this->_updateButton('save', 'label', Mage::helper('pincode')->__('Save Pincode'));
        $this->_updateButton('delete', 'label', Mage::helper('pincode')->__('Delete Pincode'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('pincode_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'pincode_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'pincode_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('pincode_data') && Mage::registry('pincode_data')->getId() ) {
            return Mage::helper('pincode')->__("Edit Pincode '%s'", $this->htmlEscape(Mage::registry('pincode_data')->getTitle()));
        } else {
            return Mage::helper('pincode')->__('Add Pincode');
        }
    }
}