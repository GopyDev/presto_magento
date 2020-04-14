<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

	    $sel_number="select * from packinginfo where order_number='".$_GET["id"]."'";
		$rs_number=mysql_query($sel_number);
		$total_number=mysql_num_rows($rs_number);

		if($_GET["cf"]=='undefined')
		{
		  $_GET["cf"]="";
		}

		if($_GET["d"]=='undefined')
		{
		  $_GET["d"]="";
		}

		if($_GET["b"]=='undefined')
		{
		  $_GET["b"]="";
		}

		if($_GET["l"]=='undefined')
		{
		  $_GET["l"]="";
		}


		if($_GET["cold_bin"]=='undefined')
		{
		  $_GET["cold_bin"]="";
		}

		if($_GET["cold_bag"]=='undefined')
		{
		  $_GET["cold_bag"]="";
		}

		if($_GET["frozen_bag"]=='undefined')
		{
		  $_GET["frozen_bag"]="";
		}

		if($_GET["frozen_bin"]=='undefined')
		{
		  $_GET["frozen_bin"]="";
		}

		$_GET["d"]=str_replace("&","and",$_GET["d"]);

		if($total_number>=1)
		{
		    $update="update packinginfo set cf='".$_GET["cf"]."',d='".$_GET["d"]."',bag='".$_GET["b"]."',loose='".$_GET["l"]."',cold_bin='".$_GET["cold_bin"]."',cold_bag='".$_GET["cold_bag"]."',frozen_bag='".$_GET["frozen_bag"]."',frozen_bin='".$_GET["frozen_bin"]."' where order_number='".$_GET["id"]."'";
			mysql_query($update);
		}
		else
		{
		   $insert="insert into packinginfo set cf='".$_GET["cf"]."',d='".$_GET["d"]."',bag='".$_GET["b"]."',loose='".$_GET["l"]."' ,cold_bin='".$_GET["cold_bin"]."',cold_bag='".$_GET["cold_bag"]."',frozen_bag='".$_GET["frozen_bag"]."',frozen_bin='".$_GET["frozen_bin"]."',order_number='".$_GET["id"]."'";
			mysql_query($insert);
		}

mysql_close();
?>