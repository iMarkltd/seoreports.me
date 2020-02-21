<?php
function checkUserLoggedIn() {
	if(!isset($_SESSION['user_name']) && $_SESSION['user_id'] == ''){
		header ("Location: login.php");
		exit; // stop further executing, very important
	}		
}

function check_semrush_details($user_id) {
global $DBcon;
	$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute();
	// check for successfull registration
	if ($stmt->rowCount() > 0) {
		return 1;
	}else{
		return 0;
	}
}

function closestDates($session_count, $finddate) {
	$date = strtotime($finddate);
	$plot_month = date('Y,m', strtotime($finddate));
	foreach($session_count as $key=>$session_details) {
		$plot_dates[] 		=	strtotime($session_details['from_date']);
		$plot_dates2[] 		=	strtotime($session_details['from_date']);
		$check_dates = date('Y,m', strtotime($session_details['from_date']));
	}
	sort($plot_dates);
	$end_date = '';
	foreach ($plot_dates as $val) {
		if ($val >= $date) {
			$end_date = $val;
			break;
		}
	}

	rsort($plot_dates2);
	$start_date = '';
	foreach ($plot_dates2 as $val) {
		if ($val <= $date) {
			$start_date = $val;
			break;
		}
	}

	$index = array_search($end_date, $plot_dates);
	$day_diff = $end_date - $start_date;
	$count = floor($day_diff/(60*60*24)) / 10;
	$index = $index - $count;
	return $index;
}

function getUserDomainDetails($ids) {
global $DBcon;
	$query = "SELECT * FROM semrush_users_account WHERE id=:user_id AND status=0";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $ids);
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0]; exit;
	}else{
		return false;
	}	
}

function getUserDomainDetailsByToken($ids) {
global $DBcon;
	$query = "SELECT * FROM semrush_users_account WHERE token=:token AND status=0";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':token', $ids);
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0]; exit;
	}else{
		return false;
	}	
}

function checkSemarshApiData($ids, $domain_name, $request_id, $db_name){
	global $DBcon;
	$s		=	'';
	$data	=	'';
	$query = "SELECT * FROM semrush_organic_search_data WHERE user_id=:user_id AND domain_name=:domain_name";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $ids);
	$stmt->bindParam(':domain_name', $domain_name);
	$stmt->execute();
	// check for successfull registration
	$results = $stmt->fetchAll();
//	print_r($results); exit;

	$mostRecent= 0;
	foreach($results as $date){
		$curDate = strtotime($date['created']);
		if ($curDate > $mostRecent) {
			$mostRecent = $curDate;
		}
	}


	$check_date			=	date('Y-m-d',$mostRecent);

	$particular_date 	=	date('Y-m-d', strtotime(date('Y-m-10'))); 

	$current_date 		=	date('Y-m-d', strtotime(date('Y-m-d'))); 
	
 	if(strtotime($check_date) < strtotime($particular_date) && strtotime($particular_date) <= strtotime($current_date)) {
		$return_result = removeSemarshApiData($ids, $domain_name, $request_id);
		if($return_result){
			unset($results);
			$results = array();
		}
	}
 	if(!empty($results[0])) {
			return $results;
	}else{	
		$s 		= 	new SemRush($domain_name);
		$data	=	($s->getOrganicKeywordsReport($db_name));
		if(!empty($data)) {
			$count_query = "INSERT INTO semrush_api_unit(user_id,request_id, domain_url, record_count, api_status, api_type) 
								VALUES(:user_id, :request_id, :domain_url, :record_count, :api_status, 'domain_rank')";
				$status		=	1;
				$count_data =	count($data);
				$count_stmt = $DBcon->prepare( $count_query );
				$count_stmt->bindParam(':user_id', $ids);
				$count_stmt->bindParam(':request_id', $request_id);
				$count_stmt->bindParam(':domain_url', $domain_name);
				$count_stmt->bindParam(':record_count', $count_data);
				$count_stmt->bindParam(':api_status', $status);
				if (!$count_stmt->execute()) {
					print_r($count_stmt->errorInfo());  exit;
				}			
			//$count_stmt->debugDumpParams();

			foreach($data as $record) { 
//				$previous_position	=	$record['position_difference']+$record['pos'];
				$date_time		=	date('Y-m-10 H:i:s');
				$query = "INSERT INTO 
								semrush_organic_search_data(user_id,request_id,domain_name,keywords,position,previous_position,position_difference,search_volume,cpc,url,traffic,traffic_cost,number_results, created) 
								VALUES(:user_id, :request_id, :domain_name, :keywords, :position, :previous_position, :position_difference, :search_volume, :cpc, :url, :traffic, :traffic_cost, :number_results, :created)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':user_id', $ids);
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':domain_name', $domain_name);
				$stmt->bindParam(':keywords', $record['keyword']);
				$stmt->bindParam(':position', $record['position']);
//				$stmt->bindParam(':previous_position', $previous_position);
				$stmt->bindParam(':previous_position', $record['previous_position']);
				$stmt->bindParam(':position_difference', $record['position_difference']);
				$stmt->bindParam(':search_volume', $record['search_volume']);
				$stmt->bindParam(':cpc', $record['cpc']);
				$stmt->bindParam(':url', $record['url']);
				$stmt->bindParam(':traffic', $record['traffic_percent']);
				$stmt->bindParam(':traffic_cost', $record['traffic_cost_percent']);
				$stmt->bindParam(':number_results', $record['number_of_results']);
				$stmt->bindParam(':created', $date_time);
				if (!$stmt->execute()) {
					//print_r($stmt->errorInfo());  exit;
				}			
			}
				$result_query = "SELECT * FROM semrush_organic_search_data WHERE user_id=:user_id AND domain_name=:domain_name";
				$again_stmt = $DBcon->prepare( $result_query );
				$again_stmt->bindParam(':user_id', $ids);
				$again_stmt->bindParam(':domain_name', $domain_name);
				$again_stmt->execute();
				if (!$again_stmt->execute()) {
					//print_r($again_stmt->errorInfo()); 
				}			
				// check for successfull registration
				$results_data = $again_stmt->fetchAll();
				return $results_data;


		} else {
			return $data;
		}
			return $data;

	} 
	
}

function checkSemarshBacklinkData($ids, $domain_name, $request_id){
	global $DBcon;
	$sem	=	'';
	$data	=	'';
	$query = "SELECT * FROM semrush_backlinks_data WHERE user_id=:user_id AND domain_name=:domain_name AND request_id=:request_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $ids);
	$stmt->bindParam(':domain_name', $domain_name);
	$stmt->bindParam(':request_id', $request_id);
	if (!$stmt->execute()) {
		//print_r($stmt->errorInfo()); 
	}			

// check for successfull registration
	$results = $stmt->fetchAll();
	$mostRecent= 0;
	foreach($results as $date){
		$curDate = strtotime($date['created']);
		if ($curDate > $mostRecent) {
			$mostRecent = $curDate;
		}
	}
	// echo "<pre>";
	// print_r($results);
	if(!empty($results)) {
		$check_date			=	date('Y-m-d',$mostRecent);
		$particular_date 	=	date('Y-m-d', strtotime(date('Y-m-10'))); 
		$current_date 		=	date('Y-m-d', strtotime(date('Y-m-d'))); 
		
		if(strtotime($check_date) < strtotime($particular_date) && strtotime($particular_date) <= strtotime($current_date)) {
			$return_result = removeSemarshBacklinkData($ids, $domain_name, $request_id);
			if($return_result){
				unset($results);
				$results = array();
			}
		}
	}

 	if(!empty($results[0])) {
			return $results;
	}else{	
		$sem 	= 	new SemRush($domain_name);
		$data	=	($sem->getBacklinkReport());
		// print_r($data);die('aa');	
		if(!empty($data)) {
				$count_query = "INSERT INTO semrush_api_unit(user_id,request_id, domain_url, record_count, api_status, api_type) 
								VALUES(:user_id, :request_id, :domain_url, :record_count, :api_status, 'backlink')";
				$status		=	2;
				$count_data =	count($data);
				$count_stmt = $DBcon->prepare( $count_query );
				$count_stmt->bindParam(':user_id', $ids);
				$count_stmt->bindParam(':request_id', $request_id);
				$count_stmt->bindParam(':domain_url', $domain_name);
				$count_stmt->bindParam(':record_count', $count_data);
				$count_stmt->bindParam(':api_status', $status);
				if (!$count_stmt->execute()) {
					$count_stmt->debugDumpParams();

				}			
			
			foreach($data as $record) { 
					if(!empty($record)) {
						if(isset($record['page_score']) && !empty($record['page_score'])) {
						$date_time	=	 date('Y-m-10 H:i:s');
						$query = "INSERT INTO 
										semrush_backlinks_data(user_id,request_id,domain_name,page_score,page_trust_score,response_code,source_size,source_title,source_url, target_url, target_title, anchor, external_num, internal_num, first_seen, last_seen, nofollow, form, frame, image, sitewide, newlink, lostlink, redirect_url,image_url,image_alt, created) 
										VALUES(:user_id, :request_id, :domain_name, :page_score, :page_trust_score, :response_code, :source_size, :source_title, :source_url, :target_url, :target_title, :anchor, :external_num, :internal_num, :first_seen, :last_seen, :nofollow, :form, :frame, :image, :sitewide, :newlink, :lostlink, :redirect_url, :image_url, :image_alt, :created)";
						$new_stmt = $DBcon->prepare( $query );
						$new_stmt->bindParam(':user_id', $ids);
						$new_stmt->bindParam(':request_id', $request_id);
						$new_stmt->bindParam(':domain_name', $domain_name);
						$new_stmt->bindParam(':page_score', $record['page_score']);
						$new_stmt->bindParam(':page_trust_score', $record['page_trust_score']);
						$new_stmt->bindParam(':response_code', $record['response_code']);
						$new_stmt->bindParam(':source_size', $record['source_size']);
						$new_stmt->bindParam(':redirect_url', $record['redirect_url']);
						$new_stmt->bindParam(':image_url', $record['image_url']);
						$new_stmt->bindParam(':image_alt', $record['image_alt']);
						$new_stmt->bindParam(':source_title', $record['source_title']);
						$new_stmt->bindParam(':source_url', $record['source_url']);
						$new_stmt->bindParam(':target_url', $record['target_url']);
						$new_stmt->bindParam(':target_title', $record['target_title']);
						$new_stmt->bindParam(':anchor', $record['anchor']);
						$new_stmt->bindParam(':external_num', $record['external_num']);
						$new_stmt->bindParam(':internal_num', $record['internal_num']);
						$new_stmt->bindParam(':first_seen', $record['first_seen']);
						$new_stmt->bindParam(':last_seen', $record['last_seen']);
						$new_stmt->bindParam(':nofollow', $record['nofollow']);
						$new_stmt->bindParam(':form', $record['form']);
						$new_stmt->bindParam(':frame', $record['frame']);
						$new_stmt->bindParam(':image', $record['image']);
						$new_stmt->bindParam(':sitewide', $record['sitewide']);
						$new_stmt->bindParam(':newlink', $record['newlink']);
						$new_stmt->bindParam(':lostlink', $record['lostlink']);
						$new_stmt->bindParam(':created', $date_time);
						if (!$new_stmt->execute()) {
							print_r($new_stmt->errorInfo()); 
						}			
					}
				}
			}
				$result_query = "SELECT * FROM semrush_backlinks_data WHERE user_id=:user_id AND domain_name=:domain_name";
				$again_stmt = $DBcon->prepare( $result_query );
				$again_stmt->bindParam(':user_id', $ids);
				$again_stmt->bindParam(':domain_name', $domain_name);
				$again_stmt->execute();
				if (!$again_stmt->execute()) {
					var_dump($again_stmt->errorInfo());
//					var_dump($again_stmt->errorCode());
					$again_stmt->debugDumpParams();
				}			
				// check for successfull registration
				$results_data = $again_stmt->fetchAll();
				// exit;
				return $results_data;
		} else {
			return $data;
		}
		return $data;

	}
	
}

