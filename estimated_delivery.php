<?php
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
	$db_write1 = Mage::getSingleton('core/resource')->getConnection('core_write');
	$today = date("d_m_Y"); 
	//$today = '19_04_2016';
	$qry = "SELECT * FROM mg_shipping_delivery_window WHERE window LIKE '%".$today."%' and estt!='' ";
	$result = $db_write1->fetchAll($qry);

	//echo "<pre>";
	//print_r($result);
	$count = 1;
	foreach($result as $value){  
			if($value['estt'] == ''){
				list($time,$date) = explode("|",$value['window']);
				$originalDate = str_replace("_","-",date($date));
				$newDate = date("jS F Y", strtotime($originalDate));
				$newtime = str_replace("pm_","pm - ",$time);
				$newtime = str_replace("_",":",$newtime);
			}else{
				$newtime = $value['estt'];
				$ETA_Time = date('h:i', strtotime($newtime.' + 30 minutes') );
			}
			
			
			
				list($time,$date) = explode("|",$value['window']);
				$originalDate = str_replace("_","-",date($date));
				$newDate = date("jS F Y", strtotime($originalDate));
				$newtime = str_replace("pm_","pm - ",$time);
				$time2 = str_replace("_",":",$newtime);
				$appo=explode("-",$time2);
				
			    $time = date("H:i", strtotime($value['estt'])); 
				
				$appo[1] = str_replace('0','', $appo[1]);
				$str = str_replace(':00',':', $value['estt']);
				$str = preg_replace('/\s+/', '', $str);
				$appo[1] = preg_replace('/\s+/', '',  $appo[1]);
				
				
				
							  $tod = explode(":",$time);
							  $minutes = $tod[1];
							  if($minutes <= 30)
							  {
							    $minutes = 30; 
							  }
							  else if($minutes > 30)
							  {
							    $minutes = 00; 
								$tod[0] = $tod[0] + 1 ;
							  }
							  else if($minutes==00)
							  {
							    $minutes = 00;
							  }
							  
							  $time = $tod[0].":".$minutes;
							  $besttime = date('g:i a', strtotime($time));
			
			                   $time = strtotime($time);
							 
							 if($appo[1]==$str)
							 {
							 // echo "hello";
                              $startTime = date("H:i", strtotime('-60 minutes', $time));
							  $besttime = date("H:i", strtotime('-30 minutes', $time));
							   $besttime = date('g:i a', strtotime($besttime));
							 }
							 else
							 {
							   $startTime = date("H:i", strtotime('-30 minutes', $time));
							 }
							 
							  $realtime = date('g:i a', strtotime($startTime)); 
							  
			
			$customer = Mage::getModel('customer/customer')->load($value['customer_id']);
			$orderNumber =  $value['order_number'];
		/***********************	
			GENERATE EMAIL
		/***********************/ 
		
		$message = '<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td align="center" valign="top" style="padding:20px 0 20px 0;">
        <table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="650" style="border:1px solid #E0E0E0;">
           
            <tr>
                <td valign="top"><a href="http://www.prestofreshgrocery.com/"><img src="http://www.prestofreshgrocery.com/skin/frontend/default/theme509/images/logo_jpg.png" alt="PrestoFresh Grocerry" style="margin-bottom:10px;" border="0"/></a></td>
            </tr>
           
            <tr>
                <td valign="top">
                    <h1 style="font-size:22px; font-weight:normal; line-height:22px; margin:0 0 11px 0;">Hello, '.$customer->getFirstname().'&nbsp; '.$customer->getLastname().'</h1>
					<p>&nbsp;</p>
					<p style="font-size:14px; line-height:16px; margin:0;">
                        Thank you for your order!<br/><br/>
                    </p>
					
					<p style="font-size:14px; line-height:22px; margin:0;">
						The delivery time for your order #'.$orderNumber.' has been set. We currently estimate that we will deliver to you between '.$realtime.' - '.$besttime.'
						<br/>
						Please note that this 30 minute ETA is provided as a courtesy only. We may run early or late, so please plan to be available for the entirety of your selected delivery window. The $10 no-show fee still applies. Thank you for understanding!
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
</div>';
		//exit;
		$qrySub = "SELECT subscribed FROM mg_estimated_delivery_subscription WHERE customer_id='".$value['customer_id']."' AND subscribed = 1 ";
		$resultSub = $db_write1->fetchAll($qrySub);
		/*echo "<pre>";
		print_r($resultSub);
		exit; */
		
		 // $mail = 'adhiyahiren108@gmail.com';
		 $mail = $customer->getEmail();
		if(!empty($resultSub)){
		
				$sub = 'Estimated Delivery Time Notification - Order - #'.$orderNumber;
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'Bcc: adhiyahiren108@gmail.com' . "\r\n";
				//if(mail($email,$sub,$message,$headers)){
				 if(mail($mail,$sub,$message,$headers)){
					echo $sucess_msg = "Email has been sent to the customer. ".$customer->getEmail().'&nbsp;Order #'.$orderNumber;
					echo "<br/>";
					$rs = mysql_query($qry);
				}else{
					echo $sucess_msg = "Problem in sending a mail.";
					
				} 
				 if ($count % 5 == 0) {
					  sleep(5); 
				}
					$count++;
				/**********************
					END EMAIL
				/**********************/
		}  // Close checking subscribe 
	}
?>