<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
$products = Mage::getModel('catalog/product')
	->getCollection()
	->addAttributeToFilter('status', array('eq' => 1));
//echo count($products);
foreach($products as $sku){
$prod = Mage::getModel('catalog/product')->load($sku->getId());
echo  $prod->getPrice();
echo '<br>';	
}
?>