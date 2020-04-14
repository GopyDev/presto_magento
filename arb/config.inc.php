<?php
    /*define('AUTHNET_LOGIN', '929XSWNfnT');
    define('AUTHNET_TRANSKEY', '2kS6z37Uzn9J4M8z');*/
	
	 define('AUTHNET_LOGIN', '85z7aV7XB');  //LIVE MOD
    define('AUTHNET_TRANSKEY', '3bWR6zZ8hx9h34zs'); //LIVE MOD

    if (!function_exists('curl_init'))
    {
        throw new Exception('CURL PHP extension not installed');
    }

    if (!function_exists('simplexml_load_file'))
    {
        throw new Exception('SimpleXML PHP extension not installed');
    }

?>