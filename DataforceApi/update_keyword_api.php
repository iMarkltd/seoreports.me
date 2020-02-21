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
$mail->Body    = $post_data_in;
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';
$mail->Body    .= json_encode($_REQUEST);
$mail->Body    .= '<p></p>';
$mail->Body    .= '<p></p>';


//saveData(json_encode($_REQUEST));


if (!empty($post_data_in)) {
	$post_arr	 = 	json_decode($post_data_in, true);

	if ((!empty($post_arr)) AND ($post_arr["status"] == "ok")) {
		foreach($post_arr["results"]["organic"] as $organic){
			$saveData    =  updateKeywordsUrl($organic, $_REQUEST);

		}
		echo "ok";
	} else {
		//_in_logit_POST('error decode', $post_data_in);
		echo "error";
	}
} else {
	echo "empty POST";
}

$mail->Body    .= json_encode($saveData);


if($mail->send()) {
	$response['status'] = 'success';
} else {
	$response['status'] = 'error'; // could not register
	$response['message'] = $mail->ErrorInfo;
}	





?>
