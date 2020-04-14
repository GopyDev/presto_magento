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
class AShop_Slidermanager_Block_Adminhtml_Bannermanager_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('bannermanager_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('slidermanager')->__('Slider Manager'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('slidermanager')->__('Slider Information'),
          'title'     => Mage::helper('slidermanager')->__('Slider Information'),
          'content'   => $this->getLayout()->createBlock('slidermanager/adminhtml_bannermanager_edit_tab_form')->toHtml(),
      ));      
      if ($this->getRequest()->getParam('active_tab') == 'custom') {
            $this->addTab('banner_section', array(
                'label' => Mage::helper('slidermanager')->__('Banner of Slider'),
                'title' => Mage::helper('slidermanager')->__('Banner of Slider'),
                'url' => $this->getUrl('*/*/custom', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                'class' => 'ajax',
                'active' => true,
            ));
        } else {
            $this->addTab('banner_section', array(
                'label' => Mage::helper('slidermanager')->__('Banner(s) of Slider'),
                'title' => Mage::helper('slidermanager')->__('Banner(s) of Slider'),
                'url' => $this->getUrl('*/*/custom', array('_current' => true, 'id' => $this->getRequest()->getParam('id'))),
                'class' => 'ajax',
            ));
        }    
      return parent::_beforeToHtml();
  }
}