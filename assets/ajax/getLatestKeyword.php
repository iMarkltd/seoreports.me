<?php
header('Content-type: application/json');

require_once('../../includes/config.php');
require_once('../../vendor/autoload.php');
require_once("../../includes/functions.php");

$response		    =	array();
$getSerpValue	    =	array();
if($_REQUEST['action'] == 'update_keywords') {
 
	$key	=	true;
	$i 		=	1;	
	while($key){
		$check_result	=	checkKeywordUpdated($_REQUEST['request_id']);
		if(empty($check_result)){
			$key			=	false;
			$getSerpValue   =   getSerpValue($_REQUEST['request_id']);
			break;
		}
	}
		$html						=	'';
		if($getSerpValue) {
			foreach($getSerpValue as $serp_data) {
				$post_data['request_id']    =   $serp_data['request_id'];
				$post_data['keyword_id']    =   $serp_data['id'];
				$getLatestKeyword           =   lastestKeywordPosition($post_data);
				$getOneDayKeyword           =   oneDayKeyword($post_data);
				$getLastSevenDayKeyword     =   weeklyKeywords($post_data);
				$getThirthDayKeyword        =   fourthKeywords($post_data);


				if($serp_data['is_favorite'] == '1' ) {
					$html	.=	'<tr class="is_favorite"><td> <a href="'.$serp_data['result_se_check_url'].'" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td><td><figure><a href="#"><figcaption>'.strtoupper(parse_url($serp_data['url_site'], PHP_URL_HOST)[0]).'</figcaption></a></figure><cite>'.$serp_data['result_url'].'</cite></td><td> <img src="https://seoreports.me/assets/scripts/country/flags/'.getCountryByCode($serp_data['canonical']).'.png"> <i class="fa fa-map-marker" data-toggle="tooltip" title="'.$serp_data['canonical'].'" ></i> '.$serp_data['keyword'].'</td><td class="serpTd" data-id="'.$serp_data['id'].'" data-value="'.$serp_data['start_ranking'].'" >';
					$html	.=	!empty($serp_data['start_ranking']) ? $serp_data['start_ranking'] : '-';
					$html	.=	'</td><td>';

					if($serp_data['tracking_option'] == 'mobile') { 
						$html	.=	'<i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>';
					}
					$html	.=	!empty($getLatestKeyword) ? $getLatestKeyword['position'] : '-';
					$html 	.=	'</td><td>';
					$html	.=	!empty($getOneDayKeyword) ? $getOneDayKeyword['position'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	!empty($getLastSevenDayKeyword) ? $getLastSevenDayKeyword['position'] : '-' ;
					$html	.=	'</td><td>';
					$html	.=	!empty($getThirthDayKeyword) ? $getThirthDayKeyword['position'] : '-' ; 
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
					$html	.=	!empty($getLatestKeyword) ? $getLatestKeyword['position'] : '-';
					$html 	.=	'</td><td>';
					$html	.=	!empty($getOneDayKeyword) ? $getOneDayKeyword['position'] : '-' ; 
					$html	.=	'</td><td>';
					$html	.=	!empty($getLastSevenDayKeyword) ? $getLastSevenDayKeyword['position'] : '-' ;
					$html	.=	'</td><td>';
					$html	.=	!empty($getThirthDayKeyword) ? $getThirthDayKeyword['position'] : '-' ; 
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


		if($html) {			
			$response['html']	= $html;	
			$response['status'] = '1'; // Insert Data Done
			$response['error'] = '0';
		} else {
			$response['status'] = '2'; // Insert Data Done
			$response['error'] = '2';

		}

	echo json_encode($response);

}



