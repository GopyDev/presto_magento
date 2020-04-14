<?php
    require_once( '_db.config.inc.php' );
    $dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL"); //connection to the database
    $selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples"); //select a database to work with



     if($_GET["comp"]=="0")
	 {
	 $query = "update mg_sales_flat_order set rcomplete=0,ramount='0' where increment_id='".$_GET["ordernumber"]."'";
	 mysql_query($query);
	  }
	  else if($_GET["comp"]=="1")
	  {
	  $query = "update mg_sales_flat_order set rcomplete=1,ramount='".$_GET["tpm"]."' where increment_id='".$_GET["ordernumber"]."'";
	  mysql_query($query);
	  }

mysql_close();
?>