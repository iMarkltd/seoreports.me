<?php
	require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
	global $DBcon;

		$action				=	$_REQUEST['action'];
		if($action  == 'profile_info'){ 
			$request_id	 		= 	$_REQUEST['request_id'];
			$user_id			= 	$_SESSION['user_id'];	
			$company_name	 	= 	($_REQUEST['company_name']);
			$email		 		= 	($_REQUEST['email']);
			$contact_number		= 	($_REQUEST['mobile']);
			$client_name		= 	($_REQUEST['client_name']);
	
			$select_query 	= 	"SELECT * FROM profile_info WHERE user_id=:user_id AND request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			if(empty($results)) {
				$query = "INSERT INTO profile_info(user_id,request_id,email, company_name,contact_no, client_name) 
							VALUES(:user_id, :request_id, :email,:company_name,:contact_no,:client_name)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':company_name', $company_name);
				$stmt->bindParam(':contact_no', $contact_number);
				$stmt->bindParam(':client_name', $client_name);
				if($stmt->execute()) {
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error'; // could not register
				}	
				echo json_encode($response); exit;
			}else{
				$delete_query 	= 	"DELETE FROM profile_info WHERE user_id=:user_id AND request_id=:request_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				if($delete_stmt->execute()) {
				$query = "INSERT INTO profile_info(user_id,request_id,email, company_name,contact_no, client_name) 
							VALUES(:user_id, :request_id, :email,:company_name,:contact_no,:client_name)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':company_name', $company_name);
				$stmt->bindParam(':contact_no', $contact_number);
				$stmt->bindParam(':client_name', $client_name);
					if($stmt->execute()) {
						$response['status'] = 'success';
					} else {
						$response['status'] = 'error'; // could not register
					}	
				echo json_encode($response); exit;
				}
			}
		}else if($action == 'general_settings') {
			$request_id	 		= 	$_REQUEST['request_id'];
			$user_id			= 	$_SESSION['user_id'];	
			$domain_name	 	= 	($_REQUEST['domain_name']);
			$domain_url  		= 	($_REQUEST['domain_url']);
			$clientName			= 	($_REQUEST['clientName']);
			$regional_db		= 	($_REQUEST['regional_db']);
			if(!empty($_REQUEST['domain_register']))
				$domain_register 	= 	date('Y-m-d', strtotime($_REQUEST['domain_register']));
			else				
				$domain_register	=	'0000-00-00';
			$sql = "UPDATE semrush_users_account SET domain_name = :domain_name, domain_url= :domain_url, regional_db=:regional_db, clientName=:clientName, domain_register=:domain_register WHERE id=:request_id";
			$stmt= $DBcon->prepare($sql);
			$stmt->bindParam(":domain_name", $domain_name);
			$stmt->bindParam(":domain_register", $domain_register);
			$stmt->bindParam(":domain_url", $domain_url);
			$stmt->bindParam(":regional_db", $regional_db);
			$stmt->bindParam(":clientName", $clientName);
			$stmt->bindParam(":request_id", $request_id);

			if($stmt->execute()) {
				$response['status'] = 'success';
			}else{
				$response['error']	=	$stmt->errorInfo(); 
				$response['status'] = 'error';
			}
			echo json_encode($response); exit;

		}else if($action == 'Update_Backlink') {
			$delete_query	=	"DELETE FROM semrush_backlinks_data";
			$delete_stmt	=	$DBcon->prepare($delete_query);
			$delete_stmt->execute();
		
		}
?>
