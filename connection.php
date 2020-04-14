<?php 
	  //$con = mysql_connect('localhost','prestofr_stage','GiAZ8sc7gpJS');
      //$connection = mysql_select_db('prestofr_stage',$con) or die(mysql_error());	
      $con = mysql_connect('localhost','prestofr_produce','2P*otXvXgUqE');
	  $connection = mysql_select_db('prestofr_sitedb',$con) or die(mysql_error());	
	  if (!$connection) {
		die('Could not connect: ' . mysql_error());
		}else{
			//echo 'sucess';
			}	 
?>