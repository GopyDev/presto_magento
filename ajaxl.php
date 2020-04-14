<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");
	      $query = "update `mg_shipping_delivery_window` set `dryload` = 1 where id='".$_GET["id"]."'";
		  mysql_query($query);

		  $sel_id="select id from mg_shipping_delivery_window where `dryload` = 1 and `coldload` = 1 and id='".$_GET["id"]."'";
		  $rs_query=mysql_query($sel_id);
		  $query=mysql_fetch_array($rs_query);

		  if($query["id"]>1)
		  {
		      $query = "update `mg_shipping_delivery_window` set `loaded` = 1 where id='".$_GET["id"]."'";
		      mysql_query($query);
			  echo "1";
		  }
mysql_close();
?>