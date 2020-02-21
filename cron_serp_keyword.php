<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('/var/www/html/includes/config.php');
require_once('/var/www/html/vendor/autoload.php');
require_once("/var/www/html/includes/functions.php");
require_once('/var/www/html/DataforceApi/RestClient.php');

/*
SELECT * FROM `keyword_position` WHERE `created_at` >= CURRENT_TIMESTAMP - INTERVAL '24' HOUR
SELECT * FROM keyword_position ks LEFT JOIN ( SELECT  MAX(id) max_id, position as Position, keyword_id FROM keyword_position GROUP BY  request_id ) kp ON (kp.keyword_id = ks.id)  WHERE ks.`request_id`=43
SELECT * FROM keyword_search ks LEFT JOIN ( SELECT MAX(id) max_id, position as Position, keyword_id FROM keyword_position GROUP BY request_id ) kp ON (kp.keyword_id = ks.id) WHERE ks.request_id=43 ORDER BY ks.`is_favorite` DESC, ks.`position` ASC 
*/

$query 				= 	"SELECT * FROM `keyword_position` WHERE `updated_at` < (now() - interval 24 hour) AND status != '1' GROUP BY keyword_id ORDER BY created_at DESC";
$stmt 				= 	$DBcon->prepare( $query );
$stmt->execute();
$results 			= 	$stmt->fetchAll();
$post_array         =   array();




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

if(!empty($results)){
    foreach($results as $result){
            $count_query 	=   "UPDATE keyword_position SET status='1' WHERE id=:id";
            $count_stmt     =   $DBcon->prepare( $count_query );
            $count_stmt->bindParam(':id', $result['id']);
            if (!$count_stmt->execute()) {
                return ($count_stmt->errorInfo());
            }			
            
            $ks_query 				= 	"SELECT * FROM `keyword_search` WHERE id=".$result['keyword_id'];
            $ks_stmt 				= 	$DBcon->prepare( $ks_query );
            $ks_stmt->execute();
            $ks_results 			= 	$ks_stmt->fetchAll();
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
                        "postback_url" => "https://seoreports.me/DataforceApi/cron_keyword_rank.php?request_id=".$result['request_id']."&data_id=".$result['id']."&keyword_id=".$result['keyword_id']
                    ); 
                }

            }        
    }

}

//print_r($post_array); exit;

if (count($post_array) > 0) {
    try {
        $task_post_result = $client->post('/v2/rnk_tasks_post', array('data' => $post_array));
        $post_array = array();
        $response['status'] = '1'; // Insert Data Done
        $response['error'] = '0';
        $response['message'] = 'Keyword Added Successfully';
        $response['html']	=	'';
    } catch (RestClientException $e) {
        $response['status'] = '2'; // Insert Data Done
        $response['error'] = '2';
        $response['message'] = $e->getMessage();
    }
}else {
    $response['status'] = '1'; // Insert Data Done
    $response['error'] = '0';
    $response['message'] = 'Already Added';
}

