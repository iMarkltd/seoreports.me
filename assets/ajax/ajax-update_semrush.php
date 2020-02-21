<?php
header('Content-type: application/json');
require_once '../../includes/config.php';
require_once '../../vendor/PHPMailer/PHPMailerAutoload.php';
require_once("../../includes/functions.php");
require_once("../../vendor/php/lib/GrabzItClient.class.php");


if ($_REQUEST) {

	if($_REQUEST['action'] == 'update_semerush_data') {
		$user_id	 	= 	trim($_POST['request_id']);
		$domain_name 	= 	trim($_POST['input_id']);
		$query 			= 	"DELETE FROM semrush_organic_search_data WHERE user_id = $user_id AND domain_name = '$domain_name' ";
		$stmt 			= 	$DBcon->prepare( $query );
		if($stmt->execute()) {			
			$backlink_query	= 	"DELETE FROM semrush_backlinks_data WHERE user_id = $user_id AND domain_name = '$domain_name' ";
			$backlink_stmt	= 	$DBcon->prepare( $backlink_query );
			if($stmt->execute()) {			
				$response['status'] = 'success';
			}else{
				print_r($stmt->errorInfo()); 
				$response['status'] = 'error'; // could not register
			}
		} else {
			print_r($stmt->errorInfo()); 
			$response['status'] = 'error'; // could not register
		}	

	}
			
}
	echo json_encode($response);exit;
	