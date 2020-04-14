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
class AShop_Slidermanager_Block_Adminhtml_Bannermanager_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('bannermanager_form', array('legend'=>Mage::helper('slidermanager')->__('Slider information')));
     
      $fieldset->addField('slidername', 'text', array(
          'label'     => Mage::helper('slidermanager')->__('Slider Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'slidername',
      ));
      
      $fieldset->addField('slider_key', 'text', array(
          'label'     => Mage::helper('slidermanager')->__('Identifier'),
          'required'  => true,
          'name'      => 'slider_key',
          'note'      => 'Identifier should be unique.',
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
     
      
      if ( Mage::getSingleton('adminhtml/session')->getBannermanagerData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBannermanagerData());
          Mage::getSingleton('adminhtml/session')->setBannermanagerData(null);
      } elseif ( Mage::registry('bannermanager_data') ) {
          $form->setValues(Mage::registry('bannermanager_data')->getData());
      }
      return parent::_prepareForm();
  }
}