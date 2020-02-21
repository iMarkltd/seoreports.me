<?php
	require_once '../../includes/config.php';
	global $DBcon;
	if ($_POST) {
		if($_POST['action'] == 'toggle_module') {
			$request_id		= 	trim($_POST['request_id']);
			$user_id		= 	$_SESSION['user_id'];	
			$module			= 	trim($_POST['module']);
			$status			= 	trim($_POST['status']);
			if($status == 1) {
				$delete_query 	= 	"DELETE FROM toggle_module WHERE user_id=:user_id AND request_id=:request_id AND module=:module";
				$delete_stmt 	= 	$DBcon->prepare( $delete_query );
				$delete_stmt->bindParam(':user_id', $user_id);
				$delete_stmt->bindParam(':request_id', $request_id);
				$delete_stmt->bindParam(':module', $module);
				$delete_stmt->execute();
				
			}else{
				$query = "INSERT INTO toggle_module(user_id,request_id,module,status) VALUES(:user_id, :request_id, :module, :status)";
				$stmt = $DBcon->prepare( $query );
				$stmt->bindParam(':request_id', $request_id);
				$stmt->bindParam(':user_id', $user_id);
				$stmt->bindParam(':module', $module);
				$stmt->bindParam(':status', $status);
				if ( $stmt->execute() ) {
					$date_val	=	null;
					$session_count	=	null;
				}
			print_r($stmt->errorInfo()); 

			}
		}
	}
?>
