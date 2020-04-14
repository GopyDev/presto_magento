<?php
//DB details
$dbHost     = 'localhost';
$dbUsername = 'prestofr_produce';
$dbPassword = '2P*otXvXgUqE';
$dbName     = 'prestofr_sitedb';
$loginname = "presto";
$loginpwd = "Zagaras#16";
//Create connection and select DB
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if($db->connect_error){
    die("Unable to connect database: " . $db->connect_error);
}