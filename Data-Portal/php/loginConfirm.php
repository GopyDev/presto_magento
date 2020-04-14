<?php
session_start();
$_SESSION['incorrect-login'] = null;
//include database configuration file
include 'dbConfig.php';
if ($_REQUEST['text-login1'] != $loginname) {
    $_SESSION['incorrect-login'] = true;
    header('Location: ../index.php');
} else if($_REQUEST['text-login2'] != $loginpwd) {
    $_SESSION['incorrect-login'] = true;
    header('Location: ../index.php');
}

else{
    $_SESSION['username'] = $_REQUEST['text-login1'];
    header('Location: ../main.php');
}
?>