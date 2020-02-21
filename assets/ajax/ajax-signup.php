<?php

	header('Content-type: application/json');

	require_once '../../includes/config.php';
	
	$response = array();

	if ($_REQUEST) {
		
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
		
		// check for successfull registration
        if ( $stmt->execute() ) {
			$response['status'] = 'success';
			$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; registered sucessfully, you may login now';
        } else {
            $response['status'] = 'error'; // could not register
			$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; could not register, try again later';
        }	
	}
	
	echo json_encode($response);