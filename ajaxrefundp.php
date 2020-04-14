<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

	      $query = "update mg_sales_flat_order_item set txtref='".$_GET["txtref"]."',selref='".$_GET["selref"]."' where item_id='".$_GET["itemid"]."'";
		  mysql_query($query);

mysql_close();
?>