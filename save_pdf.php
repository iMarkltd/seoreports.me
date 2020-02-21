<?php
	ini_set('memory_limit', '-1');
	
	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("vendor/php/lib/GrabzItClient.class.php");
	
	global $DBcon;
	$action		=	$_POST['action'];
	$ids		=	$_POST['ids'];
	$path 		= 	"assets/ajax/pdf/";
	
	if(!empty($action)) {
		if($action == 'seo_analytics_pdf') {
			$attach_token	=	getShareKeyData($ids);
			$user_id		=	$attach_token['user_id'];
			$request_id		=	$attach_token['id'];
			$select_query 	= 	"SELECT * FROM download_pdf_request WHERE user_id=:user_id AND request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			if(empty($results)) {
				$query = "INSERT INTO download_pdf_request(user_id,request_id) 
							VALUES(:user_id, :request_id)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				if($stmt->execute()) {			
					$response['status'] 	= 	'success';
					$response['recent_li']	= 	getPDFDownloadList($type='limit');
				} else {
					$response['status'] = 'error'; // could not register
				}	
			}else{
				$delete_query 	= 	"DELETE FROM download_pdf_request WHERE user_id=:user_id AND request_id=:request_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				if($delete_stmt->execute()) {
					$query = "INSERT INTO download_pdf_request(user_id,request_id) 
								VALUES(:user_id, :request_id)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':request_id', $request_id);
					$stmt->bindParam(':user_id', $user_id);
					if($stmt->execute()) {			
						$response['status'] 	= 	'success';
						$response['recent_li']	= 	getPDFDownloadList($type='limit');

					} else {
						$response['status'] = 'error'; // could not register
					}	
				}
			}

		}
	
	}
	
echo json_encode($response); exit;
	
