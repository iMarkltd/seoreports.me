<?php
	ini_set('memory_limit', '-1');
	require_once("includes/config.php");
	require_once("includes/functions.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once('vendor/autoload.php');
	error_reporting(1);
	ini_set('display_errors', 1);

	global $DBcon;
	checkUserLoggedIn();

	$ids 			= 	$_REQUEST['id'];
	$domain_details	=	getUserDomainDetails($ids);
	$data			=	'';
	$graph_data		=	array();
	if(!empty($domain_details)){
        $client 		        	=   new Google_Client();
		$profile_info		    	=	getProfileData($_REQUEST['id'], $domain_details['user_id']);
		$data						=	checkSemarshApiData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db']);
        $backlink_data		        =	checkSemarshBacklinkData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id']);
		$checkGoogle    			=	checkGoogleAuthorizeToken($domain_details['user_id']);
		$traffic_cost 		        =	getTrafficCost($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id']);
        $checkEditNotes0	        =	checkEditNotes0($domain_details['user_id'], $_REQUEST['id']);
        $getActivityDetails         =   getActivityDetails($_REQUEST['id']);
        $googleAnalytics	        =	getDomainDetails($_REQUEST['id']);
		$checkEditNotes1	        =	checkEditNotes1($domain_details['user_id'], $_REQUEST['id'], '1');
		$checkEditNotes2	        =	checkEditNotes1($domain_details['user_id'], $_REQUEST['id'], '2');
		$checkEditNotes3	        =	checkEditNotes1($domain_details['user_id'], $_REQUEST['id'], '3');
		$checkEditNotes4	        =	checkEditNotes1($domain_details['user_id'], $_REQUEST['id'], '4');
		$checkGoogleGoal	        =	checkGoogleGoal($domain_details['user_id'], $domain_details['id']);
		$checkCurrentGoogleGoal	    =	checkCurrentGoogleGoal($domain_details['user_id'], $domain_details['id']);
		$getMozData				    =	checkMozData($domain_details['domain_url'], $_REQUEST['id']);
		$range1						=	getPositionData($ids, 1, 3);
		$range4						=	getPositionData($ids, 4, 10);
		$range11					=	getPositionData($ids, 11, 20);
		$range21					=	getPositionData($ids, 21, 50);
		$range50					=	getPositionData($ids, 51, 500);
		$total_keywords 			=	getTotalOrganicKeywords($ids);
		$top_3						=	getTopKeywords($ids, 1,	3);
		$top_10						=	getTopKeywords($ids, 1, 10);
        $top_100					=	getTopKeywords($ids, 1, 100);
        $start_data                 =   date('Y-m-d', strtotime('-1 year'));
        $end_data                   =   date('Y-m-d');
        $searchDateRange            =   getModuleDateRange($_REQUEST['id'], 'search_console');
        $domainHistoryRange         =   getModuleDateRange($_REQUEST['id'], 'organic_graph');
        $sessionHistoryRange        =   getModuleDateRange($_REQUEST['id'], 'organic_traffice');
        $getCompareChart            =   getCompareChart($_REQUEST['id']);
        $getSerpValue               =   getSerpValue($_REQUEST['id']);
        $plot_month                 =   '';
        if(!empty($domain_details['domain_register'])){
            $plot_month     =   date('Y,m', strtotime($domain_details['domain_register']));
        }
        $domain_history		        =	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db']);

        if(!empty($domainHistoryRange)){
            $domain_history		        =	dateRangeDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db'], $domainHistoryRange['start_date'],$domainHistoryRange['end_date']);
        }
        if(!empty($sessionHistoryRange)){
			if(!empty($googleAnalytics['id']) && $googleAnalytics['id'] != 0) {
                $analytics 		            = 	initializeAnalytics($domain_details['google_account_id']);
                $start_date 	            =	date('Y-m-d', strtotime($sessionHistoryRange['start_date']));
                $end_data                   =   date('Y-m-d', strtotime($sessionHistoryRange['end_date']));
                $day_diff            		= 	strtotime($sessionHistoryRange['start_date']) - strtotime($sessionHistoryRange['end_date']);
                $count_days              	=	floor($day_diff/(60*60*24));
                $start_data                 =   date('Y-m-d', strtotime($sessionHistoryRange['start_date'].' '.$count_days.' days'));
                $session_count		        =	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $sessionHistoryRange['start_date'],$sessionHistoryRange['end_date']);
                $combine_session_count	    =	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $start_data, $sessionHistoryRange['start_date']);
                foreach($session_count as $key=>$session_details) {
                    $from_dates[] 		=	date('M d', strtotime($session_details['from_date']));
                    $plot_dates[] 		=	date('Y,m', strtotime($session_details['from_date']));
                    //$unix_date			=	$session_details['unix_date']*1000;
                    $count_session[]	=	intval($session_details['total_session']);
                }
                foreach($combine_session_count as $combine){
                    //$unix_date			=	$combine['unix_date']*1000;
                    $combine_session[]	=	intval($combine['total_session']);
                }

                if(!empty($plot_dates) && !empty($plot_month)){
                    $index = array_search($plot_month, $plot_dates);

                    if ($index != false)
                        $plot_index = closestDates($session_count, $domain_details['domain_register']);
                }


                // echo "<pre>";
                // print_r($plot_dates);
                // print_r($domain_details['domain_register']);echo $plot_month;die;


                $current_period 	= 	date('d-m-Y', strtotime($start_date)).' to '.date('d-m-Y', strtotime($end_data));
                $previous_period	= 	date('d-m-Y', strtotime($start_data)).' to '.date('d-m-Y', strtotime($start_date));
                if(!empty($googleAnalytics['property_id'])) {
                    $getCurrentStats    = 	getMetricsData($analytics, 'ga:'.$googleAnalytics['property_id'], $sessionHistoryRange['start_date'], $sessionHistoryRange['end_date']);
                    $getPreviousStats   = 	getMetricsData($analytics, 'ga:'.$googleAnalytics['property_id'], $start_data, $sessionHistoryRange['start_date']);
                }

            } else {
                $count_session      =   '';
                $user_session       =   '';
                $from_dates         =   '';
                $combine_session    =   '';
                $current_period 	= 	'';
                $previous_period	= 	'';
                $plot_index         =   '';
            }
        }
        if(!empty($searchDateRange)){
            //print_r($searchDateRange); exit;
            $start_data                 =   date('Y-m-d', strtotime($searchDateRange['start_date']));
            $end_data                   =   date('Y-m-d', strtotime($searchDateRange['end_date']));
        }

        $searchConsole				=	googleSearchConsole($_REQUEST['id'], $domain_details['domain_url'], $domain_details['google_account_id'], $start_data, $end_data);
        $toggle_module				=	getToggleModule($_REQUEST['id']);
//		print_r($searchConsole); exit;
//		print_r($getMozData); exit;
		if($checkGoogle){
			if(!empty($googleAnalytics['id']) && $googleAnalytics['id'] != 0) {
				$googleViewSelector	=	getAnalytcsDomainName($_REQUEST['id'], $googleAnalytics['id']);
			}else{
				$googleViewSelector	=	'';
			}
		}
    }

	$organic_cost = $organic_keywordss = $organic_traffic = $adwords_cost = $adwords_keywords = $adwords_traffic = $rank	=	array();
	$organic_history = '';
	if(!empty($domain_history)) {
        if(!empty($domain_details['domain_register'])){
            $plot_month     =   date('Y,m', strtotime($domain_details['domain_register']));
        }
        $organic_date       =   '';
		foreach($domain_history as $history) {
			$dateTime 						= 	$history['unix_date'] * 1000;
			$organic_keywordss[]	        =	array($dateTime, intval($history['organic_keywords']));
			$organic_date					.=	$dateTime.", ";
            $organic_plot[]                 =   date('Y,m', strtotime($history['date_time']));
		}
        $organic_history			=		$domain_history[(count($domain_history)-1)]['organic_keywords'];
        if(!empty($organic_plot) && !empty($plot_month)){
            $index = array_search($plot_month, $organic_plot);
            if ($index != false){
                if(date("m",strtotime($domain_details['domain_register'])) == 01) {
                    $organic_index = date('Y,m,d', strtotime($domain_details['domain_register']));
                }else {
                    $organic_index = date('Y,m,d', strtotime($domain_details['domain_register'].'-1 months'));
                }
            }
        }

    }


	if(empty($searchConsole['message'])) {
		$clicks		=	array();
		$impression	=	array();
        $click_plot	=	array();
        if(!empty($domain_details['domain_register'])){
            $plot_month     =   date('Y,m', strtotime($domain_details['domain_register']));
        }

        foreach($searchConsole['data'] as $analytics)	{
            $click_plot[]   =   date('Y,m', strtotime($analytics->keys[0]));
			$clicks[]		=	array(strtotime($analytics->keys[0])*1000, $analytics->clicks);
			$impression[]	=	array(strtotime($analytics->keys[0])*1000, $analytics->impressions);
        }

        if(!empty($click_plot) && !empty($plot_month)){

            if (in_array($plot_month, $click_plot)) {
                if(date("m",strtotime($domain_details['domain_register'])) == 01) {
                    $click_plot_line = date('Y,0', strtotime($domain_details['domain_register']));
                }else {
                    $click_plot_line = date('Y,m', strtotime($domain_details['domain_register'].'-1 months'));
                }

            }
        }


	}


    require_once("includes/new-header.php");
?>


