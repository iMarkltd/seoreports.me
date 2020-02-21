<?php
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once('../vendor/PHPMailer/PHPMailerAutoload.php');
require_once("../includes/functions.php");
require_once("../vendor/php/lib/GrabzItClient.class.php");

global $DBcon;
$post_data_in = file_get_contents('php://input');

$mail = new PHPMailer;
$mail->SMTPDebug = 0;                               	// Enable verbose debug output
$mail->isSMTP();                                      	// Set mailer to use SMTP
$mail->Host = 'in-v3.mailjet.com';  					// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               	// Enable SMTP authentication
$mail->Username = '620b20dfedf9de5cccbf1ed4b21971a0';   // SMTP username
$mail->Password = 'ba4f00c99cd8348a9ddc12bc0a30ce9b';   // SMTP password
$mail->SMTPSecure = 'ssl';                              // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                      // TCP port to connect to
$mail->setFrom('noreply@imarkinfotech.com', 'Imarkinfotech');
$mail->addAddress('navdeep.sharma@imarkinfotech.com');
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Test Keyword Rank Functionality';



//saveData(json_encode($_REQUEST));



if (!empty($post_data_in)) {
	$post_arr	 = 	json_decode($post_data_in, true);

	if ((!empty($post_arr)) AND ($post_arr["status"] == "ok")) {
		$post_data 			=	$post_data_in;
		$request 			=	$_REQUEST;
		foreach($post_arr["results"]["organic"] as $organic){
//			$saveData    =  updateKeywordsUrl($organic, $_REQUEST);
			$data_id			=	$_REQUEST['data_id'];
			$tracking_option	=	$_REQUEST['tracking'];
			$host_url			=	$_REQUEST['url'];
			$region				=	$_REQUEST['region'];
			$canonical			=	$organic['pingback_url'];
			$cmp 				=	$_REQUEST['cmp'];
			$sv 				=	$_REQUEST['sv'];
			$url				=	!empty($organic['result_url']) ? $organic['result_url'] : $organic['post_site'];
			
//			$count_query 	= "UPDATE keyword_search SET start_ranking=:start_ranking, url_site=:url_site, task_id=:task_id, position=:position, result_se_check_url=:result_se_check_url, result_url=:result_url, result_title=:result_title, result_snippet=:result_snippet WHERE request_id=:request_id AND keyword=:keyword AND region=:region AND canonical=:canonical AND host_url=:host_url AND tracking_option=:tracking_option";


			$count_query 	= "UPDATE keyword_search SET start_ranking=:start_ranking, url_site=:url_site, task_id=:task_id, position=:position, result_se_check_url=:result_se_check_url, result_url=:result_url, result_title=:result_title, result_snippet=:result_snippet WHERE id=:id";
			

			$count_stmt = $DBcon->prepare( $count_query );
			$count_stmt->bindParam(':id', $data_id);
			$count_stmt->bindParam(':start_ranking', $organic['result_position']);
			$count_stmt->bindParam(':result_url', $organic['result_url']);
			$count_stmt->bindParam(':result_title', $organic['result_title']);
			$count_stmt->bindParam(':result_snippet', $organic['result_snippet']);
			$count_stmt->bindParam(':url_site', $organic['post_site']);
			$count_stmt->bindParam(':task_id', $organic['task_id']);
			$count_stmt->bindParam(':position', $organic['result_position']);
			$count_stmt->bindParam(':result_se_check_url', $organic['result_se_check_url']);

			if (!$count_stmt->execute()) {
				$saveData	=	 ($count_stmt->errorInfo());
			}else{
				$params		=	array(
					'request_id'	=> $request['request_id'],
					'keyword_id'	=>	$data_id,
					'position'		=>	$organic['result_position'],
					'updated_at'	=>	date('Y-m-d H:i:s')
				);

				$apiData		=	array(
					'request_id'	=> $request['request_id'],
					'keyword_name'	=>	$organic['post_key'],
					'domain_name'	=>	$organic['post_site'],
					'api_name'		=>	'New Keyword - Rank Api',
				);
				addDataSeoApiUnit($apiData);
				unset($apiData);
				$add_position	=	addKeywordPosition($params);
				$saveData[]	=	$add_position;	 
			}			
//			$saveData	=	 $count_stmt->debugDumpParams();
		}
	} else {
		$saveData	=	 array('result'=>'status not ok');
	}
} else {
	$saveData	=	 array('result'=>'all_empty Data');
}


$mail->Body    = $post_data;
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= json_encode($request);
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= json_encode($saveData);

if($mail->send()) {
	$response['status'] = 'success';
} else {
	$response['status'] = 'error'; // could not register
	$response['message'] = $mail->ErrorInfo;
}	





?>
