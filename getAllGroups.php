<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);
require_once 'app/Mage.php';
ini_set('display_errors', 1);
umask(0);
Mage::app();

$group = Mage::getModel('customer/group')->getCollection();
$groupArray = array();
$search = !empty($_POST['value']) ? $_POST['value'] : '';
$html = '<ul>';
foreach ($group as $eachGroup) {
			$text = $eachGroup->getCustomerGroupCode();
	        $pos = stripos($text, $search);
            $length = strlen($search);
            if ($pos !== false) {
              $html .= '<li group_id='. $eachGroup->getCustomerGroupId() .'>' . $text . '</li>';	
			}
  /*$groupData = array(
   'customer_group_id' => $eachGroup->getCustomerGroupId(),
   'customer_group_code' => $eachGroup->getCustomerGroupCode(),
   'tax_class_id' => $eachGroup->getTaxClassId() // we dont required this
  );
  if (!empty($groupData)) {
   array_push($groupArray, $groupData);
  }
  */
 }
 $html .= '</ul>';
 echo $html;
//Mage::log('Your Data :' . print_r($groupArray, true),null,'groups.log');
?>