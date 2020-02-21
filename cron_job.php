#!/usr/bin/php -q
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('DBhost', 'localhost');
define('DBuser', 'seorepor_imark');
define('DBPass', 'im@rk123#@');
define('DBname', 'seorepor_analytics');

global $DBcon;
	try {
		
		$DBcon = new PDO("mysql:host=".DBhost.";dbname=".DBname,DBuser,DBPass);
		
	} catch(PDOException $e){
		
		die($e->getMessage());
	}


include("includes/functions.php");
include("vendor/php/lib/GrabzItClient.class.php");
include('vendor/PHPMailer/PHPMailerAutoload.php');

function doMyThings() {	
	global $DBcon;
	$path 			= 	"assets/ajax/pdf/";
	$query			=	'SELECT * FROM `user_email_details` WHERE email_status = 1';
	$stmt			=	$DBcon->prepare($query);
	if($stmt->execute()) {
		$results	=	$stmt->fetchAll();
		if(!empty($results)) {
			foreach($results as $result) { 
				$to				= 	$result['email_to'];
				$subject	 	= 	($result['email_subject']);
				$message	 	= 	($result['email_message']);
				$from_email		= 	($result['email_sender']);
				$from_sender	= 	($result['email_sender_name']);
				$user_id		= 	$result['user_id'];	
				$attach_token	=	getShareKeyData($result['request_id']);
				$attchment_url  = 	'http://seoreports.me/seo_pdf.php?token='.$attach_token['token'];
				$parse 			= 	parse_url($attach_token['domain_url']);
				$url   			= 	parseUrl($parse['host']); // print		
				$file_name		=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';
				$grabzIt 		= 	new GrabzItClient("NjczYmJiNjc1ZjI0NDFhMDljN2VjM2ViMGVhMjI2N2Q=", "Pz8/WD9FPyE/Pj9sbDQ+Pz8/DBg/P1hASwQ/P1A/Pz8=");
				$options 		= 	new GrabzItPDFOptions();
				$options->setPageSize("A3");
				$options->setDelay(30000);
				$options->setRequestAs(0);
				$path			=	$grabzIt->URLToPDF($attchment_url, $options);
				//$path			=	$grabzIt->URLToImage($attchment_url, $options);
				//$filepath 		= 	"pdf/".$attach_token.".pdf";
				//$filepath 		= 	"pdf/result.jpg";
				$grabzIt->SaveTo($file_name); 	
				$email_status	=	sendEmail($from_email, $from_sender, $to, $file_name, $subject, $message, $url);				
				if($email_status['status'] == 'success'){
					$update_query		=	"UPDATE `user_email_details` SET email_status = 2 WHERE id =".$result['id'];
					$update_result		=	$DBcon->prepare($update_query);
					if($update_result->execute()){
						$response['status']	=	'success';
					} else {
						$response['status']	=	'error';
					}
				}
			}
		}
	}else{
		$stmt->errorinfo();
	}

}
/*
$start = microtime(true);
set_time_limit(60);
for ($i = 0; $i < 59; ++$i) {
    doMyThings();
    time_sleep_until($start + $i + 1);
}
	*/
    doMyThings();