<?php
/*echo "<pre>";
	print_r($_REQUEST);
	exit;*/
include('connection.php');
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';
ini_set('display_errors', 1);

umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
$coreSession = Mage::getSingleton('core/session', array('name' => 'frontend'));
session_start();	
$customerId = '1355';
$qry1 = "SELECT subscribe FROM mg_survey_subscribe WHERE customer_id ='".$customerId."'";
$rs1 = mysql_query($qry1);
$results1 = mysql_fetch_array($rs1);


if($results1['subscribe'] == '1'){
	$email = $_REQUEST['email'];
	$orderNumber = $_REQUEST['order_id'];
	//$orderNumber = '100007758';
	$fname = $_REQUEST['fname'];
	$lname = $_REQUEST['lname'];
	$name = $fname.'&nbsp;'.$lname;
	// TESTING NAME
	//$name = 'Testing Tester';
	//$driverId = '46';
	$qry = "SELECT delivery_date FROM mg_deliverydate_order WHERE order_id ='".$orderNumber."'";
	$rs = mysql_query($qry);
	$results = mysql_fetch_array($rs);
	$Orderdate = date('F j, Y',strtotime($results['delivery_date']));
	$message = '
<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
	<td align="center" valign="top" style="padding:20px 0 20px 0;">
		<table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
		
			<tr>
				<td valign="top"><a href="http://www.prestofreshgrocery.com/"><img src="http://www.prestofreshgrocery.com/skin/frontend/default/theme509/images/logo_jpg.png" alt="PrestoFresh Grocerry" style="margin-bottom:10px;" border="0"/></a></td>
			</tr>
		
			<tr>
				<td valign="top">
					<h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Dear, '.$name.'</h1>
					<br/>
					<p style="font-size:14px; line-height:16px; margin:0;">
						We hope you enjoyed your delivery on '.$Orderdate.'
					</p>
					<br/>
					<p style="font-size:14px; line-height:16px; margin:0;">
						We value your opinion and would like to know how we did. So we’re offering you 50 PrestoRewards points to complete a short 3 question survey!
					</p>
					<p style="font-size:14px; line-height:22px; margin:0;">
						<a href="http://www.prestofreshgrocery.com/surveymanager/index/index/survey_id/1?order='.$orderNumber.'&customerId='.$customerId.'&driverId='.$driverId.'" >Click here to access the survey</a>
					</p>
					<p>Thank you for your business!</p>
					<p>PrestoFresh Grocery</p>
				</td>
			</tr>
			<tr>
				<td>
				
					<p style="font-size:12px; font-weight:normal; line-height:22px;">
						You are receiving this email because you recently received a
delivery from PrestoFresh Grocery. If you no longer wish to receive survey
related emails, please click here to <a href="http://www.prestofreshgrocery.com/unsubscribe-sruvey.php?customerId='.$customerId.'">unsubscribe</a>.
					</p>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</div>
</body>';
	$sub = 'Accepted order notification - #'.$orderNumber;
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	//if(mail($email,$sub,$message,$headers)){
	if(mail('naeem@xoomsolutions.com',$sub,$message,$headers)){
		echo $sucess_msg = "Accepted order notification has been sent to the customer.";
		//$qry = "Update mg_shipping_delivery_window SET delay_delivery = 1 WHERE order_number = '".$orderNumber."' ";
		$rs = mysql_query($qry);
	}else{
		echo $sucess_msg = "Problem in sending a mail.";
		
	}
	
	
}else{
	echo "Not subscriber survey email";	
}// Close main If condition	