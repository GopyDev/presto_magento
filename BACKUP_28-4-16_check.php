<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Validate page</title>
</head>

<body>
<?php 
 $postcode =	$_POST['postcode'];

		$zips = array(44011,44017,44022,44023,44039,44040,44070,44077,44092,44094,44095,44101,44102,44103,44104,44105,44106,44107,44108,44109,44110,44111,44112,44113,44114,44115,44116,44117,44118,44119,44120,44121,44122,44123,44124,44125,44126,44127,44128,44129,44130,44131,44132,44133,44134,44135,44136,44137,44138,44139,44140,44141,44142,44143,44144,44145,44146,44147,44149,44181,44188,44190,44191,44192,44193,44194,44195,44197,44198,44199,44202);
		if(!in_array($postcode, $zips)){
		$con =	mysql_connect("localhost","prestofr","Pa55word#24");
		mysql_select_db("prestofr_stage",$con) or die(mysql_error());
		error_reporting(E_ALL && ~E_NOTICE);
//**************************************	 	

 
echo $query1 = "SELECT * FROM `mg_zipcode` WHERE `postcode` = '{$postcode}'";
 
$result = mysql_query($query1);
 
		if ( mysql_num_rows ( $result ) > 1 )
		{
		   echo '';
		}
		else{
	
		echo $query = "INSERT INTO mg_zipcode (postcode) VALUES ('".$_POST['postcode']."')";
		echo $execute = mysql_query($query) or die(mysql_error());
		}
     
		 }else{
			
			echo 'in array'; 
			}
//**************************************
		?>
</body>
</html>
