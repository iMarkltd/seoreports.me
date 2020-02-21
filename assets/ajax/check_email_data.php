<?php
header('Content-type: application/json');
require_once '../../includes/config.php';
require_once '../../vendor/PHPMailer/PHPMailerAutoload.php';
require_once("../../includes/functions.php");
require_once("../../vendor/php/lib/GrabzItClient.class.php");

$path 		= 	"pdf/";
$user_id	= 	$_SESSION["user_id"];


if ($_REQUEST) {
	if($_REQUEST['action'] == 'check_email_data') {
		$to_emails		= 	json_decode($_REQUEST['request_ids']);
		$select_query 	= 	"SELECT request_id FROM user_email_details WHERE user_id =:user_id";
		$select_stmt 	= 	$DBcon->prepare( $select_query );
							$select_stmt->bindParam(':user_id', $user_id);
							$select_stmt->execute();
							$select_stmt->errorinfo();
							$results = $select_stmt->fetchAll();
							foreach($results as $result){
								$new_arr[]	=	($result[0]);
							}
							foreach($to_emails as $var)
							{
									$update_sql 	=	'UPDATE `user_email_details` SET `email_status` = 1 WHERE `user_id` = :user_id AND request_id =:request_id';
									$update_stmt 	= 	$DBcon->prepare( $update_sql );
														$update_stmt->bindParam(':user_id', $user_id);
														$update_stmt->bindParam(':request_id', $var);
														$update_stmt->execute();
														$update_stmt->errorinfo();
							}
							$diff_arr 		=	array_values(array_diff($to_emails, $new_arr));
							$string_diff 	= 	str_repeat ('?, ',  count($diff_arr) - 1) . '?';
							$domain_query 	= 	"SELECT domain_name, id FROM semrush_users_account WHERE id IN($string_diff)";
							$domain_stmt 	= 	$DBcon->prepare( $domain_query );
												$domain_stmt->execute($diff_arr);
												$domain_stmt->errorinfo();
												$new_results = $domain_stmt->fetchAll();
												foreach($new_results as $result){
													$ids[]		= $result['id'];
													$string[] 	= $result['domain_name'];
												}
												if(!empty($new_results)) {
													$response['status'] 		= 'success';
													$response['ids'] 			= $ids;
													$response['domain_name'] 	= $string;
												}else{
													$response['status'] = 'empty';
												}
	} elseif($_REQUEST['action'] == 'check_single_email_data') {
		$request_id   	= 	$_REQUEST['request_ids'];
		$select_query 	= 	"SELECT request_id FROM user_email_details WHERE user_id =:user_id AND request_id =:request_id ";
		$select_stmt 	= 	$DBcon->prepare( $select_query );
							$select_stmt->bindParam(':user_id', $user_id);
							$select_stmt->bindParam(':request_id', $request_id);
							$select_stmt->execute();
							$select_stmt->errorinfo();
							$results = $select_stmt->fetch();
							$update_sql 	=	'UPDATE `user_email_details` SET `email_status` = 1 WHERE `user_id` = :user_id AND request_id =:request_id';
							$update_stmt 	= 	$DBcon->prepare( $update_sql );
												$update_stmt->bindParam(':user_id', $user_id);
												$update_stmt->bindParam(':request_id', $request_id);
												$update_stmt->execute();
												$update_stmt->errorinfo();
							if(!empty($results)) {
								$response['status'] = 'success';
							}else{
								$response['status'] = 'empty';
							}
	} elseif($_REQUEST['action'] == 'check_email_status') {
		$request_id   	= 	$_REQUEST['ids'];
		$select_query 	= 	"SELECT * FROM user_email_details WHERE user_id =:user_id AND request_id =:request_id ";
		$select_stmt 	= 	$DBcon->prepare( $select_query );
							$select_stmt->bindParam(':user_id', $user_id);
							$select_stmt->bindParam(':request_id', $request_id);
							$select_stmt->execute();
							$select_stmt->errorinfo();
							$results = $select_stmt->fetch();
							if(!empty($results)) {
								$response['status'] 		= 	'success';
								$response['mail_sent']		=  	($results['email_status'] == 1 ? '1' : ($results['email_status'] == 2 ? '2' : 0));
							}else{
								$response['status'] 		= 'error';
							}
	} elseif($_REQUEST['action'] == 'change_email_status') {
		$request_id   	= 	$_REQUEST['request_ids'];
		$update_sql 	=	'UPDATE `user_email_details` SET `email_status` = 0 WHERE `user_id` = :user_id AND request_id =:request_id';
		$update_stmt 	= 	$DBcon->prepare( $update_sql );
							$update_stmt->bindParam(':user_id', $user_id);
							$update_stmt->bindParam(':request_id', $request_id);
							if($update_stmt->execute()) {
								$response['status'] = 'success';
							}else{
								$response['status'] = 'error';
								$update_stmt->errorinfo();
							}
	}

	
		
}
	echo json_encode($response);exit;
	
