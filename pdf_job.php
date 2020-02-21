#!/usr/bin/php -q
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DBhost', 'localhost');
define('DBuser', 'seorepor_imark');
define('DBPass', 'im@rk123#@');
define('DBname', 'seorepor_analytics');
define('DBname', 'seorepor_analytics');
define('FULL_PATH', 'https://'.$_SERVER['HTTP_HOST']."/");

global $DBcon;
	try {
		
		$DBcon = new PDO("mysql:host=".DBhost.";dbname=".DBname,DBuser,DBPass);
		
	} catch(PDOException $e){
		
		die($e->getMessage());
	} 


include("includes/functions.php");
include("vendor/php/lib/GrabzItClient.class.php");
	
function doMyThings() {	
	global $DBcon;
	
	$path 			= 	"assets/ajax/pdf/";
	$query			=	'SELECT * FROM `download_pdf_request` WHERE download_status = 0';
	$stmt			=	$DBcon->prepare($query);
	if($stmt->execute()) {
		$results	=	$stmt->fetchAll();
		if(!empty($results)) {
			foreach($results as $result) { 
				$user_id		= 	$result['user_id'];	
				$attach_token	=	getShareKeyData($result['request_id']);
				$attchment_url  = 	FULL_PATH.'seo_pdf.php?token='.$attach_token['token'];
				$parse 			= 	parse_url($attach_token['domain_url']);
				$url   			= 	parseUrl($parse['host']); // print		
				$file_name		=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';
				$grabzIt 		= 	new GrabzItClient("NjczYmJiNjc1ZjI0NDFhMDljN2VjM2ViMGVhMjI2N2Q=", "Pz8/WD9FPyE/Pj9sbDQ+Pz8/DBg/P1hASwQ/P1A/Pz8=");
				$options 		= 	new GrabzItPDFOptions();
				$options->setPageSize("A3");
				$options->setDelay(30000);
				$options->setRequestAs(0);
				$path			=	$grabzIt->URLToPDF($attchment_url, $options);
				$grabzIt->SaveTo($file_name); 	
				$update_query		=	"UPDATE `download_pdf_request` SET download_status = 1 WHERE id =".$result['id'];
				$update_result		=	$DBcon->prepare($update_query);
				if($update_result->execute()){
					$response['status']	=	'success';
				} else {
					$response['status']	=	'error';
				}
			}
		}
	}

}

doMyThings();	
print_r($response);