<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('/var/www/html/includes/config.php');
require_once('/var/www/html/vendor/autoload.php');
require_once("/var/www/html/includes/functions.php");
require_once('/var/www/html/DataforceApi/RestClient.php');
require_once('/var/www/html/vendor/PHPMailer/PHPMailerAutoload.php');
require_once("/var/www/html/vendor/php/lib/GrabzItClient.class.php");

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
$mail->Subject = 'Cron Keyword Rank Functionality';



//saveData(json_encode($_REQUEST));



if (!empty($post_data_in)) {
	$post_arr	 = 	json_decode($post_data_in, true);

	if ((!empty($post_arr)) AND ($post_arr["status"] == "ok")) {
		$post_data 			=	$post_data_in;
		$request 			=	$_REQUEST;
		$count_query 		=   "UPDATE keyword_position SET status='0', updated_at=:updated_at WHERE request_id=:request_id";
		$count_stmt     	=   $DBcon->prepare( $count_query );
		//$count_stmt->bindParam(':id', $data_id);
		$count_stmt->bindParam(':request_id', $request['request_id']);
		$count_stmt->bindParam(':updated_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
		if (!$count_stmt->execute()) {
			return ($count_stmt->errorInfo());
		}else{
			foreach($post_arr["results"]["organic"] as $organic){
				$data_id			=	$_REQUEST['data_id'];

					$params		=	array(
						'request_id'	=> $request['request_id'],
						'keyword_id'	=>	$request['keyword_id'],
						'position'		=>	$organic['result_position'],
						'updated_at'	=>	date('Y-m-d H:i:s')
					);
					$apiData		=	array(
						'request_id'	=> $request['request_id'],
						'keyword_name'	=>	$organic['post_key'],
						'domain_name'	=>	$organic['post_site'],
						'api_name'		=>	'Cron Job - Rank Api',
					);
					addDataSeoApiUnit($apiData);
					unset($apiData);
					$add_position	=	addKeywordPosition($params);
					$saveData[]	=	$add_position;	 
				}			
			}
	} else {
		$saveData	=	 array('result'=>'status not ok');
	}
} else {
	$saveData	=	 array('result'=>'all empty Data');
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
