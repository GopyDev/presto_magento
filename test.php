<?php
    require_once( 'wwvkey.php' );
    require_once( '_db.config.inc.php' );
/*    if(LIVE_MODE == "Y")
        $key = WWVLIVEKEY;
    else
	$key = WWVTESTKEY;*/
    if(DBDATABASE == "devprest_mage")
        $key = "Dev server";
    else 
	$key = "LIVE serve";
    echo $key."<br>";
?>