<?php
    require_once( '_db.config.inc.php' );
    $dbhandle = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD) or die("Unable to connect to MySQL"); //connection to the database
    $selected = mysql_select_db( DBDATABASE, $dbhandle) or die("Could not select examples"); //select a database to work with



        $sel_number="SELECT item_id FROM supervision where item_id='".$_GET["itemid"]."' and ddate='".$_GET["ddate"]."'";
		$rs_number=mysql_query($sel_number);
		$total_number=mysql_num_rows($rs_number);



     if($total_number>=1)
	 {
	      $query = "update `supervision` set `log2` = 1 where item_id='".$_GET["itemid"]."' and ddate='".$_GET["ddate"]."'";
		  mysql_query($query);
	  }
	  else
	  {
	      $query = "insert into `supervision` set `idstring` = '".$_GET["idstring"]."',infopicker='".$_GET["infopicker"]."',itemqty='".$_GET["itemqty"]."',item_id='".$_GET["itemid"]."',note='".$_GET["note"]."',ddate='".$_GET["ddate"]."'`log2` = 1 ";
		  mysql_query($query);
	  }

mysql_close();
?>