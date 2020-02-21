<?php

	header('Content-type: application/json');

	require_once('../../includes/config.php');
	require_once('api/semrush_api.php');
	require_once('../../vendor/autoload.php');
	require_once("../../includes/functions.php");

	global $DBcon;

	$start_data     =   date('Y-m-d', strtotime("now"));
	$end_data       =   date('Y-m-d', strtotime("-1 month"));


	
/*	$query = "SELECT sua.*, (SELECT organic_keywords FROM semrush_domain_history WHERE request_id = sua.id LIMIT 0,1) as Keywords, 
	(SELECT SUM( count_session ) AS total_session FROM google_profile_session WHERE request_id = sua.id AND (from_date BETWEEN '$end_data' AND '$start_data') ORDER by id ASC) as Traffic FROM semrush_users_account sua WHERE sua.user_id=:user_id AND sua.status=0 ORDER BY sua.id  asc LIMIT ".$_REQUEST['start']." ,".$_REQUEST['length']." ";
*/


$searchQuery = " ";
if(!empty($_REQUEST['search']['value'])){
	$searchQuery = " AND ( sua.domain_name LIKE '%".$_REQUEST["search"]["value"]."%' OR sua.domain_url LIKE '%".$_REQUEST["search"]["value"]."%' ) ";
}

## Total number of records without filtering
$total_stmt = $DBcon->prepare("SELECT COUNT(*) AS allcount FROM semrush_users_account WHERE user_id=:user_id AND status=1 ");
$total_stmt->bindParam(':user_id', $_SESSION['user_id']);
$total_stmt->execute();
$records = $total_stmt->fetch();
$totalRecords = $records['allcount'];


## Total number of records with filtering
$fstmt = $DBcon->prepare("SELECT COUNT(*) AS allcount FROM semrush_users_account sua WHERE sua.user_id=:user_id AND sua.status=1 ".$searchQuery);
$fstmt->bindParam(':user_id', $_SESSION['user_id']);
$fstmt->execute();
$frecords = $fstmt->fetch();
$totalRecordwithFilter = $frecords['allcount'];

## Fetch records


	$query = "SELECT sua.* FROM semrush_users_account sua WHERE sua.user_id=:user_id AND sua.status=1 ".$searchQuery."  ORDER BY sua.id  asc LIMIT ".$_REQUEST['start']." ,".$_REQUEST['length']." ";


	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute();
	$results = $stmt->fetchAll();
	$filtered_rows = $stmt->rowCount();
	$data =	array();
	foreach($results as $result) {
		$client_name	  	=   getClientName($result['id']);

		$data[]	    =   array('domain_first_name' => $result['domain_name'][0], 'domain_name' => $result['domain_name'], 'domain_id' => $result['id'], 'client_name' => $client_name, 'domain_url'=> $result['domain_url'], 'date' => date('d/m/Y', strtotime($result['created'])), 'totalKeywords' => (!empty($totalKeywords) ? $totalKeywords : 0), 'totalTraffic' => (!empty($totalTraffic) ? $totalTraffic : 0) );
	}

	$json_data = array(
		"draw"            => intval( $_REQUEST['draw'] ),   
		"recordsTotal"    => intval( $totalRecords ),  
		"recordsFiltered" => intval( $totalRecordwithFilter ),
		"data"            => $data   // total data array
	);
	echo json_encode($json_data); exit;	
