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
	
	
	//echo "Order Number is ".$orderNumber;
	//exit;
	$orderId = $_REQUEST['order_id'];
	$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
	$getCustomerId = $order->getCustomerId();
	$customer = Mage::getModel('customer/customer')->load($getCustomerId);
	$email = $customer['email'];
	$fname = $customer['firstname'];
	$lname = $customer['lastname'];
	$name = $fname.'&nbsp;'.$lname;
	//echo "name is ".$name;
	//exit;
	/*
	//$email = $_REQUEST['email'];
	$fname = $_REQUEST['fname'];
	$lname = $_REQUEST['lname'];
	$name = $fname.'&nbsp;'.$lname;*/
	//$qry = "SELECT order_number FROM mg_order_picker WHERE order_id ='".$order_id."'";
	//$Orderid  =  $connection->fetchAll($qry);
	//$rs = mysql_query($qry);
	//$results = mysql_fetch_array($rs);
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
                    <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Hello, '.$name.'</h1>
					<p>&nbsp;</p>
					<p style="font-size:14px; line-height:16px; margin:0;">
                        <b>Thank you for your order!</b>
                    </p>
					<p>&nbsp;</p>
					<p style="font-size:14px; line-height:16px; margin:0;">
						Delivery delay notification for Order #'.$orderId.'
					</p>
					<p>&nbsp;</p>
					<p style="font-size:14px; line-height:22px; margin:0;">
						Our driver is experiencing delays on the route. He/She will call you with a revised ETA when possible if your delivery will be outside of your 2 hour delivery window. We apologize for any inconvenience!
                    </p>
					
                 </td>
			</tr>
			<tr>
				<td>
				 
                    <p style="font-size:12px; font-weight:normal; line-height:22px;">
                        If you have any questions about your order, please email us at <a href="mailto:support@prestofreshgrocery.com" style="color:#1E7EC8;">support@prestofreshgrocery.com</a> and we will respond as soon as possible.
                    </p>
                 </td>
            </tr>
         </table>
    </td>
</tr>
</table>
</div>
</body>';
			$sub = 'Delivery delay order notification - #'.$orderId;
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			if(mail($email,$sub,$message,$headers)){
				echo $sucess_msg = "Delivery delay notification has been sent to the customer.";
				$qry = "Update mg_shipping_delivery_window SET delay_delivery = 1 WHERE order_number = '".$orderId."' ";
				$rs = mysql_query($qry);
			}else{
				echo $sucess_msg = "Problem in sending a mail.";
				
			}
	
	
	