<?php

require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");


     $sel_ss="select item_id from supervision where item_id='".$_GET["itemid"]."'";
	 $rs_ss=mysql_query($sel_ss);
	 $ss=mysql_num_rows($rs_ss);


     if($ss>=1)
	 {
	     $query = "update `supervision` set `idstring` = '".$_GET["idstring"]."',infopicker='".$_GET["infopicker"]."',itemqty='".$_GET["itemqty"]."',item_id='".$_GET["itemid"]."',cust='".$_GET["cust"]."',note='".$_GET["note"]."',ddate='".$_GET["ddate"]."',status='not found',got_qty='".$_GET["gqty"]."' where item_id='".$_GET["itemid"]."' ";
		  mysql_query($query);
	  }
	  else
	  {
	       $query = "insert into `supervision` set `idstring` = '".$_GET["idstring"]."',infopicker='".$_GET["infopicker"]."',itemqty='".$_GET["itemqty"]."',item_id='".$_GET["itemid"]."',cust='".$_GET["cust"]."',note='".$_GET["note"]."',ddate='".$_GET["ddate"]."',status='not found',got_qty='".$_GET["gqty"]."'";
		  mysql_query($query);
	  }

mysql_close();
?>