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

 /* $proid=195695740;

  $request = new AuthorizeNetCIM;

				$customerProfileId=$proid;
				$proid=$proid+1;

				$response = $request->getCustomerProfile($customerProfileId);
				$paymentProfileId = $response->getPaymentProfileId();
				$customerAddressId = $response->getCustomerAddressId();

				$response = $request->deleteCustomerProfile($customerProfileId);
				$response = $request->deleteCustomerShippingAddress($customerProfileId, $customerAddressId);
			    $response = $request->deleteCustomerPaymentProfile($customerProfileId, $paymentProfileId); */


$proid=195751368;
		  $sel_data="SELECT *
FROM  `mg_save_creditcarddetail`";
		  $rs_data=mysql_query($sel_data);
		 for($b=1;$b<=1000;$b++) {

		     /* $sel_customer_email="select email from mg_customer_entity where entity_id='".$row["customer_id"]."'";
			  $rs_customer_email=mysql_query($sel_customer_email);
			  $customer_email=mysql_fetch_array($rs_customer_email);



				$request = new AuthorizeNetCIM;
				$customerProfile = new AuthorizeNetCustomer;
				$customerProfile->description = "Customer Profile id :".$row["customer_id"];
				$customerProfile->merchantCustomerId = time().rand(1,100);
				$customerProfile->email = $customer_email["email"];

				$paymentProfile = new AuthorizeNetPaymentProfile;
				$paymentProfile->customerType = "individual12";
				$paymentProfile->payment->creditCard->cardNumber = $row["cc_number"];
				$paymentProfile->payment->creditCard->expirationDate = $row["cc_exp_year"]."-".$row["cc_exp_month"];
				$customerProfile->paymentProfiles[] = $paymentProfile;

				$response = $request->createCustomerProfile($customerProfile);
				$customerProfileId = $response->getCustomerProfileId();

				$request = new AuthorizeNetCIM;
				$response = $request->getCustomerProfile($customerProfileId);
			    $paymentProfileId = $response->getPaymentProfileId();

				echo $update="update mg_save_creditcarddetail set cid='".$customerProfileId."',pid='".$paymentProfileId."' where id='".$row["id"]."'";
				mysql_query($update);

				echo "<br/>"; */


				echo $proid."<br/>";

				$request = new AuthorizeNetCIM;

				$customerProfileId=$proid;
				$proid=$proid+1;

				$response = $request->getCustomerProfile($customerProfileId);
				$paymentProfileId = $response->getPaymentProfileId();
				$customerAddressId = $response->getCustomerAddressId();

				$response = $request->deleteCustomerProfile($customerProfileId);
				$response = $request->deleteCustomerShippingAddress($customerProfileId, $customerAddressId);
			    $response = $request->deleteCustomerPaymentProfile($customerProfileId, $paymentProfileId);
		  }

mysql_close();

?>