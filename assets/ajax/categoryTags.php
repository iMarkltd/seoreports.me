	<?php

	header('Content-type: application/json');

	require_once '../../includes/config.php';
	require_once '../../vendor/autoload.php';
	require_once("../../includes/functions.php");

	if ($_POST) {
		$response		=	array();
		if($_POST['action'] == 'save_tags') {
			$user_id	= $_SESSION['user_id'];	
			$category	= $_POST['task_report'];
			$query 		= "INSERT INTO activity_category(category_name,parent_id,user_id) VALUES(:category_name,:parent_id,:user_id)";
			$stmt 		= $DBcon->prepare( $query );
			$stmt->bindParam(':category_name', $category);
			$stmt->bindParam(':parent_id', $_POST['onsite']);
			$stmt->bindParam(':user_id', $user_id);
			if($stmt->execute()) {
				$response['status'] = '1'; // Insert Data Done
				$response['error'] = '0';
				$response['message'] = 'Data Insert Successfully';
			} else {
				$response['status'] = '2'; // could not register
				$response['error'] = '2';
				$response['message'] = 'Tags Empty';
			}
		} else if($_POST['action'] == 'save_status') {
			$user_id		= $_SESSION['user_id'];	
			$categories		= explode(',', $_POST['activity_status']);
				if(!empty($categories)) {
					foreach($categories as $category) {
						$query 	= "INSERT INTO activity_status(name,user_id) VALUES(:activity_status, :user_id)";
						$stmt = $DBcon->prepare( $query );
						$stmt->bindParam(':activity_status', $category);
						$stmt->bindParam(':user_id', $user_id);
						$stmt->execute();				
					}
					$response['status'] = '1'; // Insert Data Done
					$response['error'] = '0';
					$response['message'] = 'Data Insert Successfully';

				} else {
					$response['status'] = '2'; // could not register
					$response['error'] = '2';
					$response['message'] = 'Activity Status Empty';
				}

		} else if($_POST['action'] == 'remove_task') {
			$user_id		= 	$_SESSION['user_id'];	
			$tag			= 	$_REQUEST['tag'];
			$task_type		= 	$_REQUEST['task_type'];
			$existTeam		=	checkActivityTaskExist($tag, $task_type);
			if($existTeam){
				$query 			= 	"UPDATE activity_category SET status = 1  WHERE category_name = :category_name AND user_id = $user_id AND parent_id = $task_type "; 
				$stmt 			= 	$DBcon->prepare( $query );
				$stmt->bindParam(':category_name', $tag);
				if($stmt->execute()) {			
					$response['status'] = '1'; // Insert Data Done
					$response['error'] = '0';
					$response['message'] = 'Data delete Successfully';
				} else {
					$response['status'] = '2'; // Insert Data Done
					$response['error'] = '2';
					$response['message'] = 'Getting Error to delete data';

				}
			}else{
				$user_id		= 	$_SESSION['user_id'];	
				$delete_query	=	"DELETE FROM activity_category WHERE category_name = :category_name AND user_id = $user_id AND parent_id = $task_type "; 
				$delete_stmt	=	$DBcon->prepare($delete_query);
				$delete_stmt->bindParam(':category_name', $tag);
				if($delete_stmt->execute()) {			
					$response['status'] = '1'; // Insert Data Done
					$response['error'] = '0';
					$response['message'] = 'Data delete Successfully';
				} else {
					$response['status'] = '2'; // Insert Data Done
					$response['error'] = '2';
					$response['message'] = 'Getting Error to delete data';

				}
			}
		}
	}
	
	echo json_encode($response);
	