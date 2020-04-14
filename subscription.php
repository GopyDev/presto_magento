<?php
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);
echo '*************';

require 'app/Mage.php';
ini_set('display_errors', 1);
echo '*************';
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
$productTable = 'mg_customer_subsciption';
 $query = "SELECT * FROM " . $productTable ."";
$result = $connection->query($query);
$row = $result->fetchAll();
$i = 0;
$ids = '';
foreach($row as $data){
	$i++;
$startdate 	= $data['start_date']; echo '<br>';
$enddate 	= $data['expiry_date'];
 $currentdate = date('m/d/y');
	if (strtotime($currentdate) >= strtotime($enddate)) {
//echo 		$has_expired = "yes" . $data['id'];
		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$query2 = "update  " . $productTable ." set active='No' where id=".$data['id']."";
		$writeConnection->query($query2);
		 $ids[] = $data['id'];
		Mage::log($query2,null,'query2.log');

	} else {
	echo 	$has_expired = "no";
	}
}

//Mage::log(date('d-m-y h i s'),null,'subscription_log.log');

$customer_email = 'monika@xoomsolutions.com';

	  $to  = $customer_email; 
	 
		// subject
		$subject = 'Presto fresh grocery Site';

		// message
		$message = '
		<html>
		<head>
		  <title>Presto fresh grocery.com </title>
		</head>
		<body>
		  <p>Hello,</p>
		  <p>Subscription updated</p>
		  <p>Thank you,</p>
		  
		</body>
		</html>
		';
		
// To send HTML mail, the Content-type header must be set
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
		$headers .= 'From: Presto fresh grocery<no-reply@presto.com>' . "\r\n"; 
		"Reply-To: no-reply@presto.com" . "\r\n" .
		"X-Mailer: PHP/" . phpversion();
		
		// Mail it
		mail($to, $subject, $message, $headers);
?>