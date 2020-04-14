<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Raf
 * @version    2.1.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

umask(0);

try {
     
    if (!$raf10 = Mage::getModel('referafriend/invite')) {
       throw new Exception('RaF 1.0 referafriend/invite not found');
    }
    if (!$raf20 = Mage::getModel('awraf/referral')) {
       throw new Exception('RaF 2.0 awraf/referral not found');
    }
  
    $numPages = $totalPages = ceil(Mage::getModel('referafriend/invite')->getCollection()->getSize() / 20000);

    $websites = Mage::app()->getWebsite()->getCollection();
    foreach ($websites as $website) {
        if (!$website->getId()) {
            continue;
        }  
        
        $store = $website->getDefaultStore()->getId();
       
        $curPage = 1;
        $numPages = $totalPages;
        while ($numPages) {
            $invites = Mage::getModel('referafriend/invite')->getCollection()
                    ->setPageSize(20000)
                    ->setCurPage($curPage);
            foreach ($invites as $invite) {
                if (!$invite->getData('referral_id') || !$invite->getData('referrer_id')) {
                    continue;
                }
                Mage::getModel('awraf/referral')
                        ->setData('customer_id', $invite->getData('referral_id'))
                        ->setData('referrer_id', $invite->getData('referrer_id'))
                        ->setData('created_at', $invite->getData('created_at'))
                        ->setData('email', $invite->getData('referral_email'))
                        ->setData('updated_at', gmdate('Y-m-d H:i:s'))
                        ->setData('website_id', $website->getId())
                        ->setData('store_id', $store)
                        ->setData('status', 1)
                        ->save();
            }
            $numPages--; 
            $curPage++;
        }        
    }
} catch (Exception $e) {
    Mage::printException($e);
}
