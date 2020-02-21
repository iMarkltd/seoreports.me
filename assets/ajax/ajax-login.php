<?php

	header('Content-type: application/json');

	require_once '../../includes/config.php';
	
	$response = array();

	if ($_POST) {
		
		$email = trim($_POST['email']);
		$pass = trim($_POST['password']);
		
		$user_email = strip_tags($email);
		$user_pass = strip_tags($pass);
		
		// sha256 password hashing
		try{
			$hashed_password = hash('sha256', $user_pass);
			$query = "SELECT * FROM users WHERE email=:email AND password=:pass";
			$stmt = $DBcon->prepare( $query );
			$stmt->bindParam(':email', $user_email);
			$stmt->bindParam(':pass', $hashed_password);
			$stmt->execute();
//			$stmt->debugDumpParams();
			// check for successfull registration
		}
		// Catch any errors
		catch(PDOException $e){
			$this->error = $e->getMessage();
		}
	
		if ($stmt->rowCount() > 0) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$_SESSION["user_id"] = $row['id'];
				$_SESSION["user_name"] = $row['name'];			
			}			
			$response['status'] = 'success';
			$response['message'] = '<span class="glyphicon glyphicon-ok"></span> &nbsp; Sign in sucessfully, you may login now';
        } else {
            $response['status'] = 'error'; // could not register
			$response['message'] = '<span class="glyphicon glyphicon-info-sign"></span> &nbsp; could not sign in, try again later';
        }	
	}
	
	echo json_encode($response);