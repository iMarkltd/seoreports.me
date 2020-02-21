<?php
require('RestClient.php');
//You can download this file from here https://api.dataforseo.com/_examples/php/_php_RestClient.zip
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo '<pre>';

try {
    //Instead of 'login' and 'password' use your credentials from https://my.dataforseo.com/login
    $client = new RestClient('https://api.dataforseo.com/', null, 'ishan@siliconbeachdigital.com', 'TB5IdHh0B28vagDB');
    //do something

} catch (RestClientException $e) {
    echo "\n";
    print "HTTP code: {$e->getHttpCode()}\n";
    print "Error code: {$e->getCode()}\n";
    print "Message: {$e->getMessage()}\n";
    print  $e->getTraceAsString();
    echo "\n";
    exit();
}

$post_array 		=   array();
$task_post_result	=   array();
$request_id         =   24;
$strings            =   'buy backlinks india, ppc management services india, seo outsource';
$keys               =   explode(',', $strings);

foreach($keys as $key) {
    $my_unq_id      =   mt_rand(0, 30000000); // your unique ID. we will return it with all results. you can set your database ID, string, etc.
    $post_array[$my_unq_id] = array(
        "priority" => 2,
        "site" => "www.imarkinfotech.com",
        "se_name" => "google.com",
        "se_language" => "English",
        "loc_name_canonical" => "United States",
        "key" => mb_convert_encoding($key, "UTF-8"),
        "postback_url" => "https://seoreports.me/DataforceApi/keyword_rank.php?request_id=$request_id"
    );
}

if (count($post_array) > 0) {
    try {
        // POST /v2/rnk_tasks_post/$tasks_data
        // $tasks_data must by array with key 'data'
        $task_post_result = $client->post('/v2/rnk_tasks_post', array('data' => $post_array));
		print_r($task_post_result['results']);

        //do something with post results

        $post_array = array();
    } catch (RestClientException $e) {
        echo "\n";
        print "HTTP code: {$e->getHttpCode()}\n";
        print "Error code: {$e->getCode()}\n";
        print "Message: {$e->getMessage()}\n";
        print  $e->getTraceAsString();
        echo "\n";
    }
}

?>
