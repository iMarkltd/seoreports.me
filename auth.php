<?php
ob_start();
require_once('includes/config.php');
require_once("includes/functions.php");
// Load the Google API PHP Client Library.
require_once PATH.'/vendor/autoload.php';
// Start a session to persist credentials.
// Create the client object and set the authorization configuration
// from the client_secrets.json you downloaded from the Developers Console.
$client = new Google_Client();
$client->setAuthConfig(PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
$client->setRedirectUri(FULL_PATH.'auth.php');
$client->setState(@$_REQUEST['id']);
$client->addScope("email");
$client->addScope("profile");
//$client->addScope("https://www.googleapis.com/auth/drive");
$client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
$client->addScope(Google_Service_Webmasters::WEBMASTERS_READONLY);
//$client->addScope('https://www.googleapis.com/auth/adwords');
$client->setAccessType("offline");
$client->setIncludeGrantedScopes(true);
$client->setApprovalPrompt('consent');
// Handle authorization flow from the server.
if (!isset($_GET['code'])) {
	
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
	$client->setApprovalPrompt('consent');
	$client->authenticate($_GET['code']);
	$session_result					= $client->getAccessToken();
	$client->refreshToken($_GET['code']);
	$objOAuthService = 	new Google_Service_Oauth2($client);
	$userData 		 = 	$objOAuthService->userinfo->get();
	$checkOauthId	 =	checkOauthId($userData['id']);
	if(empty($checkOauthId)) {
		echo "if";
		$query 			 = 	"INSERT INTO google_analytics_users(oauth_provider,oauth_uid,first_name,last_name,email,gender,locale,picture,link,google_access_token,google_refresh_token,user_id,token_type,expires_in,id_token,service_created ) 
							VALUES(:oauth_provider, :oauth_uid, :first_name, :last_name, :email, :gender, :locale, :picture, :link, :google_access_token, :google_refresh_token, :user_id, :token_type, :expires_in, :id_token, :service_created )";
		$google			 = 	'google';
		$gender 		 =	($userData['gender'] != '' ? $userData['gender'] : '');
		$link	 		 =	($userData['link'] != '' ? $userData['link'] : '');
		$locale	 		 =	($userData['locale'] != '' ? $userData['locale'] : '');
		$stmt 			 = 	$DBcon->prepare( $query );
		$stmt->bindParam(':oauth_provider', $google, PDO::PARAM_STR);
		$stmt->bindParam(':oauth_uid', $userData['id'], PDO::PARAM_STR);
		$stmt->bindParam(':first_name', $userData['given_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $userData['family_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
		$stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
		$stmt->bindParam(':locale', $locale, PDO::PARAM_STR);
		$stmt->bindParam(':picture', $userData['picture'], PDO::PARAM_STR);
		$stmt->bindParam(':link', $link, PDO::PARAM_STR);
		$stmt->bindParam(':google_access_token', $session_result['access_token'], PDO::PARAM_STR);
		$stmt->bindValue(':google_refresh_token', isset($session_result['refresh_token']) ? $session_result['refresh_token'] : '', PDO::PARAM_STR);
		$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':token_type', $session_result['token_type'], PDO::PARAM_STR);
		$stmt->bindParam(':expires_in', $session_result['expires_in'], PDO::PARAM_STR);
		$stmt->bindParam(':id_token', $session_result['id_token'], PDO::PARAM_STR);
		$stmt->bindParam(':service_created', $session_result['created'], PDO::PARAM_STR);
		$stmt->execute(); 
		$id 			= $DBcon->lastInsertId();
		$analytics_id	= 	$id;
		$request_id		= 	@$_REQUEST['state'];
		$user_id		= 	trim($_SESSION['user_id']);
		$new_query 		= 	"UPDATE semrush_users_account SET google_analytics_id = :analytics_id WHERE user_id = :user_id AND id = :request_id";
		$new_stmt 		= 	$DBcon->prepare( $new_query );
		$new_stmt->bindParam(':analytics_id', $analytics_id);
		$new_stmt->bindParam(':request_id', $request_id);
		$new_stmt->bindParam(':user_id', $user_id);
		$new_stmt->execute(); 
		$_tokenArray	=	json_encode($session_result);
		$client->setAccessToken($_tokenArray);
		$analytics = new Google_Service_Analytics($client);
		$analytics = new Google_Service_Analytics($client);
		getAccountList($analytics, $_REQUEST['state'], $analytics_id);
	}else if(!empty($session_result['access_token'])) {
		echo "else if";
		$query 			 			= "Update google_analytics_users SET 
											oauth_provider = :oauth_provider, 
											oauth_uid = :oauth_uid,
											first_name = :first_name,
											last_name = :last_name,
											email = :email,
											gender = :gender,
											locale = :locale,
											picture = :picture,
											link = :link,
											google_access_token = :google_access_token,
											google_refresh_token = :google_refresh_token,
											user_id = :user_id,
											token_type = :token_type,
											expires_in = :expires_in,
											id_token = :id_token,
											service_created = :service_created
											WHERE user_id = :user_id AND oauth_uid = :oauth_uid
										";
		$google			 			= 'google';
		$refresh_token 				= isset($session_result['refresh_token']) ? $session_result['refresh_token'] : $checkOauthId['google_refresh_token'];
		$gender 					=	($userData['gender'] != '' ? $userData['gender'] : '');
		$link	 		 			=	($userData['link'] != '' ? $userData['link'] : '');
		$locale	 		 			=	($userData['locale'] != '' ? $userData['locale'] : '');
		$stmt 						= $DBcon->prepare( $query );
		$stmt->bindParam(':oauth_provider', $google, PDO::PARAM_STR);
		$stmt->bindParam(':oauth_uid', $userData['id'], PDO::PARAM_STR);
		$stmt->bindParam(':first_name', $userData['given_name'], PDO::PARAM_STR);
		$stmt->bindParam(':last_name', $userData['family_name'], PDO::PARAM_STR);
		$stmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
		$stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
		$stmt->bindParam(':locale', $locale, PDO::PARAM_STR);
		$stmt->bindParam(':picture', $userData['picture'], PDO::PARAM_STR);
		$stmt->bindParam(':link', $link, PDO::PARAM_STR);
		$stmt->bindParam(':google_access_token', $session_result['access_token'], PDO::PARAM_STR);
		$stmt->bindValue(':google_refresh_token', $refresh_token, PDO::PARAM_STR);
		$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindParam(':token_type', $session_result['token_type'], PDO::PARAM_STR);
		$stmt->bindParam(':expires_in', $session_result['expires_in'], PDO::PARAM_STR);
		$stmt->bindParam(':id_token', $session_result['id_token'], PDO::PARAM_STR);
		$stmt->bindParam(':service_created', $session_result['created'], PDO::PARAM_STR);
		$stmt->execute(); 
		$_tokenArray	=	json_encode($session_result);
		$client->setAccessToken($_tokenArray);
		if ($client->isAccessTokenExpired()) {
			$client->refreshToken($session_result['refresh_token']);
		}
		$analytics = new Google_Service_Analytics($client);
		
		$analytics_id 		=	getGoogleAnalyticId($_SESSION['user_id'], $userData['id']);
		getAccountList($analytics, $_REQUEST['state'], $analytics_id['id']);
		
		
	}
		//exit;
	//print_r($stmt->errorInfo()); exit;
//	echo $redirect_uri = FULL_PATH.'/google_accounts.php';
	echo $redirect_uri = FULL_PATH.'project-settings.php?id='.$_REQUEST['state'];
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}