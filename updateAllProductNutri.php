<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

	/*$products = Mage::getModel('catalog/product')
	->getCollection()
	->addAttributeToSelect('*')
	->setCurPage($_REQUEST['curp'])->setPageSize(200); */
	
	$products = Mage::getModel('catalog/product')->getCollection();
	
$totalProds = count($products);

$count = 0;
foreach($products as $product)
{
	
	//echo $product->getId().",".$product->getSku().",".$product->getName(). "<br>";
	//break;
//	$product->setName( $product->getName()." TTTTTT" );
//	$product -> save();
	$upc = $product->getSku();
	if($upc != '' ){
	
	
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
		echo "<pre>";
		print_r($xml);

		try{
			$ingredients = $xml->item->products->product->grocery->ingredients;
			$temperatureIndicator = $xml->item->products->product->grocery->ingredients->foodRelatedIndicators->temperatureIndicator;
			$warning = $xml->item->products->product->warnings->warning;
			$directions = $xml->item->products->product->directions;
			$glutenFree = $xml->item->products->product->grocery->foodRelatedIndicators->glutenFree; 
			$recycleCodes = $xml->item->products->product->grocery->recycleCodes; 
			$kosherCode = $xml->item->products->product->grocery->kosherCodes->kosherCode; 
			
			// Changed by Naeem  Dated: 10th July 2014
			
			$name = $xml->item->name;
			$marketing_description = $xml->item->marketingDescription;
			$otherDescription = $xml->item->otherDescription;
			$upc = $xml->item->upcs->upc;
			$category = $xml->item->categories->category;
			$created = $xml->item->created;
			$last_updated = $xml->item->lastUpdated;
			
			$length_measure = $xml->item->packageData->length->measure;
			$length_uom = $xml->item->packageData->length->uom;
			
			$height_measure = $xml->item->packageData->height->measure;
			$height_uom = $xml->item->packageData->height->uom;
			
			$width_measure = $xml->item->packageData->width->measure;
			$width_uom = $xml->item->packageData->width->uom;
			
			$packageType = $xml->item->packageData->packageType;
			
			$package_size_measure = $xml->item->packageData->packageSize->measure;
			$package_size_uom = $xml->item->packageData->packageSize->uom;
			
			$netWeight_measure = $xml->item->packageData->netWeight->measure;
			$netWeight_uom = $xml->item->packageData->netWeight->uom;
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
		
		
		// Added by Naeem Dated: 10th July 2014
		
		$_newProduct->setName($name);
		$_newProduct->setMarketingDescription($marketing_description);
		$_newProduct->setDescription($otherDescription);
		$_newProduct->setSku($upc);
		$_newProduct->setUpcCategory($category);
		$_newProduct->setLengthMeasure($length_measure);
		$_newProduct->setLengthUom($length_uom);
		$_newProduct->setHeightMeasure($height_measure);
		$_newProduct->setHeightUom($height_uom);
		$_newProduct->setWidthMeasure($width_measure);
		$_newProduct->setWidthUom($width_uom);
		$_newProduct->setPackageType($packageType);
		$_newProduct->setPackageSizeMeasure($package_size_measure);
		$_newProduct->setPackageSizeUom($package_size_uom);
		$_newProduct->setNetweightMeasure($netWeight_measure);
		$_newProduct->setNetweightUom($netWeight_uom);
		
		try {
			$_newProduct->save();
			echo $upc." saved<br>";
			echo "Product ID is updated.... ".$product->getId();
		} catch (Exception $e) {
			echo $upc." save error==<br>";
		}
	} else {
		echo $upc.' Call Failed.'."<br>";
	}

	$count++;
	//if($count == 3) break;
	} // Close sku checking condition...
}

echo $count. " Processed.";
?>