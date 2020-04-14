<?php

class Webduo_Quicksale_Helper_Data extends Mage_Core_Helper_Abstract
{
	function _getConnection($type = 'core_read'){
		return Mage::getSingleton('core/resource')->getConnection($type);
	}
}