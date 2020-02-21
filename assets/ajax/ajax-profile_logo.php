<?php
require_once '../../includes/config.php';
require_once("../../includes/functions.php");

$action			=	$_REQUEST['action']; 
$request_id		=	$_REQUEST['request_id']; 
$user_id		=	$_SESSION['user_id'];
if($_POST) {
	if($action == 'profile_logo_upload') {
		$path 		= 	'uploads/'.$user_id.'/'.$request_id."/";
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		$sourcePath 	= 	$_FILES['logo']['tmp_name']; // Storing source path of the file in a variable
		$targetPath 	= 	$path.$_FILES['logo']['name']; // Target path where file is to be stored
		
		if ( move_uploaded_file ( $sourcePath, $targetPath ) ){
			$response = array('status' => 'success' );
			$key = 'logo_image_delete';
			$url = 'assets/ajax/ajax-profile_logo.php';
			$p1[0] = "assets/ajax/".$targetPath; // sends the data
			$p2[0] = ['caption' => $_FILES['logo']['name'], 'size' => $_FILES['logo']['size'], 'width' => '120px', 'url' => $url, 'key' => $key, 'extra' => array('action' => 'logo_image_delete', 'request_id' => $request_id) ];
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
	} else if($action == 'logo_image_delete') {
		$path 		= 	'uploads/'.$user_id.'/'.$request_id."/";
		if (file_exists($path)) {
			$files 		= 	glob($path."*"); // get all file names
			foreach($files as $file){ // iterate files
			  if(is_file($file))
				unlink($file); // delete file
			}	
			$response = array('status' => 'success' );
		} else {
			$response = array('status' => 'error' );
		}
	}
	echo json_encode($response); exit;
}	
	