<!-- main-container -->
<div class="main-container clearfix">
    <!-- main-navigation -->
    <aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>

        <!-- Site nav (vertical) -->
        <?php require_once("includes/new-nav-sidebar.php"); ?>

    </aside>
    <!-- #end main-navigation -->

    <!-- content-here -->
    <div class="content-container container" id="content">

        <div class="page m134 page-charts-c3 page-dashboard" ng-controller="c3ChartDemoCtrl">

            <div class="page-wrap">

                <div class="three-box four-box">
                    <div class="box purple">
                        <figure>
                            <img src="/assets/images/OrganicKeywordsArrow.png" alt="">
                        </figure>

                        <?php
                            if(!empty($domain_history) && !empty($organic_history) ) {
                                if(count($domain_history)> 2) {
                                    $count_history		=   $domain_history[(count($domain_history)-2)]['organic_keywords'];
                                    if ($count_history) {
                                        $organic_keywords	=   round(($organic_history-$count_history)/$count_history*100, 2);
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
                        <h5><?php echo $organic_history; ?> <span id="traffic_growth" class="<?php echo ($organic_keywords < 0 ? 'red' : 'green' ); ?>">
                                <?php if($organic_keywords != 'N/A') {
                                         echo ($organic_keywords> 0 ? '+' : '' );  echo $organic_keywords."%";
                                 ?>
                                    <i class="fa fa-arrow-circle-<?php echo ($organic_keywords < 0 ? 'down' : 'up' ); ?>" aria-hidden="true"></i>
                                <?php } else{ echo $organic_keywords; }  ?>
                                </span>
                            <small>Organic keywords Ranking In Google</small></h5>

                        <cite>Keywords</cite>
                    </div>

                    <div class="box green">
                        <figure>
                            <img src="/assets/images/report-google-analytics-icon.png" alt="">
                        </figure>

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

                        <h5 class="traffic_growth ajax_session" >
                            <?php echo @$getCurrentStats['sessions']; ?>
                            <span id="traffic_growth" class="<?php echo ($traffic_growth < 0 ? 'red' : 'green' ); ?>">
                            <?php if($traffic_growth != 'N/A') {
                                        echo $traffic_growth;
                            ?>
                                        <i class="fa fa-arrow-circle-<?php echo ($traffic_growth < 0 ? 'down' : 'up' ); ?>" aria-hidden="true"></i>
                            <?php
                                    } else { echo $traffic_growth; }
                            ?>
                            </span>
                            <small>Organic Visitors From Google </small>
                        </h5>
                        <cite>Traffic</cite>
                    </div>

                    <div class="box pink">
                        <figure>
                            <img src="/assets/images/report-ahrefs-logo.png" alt="">
                        </figure>
                        <h5><?php
                                if(!empty($backlink_data) && count($backlink_data) > 0 ) echo count($backlink_data);
                                else  echo 'N/A';
                            ?>
                            <small>Backlink Profile</small>
                        </h5>

                        <cite>Links</cite>
                    </div>


                    <div class="box yellow">
                        <figure>
                            <img src="/assets/images/ReportGoogleAnaLyticsGoals.png" alt="">
                        </figure>
                        <h5 class="google_analytics"><?php echo $checkCurrentGoogleGoal['total']; ?>
                        <?php
                            if(!empty($checkGoogleGoal['goal_count']) && !empty($checkCurrentGoogleGoal['total']) ) {
                                $goal_result	=	(($checkCurrentGoogleGoal['total'] - $checkGoogleGoal['goal_count']) / $checkGoogleGoal['goal_count'] * 100)."%";
                            } else if(empty($checkCurrentGoogleGoal['total']) && !empty($checkGoogleGoal['goal_count']) ) {
                                $goal_result	= ' -100%';
                            } else if(!empty($checkCurrentGoogleGoal['total']) && empty($checkGoogleGoal['goal_count']) ) {
                                $goal_result	= ' 100%';
                            } else{
                                $goal_result	= 'N/A';
                            }
                        ?>

                            <span class="<?php echo ($goal_result < 0 ? 'red' : 'green' ); ?>">
                                <?php
                                    if($goal_result != 'N/A') {
                                        echo @number_format($goal_result, 2, '.', '');
                                ?>
                                    <i class="fa fa-arrow-circle-<?php echo (@$goal_result < 0 ? 'down' : 'up' ); ?>" aria-hidden="true"></i>
                                <?php } else { echo 'N/A'; }?>
                            </span>
                            <small>
                                Google AnaLytics <br>Goals
                            </small>
                        </h5>

                        <cite>Goals</cite>
                    </div>
                </div>

                <div class="row">

                    <!-- Line Chart -->
                    <div class="col-md-12">

                        <div class="white-box" style="display:none;">
                            <div class="row custom-table-pd">
                                <!-- Line Chart -->
                                <div class="col-md-6 pd-mb-20">
                                    <h5>Keyword Rank</h5>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Top Range</th>
                                                <th> Gain</th>
                                                <th> Loss</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td> Top 3 Positions </td>
                                                <td><?php echo $top_3['Gain'] ?> </td>
                                                <td><?php echo $top_3['Loss'] ?> </td>
                                            </tr>
                                            <tr>
                                                <td> Top 10 Positions </td>
                                                <td><?php echo $top_10['Gain'] ?> </td>
                                                <td><?php echo $top_10['Loss'] ?> </td>
                                            </tr>
                                            <tr>
                                                <td> Top 100 Positions </td>
                                                <td><?php echo $top_100['Gain'] ?> </td>
                                                <td><?php echo $top_100['Loss'] ?> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6 pd-mb-20">
                                    <h5>Keyword Rank</h5>
                                    <div class="keyRank-elem-cover">
                                        <div class="keyRank-elem-left">
                                            <div id="keyword_range" class="keyword_hide"></div>
                                            <!-- Total keywords =   -->
                                        </div>

                                        <table class="table keyRangeTable">
                                            <thead>
                                                <tr>
                                                    <th>Keyword Range</th>
                                                    <th> Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td> 1-3 </td>
                                                    <td><?php echo $range1['total'] ?> </td>
                                                </tr>
                                                <tr>
                                                    <td> 4-10 </td>
                                                    <td><?php echo $range4['total'] ?> </td>
                                                </tr>
                                                <tr>
                                                    <td> 11-20 </td>
                                                    <td><?php echo $range11['total'] ?> </td>
                                                </tr>
                                                <tr>
                                                    <td> 21-50 </td>
                                                    <td><?php echo $range21['total'] ?> </td>
                                                </tr>
                                                <tr>
                                                    <td> 51-100 </td>
                                                    <td><?php echo $range50['total'] ?> </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Line Chart End -->
                            </div>
                        </div>


                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Summary
                                    </h5>
                                </div>
                                <div class="right">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i
                                                class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">
                                                More Options
                                            </li>
                                            <li>
                                                <a href="#" data-hover="tooltip" id="monthly_report_note" title="" data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                    <?php if(!empty($checkEditNotes0))  echo 'Edit '; else echo 'Add '; ?> Summary
                                                </a>
                                            </li>
                                            <li>
                                                <?php if(!empty($checkEditNotes0)) { ?>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_first" title=""
                                                    data-row="summernote"
                                                    data-id="<?php echo $checkEditNotes0['id']; ?>"
                                                    data-text = "monthly_report_note"
                                                    class="delete_edit_notes" data-placement="left"
                                                    data-original-title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </a>
                                                <?php } ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>


                            <div class="edit-section-area">
                                <div id="monthly_report_edit_section" style="display:none">
                                    <div id="summernote" class="summernote"></div>
                                    <div class="text-right">
                                        <a class="btn btn-danger close-summernote" data-uid="monthly_report_note" data-id="monthly_report_edit_section" >Cancel</a>
                                        <a class="btn btn-success" id="saveAbout">Save</a>
                                    </div>
                                </div>
                                <div class="edit_note_first">
                                    <?php if(!empty($checkEditNotes0)) { ?>
                                        <?php echo $checkEditNotes0['edit_section']; ?>
                                    <?php }	?>
                                </div>
                            </div>
                        </div>

                        <?php if(empty($searchConsole['message'])) { ?>
                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Search Console
                                    </h5>
                                </div>
                                <div class="right">
                                    <div class="reportrange" data-module="search_console">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>

                            </div>

                            <div id="search_console"></div>

                            <div class="dynamic-tabs">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#home">QUERIES</a>
                                    </li>
                                    <li><a data-toggle="tab" href="#menu1">PAGES</a></li>
                                    <li><a data-toggle="tab" href="#menu2">COUNTRIES</a></li>
                                    <li><a data-toggle="tab" href="#menu3">DEVICES</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div id="home" class="tab-pane fade in active">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Query</th>
                                                    <th>Clicks</th>
                                                    <th>Impression</th>
                                                </tr>
                                            </thead>
                                            <tbody class="query_table">
                                                <?php if(empty($searchConsole['message'])) {
																foreach($searchConsole['query'] as $query)	{
															?>
                                                <tr>
                                                    <td><?php echo $query->keys[0]; ?></td>
                                                    <td><?php echo $query->clicks; ?></td>
                                                    <td><?php echo $query->impressions; ?></td>
                                                </tr>
                                                <?php	}
																}
															?>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div id="menu1" class="tab-pane fade">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Query</th>
                                                    <th>Clicks</th>
                                                    <th>Impression</th>
                                                </tr>
                                            </thead>
                                            <tbody class="page_table">
                                                <?php if(empty($searchConsole['message'])) {
																foreach($searchConsole['pages'] as $page)	{
															?>
                                                <tr>
                                                    <td><?php echo $page->keys[0]; ?></td>
                                                    <td><?php echo $page->clicks; ?></td>
                                                    <td><?php echo $page->impressions; ?></td>
                                                </tr>
                                                <?php	}
																}
															?>
                                            </tbody>
                                        </table>

                                    </div>
                                    <div id="menu2" class="tab-pane fade">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Query</th>
                                                    <th>Clicks</th>
                                                    <th>Impression</th>
                                                    <th>CTR</th>
                                                    <th>Position</th>
                                                </tr>
                                            </thead>
                                            <tbody class="country_table">
                                                <?php if(empty($searchConsole['message'])) {
																foreach($searchConsole['countries'] as $country)	{
															?>
                                                <tr>
                                                    <td><?php echo $country->keys[0]; ?></td>
                                                    <td><?php echo $country->clicks; ?></td>
                                                    <td><?php echo $country->impressions; ?></td>
                                                    <td><?php echo $country->ctr; ?></td>
                                                    <td><?php echo $country->position; ?></td>
                                                </tr>
                                                <?php	}
																}
															?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="menu3" class="tab-pane fade">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Query</th>
                                                    <th>Clicks</th>
                                                    <th>Impression</th>
                                                    <th>CTR</th>
                                                    <th>Position</th>
                                                </tr>
                                            </thead>
                                            <tbody class="device_table">
                                                <?php if(empty($searchConsole['message'])) {
																foreach($searchConsole['device'] as $device)	{
															?>
                                                <tr>
                                                    <td><?php echo $device->keys[0]; ?></td>
                                                    <td><?php echo $device->clicks; ?></td>
                                                    <td><?php echo $device->impressions; ?></td>
                                                    <td><?php echo $device->ctr; ?></td>
                                                    <td><?php echo $device->position; ?></td>
                                                </tr>
                                                <?php	}
																}
															?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php } else {?>
                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Search Console
                                    </h5>
                                </div>
                            </div>
                            <div class="search-console-error-msg"><p><?php echo $searchConsole['message']; ?></p></div>
                        </div>
                        <?php } ?>

                        <div class="white-box" style="">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Live Keyword Tracking
                                    </h5>
                                </div>
                                <div class="right" style="display: flex;">
                                    <button data-toggle="collapse" data-target="#liveTrackingSection" class="btn btn-default btn-small" style="display: inline-block;margin-right: 15px;">Add New Keyword(s)</button>
                                    <div class="dropdown">
                                        <a data-toggle="dropdown" ><i class="fa fa-circle"></i>
                                            <i class="fa fa-circle"></i>
                                            <i class="fa fa-circle"></i>
                                        </a>
                                        <ul id="manage_keywords" class="dropdown-menu">
                                            <li>
                                                <a id="multiplefavorite" data-toggle="confirmation" data-popout="true" data-singleton="true" data-placement="left" data-original-title="" title=""> <i class="fa fa-heart" ></i>Favorite Keywords
                                                </a>
                                            </li>
                                            <li>
                                                <a id="multipleunfavorite" data-toggle="confirmation" data-popout="true" data-singleton="true" data-placement="left" data-original-title="" title=""> <i class="fa fa-times" ></i>Unfavorite Keywords</a>
                                            </li>
                                            <li>
                                                <a id="multipleUpdate" data-toggle="confirmation" data-popout="true" data-singleton="true" data-placement="left" data-original-title="" title=""><i class="fa fa-refresh" ></i>Update Keywords</a>
                                            </li>
                                            <li>
                                                <a id="multipleDelete" data-toggle="confirmation" data-popout="true" data-singleton="true" data-placement="left" data-original-title="" title=""><i class="fa fa-trash" ></i>Delete Keywords</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="live-tracking-section collapse" id="liveTrackingSection" class="collapse">
                                <div class="settings-tab">
                                    <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#liveTab1">New Keywords</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div id="liveTab1" class="tab-pane fade in active">
                                        <form class="task_form d-flex" id="serp_track"  method="post" >
                                                <input type="hidden" name="action" value="serp_track" />
                                                <input type="hidden" name="request_id" value="<?php echo $_REQUEST['id']?>" />
                                                <input type="hidden" name="country_code" value="us" />

                                                <div class="row">
                                                    <div class="col-sm-10">

                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <label>Domain URL</label>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="input-group-addon hidden-xs hidden-sm" style="position: absolute;left: 11px;top: 1px;z-index: 9999;background: #ddd;padding: 14px;width: 79px;">http(s)://</div>
                                                                <input type="text" class="form-control"  value="<?php // echo $keyWordSearch; ?>" name="url" required style="padding-left:86px"  >
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="switch">
                                                                    <label>
                                                                    <input type="checkbox" class="check-toggle check-toggle-round-flat" name="exact_trail" id="exact_trail" >
                                                                        <span></span>Exact
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <label>Keywords Ranking</label>
                                                            </div>
                                                            <div class="col-sm-10">
                                                                <textarea name="cities" id="cities" class="form-control cities" placeholder="Enter one keyword per line"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">

                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <label>Search Engine Region</label>
                                                                    </div>
                                                                    <div class="col-sm-8">
                                                                        <select id="region" name="region" class="form-control selectpicker" data-size="8" data-live-search="true" data-dropup-auto="false" style="outline: 0;" required>
                                                                            <optgroup label="Common Regions">
                                                                                <option value="google.com" data-country="us" data-content="<img src='https://serpbook.com/serp/images/country/com.png'> google.com">google.com</option>
                                                                                <option value="google.ca" data-country="ca" data-content="<img src='https://serpbook.com/serp/images/country/ca.png'> google.ca">google.ca</option>
                                                                                <option value="google.co.uk" data-country="uk" data-content="<img src='https://serpbook.com/serp/images/country/uk.png'> google.co.uk">google.co.uk</option>
                                                                                <option value="google.com.au" data-country="au" data-content="<img src='https://serpbook.com/serp/images/country/au.png'> google.com.au">google.com.au</option>
                                                                                <option value="google.es" data-country="es" data-content="<img src='https://serpbook.com/serp/images/country/es.png'> google.es">google.es</option>
                                                                                <option value="google.co.nz" data-country="nz" data-content="<img src='https://serpbook.com/serp/images/country/nz.png'> google.co.nz">google.co.nz</option>
                                                                                <option value="google.de" data-country="de" data-content="<img src='https://serpbook.com/serp/images/country/de.png'> google.de">google.de</option>
                                                                                <option value="google.nl" data-country="nl" data-content="<img src='https://serpbook.com/serp/images/country/nl.png'> google.nl">google.nl</option>
                                                                            </optgroup>
                                                                            <optgroup label="Other Regions">
                                                                            <option value="google.ad" data-country="ad" data-content="<img src='https://serpbook.com/serp/images/country/ad.png'> google.ad">google.ad</option>
                                                                            <option value="google.ae" data-country="ae" data-content="<img src='https://serpbook.com/serp/images/country/ae.png'> google.ae">google.ae</option>
                                                                            <option value="google.al" data-country="al" data-content="<img src='https://serpbook.com/serp/images/country/al.png'> google.al">google.al</option>
                                                                            <option value="google.am" data-country="am" data-content="<img src='https://serpbook.com/serp/images/country/am.png'> google.am">google.am</option>
                                                                            <option value="google.as" data-country="as" data-content="<img src='https://serpbook.com/serp/images/country/as.png'> google.as">google.as</option>
                                                                            <option value="google.at" data-country="at" data-content="<img src='https://serpbook.com/serp/images/country/at.png'> google.at">google.at</option>
                                                                            <option value="google.az" data-country="az" data-content="<img src='https://serpbook.com/serp/images/country/az.png'> google.az">google.az</option>
                                                                            <option value="google.ba" data-country="ba" data-content="<img src='https://serpbook.com/serp/images/country/ba.png'> google.ba">google.ba</option>
                                                                            <option value="google.be" data-country="be" data-content="<img src='https://serpbook.com/serp/images/country/be.png'> google.be">google.be</option>
                                                                            <option value="google.bf" data-country="bf" data-content="<img src='https://serpbook.com/serp/images/country/bf.png'> google.bf">google.bf</option>
                                                                            <option value="google.bg" data-country="bg" data-content="<img src='https://serpbook.com/serp/images/country/bg.png'> google.bg">google.bg</option>
                                                                            <option value="google.bi" data-country="bi" data-content="<img src='https://serpbook.com/serp/images/country/bi.png'> google.bi">google.bi</option>
                                                                            <option value="google.bj" data-country="bj" data-content="<img src='https://serpbook.com/serp/images/country/bj.png'> google.bj">google.bj</option>
                                                                            <option value="google.bs" data-country="bs" data-content="<img src='https://serpbook.com/serp/images/country/bs.png'> google.bs">google.bs</option>
                                                                            <option value="google.by" data-country="by" data-content="<img src='https://serpbook.com/serp/images/country/by.png'> google.by">google.by</option>
                                                                            <option value="google.cat" data-country="es" data-content="<img src='https://serpbook.com/serp/images/country/cat.png'> google.cat">google.cat</option>
                                                                            <option value="google.cd" data-country="cd" data-content="<img src='https://serpbook.com/serp/images/country/cd.png'> google.cd">google.cd</option>
                                                                            <option value="google.cf" data-country="cf" data-content="<img src='https://serpbook.com/serp/images/country/cf.png'> google.cf">google.cf</option>
                                                                            <option value="google.cg" data-country="cg" data-content="<img src='https://serpbook.com/serp/images/country/cg.png'> google.cg">google.cg</option>
                                                                            <option value="google.ch" data-country="ch" data-content="<img src='https://serpbook.com/serp/images/country/ch.png'> google.ch">google.ch</option>
                                                                            <option value="google.ci" data-country="ci" data-content="<img src='https://serpbook.com/serp/images/country/ci.png'> google.ci">google.ci</option>
                                                                            <option value="google.cl" data-country="cl" data-content="<img src='https://serpbook.com/serp/images/country/cl.png'> google.cl">google.cl</option>
                                                                            <option value="google.cm" data-country="cm" data-content="<img src='https://serpbook.com/serp/images/country/cm.png'> google.cm">google.cm</option>
                                                                            <option value="google.co.ao" data-country="ao" data-content="<img src='https://serpbook.com/serp/images/country/ao.png'> google.co.ao">google.co.ao</option>
                                                                            <option value="google.co.bw" data-country="bw" data-content="<img src='https://serpbook.com/serp/images/country/bw.png'> google.co.bw">google.co.bw</option>
                                                                            <option value="google.co.ck" data-country="ck" data-content="<img src='https://serpbook.com/serp/images/country/ck.png'> google.co.ck">google.co.ck</option>
                                                                            <option value="google.co.cr" data-country="cr" data-content="<img src='https://serpbook.com/serp/images/country/cr.png'> google.co.cr">google.co.cr</option>
                                                                            <option value="google.co.id" data-country="id" data-content="<img src='https://serpbook.com/serp/images/country/id.png'> google.co.id">google.co.id</option>
                                                                            <option value="google.co.il" data-country="il" data-content="<img src='https://serpbook.com/serp/images/country/il.png'> google.co.il">google.co.il</option>
                                                                            <option value="google.co.in" data-country="in" data-content="<img src='https://serpbook.com/serp/images/country/in.png'> google.co.in">google.co.in</option>
                                                                            <option value="google.co.jp" data-country="jp" data-content="<img src='https://serpbook.com/serp/images/country/jp.png'> google.co.jp">google.co.jp</option>
                                                                            <option value="google.co.ke" data-country="ke" data-content="<img src='https://serpbook.com/serp/images/country/ke.png'> google.co.ke">google.co.ke</option>
                                                                            <option value="google.co.kr" data-country="kr" data-content="<img src='https://serpbook.com/serp/images/country/kr.png'> google.co.kr">google.co.kr</option>
                                                                            <option value="google.co.ls" data-country="ls" data-content="<img src='https://serpbook.com/serp/images/country/ls.png'> google.co.ls">google.co.ls</option>
                                                                            <option value="google.co.ma" data-country="ma" data-content="<img src='https://serpbook.com/serp/images/country/ma.png'> google.co.ma">google.co.ma</option>
                                                                            <option value="google.co.mz" data-country="mz" data-content="<img src='https://serpbook.com/serp/images/country/mz.png'> google.co.mz">google.co.mz</option>
                                                                            <option value="google.co.th" data-country="th" data-content="<img src='https://serpbook.com/serp/images/country/th.png'> google.co.th">google.co.th</option>
                                                                            <option value="google.co.tz" data-country="tz" data-content="<img src='https://serpbook.com/serp/images/country/tz.png'> google.co.tz">google.co.tz</option>
                                                                            <option value="google.co.ug" data-country="ug" data-content="<img src='https://serpbook.com/serp/images/country/ug.png'> google.co.ug">google.co.ug</option>
                                                                            <option value="google.co.uz" data-country="uz" data-content="<img src='https://serpbook.com/serp/images/country/uz.png'> google.co.uz">google.co.uz</option>
                                                                            <option value="google.co.ve" data-country="ve" data-content="<img src='https://serpbook.com/serp/images/country/ve.png'> google.co.ve">google.co.ve</option>
                                                                            <option value="google.co.vi" data-country="vi" data-content="<img src='https://serpbook.com/serp/images/country/vi.png'> google.co.vi">google.co.vi</option>
                                                                            <option value="google.co.za" data-country="za" data-content="<img src='https://serpbook.com/serp/images/country/za.png'> google.co.za">google.co.za</option>
                                                                            <option value="google.co.zm" data-country="zm" data-content="<img src='https://serpbook.com/serp/images/country/zm.png'> google.co.zm">google.co.zm</option>
                                                                            <option value="google.co.zw" data-country="zw" data-content="<img src='https://serpbook.com/serp/images/country/zw.png'> google.co.zw">google.co.zw</option>
                                                                            <option value="google.com.af" data-country="af" data-content="<img src='https://serpbook.com/serp/images/country/af.png'> google.com.af">google.com.af</option>
                                                                            <option value="google.com.ag" data-country="ag" data-content="<img src='https://serpbook.com/serp/images/country/ag.png'> google.com.ag">google.com.ag</option>
                                                                            <option value="google.com.ai" data-country="ai" data-content="<img src='https://serpbook.com/serp/images/country/ai.png'> google.com.ai">google.com.ai</option>
                                                                            <option value="google.com.ar" data-country="ar" data-content="<img src='https://serpbook.com/serp/images/country/ar.png'> google.com.ar">google.com.ar</option>
                                                                            <option value="google.com.bd" data-country="bd" data-content="<img src='https://serpbook.com/serp/images/country/bd.png'> google.com.bd">google.com.bd</option>
                                                                            <option value="google.com.bh" data-country="bh" data-content="<img src='https://serpbook.com/serp/images/country/bh.png'> google.com.bh">google.com.bh</option>
                                                                            <option value="google.com.bn" data-country="bn" data-content="<img src='https://serpbook.com/serp/images/country/bn.png'> google.com.bn">google.com.bn</option>
                                                                            <option value="google.com.bo" data-country="bo" data-content="<img src='https://serpbook.com/serp/images/country/bo.png'> google.com.bo">google.com.bo</option>
                                                                            <option value="google.com.br" data-country="br" data-content="<img src='https://serpbook.com/serp/images/country/br.png'> google.com.br">google.com.br</option>
                                                                            <option value="google.com.bz" data-country="bz" data-content="<img src='https://serpbook.com/serp/images/country/bz.png'> google.com.bz">google.com.bz</option>
                                                                            <option value="google.com.co" data-country="co" data-content="<img src='https://serpbook.com/serp/images/country/co.png'> google.com.co">google.com.co</option>
                                                                            <option value="google.com.cu" data-country="cu" data-content="<img src='https://serpbook.com/serp/images/country/cu.png'> google.com.cu">google.com.cu</option>
                                                                            <option value="google.com.cy" data-country="cy" data-content="<img src='https://serpbook.com/serp/images/country/cy.png'> google.com.cy">google.com.cy</option>
                                                                            <option value="google.com.do" data-country="do" data-content="<img src='https://serpbook.com/serp/images/country/do.png'> google.com.do">google.com.do</option>
                                                                            <option value="google.com.ec" data-country="ec" data-content="<img src='https://serpbook.com/serp/images/country/ec.png'> google.com.ec">google.com.ec</option>
                                                                            <option value="google.com.eg" data-country="eg" data-content="<img src='https://serpbook.com/serp/images/country/eg.png'> google.com.eg">google.com.eg</option>
                                                                            <option value="google.com.et" data-country="et" data-content="<img src='https://serpbook.com/serp/images/country/et.png'> google.com.et">google.com.et</option>
                                                                            <option value="google.com.fj" data-country="fj" data-content="<img src='https://serpbook.com/serp/images/country/fj.png'> google.com.fj">google.com.fj</option>
                                                                            <option value="google.com.gh" data-country="gh" data-content="<img src='https://serpbook.com/serp/images/country/gh.png'> google.com.gh">google.com.gh</option>
                                                                            <option value="google.com.gi" data-country="gi" data-content="<img src='https://serpbook.com/serp/images/country/gi.png'> google.com.gi">google.com.gi</option>
                                                                            <option value="google.com.gt" data-country="gt" data-content="<img src='https://serpbook.com/serp/images/country/gt.png'> google.com.gt">google.com.gt</option>
                                                                            <option value="google.com.hk" data-country="hk" data-content="<img src='https://serpbook.com/serp/images/country/hk.png'> google.com.hk">google.com.hk</option>
                                                                            <option value="google.com.jm" data-country="jm" data-content="<img src='https://serpbook.com/serp/images/country/jm.png'> google.com.jm">google.com.jm</option>
                                                                            <option value="google.com.kh" data-country="kh" data-content="<img src='https://serpbook.com/serp/images/country/kh.png'> google.com.kh">google.com.kh</option>
                                                                            <option value="google.com.kw" data-country="kw" data-content="<img src='https://serpbook.com/serp/images/country/kw.png'> google.com.kw">google.com.kw</option>
                                                                            <option value="google.com.lb" data-country="lb" data-content="<img src='https://serpbook.com/serp/images/country/lb.png'> google.com.lb">google.com.lb</option>
                                                                            <option value="google.com.ly" data-country="ly" data-content="<img src='https://serpbook.com/serp/images/country/ly.png'> google.com.ly">google.com.ly</option>
                                                                            <option value="google.com.mm" data-country="mc" data-content="<img src='https://serpbook.com/serp/images/country/mm.png'> google.com.mm">google.com.mm</option>
                                                                            <option value="google.com.mt" data-country="mt" data-content="<img src='https://serpbook.com/serp/images/country/mt.png'> google.com.mt">google.com.mt</option>
                                                                            <option value="google.com.mx" data-country="mx" data-content="<img src='https://serpbook.com/serp/images/country/mx.png'> google.com.mx">google.com.mx</option>
                                                                            <option value="google.com.my" data-country="my" data-content="<img src='https://serpbook.com/serp/images/country/my.png'> google.com.my">google.com.my</option>
                                                                            <option value="google.com.na" data-country="na" data-content="<img src='https://serpbook.com/serp/images/country/na.png'> google.com.na">google.com.na</option>
                                                                            <option value="google.com.nf" data-country="nf" data-content="<img src='https://serpbook.com/serp/images/country/nf.png'> google.com.nf">google.com.nf</option>
                                                                            <option value="google.com.ng" data-country="ng" data-content="<img src='https://serpbook.com/serp/images/country/ng.png'> google.com.ng">google.com.ng</option>
                                                                            <option value="google.com.ni" data-country="ni" data-content="<img src='https://serpbook.com/serp/images/country/ni.png'> google.com.ni">google.com.ni</option>
                                                                            <option value="google.com.np" data-country="np" data-content="<img src='https://serpbook.com/serp/images/country/np.png'> google.com.np">google.com.np</option>
                                                                            <option value="google.com.om" data-country="om" data-content="<img src='https://serpbook.com/serp/images/country/om.png'> google.com.om">google.com.om</option>
                                                                            <option value="google.com.pa" data-country="pa" data-content="<img src='https://serpbook.com/serp/images/country/pa.png'> google.com.pa">google.com.pa</option>
                                                                            <option value="google.com.pe" data-country="pe" data-content="<img src='https://serpbook.com/serp/images/country/pe.png'> google.com.pe">google.com.pe</option>
                                                                            <option value="google.com.ph" data-country="ph" data-content="<img src='https://serpbook.com/serp/images/country/ph.png'> google.com.ph">google.com.ph</option>
                                                                            <option value="google.com.pk" data-country="pk" data-content="<img src='https://serpbook.com/serp/images/country/pk.png'> google.com.pk">google.com.pk</option>
                                                                            <option value="google.com.pr" data-country="pr" data-content="<img src='https://serpbook.com/serp/images/country/pr.png'> google.com.pr">google.com.pr</option>
                                                                            <option value="google.com.py" data-country="py" data-content="<img src=https://serpbook.com//serp/images/country/py.png'> google.com.py">google.com.py</option>
                                                                            <option value="google.com.qa" data-country="qa" data-content="<img src='https://serpbook.com/serp/images/country/qa.png'> google.com.qa">google.com.qa</option>
                                                                            <option value="google.com.sa" data-country="sa" data-content="<img src='https://serpbook.com/serp/images/country/sa.png'> google.com.sa">google.com.sa</option>
                                                                            <option value="google.com.sb" data-country="sb" data-content="<img src='https://serpbook.com/serp/images/country/sb.png'> google.com.sb">google.com.sb</option>
                                                                            <option value="google.com.sg" data-country="sg" data-content="<img src='https://serpbook.com/serp/images/country/sg.png'> google.com.sg">google.com.sg</option>
                                                                            <option value="google.com.sl" data-country="sl" data-content="<img src='https://serpbook.com/serp/images/country/sl.png'> google.com.sl">google.com.sl</option>
                                                                            <option value="google.com.sv" data-country="sv" data-content="<img src=https://serpbook.com//serp/images/country/sv.png'> google.com.sv">google.com.sv</option>
                                                                            <option value="google.com.tj" data-country="tj" data-content="<img src='https://serpbook.com/serp/images/country/tj.png'> google.com.tj">google.com.tj</option>
                                                                            <option value="google.com.tr" data-country="tr" data-content="<img src='https://serpbook.com/serp/images/country/tr.png'> google.com.tr">google.com.tr</option>
                                                                            <option value="google.com.tw" data-country="tw" data-content="<img src='https://serpbook.com/serp/images/country/tw.png'> google.com.tw">google.com.tw</option>
                                                                            <option value="google.com.ua" data-country="ua" data-content="<img src='https://serpbook.com/serp/images/country/ua.png'> google.com.ua">google.com.ua</option>
                                                                            <option value="google.com.uy" data-country="uy" data-content="<img src='https://serpbook.com/serp/images/country/uy.png'> google.com.uy">google.com.uy</option>
                                                                            <option value="google.com.vc" data-country="vc" data-content="<img src=https://serpbook.com//serp/images/country/vc.png'> google.com.vc">google.com.vc</option>
                                                                            <option value="google.com.vn" data-country="vn" data-content="<img src='https://serpbook.com/serp/images/country/vn.png'> google.com.vn">google.com.vn</option>
                                                                            <option value="google.cv" data-country="cv" data-content="<img src='https://serpbook.com/serp/images/country/cv.png'> google.cv">google.cv</option>
                                                                            <option value="google.cz" data-country="cz" data-content="<img src='https://serpbook.com/serp/images/country/cz.png'> google.cz">google.cz</option>
                                                                            <option value="google.dj" data-country="dj" data-content="<img src=https://serpbook.com//serp/images/country/dj.png'> google.dj">google.dj</option>
                                                                            <option value="google.dk" data-country="dk" data-content="<img src='https://serpbook.com/serp/images/country/dk.png'> google.dk">google.dk</option>
                                                                            <option value="google.dm" data-country="dm" data-content="<img src='https://serpbook.com/serp/images/country/dm.png'> google.dm">google.dm</option>
                                                                            <option value="google.dz" data-country="dz" data-content="<img src='https://serpbook.com/serp/images/country/dz.png'> google.dz">google.dz</option>
                                                                            <option value="google.ee" data-country="ee" data-content="<img src=https://serpbook.com//serp/images/country/ee.png'> google.ee">google.ee</option>
                                                                            <option value="google.fi" data-country="fi" data-content="<img src='https://serpbook.com/serp/images/country/fi.png'> google.fi">google.fi</option>
                                                                            <option value="google.fm" data-country="fm" data-content="<img src='https://serpbook.com/serp/images/country/fm.png'> google.fm">google.fm</option>
                                                                            <option value="google.fr" data-country="fr" data-content="<img src='https://serpbook.com/serp/images/country/fr.png'> google.fr">google.fr</option>
                                                                            <option value="google.ga" data-country="ga" data-content="<img src=https://serpbook.com//serp/images/country/ga.png'> google.ga">google.ga</option>
                                                                            <option value="google.ge" data-country="ge" data-content="<img src='https://serpbook.com/serp/images/country/ge.png'> google.ge">google.ge</option>
                                                                            <option value="google.gg" data-country="gg" data-content="<img src='https://serpbook.com/serp/images/country/gg.png'> google.gg">google.gg</option>
                                                                            <option value="google.gl" data-country="gl" data-content="<img src='https://serpbook.com/serp/images/country/gl.png'> google.gl">google.gl</option>
                                                                            <option value="google.gm" data-country="gm" data-content="<img src=https://serpbook.com//serp/images/country/gm.png'> google.gm">google.gm</option>
                                                                            <option value="google.gp" data-country="gp" data-content="<img src='https://serpbook.com/serp/images/country/gp.png'> google.gp">google.gp</option>
                                                                            <option value="google.gr" data-country="gr" data-content="<img src='https://serpbook.com/serp/images/country/gr.png'> google.gr">google.gr</option>
                                                                            <option value="google.gy" data-country="gy" data-content="<img src='https://serpbook.com/serp/images/country/gy.png'> google.gy">google.gy</option>
                                                                            <option value="google.hn" data-country="hn" data-content="<img src='https://serpbook.com/serp/images/country/hn.png'> google.hn">google.hn</option>
                                                                            <option value="google.hr" data-country="hr" data-content="<img src='https://serpbook.com/serp/images/country/hr.png'> google.hr">google.hr</option>
                                                                            <option value="google.ht" data-country="ht" data-content="<img src='https://serpbook.com/serp/images/country/ht.png'> google.ht">google.ht</option>
                                                                            <option value="google.hu" data-country="hu" data-content="<img src='https://serpbook.com/serp/images/country/hu.png'> google.hu">google.hu</option>
                                                                            <option value="google.ie" data-country="ie" data-content="<img src='https://serpbook.com/serp/images/country/ie.png'> google.ie">google.ie</option>
                                                                            <option value="google.im" data-country="im" data-content="<img src='https://serpbook.com/serp/images/country/im.png'> google.im">google.im</option>
                                                                            <option value="google.iq" data-country="iq" data-content="<img src='https://serpbook.com/serp/images/country/iq.png'> google.iq">google.iq</option>
                                                                            <option value="google.is" data-country="is" data-content="<img src='https://serpbook.com/serp/images/country/is.png'> google.is">google.is</option>
                                                                            <option value="google.it" data-country="it" data-content="<img src='https://serpbook.com/serp/images/country/it.png'> google.it">google.it</option>
                                                                            <option value="google.je" data-country="je" data-content="<img src='https://serpbook.com/serp/images/country/je.png'> google.je">google.je</option>
                                                                            <option value="google.jo" data-country="jo" data-content="<img src='https://serpbook.com/serp/images/country/jo.png'> google.jo">google.jo</option>
                                                                            <option value="google.kg" data-country="kg" data-content="<img src='https://serpbook.com/serp/images/country/kg.png'> google.kg">google.kg</option>
                                                                            <option value="google.ki" data-country="ki" data-content="<img src='https://serpbook.com/serp/images/country/ki.png'> google.ki">google.ki</option>
                                                                            <option value="google.kz" data-country="kz" data-content="<img src='https://serpbook.com/serp/images/country/kz.png'> google.kz">google.kz</option>
                                                                            <option value="google.la" data-country="la" data-content="<img src='https://serpbook.com/serp/images/country/la.png'> google.la">google.la</option>
                                                                            <option value="google.li" data-country="li" data-content="<img src='https://serpbook.com/serp/images/country/li.png'> google.li">google.li</option>
                                                                            <option value="google.lk" data-country="lk" data-content="<img src='https://serpbook.com/serp/images/country/lk.png'> google.lk">google.lk</option>
                                                                            <option value="google.lt" data-country="lt" data-content="<img src='https://serpbook.com/serp/images/country/lt.png'> google.lt">google.lt</option>
                                                                            <option value="google.lu" data-country="lu" data-content="<img src='https://serpbook.com/serp/images/country/lu.png'> google.lu">google.lu</option>
                                                                            <option value="google.lv" data-country="lv" data-content="<img src='https://serpbook.com/serp/images/country/lv.png'> google.lv">google.lv</option>
                                                                            <option value="google.md" data-country="md" data-content="<img src='https://serpbook.com/serp/images/country/md.png'> google.md">google.md</option>
                                                                            <option value="google.me" data-country="me" data-content="<img src='https://serpbook.com/serp/images/country/me.png'> google.me">google.me</option>
                                                                            <option value="google.mg" data-country="mg" data-content="<img src='https://serpbook.com/serp/images/country/mg.png'> google.mg">google.mg</option>
                                                                            <option value="google.mk" data-country="mk" data-content="<img src='https://serpbook.com/serp/images/country/mk.png'> google.mk">google.mk</option>
                                                                            <option value="google.ml" data-country="ml" data-content="<img src='https://serpbook.com/serp/images/country/ml.png'> google.ml">google.ml</option>
                                                                            <option value="google.mn" data-country="mn" data-content="<img src='https://serpbook.com/serp/images/country/mn.png'> google.mn">google.mn</option>
                                                                            <option value="google.ms" data-country="ms" data-content="<img src='https://serpbook.com/serp/images/country/ms.png'> google.ms">google.ms</option>
                                                                            <option value="google.mu" data-country="mu" data-content="<img src='https://serpbook.com/serp/images/country/mu.png'> google.mu">google.mu</option>
                                                                            <option value="google.mv" data-country="mv" data-content="<img src='https://serpbook.com/serp/images/country/mv.png'> google.mv">google.mv</option>
                                                                            <option value="google.mw" data-country="mw" data-content="<img src='https://serpbook.com/serp/images/country/mw.png'> google.mw">google.mw</option>
                                                                            <option value="google.ne" data-country="ne" data-content="<img src='https://serpbook.com/serp/images/country/ne.png'> google.ne">google.ne</option>
                                                                            <option value="google.no" data-country="no" data-content="<img src='https://serpbook.com/serp/images/country/no.png'> google.no">google.no</option>
                                                                            <option value="google.nr" data-country="nr" data-content="<img src='https://serpbook.com/serp/images/country/nr.png'> google.nr">google.nr</option>
                                                                            <option value="google.nu" data-country="nu" data-content="<img src='https://serpbook.com/serp/images/country/nu.png'> google.nu">google.nu</option>
                                                                            <option value="google.pl" data-country="pl" data-content="<img src='https://serpbook.com/serp/images/country/pl.png'> google.pl">google.pl</option>
                                                                            <option value="google.pn" data-country="pn" data-content="<img src='https://serpbook.com/serp/images/country/pn.png'> google.pn">google.pn</option>
                                                                            <option value="google.ps" data-country="ps" data-content="<img src='https://serpbook.com/serp/images/country/ps.png'> google.ps">google.ps</option>
                                                                            <option value="google.pt" data-country="pt" data-content="<img src='https://serpbook.com/serp/images/country/pt.png'> google.pt">google.pt</option>
                                                                            <option value="google.ro" data-country="ro" data-content="<img src='https://serpbook.com/serp/images/country/ro.png'> google.ro">google.ro</option>
                                                                            <option value="google.rs" data-country="rs" data-content="<img src='https://serpbook.com/serp/images/country/rs.png'> google.rs">google.rs</option>
                                                                            <option value="google.ru" data-country="ru" data-content="<img src='https://serpbook.com/serp/images/country/ru.png'> google.ru">google.ru</option>
                                                                            <option value="google.rw" data-country="rw" data-content="<img src='https://serpbook.com/serp/images/country/rw.png'> google.rw">google.rw</option>
                                                                            <option value="google.sc" data-country="sc" data-content="<img src='https://serpbook.com/serp/images/country/sc.png'> google.sc">google.sc</option>
                                                                            <option value="google.se" data-country="se" data-content="<img src='https://serpbook.com/serp/images/country/se.png'> google.se">google.se</option>
                                                                            <option value="google.sh" data-country="sh" data-content="<img src='https://serpbook.com/serp/images/country/sh.png'> google.sh">google.sh</option>
                                                                            <option value="google.si" data-country="si" data-content="<img src='https://serpbook.com/serp/images/country/si.png'> google.si">google.si</option>
                                                                            <option value="google.sk" data-country="sk" data-content="<img src='https://serpbook.com/serp/images/country/sk.png'> google.sk">google.sk</option>
                                                                            <option value="google.sm" data-country="sm" data-content="<img src='https://serpbook.com/serp/images/country/sm.png'> google.sm">google.sm</option>
                                                                            <option value="google.sn" data-country="sn" data-content="<img src='https://serpbook.com/serp/images/country/sn.png'> google.sn">google.sn</option>
                                                                            <option value="google.so" data-country="so" data-content="<img src='https://serpbook.com/serp/images/country/so.png'> google.so">google.so</option>
                                                                            <option value="google.st" data-country="st" data-content="<img src='https://serpbook.com/serp/images/country/st.png'> google.st">google.st</option>
                                                                            <option value="google.td" data-country="td" data-content="<img src='https://serpbook.com/serp/images/country/td.png'> google.td">google.td</option>
                                                                            <option value="google.tg" data-country="tg" data-content="<img src='https://serpbook.com/serp/images/country/tg.png'> google.tg">google.tg</option>
                                                                            <option value="google.tk" data-country="tk" data-content="<img src='https://serpbook.com/serp/images/country/tk.png'> google.tk">google.tk</option>
                                                                            <option value="google.tl" data-country="tl" data-content="<img src='https://serpbook.com/serp/images/country/tl.png'> google.tl">google.tl</option>
                                                                            <option value="google.tm" data-country="tm" data-content="<img src='https://serpbook.com/serp/images/country/tm.png'> google.tm">google.tm</option>
                                                                            <option value="google.tn" data-country="tn" data-content="<img src='https://serpbook.com/serp/images/country/tn.png'> google.tn">google.tn</option>
                                                                            <option value="google.to" data-country="to" data-content="<img src='https://serpbook.com/serp/images/country/to.png'> google.to">google.to</option>
                                                                            <option value="google.tt" data-country="tt" data-content="<img src='https://serpbook.com/serp/images/country/tt.png'> google.tt">google.tt</option>
                                                                            <option value="google.vg" data-country="vg" data-content="<img src='https://serpbook.com/serp/images/country/vg.png'> google.vg">google.vg</option>
                                                                            <option value="google.vu" data-country="vu" data-content="<img src='https://serpbook.com/serp/images/country/vu.png'> google.vu">google.vu</option>
                                                                            <option value="google.ws" data-country="ws" data-content="<img src='https://serpbook.com/serp/images/country/ws.png'> google.ws">google.ws</option>
                                                                            </optgroup>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6">

                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <label>Tracking Options</label>
                                                                    </div>
                                                                    <div class="col-sm-8">
                                                                        <select name="tracking_option" id="" class="form-control" required>
                                                                            <option value="desktop">Desktop</option>
                                                                            <option value="mobile">Mobile</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <label>Language</label>
                                                                    </div>
                                                                    <div class="col-sm-8">
                                                                        <select id="language" name="language" class="form-control selectpicker" data-size="8" data-live-search="true" data-dropup-auto="false" style="outline: 0;" required>
                                                                            <option value="English">English</option>
                                                                            <option value="French">French</option>
                                                                            <option value="Spanish">Spanish</option>
                                                                            <option value="Arabic">Arabic</option>
                                                                            <option value="Hebrew">Hebrew</option>
                                                                            <option value="Chinese">Chinese</option>
                                                                            <option value="Thailand">Thailand</option>
                                                                            <option value="Dutch">Dutch</option>
                                                                            <option value="Russian">Russian</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <label>Locations</label>
                                                                    </div>
                                                                    <div class="col-sm-8">
                                                                        <select name="loc_name_canonical" class="form-control select-picker" data-live-search="true" required>

                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    </div>
                                                </div>

                                                <div class=" row">
                                                    <div class="col-sm-10">
                                                        <div class="text-right">
                                                        <button type="submit" class="btn btn-default"><i class="fa fa-paper-plane-o"></i> Submit</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="table-cover" id="serpTable" width="100%" cellspacing="0">
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
                                            <th>
                                                <!-- Action
                                                <label><input type="checkbox" name="select_all" class="selectall"/> Select all</label> -->
                                                <div class="my-checkbox">
                                                    <label><input type="checkbox" name="select_all" class="selectall"/><span class="checkbox"></span></label>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="serpTableBody">
                                        <?php if($getSerpValue) {
                                                foreach($getSerpValue as $serp_data) {
                                                    $post_data['request_id']    =   $serp_data['request_id'];
                                                    $post_data['keyword_id']    =   $serp_data['id'];
                                                    $getLatestKeyword           =   lastestKeywordPosition($post_data);
                                                    $getOneDayKeyword           =   oneDayKeyword($post_data);
                                                    $getLastSevenDayKeyword     =   weeklyKeywords($post_data);
                                                    $getThirthDayKeyword        =   fourthKeywords($post_data);
                                                    
                                                    if(!empty($getOneDayKeyword)){ 
                                                        $oneDayData =   abs($getOneDayKeyword['position'] - $getLatestKeyword['position']);
                                                        if($oneDayData > $getLatestKeyword['position'])
                                                            $oneDayData   .=  '<i class="fa fa-arrow-up"></i>'; 
                                                        else{
                                                            if($oneDayData != 0)
                                                                $oneDayData   .=  '<i class="fa fa-arrow-down"></i>'; 
                                                        }
                                                    }else{
                                                        $oneDayData = '-';
                                                    }

                                                    if(!empty($getLastSevenDayKeyword)){ 
                                                        $sevenDayData   =   abs($getLastSevenDayKeyword['position'] - $getLatestKeyword['position']);
                                                        if($sevenDayData > $getLatestKeyword['position'])
                                                            $sevenDayData   .=  '<i class="fa fa-arrow-up"></i>'; 
                                                        else{
                                                            if($sevenDayData != 0)
                                                                $sevenDayData   .=  '<i class="fa fa-arrow-down"></i>'; 
                                                        }
                                                    }else{
                                                        $sevenDayData   =   '-';
                                                    }

                                                    if(!empty($getThirthDayKeyword)){ 
                                                        $thirdDayData   =   abs($getThirthDayKeyword['position'] - $getLatestKeyword['position']);
                                                        if($thirdDayData > $getLatestKeyword['position'])
                                                            $thirdDayData   .=  '<i class="fa fa-arrow-up"></i>'; 
                                                        else {
                                                            if($thirdDayData != 0)
                                                                $thirdDayData   .=  '<i class="fa fa-arrow-down"></i>'; 
                                                        }
                                                    }else{
                                                        $thirdDayData   =   '-';
                                                    }

                                                    if(!empty($serp_data['start_ranking']) && !empty($getLatestKeyword['position']) ){ 
                                                        $start_ranking   =   abs($serp_data['start_ranking'] - $getLatestKeyword['position']);
                                                        if($start_ranking > $getLatestKeyword['position'])
                                                            $start_ranking   .=  '<i class="fa fa-arrow-up"></i>'; 
                                                        else {
                                                            if($start_ranking != 0)
                                                                $start_ranking   .=  '<i class="fa fa-arrow-down"></i>'; 
                                                        }
                                                    } else if(!empty($serp_data['start_ranking']) && empty($getLatestKeyword['position']) ){ 
                                                        $start_ranking   =   abs($serp_data['start_ranking'] - 0);
                                                    } else if(empty($serp_data['start_ranking']) && !empty($getLatestKeyword['position']) ){ 
                                                        $start_ranking   =   abs(100 -  $getLatestKeyword['position']);
                                                        $start_ranking   .=  '<i class="fa fa-arrow-up"></i>'; 
                                                    }else{
                                                        $start_ranking   =   '-';
                                                    }

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
                                                            <?php echo !empty($getLatestKeyword) ? $getLatestKeyword['position'] : '-' ; ?>
                                                        </td>
                                                        <td> <?php echo $oneDayData; ?> </td>
                                                        <td> <?php echo $sevenDayData; ?> </td>
                                                        <td> <?php echo $thirdDayData; ?> </td>
                                                        <td> <?php echo $start_ranking; ?> </td>
                                                        <td><?php echo date('d-m-Y', strtotime($serp_data['created'])); ?></td>
                                                        <td><?php echo $serp_data['cmp']?></td>
                                                        <td><?php echo $serp_data['sv']?></td>
                                                        <td>
                                                            <!-- <a href="#" class="keyword-trash" data-id="<?php echo $serp_data['id']; ?>"><i class="fa fa-trash"></i></a> -->
                                                            <!-- <input type="checkbox" name="check_list[]" value="<?php echo $serp_data['id']?>" /> -->

                                                            <div class="my-checkbox">
                                                        <label>
                                                        <input type="checkbox" name="check_list[]" value="<?php echo $serp_data['id']?>" />
                                                            <span class="checkbox"></span>
                                                        </label>
                                                    </div>
                                                        </td>
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
                                                            <?php echo !empty($getLatestKeyword) ? $getLatestKeyword['position'] : '-' ; ?>
                                                        </td>
                                                        <td> <?php echo $oneDayData; ?> </td>
                                                        <td> <?php echo $sevenDayData; ?> </td>
                                                        <td> <?php echo $thirdDayData; ?> </td>
                                                        <td> <?php echo $start_ranking; ?> </td>
                                                        <td><?php echo date('d-m-Y', strtotime($serp_data['created'])); ?></td>
                                                        <td><?php echo $serp_data['cmp']?></td>
                                                        <td><?php echo $serp_data['sv']?></td>
                                                        <td>
                                                            <!-- <a href="#" class="keyword-trash" data-id="<?php // echo $serp_data['id']; ?>"><i class="fa fa-trash"></i></a>
                                                            <input type="checkbox" name="check_list[]" value="<?php // echo $serp_data['id']?>" /> -->
                                                            <div class="my-checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="check_list[]" value="<?php echo $serp_data['id']?>" />
                                                                    <span class="checkbox"></span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                    <?php           } ?>
                                    <?php }
                                        } 
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if(!empty($getSerpValue) ) { ?>
                                <div id="yeskws_txt" style=";"><b>Last Updated</b>: <span id="lastupdate"> (<?php echo calculate_time_span(getLastUpdateKeyword($_REQUEST['id'])); ?>) </span></div>
                            <?php  } ?>
                            <div id="yeskws" style="display:none;"><b>Last Updated</b>: <span id="lastupdate">Gathering Data.. <img src="/assets/images/ajax-loader.gif"></span></div>

                        </div>
                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Organic Keyword Growth (12 months)
                                    </h5>
                                </div>
                                <div class="right">
                                    <div id="reportrange" data-module="organic_graph">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>

                            <?php if(!empty($getMozData)) {?>
                            <div class="table-data-outer keyword_hide" style="display:none;">
                                <div class="total_data purple2">
                                    <figure>
                                        <img src="/assets/images/roger_and_logo_moz-min.png" alt="">
                                    </figure>
                                    <h5><?php echo $getMozData['pageAuthority'].' /100'; ?>
                                    </h5>
                                    <cite>Page Authority</cite>

                                </div>
                                <div class="total_data orange">
                                    <figure>
                                        <img src="/assets/images/roger_and_logo_moz-min.png" alt="">
                                    </figure>

                                    <h5><?php echo $getMozData['domainAuthority'].' /100'; ?>
                                    </h5>
                                    <cite>Domain Authority</cite>
                                </div>
                            </div>
                            <?php } ?>

                            <div id="c3chartline" class="keyword_hide" style="opacity:0;"></div>
                            <div class="chart-loader" id="chart_1"> <img
                                    src="<?php echo 'assets/images/squares.gif'; ?>" /> </div>

                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Organic Traffic Growth (6 months)
                                    </h5>
                                </div>
                                <div class="right d-flex">
                                    <!-- <div class="my-checkbox">
                                        <label>
                                            <input type="checkbox" name="compare_graph" id="compare_graph" <?php //if(!empty($getCompareChart) && $getCompareChart['compare_status'] == 1) echo 'checked'?> >
                                            <span class="checkbox"></span>
                                        </label>
                                    </div> -->
                                    <form class="getCompareChartForm">
                                    <div class="switch">
                                        <label>
                                        <input type="checkbox"  class="check-toggle check-toggle-round-flat" name="compare_graph" id="compare_graph" <?php if(!empty($getCompareChart) && $getCompareChart['compare_status'] == 1) echo 'checked'?> >
                                            <span></span>
                                        </label>
                                    </div>
                                    </form>


                                    <div class="reportrange" data-module="organic_traffice">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="table-data-outer">
                                <div class="total_data purple" style="">
                                    <figure>
                                        <img src="/assets/images/google-logo-icon.png" alt="">
                                    </figure>
                                    <h5>
                                        <strong class="total_session">
                                        <?php
                                            if(!empty($getCurrentStats['sessions']) && !empty($getPreviousStats['sessions']) ) {
                                                echo $total_users = number_format(($getCurrentStats['sessions']  - $getPreviousStats['sessions']) / $getPreviousStats['sessions'] * 100, 2);
                                            } else if(empty($getCurrentStats['sessions']) && !empty($getPreviousStats['sessions']) ) {
                                                echo $total_users = -100;
                                            } else if(!empty($getCurrentStats['sessions']) && empty($getPreviousStats['sessions']) ) {
                                                echo $total_users = 100;
                                            } else{
                                                echo $total_users = 'N/A';
                                            }
                                            if($total_users != 'N/A') {
                                                if($total_users <= 0 )
                                                    echo '%<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                                                else
                                                    echo '%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                                            }
                                        ?>
                                        </strong>
                                        <span class="new_session">
                                        <?php
                                            $current_session    =   !empty($getCurrentStats['sessions']) ? $getCurrentStats['sessions'] : '0';
                                            $previous_session   =   !empty($getPreviousStats['sessions']) ? $getPreviousStats['sessions'] : '0';
                                            echo $current_session.' vs '.$previous_session;
                                        ?>
                                        </span>
                                        <small>Organic Traffic</small>
                                    </h5>
                                    <cite>Sessions</cite>
                                </div>
                                <div class="total_data green" style="">
                                    <figure>
                                        <img src="/assets/images/google-logo-icon.png" alt="">
                                    </figure>
                                    <h5>
                                        <strong class="total_users">
                                        <?php
                                            if(!empty($getCurrentStats['users']) && !empty($getPreviousStats['users']) ) {
                                                echo $total_users = number_format(($getCurrentStats['users']  - $getPreviousStats['users']) / $getPreviousStats['users'] * 100, 2);
                                            } else if(empty($getCurrentStats['users']) && !empty($getPreviousStats['users']) ) {
                                                echo $total_users = -100;
                                            } else if(!empty($getCurrentStats['users']) && empty($getPreviousStats['users']) ) {
                                                echo $total_users = 100;
                                            } else{
                                                echo $total_users = 'N/A';
                                            }
                                            if($total_users != 'N/A') {
                                                if($total_users <= 0 )
                                                    echo '%<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                                                else
                                                    echo '%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                                            }

                                        ?>
                                        </strong>
                                        <span class="new_users">
                                            <?php
                                                $current_users    =   !empty($getCurrentStats['users']) ? $getCurrentStats['users'] : '0';
                                                $previous_users   =   !empty($getPreviousStats['users']) ? $getPreviousStats['users'] : '0';
                                                echo $current_users.' vs '.$previous_users;
                                            ?>
                                        </span>
                                        <small>Organic Traffic</small>
                                    </h5>
                                    <cite>Users</cite>
                                </div>
                                <div class="total_data yellow" style="">
                                    <figure>
                                        <img src="/assets/images/google-logo-icon.png" alt="">
                                    </figure>
                                    <h5>
                                        <strong class="total_pageview">
                                        <?php
                                            if(!empty($getCurrentStats['pageview']) && !empty($getPreviousStats['pageview']) ) {
                                                echo $total_users = number_format(($getCurrentStats['pageview']  - $getPreviousStats['pageview']) / $getPreviousStats['pageview'] * 100, 2);
                                            } else if(empty($getCurrentStats['pageview']) && !empty($getPreviousStats['pageview']) ) {
                                                echo $total_users = -100;
                                            } else if(!empty($getCurrentStats['pageview']) && empty($getPreviousStats['pageview']) ) {
                                                echo $total_users = 100;
                                            } else{
                                                echo $total_users = 'N/A';
                                            }
                                            if($total_users != 'N/A') {
                                                if($total_users <= 0 )
                                                    echo '%<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                                                else
                                                    echo '%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                                            }

                                        ?>
                                        </strong>
                                        <span class="new_pageview">
                                            <?php
                                                $current_pageview    =   !empty($getCurrentStats['pageview']) ? $getCurrentStats['pageview'] : '0';
                                                $previous_pageview   =   !empty($getPreviousStats['pageview']) ? $getPreviousStats['pageview'] : '0';
                                                echo $current_pageview.' vs '.$previous_pageview;
                                            ?>
                                        </span>
                                        <small>Organic Traffic</small>
                                    </h5>
                                    <cite>Pageviews</cite>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div id="chart-1-container" class="chart">
                                <?php if(empty($count_session)) { ?>
                                    <img src="<?php echo 'assets/images/analytics.png'; ?>" />
                                <?php } ?>
                            </div>
                            <div class="chart-loader" id="chart_2"> <img src="<?php echo 'assets/images/squares.gif'; ?>" /> </div>

                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Organic Keywords
                                    </h5>
                                </div>
                                <div class="right">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i
                                                class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">
                                                More Options
                                            </li>
                                            <li>
                                                <a href="#" data-hover="tooltip" id="organic_edit_note" title="" data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                    <?php if(!empty($checkEditNotes1))  echo 'Edit '; else echo 'Add '; ?> Summary
                                                </a>
                                            </li>
                                            <li>
                                                <?php if(!empty($checkEditNotes1)) { ?>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_second" title="" data-row="summernote1" data-id="<?php echo $checkEditNotes1['id']; ?>" data-text = "organic_edit_note" class="delete_edit_notes" data-placement="left" data-original-title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </a>
                                                <?php } else { ?>
                                                    <a href="#" data-hover="tooltip" data-pageid="edit_note_second" title="" data-row="summernote1" data-id="" data-text="organic_edit_note" class="delete_edit_notes" data-placement="left" data-original-title="Delete" style="display:none;">
                                                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                    </a>
                                                <?php  } ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="edit-section-area">

                                <div id="organic_keyword_edit_section" style="display:none">
                                    <div id="summernote1" class="summernote"></div>
                                    <div class="text-right">
                                        <a class="btn btn-danger close-summernote" data-uid="organic_edit_note" data-id="organic_keyword_edit_section" >Cancel</a>
                                        <a class="btn btn-success" id="organic_save_note">Save</a>
                                    </div>
                                </div>
                                <div class="edit_note_second">
                                    <?php if(!empty($checkEditNotes1)) { ?>
                                        <?php echo $checkEditNotes1['edit_section']; ?>
                                    <?php }	?>
                                </div>
                            </div>
                            <div class="table-cover table2">
                                <table id="semrush_organic_table" width="100%" cellspacing="0">
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
                            </div>


                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                    Backlink Profile
                                    </h5>
                                </div>
                                <div class="right">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i
                                                class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">
                                                More Options
                                            </li>
                                            <li>
                                                <a href="#" data-hover="tooltip" id="backlink_edit_note" title=""
                                                    data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                    <?php if(!empty($checkEditNotes2))  echo 'Edit '; else echo 'Add '; ?> Summary
                                                </a>
                                            </li>
                                            <li>
                                                <?php if(!empty($checkEditNotes2)) { ?>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_third" title=""
                                                    data-row="summernote2"
                                                    data-id="<?php echo $checkEditNotes2['id']; ?>"
                                                    data-text = "backlink_edit_note"
                                                    class="delete_edit_notes" data-placement="left"
                                                    data-original-title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </a>
                                                <?php } ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="edit-section-area">
                                <div id="backlink_keyword_edit_section" style="display:none">
                                    <div id="summernote2" class="summernote"></div>
                                    <div class="col-md-12">
                                        <a class="btn btn-danger close-summernote" data-uid="backlink_edit_note"  data-id="backlink_keyword_edit_section" >Cancel</a>
                                        <a class="btn btn-success" id="backlink_save_note">Save</a>
                                    </div>
                                </div>


                                <?php
									if(!empty($checkEditNotes2)) {
								?>

                                <div class="edit_note_third">
                                    <?php echo $checkEditNotes2['edit_section']; ?>
                                </div>

                                <?php
									}
								?>
                            </div>

                            <div class="table-cover table2">
                                <table id="semrush_backlink_table" width="100%" cellspacing="0">
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
                                                <div><strong>Source:</strong>
                                                    <?php echo $record['source_url'] ?></div>
                                                <div><strong>Target:</strong>
                                                    <?php echo $record['target_url'] ?></div>
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
                                                <div class="<?php if($record['newlink'] == 'true') { echo "lostlink-success"; } ?>">
                                                <?php echo date('d M Y', $record['first_seen']) ?></div>
                                            </td>
                                            <td>
                                                <div class="<?php if($record['lostlink'] == 'true') { echo "lostlink-success"; }?>">
                                                <?php echo date('d M Y', $record['last_seen']) ?></div>
                                            </td>
                                        </tr>
                                        <?php }
											}
										?>
                                    </tbody>
                                </table>
                            </div>

                        </div>


                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        Google Analytics Goal Completion
                                    </h5>
                                </div>
                                <div class="right">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i
                                                class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">
                                                More Options
                                            </li>
                                            <li>
                                                <a href="#" data-hover="tooltip" id="goal_edit_note" title="" data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                    <?php if(!empty($checkEditNotes3))  echo 'Edit '; else echo 'Add '; ?> Summary
                                                </a>
                                            </li>
                                            <li>
                                                <?php if(!empty($checkEditNotes3)) { ?>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_fourth" title="" data-row="summernote3" data-id="<?php echo $checkEditNotes3['id']; ?>" data-text = "goal_edit_note" class="delete_edit_notes" data-placement="left" data-original-title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </a>
                                                <?php } ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="edit-section-area">
                                <div id="goal_keyword_edit_section" style="display:none">
                                    <div id="summernote3" class="summernote"></div>
                                    <div class="text-right">
                                        <a class="btn btn-danger close-summernote" data-uid="goal_edit_note"  data-id="goal_keyword_edit_section" >Cancel</a>
                                        <a class="btn btn-success" id="goal_save_note">Save</a>
                                    </div>
                                </div>


                                <?php
									if(!empty($checkEditNotes3)) {
								?>
                                <div class="edit_note_fourth">
                                    <?php echo $checkEditNotes3['edit_section']; ?>
                                </div>
                                <?php
									}
								?>
                            </div>

                            <div id="chart-4-container" class="chart"></div>

                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                        <span class="notes_heading"><?php echo ($checkEditNotes4['note_heading'] != '' ? $checkEditNotes4['note_heading'] : 'Notes'); ?></span>
                                        <div id="heading_input_field" style="display:none"> Heading : <input type="text" name="notes_value" id="notes_value" value="<?php echo ($checkEditNotes4['note_heading'] != '' ? $checkEditNotes4['note_heading'] : 'Notes'); ?>">
                                        </div>
                                    </h5>
                                </div>
                                <div class="right">
                                    <div class="dropdown">
                                        <a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">
                                                More Options
                                            </li>
                                            <li>
                                                <a href="#" data-hover="tooltip" id="edit_notes" title="" data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                    <?php if(!empty($checkEditNotes4))  echo 'Edit '; else echo 'Add '; ?> Summary
                                                </a>
                                            </li>
                                            <li>
                                                <?php if(!empty($checkEditNotes4)) { ?>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_fifth" title="" data-row="summernote4" data-id="<?php echo $checkEditNotes4['id']; ?>" data-text = "edit_note_fifth" class="delete_edit_notes" data-placement="left" data-original-title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                </a>
                                                <?php } ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="edit-section-area">
                                <div id="edit_bottom_section" style="display:none">
                                    <div id="summernote4" class="summernote"></div>
                                    <div class="col-md-12">
                                        <a class="btn btn-danger close-summernote" data-uid="edit_notes" data-id="edit_bottom_section" >Cancel</a>
                                        <a class="btn btn-success" id="save_bottom">Save</a>
                                    </div>
                                </div>

                                <?php
									if(!empty($checkEditNotes4)) {
								?>
                                <div class="edit_note_fifth">
                                    <?php echo $checkEditNotes4['edit_section']; ?>
                                </div>
                                <?php
									}
								?>
                            </div>

                        </div>

                        <?php if(!empty($getActivityDetails)){
                                $div_status      =  'left';
                        ?>
                        <div class="white-box activity-tree">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>
                                    Activity Performed
                                    </h5>
                                </div>
                            </div>
                            <div class="main-timeline">
                                <?php foreach($getActivityDetails as $activity_detail) {
                                        $activity_ids    =   explode(',', $activity_detail['ids']);
                                ?>
                                    <div class="timeline-section">
                                        <div class="timeline-date">
                                            <span class="date"><?php echo date('M d, Y', strtotime($activity_detail['activity_date'])); ?></span>
                                        </div>
                                            <?php foreach($activity_ids as $activity_id) {
                                                    $getActivityData  =   getActivityDataID($activity_id);
                                                    $work_status      =   ($getActivityData['activity_status'] == 1 ? 'Working' : 'Completed');
                                                    $work_class       =   ($getActivityData['activity_status'] == 1 ? 'work' : '');
                                                    $div_align        =   ($div_status == 'left' ? 'right' : 'left');
                                                    $div_status       =   $div_align;
                                            ?>
                                                <div class="timeline <?php echo $div_status; ?> ">
                                                    <div class="timeline-icon"></div>
                                                    <div class="timeline-content">
                                                        <span class="status <?php echo $work_class; ?>"><?php echo $work_status ?></span>
                                                        <h5 class="title"><?php echo $getActivityData['Activity_type'] ?> - <?php echo $getActivityData['Activity_task'] ?></h5>
                                                        <p class="description"><?php echo nl2br($getActivityData['activity_desc'])?></p>
                                                        <span class="date"><?php echo $getActivityData['activity_hour']?> Hours</span>
                                                        <a href="#" class="activity_delete dlt" data-id="<?php echo $getActivityData['id']?>"><i class="fa fa-trash"></i></a>
                                                    </div>
                                                </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>

                    </div>

                </div><!-- #end row -->

            </div><!-- #end row -->

        </div>
    </div>

</div> <!-- #end main-container -->


<!-- theme settings -->
<div class="site-settings clearfix hidden-xs" style="display:none;">
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
<link rel="stylesheet" href="assets/styles/jquery.dataTables.min.css">
</link>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
</link>
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

#c3chartline {
    overflow: visible !important;
}
</style>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="<?php echo FULL_PATH; ?>assets/scripts/vendors.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/perfect-scrollbar.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/jquery.dataTables.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script src="//code.highcharts.com/highcharts.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ajax-bootstrap-select/1.4.5/js/ajax-bootstrap-select.js" integrity="sha256-4JjN6XExswMnejcRZjQA+29fpeYgQcUBpu95IPf0r4Y=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.html5.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {


    $('.cities').focusout(function () {
        var text = $('.cities').val();
        text = text.replace(/(?:(?:\r\n|\r|\n)\s*){2}/gm, "");
         $(this).val(text);

    });


    $(document).on('click keyup', ".serpTd", function(e){
        e.preventDefault();
        $('.serpId').prop("contentEditable", false);

        if($(this).attr("contentEditable") == true){
            $(this).attr("contentEditable",false);
        } else {
            $(this).attr("contentEditable",true);
        }
    })

    $(document).on('focusout paste', ".serpTd", function(e){
        e.preventDefault();
        $(this).attr("contentEditable",false);
        var updated_val     =   $(this).text();
        var request_id      =   $(this).attr('data-id');
        var old_value       =   $(this).attr('data-value');
        if(updated_val != old_value ) {
            $.ajax({
                type: "POST",
                url: "assets/ajax/serpTracking.php",
                data: {action: 'update_serp', start_date: updated_val, request_id: request_id },
                success: function(result) {
                    if(result['status'] == 'success'){
                        Command: toastr["success"](result['message']);
                    }else{
                        Command: toastr["error"](result['message']);
                    }
                }
            })
        }
    })


    $('.activity_delete').on('click', function(e){
        e.preventDefault();
        $(this).parents('.timeline').remove();
        var ids    =   $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveActivityForm.php",
            data: {action: 'remove_activity', share_id: ids },
            success: function(result) {
                if(result['status'] == 'success'){
                    Command: toastr["success"](result['message']);
                }else{
                    Command: toastr["error"](result['message']);
                }
            }
        })

    })

    $('#semrush_organic_table').DataTable({
        "order": [
            [1, "asc"]
        ],
        "pageLength": 50,

    });

    $('#semrush_backlink_table').DataTable({
        "pageLength": 25
    });

    $('#serpTableRow').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excel', 'pdf'
        ],
        "columnDefs": [
            { "targets": [0], "orderable": false },
            { "targets": [12], "orderable": false },
        ],

        "pageLength": 25,


        buttons: [{
                    extend: 'excel',
                    filename: 'livekeyword',
                    exportOptions: {
                        columns: [ 1, 2, 3, 4, 5, 6, 7, 8,9,10,11]
                    }

                }]

    });

    $('#summernote').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true, // set focus to editable area after initializing summernote
        callbacks: {
            onImageUpload: function(files, editor, welEditable) {
                that = $(this);
                sendFile(files[0], that, welEditable);
            },
            onMediaDelete : function(target) {
                // alert(target[0].src)
                deleteFile(target[0].src);
            }
        }

    });

    $('#summernote1').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        callbacks: {
            onImageUpload: function(files, editor, welEditable) {
                that = $(this);
                sendFile(files[0], that, welEditable);
            },
            onMediaDelete : function(target) {
                // alert(target[0].src)
                deleteFile(target[0].src);
            }
        }
    });
    $('#summernote2').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        callbacks: {
            onImageUpload: function(files, editor, welEditable) {
                that = $(this);
                sendFile(files[0], that, welEditable);
            },
            onMediaDelete : function(target) {
                // alert(target[0].src)
                deleteFile(target[0].src);
            }
        }
    });
    $('#summernote3').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        callbacks: {
            onImageUpload: function(files, editor, welEditable) {
                that = $(this);
                sendFile(files[0], that, welEditable);
            },
            onMediaDelete : function(target) {
                // alert(target[0].src)
                deleteFile(target[0].src);
            }
        }
    });
    $('#summernote4').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        callbacks: {
            onImageUpload: function(files, editor, welEditable) {
                that = $(this);
                sendFile(files[0], that, welEditable);
            },
            onMediaDelete : function(target) {
                // alert(target[0].src)
                deleteFile(target[0].src);
            }
        }
    });
    //$('.summernote').eq(0).code('<?php //echo $checkEditNotes0['edit_section']; ?>');
    //$('.summernote').eq(1).code('<?php //echo $checkEditNotes1['edit_section']; ?>');
    var html_text = <?php echo json_encode($checkEditNotes0['edit_section']); ?>;
    var html_text1 = <?php echo json_encode($checkEditNotes1['edit_section']); ?>;
    //console.log(html_text1);
    var html_text2 = <?php echo json_encode($checkEditNotes2['edit_section']); ?>;
    var html_text3 = <?php echo json_encode($checkEditNotes3['edit_section']); ?>;
    var html_text4 = <?php echo json_encode($checkEditNotes4['edit_section']); ?>;
    if (html_text != null && html_text.length !== 0) {
        $('.summernote').eq(0).summernote('code',html_text.replace(/\\/g, ''));
    }
    if (html_text1 != null) {
        $('.summernote').eq(1).summernote('code',html_text1.replace(/\\/g, ''));
    }
    if (html_text2 !== null && html_text2.length !== 0) {
        //	console.log('backlink');
        //	console.log(html_text2);
        $('.summernote').eq(2).summernote('code',html_text2.replace(/\\/g, ''));
    }
    if (html_text3 != null && html_text3.length !== 0) {
        //	console.log(html_text3);
        $('.summernote').eq(3).summernote('code',html_text3.replace(/\\/g, ''));
    }
    if (html_text4 != null && html_text4.length !== 0) {
        //	console.log(html_text4);
        $('.summernote').eq(4).summernote('code',html_text4.replace(/\\/g, ''));
    }
    //var new_val			=	html_text.replace(//\//g, '');

    <?php if(empty($googleAnalytics['property_id'])) { ?>
    //$('#semrush_details').modal();
    <?php } ?>
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
            tickPositions: [<?php echo (@$organic_date); ?>],

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

    <?php  if(!empty($count_session)) {?>
    var chart   =   $('#chart-1-container').highcharts({
        chart: {
            type: 'area',
            height: '410',
            events: {
                load: function() {
                    var myChart = this;
                    var compare_status  =   $('#compare_graph').is(":checked");
                    var compare_value   =   '';
                    if(compare_status == true && myChart.series[1] != undefined){
                        console.log('here')
                        myChart.series[1].show();
                        compare_value   =   1;
                        myChart.series[1].update({
                            showInLegend: true
                        }, true, false);
                    }else{
                        if(myChart.series[1] != undefined){
                            myChart.series[1].hide();
                            compare_value   =   0;
                            myChart.series[1].update({
                                showInLegend: false
                            }, true, false);
                        }
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
        series: [{
                name: '<?php echo 'Current Period: '. $current_period; ?>',
                data: [<?php echo join($count_session, ','); ?>]
            },
            <?php if(!empty($combine_session)) { ?>
            {
                name: '<?php echo 'Previous Period: '. $previous_period; ?>',
                data: [<?php echo join($combine_session, ','); ?>],
            }
            <?php } ?>
        ]
    });
    <?php } ?>
});

function organicKeywordGrowth(organicData, organic_keywords) {
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
            tickPositions: organicData,
            plotLines: [{
                color: '#FF0000',
                width: 2,
                value: 3,
                zIndex: 5
            }],

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
            data: organic_keywords
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

}
</script>

<script>
$(document).ready(function() {
    $('[data-hover="tooltip"]').tooltip()
});
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
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

    var dataChart1 = new gapi.analytics.report.Data({
        query: {
            'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
            'start-date': '365daysAgo',
            'end-date': 'yesterday',
            'metrics': 'ga:sessions',
            'dimensions': 'ga:date',
            'segment': 'gaid::-5'
        },

    });

    var data_id_key = '<?php echo $googleAnalytics['property_id'];?>';
    var request_id_key = '<?php echo $_REQUEST["id"]; ?>';
    var response = [];
    <?php if(empty($count_session)) { ?>
    dataChart1.on('success', function(records) {
        var response = JSON.stringify(records['rows']);
        //console.log(response);
        jQuery.ajax({
            type: 'POST',
            url: "assets/ajax/ajax_session_response.php",
            data: {
                action: 'session_response',
                view_id: data_id_key,
                request_id: request_id_key,
                rows: response
            },
            dataType: 'json',
            success: function(result) {
                console.log(result['current_stats']['users']);
                $('.new_users').text(result['current_stats']['users']+' vs '+result['combine_stats']['users']);
                $('.new_session').text(result['current_stats']['sessions']+' vs '+result['combine_stats']['sessions']);
                $('.new_pageview').text(result['current_stats']['pageview']+' vs '+result['combine_stats']['pageview']);
                if((result['current_stats']['users'] != '' && result['current_stats']['users'] != undefined)  && (result['combine_stats']['users'] != '' && result['combine_stats']['users'] != undefined)) {
                    var total_users = ((result['current_stats']['users'] - result['combine_stats']['users']) / result['combine_stats']['users'] * 100).toFixed(2);
                } else if(result['current_stats']['users'] == '' &&  result['combine_stats']['users'] != '') {
                    var total_users = -100;
                } else if(result['current_stats']['users'] != '' && result['combine_stats']['users'] == '') {
                    var total_users = 100;
                } else{
                    var total_users = 'N/A';
                }
                if(total_users != 'N/A') {
                    if(total_users <= 0 )
                        total_users = total_users+'%<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                    else
                        total_users = total_users+'%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                }

                if((result['current_stats']['sessions'] != '' && result['current_stats']['sessions'] != undefined)  && (result['combine_stats']['sessions'] != '' && result['combine_stats']['sessions'] != undefined)) {
                    var total_session = ((result['current_stats']['sessions'] - result['combine_stats']['sessions']) / result['combine_stats']['sessions'] * 100).toFixed(2);
                } else if(result['current_stats']['sessions'] == '' &&  result['combine_stats']['sessions'] != '') {
                    var total_session = -100;
                    var session       = '<span id="traffic_growth" class="red">-100%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';
                } else if(result['current_stats']['sessions'] != '' && result['combine_stats']['sessions'] == '') {
                    var total_session = 100;
                    var session       = '<span id="traffic_growth" class="green">100%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';
                } else{
                    var total_session = 'N/A';
                    var session       = '<span id="traffic_growth" class="green">NA</span><small>Organic Visitors From Google </small>';
                }
                if(total_session != 'N/A') {
                    if(total_session <= 0 ) {
                        var session  = result['current_stats']['sessions']+'<span id="traffic_growth" class="red">'+total_session+'% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';
                        total_session = total_session+'%<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                    } else{
                        var session  = result['current_stats']['sessions']+'<span id="traffic_growth" class="green">'+total_session+'% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';
                        total_session = total_session+'%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    }
                }

                    total_session  = total_session;

                if((result['current_stats']['pageview'] != '' && result['current_stats']['pageview'] != undefined)  && (result['combine_stats']['pageview'] != '' && result['combine_stats']['pageview'] != undefined)) {
                    var total_pageview = ((result['current_stats']['pageview'] - result['combine_stats']['pageview']) / result['combine_stats']['pageview'] * 100).toFixed(2);
                } else if(result['current_stats']['pageview'] == '' &&  result['combine_stats']['pageview'] != '') {
                    var total_pageview = -100;
                } else if(result['current_stats']['pageview'] != '' && result['combine_stats']['pageview'] == '') {
                    var total_pageview = 100;
                } else{
                    var total_pageview = 'N/A';
                }
                if(total_pageview != 'N/A') {
                    if(total_pageview <= 0 )
                        total_pageview = total_pageview+'%<i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                    else
                        total_pageview = total_pageview+'%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                }

                console.log(total_session);
                $('.total_session').html(total_session);
                $('.total_users').html(total_users);
                $('.total_pageview').html(total_pageview);
                $('.ajax_session').html(session);

                highChartMap(result);
            }
        });
        //console.log(records);
    });
    dataChart1.execute();
    <?php } ?>
    var dataChart2 = new gapi.analytics.report.Data({
        query: {
            'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
            'start-date': '365daysAgo',
            'end-date': '182daysAgo',
            'metrics': 'ga:goalCompletionsAll',
            'dimensions': 'ga:keyword'
        },
    });

    var data_id_key = '<?php echo $googleAnalytics["property_id"];?>';
    var request_id_key = '<?php echo $_REQUEST["id"]; ?>';
    var response = [];
    dataChart2.on('success', function(records) {
        var response = JSON.stringify(records['rows']);
        //console.log(response);
        jQuery.ajax({
            type: 'POST',
            url: "assets/ajax/ajax_session_response.php",
            data: {
                action: 'goal_completions_last',
                view_id: data_id_key,
                request_id: request_id_key,
                rows: response
            },
            dataType: 'json',
            success: function(result) {
                $('.goal_completions').val(result['goal_count']);
            }
        });
        //console.log(records);
    });
    dataChart2.execute();


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

function highChartMap(result) {
    var from_dates = (result['from_dates']);
    var count_session = (result['count_session']);
    var combine_session = (result['combine_session']);
    var plot_index = (result['plot_index']);
    var res = count_session;
    //var b 				= count_session.split(',').map(Number);
    console.log(from_dates);
    console.log(count_session);
    console.log(plot_index);
    //console.log(b);
    // for (var i = 0; i < count_session.length; i++) {
    //     res[i] = (parseInt(count_session[i], 10));
    // }
    var chart   =   $('#chart-1-container').highcharts({
        chart: {
            type: 'area',
            height: '410',
            events: {
                load: function() {
                    var myChart = this;
                    var compare_status  =   $('#compare_graph').is(":checked");
                    var compare_value   =   '';
                    if(compare_status == true){
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

                    $(document).on("change", "#compare_graph", function(e) {
                        e.preventDefault();
                        var compare_status  =   $(this).is(":checked");
                        var compare_value   =   '';
                        if(compare_status == true){
                            chart.series[1].show();
                            compare_value   =   1;
                            chart.series[1].update({
                                showInLegend: true
                            }, true, false);
                        }else{
                            chart.series[1].hide();
                            compare_value   =   0;
                            chart.series[1].update({
                                showInLegend: false
                            }, true, false);
                        }
                        var request_id      =   '<?php echo $_REQUEST["id"]; ?>';
                        jQuery.ajax({
                            type: 'POST',
                            url: "assets/ajax/ajax_session_response.php",
                            data: {
                                action: 'compare_status',
                                request_id: '<?php echo $_REQUEST["id"]?>',
                                compare_status: compare_value
                            },
                            success: function(result) {
                                //Command: toastr["success"]('Update Compare Graph !');
                            }
                        });
                    });

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
            categories: from_dates,
            title: {
                enabled: false
            },
            plotLines: [{
                color: '#FF0000',
                width: 2,
                value: plot_index,
                zIndex: 99999,
                label: {
                    text: 'Project Start Here',
                    align: 'left',
                    y: 0,
                    verticalAlign: 'top',
                    rotation: -360
                }

            }],
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        tooltip: {
            split: true,
            valueSuffix: ' ',
            useHTML: true,
            formatter: function () {
                return this.points.reduce(function (s, point) {
                    console.log(s);
                    return s + '<br/>' + point.series.name + ': ' +
                        point.y + 'm';
                }, '<b>' + this.x + '</b>');
            },

        },
        credits: {
            enabled: false
        },
        series: [
            {
                name: 'Current Period:'+result['current_period'],
                color: 'green',
                data: res
            },
            {
                name: 'Previous Period:'+result['previous_period'],
                data: combine_session,
            }
        ]
    });
}
</script>

<?php } ?>
<script>
$(document).on("change", ".onchange", function() {
    $('.select_box_loader').show();
    var data_id = $(this).val();
    var request_id = '<?php echo $_REQUEST["id"]; ?>';
    jQuery.ajax({
        type: 'POST',
        url: "assets/ajax/ajax_google_view_details.php",
        data: {
            action: 'update_select_div',
            request_id: '<?php echo $_REQUEST["id"]?>',
            analytic_id: data_id
        },
        success: function(result) {
            console.log(result);
            $('.select_box_loader').hide();
            $('#analytic_account').html(result);
            $('#analytic_account').selectpicker('refresh');
            var li = '<option value=""><--Select Property --></option>';
            $('#analytic_property').html(li);
            var li = '<option value=""><--Select View ID  --></option>';
            $('#analytic_view_id').html(li);
        }
    });
});

$(document).on("click", ".ssss", function() {
    var myBookId = $(this).data('id');
    $(".modal-body #share_id").val(myBookId);
    $.ajax({
        action: 'email_data',
        type: "POST",
        url: "assets/ajax/ajax-email-data.php",
        data: {
            action: 'email_data',
            share_id: myBookId
        },
        success: function(result) {
            var status = result['status'];
            var analytic_id = result['analytic_id'];
            if (status == 'success') {
                $('#subject').val(result['subject']);
                $('#to').val(result['email_to']);
                $('#message').val(result['email_message']);
                $('#mail_from').val(result['email_sender']);
                $('#mail_sender_name').val(result['email_sender_name']);
            }
        }
    });
});

$(document).on("click", "#saveAbout", function() {
    var sHTML = $('#summernote').summernote('isEmpty') ? '' : $('#summernote').summernote('code');
    var requestId = '<?php echo $_REQUEST['id']?>';
    $.ajax({
        action: 'email_data',
        type: "POST",
        url: "assets/ajax/ajax-save-text.php",
        data: {
            action: 'email_data',
            share_id: requestId,
            content_text: sHTML,
            edit_section: '0'
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                $("#summernote").eq(0).summernote("reset")
                $('#monthly_report_edit_section').hide();
                $('.edit-success').addClass('animated bounceInUp');
                $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-success').removeClass('animated bounceInUp');
                $('.edit_note_first').html(sHTML);
                $('#summernote').eq(0).summernote('code', sHTML);
                $('.edit_note_first').show();
                $('#monthly_report_note').html('<i class="fa fa-pencil-square-o"></i> Edit Summery');
                $('#monthly_report_note').show();
            } else {
                $('#monthly_report_edit_section').hide();
                $('.edit-error').addClass('animated bounceInUp');
                $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-error').removeClass('animated bounceInUp');
                $('#summernote').eq(0).summernote('code', sHTML);
                $('#monthly_report_note').show();
            }
        }
    });
});

$(document).on("click", "#save_bottom", function() {
    var notes = $('#notes_value').val();
    if ($('#notes_value').val().length === 0) {
        $('#notes_value').addClass('note_warning');
        $("#notes_value").focus();
        return false;
    }
    var sHTML = $('#summernote4').summernote('isEmpty') ? '' : $('#summernote4').summernote('code');
    var requestId = '<?php echo $_REQUEST['id']?>';
    $.ajax({
        action: 'email_data',
        type: "POST",
        url: "assets/ajax/ajax-save-text.php",
        data: {
            action: 'email_data',
            share_id: requestId,
            content_text: sHTML,
            edit_section: '4',
            note_heading: notes
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                $("#summernote4").summernote("reset")
                $('#edit_bottom_section').hide();
                $('.notes_heading').text(notes);
                $('#heading_input_field').hide();
                $('.notes_heading').show();
                $('.edit-success').addClass('animated bounceInUp');
                $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-success').removeClass('animated bounceInUp');
                $('.edit_note_fifth').html(sHTML);
                $('#summernote4').summernote('code', sHTML);
                $('.edit_note_fifth').show();
                $('#edit_notes').html('<i class="fa fa-pencil-square-o"></i> Edit Summery');
                $('#edit_notes').show();

            } else {
                $('#edit_bottom_section').hide();
                $('.notes_heading').text(notes);
                $('#heading_input_field').hide();
                $('.notes_heading').show();
                $('.edit-error').addClass('animated bounceInUp');
                $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-error').removeClass('animated bounceInUp');
                $('#summernote4').summernote('code', sHTML);
                $('#edit_notes').show();
            }
        }
    });
});

$(document).on("click", "#organic_save_note", function() {
    var sHTML = $('#summernote1').summernote('isEmpty') ? '' : $('#summernote1').summernote('code');
    var requestId = '<?php echo $_REQUEST['id']?>';
    $.ajax({
        action: 'email_data',
        type: "POST",
        url: "assets/ajax/ajax-save-text.php",
        data: {
            action: 'email_data',
            share_id: requestId,
            content_text: sHTML,
            edit_section: '1'
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                $("#summernote1").summernote("reset")
                $('#organic_keyword_edit_section').hide();
                $('.edit-success').addClass('animated bounceInUp');
                $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-success').removeClass('animated bounceInUp');
                $('.edit_note_second').html(sHTML);
                $('#summernote1').summernote('code', sHTML);
                $('.edit_note_second').show();
                $('#organic_edit_note').html('<i class="fa fa-pencil-square-o"></i> Edit Summery');
                $('#organic_edit_note').show();
            } else {
                $('#organic_keyword_edit_section').hide();
                $('.edit-error').addClass('animated bounceInUp');
                $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-error').removeClass('animated bounceInUp');
                $('#summernote1').summernote('code', sHTML);
                $('#organic_edit_note').show();
            }
        }
    });
});

$(document).on("click", "#backlink_save_note", function() {
    var sHTML = $('#summernote2').summernote('isEmpty') ? '' : $('#summernote2').summernote('code');
    var requestId = '<?php echo $_REQUEST['id']?>';
    $.ajax({
        action: 'email_data',
        type: "POST",
        url: "assets/ajax/ajax-save-text.php",
        data: {
            action: 'email_data',
            share_id: requestId,
            content_text: sHTML,
            edit_section: '2'
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                $("#summernote2").summernote("reset")
                $('#backlink_keyword_edit_section').hide();
                $('.edit-success').addClass('animated bounceInUp');
                $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-success').removeClass('animated bounceInUp');
                $('.edit_note_third').html(sHTML);
                $('#summernote2').summernote('code', sHTML);
                $('.edit_note_third').show();
                $('#backlink_edit_note').html('<i class="fa fa-pencil-square-o"></i> Edit Summery');
                $('#backlink_edit_note').show();
            } else {
                $('#backlink_keyword_edit_section').hide();
                $('.edit-error').addClass('animated bounceInUp');
                $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-error').removeClass('animated bounceInUp');
                $('#summernote2').summernote('code', sHTML);
                $('#backlink_edit_note').show();
            }
        }
    });
});

$(document).on("click", "#goal_save_note", function() {
    var sHTML = $('#summernote3').summernote('isEmpty') ? '' : $('#summernote3').summernote('code');
    var requestId = '<?php echo $_REQUEST['id']?>';
    $.ajax({
        action: 'email_data',
        type: "POST",
        url: "assets/ajax/ajax-save-text.php",
        data: {
            action: 'email_data',
            share_id: requestId,
            content_text: sHTML,
            edit_section: '3'
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                $("#summernote3").summernote("reset")
                $('#goal_keyword_edit_section').hide();
                $('.edit-success').addClass('animated bounceInUp');
                $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-success').removeClass('animated bounceInUp');
                $('.edit_note_fourth').html(sHTML);
                $('#summernote3').summernote('code', sHTML);
                $('.edit_note_fourth').show();
                $('#goal_edit_note').html('<i class="fa fa-pencil-square-o"></i> Edit Summery');
                $('#goal_edit_note').show();
            } else {
                $('#goal_keyword_edit_section').hide();
                $('.edit-error').addClass('animated bounceInUp');
                $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                $('.edit-error').removeClass('animated bounceInUp');
                $('#summernote3').summernote('code', sHTML);
                $('#goal_edit_note').show();
            }
        }
    });
});

$(document).on("click", "#edit_notes", function(e) {
    e.preventDefault();
    $(".notes_heading").hide();
    $("#heading_input_field").show();
    if ($('#notes_value').val().length === 0) {
        $('#notes_value').val('NOTES');
    }
    if ($("#edit_bottom_section").is(':visible')) {
        $(".notes_heading").show();
        $("#heading_input_field").hide();
    } else {
        $(".notes_heading").hide();
    }
    $('#edit_bottom_section').toggle('show');
});

$(document).on("click", "#organic_edit_note", function(e) {
    e.preventDefault();
    $('#organic_edit_note').hide();
    $('#organic_keyword_edit_section').toggle('show');
});

$(document).on("click", "#goal_edit_note", function(e) {
    e.preventDefault();
    $('#goal_edit_note').hide();
    $('#goal_keyword_edit_section').toggle('show');
});

$(document).on("click", "#backlink_edit_note", function(e) {
    e.preventDefault();
    $('#backlink_edit_note').hide();
    $('#backlink_keyword_edit_section').toggle('show');
});

$(document).on("click", "#monthly_report_note", function(e) {
    e.preventDefault();
    $('#monthly_report_note').hide();
    $('#monthly_report_edit_section').toggle('show');
});

$(document).on("click", ".close-summernote", function(e) {
    e.preventDefault();
    var id    = $(this).attr('data-id');
    var uid    = $(this).attr('data-uid');
    $('#'+id).hide();
    $('#'+uid).show();
});


$(document).on("click", ".close_modal", function(e) {
    e.preventDefault();

    $('#semrush_details').modal('hide');
    location.reload();
});

function loadajax() {

    var request_id = '<?php echo $_REQUEST["id"]; ?>';
    var data_id = '<?php echo $googleAnalytics["property_id"];?>';
    jQuery.ajax({
        type: 'POST',
        async: true,
        url: "assets/ajax/ajax-domain.php",
        data: {
            action: 'update_google_view_id',
            view_id: data_id,
            request_id: request_id
        },
        dataType: 'json',
        success: function(result) {
            var status = result['status'];
            var analytic_id = result['analytic_id'];
            if (status == 'success') {

            }
        }
    });
}
$(function() {
    $("#serpTableRow").find(".is_favorite").eq(-1).after('<tr class="blank-tr"><td colspan="13"></td></tr>');

    setTimeout(loadajax, 5000);
});

$(document).on("click", ".delete_edit_notes", function(e) {
    e.preventDefault();
    var self = $(this);
    var requestId = $(this).data('id');
    var input_id = $(this).data('row');
    var page_id = $(this).data('pageid');
    var data_text = $(this).data('text');
    $.ajax({
        action: 'remove_edit_data',
        type: "POST",
        url: "assets/ajax/ajax-save-text.php",
        data: {
            action: 'remove_edit_data',
            request_id: requestId
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                $("#" + input_id).summernote("reset")
                $("#" + data_text).html('<i class="fa fa-pencil-square-o"></i> Add Summery');
                $("." + page_id).hide();
                self.hide();
                $(this).hide();
                Command: toastr["success"]('Text delete successfully !');
            } else {
                $("." + page_id).hide();
                Command: toastr["error"]('Please try again !');
            }
        }
    });
});

$(document).on("click", "#update_semrush_data", function(e) {
    e.preventDefault();
    var self = $(this);
    var requestId = $(this).data('id');
    var input_id = $(this).data('row');
    $.ajax({
        type: "POST",
        url: "assets/ajax/ajax-update_semrush.php",
        data: {
            action: 'update_semerush_data',
            request_id: requestId,
            input_id: input_id
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {
                location.reload();
            } else {
                Command: toastr["error"]('Please try again !');
            }
        }
    });
});
</script>
<script>
function sendFile(file, editor, welEditable) {
    console.log(file);
    console.log(editor);
    console.log(welEditable);
    data = new FormData();
    data.append("file", file);
    data.append('action', 'save_image');
    $.ajax({
        data: data,
        type: "POST",
        url: "assets/ajax/saveSummernoteImage.php",
        cache: false,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(result) {
            if(result['status'] == 'success')
                //editor.insertImage(welEditable, result['url']);
                $(editor).summernote('insertImage', location.origin+'/'+result['url'], '')
        }
    });
}

function deleteFile(src) {
    $.ajax({
        data: {src : src, action: 'delete_image'},
        type: "POST",
        url: "assets/ajax/saveSummernoteImage.php",
        cache: false,
        success: function(resp) {
            console.log(resp);
        }
    });
}

$(document).on("change", "#analytic_account", function(e) {
    var property_id = $(this).val();
    if (property_id != '') {
        $.ajax({
            type: "POST",
            url: "assets/ajax/ajax-getviewData.php",
            data: {
                action: 'property_data',
                property_id: property_id
            },
            success: function(result) {
                $('#analytic_property').html(result);
            }
        });
    }
});

$(document).on("change", "#analytic_property", function(e) {
    var property_id = $(this).val();
    $.ajax({
        type: "POST",
        url: "assets/ajax/ajax-getviewData.php",
        data: {
            action: 'property_view_data',
            property_id: property_id
        },
        success: function(result) {
            $('#analytic_view_id').html(result);
        }
    });
});

$(document).on("change", "#analytic_view_id", function(e) {
    var analytic_view_id = $(this).val();
    var analytic_property_id = $('#analytic_property').val();
    var analytic_account_id = $('#analytic_account').val();
    var google_account_id = $('#google_account').val();
    var request_id = '<?php echo $_REQUEST["id"]; ?>';
    $.ajax({
        type: "POST",
        url: "assets/ajax/ajax-getviewData.php",
        data: {
            action: 'save_property_id',
            analytic_view_id: analytic_view_id,
            google_account_id: google_account_id,
            analytic_property_id: analytic_property_id,
            analytic_account_id: analytic_account_id,
            request_id: request_id
        },
        success: function(result) {
            var status = result['status'];
            if (status == 'success') {

            } else {}
        }
    });
});

$(function() {
    updateTimeAgo();
    $('#save_view_data').validate({

        rules: {
            analytic_account: "required",
            analytic_property: "required",
            analytic_view_id: "required",
        },
        messages: {
            analytic_account: "Please provide  subject",
            analytic_property: "Please provide email subject",
            analytic_view_id: "Please provide email subject",
        },

    });
    setTimeout(function() {
        $('#chart_2').hide();
        $('#chart_1').hide();
        $('.keyword_hide').show();
        $('#c3chartline').css({
            "opacity": "1"
        });
    }, 7000); //

    setTimeout(updateTimeAgo(), 70000);

});
$(document).on("click", "#submit_button", function(e) {
    e.preventDefault();
    if ($('#save_view_data').valid()) {
        window.location.reload();
    }
});

$('.selectpicker').selectpicker({
    dropupAuto: false
});

$('.selectpicker').on('changed.bs.select', function (e) {
    e.preventDefault();
    var selected = $('option:selected', this).attr("data-country");

    $('input[name="country_code"]').val(selected);
});

</script>
<script>
<?php if(empty($searchConsole['message'])) { ?>
    Highcharts.chart('search_console', {

        chart: {
            scrollablePlotArea: {
                minWidth: 700
            }
        },
        title: {
            text: ''
        },

        subtitle: {
            text: ''
        },

        credits: {
            enabled: false
        },


        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                day: "%e. %b",
                month: "%b '%y",
                year: "%Y"
            },
            //	tickInterval: 7 * 24 * 3600 * 1000, // one week
            tickInterval: 30 * 24 * 3600 * 1000, // one month
            tickWidth: 0,
            gridLineWidth: 1,
            labels: {
                align: 'left',
                x: 3,
                y: -3
            },
            <?php if(!empty($click_plot_line)) { ?>
            plotLines: [{
                color: '#FF0000',
                width: 2,
                value: Date.UTC(<?php echo $click_plot_line; ?>),
                zIndex: 99999,
                label: {
                    text: 'Project Start Here',
                    align: 'left',
                    y: 0,
                    verticalAlign: 'top',
                    rotation: -360
                }

            }],
        <?php } ?>

        },


        yAxis: [{
            title: {
                text: 'Clicks'
            }
        }, {
            title: {
                text: 'Impression'
            },
            gridLineWidth: 0,
            opposite: true
        }],

        legend: {
            align: 'left',
            verticalAlign: 'top',
            borderWidth: 0
        },

        tooltip: {
            shared: true,
            crosshairs: true
        },

        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function(e) {
                            hs.htmlExpand(null, {
                                pageOrigin: {
                                    x: e.pageX || e.clientX,
                                    y: e.pageY || e.clientY
                                },
                                headingText: this.series.name,
                                maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this
                                        .x) +
                                    ':<br/> ' +
                                    this.y + ' sessions',
                                width: 200
                            });
                        }
                    }
                },
                marker: {
                    lineWidth: 1
                }
            }
        },

        series: [{
            color: '#cec937',
            name: 'Clicks',
            data: <?php echo json_encode($clicks); ?>
        }, {
            color: '#439009',
            name: 'Impression',
            data: <?php echo json_encode($impression); ?>,
            yAxis: 1
        }]
    });
