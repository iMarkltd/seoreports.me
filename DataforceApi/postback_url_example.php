<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once('vendor/PHPMailer/PHPMailerAutoload.php');
require_once("includes/functions.php");
require_once("vendor/php/lib/GrabzItClient.class.php");

global $DBcon;

$mail = new PHPMailer;
$mail->SMTPDebug = 0;                               	// Enable verbose debug output
$mail->isSMTP();                                      	// Set mailer to use SMTP
$mail->Host = 'in-v3.mailjet.com';  					// Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               	// Enable SMTP authentication
$mail->Username = '620b20dfedf9de5cccbf1ed4b21971a0';   // SMTP username
$mail->Password = 'ba4f00c99cd8348a9ddc12bc0a30ce9b';   // SMTP password
$mail->SMTPSecure = 'ssl';                              // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                      // TCP port to connect to
$mail->setFrom('imarkinfotech@gmail.com', 'Imarkinfotech');
$mail->addAddress('navdeep.sharma@imarkinfotech.com');
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Test Keyword Rank Functionality';
$mail->Body    = 'Excuted Successfully';
if($mail->send()) {
	$response['status'] = 'success';
} else {
	$response['status'] = 'error'; // could not register
	$response['message'] = $mail->ErrorInfo;
}	



function _in_logit_POST($id_message, $data) {
	@file_put_contents(__DIR__."/postback_url_example.log", PHP_EOL.date("Y-m-d H:i:s").": ".$id_message.PHP_EOL."---------".PHP_EOL.print_r($data, true).PHP_EOL."---------", FILE_APPEND);
}

$post_data_in = file_get_contents('php://input');

if (!empty($post_data_in)) {
	$post_arr	 = 	json_decode($post_data_in, true);
	if ((!empty($post_arr)) AND ($post_arr["status"] == "ok")) {
		$saveData    =  saveKeywordsUrl($post_arr["results"]["organic"][0]);
		foreach($post_arr["results"]["organic"] as $tasks_row) {
			_in_logit_POST($tasks_row["result_position"], $tasks_row);
			//do something with results
		}
		echo "ok";
	} else {
		//_in_logit_POST('error decode', $post_data_in);
		echo "error";
	}
} else {
	echo "empty POST";
}
?>