function checkSemarshDomainHistoryData($ids, $domain_name, $request_id, $db_name){
	global $DBcon;
	$sem	=	'';
	$data	=	'';
	$query = "SELECT *, UNIX_TIMESTAMP( date_time ) AS unix_date FROM semrush_domain_history WHERE user_id=$ids AND domain_name='".$domain_name."' AND request_id=$request_id ORDER BY id DESC";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $ids);
	$stmt->bindParam(':domain_name', $domain_name);
	$stmt->bindParam(':request_id', $request_id);
	$stmt->execute();
	// check for successfull registration
	$results = $stmt->fetchAll();
	$mostRecent= 0;
	foreach($results as $date){
		$curDate = strtotime($date['created']);
		if ($curDate > $mostRecent) {
			$mostRecent = $curDate;
		}
	}
	if(!empty($results)){		
		$check_date			=	date('Y-m-d',$mostRecent);
		$particular_date 	=	date('Y-m-d', strtotime(date('Y-m-10'))); 
		$current_date 		=	date('Y-m-d', strtotime(date('Y-m-d'))); 
		
		if(strtotime($check_date) < strtotime($particular_date) && strtotime($particular_date) <= strtotime($current_date)) {
			$return_result = removeSemarshDomainHistoryData($ids, $domain_name, $request_id);
			if($return_result){
				unset($results);
				$results = array();
			}
		}

	}
	if(!empty($results[0])) {
			return $results;
	}else{		
		$sem 	= 	new SemRush($domain_name);
		$data	=	($sem->getDomainHistory($db_name));
		if(!empty($data)) {
				$count_query = "INSERT INTO semrush_api_unit(user_id,request_id, domain_url, record_count, api_status, api_type) VALUES(:user_id, :request_id, :domain_url, :record_count, :api_status, 'domain_rank_history')";
				$status		=	2;
				$count_data =	count($data);
				$count_stmt = $DBcon->prepare( $count_query );
				$count_stmt->bindParam(':user_id', $ids);
				$count_stmt->bindParam(':request_id', $request_id);
				$count_stmt->bindParam(':domain_url', $domain_name);
				$count_stmt->bindParam(':record_count', $count_data);
				$count_stmt->bindParam(':api_status', $status);
				if (!$count_stmt->execute()) {
					//print_r($count_stmt->errorInfo()); 
				}			
			
			foreach($data as $record) { 
				$date_time	=	date('Y-m-10 H:i:s');	
				$query = "INSERT INTO 
								semrush_domain_history(user_id,request_id,domain_name,rank,organic_keywords,organic_traffic,organic_cost,adwords_keywords,adwords_traffic,adwords_cost, date_time, created) 
								VALUES(:user_id, :request_id, :domain_name, :rank, :organic_keywords, :organic_traffic, :organic_cost, :adwords_keywords, :adwords_traffic, :adwords_cost, :date_time, :created)";
				$new_stmt = $DBcon->prepare( $query );
				$new_stmt->bindParam(':user_id', $ids);
				$new_stmt->bindParam(':request_id', $request_id);
				$new_stmt->bindParam(':domain_name', $domain_name);
				$new_stmt->bindParam(':rank', $record['rank']);
				$new_stmt->bindParam(':organic_keywords', $record['organic_keywords']);
				$new_stmt->bindParam(':organic_traffic', $record['organic_traffic']);
				$new_stmt->bindParam(':organic_cost', $record['organic_cost']);
				$new_stmt->bindParam(':adwords_keywords', $record['adwords_keywords']);
				$new_stmt->bindParam(':adwords_traffic', $record['adwords_traffic']);
				$new_stmt->bindParam(':adwords_cost', $record['adwords_cost']);
				$new_stmt->bindParam(':date_time', $record['date']);
				$new_stmt->bindParam(':created', $date_time);
				if (!$new_stmt->execute()) {
					//print_r($new_stmt->errorInfo()); 
				}			
			}
				$result_query = "SELECT *, UNIX_TIMESTAMP( date_time ) AS unix_date  FROM semrush_domain_history WHERE user_id=:user_id AND domain_name=:domain_name ORDER BY id DESC";
				$again_stmt = $DBcon->prepare( $result_query );
				$again_stmt->bindParam(':user_id', $ids);
				$again_stmt->bindParam(':domain_name', $domain_name);
				$again_stmt->execute();
				if (!$again_stmt->execute()) {
					//print_r($again_stmt->errorInfo()); 
				}			
				// check for successfull registration
				$results_data = $again_stmt->fetchAll();
				return $results_data;
		} else {
			return $data;
		}
		return $data;

	}
	
}

function getDatafromUrl($url)
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function getUserImage($ids)
{
	global $DBcon;
	$data	=	'';
	$query = "SELECT * FROM users WHERE id=:user_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $ids);
	$stmt->execute();
	// check for successfull registration
	$results = $stmt->fetchAll();
	return $results;
	
}

function getFirstProfileId($analytics) {
  // Get the user's first view (profile) ID.
	global $analytics;

  // Get the list of accounts for the authorized user.
	$error	=	array();
 	try {
		$accounts = $analytics->management_accounts->listManagementAccounts();

	} catch(Exception $e) {
			$error	=	(json_decode($e->getMessage(), true));
	}	  
	if(empty($error['error']['code']) || $error['error']['code'] == 0) {	
		  if (count($accounts->getItems()) > 0) {
			$items = $accounts->getItems();
			$items['accounts'] = $accounts->getItems();
			$firstAccountId = $items[0]->getId();

			// Get the list of properties for the authorized user.
			$properties = 
			$analytics->management_webproperties
				->listManagementWebproperties($firstAccountId);

			if (count($properties->getItems()) > 0) {
			  $items = $properties->getItems();
			  $items['property'] = $properties->getItems();
			  $firstPropertyId = $items[0]->getId();

			  // Get the list of views (profiles) for the authorized user.
			  $profiles = $analytics->management_profiles
				  ->listManagementProfiles($firstAccountId, $firstPropertyId);

			  if (count($profiles->getItems()) > 0) {
				$items = $profiles->getItems();
				$items['profiles'] = $profiles->getItems();

				// Return the first view (profile) ID.
				//return $items[0]->getId();
				return $items;

			  }
			}
		  } 
	}else{
		return 'false';
	}
}

function getFirstPropertyId($analytics) {
  // Get the user's first view (profile) ID.

  // Get the list of accounts for the authorized user.
	$error	=	array();
 	try {
		$accounts = $analytics->management_accounts->listManagementAccounts();

	} catch(Exception $e) {
			$error	=	(json_decode($e->getMessage(), true));
	}	  
	if(empty($error['error']['code']) || $error['error']['code'] == 0) {	
		  if (count($accounts->getItems()) > 0) {
			$items = $accounts->getItems();
			$items['accounts'] = $accounts->getItems();
			$firstAccountId = $items[0]->getId();

			// Get the list of properties for the authorized user.
			$properties = 
			$analytics->management_webproperties
				->listManagementWebproperties($firstAccountId);

			if (count($properties->getItems()) > 0) {
			  $items = $properties->getItems();
			  $items['property'] = $properties->getItems();
			  $firstPropertyId = $items[0]->getId();

			  // Get the list of views (profiles) for the authorized user.
			  $profiles = $analytics->management_profiles
				  ->listManagementProfiles($firstAccountId, $firstPropertyId);

			  if (count($profiles->getItems()) > 0) {
				$items = $profiles->getItems();
				$view_id = $profiles->getItems();
				//print_r($profile); exit;
				// Return the first view (profile) ID.
				//return $items[0]->getId();
				return $items[0]->getId();

			  } else {
				throw new Exception('No views (profiles) found for this user.');
			  }
			} else {
			  throw new Exception('No properties found for this user.');
			}
		  } else {
			throw new Exception('No accounts found for this user.');
		  }
	}else{
		return 'false';
	}
}

function getResults($analytics, $profileId) {
  // Calls the Core Reporting API and queries for the number of sessions
  // for the last seven days.
	try {
		 return $analytics->data_ga->get(
		  'ga:' . $profileId,
		  '7daysAgo',
		  'today',
		  'ga:sessions');
		  
	} catch(Exception $e) {
			echo 'There was an error : - ' . $e->getMessage();
	}	  
}

function printResults($results) {
  // Parses the response from the Core Reporting API and prints
  // the profile name and total sessions.
  if (count($results->getRows()) > 0) {

    // Get the profile name.
    $profileName = $results->getProfileInfo()->getProfileName();

    // Get the entry for the first entry in the first row.
    $rows = $results->getRows();
    $sessions = $rows[0][0];

    // Print the results.
    print "<p>First view (profile) found: $profileName</p>";
    print "<p>Total sessions: $sessions</p>";
  } else {
    print "<p>No results found.</p>";
  }
}


function printManagmentApi($service) {
	// request user accounts
	$accounts = $service->management_accountSummaries->listManagementAccountSummaries();

	return $accounts;
}

function googleAnalytics($request_id){
	global $DBcon;
	global $client;
	$arr	= array();
	$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
	$client->setAccessType('offline');
	$query = "SELECT * FROM google_analytics_users WHERE user_id=:user_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	if ($stmt->rowCount() > 0) {
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$_SESSION['access_token'] 	= $row['google_access_token'];
			$_SESSION['refresh_token'] 	= $row['google_refresh_token'];
			$_SESSION['service_token']['access_token'] = $row['google_access_token'];
			$_SESSION['service_token']['token_type'] = $row['token_type'];
			$_SESSION['service_token']['expires_in'] = $row['expires_in'];
			$_SESSION['service_token']['id_token'] = $row['id_token'];
			$_SESSION['service_token']['created'] = $row['service_created']; 
		}			
	}

	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		//Get user profile data from google
		// Set the access token on the client. 
		$_tokenArray	=	json_encode($_SESSION['service_token']);
		$client->setAccessToken($_tokenArray);
		if ($client->isAccessTokenExpired()) {
			$client->refreshToken($_SESSION['refresh_token']);
		}
		// Create an authorized analytics service object.
		$analytics = new Google_Service_Analytics($client);
		// Get the first view (profile) id for the authorized user.
		$profile 		= getFirstProfileId($analytics);
		$property_id 	= getFirstPropertyId($analytics);
		// Get the results from the Core Reporting API and print the results.
	/*	if($profile != 'false') {
			$results = getResults($analytics, $profile);
			printResults($results);
		}else{
			echo "You dont have google analytics service account. if you want to use this service please enable it" ;
		}
		
	*/	
		$myToken	= 	$client->getAccessToken();
		$arr		=	$myToken;
		$arr		= 	array('property_id' => $property_id, 'access_token' => $myToken['access_token']); 
		return $arr;

	} else {
		echo $redirect_uri = FULL_PATH.'/php-auth.php?id='.$request_id;
		header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	}
}

function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

	$date1  = $strDateFrom;
	$date2  = $strDateTo;
	$output = [];
	$time   = strtotime($date1);
	$last   = date('m-Y', strtotime($date2));

	do {
		$month = date('m-Y', $time);

		$output[] = [
			'month' => $time
		];

		$time = strtotime('+1 month', $time);
	} 
	while ($month != $last);	

    return $output;
	
}

