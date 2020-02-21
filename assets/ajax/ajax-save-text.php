<?php
header('Content-type: application/json');
require_once '../../includes/config.php';
require_once '../../vendor/PHPMailer/PHPMailerAutoload.php';
require_once("../../includes/functions.php");
require_once("../../vendor/php/lib/GrabzItClient.class.php");


if ($_REQUEST) {

	if($_REQUEST['action'] == 'email_data') {
		$request_id		= 	($_REQUEST['share_id']);
		$edit_section	= 	($_REQUEST['content_text']);
		$edit_area		= 	($_REQUEST['edit_section']);
		$user_id		= 	$_SESSION['user_id'];	
		if(!empty($edit_section)) {
			$select_query 	= 	"SELECT * FROM seo_analytics_edit_secion WHERE user_id=:user_id AND request_id=:request_id AND edit_area=:edit_area";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->bindParam(':edit_area', $edit_area);
			$select_stmt->execute();
			$results = $select_stmt->fetch();
			if(!empty($results)) {
				if(!empty($_REQUEST['note_heading'])) {
					$query = "UPDATE seo_analytics_edit_secion SET user_id = :user_id, request_id = :request_id,edit_section = :edit_section, note_heading = :note_heading WHERE id = :id ";
				} else {
					$query = "UPDATE seo_analytics_edit_secion SET user_id = :user_id, request_id = :request_id,edit_section = :edit_section WHERE id = :id ";
				}
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':edit_section', $edit_section);
				$stmt->bindParam(':id', $results['id']);
				if(!empty($_REQUEST['note_heading'])) {
					$stmt->bindParam(':note_heading', $_REQUEST['note_heading']);
				}
				if($stmt->execute()) {			
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error'; // could not register
				}	
			}else{
				if(!empty($_REQUEST['note_heading'])) {
					$query = "INSERT INTO seo_analytics_edit_secion(user_id,request_id,edit_section, edit_area, note_heading) VALUES(:user_id, :request_id, :edit_section, :edit_area, :note_heading)";
				} else {
					$query = "INSERT INTO seo_analytics_edit_secion(user_id,request_id,edit_section, edit_area) VALUES(:user_id, :request_id, :edit_section, :edit_area)";
				}
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':edit_section', $edit_section);
				$stmt->bindParam(':edit_area', $edit_area);
				if(!empty($_REQUEST['note_heading'])) {
					$stmt->bindParam(':note_heading', $_REQUEST['note_heading']);
				}
				if($stmt->execute()) {			
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error'; // could not register
				}	
			}
		}else{
			$response['status'] = 'error';
		}
	}else if($_REQUEST['action'] == 'remove_edit_data') {
		$request_id		= 	($_REQUEST['request_id']);
		$user_id		= 	$_SESSION['user_id'];	
		$delete_query 	= 	"DELETE FROM seo_analytics_edit_secion WHERE user_id=:user_id AND id=:request_id";
		$delete_stmt 	= 	$DBcon->prepare( $delete_query );
		$delete_stmt->bindParam(':user_id', $user_id);
		$delete_stmt->bindParam(':request_id', $request_id);
		if($delete_stmt->execute()) {
			$response['status'] = 'success';
		} else {
			$response['status'] = 'error'; // could not register
		}	
	}
			
}
	echo json_encode($response);exit;
	