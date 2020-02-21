<?php
	ini_set('memory_limit', '-1');
	
	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("vendor/php/lib/GrabzItClient.class.php");
	
	global $DBcon;
	$action		=	'seo_analytics_pdf';
	$path 		= 	"assets/ajax/pdf/";
	$variable 	= 	$_REQUEST['ids'];
	$base64		=	base64_decode($variable);	
	$base 		=	explode("@dmin1@1", $base64);
	if(!empty($action)) {
		$attach_token	=	getShareKeyData($base[1]);
		$attchment_url  = 	'https://seoreports.me/seo_pdf.php?token='.$attach_token['token'];
		$parse 			= 	parse_url($attach_token['domain_url']);
		$url   			= 	parseUrl($parse['host']); // print		
		$file_name		=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';
		$checkPDFExists	=	checkPDFExists($file_name);
		echo $checkPDFExists;
		if($checkPDFExists === false) {
			$grabzIt 		= 	new GrabzItClient("NjczYmJiNjc1ZjI0NDFhMDljN2VjM2ViMGVhMjI2N2Q=", "Pz8/WD9FPyE/Pj9sbDQ+Pz8/DBg/P1hASwQ/P1A/Pz8=");
			$options = new GrabzItPDFOptions();
			$options->setPageSize("A3");
			$options->setDelay(30000);
			$options->setRequestAs(0);
			$path			=	$grabzIt->URLToPDF($attchment_url, $options);
			//$path			=	$grabzIt->URLToImage($attchment_url, $options);
			//$filepath 		= 	"pdf/result.jpg";
			$grabzIt->SaveTo($file_name); 	
		}
		$result['status']	=	'success';
		$result['file_path']	=	$file_name;
		$result['file_name']	=	'SEO PERFORMANCE REPORT - '.$url.'.pdf';
		$status		=	'1';
		$query		=	"UPDATE download_pdf_request SET download_status = :status WHERE request_id = :register_id AND user_id = :user_id";
		$statement 	= 	$DBcon->prepare($query);
		$statement->bindValue(":status", $status);
		$statement->bindValue(":register_id", $attach_token['id']);
		$statement->bindValue(":user_id", $_SESSION['user_id']);
		$statement->execute();
		//print_r($attach_token); exit;
    	//print_r($statement->errorInfo()); exit;
		header("Content-Type: application/octet-stream");
		$file = $file_name;
		header("Content-Disposition: attachment; filename=" . ($result['file_name']));   
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");            
		header("Content-Length: " . filesize($file));
		flush(); // this doesn't really matter.
		ob_clean();
		flush();
		readfile($file);
		exit;

	}
