<?php

class Potato_Compressor_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
    protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null)
    {
        if (Potato_Compressor_Helper_Config::isEnabled()) {
            if ($format == '<link rel="stylesheet" type="text/css" href="%s"%s />' . "\n" &&
                Potato_Compressor_Helper_Config::canCssMerge()) {
                $mergeCallback = array(Mage::getModel('po_compressor/design_package'), 'getMergedCssUrl');
            }
            if ($format == '<script type="text/javascript" src="%s"%s></script>' . "\n" &&
                Potato_Compressor_Helper_Config::canJsMerge()
            ) {
                $mergeCallback = array(Mage::getModel('po_compressor/design_package'), 'getMergedJsUrl');
            }
            Potato_Compressor_Helper_Data::prepareFolder(Mage::helper('po_compressor')->getRootCachePath());
            Potato_Compressor_Helper_Data::prepareFolder(Mage::helper('po_compressor')->getRootCachePath()
                . DS . Mage::app()->getStore()->getId())
            ;
        }
        return parent::_prepareStaticAndSkinElements($format, $staticItems, $skinItems, $mergeCallback);
    }
}