<?php } ?>
$(document).on('change', '.toggle_module', function(e) {
    var module = $(this).attr('data-block');
    var data_id_key = '<?php echo $googleAnalytics['property_id'];?>';
    var request_id_key = '<?php echo $_REQUEST["id"]; ?>';
    if ($(this).prop("checked") == true)
        var status = 0;
    else
        var status = 1;

    jQuery.ajax({
        type: 'POST',
        url: "assets/ajax/toggle_module.php",
        data: {
            action: 'toggle_module',
            view_id: data_id_key,
            request_id: request_id_key,
            module: module,
            status: status
        },
        dataType: 'json',
        success: function(result) {}
    });
})

$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {}

    $('#reportrange').daterangepicker({
        "minDate": moment().subtract(1, 'years'),
        "maxDate": moment(),
        startDate: start,
        opens: 'left',
        endDate: end,
        ranges: {}
    }, cb);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

        var begin = picker.startDate.format('MM/DD/YYYY');
        var stop = picker.endDate.format('MM/DD/YYYY');
        var action = $(this).attr('data-module');
        var uid = '<?php echo $_REQUEST['id']; ?>';
        var view_id = '<?php echo $googleAnalytics['property_id']; ?>';
        // AJAX call to our php function which creates the table
        $.ajax({
            url: 'assets/ajax/getDataRange.php',
            type: 'post',
            data: {
                'action': action,
                'start': begin,
                'stop': stop,
                'uid': uid,
                'view_id': view_id,
                'url': '<?php echo $domain_details['domain_url'] ?>',
            },
            dataType: 'json',
            success: function(result) {
                console.log(result['clicks']);
                if (action == 'search_console') {
                    searchConsoleChart(result['clicks'], result['impression']);
                    $('.query_table').html(result['query']);
                    $('.page_table').html(result['page']);
                    $('.country_table').html(result['country']);
                    $('.device_table').html(result['device']);
                } else if (action == 'organic_graph') {
                    if (result['organic_date'] != '') {
                        organicKeywordGrowth(result['organic_date'], result['organic_keywordss']);
                    }
                } else if (action == 'organic_traffice') {
                    console.log(result['current_stats']['users']);
                    $('.new_users').text(result['current_stats']['users']+' vs '+result['combine_stats']['users']);
                    $('.new_session').text(result['current_stats']['sessions']+' vs '+result['combine_stats']['sessions']);
                    $('.new_pageview').text(result['current_stats']['pageview']+' vs '+result['combine_stats']['pageview']);

                    if (result['current_stats']['users'] <= 0 || result['combine_stats']['users'] <= 0) {
                        var total_users = '0% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    } else {
                        var total_users = ((result['current_stats']['users'] - result['combine_stats']['users']) / result['combine_stats']['users'] * 100).toFixed(2) + '%';
                        if(total_users <= 0)
                            total_users+' <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                        else
                            total_users+' <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    }

                    if (result['current_stats']['sessions'] <= 0 || result['combine_stats']['sessions'] <= 0) {
                        var total_session = '0% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                        var session       = '<span id="traffic_growth" class="green">100%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';

                    } else {
                        var total_session = ((result['current_stats']['sessions'] - result['combine_stats']['sessions']) / result['combine_stats']['sessions'] * 100).toFixed(2) + '%';
                        if(total_session <= 0) {
                            total_session+' <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                            var session  = result['current_stats']['sessions']+'<span id="traffic_growth" class="red">'+total_session+'% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';
                        } else {
                            total_session+' <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                            var session  = result['current_stats']['sessions']+'<span id="traffic_growth" class="green">'+total_session+'% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small>';
                        }
                    }

                    if (result['current_stats']['pageview'] <= 0 || result['combine_stats']['pageview'] <= 0) {
                        var total_pageview = '0% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    } else {
                        var total_pageview = ((result['current_stats']['pageview'] - result['combine_stats']['pageview']) / result['combine_stats']['pageview'] * 100).toFixed(2) + '%';
                        if(total_pageview <= 0)
                            total_pageview+' <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                        else
                            total_pageview+' <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    }

                    $('.total_session').html(total_session);
                    $('.total_users').html(total_users);
                    $('.total_pageview').html(total_pageview);
                    $('.ajax_session').html(session);
                    highChartMap(result);
                }
            },
            error: function(xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    });


    $('.reportrange').daterangepicker({
        "minDate": moment().subtract(1, 'years'),
        "maxDate": moment(),
        startDate: start,
        opens: 'left',
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment()
                .subtract(1, 'month').endOf('month')
            ]
        }
    }, cb);

    cb(start, end);

    $('.reportrange').on('apply.daterangepicker', function(ev, picker) {
        var begin = picker.startDate.format('MM/DD/YYYY');
        var stop = picker.endDate.format('MM/DD/YYYY');
        var action = $(this).attr('data-module');
        var uid = '<?php echo $_REQUEST['id']; ?>';
        var view_id = '<?php echo $googleAnalytics['property_id']; ?>';
        // AJAX call to our php function which creates the table
        $.ajax({
            url: 'assets/ajax/getDataRange.php',
            type: 'post',
            data: {
                'action': action,
                'start': begin,
                'stop': stop,
                'uid': uid,
                'view_id': view_id,
                'url': '<?php echo $domain_details['domain_url'] ?>',
            },
            dataType: 'json',
            success: function(result) {
                console.log(result['clicks']);
                if (action == 'search_console') {
                    searchConsoleChart(result['clicks'], result['impression']);
                    $('.query_table').html(result['query']);
                    $('.page_table').html(result['page']);
                    $('.country_table').html(result['country']);
                    $('.device_table').html(result['device']);
                } else if (action == 'organic_graph') {
                    if (result['organic_date'] != '') {
                        organicKeywordGrowth(result['organic_date'], result['organic_keywordss']);
                    }
                } else if (action == 'organic_traffice') {
                    console.log(result['current_stats']['users']);
                    $('.new_users').text(result['current_stats']['users']+' vs '+result['combine_stats']['users']);
                    $('.new_session').text(result['current_stats']['sessions']+' vs '+result['combine_stats']['sessions']);
                    $('.new_pageview').text(result['current_stats']['pageview']+' vs '+result['combine_stats']['pageview']);

                    if (result['current_stats']['users'] <= 0 || result['combine_stats']['users'] <= 0) {
                        var total_users = '0% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    } else {
                        var total_users = ((result['current_stats']['users'] - result['combine_stats']['users']) / result['combine_stats']['users'] * 100).toFixed(2) + '%';
                        if(total_users < 0)
                            total_users =   total_users+' <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                        else
                            total_users =   total_users+' <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    }

                    if (result['current_stats']['sessions'] <= 0 || result['combine_stats']['sessions'] <= 0) {
                        var total_session = '0% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                        var session       = '<h5 class="traffic_growth"><span id="traffic_growth" class="green">100%<i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small></h5>';

                    } else {
                        var total_session = ((result['current_stats']['sessions'] - result['combine_stats']['sessions']) / result['combine_stats']['sessions'] * 100).toFixed(2) + '%';
                        if(total_session < 0) {
                            var session_arrow   =   ' <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                            var session  = '<h5 class="traffic_growth">'+result['current_stats']['sessions']+'<span id="traffic_growth" class="red"> '+total_session+'% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small></h5>';
                        } else {
                            var session_arrow   =   ' <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                            var session  = '<h5 class="traffic_growth">'+result['current_stats']['sessions']+'<span id="traffic_growth" class="green"> '+total_session+'% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i></span><small>Organic Visitors From Google </small></h5>';
                        }

                        total_session  = total_session+session_arrow;
                    }

                    if (result['current_stats']['pageview'] <= 0 || result['combine_stats']['pageview'] <= 0) {
                        var total_pageview = '0% <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';
                    } else {
                        var total_pageview = ((result['current_stats']['pageview'] - result['combine_stats']['pageview']) / result['combine_stats']['pageview'] * 100).toFixed(2) + '%';
                        if(total_pageview < 0)
                            total_pageview  =  total_pageview+' <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>';
                        else
                            total_pageview  =  total_pageview+' <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>';

                    }

                    $('.total_session').html(total_session);
                    $('.total_users').html(total_users);
                    $('.total_pageview').html(total_pageview);

                    highChartMap(result);
                }
            },
            error: function(xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    });
});


function searchConsoleChart(clicks, impression) {
    Highcharts.chart('search_console', {
        chart: {
            scrollablePlotArea: {
                minWidth: 700
            }
        },

        title: {
            text: ''
        },

        subtitle: {
            text: ''
        },

        credits: {
            enabled: false
        },


        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                day: "%e. %b",
                month: "%b '%y",
                year: "%Y"
            },
            //	tickInterval: 7 * 24 * 3600 * 1000, // one week
            tickInterval: 30 * 24 * 3600 * 1000, // one month
            tickWidth: 0,
            gridLineWidth: 1,
            labels: {
                align: 'left',
                x: 3,
                y: -3
            },
            plotLines: [{
                color: '#FF0000',
                width: 2,
                value: Date.UTC(2019,05),
                zIndex: 99999,
                label: {
                    text: 'Project Start Here',
                    align: 'left',
                    y: 0,
                    verticalAlign: 'top',
                    rotation: -360
                }

            }],

        },


        yAxis: [{
            title: {
                text: 'Clicks'
            }
        }, {
            title: {
                text: 'Impression'
            },
            gridLineWidth: 0,
            opposite: true
        }],

        legend: {
            align: 'left',
            verticalAlign: 'top',
            borderWidth: 0
        },

        tooltip: {
            shared: true,
            crosshairs: true
        },

        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function(e) {
                            hs.htmlExpand(null, {
                                pageOrigin: {
                                    x: e.pageX || e.clientX,
                                    y: e.pageY || e.clientY
                                },
                                headingText: this.series.name,
                                maincontentText: Highcharts.dateFormat('%A, %b %e, %Y',
                                        this.x) +
                                    ':<br/> ' +
                                    this.y + ' sessions',
                                width: 200
                            });
                        }
                    }
                },
                marker: {
                    lineWidth: 1
                }
            }
        },

        series: [{
            color: '#cec937',
            name: 'Clicks',
            data: clicks
        }, {
            color: '#439009',
            name: 'Impression',
            data: impression,
            yAxis: 1
        }]
    });

}

