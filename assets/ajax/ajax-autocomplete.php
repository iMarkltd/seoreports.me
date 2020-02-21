<?php

	require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
	global $DBcon;
	$user_text		=	$_REQUEST['text'];
	$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id AND domain_name Like :user_text AND status = 0";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->bindValue(':user_text', "%".$user_text."%");
	$stmt->execute();
	$results = $stmt->fetchAll();
	$li	=	'<ul>';
	if(!empty($results)) {
		
		foreach($results as $result) {
			$li	.= '<li><a href="test_seo_analytics_chart.php?id='.$result['id'].'" >'.$result['domain_name'].'</a></li>';
		} 
	}else{
			$li	.= '<li>No Result Found</li>';
	}
	$li.='</ul>';
	$response['html'] = $li;
	echo json_encode($response);
?>
 