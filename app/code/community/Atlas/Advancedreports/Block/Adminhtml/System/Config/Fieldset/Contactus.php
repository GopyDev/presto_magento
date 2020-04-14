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
class Atlas_Advancedreports_Block_Adminhtml_System_Config_Fieldset_Contactus extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    protected $_template = 'atlas/advancedreports/system/config/fieldset/contactus.phtml';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

}

?>