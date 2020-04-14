<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



 require 'vendor/autoload.php';
	 
				define("AUTHORIZENET_API_LOGIN_ID", "85z7aV7XB");
				define("AUTHORIZENET_TRANSACTION_KEY", "3bWR6zZ8hx9h34zs");
				define("AUTHORIZENET_SANDBOX", false);
				
//select a database to work with

               $proid=197689400;
  
                 $request = new AuthorizeNetCIM;
				 
				 for($h=1;$h<=6000;$h++)
				 
				 {
				
				$customerProfileId=$proid;
				 $proid=$proid+1;
				
				$response = $request->getCustomerProfile($customerProfileId);
				$paymentProfileId = $response->getPaymentProfileId();
				$customerAddressId = $response->getCustomerAddressId();
				
				$response = $request->deleteCustomerProfile($customerProfileId);
				$response = $request->deleteCustomerShippingAddress($customerProfileId, $customerAddressId);
			    $response = $request->deleteCustomerPaymentProfile($customerProfileId, $paymentProfileId); 
				
               }

?>  	 