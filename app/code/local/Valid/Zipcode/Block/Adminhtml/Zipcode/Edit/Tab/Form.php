<?php

class Valid_Zipcode_Block_Adminhtml_Zipcode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('zipcode_form', array('legend'=>Mage::helper('zipcode')->__('Item information')));
     
      $fieldset->addField('ID', 'text', array(
          'label'     => Mage::helper('zipcode')->__('ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'ID',
      ));

      $fieldset->addField('postcode', 'text', array(
          'label'     => Mage::helper('zipcode')->__('postcode'),
          'required'  => false,
          'name'      => 'postcode',
	  ));
		
      /*$fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('zipcode')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('zipcode')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('zipcode')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('zipcode')->__('Content'),
          'title'     => Mage::helper('zipcode')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));*/
     
      if ( Mage::getSingleton('adminhtml/session')->getZipcodeData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getZipcodeData());
          Mage::getSingleton('adminhtml/session')->setZipcodeData(null);
      } elseif ( Mage::registry('zipcode_data') ) {
          $form->setValues(Mage::registry('zipcode_data')->getData());
      }
      return parent::_prepareForm();
  }
}