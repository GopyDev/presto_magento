<?php
require_once( Mage::getBaseDir('base').'/_db.config.inc.php' );
$con = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
$selected = mysql_select_db( DBDATABASE, $con ) or die(mysql_error());

          $sel_data="select id from mg_shipping_delivery_window where window LIKE '%".$_GET["ordernumber"]."'";
		  $rs_data= mysql_query($sel_data);
		  $data=mysql_num_rows($rs_data);

		  echo $data;

mysql_close();
?>