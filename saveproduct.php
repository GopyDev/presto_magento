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
	//echo "HTTP response is ".$resultStatus['http_code'];
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
			
			
			// Changed by Naeem  Dated: 16th June 2014
			
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
        
		Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		$_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$upc);
		$_newProduct->setWebsiteIds(array(1));
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
		
		// Added by Naeem Dated: 16th June 2014
		
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
		
		
		$_product = Mage::getModel('catalog/product')->load($_newProduct->getId());
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

		$_newProduct->save();
		
		 Mage::app()->getStore()->setId(1);
		 
		 $_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$upc);
		
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
		$_newProduct->setPackageSizeMeasure($package_size_measure);
		$_newProduct->setPackageSizeUom($package_size_uom);
		$_newProduct->setNetweightMeasure($netWeight_measure);
		$_newProduct->setNetweightUom($netWeight_uom);
		
		try {
			$_newProduct->save();
			ini_set('display_errors', 1);
				
		umask(0);
		Mage::app();
		Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);

		$count = 0;
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
		if(count($xml->item[0]->media->medium) >= 1)
		{
			$imgCnt = 0;
			Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
			$_newProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$upc);
			
			$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
			$items = $mediaApi->items($_newProduct->getId());
			foreach($items as $item)
			{
				$mediaApi->remove($_newProduct->getId(), $item['file']);
            }
           $_newProduct->save();
		   
			foreach($xml->item[0]->media->medium as $medium)
			{
				$imgUrl = save_image($medium->url, $upc."_".++$imgCnt);
				if( $imgCnt == 1)
					$_newProduct->addImageToMediaGallery( $imgUrl , array('image', 'small_image', 'thumbnail'), true, false );
				else
					$_newProduct->addImageToMediaGallery( $imgUrl , array(), true, false );
					try {
						$_newProduct->save();
					} 
					catch (Exception $e) {
				    }
			}
	
			
		}
		else
		{
			
		}
		
	} 
	    else {
	     }
	$count++;
		} catch (Exception $e) {
		}
	} else {
	}

	$count++;

header("location:http://www.prestofreshgrocery.com/?gt=pm&upc=".$upc);

function save_image($img, $imageFilename) {
	$imageFilename      = $imageFilename.".jpg";
	$image_type         = substr(strrchr($imageFilename,"."),1); //find the image extension
	$filename           = $imageFilename; //give a new name, you can modify as per your requirement
	$filepath           = Mage::getBaseDir('media') . DS . 'itemmaster'. DS . $filename; //path for temp storage folder: ./media/import/
	$newImgUrl          = file_put_contents($filepath, file_get_contents(trim($img))); //store the image from external url to the temp storage folder
	return $filepath;
}
?>