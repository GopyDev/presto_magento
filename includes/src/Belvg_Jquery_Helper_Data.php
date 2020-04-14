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

class Belvg_Jquery_Helper_Data extends Mage_Core_Helper_Data 
{
    
    public function getUrl($lib)
    {
        switch ($lib) {
            case 'jquery': 
                $version = (array)Mage::getConfig()->getNode('jquery/versions/' . Mage::getStoreConfig('jquery/settings/jq_version'));
                return $version['lib']; 
            case 'noconflict':
                return 'belvg/jquery/jquery.noconflict.js'; 
            default:
                break;
        }
    }
    
}