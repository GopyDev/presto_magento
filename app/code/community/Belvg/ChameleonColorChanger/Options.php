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
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_ChameleonColorChanger
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_ChameleonColorChanger_Options
{
	public function toOptionArray()
    {
        return array(
            array('value' => 'black', 'label'=>Mage::helper('chameleoncolorchanger')->__('Charcoal Black')),
		    array('value' => 'white', 'label'=>Mage::helper('chameleoncolorchanger')->__('Ivory White')),
		    array('value' => 'red',   'label'=>Mage::helper('chameleoncolorchanger')->__('Sunset Red')),
			array('value' => 'green', 'label'=>Mage::helper('chameleoncolorchanger')->__('Emerald Green')),
			array('value' => 'darkblue', 'label'=>Mage::helper('chameleoncolorchanger')->__('Midnight Blue')),
			array('value' => 'blue',  'label'=>Mage::helper('chameleoncolorchanger')->__('Sky Blue')),
			array('value' => 'pink',  'label'=>Mage::helper('chameleoncolorchanger')->__('Magenta Pink')),
			array('value' => 'brown',  'label'=>Mage::helper('chameleoncolorchanger')->__('Chocolate Brown'))
        );
    }
}