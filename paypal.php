<?php 

ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
$sel_all_profile="select * from alw_sarp_subscriptions where real_id!=''";
$rs_all_profile=$connection->query($sel_all_profile);
foreach($rs_all_profile as $pro)
{


$info = 'TRXTYPE=R'
        .'&TENDER=C'
        .'&PARTNER=PayPal'
        .'&VENDOR=fhcbooks'
		.'&USER=hiremarp'
        .'&PWD=rempub649!dev'
		.'&ACTION=I&PAYMENTHISTORY=Y'
        .'&ORIGPROFILEID='.$rs_all_profile;

$curl = curl_init('https://payflowpro.paypal.com');
curl_setopt($curl, CURLOPT_FAILONERROR, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($curl, CURLOPT_POSTFIELDS,  $info);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_POST, 1);

$result = curl_exec($curl);

print_r($result);

echo "<br/>";

}

exit(0);

$info = 'USER=paypal_api1.fhcbooks.com'
        .'&PWD=B7LR5DD5PV73N2U5'
        .'&SIGNATURE=ABOR3ZCLaF3qoW68iojgFYDqiEXtAKj8ZRvvQSe2e2Q9EsADiZ42k6zJ'
        .'&METHOD=TransactionSearch'
        // .'&TRANSACTIONCLASS=RECEIVED'
        .'&STARTDATE=2015-01-01T05:38:48Z'
        //.'&ENDDATE=2017-02-18T05:38:48Z'
		//.'&PROFILEID=RP0000000039'
        .'&VERSION=95';

$curl = curl_init('https://api-3t.paypal.com/nvp');
curl_setopt($curl, CURLOPT_FAILONERROR, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($curl, CURLOPT_POSTFIELDS,  $info);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_POST, 1);

$result = curl_exec($curl);

# Bust the string up into an array by the ampersand (&)
# You could also use parse_str(), but it would most likely limit out
$result = explode("&", $result);

# Loop through the new array and further bust up each element by the equal sign (=)
# and then create a new array with the left side of the equal sign as the key and the right side of the equal sign as the value
foreach($result as $value){
    $value = explode("=", $value);
    $temp[$value[0]] = $value[1];
}

# At the time of writing this code, there were 11 different types of responses that were returned for each record
# There may only be 10 records returned, but there will be 110 keys in our array which contain all the different pieces of information for each record
# Now create a 2 dimensional array with all the information for each record together
for($i=0; $i<count($temp)/11; $i++){

  
    $returned_array[$i] = array(
        "timestamp"         =>    urldecode($temp["L_TIMESTAMP".$i]),
        "timezone"          =>    urldecode($temp["L_TIMEZONE".$i]),
        "type"              =>    urldecode($temp["L_TYPE".$i]),
        "email"             =>    urldecode($temp["L_EMAIL".$i]),
        "name"              =>    urldecode($temp["L_NAME".$i]),
        "transaction_id"    =>    urldecode($temp["L_TRANSACTIONID".$i]),
        "status"            =>    urldecode($temp["L_STATUS".$i]),
        "amt"               =>    urldecode($temp["L_AMT".$i]),
        "currency_code"     =>    urldecode($temp["L_CURRENCYCODE".$i]),
        "fee_amount"        =>    urldecode($temp["L_FEEAMT".$i]),
        "net_amount"        =>    urldecode($temp["L_NETAMT".$i]));
 
}

for($l=0;$l<=$i;$l++)
{
		if($returned_array[$l]["amt"]=='50.00')
		{
		   // print_r($returned_array[$l]);
		   
		  echo $returned_array[$l]["transaction_id"];
					
		   $info = 'USER=paypal_api1.fhcbooks.com'
        .'&PWD=B7LR5DD5PV73N2U5'
        .'&SIGNATURE=ABOR3ZCLaF3qoW68iojgFYDqiEXtAKj8ZRvvQSe2e2Q9EsADiZ42k6zJ'
					.'&METHOD=GetTransactionDetails'
					.'&TRANSACTIONID='.$returned_array[$l]["transaction_id"]
        .'&STARTDATE=2015-01-01T05:38:48Z'
        .'&ENDDATE=2017-02-18T05:38:48Z'
        .'&VERSION=95';
		
			
			$curl = curl_init('https://api-3t.paypal.com/nvp');
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			
			curl_setopt($curl, CURLOPT_POSTFIELDS,  $info);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_POST, 1);
			
			$result = curl_exec($curl);
			
			parse_str($result, $result);
			
			foreach($result as $key => $value){
				
				
				echo $key."=".$value;
				echo "<br/>";
			}
			
			//echo $mego."<br/>";		
					

		}
}
?>