<?php

	header('Content-type: application/json');

	require_once('../../includes/config.php');
	require_once('api/semrush_api.php');
	require_once('../../vendor/autoload.php');
	require_once("../../includes/functions.php");

	global $DBcon;
	$active_task_links	=	'';
	if($_POST){
		if($_POST['action'] == 'activity_task' ){
			$user_id		= 	$_SESSION['user_id'];	
			$request_id		=	$_POST['request_id'];	
			foreach($_POST['activity_date'] as $key=>$value){
				$activity_date 	=	date('Y-m-d', strtotime($value));				
				$query = "INSERT INTO activities_product(user_id,request_id,activity_type,activity_task, activity_hour, activity_date, activity_status, activity_desc) VALUES(:user_id, :request_id, :activity_type, :activity_task, :activity_hour, :activity_date, :activity_status, :activity_desc)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':activity_type', $_POST['activity_type'][$key]);
				$stmt->bindParam(':activity_task', $_POST['activity_task'][$key]);
				$stmt->bindParam(':activity_hour', $_POST['task_hours'][$key]);
				$stmt->bindParam(':activity_date', $activity_date);
				$stmt->bindParam(':activity_status', $_POST['activity_status'][$key]);
				$stmt->bindParam(':activity_desc', $_POST['desc'][$key]);
				if ( $stmt->execute() ) {
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; Activity Added sucessfully, you need to refresh the page';
				}else{
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, please contact to developer ';
				}
			}
		}else if($_POST['action'] == 'remove_activity'){
			$id	 	= 	trim($_POST['share_id']);
			$query 	= 	"DELETE FROM activities_product WHERE id IN ($id) ";
			$stmt 	= 	$DBcon->prepare( $query );
			if($stmt->execute()) {			
				$response['status'] = 'success';
				$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; Record delete sucessfully ';
			} else {
				$response['status'] = 'error'; // could not register
				$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
			}	
			

		}
	}

	
	echo json_encode($response);
