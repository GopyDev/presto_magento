<?php
	ini_set('memory_limit', '3G');
	set_time_limit(360000);
	//error_reporting(E_ALL | E_STRICT);
	require_once 'app/Mage.php';
	//$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	//ini_set('display_errors', 1);
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");
    $curl = curl_init('https://api.itemmaster.com/v2/item/?q='.$_GET["desc"].'&epl=50&epl=50&epf=50&epf=50&ef=png');

	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'username:sdemoulpied',
		'password:Password1'
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
	$response = curl_exec($curl);
	$resultStatus = curl_getinfo($curl);
	//echo $resultStatus['http_code'];
	if($resultStatus['http_code'] == 200) {
		$string = $response;
		$xml = simplexml_load_string($string);
		//print_r($xml);
		$json = json_encode($xml);
        $array = json_decode($json,TRUE);
		?>
        <table border="1" style="float:left;width:47%; border-collapse:collapse;" cellpadding="5" cellspacing="5" >
        <tr>
        <th>Name</th>
        <th>Sku</th>
        <th>Image</th>
        <th>In Magneto</th>
        </tr>
        <?php
		$aa=array();
		$i=0;
        foreach ($array["item"] as $value3) {

		// print_r($array["item"]);

		   $sel_number="select * from mg_catalog_product_entity where sku='".$value3["upcs"]["upc"]."'";
		   $rs_number=mysql_query($sel_number);
		   $total_number=mysql_fetch_array($rs_number);
		   $aa[$i] = $value3["upcs"]["upc"];
		   $i++;
		   // print_r($value3["media"]["medium"][0]);
		   // echo "<br/>";

		   // $packageType = $xml->item->packageData->packageType;

			//echo $package_size_measure = $xml->item->packageData->packageSize->measure;
			//echo $package_size_uom = $xml->item->packageData->packageSize->uom;

			//$netWeight_measure = $xml->item->packageData->netWeight->measure;
			//$netWeight_uom = $xml->item->packageData->netWeight->uom;

			print_r($value3["packageData"] ["measure"] );
		?>
		<tr>
        <td><?php echo $value3["name"]; ?> <br/> Size :-
        <?php echo $value3["packageData"]["packageSize"]["measure"]; ?> <?php echo $value3["packageData"]["packageSize"]["uom"]; ?>
        </td>
        <td><?php echo $value3["upcs"]["upc"]; ?></td>
        <td>
        <img src="<?php echo $value3["media"]["medium"][0]["url"]; ?>" width="50" height="50"  />
        </td>
        <td><?php echo $total_number["entity_id"]; ?>

        <?php if($total_number["entity_id"]=="") { ?>
         <a href="http://www.prestofreshgrocery.com/addandsave.php?curp=<?php echo $value3["upcs"]["upc"]; ?>" target="_blank"> Add Product </a>
        <?php } else {  ?>
        <a href="http://www.prestofreshgrocery.com/saveproduct.php?curp=<?php echo $value3["upcs"]["upc"]; ?>" target="_blank" > Update Product </a>
        <?php } ?>
        </td>
        </tr>
        <?php } ?>
        </table>
		<?php
            curl_close($curl);
            }
        ?>

        <?php // print_r($aa); ?>

        <table border="1" style="float:left;width:49%;margin-left:2%;border-collapse:collapse;" cellpadding="5" cellspacing="5" >
        <tr>
        <th>Name</th>
        <th>Sku</th>
        <th>Image</th>
        </tr>
        <?php
		   $sel_number="select * from mg_catalog_product_flat_1 where name LIKE '".$_GET["desc"]."%'";
		   $rs_number=mysql_query($sel_number);
		   while($total_number=mysql_fetch_array($rs_number))
		   {
		       if (in_array($total_number["sku"], $aa))
			   {
			      $gr=1;
			   }
			   else
			   {
			     $gr=0;
			   }

			  // echo $gr;
		?>
		<tr>
        <td><?php echo $total_number["name"]; ?>
        <br/> Size : <?php echo $total_number["size"]; ?> &nbsp;&nbsp;&nbsp; Price : <?php echo $total_number["price"]; ?>
        </td>
        <td <?php if($gr==1) { ?> style="background:#00FFFF;" <?php } else { ?> style="background:#fff;" <?php }?> ><?php echo $total_number["sku"]; ?></td>
        <td><img src="http://www.prestofreshgrocery.com/media/catalog/product/<?php echo $total_number["small_image"]; ?>" width="50" height="50"  /></td>
        <!--<td></td>-->
        </tr>
        <?php } ?>
        </table>