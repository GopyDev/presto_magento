<?php

class Potato_Compressor_Model_Observer
{
    public function refreshCache()
    {
        Mage::helper('po_compressor')->clearCache();
        return $this;
    }

    public function deferJs(Varien_Event_Observer $observer)
    {
        if (Potato_Compressor_Helper_Config::isEnabled() &&
            Potato_Compressor_Helper_Config::canJsMerge() &&
            Potato_Compressor_Helper_Config::isCanJsDefer() &&
            !Mage::app()->getRequest()->getParam('isAjax', false)
        ) {
            $response = $observer->getEvent()->getFront()->getResponse();
            Mage::getSingleton('po_compressor/compressor_js')->makeDefer($response);
        }
        return $this;
    }

    public function minifyHTML(Varien_Event_Observer $observer)
    {
        $response = $observer->getEvent()->getFront()->getResponse();
        $response->setBody(Potato_Compressor_Helper_Data::minifyContent($response->getBody()));
        return $this;
    }
}