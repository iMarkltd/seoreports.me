<?php

	header('Content-type: application/json');

	require_once '../../includes/config.php';
	require_once("../../includes/functions.php");
	
	$response = array();

	if ($_POST) {
		$ids 			= trim($_POST['ids']);
		$results		= checkDownloadLink($ids);
		$response		=	array();
		if(!empty($results)){
			$response['result'] = '1';
		} else {
			$response['result'] = '2';
		}
	}
	
	echo json_encode($response);
	