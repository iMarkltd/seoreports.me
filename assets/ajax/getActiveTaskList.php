<?php

	header('Content-type: application/json');

	require_once('../../includes/config.php');
	require_once('api/semrush_api.php');
	require_once('../../vendor/autoload.php');
	require_once("../../includes/functions.php");

	global $DBcon;
	$active_task_links	=	'';
	if($_POST){
		if($_POST['action'] == 'get_active_data'){
			$active_task_links    =    getActiveTaskList($_POST['task_val']);
		}
	}

/*	?>
		<option value="">Please Select</option>
	<?php 
	if(!empty($active_task_links)) {
		foreach($active_task_links as $link) {
	?>			
			<option value="<?php echo $link['id']?>"><?php echo $link['category_name']; ?></option>
	<?php			
		}
	}

echo json_encode('<option value="">Select State</option><option value="6">Web 2.0</option><option value="7">Blog Comments</option><option value="8">Social Backlinks</option><option value="29">adf</option><option value="30">af</option><option value="31">dse</option><option value="32">seee</option><option value="42">Social Bookmarks</option><option value="43">Web2.0</option><option value="44">profile links</option><option value="45">adf</option><option value="46">af</option><option value="47">dse</option><option value="48">seee</option><option value="49">Social Bookmarks</option><option value="50">Web2.0</option><option value="51">profile links</option>');

*/


$html	= '<option value="">Please Select</option>'; 
if(!empty($active_task_links)) {
	foreach($active_task_links as $link) {
		$html .='<option value="'.$link['id'].'">'.$link['category_name'].'</option>';
	}
}

echo json_encode($html);
