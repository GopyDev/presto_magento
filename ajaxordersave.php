<?php

    require_once( '_db.config.inc.php' );

//connection to the database
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD)
 or die("Unable to connect to MySQL");

//select a database to work with
$selected = mysql_select_db( DBDATABASE, $dbhandle)
  or die("Could not select examples");


			   $update = "update mg_catalog_product_entity_int set value='".$_GET["note"]."' where entity_id='".$_GET["id"]."' and attribute_id=253";
			  mysql_query($update);

			  /* $update2 = "update mg_catalog_product_flat_1 set price='".$price."' where entity_id='".$_GET["id"]."'";
              mysql_query($update2); */

	mysql_close();
?>