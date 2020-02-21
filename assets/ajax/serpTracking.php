<?php

header('Content-type: application/json');

require_once('../../includes/config.php');
require_once('../../vendor/autoload.php');
require_once("../../includes/functions.php");
require_once('dataForceApi/RestClient.php');

if ($_POST) {
	$response		=	array();
	if($_POST['action'] == 'serp_track') {

		$check_result		=	checkKeywordExsist($_REQUEST);
			try {
				$client = new RestClient('https://api.dataforseo.com/', null, 'ishan@siliconbeachdigital.com', 'TB5IdHh0B28vagDB');
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
			$request_id         =   $_REQUEST['request_id'];
			$url				=	rtrim($_REQUEST['url'], '/');
			$region				= 	$_REQUEST['region'];	
			$tracking			= 	$_REQUEST['tracking_option'];	
			$canonical			= 	$_REQUEST['loc_name_canonical'];	
			$language			= 	$_REQUEST['language'];	
			$tasks				=	explode(PHP_EOL, $_REQUEST['cities']);
			$html				=	'';
			foreach($tasks as $key) {
				$check_key	=	trim(str_replace(PHP_EOL, ',', $key ) );
				$get_country	=	explode(',', $canonical);
				try {
					$live_data_array[] =array(
						"language" => "en",
						"loc_name_canonical"=> $get_country[count($get_country) - 1],
						"key" => $key,
					);
				} catch (RestClientException $e) {
					echo "\n";
					print "HTTP code: {$e->getHttpCode()}\n";
					print "Error code: {$e->getCode()}\n";
					print "Message: {$e->getMessage()}\n";
					print  $e->getTraceAsString();
					echo "\n";
					exit();
				}
				if(!empty($check_result)) {
				    //if the keyword not exit in db.
					if(!in_array($check_key, $check_result)) {
						$sv_post_result		=	$client->post('v2/kwrd_sv', array('data' => $live_data_array));
						unset($live_data_array);
						$apiData		=	array(
							'request_id'	=> 	$request_id,
							'keyword_name'	=>	$key,
							'domain_name'	=>	$url,
							'api_name'		=>	'New Keyword - Search Volumne Api',
						);
						addDataSeoApiUnit($apiData);
						unset($apiData);
						$cmp				=	!empty($sv_post_result["results"][0]['cmp']) ? $sv_post_result["results"][0]['cmp'] : 0;
						$sv					=	!empty($sv_post_result["results"][0]['sv']) ? $sv_post_result["results"][0]['sv'] : 0;
						$_REQUEST['cmp']	=	$cmp;
						$_REQUEST['sv']		=	$sv;
						$_REQUEST['key']	=	$check_key;
						$my_unq_id      	=   mt_rand(0, 30000000); // your unique ID. we will return it with all results. you can set your database ID, string, etc.
						if($tracking == 'mobile') {
							$option_txt	=	' mobile';
						}else{
							$option_txt	=	'';
						}
						$insert_id =	(saveKeywords($_REQUEST));			//Insert keyword in db
						$post_array[$my_unq_id] = array(
							"priority" => 1,
							"site" => $url,
							"se_name" => $region.$option_txt,
							"se_language" => $language,
							"loc_name_canonical" => $canonical,
							"key" => mb_convert_encoding($key, "UTF-8"),
							"pingback_url"=> $canonical,
							"postback_url" => "https://seoreports.me/DataforceApi/keyword_rank.php?request_id=$request_id&tracking=$tracking&url=$url&region=$region&cmp=$cmp&sv=$sv&data_id=$insert_id"
						); 

						$html	.=	'<tr class=""><td> <a href="#" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td><td><figure><a href="#"><figcaption>'.strtoupper($url)[0].'</figcaption></a></figure><cite>'.$url.'</cite></td><td> <img src="https://seoreports.me/assets/scripts/country/flags/'.getCountryByCode($canonical).'.png"> <i class="fa fa-map-marker" data-toggle="tooltip" title="'.$canonical.'" ></i> '.$key.'</td><td class="serpTd" data-id="'.$insert_id.'" data-value="" >-</td><td>';	
						if($tracking == 'mobile') { 
							$html	.=	'<i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>';
						}
						$html	.=	'-</td><td>-</td><td>-</td><td>-</td><td><i class="fa fa-arrow-up"></i>-</td><td>'.date('d-m-Y').'</td><td>'.$cmp.'</td><td>'.$sv.'</td><td><input type="checkbox" name="check_list[]" value="'.$insert_id.'" /></td></tr>';

					}
				}else {
						$sv_post_result		=	$client->post('v2/kwrd_sv', array('data' => $live_data_array));
						unset($live_data_array);
						$apiData		=	array(
							'request_id'	=> 	$request_id,
							'keyword_name'	=>	$key,
							'domain_name'	=>	$url,
							'api_name'		=>	'New Keyword - Search Volumne Api',
						);
						addDataSeoApiUnit($apiData);
						unset($apiData);
		
						$cmp				=	!empty($sv_post_result["results"][0]['cmp']) ? $sv_post_result["results"][0]['cmp'] : 0;
						$sv					=	!empty($sv_post_result["results"][0]['sv']) ? $sv_post_result["results"][0]['sv'] : 0;
						$_REQUEST['cmp']	=	$cmp;
						$_REQUEST['sv']		=	$sv;
						$_REQUEST['key']	=	$check_key;
						$my_unq_id      	=   mt_rand(0, 30000000); // your unique ID. we will return it with all results. you can set your database ID, string, etc.
						if($tracking == 'mobile') {
							$option_txt	=	' mobile';
						}else{
							$option_txt	=	'';
						}
						$insert_id =	(saveKeywords($_REQUEST));				
						$post_array[$my_unq_id] = array(
							"priority" => 1,
							"site" => $url,
							"se_name" => $region.$option_txt,
							"se_language" => $language,
							"loc_name_canonical" => $canonical,
							"key" => mb_convert_encoding($key, "UTF-8"),
							"pingback_url"=> $canonical,
							"postback_url" => "https://seoreports.me/DataforceApi/keyword_rank.php?request_id=$request_id&tracking=$tracking&url=$url&region=$region&cmp=$cmp&sv=$sv&data_id=$insert_id"
						);
						$html	.=	'<tr class=""><td> <a href="#" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td><td><figure><a href="#"><figcaption>'.strtoupper($url)[0].'</figcaption></a></figure><cite>'.$url.'</cite></td><td> <img src="https://seoreports.me/assets/scripts/country/flags/'.getCountryByCode($canonical).'.png"> <i class="fa fa-map-marker" data-toggle="tooltip" title="'.$canonical.'" ></i> '.$key.'</td><td class="serpTd" data-id="'.$insert_id.'" data-value="" >-</td><td>';	
						if($tracking == 'mobile') { 
							$html	.=	'<i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>';
						}
						$html	.=	'-</td><td>-</td><td>-</td><td>-</td><td><i class="fa fa-arrow-up"></i>-</td><td>'.date('d-m-Y').'</td><td>'.$cmp.'</td><td>'.$sv.'</td><td><input type="checkbox" name="check_list[]" value="'.$insert_id.'" /></td></tr>';
	
					}

			}
			if (count($post_array) > 0) {
				try {
					$task_post_result = $client->post('/v2/rnk_tasks_post', array('data' => $post_array));
					$post_array = array();
					$response['status'] = '1'; // Insert Data Done
					$response['error'] = '0';
					$response['message'] = 'Keyword Added Successfully';
					$response['html']	=	$html;
				} catch (RestClientException $e) {
					$response['status'] = '2'; // Insert Data Done
					$response['error'] = '2';
					$response['message'] = $e->getMessage();
				}
				
			}else {
				$response['status'] = '1'; // Insert Data Done
				$response['error'] = '0';
				$response['message'] = 'Already Added';
			}
	} else if($_POST['action'] == 'remove_keyword'){
		$request_id     =   $_REQUEST['data_id'];
		$delete_query	=	"DELETE FROM keyword_search WHERE id =  $request_id "; 
		$delete_stmt	=	$DBcon->prepare($delete_query);
		$delete_stmt->bindParam(':keyword', $tag);
		if($delete_stmt->execute()) {			
			$response['status'] = '1'; // Insert Data Done
			$response['error'] = '0';
			$response['message'] = 'Data delete Successfully';
		} else {
			$response['status'] = '2'; // Insert Data Done
			$response['error'] = '2';
			$response['message'] = 'Getting Error to delete data';
		}

	} else if($_POST['action'] == 'update_serp'){
		$updated_value  =   $_REQUEST['start_date'];
		$request_id		=	$_REQUEST['request_id'];
		$update_query	=	"UPDATE keyword_search SET start_ranking=$updated_value WHERE id=$request_id";
		$update_stmt	=	$DBcon->prepare($update_query);
		if($update_stmt->execute()) {			
			$response['status'] = '1'; // Insert Data Done
			$response['error'] = '0';
			$response['message'] = 'Data updated successfully';
		} else {
			$response['status'] = '2'; // Insert Data Done
			$response['error'] = '2';
			$response['message'] = 'Getting Error to update data';

		}

	} else if($_POST['action'] == 'update_time'){
		//$updated_value  =   $_REQUEST['start_date'];
		$request_id		=	$_REQUEST['request_id'];
		$result			=	getLastUpdateKeyword($request_id);
		if($result) {			
			$response['status'] = '1'; // Insert Data Done
			$response['error'] 	= '0';
			$response['time'] 	= calculate_time_span($result);
		} else {
			$response['status'] = '2'; // Insert Data Done
			$response['error'] = '2';
			$response['message'] = 'Getting Error to update data';

		}

	} else if($_POST['action'] == 'multiple_select'){
		$checked		=   $_REQUEST['selected_ids'];
		$type			=	$_REQUEST['type'];
		if($type == 'unfavorite') {
			$msg	=	'Unfavorite mark';
			foreach($checked as $check) {
				$update_query	=	"UPDATE keyword_search SET `is_favorite` = '0' WHERE id = $check";
				$update_stmt	=	$DBcon->prepare($update_query);
				$result			=	$update_stmt->execute();
			}
		} else if($type == 'favorite') {
			$msg	=	'Favorite mark';
			foreach($checked as $check) {
				$update_query	=	"UPDATE keyword_search SET `is_favorite` = '1' WHERE id = $check";
				$update_stmt	=	$DBcon->prepare($update_query);
				$result			=	$update_stmt->execute();
			}
		} else if ($type == 'multiDelete'){
			$msg	=	'Delete record';
			foreach($checked as $check) {
//				$update_query	=	"DELETE kp.*, ks.* FROM keyword_search ks, keyword_position kp WHERE ks.id =  $check AND ks.id = kp.keyword_id "; 
				$update_query	=	"DELETE ks.* FROM keyword_search ks WHERE ks.id =  $check"; 
				$update_stmt	=	$DBcon->prepare($update_query);
				$result			=	$update_stmt->execute();
				
				$update_query	=	"DELETE kp.* FROM keyword_search ks, keyword_position kp WHERE kp.keyword_id =  $check"; 
				$update_stmt	=	$DBcon->prepare($update_query);
				$result			=	$update_stmt->execute();
			}
		}

		$getSerpValue               =   getSerpValue($_REQUEST['request_id']);
		//echo   '<pre>'; print_r($getSerpValue); die('jagdish');
		$html						=	'';
		if($getSerpValue) {
			foreach($getSerpValue as $serp_data) {
				if($serp_data['is_favorite'] == '1' ) {
					$html	.=	'<tr class="is_favorite"><td> <a href="'.$serp_data['result_se_check_url'].'" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td><td><figure><a href="#"><figcaption>'.strtoupper(parse_url($serp_data['url_site'], PHP_URL_HOST)[0]).'</figcaption></a></figure><cite>'.$serp_data['result_url'].'</cite></td><td> <img src="https://seoreports.me/assets/scripts/country/flags/'.getCountryByCode($serp_data['canonical']).'.png"> <i class="fa fa-map-marker" data-toggle="tooltip" title="'.$serp_data['canonical'].'" ></i> '.$serp_data['keyword'].'</td><td class="serpTd" data-id="'.$serp_data['id'].'" data-value="'.$serp_data['start_ranking'].'" >';
					$html	.=	!empty($serp_data['start_ranking']) ? $serp_data['start_ranking'] : '-';
					$html	.=	'</td><td>';

					if($serp_data['tracking_option'] == 'mobile') { 
						$html	.=	'<i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>';
					}
					$html	.=	!empty($serp_data['position']) ? $serp_data['position'] : '-' ;
					$html 	.=	'</td><td>';
					$html	.=	!empty($serp_data['one_week_ranking']) ? $serp_data['one_week_ranking'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	!empty($serp_data['monthly_ranking']) ? $serp_data['monthly_ranking'] : '-' ;
					$html	.=	'</td><td>';
					$html	.=	!empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; 
					$html	.=	'</td><td><i class="fa fa-arrow-up"></i>';
					$html	.=	!empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	date('d-m-Y', strtotime($serp_data['created'])); 
					$html	.=	'</td><td>';
					$html	.=	$serp_data['cmp'];
					$html	.=	'</td><td>';
					$html	.=	$serp_data['sv'];
					$html	.=	'</td><td><input type="checkbox" name="check_list[]" value="'.$serp_data['id'].'" />
					</td></tr>';
				} else { 
					$html	.=	'<tr class=""><td> <a href="'.$serp_data['result_se_check_url'].'" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td><td><figure><a href="#"><figcaption>'.strtoupper(parse_url($serp_data['url_site'], PHP_URL_HOST)[0]).'</figcaption></a></figure><cite>'.$serp_data['result_url'].'</cite></td><td> <img src="https://seoreports.me/assets/scripts/country/flags/'.getCountryByCode($serp_data['canonical']).'.png"> <i class="fa fa-map-marker" data-toggle="tooltip" title="'.$serp_data['canonical'].'" ></i> '.$serp_data['keyword'].'</td><td class="serpTd" data-id="'.$serp_data['id'].'" data-value="'.$serp_data['start_ranking'].'" >';
					$html	.=	!empty($serp_data['start_ranking']) ? $serp_data['start_ranking'] : '-';
					$html	.=	'</td><td>';

					 if($serp_data['tracking_option'] == 'mobile') { 
						$html	.=	'<i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>';
					}
					$html	.=	!empty($serp_data['position']) ? $serp_data['position'] : '-' ;
					$html	.=	'</td><td>';
					$html	.=	!empty($serp_data['one_week_ranking']) ? $serp_data['one_week_ranking'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	!empty($serp_data['monthly_ranking']) ? $serp_data['monthly_ranking'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	!empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; 
					$html	.=	'</td><td><i class="fa fa-arrow-up"></i>';
					$html	.=	!empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	date('d-m-Y', strtotime($serp_data['created'])); 
					$html	.=	'</td><td>'.$serp_data['cmp'].'</td><td>'.$serp_data['sv'].'</td><td> <input type="checkbox" name="check_list[]" value="'.$serp_data['id'].'" /></td>	</tr>';
					} 
			 	}
		} else {
				$html	.=	'<tr><td colspan="13">Not Found!</td></tr>';
		} 


		if($result) {			
			$response['html']	= $html;	
			$response['status'] = '1'; // Insert Data Done
			$response['error'] = '0';
			$response['message'] = $msg.' successfully';
		} else {
			$response['status'] = '2'; // Insert Data Done
			$response['error'] = '2';
			$response['message'] = 'Getting Error to update data';

		}
		
	}
}

echo json_encode($response);
