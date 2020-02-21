<?php
ob_start();
session_start();
unset($_SESSION["user"]);
unset($_SESSION["user_name"]);
$_SESSION = array();
session_destroy();
session_unset();
header ("Location: index.php");	exit;
?>