function googleAnalyticsWithoutLogin($user_id, $ids){
	global $DBcon;
	global $client;
	global $analytics;
	
	$user_id	=	$user_id;
	$query 		= 	"SELECT * FROM semrush_users_account WHERE user_id=:user_id AND id=:id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':id', $ids);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$records = $stmt->fetch();
	if(!empty($records)) {
		$results							= googleAccessDetailsWithoutLogin($records['google_account_id'], $user_id);
		if(!empty($results)) {
			$refresh_token				 	= $results['google_refresh_token'];
			$service_token['access_token'] 	= $results['google_access_token'];
			$service_token['token_type'] 	= $results['token_type'];
			$service_token['expires_in']	= $results['expires_in'];
			$service_token['id_token'] 		= $results['id_token'];
			$service_token['created'] 		= $results['service_created']; 						
			$_tokenArray					= json_encode($service_token);
			
			$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
			$client->setAccessType('offline');
			$client->setAccessToken($_tokenArray);
			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($refresh_token);
			}

			$myToken	= 	$client->getAccessToken();
		}
		$response['status'] = 'success';
		if(!empty($myToken)) {
			$response['access_token'] 			= $myToken['access_token'];
			$response['analytic_id']  			= $myToken['access_token'];
		}else{
			$response['access_token'] 			=	'';
			$response['analytic_id']  			= 	'';
		}
		$response['status'] = 'success';
		$response['property_id']  			= $records['google_profile_id'];
		$response['id']			  			= $records['google_analytics_id'];
		$response['google_property_id']  	= $records['google_property_id'];
		#print_r($response); #exit;
		return	$response; exit;
	} else {
		return null;
	}
}

function checkGoogleAuthorizeToken($user_id){
	global $DBcon;
	$query 	= "SELECT * FROM google_analytics_users WHERE user_id=:user_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	if ($stmt->rowCount() > 0) {	
		return true;
	}else{
		return false;
	}
}

function googleAccountList(){
	global $DBcon;
	$user_id		=	$_SESSION['user_id'];
	$account_status	=	'1';
	$query 	= "SELECT * FROM google_analytics_users WHERE user_id=:user_id ";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	//$stmt->bindParam(':account_status', $account_status);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results; exit;
	}else{
		return false;
	}	
}

function googleAccessDetails($id){
	$result = null;
	global $DBcon;
	global $client;
	$user_id	=	$_SESSION['user_id'];
	$query 		= 	"SELECT * FROM google_analytics_users WHERE user_id=:user_id AND id=:id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$results = $stmt->fetch();
	if(!empty($results)) {
		return $results;
	}else{
		return false;
	}	
}

function googleAccessDetailsWithoutLogin($ids, $user_id){
	global $DBcon;
	global $client;
	$query 		= 	"SELECT * FROM google_analytics_users WHERE user_id=:user_id AND id=:id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':id', $ids);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetch();
	if(!empty($results)) {
		return $results;
	}else{
		return false;
	}	
}

function googleAnalyticsMulitipleAccount($ids){
	global $DBcon;
	global $client;
	$arr	= array();
	$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
	$client->setAccessType('offline');
	$redirect_uri = FULL_PATH.'/auth.php?id='.$ids;
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

function getDomainDetails($ids){
	global $DBcon;
	global $client;
	global $analytics;
	
	$user_id	=	$_SESSION['user_id'];
	$query 		= 	"SELECT * FROM semrush_users_account WHERE user_id=:user_id AND id=:id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':id', $ids);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$records = $stmt->fetch();
	if(!empty($records)) {
		$results							= googleAccessDetails($records['google_account_id']);
		if(!empty($results)) {
			$refresh_token				 	= $results['google_refresh_token'];
			$service_token['access_token'] 	= $results['google_access_token'];
			$service_token['token_type'] 	= $results['token_type'];
			$service_token['expires_in']	= $results['expires_in'];
			$service_token['id_token'] 		= $results['id_token'];
			$service_token['created'] 		= $results['service_created']; 						
			$_tokenArray					= json_encode($service_token);
			
			$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
			$client->setAccessType('offline');
			$client->setAccessToken($_tokenArray);
			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($refresh_token);
			}
			$analytics = new Google_Service_Analytics($client);
			// Get the first view (profile) id for the authorized user.
			$profile 		= getFirstProfileId($analytics);
			$property_id 	= getFirstPropertyId($analytics);

			$myToken	= 	$client->getAccessToken();
		}
		$response['status'] = 'success';
		if(!empty($myToken)) {
			$response['access_token'] 			= $myToken['access_token'];
			$response['analytic_id']  			= $myToken['access_token'];
		}else{
			$response['access_token'] 			=	'';
			$response['analytic_id']  			= 	'';
		}
		$response['property_id']  			= $records['google_profile_id'];
		$response['google_property_id']  	= $records['google_property_id'];
		$response['id']			  			= $records['google_account_id'];
		$response['google_analytics_id']	= $records['google_analytics_id'];		
		#print_r($response); #exit;
		return	$response; exit;
	} else {
		return null;
	}
}

function getShareKeyUrl($post_id) {
	global $DBcon;
	$query 	= 	"SELECT * FROM semrush_users_account WHERE id IN ($post_id) ";
	$stmt 	= 	$DBcon->prepare( $query );
	if($stmt->execute()) {			
		$results = $stmt->fetch();
		return $results['token'];
	} else {
		return false;
	}	
	
}

function getShareKeyData($post_id) {
	global $DBcon;
	$query 	= 	"SELECT * FROM semrush_users_account WHERE id IN ($post_id) ";
	$stmt 	= 	$DBcon->prepare( $query );
	if($stmt->execute()) {			
		$results = $stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}

function googleAnalyticsSession($user_id, $request_id, $access_token) {
	
	global $DBcon;
	$select_query 	= 	"SELECT *, WEEK( from_date ) AS DWNum, SUM( count_session ) AS total_session FROM google_profile_session WHERE user_id=:user_id AND request_id=:request_id GROUP BY DWNum ORDER by id ASC";

	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetchAll();
		return $results;
	} else {
		return false;
	}	
	
}

function googleAnalyticsProfileData($user_id, $request_id, $access_token) {
	
	global $DBcon;
	$select_query 	= 	"SELECT * FROM google_profile_data WHERE user_id=:user_id AND request_id=:request_id ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetchAll();
		return $results;
	} else {
		return false;
	}	
	
}

function googleAnalyticsProfileOldData($user_id, $request_id, $access_token) {
	
	global $DBcon;
	$select_query 	= 	"SELECT * FROM google_previous_old_data WHERE user_id=:user_id AND request_id=:request_id ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}

function checkEditNotes0($user_id, $request_id) {
	global $DBcon;
	$select_query 	= 	"SELECT * FROM seo_analytics_edit_secion WHERE user_id=:user_id AND request_id=:request_id AND edit_area='0' ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}

function checkEditNotes1($user_id, $request_id, $edit_area) {
	
	global $DBcon;
	$select_query 	= 	"SELECT * FROM seo_analytics_edit_secion WHERE user_id=:user_id AND request_id=:request_id AND edit_area=:edit_area ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	$select_stmt->bindParam(':edit_area', $edit_area);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}


function checkGoogleGoal($user_id, $request_id) {
	
	global $DBcon;
	$select_query 	= 	"SELECT * FROM google_goal_completion WHERE user_id=:user_id AND request_id=:request_id ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}

function checkEmailStatus($request_id) {
	
	global $DBcon;
	$user_id		=	$_SESSION['user_id'];
	$select_query 	= 	"SELECT * FROM user_email_details WHERE user_id=:user_id AND request_id=:request_id ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}

function checkCurrentGoogleGoal($user_id, $request_id) {
	global $DBcon;
	$select_query 	= 	"SELECT SUM(goal_completions) AS total FROM google_profile_data WHERE user_id=:user_id AND request_id=:request_id ORDER BY id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $user_id);
	$select_stmt->bindParam(':request_id', $request_id);
	if($select_stmt->execute()) {			
		$results = $select_stmt->fetch();
		return $results;
	} else {
		return false;
	}	
	
}

function parseUrl($url){
	$input = $url;

	// in case scheme relative URI is passed, e.g., //www.google.com/
	$input = trim($input, '/');

	// If scheme not included, prepend it
	if (!preg_match('#^http(s)?://#', $input)) {
		$input = 'http://' . $input;
	}

	$urlParts = parse_url($input);

	// remove www
	$domain = preg_replace('/^www\./', '', $urlParts['host']);

	return $domain;
	
}	

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function getPDFDownloadList($type=''){
	global $DBcon;
	$li		=	'';
	$user_id 	=	$_SESSION['user_id'];
	if(empty($type)) {
		$query 		= 	"SELECT * FROM download_pdf_request WHERE user_id IN ($user_id) AND download_status != 0";
		$li	=	'<li class="dropdown-header"><div class="pull-left">PDF Files</div> <div class="pull-right"><a href="#" class="remove_all_pdf">Remove All</a></div></li>';
	}
	else {
		$query 		= 	"SELECT * FROM download_pdf_request WHERE user_id IN ($user_id) Order By id DESC Limit 1";
	}
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		foreach($results as $result) { 
				$variable 		=	$result['request_id'];
				$ids	  		= 	$result['id'];
				$salt 			= 	'@dmin1@1';
				$hash			=	base64_encode($salt.$variable);
				$get_file_name	=	getShareKeyData($result['request_id']);
				$status 		=	$result['download_status'] == 1 ? '' : 'unread';
				$time 			=	time_elapsed_string($result['created']);
				$li				.=	'<li class="'.$status.'"><a href="download_pdf_cron.php?ids='.$hash.'" target="_blank" class="jquery_pdf"><strong>'.$get_file_name['domain_name'].' </strong><summary>PDF, <time>'.$time.'</time></summary></a> <div class="remove-drop-pdf" data-id="'.$ids.'"></div></li>';
		}
	}else{
			$li	.=	'<li class="not_found"><strong>No download link Found</strong></li>';
	}
	
	return $li;
}

function getUnreadNotification(){
	global $DBcon;
	$user_id 	=	$_SESSION['user_id'];
	$query 		= 	"SELECT count(id) as total FROM download_pdf_request WHERE user_id IN ($user_id) AND download_status = 1";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return $results[0];
	}else{
		return null;
	}
}

function checkDownloadLink($ids){
	global $DBcon;
	$user_id 	=	$_SESSION['user_id'];
	$query 		= 	"SELECT * FROM download_pdf_request WHERE user_id IN ($user_id) AND request_id = ($ids) ";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetch();
	
	return $results;
}

function checkPDFExists($url){
	if(file_exists($url)) {
		return true;
	}else {
		return false;
	}
	
}

function checkMozData($url, $request_id, $user_id=null){
	global $DBcon;
	if(empty($user_id))
		$user_id 	=	$_SESSION['user_id'];
	$query 		= 	"SELECT id, page_authority as pageAuthority, domain_authority as domainAuthority, created_at FROM moz_data WHERE user_id IN ($user_id) AND request_id = $request_id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetch();
	if(!empty($results)){
		if(strtotime($results['created_at']) < strtotime('-30 days')) {
			$delete_query	=	"DELETE FROM moz_data WHERE user_id=:user_id AND request_id=:request_id";
			$delete_stmt	=	$DBcon->prepare($delete_query);
			$delete_stmt->bindParam(":user_id", $user_id);
			$delete_stmt->bindParam(":request_id", $request_id);
			$delete_stmt->execute();
			// $items = $accounts->getItems();

			$get_moz_results	=	getMozData($url);
			$query				=	"INSERT INTO moz_data(page_authority, domain_authority, user_id, request_id) VALUES(:page_authority, :domain_authority,  :user_id, :request_id)";
			$moz_stmt	=	$DBcon->prepare($query);
			$moz_stmt->bindParam(":user_id", $user_id);
			$moz_stmt->bindParam(":request_id", $request_id);
			$moz_stmt->bindParam(":page_authority", $get_moz_results['pageAuthority']);
			$moz_stmt->bindParam(":domain_authority", $get_moz_results['domainAuthority']);
			if (!$moz_stmt->execute()) {
				print_r($moz_stmt->errorInfo());  exit;
			}			

			$data		=	$get_moz_results;
		} else {
			$data		=	$results;
		}
	}else{
		$get_moz_results	=	getMozData($url);
		$query				=	"INSERT INTO moz_data(page_authority, domain_authority, user_id, request_id) VALUES(:page_authority, :domain_authority,  :user_id, :request_id)";
		$moz_stmt	=	$DBcon->prepare($query);
		$moz_stmt->bindParam(":user_id", $user_id);
		$moz_stmt->bindParam(":request_id", $request_id);
		$moz_stmt->bindParam(":page_authority", $get_moz_results['pageAuthority']);
		$moz_stmt->bindParam(":domain_authority", $get_moz_results['domainAuthority']);
		if (!$moz_stmt->execute()) {
			print_r($moz_stmt->errorInfo());  exit;
		}			

		$data		=	$get_moz_results;
	}

/*
	//ishan@imarkinfotech.com Account Api
	$accessID = "mozscape-4ebb04ded4"; // * Add unique Access ID
	$secretKey = "555bf047e36f622f6005f137ab4174ca"; // * Add unique Secret Key
*/
	//shweta.sadana@imarkinfotech.com Account Api

	return $data; exit;
}

function checkUserProfileImage(){
	
	$user_id		=	@$_SESSION['user_id'];
	if(!empty($user_id)) {
		$path 			= 	'assets/ajax/uploads/'.$user_id.'/profile/';
		if (file_exists($path)) {
			$files1 					= 	array_values(array_diff(scandir($path), array('..', '.')));
			if(!empty($files1)) {
				$response['return_path']	=	$path.($files1[0]); 
				$response['file_name']		=	$files1[0];
			}else{
				$response	=	'';
			}
		} else{
				$response	=	'';
		}
	} else {
				$response	=	'';
	}
	return $response; exit;

}

function checkProfileLogo($request_id){
	
	$user_id		=	$_SESSION['user_id'];
	$path 			= 	'assets/ajax/uploads/'.$user_id.'/'.$request_id."/";
	
	if (file_exists($path)) {
		$files1 					= 	array_values(array_diff(scandir($path), array('..', '.')));
		if(!empty($files1)) {
			$response['return_path']	=	$path.($files1[0]); 
			$response['file_name']		=	$files1[0];
		}else{
			$response	=	'';
		}
		return $response; exit;
	}
	
}


function sendEmail($from_email, $from_sender, $to, $file_name, $subject, $message, $url) {
	$to_emails	= 	explode(', ', ($to));
	
	$mail = new PHPMailer;
				$mail->SMTPDebug = 1;                               	// Enable verbose debug output
				//$mail->isSMTP();                                      	// Set mailer to use SMTP
				$mail->Host = 'in-v3.mailjet.com';  					// Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               	// Enable SMTP authentication
				$mail->Username = '620b20dfedf9de5cccbf1ed4b21971a0';   // SMTP username
				$mail->Password = 'ba4f00c99cd8348a9ddc12bc0a30ce9b';   // SMTP password
				$mail->SMTPSecure = 'ssl';                              // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 465;                                      // TCP port to connect to
				$mail->SMTPOptions = array(
					'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
					)
				);
				$mail->setFrom($from_email, $from_sender);
				foreach($to_emails as $to_email) { 
					$mail->addAddress($to_email, $to_email);
				}
				$mail->addReplyTo($from_email, 'Information');
				$mail->addAttachment($file_name, 'SEO PERFORMANCE REPORT - '.$url.'.pdf');    // Optional name
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $subject;
				$mail->Body    = $message;
				if($mail->send()) {
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error'; // could not register
				}		
	
	return $response;
}

function getStartCron() {
	return 'test'; exit;
}

function checkOauthId($oauthID){
	global $DBcon;
	$user_id 	=	$_SESSION['user_id'];
	$query 		= 	"SELECT * FROM google_analytics_users WHERE user_id IN ($user_id) AND oauth_uid = $oauthID";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return $results[0];
	}else{
		return null;
	}
}

function getTrafficCost($user_id, $domain_name, $domain_id){
	global $DBcon;
	$user_id 	=	$_SESSION['user_id'];
	$query 		= 	"SELECT sum(`traffic_cost`) as cost FROM `semrush_organic_search_data` WHERE user_id = ".$user_id." AND request_id =".$domain_id." AND domain_name = '".$domain_name."'";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return $results[0];
	}else{
		return null;
	}
}

