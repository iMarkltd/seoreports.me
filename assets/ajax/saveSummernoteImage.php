<?php
require_once('../../includes/config.php');
require_once("../../includes/functions.php");

if($_POST) {
	$action =	$_REQUEST['action'];
	if($action == 'save_image') {
		$path 		= 	'../uploads/summernote/';
		$name 		= 	md5(rand(100, 200));
		$extension 	= 	pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
		$filename 	= 	$name . '.' . $extension;
		$allowed 	= 	array('png', 'jpg', 'jpeg', 'gif','zip');
		$sourcePath 	= 	$_FILES['file']['tmp_name']; // Storing source path of the file in a variable
		$targetPath 	= 	$path.$filename; // Target path where file is to be stored
		if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
			if(!in_array(strtolower($extension), $allowed)){
				$response = array('status' => 'error', 'message' => 'extenstion not found' );
			}else {
				if ( move_uploaded_file ( $sourcePath, $targetPath ) ){
					$url		=	'assets/uploads/summernote/'.$filename;
					$response 	= 	array('status' => 'success', 'url' => $url );
				} else {
					$response = array('status' => 'error', 'message' => 'error in uploading' );
				}
			}
		} else {
			$response = array('status' => 'error', 'message' => 'file error' );;
		}			
	} else if($action == 'delete_image') {
		$src =  basename($_POST['src']);
		$path 		= 	'../uploads/summernote/'.$src;
		if(unlink($path))
		{
			$response = array('status' => 'success' );
		}else{
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
	