<?php

	header('Content-type: application/json');

	require_once '../../includes/config.php';
	require_once 'api/semrush_api.php';
	require_once '../../vendor/autoload.php';
	require_once("../../includes/functions.php");
	
	$response = array();
	$client 			= new Google_Client();

	if ($_POST) { 
		if($_POST['action'] == 'location') {
			$name 			= trim($_POST['domain_name']);
			$url 			= trim($_POST['domain_url']);
			$regional_db 	= trim($_POST['regional_db']);
			$user_id		= $_SESSION['user_id'];	
			$token 			= bin2hex(openssl_random_pseudo_bytes(16));			
			$url_info 		= parse_url($url);
			$checkDomainName	=	checkDomainNameExists($url_info['host']);
			if(empty($checkDomainName)){			
				$query = "INSERT INTO semrush_users_account(domain_name,domain_url,regional_db,user_id, token) VALUES(:domain_name, :domain_url, :regional_db, :user_id, :token_id)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':domain_name', $name);
				$stmt->bindParam(':domain_url', $url);
				$stmt->bindParam(':regional_db', $regional_db);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':token_id', $token);
				
				// check for successfull registration
				if ( $stmt->execute() ) {
					/*
					$s 		= 	new SemRush($_REQUEST['domain_url']);
					$data	=	($s->getOrganicKeywordsReport());
					foreach($data as $record) {			
						$query = "INSERT INTO 
								  semrush_organic_search_data(user_id,keywords,position,previous_position,position_difference,search_volume,cpc,url,traffic,traffic_cost,competition,number_results,trends) 
								  VALUES(:user_id, :keywords, :position, :previous_position, :position_difference, :search_volume, :cpc, :url, :traffic, :traffic_cost, :competition, :number_results, :trends)";
						$stmt = $DBcon->prepare( $query );
						$stmt->bindParam(':user_id', $user_id);
						$stmt->bindParam(':keywords', $record['keyword']);
						$stmt->bindParam(':position', $record['pos']);
						$stmt->bindParam(':previous_position', $record['position_difference']+$record['pos']);
						$stmt->bindParam(':position_difference', $record['position_difference']);
						$stmt->bindParam(':cpc', $record['position_difference']);
						$stmt->bindParam(':url', $record['position_difference']);
						$stmt->bindParam(':traffic', $record['position_difference']);
						$stmt->bindParam(':traffic_cost', $record['traffic_percent']);
						$stmt->bindParam(':competition', $record['position_difference']);
						$stmt->bindParam(':number_results', $record['position_difference']);
						$stmt->bindParam(':trends', $record['position_difference']);
					}
					*/
					$insertId	=	$DBcon->lastInsertId();
					addCustomNote($insertId);

					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; registered sucessfully, your domain add now';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
			}else{
				$response['status'] = '2'; // could not register
				$response['error'] = '2';
				$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp;Domain name already exists';
			}
			//print_r($stmt->errorInfo()); exit;
		} else if($_POST['action'] == 'update') {

				$name 			= trim($_POST['domain_name']);
				$url 			= trim($_POST['domain_url']);
				$regional_db 	= trim($_POST['regional_db']);
				$id			 	= trim($_POST['ids']);
				$user_id		= $_SESSION['user_id'];	
				
				
				$query = "UPDATE semrush_users_account SET domain_name = :domain_name, domain_url = :domain_url,regional_db = :regional_db WHERE id = :id ";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':domain_name', $name);
				$stmt->bindParam(':domain_url', $url);
				$stmt->bindParam(':regional_db', $regional_db);
				$stmt->bindParam(':id', $id);
				$stmt->execute();				
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; update sucessfully, your domain update now';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
			//print_r($stmt->errorInfo());
		} else if($_POST['action'] == 'row_edit') {

				$name 			= trim($_POST['domain_name']);
				$id			 	= trim($_POST['ids']);
				
				
				$query = "UPDATE semrush_users_account SET domain_name = :domain_name WHERE id = :id ";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':domain_name', $name);
				$stmt->bindParam(':id', $id);
				$stmt->execute();				
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; update sucessfully, your domain update now';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
				
			//print_r($stmt->errorInfo());
				
		} else if($_POST['action'] == 'archive') {

				$id			 	= trim($_POST['ids']);
				$status		 	= '1';
				$query = "UPDATE semrush_users_account SET status = :status WHERE id = :id ";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':id', $id);
				$stmt->bindParam(':status', $status);
				$stmt->execute();				
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; archive sucessfully, your domain archive now';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
			//print_r($stmt->errorInfo());

		} else if($_POST['action'] == 'restore_domain') {

				$id			 	= trim($_POST['ids']);
				$status		 	= '0';
				
				
				$query = "UPDATE semrush_users_account SET status = :status WHERE id = :id ";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':id', $id);
				$stmt->bindParam(':status', $status);
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; Restore sucessfully, your domain restore now';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
			//print_r($stmt->errorInfo());
				
		} else if($_POST['action'] == 'delete_domain') {
				$id	 	= 	trim($_POST['ids']);
				$query 	= 	"DELETE FROM semrush_users_account WHERE id IN ($id) ";
				$stmt 	= 	$DBcon->prepare( $query );
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; delete sucessfully, your domain delete now';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
				
		} else if($_POST['action'] == 'replace_key') {
				$id	 	= 	trim($_POST['rowid']);
				$query 	= 	"SELECT * FROM semrush_users_account WHERE id IN ($id) ";
				$stmt 	= 	$DBcon->prepare( $query );
				if($stmt->execute()) {			
					$results = $stmt->fetchAll();
					$response['status'] = 'success';
					$response['data'] = $results;
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
		} else if($_POST['action'] == 'update_google_analytic_id') {
				$analytics_id	= 	trim($_POST['analytic_id']);
				$request_id		= 	trim($_POST['request_id']);
				$user_id		= 	trim($_SESSION['user_id']);
				$query 			= 	"UPDATE semrush_users_account SET google_analytics_id = :analytics_id WHERE user_id = :user_id AND id = :request_id";
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
						// Create an authorized analytics service object.
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
						print_r($stmt1->errorInfo()); exit;
					
						$response['status'] = 'success';
						$response['analytic_id'] = $myToken['access_token'];
						$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
					}
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
				
		} else if($_POST['action'] == 'update_google_view_id') {
				$google_view_id	= 	trim($_POST['view_id']);
				$request_id		= 	trim($_POST['request_id']);
				$user_id		= 	trim($_SESSION['user_id']);
				$query 			= 	"UPDATE semrush_users_account SET google_profile_id = :google_view_id WHERE user_id = :user_id AND id = :request_id";
				$stmt 			= 	$DBcon->prepare( $query );
				$stmt->bindParam(':google_view_id', $google_view_id);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
				
		} else if($_POST['action'] == 'remove_analaytic_data') {
				$request_id		= 	trim($_POST['request_ids']);
				$user_id		= 	trim($_SESSION['user_id']);
				$query 			= 	"UPDATE semrush_users_account SET google_account_id = '', google_analytics_id = '', google_property_id= '', google_profile_id='' WHERE user_id = :user_id AND id = :request_id";
				$stmt 			= 	$DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				if($stmt->execute()) {			
					$response['status'] = 'success';
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				} else {
					$response['status'] = 'error'; // could not register
					$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; getting error, try again';
				}	
				//print_r($stmt); exit;
		}
	}
	
	echo json_encode($response);
	