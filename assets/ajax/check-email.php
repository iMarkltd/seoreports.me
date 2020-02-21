<?php

	require_once '../../includes/config.php';

	$email = trim($_REQUEST['email']);
	$email = strip_tags($email);
	
	$query = "SELECT email FROM users WHERE email=:email";
	$stmt = $DBcon->prepare( $query );
	$stmt->execute(array(':email'=>$email));
	
	if ($stmt->rowCount() == 1) {
		echo 'false'; 
	} else {
		echo 'true'; 
	}