$(document).on("change", "#compare_graph", function(e) {
    var chart = $('#chart-1-container').highcharts();
    e.preventDefault();
    var compare_status  =   $(this).is(":checked");
    var compare_value   =   '';
    if(compare_status == true){
        chart.series[1].show();
        compare_value   =   1;
        chart.series[1].update({
            showInLegend: true
        }, true, false);
    }else{
        chart.series[1].hide();
        compare_value   =   0;
        chart.series[1].update({
            showInLegend: false
        }, true, false);
    }
    var request_id      =   '<?php echo $_REQUEST["id"]; ?>';
    jQuery.ajax({
        type: 'POST',
        url: "assets/ajax/ajax_session_response.php",
        data: {
            action: 'compare_status',
            request_id: '<?php echo $_REQUEST["id"]?>',
            compare_status: compare_value
        },
        success: function(result) {
            //Command: toastr["success"]('Update Compare Graph !');
        }
    });
});

$(document).on("click", ".keyword-trash", function(e){
    e.preventDefault();
    var data_id = $(this).attr('data-id');
    var action = 'remove_keyword';
    var self   =   $(this);
    // Do some processing here
    $.ajax({
        type: "POST",
        url: "assets/ajax/serpTracking.php",
        data: {action: action, data_id: data_id},
        dataType: 'json',
        success: function(result) {
            if (result['status'] == '1') {
                Command: toastr["success"]('Keyword Delete Successfully');
                self.closest('tr').remove();
            }
        }
    });
})


