<?php

function json_indent($json) {
  $result      = '';
  $pos         = 0;
  $strLen      = strlen($json);
  $indentStr   = '  ';
  $newLine     = "\n";
  $prevChar    = '';
  $outOfQuotes = true;

  for ($i=0; $i<=$strLen; $i++) {
    // Grab the next character in the string.
    $char = substr($json, $i, 1);

    // Are we inside a quoted string?
    if ($char == '"' && $prevChar != '\\') {
      $outOfQuotes = !$outOfQuotes;

    // If this character is the end of an element,
    // output a new line and indent the next line.
    } else if(($char == '}' || $char == ']') && $outOfQuotes) {
      $result .= $newLine;
      $pos --;
      for ($j=0; $j<$pos; $j++) {
        $result .= $indentStr;
      }
    }

    // Add the character to the result string.
    $result .= $char;

    // If the last character was the beginning of an element,
    // output a new line and indent the next line.
    if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
      $result .= $newLine;
      if ($char == '{' || $char == '[') {
        $pos ++;
      }

      for ($j = 0; $j < $pos; $j++) {
        $result .= $indentStr;
      }
    }

    $prevChar = $char;
  }
  return $result;
}

function executeRest($url, $method, $data = "") {
  $headers = array(
    'Accept: application/json',
    'Content-Type: application/json'
  );

  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $url);
  curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($handle, CURLOPT_TIMEOUT, 360);

  switch($method)
  {
    case 'GET':
    break;

    case 'POST':
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    break;

    case 'PUT':
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    break;

    case 'DELETE':
    curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
    break;
  }

  $response = curl_exec($handle);
  $status = curl_getinfo($handle, CURLINFO_HTTP_CODE);

  return json_indent($response);
}

$date = !empty($_REQUEST['date']) ? $_REQUEST['date'] : date('Ymd');
$url = 'https://wwrm.workwave.com/api/v1/territories/d6cf918e-390d-4f2c-88c0-e9f088fcfbf6/approved/routes?key=e8712eea-32c3-4536-83fd-91fa0c82698c&date='.$date;

echo executeRest($url,'GET');

exit;



     error_reporting(E_ALL);
     ini_set('display_errors', 1);
     require 'vendor/autoload.php';

	    define("AUTHORIZENET_API_LOGIN_ID", "85z7aV7XB");
		define("AUTHORIZENET_TRANSACTION_KEY", "3bWR6zZ8hx9h34zs");
		define("AUTHORIZENET_SANDBOX", false);
		/* $sale           = new AuthorizeNetAIM;
		$sale->amount   = "5";
		$sale->description = $description = "Gratuaity for order # Testing ";
		 $sale->invoice_num = $invoice_number = "Gratuaity for order # Testing";
		$sale->card_num = '4246315197866609';
		$sale->exp_date = '11/17';
		$response = $sale->authorizeAndCapture();
		 print_r($response);
		if ($response->approved) {
			echo $transaction_id = $response->transaction_id;
		}


		$request  = new AuthorizeNetTD;
        $response = $request->getTransactionDetails("2243594413");
		echo $response->xml->transaction->transactionStatus;
		print_r($response); */


		   /* $request = new AuthorizeNetCIM;
			$customerProfile = new AuthorizeNetCustomer;
			$customerProfile->description = "Testing By Hiren";
			$customerProfile->merchantCustomerId = time().rand(1,100);
			$customerProfile->email = "adhiyahiren1089@gmail.com";

			 $paymentProfile = new AuthorizeNetPaymentProfile;
			$paymentProfile->customerType = "individual";
			$paymentProfile->payment->creditCard->cardNumber = "4111111111111111";
			$paymentProfile->payment->creditCard->expirationDate = "2015-10";
			$customerProfile->paymentProfiles[] = $paymentProfile;

			$response = $request->createCustomerProfile($customerProfile);
            $customerProfileId = $response->getCustomerProfileId();

			echo $customerProfileId;
			print_r($response); */

			$request = new AuthorizeNetCIM;
			$customerProfileId='197710376';
            $response = $request->getCustomerProfile($customerProfileId);

			// print_r($response);


			     $paymentProfileId = $response->getPaymentProfileId();
				 echo $customerAddressId = $response->getCustomerAddressId();
                 $request2 = new AuthorizeNetCIM;
				//$paymentProfileId = '188626408';
				$response2 = $request2->getCustomerPaymentProfile($customerProfileId, $paymentProfileId);

	           // print_r($response2);



				$transaction = new AuthorizeNetTransaction;
				$transaction->amount = "1";
				$transaction->order->invoiceNumber = "Hiren Adhiya Testing";
				$transaction->customerProfileId = $customerProfileId;
				$transaction->customerPaymentProfileId = $paymentProfileId;



				    $response = $request->createCustomerProfileTransaction("AuthCapture", $transaction);
					$transactionResponse = $response->getTransactionResponse();
					$transactionId = $transactionResponse->transaction_id;
					echo $transactionId;


?>