function getAccountList($analytics, $register_id, $google_account_id) {
  // Get the user's first view (profile) ID.
	global $analytics;
	global $DBcon;
	$user_id	=	$_SESSION['user_id'];
  // Get the list of accounts for the authorized user.
	$error	=	array();
	try {
		$accounts = $analytics->management_accounts->listManagementAccounts();

	} catch(Exception $e) {
		$error	=	(json_decode($e->getMessage(), true));
	}	  
	if(empty($error['error']['code']) || $error['error']['code'] == 0) {	
		if (count($accounts->getItems()) > 0) {
			$delete_query	=	"DELETE FROM google_account_view_data WHERE user_id=:user_id AND google_account_id=:google_account_id";
			$delete_stmt	=	$DBcon->prepare($delete_query);
			$delete_stmt->bindParam(":user_id", $user_id);
			$delete_stmt->bindParam(":google_account_id", $google_account_id);
			$delete_stmt->execute();
			$items = $accounts->getItems();
			foreach($items as $item) {
				$account_id		=	$item->getId();
				$account_name	=	$item->name;
				$query			=	"INSERT INTO google_account_view_data(category_name, category_id, google_account_id, user_id, request_id) VALUES(:category_name, :category_id, :google_account_id,  :user_id, :request_id)";
				$account_stmt	=	$DBcon->prepare($query);
				$account_stmt->bindParam(":user_id", $user_id);
				$account_stmt->bindParam(":request_id", $register_id);
				$account_stmt->bindParam(":google_account_id", $google_account_id);
				$account_stmt->bindParam(":category_name", $account_name);
				$account_stmt->bindParam(":category_id", $account_id);
				if($account_stmt->execute()) {
					$last_id			=	$DBcon->lastInsertId();
					$properties 		= 	$analytics->management_webproperties->listManagementWebproperties($account_id);
					if (count($properties->getItems()) > 0) {
						$property_all	= 	$properties->getItems();
						foreach($property_all as $property_single) {
							$property_id		=	$property_single->getId();
							$property_name		=	$property_single->name;
							$property_stmt		=	$DBcon->prepare("INSERT google_account_view_data SET category_name=:category_name, category_id=:category_id, google_account_id=:google_account_id, parent_id=:parent_id, user_id=:user_id, request_id=:request_id");
							$property_stmt->bindParam(":user_id", $user_id);
							$property_stmt->bindParam(":request_id", $register_id);
							$property_stmt->bindParam(":category_name", $property_name);
							$property_stmt->bindParam(":category_id", $property_id);
							$property_stmt->bindParam(":google_account_id", $google_account_id);
							$property_stmt->bindParam(":parent_id", $last_id);
							if($property_stmt->execute()) {
								$property_last_id	=	$DBcon->lastInsertId();
								$profiles = $analytics->management_profiles->listManagementProfiles($account_id, $property_id);
								if (count($profiles->getItems()) > 0) {
									$profiles_all	= 	$profiles->getItems();
									foreach($profiles_all as $profiles) {
										$profiles_id		=	$profiles->getId();
										$profiles_name		=	$profiles->name;
										$profiles_stmt		=	$DBcon->prepare("INSERT google_account_view_data SET category_name=:category_name, category_id=:category_id, google_account_id=:google_account_id, parent_id=:parent_id, user_id=:user_id, request_id=:request_id");
										$profiles_stmt->bindParam(":user_id", $user_id);
										$profiles_stmt->bindParam(":request_id", $register_id);
										$profiles_stmt->bindParam(":category_id", $profiles_id);
										$profiles_stmt->bindParam(":category_name", $profiles_name);
										$profiles_stmt->bindParam(":google_account_id", $google_account_id);
										$profiles_stmt->bindParam(":parent_id", $property_last_id);
										$profiles_stmt->execute() or die(print_r($profiles_stmt->errorInfo(), true));
									}
								}
							}else{
								print_r($property_stmt->errorinfo());
							}
						}
					}
				}else{
					print_r($account_stmt->errorinfo());
				}
			}
		}
	}else{
		return 'false';
	}
}

function getViewAccountLits(){
	global $client;
	
}

function getAnalytcsDomainName($request_id, $account_id){

	global $DBcon;
	$user_id	=	$_SESSION['user_id'];
  // Get the list of accounts for the authorized user.
	$query 		=	"SELECT * FROM google_account_view_data WHERE user_id =:user_id AND google_account_id=:google_account_id AND parent_id = 0";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam('user_id', $user_id);
	$stmt->bindParam('google_account_id', $account_id);
	$stmt->execute();
	$results = $stmt->fetchAll();
	return $results;
}

function getGoogleAnalyticsProperty($property_id){
	global $DBcon;
	$query 		=	"SELECT * FROM google_account_view_data WHERE parent_id =:parent_id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam('parent_id', $property_id);
	$stmt->execute();
	$results = $stmt->fetchAll();
	return $results;
}

function getGoogleAnalyticId($user_id, $oauth_uid){
	global $DBcon;
	$query 		=	"SELECT * FROM google_analytics_users WHERE user_id =:user_id AND oauth_uid =:oauth_uid";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam('user_id', $user_id);
	$stmt->bindParam('oauth_uid', $oauth_uid);
	$stmt->execute();
	$results = $stmt->fetchAll();
	return $results[0];
}

function urlToDomain($url) {
   if ( substr($url, 0, 8) == 'https://' ) {
      $url = substr($url, 8);
   }
   if ( substr($url, 0, 7) == 'http://' ) {
      $url = substr($url, 7);
   }
   if ( substr($url, 0, 4) == 'www.' ) {
      $url = substr($url, 4);
   }
   if ( strpos($url, '/') !== false ) {
      $explode = explode('/', $url);
      $url     = $explode['0'];
   }
   return $url;
}

function getProfileData($ids, $user_id){
	global $DBcon;
	$query 		=	"SELECT * FROM profile_info WHERE user_id =:user_id AND request_id =:request_id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam('user_id', $user_id);
	$stmt->bindParam('request_id', $ids);
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)){
		return $results[0];
	}else 
	return null;
	
}
function apiSemrushDataUpdateAfter30Days($user_id=null, $domain_name=null, $request_id=null){
	global $DBcon;
	$delete_query	=	"DELETE FROM semrush_organic_search_data";
	$delete_stmt	=	$DBcon->prepare($delete_query);
	$delete_stmt->execute();
	// check for successfull registration
	return 1;
	
}

function apiBacklinkDataUpdateAfter30Days($user_id=null, $domain_name=null, $request_id=null){
	global $DBcon;
	$delete_query	=	"DELETE FROM semrush_backlinks_data";
	$delete_stmt	=	$DBcon->prepare($delete_query);
	$delete_stmt->execute();
	// check for successfull registration
	return 1;
	
}


