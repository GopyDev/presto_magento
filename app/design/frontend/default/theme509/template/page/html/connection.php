<?php
	  require_once( Mage::getBaseDir('base').'/_db.config.inc.php' );
        $con = mysql_connect(DBHOSTNAME, DBUSERNAME, DBPASSWORD);
        $connection = mysql_select_db( DBDATABASE, $con ) or die(mysql_error());
	  if (!$connection) {
		die('Could not connect: ' . mysql_error());
		}else{
			//echo 'sucess';
			}
?>