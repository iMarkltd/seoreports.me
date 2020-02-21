<?php

	require_once('../../includes/config.php');
	require_once("../../includes/functions.php");
	require_once('../../assets/ajax/api/semrush_api.php');
	require_once('../../vendor/autoload.php');

	global $DBcon;
	$client       	=   new Google_Client();

	if ($_POST) {
		if($_POST['action'] == 'session_response') {
			$view_id 		= 	trim($_POST['view_id']);
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	$_SESSION['user_id'];	
			$rows			= 	json_decode($_REQUEST['rows'], true);
			$select_query 	= 	"SELECT * FROM google_profile_session WHERE user_id=:user_id AND request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();

			$domain_details	=	getUserDomainDetails($request_id);

			if(empty($results)) {
				foreach($rows as $val ) {
					$date_val		= 	date("Y-m-d", strtotime($val[0]));
					$session_count	=	$val[1];
					$query = "INSERT INTO google_profile_session(user_id,view_id,request_id,from_date, count_session) VALUES(:user_id, :view_id, :request_id, :from_date, :count_session)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':view_id', $view_id);
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
					foreach($rows as $val ) {
						$date_val		= 	date("Y-m-d", strtotime($val[0]));
						$session_count	=	$val[1];
						$query = "INSERT INTO google_profile_session(user_id,view_id,request_id,from_date, count_session) VALUES(:user_id, :view_id, :request_id, :from_date, :count_session)";
						$stmt = $DBcon->prepare( $query );
						$stmt->bindParam(':view_id', $view_id);
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
				$start_data     =   date('Ymd', strtotime('now'));
				$end_data       =   date('Ymd', strtotime($start_data." -180 days"));

				$end_combine_date	=	date('Ymd', strtotime($end_data." -180 days"));
			
				$select_query 	= 	"SELECT *, WEEK( from_date ) AS DWNum, SUM( count_session ) AS total_session, UNIX_TIMESTAMP( from_date ) AS unix_date FROM google_profile_session WHERE user_id=$user_id AND request_id = $request_id AND (from_date BETWEEN '$end_data' AND '$start_data') GROUP BY DWNum ORDER by id ASC";
				$select_stmt = $DBcon->prepare( $select_query );
				$select_stmt->execute();
				$results = $select_stmt->fetchAll();

				$combine_query 	= 	"SELECT *, WEEK( from_date ) AS DWNum, SUM( count_session ) AS total_session, UNIX_TIMESTAMP( from_date ) AS unix_date FROM google_profile_session WHERE user_id=$user_id AND request_id = $request_id AND (from_date BETWEEN '$end_combine_date' AND '$end_data') GROUP BY DWNum ORDER by id ASC";
				$combine_stmt = $DBcon->prepare( $combine_query );
				$combine_stmt->execute();
				$combine_results = $combine_stmt->fetchAll();

				$analytics 		= 	initializeAnalytics($domain_details['google_account_id']);
				$current_stats 	= 	getMetricsData($analytics, 'ga:'.$view_id, date('Y-m-d', strtotime($start_data." -180 days")), date('Y-m-d', strtotime('now')));
				$combine_stats 	= 	getMetricsData($analytics, 'ga:'.$view_id, date('Y-m-d', strtotime($end_data." -180 days")), date('Y-m-d', strtotime($start_data." -180 days")));
				$plot_index	=	null;
				$unix_date	=	'';
				$from_dates	=	'';
				$count_session = '';
				$combine_session = '';
				foreach($results as $session_details) { 
					$from_dates[] 		=	"'".date('M d', strtotime($session_details['from_date']))."'";
                    $plot_dates[] 		=	date('M', strtotime($session_details['from_date']));
					$unix_date			=	$session_details['unix_date']*1000;
					$count_session[]	=	intval($session_details['total_session']);
				}
				foreach($combine_results as $combine){
					$unix_date			=	$combine['unix_date']*1000;
					$combine_session[]	=	intval($combine['total_session']);
				}
				if(!empty($domain_details['domain_register'])){
					$plot_month     =   date('M', strtotime($domain_details['domain_register']));
				}
		
		
				if(!empty($plot_dates) && !empty($plot_month)){
                    $index = array_search($plot_month, $plot_dates);
                    
                    if ($index != false)
                        $plot_index = $index;                    
                }


				$response['current_period']		= date('d-m-Y', strtotime($start_data." -180 days")).' to '.date('d-m-Y', strtotime('now'));
				$response['previous_period']	= date('d-m-Y', strtotime($end_data." -180 days")).' to '.date('d-m-Y', strtotime($start_data." -180 days"));


				$response['from_dates']		=	($from_dates);	
				$response['count_session']	=	($count_session);
				$response['combine_session']=	($combine_session);
				$response['current_stats']	=	($current_stats);
				$response['combine_stats']	=	($combine_stats);
				$response['plot_index']		=	($plot_index);
	
				echo  json_encode($response); exit;
			
		} else if($_POST['action'] == 'session_data') {
			$view_id 		= 	trim($_POST['view_id']);
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	$_SESSION['user_id'];	
			$rows			= 	json_decode($_REQUEST['rows'], true);
			$session_count	=	array();
			$select_query 	= 	"SELECT * FROM google_profile_data WHERE user_id=:user_id AND request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			if(empty($results)) {
				foreach($rows as $key=>$val ) {
					foreach($val['c'] as $new_key=>$new_val) {
						if($new_key == 0){
							$keywords	=	 $new_val['v'];
						}else if($new_key == 1){
							$sessions	=	 $new_val['v'];
						}else if($new_key == 2){
							$new_users	=	 $new_val['v'];
						}else if($new_key == 3){
							$bounse_rate	=	 round($new_val['v'], 2);
						}else if($new_key == 4){
							$page_sessions	=	round($new_val['v'], 2);
						}else if($new_key == 5){
							$avg_session	=	 round($new_val['v'], 2);
						}else if($new_key == 6){
							$goal_conversions	=	 $new_val['v'];
						}else if($new_key == 7){
							$goal_completions	=	 $new_val['v'];
							$session_count[]	=	$new_val['v'];
						}else if($new_key == 8){
							$goal_value	=	 $new_val['v'];
						}
					}
					$query = "INSERT INTO google_profile_data(user_id,view_id,request_id,sessions, keywords,new_users,bounse_rate,page_sessions,avg_session,goal_conversions,goal_completions, goal_value) 
								VALUES(:user_id, :view_id, :request_id, :sessions,:keywords,:new_users,:bounse_rate,:page_sessions,:avg_session,:goal_conversions, :goal_completions, :goal_value)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':view_id', $view_id);
					$stmt->bindParam(':request_id', $request_id);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':keywords', $keywords);
					$stmt->bindParam(':sessions', $sessions);
					$stmt->bindParam(':new_users', $new_users);
					$stmt->bindParam(':bounse_rate', $bounse_rate);
					$stmt->bindParam(':page_sessions', $page_sessions);
					$stmt->bindParam(':avg_session', $avg_session);
					$stmt->bindParam(':goal_conversions', $goal_conversions);
					$stmt->bindParam(':goal_completions', $goal_completions);
					$stmt->bindParam(':goal_value', $goal_value);
					if ( $stmt->execute() ) {
							$keywords			=	null;
							$sessions			=	null;
							$new_session		=	null;
							$new_users			=	null;
							$bounse_rate		=	null;
							$page_sessions		=	null;
							$avg_session		=	null;
							$goal_conversions	=	null;
							$goal_completions	=	null;
							$goal_value			=	null;
					}

						//print_r($stmt->errorInfo()); 
					
				}
				$goal_count	=	array_sum($session_count);
				
			}else{
				$delete_query 	= 	"DELETE FROM google_profile_data WHERE user_id=:user_id AND request_id=:request_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				if($delete_stmt->execute()) {
					foreach($rows as $key=>$val ) {
						foreach($val['c'] as $new_key=>$new_val) {
							if($new_key == 0){
								$keywords	=	 $new_val['v'];
							}else if($new_key == 1){
								$sessions	=	 $new_val['v'];
							}else if($new_key == 2){
								$new_users	=	 $new_val['v'];
							}else if($new_key == 3){
								$bounse_rate	=	 round($new_val['v'], 2);
							}else if($new_key == 4){
								$page_sessions	=	round($new_val['v'], 2);
							}else if($new_key == 5){
								$avg_session	=	 round($new_val['v'], 2);
							}else if($new_key == 6){
								$goal_conversions	=	 $new_val['v'];
							}else if($new_key == 7){
								$goal_completions	=	 $new_val['v'];
							}else if($new_key == 8){
								$goal_value	=	 $new_val['v'];
							}
						}
						$query = "INSERT INTO google_profile_data(user_id,view_id,request_id,sessions, keywords,new_users,bounse_rate,page_sessions,avg_session,goal_conversions,goal_completions, goal_value) 
									VALUES(:user_id, :view_id, :request_id, :sessions,:keywords,:new_users,:bounse_rate,:page_sessions,:avg_session,:goal_conversions, :goal_completions, :goal_value)";
						$stmt = $DBcon->prepare( $query );
						$stmt->bindParam(':view_id', $view_id);
						$stmt->bindParam(':request_id', $request_id);
						$stmt->bindParam(':user_id', $user_id);
						$stmt->bindParam(':keywords', $keywords);
						$stmt->bindParam(':sessions', $sessions);
						$stmt->bindParam(':new_users', $new_users);
						$stmt->bindParam(':bounse_rate', $bounse_rate);
						$stmt->bindParam(':page_sessions', $page_sessions);
						$stmt->bindParam(':avg_session', $avg_session);
						$stmt->bindParam(':goal_conversions', $goal_conversions);
						$stmt->bindParam(':goal_completions', $goal_completions);
						$stmt->bindParam(':goal_value', $goal_value);
						if ( $stmt->execute() ) {
							$keywords			=	null;
							$sessions			=	null;
							$new_session		=	null;
							$new_users			=	null;
							$bounse_rate		=	null;
							$page_sessions		=	null;
							$avg_session		=	null;
							$goal_conversions	=	null;
							$goal_completions	=	null;
							$goal_value			=	null;
						}
						//print_r($stmt->errorInfo()); 
						
					}
					$goal_count	=	array_sum($session_count);
					
				}
			}
			$response['goal_count']	=	$goal_count;
			echo json_encode($response); exit;
			
		} else if($_POST['action'] == 'old_vs_new') {
			$view_id 			= 	trim($_POST['view_id']);
			$request_id			= 	trim($_POST['request_id']);
			$user_id			= 	$_SESSION['user_id'];	
			$sessions_old		= 	$_POST['session_old'];	
			$sessions_new		= 	$_POST['session_new'];	
			$sessions_total		= 	$_POST['total_session'];	
			$users_new			= 	$_POST['users_new'];	
			$users_old			= 	$_POST['users_old'];	
			$users_total		= 	$_POST['total_users'];	
			$pageviews_old		= 	$_POST['pageview_new'];	
			$pageviews_new		= 	$_POST['pageview_old'];	
			$pageviews_total	= 	$_POST['total_pageview'];	
			$select_query 	= 	"SELECT * FROM google_previous_old_data WHERE user_id=:user_id AND request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			if(empty($results)) {
				$query = "INSERT INTO google_previous_old_data(user_id,view_id,request_id,sessions_old, sessions_new,sessions_total,users_old,users_new,users_total,pageviews_old,pageviews_new,pageviews_total) 
							VALUES(:user_id, :view_id, :request_id, :sessions_old,:sessions_new,:sessions_total,:users_old,:users_new,:users_total,:pageviews_old,:pageviews_new, :pageviews_total)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':view_id', $view_id);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':sessions_old', $sessions_old);
				$stmt->bindParam(':sessions_new', $sessions_new);
				$stmt->bindParam(':sessions_total', $sessions_total);
				$stmt->bindParam(':users_old', $users_old);
				$stmt->bindParam(':users_new', $users_new);
				$stmt->bindParam(':users_total', $users_total);
				$stmt->bindParam(':pageviews_old', $pageviews_old);
				$stmt->bindParam(':pageviews_new', $pageviews_new);
				$stmt->bindParam(':pageviews_total', $pageviews_total);
				$stmt->execute();
				
				print_r($stmt->errorInfo()); exit;
				
			}else{
				$delete_query 	= 	"DELETE FROM google_previous_old_data WHERE user_id=:user_id AND request_id=:request_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				if($delete_stmt->execute()) {
					$view_id 			= 	trim($_POST['view_id']);
					$request_id			= 	trim($_POST['request_id']);
					$user_id			= 	$_SESSION['user_id'];	
					$sessions_old		= 	$_POST['session_old'];	
					$sessions_new		= 	$_POST['session_new'];	
					$sessions_total		= 	$_POST['total_session'];	
					$users_new			= 	$_POST['users_new'];	
					$users_old			= 	$_POST['users_old'];	
					$users_total		= 	$_POST['total_users'];	
					$pageviews_old		= 	$_POST['pageview_new'];	
					$pageviews_new		= 	$_POST['pageview_old'];	
					$pageviews_total	= 	$_POST['total_pageview'];	
					$query = "INSERT INTO google_previous_old_data(user_id,view_id,request_id,sessions_old, sessions_new,sessions_total,users_old,users_new,users_total,pageviews_old,pageviews_new,pageviews_total) 
								VALUES(:user_id, :view_id, :request_id, :sessions_old,:sessions_new,:sessions_total,:users_old,:users_new,:users_total,:pageviews_old,:pageviews_new, :pageviews_total)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':view_id', $view_id);
					$stmt->bindParam(':request_id', $request_id);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':sessions_old', $sessions_old);
					$stmt->bindParam(':sessions_new', $sessions_new);
					$stmt->bindParam(':sessions_total', $sessions_total);
					$stmt->bindParam(':users_old', $users_old);
					$stmt->bindParam(':users_new', $users_new);
					$stmt->bindParam(':users_total', $users_total);
					$stmt->bindParam(':pageviews_old', $pageviews_old);
					$stmt->bindParam(':pageviews_new', $pageviews_new);
					$stmt->bindParam(':pageviews_total', $pageviews_total);
					$stmt->execute();
				}
			}
		} else if($_POST['action'] == 'goal_completions_last') {
			$view_id 		= 	trim($_POST['view_id']);
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	$_SESSION['user_id'];	
			$rows			= 	json_decode($_REQUEST['rows'], true);
			$goal_count		= 	'';;
			$select_query 	= 	"SELECT * FROM google_goal_completion WHERE user_id=:user_id AND request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':user_id', $user_id);
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			if(empty($results)) {
				foreach($rows as $val ) {
					$session_count[]	=	$val[1];
				}
				$goal_count	=	array_sum($session_count);
				$query = "INSERT INTO google_goal_completion(user_id,view_id,request_id, goal_count) VALUES(:user_id, :view_id, :request_id, :goal_count)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':view_id', $view_id);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':goal_count', $goal_count);
				$stmt->execute();
			}else{
				$delete_query 	= 	"DELETE FROM google_goal_completion WHERE user_id=:user_id AND request_id=:request_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				if($delete_stmt->execute()) {
					foreach($rows as $val ) {
						$session_count[]	=	$val[1];
					}
					$goal_count	=	array_sum($session_count);
					$query = "INSERT INTO google_goal_completion(user_id,view_id,request_id, goal_count) VALUES(:user_id, :view_id, :request_id, :goal_count)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':view_id', $view_id);
					$stmt->bindParam(':request_id', $request_id);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':goal_count', $goal_count);
					$stmt->execute();
				}
			}
				$response['goal_count']	= $goal_count;
				echo  json_encode($response); exit;
			
		} else if($_POST['action'] == 'compare_status'){
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	$_SESSION['user_id'];	
			$comapre_status	= 	$_POST['compare_status'];	
			$select_query 	= 	"SELECT * FROM project_compare_graph WHERE request_id=:request_id";
			$select_stmt = $DBcon->prepare( $select_query );
			$select_stmt->bindParam(':request_id', $request_id);
			$select_stmt->execute();
			$results = $select_stmt->fetchAll();
			if(empty($results)) {
				$query = "INSERT INTO project_compare_graph(user_id,request_id, compare_status) VALUES(:user_id, :request_id, :compare_status)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':compare_status', $comapre_status);
				if (!$stmt->execute()) {
					print_r($stmt->errorInfo()); 
				}			
		}else{
				$delete_query 	= 	"DELETE FROM project_compare_graph WHERE user_id=:user_id AND request_id=:request_id";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				if($delete_stmt->execute()) {
					$query = "INSERT INTO project_compare_graph(user_id,request_id, compare_status) VALUES(:user_id, :request_id, :compare_status)";
					$stmt = $DBcon->prepare( $query );
					$stmt->bindParam(':request_id', $request_id);
					$stmt->bindParam(':user_id', $user_id);
					$stmt->bindParam(':compare_status', $comapre_status);
					if (!$stmt->execute()) {
						print_r($stmt->errorInfo()); 
					}			
				}
			}
				$response['status']	= 'success';
				echo  json_encode($response); exit;
		}
	}
?>
