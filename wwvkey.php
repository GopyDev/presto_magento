<?php
    require_once( '_db.config.inc.php' );

    if(DBDATABASE == "devprest_mage")
	define( 'LIVE_MODE', "N" );
    else
	define( 'LIVE_MODE', "Y" );

    if( !defined('WWVLIVEKEY') )
        define( 'WWVLIVEKEY', "e8712eea-32c3-4536-83fd-91fa0c82698c" );
    if( !defined('WWVTESTKEY') )
        define( 'WWVTESTKEY', "da918659-2ba1-4c2a-aef9-0de7f1c2c6b5" );


?>