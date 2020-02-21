<?php
ini_set('memory_limit', '1024M'); // or you could use 1G

header('Content-type: application/json');

require_once('../../includes/config.php');
require_once('../../vendor/autoload.php');
require_once("../../includes/functions.php");
require_once('dataForceApi/RestClient.php');

if ($_POST) {
	global $DBcon;
	$user_text		=	$_REQUEST['q'];
	$query = "SELECT * FROM keyword_location_list WHERE loc_name_canonical Like :user_text Limit 10 ";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindValue(':user_text', $user_text."%");
	$stmt->execute();
	$results = $stmt->fetchAll();
	$array = array();
	if(!empty($results)) {
		foreach($results as $result) {
			$array[]	=	$result['loc_name_canonical'];
		} 
	}

}

echo json_encode($array);
