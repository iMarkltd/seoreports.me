<?php
header('Content-type: application/json');
require_once '../../includes/config.php';
require_once '../../vendor/PHPMailer/PHPMailerAutoload.php';
require_once("../../includes/functions.php");
require_once("../../vendor/php/lib/GrabzItClient.class.php");


if ($_REQUEST) {

	if($_REQUEST['action'] == 'email_data') {
		$request_id		= 	trim($_REQUEST['share_id']);
		$user_id		= 	$_SESSION['user_id'];	

		$select_query 	= 	"SELECT * FROM user_email_details WHERE user_id=:user_id AND request_id=:request_id";
		$select_stmt = $DBcon->prepare( $select_query );
		$select_stmt->bindParam(':user_id', $user_id);
		$select_stmt->bindParam(':request_id', $request_id);
		$select_stmt->execute();
		$results = $select_stmt->fetch();
		if(!empty($results)) {
			$response['status'] = 'success';
			$response['subject'] = $results['email_subject'];
			$response['email_to'] = $results['email_to'];
			$response['email_message'] = $results['email_message'];
			$response['email_sender'] = $results['email_sender'];
			$response['email_sender_name'] = $results['email_sender_name'];
		}else{
			$response = '';
		}
	}
			
}
	echo json_encode($response);exit;
	