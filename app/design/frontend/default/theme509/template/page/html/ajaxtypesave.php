<?php
	require_once( Mage::getBaseDir('base').'/_db.config.inc.php' );
    $con = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
    $selected = mysql_select_db( DBDATABASE, $con ) or die(mysql_error());


			  $update = "update mg_catalog_product_entity_int set value='".$_GET["note"]."' where entity_id='".$_GET["id"]."' and attribute_id=251";
			  mysql_query($update);

			  /* $update2 = "update mg_catalog_product_flat_1 set price='".$price."' where entity_id='".$_GET["id"]."'";
              mysql_query($update2); */

	mysql_close();
?>