<?php

require_once('../../includes/config.php');
require_once("../../includes/functions.php");
require_once('../../assets/ajax/api/semrush_api.php');
require_once('../../vendor/autoload.php');
use Carbon\Carbon;
global $DBcon;
error_reporting(1);
ini_set('display_errors', 1);

$ids 			= 	$_REQUEST['uid'];
$domain_details	=	getUserDomainDetails($ids);
$client       	=   new Google_Client();
$result			=	array();
if ($_POST) {
	
	$delete_query	=	"DELETE FROM module_by_daterange WHERE request_id=".$_POST['uid']." AND module='".$_POST['action']."'";
	$delete_stmt	=	$DBcon->prepare($delete_query);
	$delete_stmt->execute();
	
	$count_query = "INSERT INTO module_by_daterange(user_id,request_id, module, start_date, end_date) 
	VALUES(:user_id, :request_id, :module, :start_date, :end_date)";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':user_id', $domain_details['user_id']);
	$count_stmt->bindParam(':request_id', $_REQUEST['uid']);
	$count_stmt->bindParam(':module', $_POST['action']);
	$count_stmt->bindParam(':start_date', date('Y-m-d', strtotime($_POST['start'])));
	$count_stmt->bindParam(':end_date', date('Y-m-d', strtotime($_POST['stop'])));
	if (!$count_stmt->execute()) {
		print_r($count_stmt->errorInfo());  exit;
	}			


	if($_POST['action'] == 'search_console') {
		$start_data     =   date('Y-m-d', strtotime($_POST['start']));
		$end_data       =   date('Y-m-d', strtotime($_POST['stop']));
		$searchConsole	=	googleSearchConsole($_REQUEST['uid'], $domain_details['domain_url'], $domain_details['google_account_id'], $start_data, $end_data);
		if(!empty($searchConsole)) { 
			$result['clicks']		=	array();
			$result['impression']	=	array();
			foreach($searchConsole['data'] as $analytics)	{
				$result['clicks'][]		=	array(strtotime($analytics->keys[0])*1000, $analytics->clicks);
				$result['impression'][]	=	array(strtotime($analytics->keys[0])*1000, $analytics->impressions);			
			}	
		}
		if(!empty($searchConsole)) { 
			$query_html		=	'';
			foreach($searchConsole['query'] as $query)	{
				$query_html	.='
				<tr>
				<td>'.$query->keys[0].'</td>
				<td>'.$query->clicks.'</td>
				<td>'.$query->impressions.'</td>
				</tr>';
			}
			$result['query']	=	$query_html;
		}
		if(!empty($searchConsole)) { 
			$page_html		=	'';
			foreach($searchConsole['pages'] as $page)	{
				$page_html	.='
				<tr>
				<td>'.$page->keys[0].'</td>
				<td>'.$page->clicks.'</td>
				<td>'.$page->impressions.'</td>
				</tr>';
			}
			$result['page']	=	$page_html;
		}
		if(!empty($searchConsole)) { 
			$country_html		=	'';
			foreach($searchConsole['countries'] as $country)	{
				$country_html	.='
				<tr>
				<td>'.$country->keys[0].'</td>
				<td>'.$country->clicks.'</td>
				<td>'.$country->impressions.'</td>
				<td>'.$country->ctr.'</td>
				<td>'.$country->position.'</td>
				</tr>';
			}
			$result['country']	=	$country_html;
		}
		if(!empty($searchConsole)) { 
			$device_html		=	'';
			foreach($searchConsole['device'] as $device)	{
				$device_html	.='
				<tr>
				<td>'.$device->keys[0].'</td>
				<td>'.$device->clicks.'</td>
				<td>'.$device->impressions.'</td>
				<td>'.$device->ctr.'</td>
				<td>'.$device->position.'</td>
				</tr>';
			}
			$result['device']	=	$device_html;
		}
	}else if($_POST['action'] == 'organic_graph') {
		$domain_name	=	$_REQUEST['url'];
		$start_data     =   date('Ymd', strtotime($_POST['start']));
		$end_data       =   date('Ymd', strtotime($_POST['stop']));
		$result_query 	= 	"SELECT * FROM semrush_domain_history WHERE request_id=$ids AND domain_name='".$domain_name."' AND (date_time BETWEEN $start_data AND $end_data) ORDER By id DESC";
		$again_stmt = $DBcon->prepare( $result_query );
		
		$again_stmt->execute();
		if (!$again_stmt->execute()) {
			print_r($again_stmt->errorInfo()); 
		}			
			// check for successfull registration
		$domain_history = $again_stmt->fetchAll();
		if(!empty($domain_history)) {
			foreach($domain_history as $history) {
				$timeZone 						= 	new DateTimeZone("Asia/Kolkata");
				$dateTime 						= 	\DateTime::createFromFormat('Ymd', $history['date_time'])->format('U') * 1000;
				$organic_keywordss[]	        =	array($dateTime, intval($history['organic_keywords']));
				$organic_date[]					=	$dateTime;
			}
			$organic_history			=		$domain_history[0]['organic_keywords'];
		}else{
			$result['organic_date']			= 	'';
			$result['organic_keywordss']	= 	'';
		}
		$organic_history				= $domain_history[(count($domain_history)-1)]['organic_keywords'];
		$domain_history					= $domain_history[(count($domain_history)-2)]['organic_keywords']; 
		$organic_keywords	    		= round(($organic_history-$domain_history)/$domain_history*100, 2); 
		$result['organic_date']			= $organic_date;
		$result['organic_keywordss']	= $organic_keywordss;
		$result['organic_history']		= $organic_history;
		$result['organic_keywords']		= $organic_keywords;
		

	}else if($_POST['action'] == 'organic_traffice') {
		$request_id		= 	trim($_POST['uid']);
		$user_id		= 	$_SESSION['user_id'];	
		$view_key		= 	$_POST['view_id'];	
		$start_date 	=	date('Y-m-d', strtotime($_POST['start']));
		$end_data       =   date('Y-m-d', strtotime($_POST['stop']));
		$day_diff 		= 	strtotime($_POST['start']) - strtotime($_POST['stop']);
		$count_days 	=	floor($day_diff/(60*60*24));
			//print_r($count_days); exit;
		$start_data     =   date('Y-m-d', strtotime($_POST['start'].' '.$count_days.' days'));
//			$start_data		=	DateTime::createFromFormat('Y-m-d',$_POST['start'])->modify()->format('M 1, Y');
		$analytics 		= 	initializeAnalytics($domain_details['google_account_id']);
		$checkData 		= 	getGoogleProfileSession($request_id);
		$analyticsR		= 	getDataValue($analytics, 'ga:'.$view_key, $start_data, $end_data);
		if(empty($checkData)) {
			foreach($analyticsR->rows as $key=>$val ) {
				$date_val		= 	date("Y-m-d", strtotime($val[0]));
				$session_count	=	$val[1];
				$query = "INSERT INTO google_profile_session(user_id,view_id,request_id,from_date, count_session) VALUES(:user_id, :view_id, :request_id, :from_date, :count_session)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':view_id', $view_key);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':from_date', $date_val);
				$stmt->bindParam(':count_session', $session_count);
				if ( $stmt->execute() ) {
					$date_val	=	null;
					$session_count	=	null;
				}
			}
		}else{
			$delete_query 	= 	"DELETE FROM google_profile_session WHERE user_id=:user_id AND request_id=:request_id";
			$delete_stmt 	= 	$DBcon->prepare( $delete_query );
			$delete_stmt->bindParam(':user_id', $user_id);
			$delete_stmt->bindParam(':request_id', $request_id);
			if($delete_stmt->execute()) {
				foreach($analyticsR->rows as $key=>$val ) {
					$date_val		= 	date("Y-m-d", strtotime($val[0]));
					$session_count	=	$val[1];
					$query = "INSERT INTO google_profile_session(user_id,view_id,request_id,from_date, count_session) VALUES(:user_id, :view_id, :request_id, :from_date, :count_session)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':view_id', $view_key);
					$stmt->bindParam(':request_id', $request_id);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':from_date', $date_val);
					$stmt->bindParam(':count_session', $session_count);
					if ( $stmt->execute() ) {
						$date_val	=	null;
						$session_count	=	null;
					}
				}
			}
		}
		
//			$response 		= 	getReport($analytics, $view_key, $start_data, $end_data);

		$select_query 	= 	"SELECT *, WEEK( from_date ) AS DWNum, SUM( count_session ) AS total_session FROM google_profile_session WHERE user_id=$user_id AND request_id = $request_id AND (from_date BETWEEN '$start_date' AND '$end_data') GROUP BY DWNum ORDER by id ASC"; 
		$select_stmt = $DBcon->prepare( $select_query );
		$select_stmt->execute();
		$results = $select_stmt->fetchAll();

		$combine_query 	= 	"SELECT *, WEEK( from_date ) AS DWNum, SUM( count_session ) AS total_session, UNIX_TIMESTAMP( from_date ) AS unix_date FROM google_profile_session WHERE user_id=$user_id AND request_id = $request_id AND (from_date BETWEEN '$start_data' AND '$start_date') GROUP BY DWNum ORDER by id ASC";
		$combine_stmt = $DBcon->prepare( $combine_query );
		$combine_stmt->execute();
		$combine_results = $combine_stmt->fetchAll();

		$domain_details	=	getUserDomainDetails($request_id);
		$plot_month                 =   '';
		if(!empty($domain_details['domain_register'])){
			$plot_month     =   date('M', strtotime($domain_details['domain_register']));
		}
		
		$unix_date		 =	'';
		$from_dates		 =	'';
		$count_session 	 =	'';
		$combine_session =	'';
		$plot_index 	 =	'test';
		foreach($results as $session_details) { 
			$plot_dates[] 		=	date('M', strtotime($session_details['from_date']));
			$from_dates[] 		=	"'".date('M d', strtotime($session_details['from_date']))."'";
			$unix_date			=	$session_details['unix_date']*1000;
			$count_session[]	=	intval($session_details['total_session']);
		}
		foreach($combine_results as $combine){
			$unix_date			=	$combine['unix_date']*1000;
			$combine_session[]	=	array(date('Y-d-m', strtotime($combine['from_date'])), intval($combine['total_session']));
		}

		$current_stats 	= 	getMetricsData($analytics, 'ga:'.$view_key, $start_date, $end_data);
		$combine_stats 	= 	getMetricsData($analytics, 'ga:'.$view_key, $start_data, $start_date);

		if(!empty($plot_dates) && !empty($plot_month)){
			$index = array_search($plot_month, $plot_dates);
			
			if ($index != false)
				$plot_index = closestDates($results, $domain_details['domain_register']);
		}

		$result['current_period']	= 	date('d-m-Y', strtotime($start_date)).' to '.date('d-m-Y', strtotime($end_data));
		$result['previous_period']	= 	date('d-m-Y', strtotime($start_data)).' to '.date('d-m-Y', strtotime($start_date));

		$result['from_dates']		=	($from_dates);	
		$result['count_session']	=	($count_session);
		$result['combine_session']	=	($combine_session);
		$result['plot_index']		=	($plot_index);
		$result['current_stats']	=	($current_stats);
		$result['combine_stats']	=	($combine_stats);
	}

	echo json_encode($result); exit;
}
?>
