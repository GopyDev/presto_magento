<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");


	$price=round($_GET["note"],4);

			  $update = "update mg_catalog_product_entity_decimal set value='".$price."' where entity_id='".$_GET["id"]."' and attribute_id=75";
			  mysql_query($update);

			  $update2 = "update mg_catalog_product_flat_1 set price='".$price."' where entity_id='".$_GET["id"]."'";
              mysql_query($update2);

	mysql_close();
?>