<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/**********************************************
 *        MAGENTO EDITION USAGE NOTICE        *
 **********************************************/
/* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/**********************************************
 *        DISCLAIMER                          *
 **********************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 **********************************************
 * @category   Belvg
 * @package    Belvg_jQuery
 * @version    1.9.1
 * @copyright  Copyright (c) 2010 - 2012 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Jquery_Model_Observer
{
    
    public function addLibz()
    {
        $jquery_head = Mage::app()->getLayout()->getBlock('jquery_head');
        $head   = Mage::app()->getLayout()->getBlock('head');
        if ($jquery_head instanceof Belvg_Jquery_Block_Head 
        && is_object($head)
        && Mage::getStoreConfigFlag('jquery/settings/enabled')) {
            $helper = Mage::helper('jquery/data');
            $head   = Mage::app()->getLayout()->getBlock('head');
            $data   = $head->getData();
            $tmp    = $data['items'];
            $data['items'] = '';
            $head->setData($data);
            
            $libz   = $jquery_head->getLibz();
            $urlz   = $jquery_head->getJsUrlz();
            //print_r($urlz); die;
            if (!empty($libz)) {
                foreach ($libz as $lib) {
                    $head->addJs($helper->getUrl($lib));
                }
            }
            
            if (!empty($urlz)) {
                foreach ($urlz as $url) {
                    $head->addJs($url);
                }
            }
            
            $data = $head->getData();
            if (!$data['items']) {
                $data['items'] = array();
            }
            
            $data['items'] = array_merge((array)$data['items'], (array)$tmp);
            $head->setData($data);
        }
    }
    
}