<?php
	    require_once( '_db.config.inc.php' );
    $dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL"); //connection to the database
    $selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples"); //select a database to work with



			  $update = "update mg_catalog_product_entity_int set value='".$_GET["note"]."' where entity_id='".$_GET["id"]."' and attribute_id=96";
			  mysql_query($update);

			  /* $update2 = "update mg_catalog_product_flat_1 set price='".$price."' where entity_id='".$_GET["id"]."'";
              mysql_query($update2); */

	mysql_close();
?>