function updateGoogleProfileData($request_id, $analytics_id){
	global $DBcon;
	global $client;
	global $analytics;
	$client = new Google_Client();
	$user_id	=	$_SESSION['user_id'];
	$results	= 	googleAccessDetails($analytics_id);
	if(!empty($results)) {
			$refresh_token				 	= $results['google_refresh_token'];
			$service_token['access_token'] 	= $results['google_access_token'];
			$service_token['token_type'] 	= $results['token_type'];
			$service_token['expires_in']	= $results['expires_in'];
			$service_token['id_token'] 		= $results['id_token'];
			$service_token['created'] 		= $results['service_created']; 						
			$_tokenArray					= json_encode($service_token);
			
			$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
			$client->setAccessType('offline');
			$client->setAccessToken($_tokenArray);
			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($refresh_token);
			}
			$analytics 			= 	new Google_Service_Analytics($client);
			//$analytics_id 		=	getGoogleAnalyticId($_SESSION['user_id'], $results['oauth_uid']);
			$result				=	 getAccountList($analytics, $request_id, $analytics_id);
			return $result;
			// Get the first view (profile) id for the authorized user.
	} else {
		return null;
	}
}

function checkDomainNameExists($url_host){
	global $DBcon;
	$user_id	=	$_SESSION['user_id'];
	$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id AND domain_url like concat('%', :domain_url, '%')";
	$stmt = $DBcon->prepare( $query );
	$domain_url		=	'"%'.$url_host.'%"';
	$stmt->bindParam(':domain_url', $url_host, PDO::PARAM_STR);
	$stmt->bindParam(':user_id', $user_id);
	$stmt->execute();
	// check for successfull registration
	if ($stmt->rowCount() > 0) {
		return 1;
	}else{
		return 0;
	}
	
}

function removeSemarshApiData($user_id, $domain_name, $request_id){
	global $DBcon;
	$delete_query	=	"DELETE FROM semrush_organic_search_data WHERE user_id=:user_id AND domain_name=:domain_name AND request_id=:request_id";
	$delete_stmt	=	$DBcon->prepare($delete_query);
	$delete_stmt->bindParam(":user_id", $user_id);
	$delete_stmt->bindParam(":domain_name", $domain_name);
	$delete_stmt->bindParam(":request_id", $request_id);
	$delete_stmt->execute();
	// check for successfull registration
	return 1;
	
}

function removeSemarshBacklinkData($user_id, $domain_name, $request_id){
	global $DBcon;
	$delete_query	=	"DELETE FROM semrush_backlinks_data WHERE user_id=:user_id AND domain_name=:domain_name AND request_id=:request_id";
	$delete_stmt	=	$DBcon->prepare($delete_query);
	$delete_stmt->bindParam(":user_id", $user_id);
	$delete_stmt->bindParam(":domain_name", $domain_name);
	$delete_stmt->bindParam(":request_id", $request_id);
	$delete_stmt->execute();
	// check for successfull registration
		return 1;
	
}

function removeSemarshDomainHistoryData($user_id, $domain_name, $request_id){
	global $DBcon;
	$delete_query	=	"DELETE FROM semrush_domain_history WHERE user_id=:user_id AND domain_name=:domain_name AND request_id=:request_id";
	$delete_stmt	=	$DBcon->prepare($delete_query);
	$delete_stmt->bindParam(":user_id", $user_id);
	$delete_stmt->bindParam(":domain_name", $domain_name);
	$delete_stmt->bindParam(":request_id", $request_id);
	$delete_stmt->execute();
	// check for successfull registration
		return 1;
	
}

function getGoogleAnalyticsDetails($id){
	$user_id	=	$_SESSION['user_id'];
	global $DBcon;
	$query 		=	"SELECT * FROM google_account_view_data WHERE user_id=:user_id AND id =:id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(":user_id", $user_id);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	$results = $stmt->fetch();
	//print_r($results); exit;
	return $results;
}

function getGoogleAnalyticsProfileDetails($id){
	$user_id	=	$_SESSION['user_id'];
	global $DBcon;
	$query 		=	"SELECT * FROM google_account_view_data WHERE user_id=:user_id AND category_id =:id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(":user_id", $user_id);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	$results = $stmt->fetch();
	//print_r($results); exit;
	return $results;
}

function updateGoogleAccountData($analytics_id){
	global $DBcon;
	global $client;
	global $analytics;
	$client 	= 	new Google_Client();
	$user_id	=	$_SESSION['user_id'];
	$results	= 	googleAccessDetails($analytics_id);
	if(!empty($results)) {
			$refresh_token				 	= $results['google_refresh_token'];
			$service_token['access_token'] 	= $results['google_access_token'];
			$service_token['token_type'] 	= $results['token_type'];
			$service_token['expires_in']	= $results['expires_in'];
			$service_token['id_token'] 		= $results['id_token'];
			$service_token['created'] 		= $results['service_created']; 						
			$_tokenArray					= json_encode($service_token);
			$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
			$client->setAccessType('offline');
			$client->setAccessToken($_tokenArray);
			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($refresh_token);
			}
			$analytics 			= 	new Google_Service_Analytics($client);
			//$analytics_id 		=	getGoogleAnalyticId($_SESSION['user_id'], $results['oauth_uid']);
			$request_id			=	'0';
			$result				=	 getAccountList($analytics, $request_id, $analytics_id);
			return $result;
			// Get the first view (profile) id for the authorized user.
	} else {
		return null;
	}
}


function getApiUnitList(){
	global $DBcon;
	$query 		= 	"SELECT * FROM semrush_api_unit order by created desc";
	$stmt 		= 	$DBcon->prepare( $query );
	//$stmt->bindParam(':account_status', $account_status);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results; exit;
	}else{
		return false;
	}	
	
}

function getDFSApiUnitList(){
	global $DBcon;
	$query 		= 	"SELECT * FROM dataforseo_api_unit order by created_at desc";
	$stmt 		= 	$DBcon->prepare( $query );
	//$stmt->bindParam(':account_status', $account_status);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results; exit;
	}else{
		return false;
	}	
	
}

function getApiUnit()
{
	$url		=	'http://www.semrush.com/users/countapiunits.html?key=4db1115329e0af57fffb04585e81b549';
	$homepage 	= file_get_contents($url);
	return $homepage;
}


function getPositionData($request_id, $from, $end){
	global $DBcon;
	$query 		= 	"SELECT count(request_id) as total FROM `semrush_organic_search_data` WHERE `request_id` = $request_id AND `position` BETWEEN $from and $end";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0];
	}else{
		return null;
	}	
	
}

function getTotalOrganicKeywords($request_id){
	global $DBcon;
	$query 		= 	"SELECT count(request_id) as total FROM `semrush_organic_search_data` WHERE `request_id` = $request_id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0]; exit;
	}else{
		return false;
	}	
}

function getTopKeywords($request_id, $from, $end){
	global $DBcon;
	$query 		= 	"SELECT a.request_id, SUM(a.position_difference > 0) `Gain`, SUM(a.position_difference < 0) `Loss` FROM semrush_organic_search_data a WHERE `request_id` = $request_id AND `position` BETWEEN $from and $end";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0]; exit;
	}else{
		return false;
	}	
	
}

function googleSearchConsole($request_id, $url=null, $google_id = null, $start_date=null, $end_date=null){
	global $DBcon;
	global $client;
	$arr	= array(); 
	$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
	$client->setAccessType('offline');
	// $client->setApprovalPrompt('force');
	$query = "SELECT * FROM google_analytics_users WHERE id=$google_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	if ($stmt->rowCount() > 0) {
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$_SESSION['access_token'] 	= $row['google_access_token'];
			$_SESSION['refresh_token'] 	= $row['google_refresh_token'];
			$_SESSION['service_token']['access_token'] = $row['google_access_token'];
			$_SESSION['service_token']['token_type'] = $row['token_type'];
			$_SESSION['service_token']['expires_in'] = $row['expires_in'];
			$_SESSION['service_token']['id_token'] = $row['id_token'];
			$_SESSION['service_token']['created'] = $row['service_created']; 
		}			
		//Get user profile data from google
		// Set the access token on the client. 
		$_tokenArray	=	json_encode($_SESSION['service_token']);
		$client->setAccessToken($_tokenArray);
		// echo "<pre>";
		
		if ($client->isAccessTokenExpired()) {
			// print_r($_SESSION['refresh_token']);
			// echo "string <br>";
			// echo "<pre>";
			$client->refreshToken($_SESSION['refresh_token']);
			// print_r($client->refreshToken($_SESSION['refresh_token']));
			// print_r($client);
			// echo "</pre>";
			// die();
		}
		// print_r($client);die();
		// Create an authorized analytics service object.
		$analytics = new Google_Service_Analytics($client);
		// Get the first view (profile) id for the authorized user.
		$q	= new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
		$q->setStartDate($start_date);
		$q->setEndDate($end_date);
		$q->setDimensions(['query']);
		$q->setSearchType('web');
		$q->setRowLimit(10);

		$page	= new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
		$page->setStartDate($start_date);
		$page->setEndDate($end_date);
		$page->setDimensions(['page']);
		$page->setSearchType('web');
		$page->setRowLimit(10);

		$country	= new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
		$country->setStartDate($start_date);
		$country->setEndDate($end_date);
		$country->setDimensions(['country']);
		$country->setSearchType('web');
		$country->setRowLimit(10);

		$device	= new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
		$device->setStartDate($start_date);
		$device->setEndDate($end_date);
		$device->setDimensions(['device']);
		$device->setSearchType('web');
		$device->setRowLimit(10);


		$d	= new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
		$d->setStartDate($start_date);
		$d->setEndDate($end_date);
		$d->setDimensions(['date']);
		$d->setSearchType('web');

		try {
			$service = new Google_Service_Webmasters($client);
			$site = $service->sites->get($url);
			$u 	 = $service->searchanalytics->query($url, $q);
			$res = $service->searchanalytics->query($url, $d);
			$pages = $service->searchanalytics->query($url, $page);
			$countries = $service->searchanalytics->query($url, $country);
			$devices = $service->searchanalytics->query($url, $device);
			$data  = array('query'=>$u->rows,'data'=>$res->rows,'pages'=>$pages->rows,'countries'=>$countries->rows,'device'=>$devices->rows, 'message'=> ''); 
		  } catch(\Exception $e ) {
			 $message = json_decode($e->getMessage(), true);
			 $data = array('message'=> $message['error']['message']); 
			}  

		return ($data);
	} else {
		$data = array('message'=> 'Please Attach Google Search Console Account On Project Setting Page'); 
		return $data;
	}

	
}


function getToggleModule($request_id){
	global $DBcon;
	$query 		= 	"SELECT * FROM `toggle_module` WHERE `request_id` = $request_id AND `status` = 0";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		$arr	=	'';
		foreach($results as $result){
			$arr[]	=	$result['module'];
		}

		return	$arr; exit;
	}else{
		return false;
	}	
}

function semrushOrganicGraph($request_id=null){

}

function getReport($analytics, $view_key, $start_data, $end_data) {

	// Replace with your view ID, for example XXXX.
	$VIEW_ID = $view_key;
	$SecondaryParams = array('dimensions' => 'ga:date');

	$results	=	 $analytics->data_ga->get(
						'ga:' . $view_key,
						$start_data,
						$end_data,
						'ga:sessions',
						$SecondaryParams						
					);	

	if (count($results->getRows()) > 0) {
		$table .= '<table>';

		// Print table rows.
		foreach ($results->getRows() as $row) {
			$table .= '<tr>';
			foreach ($row as $cell) {
				$table .= '<td>'
						. htmlspecialchars($cell)
						. '</td>';
			}
			$table .= '</tr>';
		}
		$table .= '</table>';
	
		} else {
			$table .= '<p>No Results Found.</p>';
		}
		print $table;
							
	exit;
 }

