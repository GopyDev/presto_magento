<?php
    require_once( '_db.config.inc.php' );
    $dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL"); //connection to the database
    $selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples"); //select a database to work with


	      $query = "update mg_shipping_delivery_window set drivermistake='".$_GET["dm"]."' where order_number='".$_GET["ordernumber"]."'";
		  mysql_query($query);

mysql_close();
?>