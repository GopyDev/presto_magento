<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

$products = Mage::getModel('catalog/product')
	->getCollection()
	->addAttributeToSelect('*')
	->setCurPage($_REQUEST['curp'])->setPageSize(100);
	
$totalProds = count($products);
$count = 0;
foreach($products as $product)
{
	//echo $product->getId().",".$product->getSku().",".$product->getName(). "<br>";
	//break;
//	$product->setName( $product->getName()." TTTTTT" );
//	$product -> save();
	$upc = $product->getSku();
	
	$curl = curl_init('https://api.itemmaster.com/v2/item/?upc='.$upc);
	
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
		/*echo "<pre>";
		print_r($xml);*/

		try{
			$ingredients = $xml->item->products->product->grocery->ingredients;
			$temperatureIndicator = $xml->item->products->product->grocery->ingredients->foodRelatedIndicators->temperatureIndicator;
			$warning = $xml->item->products->product->warnings->warning;
			$directions = $xml->item->products->product->directions;
			$glutenFree = $xml->item->products->product->grocery->foodRelatedIndicators->glutenFree; 
			$recycleCodes = $xml->item->products->product->grocery->recycleCodes; 
			$kosherCode = $xml->item->products->product->grocery->kosherCodes->kosherCode; 
		}
		catch (Exception $err){ echo $err->getMessage();}

			
		/*echo "<br>".$ingredients = $xml->item->products->product->grocery->ingredients;
		echo "<br>".$temperatureIndicator = $xml->item->products->product->grocery->foodRelatedIndicators->temperatureIndicator;
		echo "<br>".$warning = $xml->item->products->product->warnings->warning;
		echo "<br>".$directions = $xml->item->products->product->directions;
		echo "<br>".$glutenFree = $xml->item->products->product->grocery->foodRelatedIndicators->glutenFree; 
		echo "<br>".$recycleCodes = $xml->item->products->product->grocery->recycleCodes; 
		echo "<br>".$kosherCode = $xml->item->products->product->grocery->kosherCodes->kosherCode;*/ 
		
		/*if(trim($serving_size) == "")
		{
			echo $upc." not found in ItemMaster.<br>";
			$count++;
			continue;
		}*/
		
		//$_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',"071464301514");
		$_newProduct = Mage::getModel('catalog/product')->load( $product->getId() );

		$_newProduct->setIngredients($ingredients);
		$_newProduct->setTemperatureindicator($temperatureIndicator);
		$_newProduct->setWarning($warning);
		$_newProduct->setDirections($directions);
		$_newProduct->setGlutenfree($glutenFree);
		$_newProduct->setRecyclecodes($recycleCodes);
		$_newProduct->setKoshercode($kosherCode);
		
		try {
			$_newProduct->save();
			echo $upc." saved<br>";
		} catch (Exception $e) {
			echo $upc." save error==<br>";
		}
	} else {
		echo $upc.' Call Failed.'."<br>";
	}

	$count++;
	//if($count == 3) break;
}

echo $count. " Processed.";
?>