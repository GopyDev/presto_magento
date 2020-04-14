<?php
    require_once( '_db.config.inc.php' );
    $dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL"); //connection to the database
    $selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples"); //select a database to work with



     if($_GET["note"]!="")
	 {
	      $query = "insert into `supervision` set `idstring` = '".$_GET["idstring"]."',infopicker='".$_GET["infopicker"]."',itemqty='".$_GET["itemqty"]."',item_id='".$_GET["itemid"]."',cust='".$_GET["cust"]."',note='".$_GET["note"]."',ddate='".$_GET["ddate"]."',status='found' ";
		  mysql_query($query);
	 }
	 else
	 {
	     $query = "insert into `supervision` set `idstring` = '".$_GET["idstring"]."',infopicker='".$_GET["infopicker"]."',itemqty='".$_GET["itemqty"]."',item_id='".$_GET["itemid"]."',cust='".$_GET["cust"]."',ddate='".$_GET["ddate"]."',status='found' ";
		  mysql_query($query);
	 }
mysql_close();
?>