<?php
	require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
	global $DBcon;

	$action		=	$_POST['action'];
	$id 		=	@$_POST['property_id'];

	if(!empty($action)) {
		if($action == 'property_data' && !empty($id)) {
			$query 		=	"SELECT * FROM google_account_view_data WHERE parent_id =:parent_id";
			$stmt 		= 	$DBcon->prepare( $query );
			$stmt->bindParam('parent_id', $id);
			$stmt->execute();
			$results = $stmt->fetchAll();

			$li	=	'<option value=""><--Select Property--></option>';
			if(!empty($results)) {
				foreach($results as $result) {
					$li	.= '<option value="'.$result['id'].'">'.$result['category_name'].'</option>';
				} 
			}else{
				$li	.= '<option value="">No Result Found</option>';
			}
			echo ($li); exit;
		} else if($action == 'property_view_data' && !empty($id)) {
			$query 		=	"SELECT * FROM google_account_view_data WHERE parent_id =:parent_id";
			$stmt 		= 	$DBcon->prepare( $query );
			$stmt->bindParam('parent_id', $id);
			$stmt->execute();
			$results = $stmt->fetchAll();

			$li	=	'<option value=""><--Select view id--></option>';
			if(!empty($results)) {
				foreach($results as $result) {
					$li	.= '<option value="'.$result['category_id'].'">'.$result['category_name'].'</option>';
				} 
			}else{
				$li	.= '<option value="">No Result Found</option>';
			}
			echo ($li); exit;

		} else if($action == 'save_property_id' && !empty($_REQUEST['analytic_view_id'])) {
			$google_view_id			= 	trim($_POST['analytic_view_id']);
			$google_account_id		= 	trim($_POST['google_account_id']);
			$google_property_id		= 	trim($_POST['analytic_property_id']);
			$google_analytics_id	= 	trim($_POST['analytic_account_id']);
			$request_id				= 	trim($_POST['request_id']);
			$user_id				= 	trim($_SESSION['user_id']);
			$query 					= 	"UPDATE semrush_users_account SET google_account_id = :google_account_id, google_profile_id = :google_view_id, google_analytics_id = :google_analytics_id, google_property_id = :google_property_id WHERE user_id = :user_id AND id = :request_id";
			$stmt 			= 	$DBcon->prepare( $query );
			$stmt->bindParam(':google_analytics_id', $google_analytics_id);
			$stmt->bindParam(':google_property_id', $google_property_id);
			$stmt->bindParam(':google_account_id', $google_account_id);
			$stmt->bindParam(':google_view_id', $google_view_id);
			$stmt->bindParam(':request_id', $request_id);
			$stmt->bindParam(':user_id', $user_id);
			if($stmt->execute()) {			
				$response['status'] = 'success';
			} else {
				$response['status'] = 'error'; // could not register
			}				
			echo json_encode($response); exit;
		}

	}else {
			$li	=	'<option value=""><--Select Property--></option>';
	}

?>
