<?php

class Valid_Zipcode_Block_Adminhtml_Zipcode_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('zipcode_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('zipcode')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('zipcode')->__('Item Information'),
          'title'     => Mage::helper('zipcode')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('zipcode/adminhtml_zipcode_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}