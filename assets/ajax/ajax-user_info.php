<?php
require_once '../../includes/config.php';
require_once("../../includes/functions.php");

$action			=	$_REQUEST['action']; 
$user_id		=	$_SESSION['user_id'];
if($_POST) {
	if($action == 'user_info') {
		$user_ids		=	base64_decode($_REQUEST['ids']);
		$full_name		=	($_REQUEST['full_name']);
		$company		=	($_REQUEST['company_name']);

		$query = "UPDATE users SET name = :name, company = :company WHERE id = :id ";
		$stmt = $DBcon->prepare( $query );
		$stmt->bindParam(':name', $full_name);
		$stmt->bindParam(':company', $company);
		$stmt->bindParam(':id', $user_ids);
		$stmt->execute();				
		if($stmt->execute()) {			
			$response['status'] = 'success';
		} else {
			$response['status'] = 'error'; // could not register
		}	
	} else if($action == 'user_image_upload') {
		$path 		= 	'uploads/'.$user_id.'/profile/';
		$file_path	=	'uploads/'.$user_id.'/profile';
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		$sourcePath 	= 	$_FILES['avatar-2']['tmp_name']; // Storing source path of the file in a variable
		$targetPath 	= 	$path.$_FILES['avatar-2']['name']; // Target path where file is to be stored
		
		if ( move_uploaded_file ( $sourcePath, $targetPath ) ){
			$response = array('status' => 'success' );
			$key = 'user_image_delete';
			$url = 'assets/ajax/ajax-user_info.php';
			$p1[0] = "assets/ajax/".$targetPath; // sends the data
			$p2[0] = ['caption' => $_FILES['avatar-2']['name'], 'size' => $_FILES['avatar-2']['size'], 'width' => '120px', 'url' => $url, 'key' => $key, 'extra' => array('action' => 'user_image_delete') ];
			echo json_encode([
				'initialPreview' => $p1, 
				'initialPreviewConfig' => $p2,   
				'append' => true // whether to append these configurations to initialPreview.
								 // if set to false it will overwrite initial preview
								 // if set to true it will append to initial preview
								 // if this propery not set or passed, it will default to true.
			 ], JSON_UNESCAPED_SLASHES); 
			exit;
		} else {
		   $response = array('status' => 'error' );
		}
	} else if($action == 'user_image_delete') {
		$path 		= 	'uploads/'.$user_id.'/profile/*';
		$files 		= 	glob($path); // get all file names
		foreach($files as $file){ // iterate files
		  if(is_file($file))
			unlink($file); // delete file
		}	
		$response = array('status' => 'success' );
	}
	echo json_encode($response); exit;
}	
	