function getDataValue($analytics, $view_key, $start_data, $end_data) {

	$VIEW_ID = $view_key;
	$SecondaryParams = array('dimensions' => 'ga:date');

	$results	=	 $analytics->data_ga->get(
						$view_key,
						$start_data,
						$end_data,
						'ga:sessions',
						$SecondaryParams						
					);	
					
	if (count($results->getRows()) > 0) {
		return ($results); 
	} else {
		return null;
	}
}

function initializeAnalytics($analytics_id)
{
	global $client;
	$user_id	=	$_SESSION['user_id'];
	$results	= 	googleAccessDetails($analytics_id);
	if(!empty($results)) {
			$refresh_token				 	= $results['google_refresh_token'];
			$service_token['access_token'] 	= $results['google_access_token'];
			$service_token['token_type'] 	= $results['token_type'];
			$service_token['expires_in']	= $results['expires_in'];
			$service_token['id_token'] 		= $results['id_token'];
			$service_token['created'] 		= $results['service_created']; 						
			$_tokenArray					= json_encode($service_token);
			
			$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
			$client->setAccessType('offline');
			$client->setAccessToken($_tokenArray);
			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($refresh_token);
			}
			$analytics 			= 	new Google_Service_Analytics($client);
			return $analytics;
	} else {
		return null;
	}
}

function initializeAnalyticsWithoutLogin($analytics_id, $user_id)
{
	global $client;
	global $analytics;
//	$user_id	=	$_SESSION['user_id'];
	$results	= 	googleAccessDetailsWithoutLogin($analytics_id, $user_id);
	if(!empty($results)) {
			$refresh_token				 	= $results['google_refresh_token'];
			$service_token['access_token'] 	= $results['google_access_token'];
			$service_token['token_type'] 	= $results['token_type'];
			$service_token['expires_in']	= $results['expires_in'];
			$service_token['id_token'] 		= $results['id_token'];
			$service_token['created'] 		= $results['service_created']; 						
			$_tokenArray					= json_encode($service_token);
			
			$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
			$client->setAccessType('offline');
			$client->setAccessToken($_tokenArray);
			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($refresh_token);
			}
			$analytics 			= 	new Google_Service_Analytics($client);
			return $analytics;
	} else {
		return null;
	}
}

function getModuleDateRange($ids, $module){
	global $DBcon;
	$query 		= 	"SELECT * FROM `module_by_daterange` WHERE `request_id` = $ids AND `module` = '".$module."'";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0]; exit;
	}else{
		return false;
	}	

	return $results;

}

function dateRangeDomainHistoryData($ids, $domain_name, $request_id, $db_name, $start, $end){
	global $DBcon;
	$start_data     =   date('Ymd', strtotime($start));
	$end_data       =   date('Ymd', strtotime($end));
	$result_query 	= 	"SELECT *, UNIX_TIMESTAMP( date_time ) AS unix_date  FROM semrush_domain_history WHERE request_id=$request_id AND domain_name='".$domain_name."' AND (date_time BETWEEN $start_data AND $end_data) ORDER BY id DESC ";
	$again_stmt = $DBcon->prepare( $result_query );
	$again_stmt->execute();
	if (!$again_stmt->execute()) {
		print_r($again_stmt->errorInfo()); 
	}			
	$domain_history = $again_stmt->fetchAll();
	return $domain_history;
}
 
function dateRangeSession($ids, $domain_name, $request_id, $start, $end){
	global $DBcon;

	$start_data     =   date('Ymd', strtotime($start));
	$end_data       =   date('Ymd', strtotime($end));

	$select_query 	= 	"SELECT *, WEEK( from_date ) AS DWNum, SUM( count_session ) AS total_session FROM google_profile_session WHERE user_id=$ids AND request_id = $request_id AND (from_date BETWEEN '$start_data' AND '$end_data') GROUP BY DWNum ORDER by id ASC";
	$select_stmt = $DBcon->prepare( $select_query );

	if($select_stmt->execute()) {			
		$results = $select_stmt->fetchAll();
		return $results;
	} else {
		return false;
	}	
}


function countKeywords($request_id){
	global $DBcon;
	$query 	= "SELECT * FROM semrush_domain_history WHERE request_id = $request_id LIMIT 0,1";
	$stmt	= $DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)){
		return $results[0]['organic_keywords'];
	} else {
		return 0;
	}
}
 
function countTraffic($request_id){
	global $DBcon;
	$start_data     =   date('Y-m-d', strtotime("now"));
	$end_data       =   date('Y-m-d', strtotime("-1 month"));
	$select_query 	= 	"SELECT SUM( count_session ) AS total_session FROM google_profile_session WHERE request_id = $request_id AND (from_date BETWEEN '$end_data' AND '$start_data') ORDER by id ASC";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->execute();
	$results = $select_stmt->fetchAll();
	if(!empty($results)){
		return $results[0]['total_session'];
	} else {
		return 0;
	}

//	SELECT count(id) as ID, YEAR(created), MONTH(created) FROM `semrush_users_account` WHERE status = 0 AND (created BETWEEN '2018-08-01' AND '2019-07-30' ) GROUP BY YEAR(created), MONTH(created)

}
 
function getClientName($request_id){
	global $DBcon;
	$select_query 	= 	"SELECT client_name FROM profile_info WHERE request_id = $request_id ";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->execute();
	$results = $select_stmt->fetch();
	if(!empty($results)) {
		return $results['client_name'];
	} else {
		return null;
	}

}

function getActiveProject($status){
	global $DBcon;
	$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id AND status=:status limit 50";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->bindParam(':status', $status);
	$stmt->execute();
	$results = $stmt->fetchAll();

	return $results;
	
}


function getActiveProjectDateRange(){
	global $DBcon;
	$start_data     =   date('Y-m-d', strtotime("now"));
	$end_data       =   date('Y-m-d', strtotime("-180 days"));
	$query = "SELECT
				CONCAT(y, '-', LPAD(m, 2, '0')) as byMonth,
				monthname(str_to_date(m,'%m')) as MonthName,
				COUNT(`created`) AS Total 
				FROM (
				SELECT year(now())     AS y UNION ALL
				SELECT year(now()) - 1 AS y 
				) `years`
				CROSS JOIN (
				SELECT  1 AS m UNION ALL
				SELECT  2 AS m UNION ALL
				SELECT  3 AS m UNION ALL
				SELECT  4 AS m UNION ALL
				SELECT  5 AS m UNION ALL
				SELECT  6 AS m UNION ALL
				SELECT  7 AS m UNION ALL
				SELECT  8 AS m UNION ALL
				SELECT  9 AS m UNION ALL
				SELECT 10 AS m UNION ALL
				SELECT 11 AS m UNION ALL
				SELECT 12 AS m
				) `months`
				LEFT JOIN `semrush_users_account` q
				ON YEAR(`created`) = y 
				AND MONTH(`created`) = m
				AND `status` = 0
				WHERE STR_TO_DATE(CONCAT(y, '-', m, '-01'), '%Y-%m-%d') 
					>= MAKEDATE(year(now()-interval 6 month),1) + interval 3 month
				AND STR_TO_DATE(CONCAT(y, '-', m, '-01'), '%Y-%m-%d') 
					<= now()
				GROUP BY y, m
				ORDER BY y, m";
	$stmt = $DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	return $results;
}


function getDeactiveProjectDateRange(){
	global $DBcon;
	$start_data     =   date('Y-m-d', strtotime("now"));
	$end_data       =   date('Y-m-d', strtotime("-180 days"));
	$query = "SELECT
				CONCAT(y, '-', LPAD(m, 2, '0')) as byMonth,
				COUNT(`created`) AS Total 
				FROM (
				SELECT year(now())     AS y UNION ALL
				SELECT year(now()) - 1 AS y 
				) `years`
				CROSS JOIN (
				SELECT  1 AS m UNION ALL
				SELECT  2 AS m UNION ALL
				SELECT  3 AS m UNION ALL
				SELECT  4 AS m UNION ALL
				SELECT  5 AS m UNION ALL
				SELECT  6 AS m UNION ALL
				SELECT  7 AS m UNION ALL
				SELECT  8 AS m UNION ALL
				SELECT  9 AS m UNION ALL
				SELECT 10 AS m UNION ALL
				SELECT 11 AS m UNION ALL
				SELECT 12 AS m
				) `months`
				LEFT JOIN `semrush_users_account` q
				ON YEAR(`created`) = y 
				AND MONTH(`created`) = m
				AND `status` = 1
				WHERE STR_TO_DATE(CONCAT(y, '-', m, '-01'), '%Y-%m-%d') 
					>= MAKEDATE(year(now()-interval 6 month),1) + interval 3 month
				AND STR_TO_DATE(CONCAT(y, '-', m, '-01'), '%Y-%m-%d') 
					<= now()
				GROUP BY y, m
				ORDER BY y, m";
	$stmt = $DBcon->prepare( $query );
	$stmt->execute();
	$results = $stmt->fetchAll();
	return $results;
}

function getTopKeywordDateRange($kewords){
	global $DBcon;
	$start_data     =   date('Y-m-d', strtotime("now"));
	$end_data       =   date('Y-m-d', strtotime("-1 month"));
	$select_query 	= 	"SELECT count(sosd.id) as Total FROM `semrush_organic_search_data` sosd, semrush_users_account sua WHERE sosd.request_id = sua.id AND sosd.`position` <= $kewords AND sua.status = 0";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->execute();
	$results = $select_stmt->fetch();
	return $results['Total'];

}

function getImprovedKeywords($request_id, $from, $end){
	global $DBcon;
	$query 		= 	"SELECT count(request_id) as total FROM `semrush_organic_search_data` WHERE `request_id` = $request_id AND `position_difference` BETWEEN $from and $end";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0];
	}else{
		return null;
	}	
	
}

function getMetricsData($analytics, $analytics_id, $start_date, $end_date){

	global $client;
	global $analytics;
	$arr	= array();
	$client->setAuthConfig(ABS_PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
	$client->setAccessType('offline');

	$optParams = array();
	// Required parameter
	$metrics    = 'ga:sessions, ga:users, ga:pageviews';
	// Optional parameters
	// optParams['filters']      = 'ga:pagePath==/';
	// $optParams['dimensions']  = 'ga:pagePath';
	// $optParams['sort']        = '-ga:pageviews';
	// $optParams['max-results'] = '10';
	try {
		//code...
		$result = $analytics->data_ga->get( $analytics_id,  $start_date,  $end_date, $metrics, $optParams);
		$new_results['sessions']	=	$result['rows'][0][0];
		$new_results['users']		=	$result['rows'][0][1];
		$new_results['pageview']	=	$result['rows'][0][2];
	
	} catch (Exception $th) {
		//throw $th;
		$results				=	(json_decode($th->getMessage(), true));
		$new_results['message']	=	($results['error']['message']);
	}
	return $new_results; exit;
}

function getUserProfileData($request_id){
	global $DBcon;
	$query 		= 	"SELECT * FROM `profile_info`,semrush_users_account WHERE profile_info.`request_id` = semrush_users_account.id AND semrush_users_account.id = $request_id";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results[0];
	}else{
		return null;
	}	
}

