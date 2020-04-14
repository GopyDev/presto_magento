<?php
/**
 * Abandoned Carts Alerts Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Cartalert
 * @version      3.3.4
 * @license:     W22sKZAc65sLiShWmPFxsroCMZvSx8DyIvGLqZPs4w
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Cartalert_Block_Adminhtml_Stoplist extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    parent::__construct();
    $this->_controller = 'adminhtml_stoplist';
    $this->_blockGroup = 'adjcartalert';
    $this->_headerText = Mage::helper('adjcartalert')->__('Unsubscribed customers list');
    $this->_removeButton('add'); 
  }
}