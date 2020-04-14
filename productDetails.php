<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

$skus = array("33776011741" => "Butter and Margarine (ID: 4)", "2100012125" => "Cottage Cheese (ID: 5", "20188050109" => "Cream Cheese (ID: 6)", "3360453500" => "Eggs and Egg Substitutes (ID: 7", "645125123458" => "Milk and Cream (ID: 8", "2100063897" => "Packaged Cheese (ID: 9", "18000004683" => "Refrigerated Doughs (ID: 10", "21000301676" => "Sour Cream (ID: 11)", "SF-81662" => "Yogurt (ID: 13)", "58335001971" => "Fruit (ID: 38", "58335000233" => "Vegetables (ID: 40)", "44700019764" => "MEAT", "70177154127" => "Beverages (ID: 42", "52603041201" => "Canned Goods & Soups (ID: 114", "602652176524" => "Snacks (ID: 123", "74401310419" => "Grains Pasta Beans (ID: 70", "71518010423" => "Breakfast & Cereal (ID: 137)", "40100002948" => "Cooking Needs (ID: 82", "20732400000" => "Cheese (ID: 29)", "233005000007" => "Meals & Sides Etc (ID: 34", "7218063300" => "Pizza (ID: 67", "7056085615" => "Frozen Vegetables (ID: 68", "7255400154" => "Ice Cream & Novelties (ID: 69)", "1540019075" => "Organic & Natural (ID: 141)", "2580002246" => "Meals & Entrees (ID: 66");

$count = 0;
foreach($skus as $k => $v)
{
	$curl = curl_init('https://api.itemmaster.com/v2/item/?upc='.$k);
	
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'username:sdemoulpied',
		'password:Password1'
	));
	
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
	
	$response = curl_exec($curl);                                          
	$resultStatus = curl_getinfo($curl);                                   
	
	if($resultStatus['http_code'] == 200) {
		$string = $response;
		$xml = simplexml_load_string($string);
		echo "<h3>".$v." | ".$k."</h3>";
		echo "<pre>";
		print_r($xml);
		echo "</pre>";
	} else {
		echo $k.' Call Failed.'."<br>";
	}
	
	$count++;
	//if($count == 5) break;
}

echo $count. " Processed.";

?>