<?php

/**
 * Atlas Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Atlas Commercial License
 * that is available through the world-wide-web at this URL:
 *
 * @copyright   Copyright (c) 2015 Atlas Extensions
 * @license     Commercial
 */
class Atlas_Advancedreports_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getNewstoreids()
    {

        $object = new Atlas_Advancedreports_Block_Report_Revenue_Orders;

        $storeidarr = $object->Storeparam();


        return $storeidarr;
    }

}