$('#serp_track').on('submit', function(e){
    e.preventDefault();
    var new_data    = $(this).serializeArray();
    var d           = new Date();
    var strDate     = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

    var lines       = $('#cities').val().split(/\n/);
    console.log(new_data);
    console.log(new_data[3]["value"]);
    liveTrackingkeyWord(new_data,d, strDate, lines);

});


function getUpdateRow(){
    $.ajax({
        type: "POST",
        url: "assets/ajax/getLatestKeyword.php",
        data: {action: 'update_keywords', request_id: '<?php echo $_REQUEST["id"]; ?>'},
        dataType: 'json',
        success: function(result) {
            var status = result['status'];
            if (status == '1') {
                if(result['html'] !='' ){
                    $("#serpTable tbody").html(result['html']);
                }
            }
            $("#yeskws").hide();
            $("#yeskws_txt").hide();

        }
    });

}


$('#multiplefavorite').on('click', function(e){
    e.preventDefault();
    if(!confirm("Are you sure you want to favorite this?")){
        return false;
    }
    var checked = []
    $("input[name='check_list[]']:checked").each(function ()
    {
        checked.push(parseInt($(this).val()));
    });
    if(checked.length > 0 ){
        $.ajax({
            type: "POST",
            url: "assets/ajax/serpTracking.php",
            data: {action: 'multiple_select', type: 'favorite', selected_ids:checked, request_id: '<?php echo $_REQUEST["id"]; ?>'},
            dataType: 'json',
            success: function(result) {
                var status = result['status'];
                var analytic_id =   result['analytic_id'];

                if (status == '1') {
                    $('.serpTableBody').html(result['html']);
                    $("#serpTableRow").find(".is_favorite").eq(-1).after('<tr class="blank-tr"><td colspan="13"></td></tr>');
                    $('#logo_modal').modal('hide');
                    Command: toastr["success"]('Your keyword favourite successfully');
                } else {
                    Command: toastr["error"]('Please try again getting error');

                }
            }
        });
    }

});

