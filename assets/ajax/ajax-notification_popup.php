<?php
	require_once '../../includes/config.php';
	require_once("../../includes/functions.php");
	
	$response = array();
	$user_id		  =		$_SESSION['user_id'];
	if ($_POST) {
		if($_POST['action'] == 'notification_popup') {
				$query 	= 	"SELECT * FROM download_pdf_request WHERE user_id IN ($user_id) ";
				$stmt 	= 	$DBcon->prepare( $query );
				$stmt->execute();
				$results = $stmt->fetchAll();
		}	
	}

	if(!empty($results)) {
?>
				
		<li class="dropdown-header"><div class="pull-left">PDF Files</div> <div class="pull-right"><a href="#">Remove All</a></div></li>
		<?php foreach($results as $result) { 
				$variable = $result['id'];
				$salt = '@dmin1@1';
				$hash = md5($salt.$variable);
				$get_file_name	=	getShareKeyData($result['request_id']);
		?>					
		<li class="<?php echo ($result['download_status']) == 0 ? '' : 'unread'; ?>"><a href="download_pdf.php?ids=<?php echo $hash; ?>" target="_blank"><strong><?php echo $get_file_name['domain_name']?> </strong><summary>PDF, <time><?php echo time_elapsed_string($result['created']); ?></time></summary></a> <div class="remove-drop-pdf"></div></li>

<?php  }
	} else{	
?>					
					<li class="dropdown-header"><div class="pull-left">PDF Files</div> <div class="pull-right"><a href="#">Remove All</a></div></li>
					<li><a href="#"><strong>No download link Found</strong></a> </li>

<?php } 
exit;
?>
	
	