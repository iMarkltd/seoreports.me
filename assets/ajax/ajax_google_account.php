<?php

	header('Content-type: application/json');

	require_once '../../includes/config.php';
	require_once 'api/semrush_api.php';
	require_once '../../vendor/autoload.php';
	require_once("../../includes/functions.php");
	
	$response = array();
	$client 			= new Google_Client();

	if ($_POST) {
		if($_POST['action'] == 'update_google_analytic_id') {
			$analytics_id	= 	trim($_POST['analytic_id']);
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	trim($_SESSION['user_id']);
			$query 			= 	"UPDATE semrush_users_account SET google_account_id = :analytics_id, google_analytics_id = '', google_property_id = '', google_profile_id = ''  WHERE user_id = :user_id AND id = :request_id";
			$stmt 			= 	$DBcon->prepare( $query );
			$stmt->bindParam(':analytics_id', $analytics_id);
			$stmt->bindParam(':request_id', $request_id);
			$stmt->bindParam(':user_id', $user_id);
			if($stmt->execute()) {			
				$query 	= 	"SELECT * FROM google_analytics_users WHERE id IN ($analytics_id) ";
				$stmt 	= 	$DBcon->prepare( $query );
				if($stmt->execute()) {			
					$results = $stmt->fetch();
					$refresh_token				 	= $results['google_refresh_token'];
					$service_token['access_token'] 	= $results['google_access_token'];
					$service_token['token_type'] 	= $results['token_type'];
					$service_token['expires_in']	= $results['expires_in'];
					$service_token['id_token'] 		= $results['id_token'];
					$service_token['created'] 		= $results['service_created']; 						
					$_tokenArray					= json_encode($service_token);
					
					$client->setAuthConfig('/home/imarkclients/public_html/seo-analytics/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
					$client->setAccessType('offline');
					
					$client->setAccessToken($_tokenArray);
					if ($client->isAccessTokenExpired()) {
						$client->refreshToken($refresh_token);
					}
					$analytics = new Google_Service_Analytics($client);
					// Get the first view (profile) id for the authorized user.
					$profile 		= getFirstProfileId($analytics);
					$property_id 	= getFirstPropertyId($analytics);
					if(!empty($property_id)) {
						$google_view_id		= 	trim($property_id);
						$new_query1 		= 	"UPDATE semrush_users_account SET google_profile_id = :google_view_id WHERE user_id = :user_id AND id = :request_id";
						$stmt1 				= 	$DBcon->prepare( $new_query1 );
						$stmt1->bindParam(':google_view_id', $google_view_id);
						$stmt1->bindParam(':request_id', $request_id);
						$stmt1->bindParam(':user_id', $user_id);
						$stmt1->execute(); 
					}
					$myToken	= 	$client->getAccessToken();
					$response['status'] = 'success';
					$response['analytic_id'] = $myToken['access_token'];
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}
			} else {
				$response['status'] = 'error'; // could not register
				$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
			}	
		}		
	}
	
	echo json_encode($response);
	