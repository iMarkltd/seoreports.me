<?php

	require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
	global $DBcon;
	$user_id	=	$_SESSION['user_id'];
	$id			=	@$_POST['ids'];
	$path 		= 	"pdf/";
	
	if ($_POST) {
		if($_POST['action'] == 'remove_all_pdf') {
			$select_query 	= 	"SELECT * FROM download_pdf_request WHERE user_id=:user_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			
			if(!empty($results)) {
				foreach($results as $result ) {
					$attach_token	=	getShareKeyData($result['request_id']);
					$parse 			= 	parse_url($attach_token['domain_url']);
					$url   			= 	parseUrl($parse['host']); // print		
					$file_name		=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';
					if(file_exists($file_name))
						unlink($file_name);
				}
				$delete_query 	= 	"DELETE FROM download_pdf_request WHERE user_id=:user_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->execute();
				
			}
		} else if ($_POST['action'] == 'remove_pdf') {
			$select_query 	= 	"SELECT * FROM download_pdf_request WHERE id=:id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':id', $id);
			$select_stmt->execute();
			$results = $select_stmt->fetch();
			if(!empty($results)) {
				$attach_token	=	getShareKeyData($results['request_id']);
				$parse 			= 	parse_url($attach_token['domain_url']);
				$url   			= 	parseUrl($parse['host']); // print		
				$file_name		=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';
				if(file_exists($file_name))
					unlink($file_name);
				$delete_query 	= 	"DELETE FROM download_pdf_request WHERE id=:id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':id', $id);
				$delete_stmt->execute();
			}
		}
	}	
	$result['result']	=	'success';
	echo json_encode($result); exit;
?>

