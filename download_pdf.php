<?php
	error_reporting(0);
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	
	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("vendor/php/lib/GrabzItClient.class.php");
	
	global $DBcon;
	$action		=	$_POST['action'];
	$ids		=	$_POST['ids'];
	$path 		= 	"assets/ajax/pdf/";
	
	if(!empty($action)) {
		if($action == 'seo_view_pdf') {
			$attach_token	=	getUserDomainDetailsByToken($ids);
			$attchment_url  = 	FULL_PATH.'seo_pdf.php?token='.$ids;
		}elseif($action == 'seo_analytics_pdf') {
			$attach_token	=	getShareKeyData($ids);
			$attchment_url  = 	FULL_PATH.'seo_pdf.php?token='.$attach_token['token'];

		}
		$parse = parse_url($attach_token['domain_url']);
		$url   = parseUrl($parse['host']); // print		
		$file_name	=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';
		$grabzIt 		= 	new GrabzItClient("NjczYmJiNjc1ZjI0NDFhMDljN2VjM2ViMGVhMjI2N2Q=", "Pz8/WD9FPyE/Pj9sbDQ+Pz8/DBg/P1hASwQ/P1A/Pz8=");
		$options = new GrabzItPDFOptions();
		$options->setPageSize("A3");
		$options->setDelay(30000);
		$options->setRequestAs(0);
		$path			=	$grabzIt->URLToPDF($attchment_url, $options);
		//$path			=	$grabzIt->URLToImage($attchment_url, $options);
		//$filepath 		= 	"pdf/result.jpg";
		$grabzIt->SaveTo($file_name); 	
		$result['status']	=	'success';
		$result['file_path']	=	$file_name;
		$result['file_name']	=	'SEO PERFORMANCE REPORT - '.$url.'.pdf';
		echo json_encode($result); exit;
	
	}
	
