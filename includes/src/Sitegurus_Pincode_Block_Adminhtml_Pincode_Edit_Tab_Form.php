<?php

class Sitegurus_Pincode_Block_Adminhtml_Pincode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('pincode_form', array('legend'=>Mage::helper('pincode')->__('Pincode information')));
     
      $fieldset->addField('area_name', 'text', array(
          'label'     => Mage::helper('pincode')->__('Area Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'area_name',
      ));

      
	
     $fieldset->addField('pin_code', 'text', array(
           'label'     => Mage::helper('pincode')->__('PinCode(Add comma saperated pin codes)'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'pin_code',
      ));	
     
     
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('pincode')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('pincode')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('pincode')->__('Disabled'),
              ),
          ),
      ));
     
     
     
      if ( Mage::getSingleton('adminhtml/session')->getPincodeData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getPincodeData());
          Mage::getSingleton('adminhtml/session')->setPincodeData(null);
      } elseif ( Mage::registry('pincode_data') ) {
          $form->setValues(Mage::registry('pincode_data')->getData());
      }
      return parent::_prepareForm();
  }
}