function addCustomNote($insertId){
	global $DBcon;
	$query = "INSERT INTO seo_analytics_edit_secion(user_id,request_id,edit_section, edit_area) VALUES(:user_id, :request_id, :edit_section, :edit_area)";
	$user_id		= 	$_SESSION['user_id'];	
	$request_id		= 	$insertId;
	$edit_section	= 	'<p>Welcome to Your Dashboard!</p>
	<p>This dashboard gives you an at-a-glance view of the aspects of your campaign that are most important to you. And since it’s customizable, you can ask your account manager to update it for you.</p>
	<p>This dashboard shows you: </p>
	<ul>
	<li>a. Traffic from Google analytics. </li>
	<li>b. Visibility of your campaign in Google from Search Console. </li>
	<li>c. Additional Keywords that you are ranking for from SEMRUSH.</li> 
	<li>d. Work performed by our team in Activity Seaction and much more.</li></ul>
	<p>You can download a PDF copy of whole report by clicking a button in top right section.</p>
	<p>To give us feedback on this tool, please leave a message with your account manager. </p>';
	$edit_area		= 	0;

	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':request_id', $request_id);
	$stmt->bindParam(':edit_section', $edit_section);
	$stmt->bindParam(':edit_area', $edit_area);
	$stmt->execute();
}

function getActivateTags(){

	global $DBcon;
	$query 		= 	"SELECT category_name FROM `activity_category` WHERE parent_id = 0 AND status = 0";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results;
	}else{
		return null;
	}	
}

function getActivateTask($id){

	global $DBcon;
	$query 		= 	"SELECT category_name FROM `activity_category` WHERE parent_id = $id AND user_id=:user_id AND status = 0";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results;
	}else{
		return null;
	}	
}

function getActiveTaskList($id){

	global $DBcon;
	$query 		= 	"SELECT id, category_name FROM `activity_category` WHERE parent_id = $id AND (user_id=:user_id OR user_id = 0) AND status = 0";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results;
	}else{
		return null;
	}	
}

function getActivateStatus(){

	global $DBcon;
	$query 		= 	"SELECT name FROM `activity_status` WHERE user_id=:user_id AND status = 0";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results[0])) {
		return	$results;
	}else{
		return null;
	}	

}

function getActivityDetails($requestId, $user_id=null){
	global $DBcon;
	if(empty($user_id))
		$user_id	=	$_SESSION['user_id'];
	$query 		= 	"SELECT GROUP_CONCAT(id) as ids, activity_date FROM `activities_product` WHERE user_id=:user_id AND request_id = :request_id AND status = 0 GROUP by `activity_date` ORDER BY activity_date DESC ";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':request_id', $requestId);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	

}

function getActivityDataID($requestId){
	global $DBcon;
	$query		=	"SELECT ap.*, ac.category_name as Activity_type, acc.category_name AS Activity_task FROM `activities_product` ap, activity_category ac, activity_category acc WHERE ap.id = :request_id AND ap.activity_type = ac.id AND ap.activity_task = acc.id
	";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':request_id', $requestId);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetch();
	if(!empty($results)) {
		return $results;
	}else{
		return false;
	}	

}

function checkActivityTaskExist($tag, $task_type){
	global $DBcon;
	$user_id	=	$_SESSION['user_id'];	
	$query		=	"SELECT ac.id as id FROM `activities_product` ap, activity_category ac WHERE ap.activity_task = ac.id AND ac.category_name = :category_name AND ac.user_id = $user_id AND ac.parent_id = $task_type";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':category_name', $tag);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetch();
	if(!empty($results)) {
		return $results;
	}else{
		return false;
	}	

}

function getMozData($url){
	$data		=	array();
	$accessID 	= 	"mozscape-4fd7e84d46"; // * Add unique Access ID
	$secretKey 	= 	"21f8e785a942ee47e0dbab55aff4aa6c"; // * Add unique Secret Key

	$expires = time() + 300;
	$stringToSign = $accessID."\n".$expires;
	$binarySignature = hash_hmac('sha1', $stringToSign, $secretKey, true);
	$urlSafeSignature = urlencode(base64_encode($binarySignature));
	$objectURL = $url;
	$cols = array("34359738368", "68719476736");
	foreach($cols as $col){
		$requestUrl = "https://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($objectURL)."?Cols=".$col."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$urlSafeSignature;
		$options = array(
			CURLOPT_RETURNTRANSFER => true
			);
		
		$ch = curl_init($requestUrl);
		curl_setopt_array($ch, $options);
		$content = curl_exec($ch);
		curl_close($ch);
		$json_a[] = json_decode($content);
	}
	$data['pageAuthority'] = round($json_a[0]->upa,0); // * Use the round() function to return integer
	$data['domainAuthority'] = round($json_a[1]->pda,0);

	return $data; 
}

function getFaqData($request_id=null){
	global $DBcon;
	$query 		= 	"SELECT * FROM `project_FAQ`";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':request_id', $request_id);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	
}

function getFaqDataWithoutId(){
	global $DBcon;
	$query 		= 	"SELECT * FROM `project_FAQ` ";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	
}

function getFaqHtmlData($request_id=null){
	global $DBcon;
	$query 		= 	"SELECT * FROM `project_FAQ` ";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		$html	=	'';
		foreach($results as $result){
			$html	.=	'<li><a href="#" class="faq_detail" data-id="'.$result['id'].'" >'.$result['faq_title'].'<span class="text-center delete" data-id="'.$result['id'].'"><i class="fa fa-trash"></i></span></a> </li>';
		}
		return	$html;
	}else{
		$html 	=	'';
		return $html;
	}	
}

function getFaqDeleteHtmlData($request_id = null){
	global $DBcon;
	$query 		= 	"SELECT * FROM `project_FAQ`";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		$html	=	'';
		$i		=	1;
		foreach($results as $result){
			$html	.=	'
							<div class="panel">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#faqAccordion" href="#collapse'.$i.'">'.$result['faq_title'].'</a>
									</h4>
								</div>
								<div id="collapse'.$i.'" class="panel-collapse collapse">
									<div class="panel-body">'.$result['faq_content'].'</div>
								</div>
							</div>';
			$i++;
		}
		return	$html;
	}else{
		$html 	=	'';
		return $html;
	}	
}

function getFaqDataByID($id){
	global $DBcon;
	$query 		= 	"SELECT * FROM `project_FAQ` WHERE id=:id ";
	$stmt 		= 	$DBcon->prepare( $query );
	$stmt->bindParam(':id', $id);
	$stmt->execute() or die(print_r($stmt->errorInfo(), true));
	$results = $stmt->fetch();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	
}

function getGoogleProfileSession($id){
	global $DBcon;
	
	$select_query 	= 	"SELECT * FROM google_profile_session WHERE user_id=:user_id AND request_id=:request_id";
	$select_stmt = $DBcon->prepare( $select_query );
	$select_stmt->bindParam(':user_id', $_SESSION['user_id']);
	$select_stmt->bindParam(':request_id', $id);
	$select_stmt->execute();
	$results = $select_stmt->fetchAll();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	

}

function getCompareChart($id){
	global $DBcon;
	
	$select_query 	= 	"SELECT * FROM project_compare_graph WHERE request_id=:request_id";
	$select_stmt = $DBcon->prepare( $select_query );
//	$select_stmt->bindParam(':user_id', $_SESSION['user_id']);
	$select_stmt->bindParam(':request_id', $id);
	$select_stmt->execute();
	$results = $select_stmt->fetch();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	

}

function saveKeywordsUrl($post, $results=null){
	global $DBcon;
	$request_id			=	$results['request_id'];
	$tracking_option	=	$results['tracking'];
	$host_url			=	$results['url'];
	$region				=	$results['region'];
	$canonical			=	$post['pingback_url'];
	$cmp 				=	$results['cmp'];
	$sv 				=	$results['sv'];


	$count_query 		= 	"INSERT INTO keyword_search(keyword, start_ranking, url_site, task_id, position, result_se_check_url, result_url, result_title, result_snippet, request_id, tracking_option, host_url, region, canonical, cmp, sv) VALUES(:keyword, :start_ranking, :url_site, :task_id, :position, :result_se_check_url, :result_url, :result_title, :result_snippet, :request_id, :tracking_option, :host_url, :region, :canonical, :cmp, :sv)";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $request_id);
	$count_stmt->bindParam(':tracking_option', $tracking_option);
	$count_stmt->bindParam(':host_url', $host_url);
	$count_stmt->bindParam(':region', $region);
	$count_stmt->bindParam(':canonical', $canonical);
	$count_stmt->bindParam(':start_ranking', $post['result_position']);
	$count_stmt->bindParam(':result_url', $post['result_url']);
	$count_stmt->bindParam(':cmp', $cmp);
	$count_stmt->bindParam(':sv', $sv);
	$count_stmt->bindParam(':result_title', $post['result_title']);
	$count_stmt->bindParam(':result_snippet', $post['result_snippet']);
	$count_stmt->bindParam(':keyword', $post['post_key']);
	$count_stmt->bindParam(':url_site', $post['post_site']);
	$count_stmt->bindParam(':task_id', $post['task_id']);
	$count_stmt->bindParam(':position', $post['result_position']);
	$count_stmt->bindParam(':result_se_check_url', $post['result_se_check_url']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	return $count_stmt; 
}


function saveData($post){
	global $DBcon;
	$count_query 	= 	"INSERT INTO keyword_search(result_se_check_url) VALUES(:result_se_check_url)";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':result_se_check_url', $post);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			

}

function getCountries(){
	global $DBcon;
	$select_query 	= 	"SELECT * FROM country ";
	$select_stmt 	= 	$DBcon->prepare( $select_query );
	$select_stmt->execute();
	$results = $select_stmt->fetchAll();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	

}

function getKeyWordSearch($ids){
	global $DBcon;
	$query = "SELECT * FROM keyword_search WHERE request_id=:request_id";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':request_id', $ids);
	$stmt->execute();
	$results = $stmt->fetchAll();
	if(!empty($results)) {
		return	$results; exit;
	}else{
		return false;
	}	

}

function checkKeywordExsist($post){ 
	global $DBcon;
	$tasks				=	$post['cities'];
	$region				=	$post['region'];
	$request_id			=	$post['request_id'];
	$tracking_option	= 	$post['tracking_option'];
	$canonical			=	$post['loc_name_canonical']; 
	$tasks				=	preg_replace('~[\r\n\t]+~', ',', $tasks);
	$str_array			= 	explode(',', $tasks);
	$str				=	 "'" . implode ("', '", $str_array ) . "'";

	$query 				= 	"SELECT keyword FROM keyword_search WHERE request_id=$request_id AND keyword IN($str) AND tracking_option = '".$tracking_option."' AND region = '".$region."' AND canonical = '".$canonical."'";


	$stmt 				= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results 			= 	$stmt->fetchAll();

	if(!empty($results)) {
		$results = array_column($results, 'keyword');
		return	$results; exit;
	}else{
		return false;
	}	
	
}

function getSerpValue($request_id){
	global $DBcon;
	$query 				= 	"SELECT * FROM keyword_search ks LEFT JOIN ( SELECT  MAX(id) max_id, position as Position, keyword_id FROM keyword_position	GROUP BY  request_id ) kp ON (kp.keyword_id = ks.id)  WHERE ks.request_id=$request_id ORDER BY ks.`is_favorite` DESC, ks.`position` ASC";
	$stmt 				= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results 			= 	$stmt->fetchAll();

	if(!empty($results)) {
		return	$results; exit;
	}else{
		return false;
	}	
	
}

function checkKeywordUpdated($request_id){
	global $DBcon;
	$count_query 		= 	"UPDATE keyword_search SET is_flag='0' WHERE request_id =$request_id AND task_id != '' ";
	$count_stmt			= 	$DBcon->prepare($count_query);
	$count_stmt->execute();
	$query 				= 	"SELECT * FROM keyword_search WHERE request_id=$request_id AND is_flag = '1' ORDER BY `is_favorite` DESC, `position` ASC";
	$stmt 				= 	$DBcon->prepare( $query );
	$stmt->execute();
	$results 			= 	$stmt->fetchAll();

	if(!empty($results)) {
		return	$results; exit;
	}else{
		return false;
	}	
	
}

