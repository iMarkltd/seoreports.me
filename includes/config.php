<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();
define('DBhost', 'localhost');
define('DBuser', 'root');
define('DBPass', 'im@rk123#@');
define('DBname', 'seoreport');

///home/imarkclients/public_html/seo-analytics
define('PATH', getcwd());

define('ABS_PATH', $_SERVER["DOCUMENT_ROOT"]);

// http://imarkclients.com/seo-analytics
define('FULL_PATH', 'https://'.$_SERVER['HTTP_HOST']."/");

//http://imarkclients.com/seo-analytics/index.php
define('PAGE_PATH', 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);

//http://imarkclients.com/seo-analytics/index.php
define('PAGE_NAME', basename($_SERVER['PHP_SELF']));

// /home/imarkclients/public_html/seo-analytics/assets/
define('ASSETS', $_SERVER['DOCUMENT_ROOT'] . '/assets/');

//show path with folder name //
///home/imarkclients/public_html/seo-analytics/includes 
define('REAL_PATH', realpath(dirname(__FILE__)));

global $DBcon;
	try {
		
		$DBcon = new PDO("mysql:host=".DBhost.";dbname=".DBname,DBuser,DBPass);
		return $DBcon;
		
	} catch(PDOException $e){
		
		die($e->getMessage());
	}