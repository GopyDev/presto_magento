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
class Atlas_Advancedreports_Block_Report_Revenue_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Initialize container block settings
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'advancedreports';
        $this->_controller = 'report_revenue_product';
        $this->_headerText = Mage::helper('advancedreports')->__('Product Report');
        parent::__construct();
        $this->_removeButton('add');
    }

}
