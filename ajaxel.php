<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");


     if($_GET["refund"]=="0")
	 {
	      $query = "update mg_shipping_delivery_window set exclude_lables=0 where order_number='".$_GET["itemid"]."'";
		  mysql_query($query);
		  echo "0";
	  }
	  else if($_GET["refund"]=="1")
	  {
	       $query = "update mg_shipping_delivery_window set exclude_lables=1 where order_number='".$_GET["itemid"]."'";
		  mysql_query($query);
		  echo "1";
	  }

mysql_close();
?>