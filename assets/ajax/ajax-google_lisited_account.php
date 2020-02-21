<?php
	error_reporting(0);
	require_once '../../includes/config.php';
	require_once 'api/semrush_api.php';
	require_once '../../vendor/autoload.php';
	require_once("../../includes/functions.php");
	global $DBcon;
	if ($_POST) {
		if($_POST['action'] == 'update_google_account') {
			$analytics_id	= 	trim($_POST['request_id']);
			$user_id		= 	trim($_SESSION['user_id']);
			if(!empty($analytics_id)) {
				$details 		=	updateGoogleAccountData($analytics_id);	
				$response['status'] = 'success';
			}else{
				$response['status'] = 'error'; // could not register
			}

		}		
	}
echo json_encode($response); exit;
	
?>
