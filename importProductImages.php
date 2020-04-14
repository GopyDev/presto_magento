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

//echo $product->getId().",".$product->getSku().",".$product->getName(). "<br>";
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
				
				$imgUrl = save_image( $medium->url, $upc."_".++$imgCnt );
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
	
			echo $upc." saved.<br>";
		}
		else
		{
			echo $upc." not found in ItemMaster.<br>";
		}
		
	} else {
		echo $upc.' Call Failed.'."<br>";
	}
	
	
	$count++;

	function save_image($img, $imageFilename) {
	$imageFilename      = $imageFilename.".jpg";
	$image_type         = substr(strrchr($imageFilename,"."),1); //find the image extension
	$filename           = $imageFilename; //give a new name, you can modify as per your requirement
	$filepath           = Mage::getBaseDir('media') . DS . 'itemmaster'. DS . $filename; //path for temp storage folder: ./media/import/
	$newImgUrl          = file_put_contents($filepath, file_get_contents(trim($img))); //store the image from external url to the temp storage folder
	return $filepath;
}

echo $count. " Processed.";


?>