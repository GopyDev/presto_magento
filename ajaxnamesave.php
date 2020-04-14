<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

			  $update = "update mg_catalog_product_entity_varchar set value='".$_GET["note"]."' where entity_id='".$_GET["id"]."' and attribute_id=71";
			  mysql_query($update);

			  $update2 = "update mg_catalog_product_flat_1 set name='".$_GET["note"]."' where entity_id='".$_GET["id"]."'";
              mysql_query($update2);

			  $update3 = "update mg_catalog_product_entity_varchar set value='".$_GET["note"]."' where entity_id='".$_GET["id"]."' and attribute_id=71";
			  mysql_query($update3);

	mysql_close();
?>