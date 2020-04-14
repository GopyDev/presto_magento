<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

	      $query = "update `route_driver` set note='".$_GET["note"]."' where rid='".$_GET["rid"]."' and route='".$_GET["route"]."'";
		  mysql_query($query);

mysql_close();
?>