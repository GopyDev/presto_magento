<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

     $sel_ss="select id from general_note where dodate='".$_GET["dodate"]."'";
	 $rs_ss=mysql_query($sel_ss);
	 $ss=mysql_num_rows($rs_ss);

     if($ss>=1)
	 {
	      $query = "update general_note set note='".$_GET["gnote"]."' where dodate='".$_GET["dodate"]."'";
		  mysql_query($query);
	  }
	  else
	  {
	       $query = "insert into `general_note` set `note` = '".$_GET["gnote"]."',dodate='".$_GET["dodate"]."'";
		   mysql_query($query);
	  }

mysql_close();
?>