<?php
class Webduo_Simpleupc_Model_System_Config_Source_Daystart{
	
	public function toOptionArray(){
        return array(
            array('value' => 1, 'label'=>Mage::helper('simpleupc')->__('1 AM')),
            array('value' => 2, 'label'=>Mage::helper('simpleupc')->__('2 AM')),
			array('value' => 3, 'label'=>Mage::helper('simpleupc')->__('3 AM')),
			array('value' => 4, 'label'=>Mage::helper('simpleupc')->__('4 AM')),
			array('value' => 5, 'label'=>Mage::helper('simpleupc')->__('5 AM')),
			array('value' => 6, 'label'=>Mage::helper('simpleupc')->__('6 AM')),
			array('value' => 7, 'label'=>Mage::helper('simpleupc')->__('7 AM')),
			array('value' => 8, 'label'=>Mage::helper('simpleupc')->__('8 AM')),
			array('value' => 9, 'label'=>Mage::helper('simpleupc')->__('9 AM')),
			array('value' => 10, 'label'=>Mage::helper('simpleupc')->__('10 AM')),
			array('value' => 11, 'label'=>Mage::helper('simpleupc')->__('11 AM')),
			array('value' => 12, 'label'=>Mage::helper('simpleupc')->__('12 PM')),
			array('value' => 13, 'label'=>Mage::helper('simpleupc')->__('1 PM')),
			array('value' => 14, 'label'=>Mage::helper('simpleupc')->__('2 PM')),
			array('value' => 15, 'label'=>Mage::helper('simpleupc')->__('3 PM')),
			array('value' => 16, 'label'=>Mage::helper('simpleupc')->__('4 PM')),
			array('value' => 17, 'label'=>Mage::helper('simpleupc')->__('5 PM')),
			array('value' => 18, 'label'=>Mage::helper('simpleupc')->__('6 PM')),
			array('value' => 19, 'label'=>Mage::helper('simpleupc')->__('7 PM')),
			array('value' => 20, 'label'=>Mage::helper('simpleupc')->__('8 PM')),
			array('value' => 21, 'label'=>Mage::helper('simpleupc')->__('9 PM')),
			array('value' => 22, 'label'=>Mage::helper('simpleupc')->__('10 PM')),
			array('value' => 23, 'label'=>Mage::helper('simpleupc')->__('11 PM')),
        );
    }
}