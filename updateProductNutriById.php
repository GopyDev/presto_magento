<?php 
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

	$start = $_REQUEST['start'];  //starting product id
	$end = $_REQUEST['end'];   //Ending product id

	/*$products = Mage::getModel('catalog/product')
	->getCollection()
	->addAttributeToSelect('*')
	->setCurPage($_REQUEST['curp'])->setPageSize(100); */
	
	$products = Mage::getModel('catalog/product')->getCollection()
	->addAttributeToSelect('*')
	->addAttributeToFilter('entity_id', array(
		'from' => $start ,
		'to' => $end
	))
	->load();
	
$totalProds = count($products);
$count = 0;
foreach($products as $product)
{
	//echo $product->getId().",".$product->getSku().",".$product->getName(). "<br>";
	//break;
//	$product->setName( $product->getName()." TTTTTT" );
//	$product -> save();
	$productupc = trim($product->getSku());
	
	$curl = curl_init('https://api.itemmaster.com/v2/item/?upc='.$productupc);
	
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
		//echo "<pre>";
		//print_r($xml);
		
		try{
			$ingredients = $xml->item->products->product->grocery->ingredients;
			
			$temperatureIndicator = $xml->item->products->product->grocery->ingredients->foodRelatedIndicators->temperatureIndicator;
			
			$warning = $xml->item->products->product->warnings->warning;
			
			$directions = $xml->item->products->product->directions;
			
			$glutenFree = $xml->item->products->product->grocery->foodRelatedIndicators->glutenFree; 
			
			$recycleCodes = $xml->item->products->product->grocery->recycleCodes; 
			
			$kosherCode = $xml->item->products->product->grocery->kosherCodes->kosherCode; 
			
			$name = $xml->item->name; // No need to update product name as per discussion on 14th July 2014
			
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

		if($productupc == $upc){
		
		
		$_newProduct = Mage::getModel('catalog/product')->load( $product->getId() );

		$_newProduct->setIngredients($ingredients);
		$_newProduct->setTemperatureindicator($temperatureIndicator);
		$_newProduct->setWarning($warning);
		$_newProduct->setDirections($directions);
		$_newProduct->setGlutenfree($glutenFree);
		$_newProduct->setRecyclecodes($recycleCodes);
		$_newProduct->setKoshercode($kosherCode);
		
		//$_newProduct->setName($name); // No need to update product name as per discussion on 14th July 2014
		
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
				$Successmsg .= "Product ID:  ".$product->getId()." is updated...<br>";
				
			} catch (Exception $e) {
				echo $upc." save error==<br>";
			}
		}	// Close UPC Condition...
		else{
			
			$failuremsg .= "Your Product Id ".$product->getId()." is notUpdated,Your SKU is not matched with item master UPC. Your SKU IS ".$productupc." <br>";
			
			//echo "Your Product Id ".$product->getId()." is notUpdated,Your SKU is not matched with item master UPC. Your SKU IS ".$productupc." <br>";
		}
		
		
	} else {
		echo $upc.' Call Failed.'."<br>";
	}

	$count++;
	//if($count == 3) break;
}
echo $Successmsg;
echo "<br>";
echo $failuremsg;
echo $count. " Processed.";
?>