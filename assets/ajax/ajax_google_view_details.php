<?php

	require_once '../../includes/config.php';
	require_once 'api/semrush_api.php';
	require_once '../../vendor/autoload.php';
	require_once("../../includes/functions.php");
	
	$response = array();

	if ($_POST) {
		if($_POST['action'] == 'update_select_div') {
			$analytics_id	= 	trim($_POST['analytic_id']);
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	trim($_SESSION['user_id']);
			$li				=	'';
			if(!empty($analytics_id)) {
				//$details 		=	updateGoogleProfileData($request_id, $analytics_id);	
				$query 		=	"SELECT * FROM google_account_view_data WHERE google_account_id =:google_account_id AND user_id =:user_id AND parent_id =0";
				$stmt 		= 	$DBcon->prepare( $query );
				$stmt->bindParam('google_account_id', $analytics_id);
				$stmt->bindParam('user_id', $user_id);
				$stmt->execute();
				$results = $stmt->fetchAll();

				$li	=	'<option value=""><--Select Account--></option>';
				if(!empty($results)) {
					foreach($results as $result) {
						$li	.= '<option value="'.$result['id'].'">'.$result['category_name'].'</option>';
					} 
					
				}else{
					$li	.= '<option value="">No Result Found</option>';
				}
			}
			echo ($li); exit;

		}		
	}
	
?>

