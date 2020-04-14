<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

 require 'vendor/autoload.php';

				define("AUTHORIZENET_API_LOGIN_ID", "85z7aV7XB");
				define("AUTHORIZENET_TRANSACTION_KEY", "3bWR6zZ8hx9h34zs");
				define("AUTHORIZENET_SANDBOX", false);

		  $sel_data="SELECT *
FROM  `mg_save_creditcarddetail`
WHERE  `cid` =  ''";
		  $rs_data=mysql_query($sel_data);
		  while ($row = mysql_fetch_array($rs_data)) {

		      $sel_customer_email="select email from mg_customer_entity where entity_id='".$row["customer_id"]."'";
			  $rs_customer_email=mysql_query($sel_customer_email);
			  $customer_email=mysql_fetch_array($rs_customer_email);

			  if($row["cc_number"]!="" && $row["cc_exp_year"]!="" && $row["cc_exp_month"]!="")
			  {

				if($customer_email=="")
				{
				  $customer_email="noemail@gmail.com";
				}


				print_r($row);

				$request = new AuthorizeNetCIM;
				$customerProfile = new AuthorizeNetCustomer;
				$customerProfile->description = "Customer Profile id :".$row["customer_id"];
				$customerProfile->merchantCustomerId = time().rand(1,5000000);
				$customerProfile->email = $customer_email["email"];


				for($i=1;$i<=9;$i++)
				{
					if($row["cc_exp_month"]==$i)
					{
					   $row["cc_exp_month"]="0".$i;
					}
				}

				$paymentProfile = new AuthorizeNetPaymentProfile;
				$paymentProfile->customerType = "individual";
				$paymentProfile->payment->creditCard->cardNumber = $row["cc_number"];
				$paymentProfile->payment->creditCard->expirationDate = $row["cc_exp_year"]."-".$row["cc_exp_month"];
				$customerProfile->paymentProfiles[] = $paymentProfile;

				$response = $request->createCustomerProfile($customerProfile);
				$customerProfileId = $response->getCustomerProfileId();

				$request = new AuthorizeNetCIM;
				$response = $request->getCustomerProfile($customerProfileId);
			    $paymentProfileId = $response->getPaymentProfileId();

				echo $response->response;

				echo $update="update mg_save_creditcarddetail set cid='".$customerProfileId."',pid='".$paymentProfileId."' where id='".$row["id"]."'";
				mysql_query($update);

				echo "<br/>";


				echo $proid."<br/>";

				}
		  }

mysql_close();

?>