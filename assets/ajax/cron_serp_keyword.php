<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('DBhost', 'localhost');
define('DBuser', 'root');
define('DBPass', 'im@rk123#@');
define('DBname', 'seoreport');

try {
    
    $DBcon = new PDO("mysql:host=".DBhost.";dbname=".DBname,DBuser,DBPass);
    
} catch(PDOException $e){
    
    die($e->getMessage());
}

global $DBcon;

require_once("includes/functions.php");

require_once('DataforceApi/RestClient.php');

$response		    =	array();
$check_result		=	updateKeywords();
print_r($check_result);
    if(!empty($check_result)) {
        try {
            $client = new RestClient('https://api.dataforseo.com/', null, 'ishan@siliconbeachdigital.com', 'TB5IdHh0B28vagDB');
        } catch (RestClientException $e) {
            echo "\n";
            print "HTTP code: {$e->getHttpCode()}\n";
            print "Error code: {$e->getCode()}\n";
            print "Message: {$e->getMessage()}\n";
            print  $e->getTraceAsString();
            echo "\n";
            exit();
        }
        
        foreach($check_result as $check)
			$post_array 		=   array();
			$task_post_result	=   array();
			$request_id         =   $check['id'];
			$url				=	$check['url_site'];
			$region				= 	$check['region'];	
			$tracking			= 	$check['tracking_option'];	
			$canonical			= 	$check['canonical'];	
			$keyword			=	$check['keywords'];
			$last_week			=	$check['last_week'];
			
			$my_unq_id      	=   mt_rand(0, 30000000); // your unique ID. we will return it with all results. you can set your database ID, string, etc.
			if($tracking == 'mobile') {
				$option_txt	=	' mobile';
			}else{
				$option_txt	=	'';
			}
			$post_array[$my_unq_id] = array(
				"priority" => 1,
				"site" => $url,
				"se_name" => $region.$option_txt,
				"se_language" => "English",
				"loc_name_canonical" => $canonical,
				"key" => mb_convert_encoding($keyword, "UTF-8"), 
				"pingback_url"=> $check['last_week'],
				"postback_url" => "https://seoreports.me/DataforceApi/update_keyword_api.php?request_id=$request_id&last_week=$last_week"
			);

			if (count($post_array) > 0) {
				try {
					$task_post_result = $client->post('/v2/rnk_tasks_post', array('data' => $post_array));
					$post_array = array();
				} catch (RestClientException $e) {
					$response['message'] = $e->getMessage();
				}
			}else {
				$response['status'] = '1'; // Insert Data Done
				$response['error'] = '0';
				$response['message'] = 'Already Added';
            }
            
    }
    echo json_encode($response);
