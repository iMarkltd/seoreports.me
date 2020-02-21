<?php
	ini_set('memory_limit', '-1');

	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("includes/report-header.php");
	include_once('assets/ajax/api/semrush_api.php');
    require_once 'vendor/autoload.php';
//	error_reporting(0);
//    ini_set('display_errors', 0);

    error_reporting(E_ALL);
	ini_set('display_errors', 1);

	global $DBcon;

    $token 			= 	$_REQUEST['token_id'];
	$client       	=   new Google_Client();

	$domain_details	=	getUserDomainDetailsByToken($token);
    // print_r($domain_details);
    $user_id        =   $domain_details['user_id'];
	$data			=	'';
	$graph_data		=	array();
	if(!empty($domain_details)){
		$client 			= new Google_Client();
		$profile_info		=	getProfileData($domain_details['id'], $domain_details['user_id']);
		$data				=	checkSemarshApiData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db']);
		$backlink_data		=	checkSemarshBacklinkData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id']);
        $googleAnalytics	=	googleAnalyticsWithoutLogin($domain_details['user_id'], $domain_details['id']);
        // print_r($googleAnalytics); exit;
        if(!empty($googleAnalytics['access_token'])) {
            $profile_data		=	googleAnalyticsProfileData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
            $profile_old_data	=	googleAnalyticsProfileOldData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
        } else {
            $profile_data		=	array();
            $profile_old_data	=	0;
        }

        $checkEditNotes0	=	checkEditNotes0($domain_details['user_id'], $domain_details['id']);
		$checkEditNotes1	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '1');
		$checkEditNotes2	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '2');
		$checkEditNotes3	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '3');
		$checkEditNotes4	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '4');
        $checkGoogleGoal	=	checkGoogleGoal($domain_details['user_id'], $domain_details['id']);

		$checkCurrentGoogleGoal	=	checkCurrentGoogleGoal($domain_details['user_id'], $domain_details['id']);

        $getMozData		    =	checkMozData($domain_details['domain_url'], $domain_details['id'], $domain_details['user_id']);
		$toggle_module		=	getToggleModule($domain_details['id']);
        $searchDateRange    =   getModuleDateRange($domain_details['id'], 'search_console');
        $domainHistoryRange =   getModuleDateRange($domain_details['id'], 'organic_graph');
        $sessionHistoryRange=   getModuleDateRange($domain_details['id'], 'organic_traffice');
        // print_r($sessionHistoryRange);
        $plot_month         =   '';
        $analytics 		    = 	initializeAnalyticsWithoutLogin($domain_details['google_account_id'], $domain_details['user_id']);
        $getCompareChart    =   getCompareChart($domain_details['id']);
        $getSerpValue       =   getSerpValue($domain_details['id']);

        if(!empty($domain_details['domain_register'])){
            $plot_month     =   date('M', strtotime($domain_details['domain_register']));
        }

        if(!empty($domainHistoryRange)){
            $domain_history	    =	dateRangeDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db'], $domainHistoryRange['start_date'],$domainHistoryRange['end_date']);
        }else{
            $domain_history		=	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db']);
        }

        if(!empty($sessionHistoryRange)){
			if(!empty($googleAnalytics['id']) && $googleAnalytics['id'] != 0) {
                $start_date 	            =	date('Y-m-d', strtotime($sessionHistoryRange['start_date']));
                $end_data                   =   date('Y-m-d', strtotime($sessionHistoryRange['end_date']));
                $day_diff            		= 	strtotime($sessionHistoryRange['start_date']) - strtotime($sessionHistoryRange['end_date']);
                $count_days              	=	floor($day_diff/(60*60*24));
                $start_data                 =   date('Y-m-d', strtotime($sessionHistoryRange['start_date'].' '.$count_days.' days'));
                $session_count		        =	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $sessionHistoryRange['start_date'],$sessionHistoryRange['end_date']);
                $combine_session_count	    =	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $start_data, $sessionHistoryRange['start_date']);
               // print_r($sessionHistoryRange); exit;
                foreach($session_count as $key=>$session_details) {
                    $from_dates[] 		=	date('M d', strtotime($session_details['from_date']));
                    $plot_dates[] 		=	date('M', strtotime($session_details['from_date']));
//                    $unix_date			=	$session_details['unix_date']*1000;
                    $count_session[]	=	intval($session_details['total_session']);
                }
                foreach($combine_session_count as $combine){
//                    $unix_date			=	$combine['unix_date']*1000;
                    $combine_session[]	=	intval($combine['total_session']);
                }

                if(!empty($plot_dates) && !empty($plot_month)){
                    $index = array_search($plot_month, $plot_dates);

                    if ($index != false)
                        $plot_index = $index;
                }
                    $getCurrentStats    =   array();
                    $getPreviousStats    =   array();

                if(!empty($googleAnalytics['property_id'])){
                    $getCurrentStats    = 	getMetricsData($analytics, 'ga:'.$googleAnalytics['property_id'], $sessionHistoryRange['start_date'], $sessionHistoryRange['end_date']);
                    $getPreviousStats   = 	getMetricsData($analytics, 'ga:'.$googleAnalytics['property_id'], $start_data, $sessionHistoryRange['start_date']);
                }
                $current_period 	= 	$start_date.' to '.$end_data;
                $previous_period	= 	$start_data.' to '.$start_date;

            } else {
                $count_session      =   '';
                $user_session       =   '';
                $from_dates         =   '';
                $combine_session    =   '';
                $current_period 	= 	'';
                $previous_period	= 	'';
                $plot_index         =   '';
                $getCurrentStats    =   array();
                $getPreviousStats   =   array();

            }
        } else {
            $start_data     =   date('Y-m-d', strtotime('now'));
            $end_data       =   date('Y-m-d', strtotime($start_data." -180 days"));

            $end_combine_date	=	date('Y-m-d', strtotime($end_data." -180 days"));
            $request_id     =   $domain_details['id'];

            $session_count		        =	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $end_data,$start_data);
            $combine_session_count	    =	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $end_combine_date, $end_data);

            $unix_date	=	'';
            $from_dates	=	array();
            $count_session = array();
            $combine_session = array();
            foreach($session_count as $key=>$session_details) {
                $from_dates[] 		=	date('M d', strtotime($session_details['from_date']));
                $plot_dates[] 		=	date('M', strtotime($session_details['from_date']));
//                    $unix_date			=	$session_details['unix_date']*1000;
                $count_session[]	=	intval($session_details['total_session']);
            }
            foreach($combine_session_count as $combine){
//                    $unix_date			=	$combine['unix_date']*1000;
                $combine_session[]	=	intval($combine['total_session']);
            }

            if(!empty($plot_dates) && !empty($plot_month)){
                $index = array_search($plot_month, $plot_dates);

                if ($index != false)
                    $plot_index = $index;
            }
                $getCurrentStats    =   array();
                $getPreviousStats    =   array();

            if(!empty($googleAnalytics['property_id'])){
                $getCurrentStats    = 	getMetricsData($analytics, 'ga:'.$googleAnalytics['property_id'], $end_data, $start_data);
                $getPreviousStats   = 	getMetricsData($analytics, 'ga:'.$googleAnalytics['property_id'], $end_combine_date, $end_data);
            }
            $current_period 	= 	$end_data.' to '.$start_data;
            $previous_period	= 	$end_combine_date.' to '.$end_data;
        }

        $organic_cost = $organic_keywordss = $organic_traffic = $adwords_cost = $adwords_keywords = $adwords_traffic = $rank	=	array();
        $organic_history    =  '';
        $organic_date       =  '';

        if(!empty($domain_history)) {
            if(!empty($domain_details['domain_register'])){
                $plot_month     =   date('Y,m', strtotime($domain_details['domain_register']));
            }
            foreach($domain_history as $history) {
                $timeZone 				= 	new DateTimeZone("Asia/Kolkata");
                $dateTime 				= 	\DateTime::createFromFormat('Ymd', $history['date_time'])->format('U') * 1000;
                $organic_keywordss[]    =	array($dateTime, intval($history['organic_keywords']));
                $organic_date			.=	$dateTime.", ";
                $organic_plot[]         =   date('Y,m', strtotime($history['date_time']));

            }
            $organic_history			=		$domain_history[(count($domain_history)-1)]['organic_keywords'];

            if(!empty($organic_plot) && !empty($plot_month)){
                $index = array_search($plot_month, $organic_plot);
                if ($index != false){
                    if(date("m",strtotime($domain_details['domain_register'])) == 01) {
                        $organic_index = date('Y,0', strtotime($domain_details['domain_register']));
                    }else {
                        $organic_index = date('Y,m', strtotime($domain_details['domain_register'].'-1 months'));
                    }
                }
            }
        }

        $toggle_module			=	getToggleModule($domain_details['id']);
        if(empty($toggle_module))
            $toggle_module      =   array();

        if($checkGoogleGoal['goal_count'] == 0){
            $goal_result	=	"0%";
        }else{
            $goal_result	=	(($checkCurrentGoogleGoal['total'] - $checkGoogleGoal['goal_count']) / $checkGoogleGoal['goal_count'] * 100)."%";
        }


	}else{
		header("Location:404.php");
	}



