<?php
header('Content-type: application/json');
require_once '../../includes/config.php';
require_once '../../vendor/PHPMailer/PHPMailerAutoload.php';
require_once("../../includes/functions.php");
require_once("../../vendor/php/lib/GrabzItClient.class.php");

$path 		= 	dirname(__FILE__)."/pdf/";

if ($_REQUEST) {
	
	$attach_token	=	getShareKeyData($_REQUEST['share_id']);
	$attchment_url  = 	'https://seoreports.me/seo_pdf.php?token='.$attach_token['token'];
	$parse 			= 	parse_url($attach_token['domain_url']);
	$url   			= 	parseUrl($parse['host']); // print		
	$file_name		=	$path.'SEO PERFORMANCE REPORT - '.$url.'.pdf';


	$grabzIt 		= 	new GrabzItClient("NjczYmJiNjc1ZjI0NDFhMDljN2VjM2ViMGVhMjI2N2Q=", "Pz8/WD9FPyE/Pj9sbDQ+Pz8/DBg/P1hASwQ/P1A/Pz8=");
	$options 		= 	new GrabzItPDFOptions();
	$options->setPageSize("A3");
	$options->setDelay(30000);
	$options->setRequestAs(0);
	$path			=	$grabzIt->URLToPDF($attchment_url, $options);
	//$path			=	$grabzIt->URLToImage($attchment_url, $options);
	//$filepath 		= 	"pdf/".$attach_token.".pdf";
	//$filepath 		= 	"pdf/result.jpg";
	$grabzIt->SaveTo($file_name); 	

	if($_REQUEST['action'] == 'email_sent') {
		$to_emails[]	= 	explode(', ', ($_REQUEST['to']));
		$subject	 	= 	($_REQUEST['subject']);
		$message	 	= 	($_REQUEST['message']);
		$from_email		= 	($_REQUEST['mail_from']);
		$from_sender	= 	($_REQUEST['mail_sender_name']);
		$user_id		= 	$_SESSION['user_id'];	
		$request_id		= 	trim($_REQUEST['share_id']);


		$select_query 	= 	"SELECT * FROM user_email_details WHERE user_id=:user_id AND request_id=:request_id";
		$select_stmt = $DBcon->prepare( $select_query );
		$select_stmt->bindParam(':user_id', $user_id);
		$select_stmt->bindParam(':request_id', $request_id);
		$select_stmt->execute();
		$results = $select_stmt->fetchAll();
		if(empty($results)) {
			$query = "INSERT INTO user_email_details(user_id,request_id,email_subject, email_to,email_message,email_sender,email_sender_name) 
						VALUES(:user_id, :request_id, :email_subject,:email_to,:email_message,:email_sender,:email_sender_name)";
			$stmt = $DBcon->prepare( $query );
			$stmt->bindParam(':request_id', $request_id);
			$stmt->bindParam(':user_id', $user_id);
			$stmt->bindParam(':email_subject', $subject);
			$stmt->bindParam(':email_to', $_REQUEST['to']);
			$stmt->bindParam(':email_message', $message);
			$stmt->bindParam(':email_sender', $from_email);
			$stmt->bindParam(':email_sender_name', $from_sender);
			if($stmt->execute()) {
				$response['status'] = 'success';
			} else {
				$response['status'] = 'error'; // could not register
			}	
			
		}else{
			$delete_query 	= 	"DELETE FROM user_email_details WHERE user_id=:user_id AND request_id=:request_id";
			$delete_stmt 	= 	$DBcon->prepare( $delete_query );
			$delete_stmt->bindParam(':user_id', $user_id);
			$delete_stmt->bindParam(':request_id', $request_id);
			if($delete_stmt->execute()) {
				$query = "INSERT INTO user_email_details(user_id,request_id,email_subject, email_to,email_message,email_sender,email_sender_name) 
							VALUES(:user_id, :request_id, :email_subject,:email_to,:email_message,:email_sender,:email_sender_name)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':email_subject', $subject);
				$stmt->bindParam(':email_to', $_REQUEST['to']);
				$stmt->bindParam(':email_message', $message);
				$stmt->bindParam(':email_sender', $from_email);
				$stmt->bindParam(':email_sender_name', $from_sender);
				if($stmt->execute()) {
					$response['status'] = 'success';
				} else {
					$response['status'] = 'error'; // could not register
				}	
			}
		}
		$mail = new PHPMailer;
		$mail->SMTPDebug = 0;                               	// Enable verbose debug output
		$mail->isSMTP();                                      	// Set mailer to use SMTP
		$mail->Host = 'in-v3.mailjet.com';  					// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               	// Enable SMTP authentication
		$mail->Username = '620b20dfedf9de5cccbf1ed4b21971a0';   // SMTP username
		$mail->Password = 'ba4f00c99cd8348a9ddc12bc0a30ce9b';   // SMTP password
		$mail->SMTPSecure = 'ssl';                              // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                      // TCP port to connect to
		$mail->setFrom($from_email, $from_sender);
		foreach($to_emails[0] as $to_email) { 
			$mail->addAddress($to_email);
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
			$response['message'] = $mail->ErrorInfo;
		}	
	}

		
}
	echo json_encode($response);exit;
	
