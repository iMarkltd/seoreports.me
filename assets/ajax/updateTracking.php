<?php

header('Content-type: application/json');

require_once('../../includes/config.php');
require_once('../../vendor/autoload.php');
require_once("../../includes/functions.php");
require_once('dataForceApi/RestClient.php');

if ($_POST) {


	$ks_results	=	getKeywordsData($_REQUEST);
	if(!empty($ks_results)) {
		foreach($ks_results as $ks) {
			if($ks['tracking_option'] == 'mobile') {
				$option_txt	=	' mobile';
			}else{
				$option_txt	=	'';
			}
			$my_unq_id 	    =   mt_rand(0, 30000000); // your unique ID. we will return it with all results. you can set your database ID, string, etc.
			
			$post_array[$my_unq_id] = array(
				"priority" => 1,
				"site" => $ks['host_url'],
				"se_name" => $ks['region'].$option_txt,
				"se_language" => 'English',
				"loc_name_canonical" => $ks['canonical'],
				"key" => mb_convert_encoding($ks['keyword'], "UTF-8"),
				"pingback_url"=> $ks['canonical'],
				"postback_url" => "https://seoreports.me/DataforceApi/keyword_rank.php?request_id=".$_REQUEST['request_id']."&data_id=".$ks['id']
			); 
		}
	}

	$html	=	'';

}

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

if (count($post_array) > 0) {
	try {
		$task_post_result = $client->post('/v2/rnk_tasks_post', array('data' => $post_array));
		$post_array = array();
		$response['status'] = '1'; // Insert Data Done
		$response['error'] = '0';
		$response['message'] = 'Keyword updated Successfully';
		$response['html']	=	$html;
	} catch (RestClientException $e) {
		$response['status'] = '2'; // Insert Data Done
		$response['error'] = '2';
		$response['message'] = $e->getMessage();
	}
}else {
	$response['status'] = '1'; // Insert Data Done
	$response['error'] = '0';
	$response['message'] = 'Not Found!';
}




echo json_encode($response);
