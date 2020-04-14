<?php
require('../app/Mage.php');

Mage::app('default'); 
ini_set('display_errors', 1);
try
{
  $catalogPriceRule = Mage::getModel('catalogrule/rule');
  $catalogPriceRule->applyAll();
  Mage::app()->removeCache('catalog_rules_dirty');
  echo Mage::helper('catalogrule')->__('The rules have been applied.');
} 
catch (Exception $e) 
{
  die($e);
}

/*
$rules =  Mage::getResourceModel('catalogrule/rule_collection')->load();


if(empty($rules))
{
	echo "null value";
}
else
{
	echo "not null";
}
echo "<pre>";
print_r($rules->getData());
echo "</pre>";

 if($rules->getData())
 {
	$rule_ids = array(); 
	$rule_status = array();
	foreach($rules as $rule) 
	{
        $rule_ids[] = $rule->rule_id;
		$rule_status[] = $rule->is_active;
    }
 
	 echo "<pre>";
	 print_r($rule_ids);
	 echo "</pre>";
	 
	 echo "<pre>";
	 print_r($rule_status);
	 echo "</pre>";
	 
	 if(empty($rule_ids))
	 {
		echo "null value";
	 }
 }
 else
 {
	echo "condition false";
 }
 */
?>