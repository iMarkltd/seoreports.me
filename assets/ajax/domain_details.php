<?php
	header('Content-type: application/json');

	require_once '../../includes/config.php';
	global $DBcon;
	
	$response = array();

	if ($_POST) {
		
		if($_POST['action'] == 'edit') {
			$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id AND id=:domain_id";
			$stmt = $DBcon->prepare( $query );
			$stmt->bindParam(':user_id', $_SESSION['user_id']);
			$stmt->bindParam(':domain_id', $_POST['edit']);
			$stmt->execute();
			$results = $stmt->fetch();
		}
	}
	$response['user_id']		=	$results['user_id'];
	$response['domain_name']	=	$results['domain_name'];
	$response['domain_url']		=	$results['domain_url'];
	//$arr_response	=	 $results;
	echo json_encode($response); exit;
	
?>	