function getCountryByCode($country){
	global $DBcon;
	$select_query 	= 	"SELECT LOWER(loc_country_iso_code) FROM keyword_location_list where loc_name_canonical = '".$country."'";
	$select_stmt 	= 	$DBcon->prepare( $select_query );
	$select_stmt->execute();
	$results = $select_stmt->fetch();
	if(!empty($results)) {
		return	$results[0];
	}else{
		return null;
	}	

}

function getDomain($url){
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
        return $regs['domain'];
    }
    return FALSE;
}

function updateKeywords(){
	global $DBcon;
	$select_query	= 	"SELECT * FROM `keyword_search` where date(created) = date(CURDATE() - interval 7 day)	";
	$select_stmt 	= 	$DBcon->prepare( $select_query );
	$select_stmt->execute();
	$results = $select_stmt->fetchAll();
	if(!empty($results)) {
		return	$results;
	}else{
		return null;
	}	


}

function updateKeywordsUrl($post=null, $results=null){
	global $DBcon;
	$request_id			=	$results['request_id'];
	$last_week			=	$results['last_week'];
	$cmp 				=	$results['cmp'];
	$sv 				=	$results['sv'];

	$count_query 	= "UPDATE keyword_search SET one_week_ranking=:one_week_ranking, position=:position, cmp=:cmp, sv=:sv WHERE id=:id";
	$count_stmt		= $DBcon->prepare($count_query);
	$count_stmt->bindParam(':one_week_ranking', $last_week);
	$count_stmt->bindParam(':position', $post['result_position']);
	$count_stmt->bindParam(':cmp', $cmp);
	$count_stmt->bindParam(':sv', $sv);
	$count_stmt->bindParam(':id', $request_id);
	
	
	if (!$count_stmt->execute()) {
		return ($count_stmt->errorInfo());
	}			
	return $count_stmt; 
}


function saveKeywords($results){
	global $DBcon;
	$request_id			=	$results['request_id'];
	$tracking_option	=	$results['tracking_option'];
	$host_url			=	$results['url'];
	$region				=	$results['region'];
	$canonical			=	$results['loc_name_canonical'];
	$cmp 				=	$results['cmp'];
	$sv 				=	$results['sv'];
	$is_flag			=	'1';

	$count_query 		= 	"INSERT INTO keyword_search(keyword, request_id, tracking_option, host_url, result_url, region, canonical, cmp, sv, is_flag) VALUES(:keyword, :request_id, :tracking_option, :host_url, :result_url,  :region, :canonical, :cmp, :sv, :is_flag)";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $request_id);
	$count_stmt->bindParam(':tracking_option', $tracking_option);
	$count_stmt->bindParam(':host_url', $host_url);
	$count_stmt->bindParam(':result_url', $host_url);
	$count_stmt->bindParam(':region', $region);
	$count_stmt->bindParam(':canonical', $canonical);
	$count_stmt->bindParam(':cmp', $cmp);
	$count_stmt->bindParam(':sv', $sv);
	$count_stmt->bindParam(':is_flag', $is_flag);
	$count_stmt->bindParam(':keyword', $results['key']);
	if (!$count_stmt->execute()) {
		($count_stmt->errorInfo()); 
		return null;
	}			
	return $DBcon->lastInsertId();
}


function updateApiKeywordsUrl($post, $results=null){
	global $DBcon;
	$request_id			=	$results['request_id'];
	$tracking_option	=	$results['tracking'];
	$host_url			=	$results['url'];
	$region				=	$results['region'];
	$canonical			=	$post['pingback_url'];
	$cmp 				=	$results['cmp'];
	$sv 				=	$results['sv'];

	
	$count_query 	= "UPDATE keyword_search SET start_ranking=:start_ranking, url_site=:url_site, task_id=:task_id, position=:position, result_se_check_url=:result_se_check_url, result_url=:result_url, result_title=:result_title, result_snippet=:result_snippet WHERE request_id=:request_id AND keyword=:keyword AND region=:region AND canonical=:canonical AND host_url=:host_url AND tracking_option=:tracking_option";

	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $request_id);
	$count_stmt->bindParam(':tracking_option', $tracking_option);
	$count_stmt->bindParam(':host_url', $host_url);
	$count_stmt->bindParam(':region', $region);
	$count_stmt->bindParam(':canonical', $canonical);
	$count_stmt->bindParam(':start_ranking', $post['result_position']);
	$count_stmt->bindParam(':result_url', $post['result_url']);
	$count_stmt->bindParam(':result_title', $post['result_title']);
	$count_stmt->bindParam(':result_snippet', $post['result_snippet']);
	$count_stmt->bindParam(':keyword', $post['post_key']);
	$count_stmt->bindParam(':url_site', $post['post_site']);
	$count_stmt->bindParam(':task_id', $post['task_id']);
	$count_stmt->bindParam(':position', $post['result_position']);
	$count_stmt->bindParam(':result_se_check_url', $post['result_se_check_url']);
	if (!$count_stmt->execute()) {
		return ($count_stmt->errorInfo());
	}			
	return $DBcon->debugDumpParams();
	
}

function updateCpcKeywords($results){
	global $DBcon;
	$id			=	$results['id'];
	$cmp		=	$results['cmp'];
	$sv 		=	$results['sv'];

	
	$count_query 	= "UPDATE keyword_search SET cmp=:cmp, sv=:sv WHERE id=:id";

	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':cmp', $cmp);
	$count_stmt->bindParam(':sv', $sv);
	$count_stmt->bindParam(':id', $id);
	if (!$count_stmt->execute()) {
		return ($count_stmt->errorInfo());
	}			
	return $DBcon->debugDumpParams();
	
}

function addKeywordPosition($results=null){
	global $DBcon;

	$count_query 		= 	"INSERT INTO keyword_position(request_id, keyword_id, position, updated_at) VALUES(:request_id, :keyword_id, :position, :updated_at)";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $results['request_id']);
	$count_stmt->bindParam(':keyword_id', $results['keyword_id']);
	$count_stmt->bindParam(':position', $results['position']);
	$count_stmt->bindParam(':updated_at', $results['updated_at']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	return $count_stmt; 
}


function lastestKeywordPosition($results){
	global $DBcon;

//	$count_query	= 	"SELECT position, request_id, keyword_id FROM `keyword_position` WHERE request_id= :request_id AND keyword_id = :keyword_id ORDER BY created_at DESC Limit 1 ";
	$count_query	= 	"SELECT position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <= now() - interval 0 DAY AND request_id= :request_id AND keyword_id = :keyword_id ORDER BY created_at DESC Limit 1 ";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $results['request_id']);
	$count_stmt->bindParam(':keyword_id', $results['keyword_id']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	$result = $count_stmt->fetch(PDO::FETCH_ASSOC);
	return $result; 

}

function oneDayKeyword($results){
	
	global $DBcon;

//	$count_query 		= 	"SELECT position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <= now() - interval 0 DAY AND `created_at` >= now() - interval 1 DAY AND request_id= :request_id AND keyword_id = :keyword_id Limit 1";
	$count_query 		= 	"SELECT position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <= now() - interval 2 DAY AND request_id= :request_id AND keyword_id = :keyword_id Limit 1";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $results['request_id']);
	$count_stmt->bindParam(':keyword_id', $results['keyword_id']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	$result = $count_stmt->fetch(PDO::FETCH_ASSOC);
	return $result; 

}

function weeklyKeywords($results){
	
	global $DBcon;

//	$count_query 		= 	"SELECT position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <= now() - interval 6 DAY AND `created_at` >= now() - interval 7 DAY AND request_id= :request_id AND keyword_id = :keyword_id";
	$count_query 		= 	"SELECT position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <= now() - interval 7 DAY AND request_id= :request_id AND keyword_id = :keyword_id ORDER BY created_at DESC Limit 1";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $results['request_id']);
	$count_stmt->bindParam(':keyword_id', $results['keyword_id']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	$result = $count_stmt->fetch(PDO::FETCH_ASSOC);
	return $result; 

}

function secondKeywords(){
	
	global $DBcon;

	$count_query 		= 	"SELECT created_at, position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <=  now() - interval 14 DAY AND `created_at` >= now() - interval 15 DAY AND request_id= :request_id  GROUP BY keyword_id";
	$count_stmt 		= 	$DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $results['request_id']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	return $count_stmt; 

}

function fourthKeywords($results){
	
	global $DBcon;

//	$count_query 		= 	"SELECT created_at, position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <=  now() - interval 29 DAY AND `created_at` >= now() - interval 30 DAY AND request_id= :request_id  AND keyword_id = :keyword_id";
	$count_query 		= 	"SELECT created_at, position, request_id, keyword_id FROM `keyword_position` WHERE `created_at` <=  now() - interval 30 DAY AND request_id= :request_id  AND keyword_id = :keyword_id ORDER BY created_at DESC Limit 1";
	$count_stmt 		= 	$DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $results['request_id']);
	$count_stmt->bindParam(':keyword_id', $results['keyword_id']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	$result = $count_stmt->fetch(PDO::FETCH_ASSOC);
	return $result; 
}

function calculate_time_span($post)
{  
	$seconds = time() - strtotime($post);
	$year = floor($seconds /31556926);
	$months = floor($seconds /2629743);
	$week=floor($seconds /604800);
	$day = floor($seconds /86400); 
	$hours = floor($seconds / 3600);
	$mins = floor(($seconds - ($hours*3600)) / 60); 
	$secs = floor($seconds % 60);
	if($seconds < 60) $time = $secs." seconds ago";
	else if($seconds < 3600 ) $time =($mins==1)?$mins."now":$mins." mins ago";
	else if($seconds < 86400) $time = ($hours==1)?$hours." hour ago":$hours." hours ago";
	else if($seconds < 604800) $time = ($day==1)?$day." day ago":$day." days ago";
	else if($seconds < 2629743) $time = ($week==1)?$week." week ago":$week." weeks ago";
	else if($seconds < 31556926) $time =($months==1)? $months." month ago":$months." months ago";
	else $time = ($year==1)? $year." year ago":$year." years ago";
	return $time; 
}  

function getKeywordsData($post){
	global $DBcon;
	$inQuery 			= 	implode(',', $post['selected_ids']);
	$count_query 		= 	'SELECT * FROM `keyword_search` WHERE id IN('.$inQuery.')';
	$count_stmt 		= 	$DBcon->prepare( $count_query );
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	return $count_stmt->fetchAll(); 
}

function getLastUpdateKeyword($request_id){
	global $DBcon;
	$count_query	= 	"SELECT updated_at FROM `keyword_position` WHERE request_id= :request_id ORDER BY updated_at DESC LIMIT 1";
	$count_stmt 	= 	$DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $request_id);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();
	}			
	$result =  $count_stmt->fetch(); 
	if(!empty($result)) {
		return $result['updated_at'];
	} else {
		return null;
	}

}

function addDataSeoApiUnit($data){
	global $DBcon;

	$count_query = "INSERT INTO dataforseo_api_unit(request_id, domain_name, keyword, api_name) VALUES(:request_id, :domain_name, :keyword, :api_name)";
	$count_stmt = $DBcon->prepare( $count_query );
	$count_stmt->bindParam(':request_id', $data['request_id']);
	$count_stmt->bindParam(':domain_name', $data['domain_name']);
	$count_stmt->bindParam(':keyword', $data['keyword_name']);
	$count_stmt->bindParam(':api_name', $data['api_name']);
	if (!$count_stmt->execute()) {
		$count_stmt->debugDumpParams();

	}			

}
