<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

          $sel_sub="select email from mg_pickers where id='".$_GET["driver"]."'";
		  $rs_sub=mysql_query($sel_sub);
		  $sub=mysql_fetch_array($rs_sub);

	$update_orders="update mg_shipping_delivery_window set driver_email='".$sub["email"]."' where route='".$_GET["route"]."' and drivername='".$_GET["vname"]."'";
	mysql_query($update_orders);

	$update_route="update route_driver set driver_id='".$_GET["driver"]."',note='".$_GET["textnote"]."'  where route='".$_GET["route"]."' and rid='".$_GET["vid"]."'";
	mysql_query($update_route);

mysql_close();
?>