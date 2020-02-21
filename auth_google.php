<?php
	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once 'vendor/autoload.php';
	global $DBcon;
	$client 			= 	new Google_Client();
	$googleAnalytics	=	googleAnalytics($_REQUEST['ids']);
