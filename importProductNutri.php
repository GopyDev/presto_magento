<?php 
if($_REQUEST['curp'] == "") {echo "ERROR";exit; }
ini_set('memory_limit', '3G');
set_time_limit(360000);
error_reporting(E_ALL | E_STRICT);

require_once 'app/Mage.php';

	ini_set('display_errors', 1);
	umask(0);
	Mage::app();
	Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

	$count = 0;
	$upc = trim($_REQUEST['curp']);
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
		
		try{
			$serving_size = "";
			if(
			!empty($xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[1]->measure) &&
			!empty($xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[0]->measure) )
			{
				$serving_size = $xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[1]->measure." ".$xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[1]->uom." (".$xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[0]->measure.$xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[0]->uom.")";
			}

			$servings_per_container = $xml->item->products->product->grocery->nutritions->nutrition->numberOfServings;
			$calories = $xml->item->products->product->grocery->nutritions->nutrition->energy;
			$totalfat = $xml->item->products->product->grocery->nutritions->nutrition->totalFat;
			$saturatedfat = $xml->item->products->product->grocery->nutritions->nutrition->saturatedFat;
			$transfat = $xml->item->products->product->grocery->nutritions->nutrition->transFat;
			$cholesterol = $xml->item->products->product->grocery->nutritions->nutrition->cholesterol;
			$sodium = $xml->item->products->product->grocery->nutritions->nutrition->sodium;
			$totalcarbohydrate = $xml->item->products->product->grocery->nutritions->nutrition->carbohydrates;
			$dietaryfiber = $xml->item->products->product->grocery->nutritions->nutrition->dietaryFiber;
			$sugars = $xml->item->products->product->grocery->nutritions->nutrition->sugars;
			$protein_per_serving = $xml->item->products->product->grocery->nutritions->nutrition->protein;
			$dvp_vitamin_a = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfVitaminA;
			$dvp_vitamin_c = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfVitaminC;
			$dvp_calcium = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfCalcium;
			$dvp_iron = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfIron;
			$ingredients = $xml->item->products->product->grocery->ingredients;
			$warning = $xml->item->products->product->warnings->warning;
			$directions = $xml->item->products->product->directions;
			$allergen = '';
			$glutenfree = '';
			$kosher = '';
			$allergen = $xml->item->products->product->grocery->allergen->allergen;
			$glutenfree = $xml->item->products->product->grocery->glutenfree->glutenfree;
			$kosher  = $xml->item->products->product->grocery->kosherCodes->kosherCode;
			
			
			// Changed by Naeem  Dated: 16th June 2014
			
			$name = $xml->item->name;
			$name = str_replace("&","&amp;",$name);
			
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
			
			
			
			//echo "Measure is ".$package_size_measure;
                        // exit;
			/* Naeem changed end */
			
			
			
			/*Mage::log($name,null,'TEST.log');
			Mage::log($marketing_description,null,'TEST.log');
			Mage::log($otherDescription,null,'TEST.log');
			Mage::log($upc,null,'TEST.log');
			Mage::log($category,null,'TEST.log');
			Mage::log($created,null,'TEST.log');
			Mage::log($last_updated,null,'TEST.log');
			Mage::log($length_measure,null,'TEST.log');
			Mage::log($length_uom,null,'TEST.log');
			Mage::log($height_measure,null,'TEST.log');
			Mage::log($height_uom,null,'TEST.log');
			Mage::log($width_measure,null,'TEST.log');
			Mage::log($width_uom,null,'TEST.log');
			Mage::log($packageType,null,'TEST.log');
			Mage::log($package_size_uom,null,'TEST.log');
			Mage::log($netWeight_measure,null,'TEST.log');
			Mage::log($netWeight_uom,null,'TEST.log');*/
			
		}
		catch (Exception $err){ echo $err->getMessage();}

		/*$serving_size = "";
		if(
		!empty($xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[1]->measure) &&
		!empty($xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[0]->measure) )
		{
			echo $serving_size = $xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[1]->measure." ".$xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[1]->uom." (".$xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[0]->measure.$xml->item->products->product->grocery->nutritions->nutrition->servingSizes->servingSize[0]->uom.")";
		}
		
		echo $servings_per_container = $xml->item->products->product->grocery->nutritions->nutrition->numberOfServings;
		echo $calories = $xml->item->products->product->grocery->nutritions->nutrition->energy;
		echo $totalfat = $xml->item->products->product->grocery->nutritions->nutrition->totalFat;
		echo $saturatedfat = $xml->item->products->product->grocery->nutritions->nutrition->saturatedFat;
		echo $transfat = $xml->item->products->product->grocery->nutritions->nutrition->transFat;
		echo $cholesterol = $xml->item->products->product->grocery->nutritions->nutrition->cholesterol;
		echo $sodium = $xml->item->products->product->grocery->nutritions->nutrition->sodium;
		echo $totalcarbohydrate = $xml->item->products->product->grocery->nutritions->nutrition->carbohydrates;
		echo $dietaryfiber = $xml->item->products->product->grocery->nutritions->nutrition->dietaryFiber;
		echo $sugars = $xml->item->products->product->grocery->nutritions->nutrition->sugars;
		echo $protein_per_serving = $xml->item->products->product->grocery->nutritions->nutrition->protein;
		echo $dvp_vitamin_a = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfVitaminA;
		echo $dvp_vitamin_c = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfVitaminC;
		echo $dvp_calcium = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfCalcium;
		echo $dvp_iron = $xml->item->products->product->grocery->nutritions->nutrition->dailyPercentOfIron;
		echo $ingredients = $xml->item->products->product->grocery->ingredients;*/
		
		/*if(trim($serving_size) == "")
		{
			echo $upc." not found in ItemMaster.<br>";
			$count++;
			exit;
		}*/
		
		
		$_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$upc);
		//$_newProduct = Mage::getModel('catalog/product')->load( 'sku',$upc );
		/*echo "<pre>";
		print_r($_newProduct);
		exit; */
		$price=$_newProduct->getPrice();
		

		//$serving_size = '10';
		 Mage::app()->getStore()->setId(1);
		$_newProduct->setServingSize($serving_size);
		$_newProduct->setWebsiteIds(array(1));
		$_newProduct->setPrice($price);
		$_newProduct->setServingsPerContainer($servings_per_container);
		$_newProduct->setCalories($calories);
		$_newProduct->setTotalfat($totalfat);
		$_newProduct->setSaturatedfat($saturatedfat);
		$_newProduct->setTransfat($transfat);
		$_newProduct->setCholesterol($cholesterol);
		$_newProduct->setSodium($sodium);
		$_newProduct->setTotalcarbohydrate($totalcarbohydrate);
		$_newProduct->setDietaryfiber($dietaryfiber);
		$_newProduct->setSugars($sugars);
		$_newProduct->setProteinPerServing($protein_per_serving);
		$_newProduct->setDvpVitaminA($dvp_vitamin_a);
		$_newProduct->setDvpVitaminC($dvp_vitamin_c);
		$_newProduct->setDvpCalcium($dvp_calcium);
		$_newProduct->setDvpIron($dvp_iron);
		$_newProduct->setIngredients($ingredients);
		$_newProduct->setWarning($warning);
		$_newProduct->setDirections($directions);
        $_newProduct->setPackageSizeMeasure($package_size_measure);
        $_newProduct->setSize($package_size_measure);
		// Added by Naeem Dated: 16th June 2014
		
		$_newProduct->setName($name);
		$url = preg_replace('#[^0-9a-z]+#i', '-', $name);
		$url = strtolower($url);
		$_newProduct->setUrlKey($url);
		$_newProduct->setMarketingDescription($marketing_description);
		$_newProduct->setDescription($otherDescription);
		$_newProduct->setUpcCategory($category);
		$_newProduct->setLengthMeasure($length_measure);
		$_newProduct->setLengthUom($length_uom);
		$_newProduct->setHeightMeasure($height_measure);
		$_newProduct->setHeightUom($height_uom);
		$_newProduct->setWidthMeasure($width_measure);
		$_newProduct->setWidthUom($width_uom);
		$_newProduct->setPackageType($packageType);
        $_newProduct->setPackageSizeUom($package_size_uom);
		$_newProduct->setNetweightMeasure($netWeight_measure);
		$_newProduct->setNetweightUom($netWeight_uom);
		$_newProduct->save();
		
		
		Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		$_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$upc);
		//$_newProduct = Mage::getModel('catalog/product')->load( 'sku',$upc );
		/*echo "<pre>";
		print_r($_newProduct);
		exit; */
		
		//$serving_size = '10';
		$_newProduct->setServingSize($serving_size);
		$_newProduct->setServingsPerContainer($servings_per_container);
		$_newProduct->setCalories($calories);
		$_newProduct->setTotalfat($totalfat);
		$_newProduct->setSaturatedfat($saturatedfat);
		$_newProduct->setTransfat($transfat);
		$_newProduct->setCholesterol($cholesterol);
		$_newProduct->setSodium($sodium);
		$_newProduct->setTotalcarbohydrate($totalcarbohydrate);
		$_newProduct->setDietaryfiber($dietaryfiber);
		$_newProduct->setSugars($sugars);
		$_newProduct->setProteinPerServing($protein_per_serving);
		$_newProduct->setDvpVitaminA($dvp_vitamin_a);
		$_newProduct->setDvpVitaminC($dvp_vitamin_c);
		$_newProduct->setDvpCalcium($dvp_calcium);
		$_newProduct->setDvpIron($dvp_iron);
		$_newProduct->setIngredients($ingredients);
		$_newProduct->setWarning($warning);
		$_newProduct->setDirections($directions);
        $_newProduct->setPackageSizeMeasure($package_size_measure);
        $_newProduct->setSize($package_size_measure);
		$_newProduct->setPrice($price);
		// Added by Naeem Dated: 16th June 2014
		
		$_newProduct->setName($name);
		$_newProduct->setWebsiteIds(array(1));
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
        $_newProduct->setPackageSizeUom($package_size_uom);
		$_newProduct->setNetweightMeasure($netWeight_measure);
		$_newProduct->setNetweightUom($netWeight_uom);
		
		if($allergen != ''){
			$_newProduct->setallergen($allergen);
		}else{
			$allergen = '';
			$_newProduct->setallergen($allergen);
		}
		if($glutenfree != ''){
			$_newProduct->setglutenfree($glutenfree);
		}else{
			$glutenfree = '';
			$_newProduct->setglutenfree($glutenfree);
		}
		if($kosher != ''){
			$_newProduct->setkoshercode($kosher);
		}else{
			$glutenfree = '';
			$_newProduct->setkoshercode($kosher);
		}
		
		try {
			/*echo "<pre>";
			print_r($_newProduct);
                        exit;*/

/***************************************
	// Update Quantity,status
	// Changed by Naeem
	// Chaged Date: 16/7/15
/**************************************/
$product_sku = $_GET['curp']; // use your own sku number
$product_id = Mage::getModel("catalog/product")->getIdBySku( $product_sku );

		$productId = $product_id ;

		$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
		if ($stockItem->getId() >= 0 and $stockItem->getManageStock()) {
			$qty = 1000;
			$stockItem->setQty($qty);
			$stockItem->setIsInStock((int)($qty > 0));
			$stockItem->save();
                    
		}
		
		
		

//$productid=10;// product id which you want to change status;
 
$storeid=1; // your store id 0 is for default store id
Mage::getModel('catalog/product_status')->updateProductStatus($productId, $storeid, Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

/***********************
// Remove Old Images
/***********************/

$_product = Mage::getModel('catalog/product')->load($productId);
$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
try {
    $items = $mediaApi->items($_product->getId());
	
    foreach($items as $item) {
        $mediaApi->remove($_product->getId(), $item['file']);
    }
} catch (Exception $exception){
    var_dump($exception);
    die('Exception Thrown');
}

/************************
// End remove images
/*************************/

			$_newProduct->save();
			
			
			//require_once('importProductImages.php?curp='.$upc);
				ini_set('display_errors', 1);
umask(0);
Mage::app();
Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

$count = 0;

//echo "Product ID is ".$product->getId().",".$product->getSku().",".$product->getName(). "<br>";
//$product->setName( $product->getName()." TTTTTT" );
//$product -> save();
	$upc = trim($_REQUEST['curp']);
	
	$curl = curl_init('https://api.itemmaster.com/v2/item/?upc='.$upc.'&pi=e&epl=600&epf=600&epr=600&ef=jpg');
	
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
//		echo "<pre>";
//		print_r($xml);
		
		if(count($xml->item[0]->media->medium) >= 1)
		{
			$imgCnt = 0;
			foreach($xml->item[0]->media->medium as $medium)
			{
				
				$imgUrl = save_image($medium->url, $upc."_".++$imgCnt);
				$_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$upc);
				//$_newProduct = Mage::getModel('catalog/product')->load( $product->getId() );
				
				if( $imgCnt == 1)
					$_newProduct->addImageToMediaGallery( $imgUrl , array('image', 'small_image', 'thumbnail'), true, false );
				else
					$_newProduct->addImageToMediaGallery( $imgUrl , array(), true, false );
				
				try {
					$_newProduct->save();
					//echo $upc." saved<br>";
				} catch (Exception $e) {
					//echo $upc." Image save error==".$e."<br>";
				}
	
			}
	
		}
		else
		{
		
		}
		
	} else {
		
	}
		
	$count++;

	
			
			
			/**************************
			
				End image upload
			/***************************/
		} catch (Exception $e) {
		}
	} else {
	}

	$count++;

	 header("location:http://www.prestofreshgrocery.com/?gt=pm&upc=".$upc."&ppid=".$_newProduct->getId());

function save_image($img, $imageFilename) {
	$imageFilename      = $imageFilename.".jpg";
	$image_type         = substr(strrchr($imageFilename,"."),1); //find the image extension
	$filename           = $imageFilename; //give a new name, you can modify as per your requirement
	$filepath           = Mage::getBaseDir('media') . DS . 'itemmaster'. DS . $filename; //path for temp storage folder: ./media/import/
	$newImgUrl          = file_put_contents($filepath, file_get_contents(trim($img))); //store the image from external url to the temp storage folder
	return $filepath;
}
?>