<?php
require_once( '_db.config.inc.php' );
$dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL");
$selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples");

	    $sel_number="select * from chatfeatures where email='".$_GET["email"]."' and flag=0 order by id desc limit 0,1";
		$rs_number=mysql_query($sel_number);
		$total_number=mysql_num_rows($rs_number);

		if($total_number>=1)
		{
		    $data=mysql_fetch_array($rs_number);
			echo $data["message"];
		}


mysql_close();
?>