?>

<!-- main-container -->
<div class="main-container report-page clearfix">

<?php include("includes/ranking-nav-sidebar.php") ?>

<div class="report-progress-top">
    <div class="progress" >
        <div class="progress-bar progress-bar-striped active" role="progressbar"
        aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Intial Stage " data-placement="bottom">
            Month 1
        </div>
        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Intial Stage " data-placement="bottom">
            Month 2
        </div>
        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Intial Stage " data-placement="bottom">
            Month 3
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Growth Stage " data-placement="bottom">
            Month 4
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Growth Stage " data-placement="bottom">
            Month 5
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Growth Stage " data-placement="bottom">
            Month 6
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Mature Stage " data-placement="bottom">
            Month 7
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Mature Stage " data-placement="bottom">
            Month 8
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Mature Stage " data-placement="bottom">
            Month 9
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Result Stage " data-placement="bottom">
            Month 10
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Result Stage " data-placement="bottom">
            Month 11
        </div>

        <div class="progress-bar" role="progressbar" style="width:8.33%" data-toggle="tooltip" title="Campaign Progress: Result Stage " data-placement="bottom">
            Month 12
        </div>
    </div>
</div>

    <div class="report-header">
        <div class="report-top-head">
            <div class="container">
                <h2>SEO Dashboard <small><?php echo date('F, Y')?></small></h2>
            </div>
        </div>
        <div class="report-nav">
            <div class="container">
                <?php if(!empty($profile_info)) { ?>
                <div class="left">
                    <?php
						$user_id	=	@$profile_info['user_id'];
						$request_id	=	@$profile_info['request_id'];
						$path 		= 	'assets/ajax/uploads/'.$user_id.'/'.$request_id."/";
						$files 		= 	scandir($path);
						$files 		= 	array_diff(scandir($path), array('.', '..'));
					?>

                    <div class="client-share-logo" style="background-image: url('<?php echo $path.$files[2]; ?>');">
                    </div>
                </div>
                <p> <i class="fa fa-globe"></i> <?php echo urlToDomain($domain_details['domain_url']);?></p>
                <div class="right">
                    <a href="tel:<?php echo @$profile_info['contact_no']?>"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo @$profile_info['contact_no']?></a> <a href="mailto:<?php echo @$profile_info['email']?>"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo @$profile_info['email']?></a>
                </div>
                <?php } ?>
            </div>
        </div>

    </div>
    <!-- #end main-navigation -->

    <!-- content-here -->
    <div class="content-container" id="content">

        <div class="page m134 page-charts-c3" ng-controller="c3ChartDemoCtrl">

            <div class="page-wrap">

                <div class="row d-flex">
                <?php if(!in_array('company_note', $toggle_module)) {?>
                    <div class="col-md-6">
                        <div class="report-box">
                            <h5>
                                SEO Summary
                            </h5>

                            <?php
									if(!empty($checkEditNotes0)) {
										 echo $checkEditNotes0['edit_section'];
									}
								?>
                        </div>
                    </div>
                <?php } ?>
                <?php if(!in_array('company_note', $toggle_module))  $col = '6'; else $col = '12'; ?>
                    <div class="col-md-<?php echo $col; ?> four-report-box">
                        <?php if(!in_array('top_keyword', $toggle_module)) { ?>
                        <div class="report-box purple">

                            <figure>
                                <img src="/assets/images/OrganicKeywordsArrow.png" alt="">
                            </figure>

                            <?php
                                if(!empty($domain_history) && !empty($organic_history) ) {
                                    if(count($domain_history)> 2) {
                                        $count_history		=   $domain_history[(count($domain_history)-2)]['organic_keywords'];
                                        if ($count_history) {
                                        $organic_keywords   =   round(($organic_history-$count_history)/$count_history*100, 2);
                                        } else {
                                             $organic_keywords = 0;
                                        }
                                    }else{
                                        $organic_keywords	=  100;
                                    }
                                } else if(empty($organic_history) && !empty($domain_history) ) {
                                    $organic_keywords	=  -100;
                                } else if(!empty($organic_history) && empty($domain_history) ) {
                                    $organic_keywords	=  100;
                                } else{
                                    $organic_keywords	=  'N/A';
                                }
                            ?>
                            <h5>
                                <?php echo $organic_history; ?>
                                <?php if($organic_keywords != 'N/A') {  ?>
                                    <span id="traffic_growth" class="<?php echo ($organic_keywords < 0 ? 'red' : 'green' ); ?>"> <?php echo ($organic_keywords> 0 ? '+' : '' );  echo $organic_keywords."%"; ?>
                                        <i class="fa fa-arrow-circle-<?php echo ($organic_keywords < 0 ? 'down' : 'up' ); ?>" aria-hidden="true"></i>
                                    </span>
                                <?php } else{ echo $organic_keywords; }  ?>

                                <small>Organic keywords Ranking In Google</small>
                            </h5>

                            <cite>Keywords</cite>

                        </div>
                        <?php } ?>
                        <?php if(!in_array('top_traffic', $toggle_module)) { ?>
                        <div class="report-box green">

                            <figure>
                                <img src="/assets/images/report-google-analytics-icon.png" alt="">
                            </figure>
                            <?php// $traffic_growth	= round(($profile_old_data['sessions_new']-$profile_old_data['sessions_old'])/$profile_old_data['sessions_old']*100, 2); ?>
                            <h5 class="traffic_growth">
                                <?php echo @$getCurrentStats['sessions']; ?>
                            <span id="traffic_growth" class="<?php echo ($getCurrentStats < 0 ? 'red' : 'green' ); ?>">
                                <?php
                                    if(!empty($getCurrentStats['sessions']) && !empty($getPreviousStats['sessions']) ) {
                                        $traffic_growth	= number_format(($getCurrentStats['sessions']  - $getPreviousStats['sessions']) / $getPreviousStats['sessions'] * 100, 2).'%';
                                    } else if(empty($getCurrentStats['sessions']) && !empty($getPreviousStats['sessions']) ) {
                                        $traffic_growth	= ' -100%';
                                    } else if(!empty($getCurrentStats['sessions']) && empty($getPreviousStats['sessions']) ) {
                                        $traffic_growth	= ' 100%';
                                    } else{
                                        $traffic_growth	= 'N/A';
                                    }

                                ?>
                                <?php if($traffic_growth != 'N/A') {
                                            echo $traffic_growth;
                                ?>
                                            <i class="fa fa-arrow-circle-<?php echo ($traffic_growth < 0 ? 'down' : 'up' ); ?>" aria-hidden="true"></i>
                                <?php
                                        } else { echo $traffic_growth; }
                                ?>
                            </span>

                                <small>
                                    Organic Visitors From Google
                                </small>
                            </h5>
                            <cite>Traffic</cite>
                        </div>
                        <?php } ?>
                        <?php if(!in_array('top_backlink', $toggle_module)) { ?>
                        <div class="report-box pink">
                            <figure>
                                <img src="/assets/images/report-ahrefs-logo.png" alt="">
                            </figure>

                            <h5>
                            <?php
                                if(!empty($backlink_data) && count($backlink_data) > 0 ) echo count($backlink_data);
                                else  echo 'N/A';
                            ?>
                                <small>Backlink Profile</small>
                            </h5>

                            <cite>Links</cite>
                        </div>
                        <?php } ?>
                        <?php if(!in_array('top_goal', $toggle_module)) { ?>
                        <div class="report-box yellow">

                            <figure>
                                <img src="/assets/images/ReportGoogleAnaLyticsGoals.png" alt="">
                            </figure>

                            <h5 class="google_analytics"><?php echo $checkCurrentGoogleGoal['total']; ?>
                                <span class="<?php echo ($goal_result < 0 ? 'red' : 'green' ); ?>">
                                    <?php
                                        if(empty($goal_result)) {
                                            echo '0.00';
                                        } else {
                                            echo @number_format($goal_result, 2, '.', '');
                                        }

                                    ?>
                                    <i class="fa fa-arrow-circle-<?php echo ($goal_result < 0 ? 'down' : 'up' ); ?>" aria-hidden="true"></i>
                                </span>
                                <small>
                                    Google AnaLytics <br>Goals
                                </small>
                            </h5>

                            <cite>Goals</cite>
                        </div>
                        <?php } ?>

                    </div>

                    <div class="col-md-12 four-report-box three-report-box">
                        <div class="report-box purple2">
                            <figure>
                                <img src="/assets/images/roger_and_logo_moz-min.png" alt="">
                            </figure>
                            <h5>
                                <?php echo $getMozData['pageAuthority'].' /100'; ?>
                            </h5>

                            <cite>Page Authority</cite>
                        </div>

                        <div class="report-box orange">
                            <figure>
                                <img src="/assets/images/roger_and_logo_moz-min.png" alt="">
                            </figure>

                            <h5><?php echo $getMozData['domainAuthority'].' /100'; ?></h5>
                            <cite>Domain Authority</cite>
                        </div>

                        <div class="report-box darkGreen">
                            <figure>
                                <img src="/assets/images/majestic-logo-black-trans-large.png" alt="">
                            </figure>
                            <h5>35</h5>
                            <cite>Trust Flow</cite>
                        </div>

                    </div>


                </div>
                <?php if(!in_array('serp_ranking_table', $toggle_module)) {?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="report-box">

                                    <div class="top-project-group">
                                        <div class="left">
                                            <h5>
                                                Live Keyword Tracking
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="table-cover" id="serpTable">
                                        <table id="serpTableRow">
                                            <thead>
                                                <tr>
                                                    <th> &nbsp; </th>
                                                    <th>Domain</th>
                                                    <th>Keyword</th>
                                                    <th>Start</th>
                                                    <th><img src="/assets/images/google-logo-icon.png"></th>
                                                    <th>1 Day</th>
                                                    <th>7 Days</th>
                                                    <th>30 Days</th>
                                                    <th>Life</th>
                                                    <th>Date Added</th>
                                                    <th>Comp</th>
                                                    <th>Ms</th>
                                                </tr>
                                            </thead>
                                            <tbody class="serpTableBody">
                                                <?php if($getSerpValue) {
                                                        foreach($getSerpValue as $serp_data) {
                                                            if($serp_data['is_favorite'] == '1' ) {
                                                ?>
                                                            <tr class="is_favorite">
                                                                <td> <a href="<?php echo $serp_data['result_se_check_url'] ?>" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td>
                                                                <td>
                                                                    <figure><a href="#"><figcaption><?php echo getDomain($domain_details['domain_url'])[0]; ?></figcaption></a></figure>
                                                                    <cite><?php echo $serp_data['result_url']?></cite>
                                                                </td>
                                                                <td> <img src="https://seoreports.me/assets/scripts/country/flags/<?php echo getCountryByCode($serp_data['canonical']) ?>.png">
                                                                    <i class="fa fa-map-marker" data-toggle="tooltip" title="<?php echo $serp_data['canonical']?>" ></i> <?php echo $serp_data['keyword']; ?>
                                                                </td>
                                                                <td class="serpTd" data-id="<?php echo $serp_data['id']; ?>" data-value="<?php echo $serp_data['start_ranking']?>" ><?php echo !empty($serp_data['start_ranking']) ? $serp_data['start_ranking'] : '-' ; ?></td>
                                                                <td>
                                                                    <?php if($serp_data['tracking_option'] == 'mobile') { ?>
                                                                        <i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>
                                                                    <?php } ?>
                                                                    <?php echo !empty($serp_data['position']) ? $serp_data['position'] : '-' ; ?></td>
                                                                <td><?php echo !empty($serp_data['one_week_ranking']) ? $serp_data['one_week_ranking'] : '-' ; ?></td>
                                                                <td><?php echo !empty($serp_data['monthly_ranking']) ? $serp_data['monthly_ranking'] : '-' ; ?></td>
                                                                <td><?php echo !empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; ?></td>
                                                                <td><i class="fa fa-arrow-up"></i><?php echo !empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime($serp_data['created'])); ?></td>
                                                                <td><?php echo $serp_data['cmp']?></td>
                                                                <td><?php echo $serp_data['sv']?></td>
                                                            </tr>
                                                <?php           } else { ?>
                                                            <tr class="">
                                                                <td> <a href="<?php echo $serp_data['result_se_check_url'] ?>" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td>
                                                                <td>
                                                                    <figure><a href="#"><figcaption><?php echo getDomain($domain_details['domain_url'])[0]; ?></figcaption></a></figure>
                                                                    <cite><?php echo $serp_data['result_url']?></cite>
                                                                </td>
                                                                <td> <img src="https://seoreports.me/assets/scripts/country/flags/<?php echo getCountryByCode($serp_data['canonical']) ?>.png">
                                                                    <i class="fa fa-map-marker" data-toggle="tooltip" title="<?php echo $serp_data['canonical']?>" ></i> <?php echo $serp_data['keyword']; ?>
                                                                </td>
                                                                <td class="serpTd" data-id="<?php echo $serp_data['id']; ?>" data-value="<?php echo $serp_data['start_ranking']?>" ><?php echo !empty($serp_data['start_ranking']) ? $serp_data['start_ranking'] : '-' ; ?></td>
                                                                <td>
                                                                    <?php if($serp_data['tracking_option'] == 'mobile') { ?>
                                                                        <i class="fa fa-mobile" data-toggle="tooltip" title="Google Mobile Ranking"></i>
                                                                    <?php } ?>
                                                                    <?php echo !empty($serp_data['position']) ? $serp_data['position'] : '-' ; ?></td>
                                                                <td><?php echo !empty($serp_data['one_week_ranking']) ? $serp_data['one_week_ranking'] : '-' ; ?></td>
                                                                <td><?php echo !empty($serp_data['monthly_ranking']) ? $serp_data['monthly_ranking'] : '-' ; ?></td>
                                                                <td><?php echo !empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; ?></td>
                                                                <td><i class="fa fa-arrow-up"></i><?php echo !empty($serp_data['life_ranking']) ? $serp_data['life_ranking'] : '-' ; ?></td>
                                                                <td><?php echo date('d-m-Y', strtotime($serp_data['created'])); ?></td>
                                                                <td><?php echo $serp_data['cmp']?></td>
                                                                <td><?php echo $serp_data['sv']?></td>
                                                            </tr>
                                            <?php           } ?>
                                            <?php }
                                                } else {
                                            ?>
                                            <tr>
                                                <td colspan="12">Not Found!</td>
                                            </tr>
                                            <?php  } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!in_array('organic_graph', $toggle_module)) {?>
                            <div class="report-box">
                                <h5>Organic Keyword Growth (12 months)</h5>
                                <div id="c3chartline"></div>
                            </div>
                        <?php } ?>
                        <input type="hidden" name="compare_graph" id="compare_graph" value="<?php if(!empty($getCompareChart) && $getCompareChart['compare_status'] == 1) echo 1; ?>"  >


                        <?php if(!in_array('organic_traffic', $toggle_module)) {?>
                            <div class="report-box">
                                <h5>Organic Traffic Growth (6 months)</h5>
                                <div class="table-data-outer">
                                    <div class="total_data" style="">
                                        <h5>Sessions</h5>
                                        <small>Organic Traffic</small>
                                        <?php
                                        // print_r($getCurrentStats['sessions']);
                                            if(!empty($getCurrentStats['sessions'])) {
                                                if ($getCurrentStats['sessions'] <= 0 || $getPreviousStats['sessions'] <= 0 ) {
                                                    $total_sessions = "100%";
                                                } else {
                                                    $total_sessions	= number_format(($getCurrentStats['sessions']  - $getPreviousStats['sessions']) / $getPreviousStats['sessions'] * 100, 2).'%';
                                                }
                                            } else{
                                                $total_sessions = "100%";
                                            }
                                        ?>
                                        <div class="total_session <?php if($total_sessions < 0) echo 'red' ?>">
                                            <?php echo $total_sessions; ?>
                                        </div>
                                        <div class="new_session">
                                            <?php
                                                $current_session    =   !empty($getCurrentStats['sessions']) ? $getCurrentStats['sessions'] : '0';
                                                $previous_session   =   !empty($getPreviousStats['sessions']) ? $getPreviousStats['sessions'] : '0';
                                                echo $current_session.' vs '.$previous_session;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="total_data" style="">
                                        <h5>Users</h5>
                                        <small>Organic Traffic</small>
                                        <?php
                                            if(!empty($getCurrentStats['users'])){
                                                if ($getCurrentStats['users'] <= 0 || $getPreviousStats['users'] <= 0 ) {
                                                    $total_users = "100%";
                                                } else {
                                                    $total_users = number_format(($getCurrentStats['users']  - $getPreviousStats['users']) / $getPreviousStats['users'] * 100, 2).'%';
                                                }
                                            }else{
                                                $total_users = "100%";
                                            }
                                        ?>
                                        <div class="total_users <?php if($total_users < 0) echo 'red' ?>">
                                            <?php echo $total_users; ?>
                                        </div>
                                        <div class="new_users">
                                            <?php
                                                $current_users    =   !empty($getCurrentStats['users']) ? $getCurrentStats['users'] : '0';
                                                $previous_users   =   !empty($getPreviousStats['users']) ? $getPreviousStats['users'] : '0';
                                                echo $current_users.' vs '.$previous_users;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="total_data" style="">
                                        <h5>Pageviews</h5>
                                        <small>Organic Traffic</small>
                                        <?php
                                            if(!empty($getCurrentStats['pageview'])){
                                                if ($getCurrentStats['pageview'] <= 0 || $getPreviousStats['pageview'] <= 0 ) {
                                                    $total_pageview = "100%";
                                                } else {
                                                    $total_pageview = number_format(($getCurrentStats['pageview']  - $getPreviousStats['pageview']) / $getPreviousStats['pageview'] * 100, 2).'%';
                                                }
                                            }else{
                                                $total_pageview = "100%";
                                            }
                                        ?>
                                        <div class="total_pageview <?php if($total_pageview < 0) echo 'red' ?>" >
                                            <?php echo $total_pageview; ?>
                                        </div>
                                        <div class="new_pageview">
                                            <?php
                                                $current_pageview    =   !empty($getCurrentStats['pageview']) ? $getCurrentStats['pageview'] : '0';
                                                $previous_pageview   =   !empty($getPreviousStats['pageview']) ? $getPreviousStats['pageview'] : '0';
                                                echo $current_pageview.' vs '.$previous_pageview;
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div id="chart-1-container" class="chart">
                                    <?php if(empty($count_session)) {
                                            if(!empty($getCurrentStats['message'])) {
                                                echo '<h2 style="color:red">Please resolve this error from google:- '.$getCurrentStats['message'].'</h2>';
                                            } else{
                                    ?>
                                        <img src="<?php echo 'assets/images/analytics.png'; ?>" />
                                    <?php } } ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if(!in_array('organic_keywords_summary', $toggle_module) || !in_array('organic_keywords_table', $toggle_module)) {?>
                            <div class="report-box">
                                <h5>Organic Keywords</h5>
                                <?php
                                    if(!in_array('organic_keywords_summary', $toggle_module)) {
                                        if(!empty($checkEditNotes1)) {
                                            echo $checkEditNotes1['edit_section'];
                                        }
                                    }
                                ?>
                                <?php if(!in_array('organic_keywords_table', $toggle_module)) {?>

                                    <table id="semrush_organic_table"
                                        class="table table-bordered table-striped table-hover table-condensed dataTable no-footer"
                                        width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Keywords</th>
                                                <th>Current Position</th>
                                                <th>Traffic Percentage</th>
                                                <th>CPC(USD)</th>
                                                <th>Average Vol</th>
                                                <th>URL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                    if(!empty($data)) {
                                                        foreach($data as $record) {
                                                ?>
                                            <tr>
                                                <td><?php echo $record['keywords']?></td>
                                                <td><?php echo $record['position']?></td>
                                                <td><?php echo $record['traffic']?></td>
                                                <td><?php echo $record['cpc']?></td>
                                                <td><?php echo $record['search_volume']?></td>
                                                <td><?php echo $record['url']?></td>
                                            </tr>
                                            <?php }
                                                    }
                                                ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if(!in_array('organic_backlink_summary', $toggle_module) || !in_array('organic_backlink_table', $toggle_module)) {?>
                            <div class="report-box">
                                <h5>Backlink Profile</h5>
                                <?php
                                    if(!in_array('organic_backlink_summary', $toggle_module)) {
                                        if(!empty($checkEditNotes2)) {
                                            echo $checkEditNotes2['edit_section'];
                                        }
                                    }
                                ?>
                                <?php if(!in_array('organic_backlink_table', $toggle_module)) {?>
                                    <table id="semrush_backlink_table"
                                        class="table table-bordered table-striped table-hover table-condensed dataTable no-footer"
                                        width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Source Page Title and URL | Target URL</th>
                                                <th>Anchor Text</th>
                                                <th>External Links </th>
                                                <th>Internal Links</th>
                                                <th>Type</th>
                                                <th>First Seen</th>
                                                <th>Last Seen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                    if(!empty($backlink_data)) {
                                                        foreach($backlink_data as $record) {
                                                        ?>
                                            <tr>
                                                <td>
                                                    <div><?php echo $record['source_title']?></div>
                                                    <div><strong>Source:</strong> <?php echo $record['source_url'] ?></div>
                                                    <div><strong>Target:</strong> <?php echo $record['target_url'] ?></div>
                                                </td>
                                                <td><?php echo $record['anchor']?></td>
                                                <td><?php echo $record['external_num']?></td>
                                                <td><?php echo $record['internal_num']?></td>
                                                <td>
                                                    <?php if($record['form'] == 'true' && $record['frame'] == 'true' && $record['image'] == 'true') { ?>
                                                    <div> IMAGE | FRAME | FORM </div>
                                                    <?php } else if( $record['form'] == 'true' ) { ?>
                                                    <div> FORM </div>
                                                    <?php } else if( $record['frame'] == 'true' ) { ?>
                                                    <div> FRAME </div>
                                                    <?php } else if( $record['form'] == 'image' ) { ?>
                                                    <div> IMAGE </div>
                                                    <?php } else { ?>
                                                    <div> Text</div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div
                                                        class="<?php if($record['newlink'] == 'true') { echo "lostlink-success"; } ?>">
                                                        <?php echo date('d M Y', $record['first_seen'])?></div>
                                                </td>
                                                <td>
                                                    <div
                                                        class="<?php if($record['lostlink'] == 'true') { echo "lostlink-success"; }?>">
                                                        <?php echo date('d M Y', $record['last_seen'])?></div>
                                                </td>
                                            </tr>
                                            <?php }
                                                    }
                                                ?>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if(!in_array('google_goal_summary', $toggle_module) || !in_array('google_goal_table', $toggle_module)) {?>
                            <div class="report-box">
                                <h5>Google Analytics Goal Completion</h5>
                                <?php
                                     if(!in_array('google_goal_summary', $toggle_module)) {
                                        if(!empty($checkEditNotes3)) {
                                            echo $checkEditNotes3['edit_section'];
                                        }
                                    }
                                ?>
                                <?php if(!in_array('google_goal_table', $toggle_module)) {
                                        if(!empty($profile_data)) {
                                ?>
                                        <table id="google_profile_table"
                                            class="table table-bordered table-striped table-hover table-condensed dataTable no-footer"
                                            width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Keywords</th>
                                                    <th>Sessions</th>
                                                    <th>New Users</th>
                                                    <th>Bounce Rate</th>
                                                    <th>Page / Session</th>
                                                    <th>Avg.Session Duration</th>
                                                    <th>Goal Conversion</th>
                                                    <th>Goal Completions</th>
                                                    <th>Goal Value</th>
                                                </tr>
                                            </thead>
                                        <?php foreach($profile_data as $profile) { ?>

                                            <tbody>
                                                <tr>
                                                    <td><?php echo $profile['keywords']; ?></td>
                                                    <td><?php echo $profile['sessions']; ?></td>
                                                    <td><?php echo $profile['new_users']; ?></td>
                                                    <td><?php echo $profile['bounse_rate']; ?></td>
                                                    <td><?php echo $profile['page_sessions']; ?></td>
                                                    <td><?php echo $profile['avg_session']; ?></td>
                                                    <td><?php echo $profile['goal_conversions']; ?></td>
                                                    <td><?php echo $profile['goal_completions']; ?></td>
                                                    <td><?php echo $profile['goal_value']; ?></td>
                                                </tr>
                                            </tbody>
                                        <?php } ?>

                                        </table>
                                    <?php
                                        } else {
                                    ?>
                                        <div id="chart-4-container" class="chart"></div>
                                    <?php }
                                    }
                                    ?>
                            </div>

                        <?php }
                        if(!in_array('summary_note', $toggle_module)) {
                            if(!empty($checkEditNotes4)) {
                        ?>
                            <div class="report-box">
                                <h5>Notes</h5>
                                <?php echo $checkEditNotes4['edit_section']; ?>
                            </div>
                        <?php }
                        }
                        ?>

                    </div>
                </div>



            </div>
        </div>

    </div> <!-- #end main-container -->


    <!-- theme settings -->
    <div class="site-settings clearfix hidden-xs">
        <div class="settings clearfix">
            <div class="trigger ion ion-settings left"></div>
            <div class="wrapper left">
                <ul class="list-unstyled other-settings">
                    <li class="clearfix mb10">
                        <div class="left small">Nav Horizontal</div>
                        <div class="md-switch right">
                            <label>
                                <input type="checkbox" id="navHorizontal">
                                <span>&nbsp;</span>
                            </label>
                        </div>


                    </li>
                    <li class="clearfix mb10">
                        <div class="left small">Fixed Header</div>
                        <div class="md-switch right">
                            <label>
                                <input type="checkbox" id="fixedHeader">
                                <span>&nbsp;</span>
                            </label>
                        </div>
                    </li>
                    <li class="clearfix mb10">
                        <div class="left small">Nav Full</div>
                        <div class="md-switch right">
                            <label>
                                <input type="checkbox" id="navFull">
                                <span>&nbsp;</span>
                            </label>
                        </div>
                    </li>
                </ul>
                <hr />
                <ul class="themes list-unstyled" id="themeColor">
                    <li data-theme="theme-zero" class="active"></li>
                    <li data-theme="theme-one"></li>
                    <li data-theme="theme-two"></li>
                    <li data-theme="theme-three"></li>
                    <li data-theme="theme-four"></li>
                    <li data-theme="theme-five"></li>
                    <li data-theme="theme-six"></li>
                    <li data-theme="theme-seven"></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- #end theme settings -->

    <!-- Dev only -->
    <!-- Vendors -->
    <link rel="stylesheet" href="assets/styles/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="assets/styles/introjs.css" />
    <style type="text/css">
    .bs-example {
        margin: 20px;
    }

    #example tr td div {
        text-align: left;
        padding: 0 5px;
    }

    span.s-label__text {
        background: #f00;
        padding: 2px 5px 1px;
        border-radius: 3px;
        color: #fff;
        text-transform: uppercase;
        line-height: 12px;
        font-size: 12px;
    }

    .-success span.s-label__text {
        background: #069856;
    }

    .highcharts-series-5 .highcharts-point {
        stroke: #2675b9;
    }

    .dcs-a-dcs-gb {
        z-index: 9999;
    }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/vendors.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/d3.min.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/c3.min.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/screenfull.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/perfect-scrollbar.min.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/waves.min.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/jquery.dataTables.min.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/app.js"></script>
    <script src="//code.highcharts.com/highcharts.js"></script>
    <script src="<?php echo FULL_PATH; ?>assets/scripts/intro.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#semrush_organic_table').DataTable({
            "order": [
                [1, "asc"]
            ],
            "pageLength": 25,
            "searching": true

        });

        $('#semrush_backlink_table').DataTable({
            "pageLength": 25
        });
        $('#google_profile_table').DataTable({
            "pageLength": 25,
            "bPaginate": false,
            "showNEntries": false,
            "bInfo": false,
            "paging": false
        });

    });
    $(function() {
        Highcharts.chart('c3chartline', {
            chart: {
                type: 'column'
            },
            xAxis: {
                dateTimeLabelFormats: {
                    "month": "%m/%e"
                },
                type: "datetime",
                labels: {
                    formatter: function() {
                        return Highcharts.dateFormat('%b', this.value);
                    }
                },
                //tickInterval: 30 * 24 * 3600 * 1000 // mills in a year.
                tickPositions: [<?php echo ($organic_date); ?>],
                <?php if(!empty($organic_index)) { ?>

                plotLines: [{
                    color: '#FF0000',
                    width: 2,
                    value: Date.UTC(<?php echo $organic_index; ?>),
                    zIndex: 99999,
                    label: {
                        text: 'Project Start Here',
                        align: 'left',
                        y: 0,
                        verticalAlign: 'top',
                        rotation: -360
                    }

                }],
 
            <?php  } ?>

            },
            title: {
                text: null
            },

            plotOptions: {
                spline: {
                    marker: {
                        enabled: true
                    }
                },

            },
            // plotOptions: {
            // 	line: {
            // 		dataLabels: {
            // 			enabled: false
            // 		}
            // 	},
            // 	series: {
            // 		stacking: "normal"
            // 	}
            // },

            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
            },
            exporting: {
                buttons: {
                    contextButton: {
                        enabled: false
                    }
                }
            },
            credits: {
                enabled: false
            },

            series: [{
                name: 'Organic Keywords',
                data: <?php echo json_encode($organic_keywordss); ?>
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
        <?php if(!empty($count_session)) { ?>
            var chart   =   $('#chart-1-container').highcharts({
                chart: {
                    type: 'area',
                    height: '410',
                    events: {
                        load: function() {
                            var myChart = this;
                            var compare_status  =   $('#compare_graph').val();
                            var compare_value   =   '';
                            if(compare_status == '1'){
                                myChart.series[1].show();
                                compare_value   =   1;
                                myChart.series[1].update({
                                    showInLegend: true
                                }, true, false);
                            }else{
                                myChart.series[1].hide();
                                compare_value   =   0;
                                myChart.series[1].update({
                                    showInLegend: false
                                }, true, false);
                            }
                        }
                    }
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: <?php echo json_encode($from_dates) ?>,
                    tickmarkPlacement: 'on',
                    title: {
                        enabled: false
                    },
                    <?php if(!empty($plot_index)) { ?>
                        plotLines: [{
                            color: '#FF0000',
                            width: 2,
                            value: <?php echo $plot_index?>,
                            zIndex: 99999,
                            label: {
                                text: 'Project Start Here',
                                align: 'left',
                                y: 0,
                                verticalAlign: 'top',
                                rotation: -360
                            }

                        }],
                <?php  } ?>

                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    split: true,
                    valueSuffix: ' '
                },
                series: [
                    <?php if(!empty($count_session)) { ?>

                    {
                        name: '<?php echo 'Current Period: '. $current_period; ?>',
                        data: [<?php echo join($count_session, ','); ?>]
                    },
                <?php } if(!empty($combine_session)) { ?>
                    {
                        name: '<?php echo 'Previous Period: '. $previous_period; ?>',
                        data: [<?php echo join($combine_session, ','); ?>],
                    }
                <?php } ?>
                ]
            });
        <?php } ?>
    });
    </script>

    <script>
    $(document).ready(function() {
        $('[data-hover="tooltip"]').tooltip()
        $('#chart1').click(function() {
            $("#chart1").children(".chart").toggle();
        });

        $("#semrush_details").on("hidden.bs.modal", function() {
            // put your default event here });
            window.location.reload();
        });
    });

    </script>
    <?php if(!empty($googleAnalytics['access_token'])) { ?>
    <script>
    (function(w, d, s, g, js, fs) {
        g = w.gapi || (w.gapi = {});
        g.analytics = {
            q: [],
            ready: function(f) {
                this.q.push(f);
            }
        };
        js = d.createElement(s);
        fs = d.getElementsByTagName(s)[0];
        js.src = 'https://apis.google.com/js/platform.js';
        fs.parentNode.insertBefore(js, fs);
        js.onload = function() {
            g.load('analytics');
        };
    }(window, document, 'script'));

    gapi.analytics.ready(function() {
        var session_new, session_old, users_new, users_old;
        var request = gapi.analytics.auth.authorize({
            'serverAuth': {
                'access_token': '<?php echo ($googleAnalytics['access_token']); ?>'
            }
        });

    var viewSelector = new gapi.analytics.ViewSelector({
        container: 'view-selector'
    });

    // Step 6: Hook up the components to work together.

    viewSelector.set({
        webPropertyId: '<?php echo $googleAnalytics['property_id'];?>',
    });


    var result = viewSelector.get();

        var dataChart4 = new gapi.analytics.googleCharts.DataChart({
            query: {
                'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
                'start-date': '30daysAgo',
                'end-date': 'yesterday',
                'metrics': 'ga:sessions,ga:newUsers, ga:bounceRate, ga:pageviewsPerSession, ga:avgSessionDuration, ga:goalConversionRateAll, ga:goalCompletionsAll, ga:goalValueAll',
                'dimensions': 'ga:keyword',
                'max-results': '10'
            },
            chart: {
                'container': 'chart-4-container',
                'type': 'TABLE',
                'options': {
                    'width': '100%',
                    title: 'Sessions over the past week.',
                    fontSize: 12
                }
            }
        });

        dataChart4.execute();

    });

    </script>
    <?php } ?>
    <style>
    /*.table-responsive .panel-body{
	min-width: 1500px;
	}*/

    #c3chartline,
    div#chart-1-container {
        clear: both;
    }
    </style>
    <script>
    function highChartMap(result) {
        var from_dates = (result['from_dates']);
        var count_session = (result['count_session']);
        var res = [];
        //var b 				= count_session.split(',').map(Number);
        console.log(from_dates);
        console.log(count_session);
        //console.log(b);
        for (var i = 0; i < count_session.length; i++) {
            res[i] = (parseInt(count_session[i], 10));
        }
        console.log(res);
        new Highcharts.chart('chart-1-container', {
            chart: {
                type: 'area',
                height: '410'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: from_dates,
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: ''
                }
            },
            tooltip: {
                split: true,
                valueSuffix: ' '
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '#666666',
                    lineWidth: 1,
                    marker: {
                        lineWidth: 1,
                        lineColor: '#666666'
                    }
                }
            },
            series: [{
                name: 'Session',
                data: res
            }]
        });

    }

    function startTour() {
        var tour = introJs()
        tour.setOption('tooltipPosition', 'auto');
        tour.setOption('positionPrecedence', ['left', 'right', 'bottom', 'top'])
        tour.start()
    }
    </script>
    <input type="hidden" class="session_new" />
    <input type="hidden" class="users_new" />
    <input type="hidden" class="pageview_new" />
    <input type="hidden" class="session_old" />
    <input type="hidden" class="users_old" />
    <input type="hidden" class="pageview_old" />
    <input type="hidden" class="goal_completions" />
    <!-- Location Modal End-->

    <script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?php
	    include("includes/footer.php");
    ?>
