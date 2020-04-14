<?php

	include("connection.php");
	$postcode = $_GET['postcode'];
	$query1 = "SELECT value FROM mg_core_config_data WHERE path = 'simpleupc/options/zipcodes' ";
	$rs = mysql_query($query1);
	$results  = array();
	//$results = mysql_fetch_assoc($rs);
	while($row = mysql_fetch_array($rs) ){
		//$results[] = $row['value'];
		//echo $row['value'];
		//echo "<br/>";
		$temp = trim($row['value']);
	}  
	
		
	if (strpos($temp, $postcode) !== false){
		 echo "1"; //if found then return 1	
	}else{
		echo "0"; //if not found then return 0
		
	}

?>