$('#multipleunfavorite').on('click', function(e){
    e.preventDefault();
    if(!confirm("Are you sure you want to unfavorite this?")){
        return false;
    }
    var checked = []
    $("input[name='check_list[]']:checked").each(function () {
        checked.push(parseInt($(this).val()));
    });
    if(checked.length > 0 ){
        $.ajax({
            type: "POST",
            url: "assets/ajax/serpTracking.php",
            data: {action: 'multiple_select', type: 'unfavorite', selected_ids:checked, request_id: '<?php echo $_REQUEST["id"]; ?>'},
            dataType: 'json',
            success: function(result) {
                var status = result['status'];
                var analytic_id =   result['analytic_id'];
                if (status == '1') {
                    $('.serpTableBody').html(result['html']);
                    $("#serpTableRow").find(".is_favorite").eq(-1).after('<tr class="blank-tr"><td colspan="13"></td></tr>');
                    $('#logo_modal').modal('hide');
                    Command: toastr["success"]('Your keyword unfavorite successfully');
                } else {
                    Command: toastr["error"]('Please try again getting error');

                }
            }
        });
    }

});
$('#multipleUpdate').on('click', function(e){
    e.preventDefault();
    var checked = []
    $("input[name='check_list[]']:checked").each(function () {
        checked.push(parseInt($(this).val()));
    });
    if(checked.length > 0 ){
        $.ajax({
            type: "POST",
            url: "assets/ajax/updateTracking.php",
            data: {action: 'multiple_select', type: 'update', selected_ids:checked, request_id: '<?php echo $_REQUEST["id"]; ?>'},
            dataType: 'json',
            success: function(result) {
                getUpdateRow();
                Command: toastr["success"]('Your keyword updated successfully');
            }
        });
    }

});

