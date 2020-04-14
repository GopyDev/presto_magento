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
class AShop_Slidermanager_Block_Adminhtml_Slidermanager_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('slidermanager_form', array('legend'=>Mage::helper('slidermanager')->__('Banner information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('slidermanager')->__('Banner Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
          'note'      => 'Best View With 15 characters.'
      ));
      
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('slidermanager')->__('Banner Subtitle'),
          'title'     => Mage::helper('slidermanager')->__('Banner Subtitle'),
          'style'     => 'width:450px; height:75px;',
          'wysiwyg'   => false,
          'required'  => true,
          'note'      => 'Best View With 160 characters.'
      ));
     
      
      $fieldset->addField('background_image', 'file', array(
          'label'     => Mage::helper('slidermanager')->__('Background Image'),
          'required'  => false,
          'name'      => 'background_image',
	  ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('slidermanager')->__('Banner Main Image'),
          'required'  => false,
          'name'      => 'filename',
	  ));
      
      $fieldset->addField('link_button', 'select', array(
          'label'     => Mage::helper('slidermanager')->__('Show Link Button'),
          'name'      => 'link_button',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('slidermanager')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('slidermanager')->__('Disabled'),
              ),
          ),
      ));
      
      $fieldset->addField('button_title', 'text', array(
          'label'     => Mage::helper('slidermanager')->__('Button Title'),
          'required'  => false,
          'name'      => 'button_title',
      ));
      
      $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('slidermanager')->__('Button Link'),
          'required'  => false,
          'name'      => 'link',
          'class'     => 'validate-url',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('slidermanager')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('slidermanager')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('slidermanager')->__('Disabled'),
              ),
          ),
      ));
      
      if ($this->getRequest()->getParam('id') || count(Mage::helper('slidermanager')->getOptionSliderId()) > 1){
			$fieldset->addField('bannermanager_id', 'select', array(
				'label' => Mage::helper('slidermanager')->__('Select Slider'),
				'name' => 'bannermanager_id',
				'values' => Mage::helper('slidermanager')->getOptionSliderId(),
			));
		}
        
        $fieldset->addField('target', 'select', array(
				'label' => Mage::helper('slidermanager')->__('Select Target'),
				'name' => 'target',
				'values' => Mage::getModel('slidermanager/target')->getTargetOption(),
			));   
     
      
      if ( Mage::getSingleton('adminhtml/session')->getSlidermanagerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSlidermanagerData());
          Mage::getSingleton('adminhtml/session')->setSlidermanagerData(null);
      } elseif ( Mage::registry('slidermanager_data') ) {
          $form->setValues(Mage::registry('slidermanager_data')->getData());
      }
      return parent::_prepareForm();
  }
}