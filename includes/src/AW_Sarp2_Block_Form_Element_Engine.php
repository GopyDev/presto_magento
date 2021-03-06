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
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Sarp2_Block_Form_Element_Engine extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }

    public function getElementHtml()
    {
        $html = $this->_getValueHtml();
        $html .= $this->_getConfigureLinkHtml();
        $html .= $this->_getHiddenInput();
        return $html;
    }

    protected function _getValueHtml()
    {
        return '<div id="' . $this->getLabelId() . '"><b>' . $this->getValue() . '</b></div>';
    }

    protected function _getConfigureLinkHtml()
    {
        return '<a id="configure" style="cursor:pointer;" href="' . $this->getLink() . '">'
            . $this->getConfigureLinkTitle()
            . '</a>'
        ;
    }

    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getId() . '" value="'
            . $this->getValue() . '" />'
        ;
    }


}