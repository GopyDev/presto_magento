<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

	    $sel_number="select * from brochure where order_number='".$_GET["oid"]."'";
		$rs_number=mysql_query($sel_number);
		$total_number=mysql_num_rows($rs_number);

		if($total_number>=1)
		{
		    $update="update brochure set bt='1' where order_number='".$_GET["oid"]."'";
			mysql_query($update);
		}
		else
		{
		    $insert="insert into brochure set  bt='1',order_number='".$_GET["oid"]."'";
			mysql_query($insert);
		}

mysql_close();
?>