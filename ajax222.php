<?php
    require_once( '_db.config.inc.php' );
    $dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL"); //connection to the database
    $selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples"); //select a database to work with


     if($_GET["note"]=="")
	 {
	      $query = "update `supervision` set substitute='".$_GET["note"]."',log=3 where idstring='".$_GET["idstring"]."'";
		  mysql_query($query);

	 }
	 else
	 {
	    $query = "update `supervision` set substitute='".$_GET["note"]."',log=1 where idstring='".$_GET["idstring"]."'";
		  mysql_query($query);
	 }
mysql_close();
?>