$('#multipleDelete').on('click', function(e){
    e.preventDefault();
    if(!confirm("Are you sure you want to delete this?")){
        return false;
    }
    var checked = []
    $("input[name='check_list[]']:checked").each(function () {
        checked.push(parseInt($(this).val()));
    });
    if(checked.length > 0 ){
        $.ajax({
            type: "POST",
            url: "assets/ajax/serpTracking.php",
            data: {action: 'multiple_select', type: 'multiDelete', selected_ids:checked, request_id: '<?php echo $_REQUEST["id"]; ?>'},
            dataType: 'json',
            success: function(result) {
                var status = result['status'];
                var analytic_id =   result['analytic_id'];

                if (status == '1') {
                    $('.serpTableBody').html(result['html']);
                    $("#serpTableRow").find(".is_favorite").eq(-1).after('<tr class="blank-tr"><td colspan="13"></td></tr>');
                    $('#logo_modal').modal('hide');
                    Command: toastr["success"]('Your keyword(s) delete successfully');
                } else {
                    Command: toastr["error"]('Please try again getting error');

                }
            }
        });
    }

});


function updateTimeAgo(){
    var request_id      =   '<?php echo $_REQUEST["id"]; ?>';
    jQuery.ajax({
        type: 'POST',
        url: "assets/ajax/serpTracking.php",
        data: {action: 'update_time', request_id:'<?php echo $_REQUEST["id"]; ?>'},
        dataType: 'json',
        success: function(result) {
            if (status == '1') {
                $('#lastupdate').html(result['time']);
            }
        }
    });

}


