<?php
require_once '../../includes/config.php';
global $DBcon;
$user_id 	=	$_SESSION['user_id']; 
$query 		= 	"SELECT count(id) as total FROM download_pdf_request WHERE user_id IN ($user_id) AND download_status = 1";
$stmt 		= 	$DBcon->prepare( $query );
$stmt->execute();
$results = $stmt->fetch();
$stmt->errorinfo();
if(!empty($results['total'])) {
	echo json_encode(1); exit; 
}else{
	echo json_encode(0);exit; 
}

