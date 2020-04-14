<?php

    require_once( '_db.config.inc.php' );

//connection to the database
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD)
 or die("Unable to connect to MySQL");

//select a database to work with
$selected = mysql_select_db( DBDATABASE, $dbhandle)
  or die("Could not select examples");

          $sel_data="select id from mg_shipping_delivery_window where window LIKE '%".$_GET["ordernumber"]."'";
		  $rs_data= mysql_query($sel_data);
		  $data=mysql_num_rows($rs_data);

		  echo $data;

mysql_close();
?>