function compareGraph(chart){
    $(document).on("change", "#compare_graph", function(e) {
        e.preventDefault();
        var compare_status  =   $(this).is(":checked");
        var compare_value   =   '';
        if(compare_status == true){
            chart.series[1].show();
            compare_value   =   1;
            chart.series[1].update({
                showInLegend: true
            }, true, false);
        }else{
            chart.series[1].hide();
            compare_value   =   0;
            chart.series[1].update({
                showInLegend: false
            }, true, false);
        }
        var request_id      =   '<?php echo $_REQUEST["id"]; ?>';
        jQuery.ajax({
            type: 'POST',
            url: "assets/ajax/ajax_session_response.php",
            data: {
                action: 'compare_status',
                request_id: '<?php echo $_REQUEST["id"]?>',
                compare_status: compare_value
            },
            success: function(result) {
                //Command: toastr["success"]('Update Compare Graph !');
            }
        });
    });

}

$('.keywords').tagsinput({
    allowDuplicates: false,
    trimValue: true,
});

$(function() {
    $('.select-picker').selectpicker({ liveSearch: true })
    .ajaxSelectPicker({
        ajax: {
            url: 'assets/ajax/serpTrackingKeywords.php',
            dataType: "json",
            // Use "{{{q}}}" as a placeholder and Ajax Bootstrap Select will
            // automatically replace it with the value of the search query.
            data: {
                q: "{{{q}}}"
            }
        },
        locale: {
            emptyTitle: 'Search for location...'
        },
        preprocessData: function(data){
            var i, l = data.length,
            array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push(
                        $.extend(true, data[i], {
                            text: data[i],
                            value: data[i],
                        })
                    );
                }
            }
            // You must always return a valid array when processing data. The
            // data argument passed is a clone and cannot be modified directly.
            console.log(array);
            return array;
        }
    });

})


$('#serpTableRow .selectall').click(function() {
    if ($("#serpTableRow .selectall").is(':checked')) {
        $('#serpTableRow input[type=checkbox]').prop('checked', true);
    } else {
        $('#serpTableRow input[type=checkbox]').prop('checked', false);
    }
});
</script>
<script>
    $ = jQuery;
    function liveTrackingkeyWord(new_data,d, strDate, lines ) {

        var rows         =  '';
        if(lines != '' || lines != undefined){
        }
        if(lines != '' || lines != undefined){
            $.each(lines, function(i, line){
                rows         =   '<tr><td> <a href="#" target="_blank"><i class="fa fa-search" aria-hidden="true"></i></a> </td><td><figure><a href="#"><figcaption>G</figcaption></a></figure><cite>'+new_data[3]["value"]+'</cite></td><td><img src="https://seoreports.me/assets/scripts/country/flags/'+new_data[2]["value"]+'.png"> <i class="fa fa-map-marker" data-toggle="tooltip" title="" data-original-title="Australia"></i> '+line+' </td><td class="serpTd" data-id="" data-value="">-</td><td></td><td>-</td><td>-</td><td>-</td><td><i class="fa fa-arrow-up"></i>-</td><td>'+new_data[8]["value"]+'</td><td>-</td><td>-</td><td><a href="#" class="" data-id=""></a></td></tr>';
                $('#serpTableRow').DataTable().row.add($(rows).get(0)).draw();
            });
        }

        $("#yeskws_txt").hide();
        $("#yeskws").show();

        $.ajax({
            type: "POST",
            url: "assets/ajax/serpTracking.php",
            data: new_data,
            dataType: 'json',
            success: function(result) {
                var status = result['status'];
                var analytic_id =   result['analytic_id'];
                if (status == '1') {
                    if(result['html'] !='' ){
                        $.noConflict();
                        $('#serpTable').DataTable().clear();
                        $('#serpTable').DataTable().destroy();
//                    $("#serpTable tbody").append(result['html']);
                        $('#serpTableRow').DataTable().row.add($(result['html']).get(0)).draw();
                        getUpdateRow();
                       // setTimeout(updateTimeAgo(), 70000);
                    }
                    Command: toastr["success"]('Your keyword saved successfully');
                } else {
                    Command: toastr["error"]('Please try again getting error');

                }
            }
        });

    }
</script>
<div class="alert edit-error" style="display:none">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">×</span>
    </button>
    <div>Getting Error! to save edit section.</div>
</div>
<div class="alert edit-success" style="display:none">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">×</span>
    </button>
    <div>Well done! text update successfully.</div>
</div>
<style>
/*.table-responsive .panel-body{
	min-width: 1500px;
	}*/
</style>

<?php
	include("includes/nav-footer.php");
?>
<input type="hidden" class="session_new" />
<input type="hidden" class="users_new" />
<input type="hidden" class="pageview_new" />
<input type="hidden" class="session_old" />
<input type="hidden" class="users_old" />
<input type="hidden" class="pageview_old" />
<input type="hidden" class="goal_completions" />



<!-- Modal Footer -->

</div>
</div>
</div>
<!-- Location Modal End-->
