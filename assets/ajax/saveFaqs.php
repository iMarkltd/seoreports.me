	<?php

	header('Content-type: application/json');

	require_once('../../includes/config.php');
	require_once('../../vendor/autoload.php');
	require_once("../../includes/functions.php");

	if ($_POST) {
		$response		=	array();
		if($_REQUEST['action'] == 'save_faq') {
			$user_id	= $_SESSION['user_id'];	
			$query 		= "INSERT INTO project_FAQ(faq_title, faq_content, request_id,user_id) VALUES(:faq_title,:faq_content,:request_id,:user_id)";
			$stmt 		= $DBcon->prepare( $query );
			$stmt->bindParam(':faq_title', $_REQUEST['faq_title']);
			$stmt->bindParam(':faq_content', $_REQUEST['faq_content']);
			$stmt->bindParam(':request_id', $_REQUEST['request_id']);
			$stmt->bindParam(':user_id', $user_id);
			if($stmt->execute()) {
				$response['status'] = '1'; // Insert Data Done
				$response['error'] = '0';
				$response['message'] = 'Data Insert Successfully';
			} else {
				$response['status'] = '2'; // could not register
				$response['error'] = '2';
				$response['message'] = 'Error to save data';
			}
		} else if($_REQUEST['action'] == 'edit_faq') {
			$query 			= 	"UPDATE project_FAQ SET faq_title = :faq_title, faq_content = :faq_content WHERE id = :faq_id";
			$stmt 			= 	$DBcon->prepare( $query );
			$stmt->bindParam(':faq_title', $_POST['faq_title']);
			$stmt->bindParam(':faq_content', $_POST['faq_content']);
			$stmt->bindParam(':faq_id', $_POST['faq_ids']);
			if($stmt->execute()) {	
				$response['status'] = '1'; // Insert Data Done
				$response['error'] = '0';
				$response['message'] = 'Data Updated Successfully';
			} else {
				$response['status'] = '2'; // could not register
				$response['error'] = '2';
				$response['message'] = 'Error to update data';
			}
		} else if($_REQUEST['action'] == 'delete_faq') {
			$id	 	= 	trim($_POST['data_id']);
			$query 	= 	"DELETE FROM project_FAQ WHERE id IN ($id) ";
			$stmt 	= 	$DBcon->prepare( $query );
			if($stmt->execute()) {			
				$response['status'] 	= '1'; // Insert Data Done
				$response['error'] 	 	= '0';
				$response['message'] 	= 'Data delete successfully';
			} else {
				$response['status'] 	= '2'; // could not register
				$response['error'] 		= '2';
				$response['message'] 	= 'Error to delete data ';
			}	
		} else if($_REQUEST['action'] == 'getDeleteHTMLFaq') {
			$html 	=	getFaqDeleteHtmlData();
			$response['status'] = '1'; // Insert Data Done
			$response['data'] = $html;
		} else if($_REQUEST['action'] == 'getHTMLFaq') {
			$html 	=	getFaqHtmlData();
			$response['status'] = '1'; // Insert Data Done
			$response['data'] = $html;
		} else if($_REQUEST['action'] == 'get_faq') {
			$data 	=	getFaqDataByID($_POST['slide_id']);
			$response['status'] = '1'; // Insert Data Done
			$response['data'] = $data;
			
		} else if($_REQUEST['action'] == 'getDeleteHTMLF') {
			$name = trim($_REQUEST['name']);
			$email = trim($_REQUEST['email']);
			$pass = trim($_REQUEST['cpassword']);
			
			$full_name = strip_tags($name);
			$user_email = strip_tags($email);
			$user_pass = strip_tags($pass);
			
			// sha256 password hashing
			$hashed_password = hash('sha256', $user_pass);
			
			$query = "INSERT INTO users(name,email,password) VALUES(:name, :email, :pass)";
			
			$stmt = $DBcon->prepare( $query );
			$stmt->bindParam(':name', $full_name);
			$stmt->bindParam(':email', $user_email);
			$stmt->bindParam(':pass', $hashed_password);
			
			$response['status'] = '1'; // Insert Data Done
			$response['data'] = $html;
		}
	}
	
	echo json_encode($response);
	