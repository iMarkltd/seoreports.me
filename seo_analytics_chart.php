<?php
	ini_set('memory_limit', '-1');
	require_once("includes/config.php");
	require_once("includes/functions.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once('vendor/autoload.php');
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);
	error_reporting(0);
	ini_set('display_errors', 0);

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
        if(!empty($domainHistoryRange)){
            $domain_history		        =	dateRangeDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db'], $domainHistoryRange['start_date'],$domainHistoryRange['end_date']);
        }else{
            $domain_history		        =	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db']);
        }

        if(!empty($sessionHistoryRange)){
            $session_count		=	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $sessionHistoryRange['start_date'],$sessionHistoryRange['end_date']);

            foreach($session_count as $session_details) {
                $current_session	=	strtotime($session_details['from_date']);
                $from_dates[] 		=  "'".date('M d', strtotime($session_details['from_date']))."'";
                $count_session[]	=  $session_details['total_session'];
            }
        }else{
            $session_count		=	dateRangeSession($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $start_data, $end_data );

            foreach($session_count as $session_details) {
                $current_session	=	strtotime($session_details['from_date']);
                $from_dates[] 		=  "'".date('M d', strtotime($session_details['from_date']))."'";
                $count_session[]	=  $session_details['total_session'];
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
			$google_account		=	googleAccountList();
			$googleAnalytics	=	getDomainDetails($_REQUEST['id']);
			if(!empty($googleAnalytics['id'])) {
				$googleViewSelector	=	getAnalytcsDomainName($_REQUEST['id'], $googleAnalytics['id']);
			}else{
				$googleViewSelector	=	'';
			}
		}else{
			$google_account	=	'';
		}
	}
	$organic_cost = $organic_keywordss = $organic_traffic = $adwords_cost = $adwords_keywords = $adwords_traffic = $rank	=	array();
	$organic_history = '';
	if(!empty($domain_history)) {
		foreach($domain_history as $history) {
			$timeZone 						= 	new DateTimeZone("Asia/Kolkata");
			$dateTime 						= 	\DateTime::createFromFormat('Ymd', $history['date_time'])->format('U') * 1000;
			$organic_keywordss[]	        =	array($dateTime, intval($history['organic_keywords']));
			$organic_date					.=	$dateTime.", ";
		}
		$organic_history			=		$domain_history[0]['organic_keywords'];
    }


	if($checkGoogleGoal['goal_count'] == 0){
		$goal_result	=	"0%";
	}else{
		$goal_result	=	(($checkCurrentGoogleGoal['total'] - $checkGoogleGoal['goal_count']) / $checkGoogleGoal['goal_count'] * 100)."%";
	}


	if(!empty($searchConsole)) {
		$clicks		=	array();
		$impression	=	array();
		foreach($searchConsole['data'] as $analytics)	{
			$clicks[]		=	array(strtotime($analytics->keys[0])*1000, $analytics->clicks);
			$impression[]	=	array(strtotime($analytics->keys[0])*1000, $analytics->impressions);
		}
	}
	require_once("includes/header.php");
?>




<!-- main-container -->
<div class="main-container clearfix">
    <!-- main-navigation -->
    <aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>
        <div class="nav-head">
            <!-- site logo -->
            <a href="home.php" class="site-logo text-uppercase">
                <i class="ion ion-disc"></i>
                <span class="text">Imark </span>
            </a>
        </div>

        <!-- Site nav (vertical) -->
        <?php require_once("includes/nav-sidebar.php"); ?>

    </aside>
    <!-- #end main-navigation -->

    <!-- content-here -->
    <div class="content-container" id="content">
        <div class="page m134 page-charts-c3" ng-controller="c3ChartDemoCtrl">

            <!--
				<ol class="breadcrumb breadcrumb-small">
				<li>Charts</li>
				<li class="active"><a href="charts.c3.html">C3</a></li>
				</ol>


			-->

            <div class="page-wrap">

                <!--
				<div class="about-comp-btn">
                                    <a href="#" data-hover="tooltip" title="Edit"  data-placement="left"><i class="fa fa-pencil"></i></a>
                                </div>
-->

                <!-- row -->
                <div class="row">

                    <!-- New row -->
                    <div class="col-md-12" style="display:none">
                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                            <div class="panel-body custom-top-heading-pd">
                                <div class="row custom-table-pd">
                                    <!-- Line Chart -->
                                    <div class="col-md-6 pd-mb-20">
                                        <h2>Keyword Rank</h2>
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
                                        <h2>Keyword Rank</h2>
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
                        </div>
                    </div>
                    <!-- New row end -->

                    <!-- Line Chart -->
                    <div class="col-md-12">
                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                            <div class="panel-heading inner-dash-heading text-right">
                                <h2 class="text-center performance">Monthly Performance Report -
                                    <?php echo date('F, Y')?><small>(<?php echo $domain_details['domain_url'];?>)</small><br />
                                    <?php
									/* if(!empty($data)) { ?>
                                    <small class="report-genrated">Report Generated on:
                                        <span><?php echo date('d-m-Y', strtotime($data[0]['created']))?><span>
                                    </small>
                                    <?php } */
									?>
                                </h2>

                                <div class="heading-flex">
                                    <h2 class="text-left ">
                                        Domain : <?php echo urlToDomain($domain_details['domain_url']);?>
                                    </h2>

                                    <div class="right-group">
                                        <div class="about-comp-btn pull-right">
                                            <a href="#" data-hover="tooltip" id="monthly_report_note" title=""
                                                data-placement="left" data-original-title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <?php
											if(!empty($checkEditNotes0)) {
										?>
                                            <a href="#" data-hover="tooltip" data-pageid="edit_note_first" title=""
                                                data-row="summernote" data-id="<?php echo $checkEditNotes0['id']; ?>"
                                                class="delete_edit_notes" data-placement="left"
                                                data-original-title="Delete">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </a>
                                            <?php } ?>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="pd-fixed-bar-outer">
                                <div class="pd-fixed-bar">
                                    <div class="pd-bar-data">
                                        <a href="#pdS">
                                            <h5>
                                                <div class="custom-circle"></div>Organic keywords
                                            </h5>
                                            <?php $domain_history		= $domain_history[1]['organic_keywords']; ?>
                                            <?php $organic_keywords	= round((count($data)-$domain_history)/$domain_history*100, 2); ?>
                                            <big><?php echo $organic_history; ?> <span id="traffic_growth"
                                                    class="<?php echo ($organic_keywords < 0 ? 'red' : 'green' ); ?>"><?php echo ($organic_keywords> 0 ? '+' : '' );  echo $organic_keywords."%"; ?></span></big><strong>Keywords</strong>
                                        </a>
                                    </div>
                                    <div class="pd-bar-data">
                                        <a href="#pdF">
                                            <h5>
                                                <div class="custom-circle"></div>Organic traffic growth
                                            </h5>
                                            <big class="traffic_growth">0 <span id="traffic_growth"
                                                    class="green">0%</span></big><strong>Traffic</strong>
                                        </a>
                                    </div>
                                    <div class="pd-bar-data">
                                        <a href="#pdT">
                                            <h5>
                                                <div class="custom-circle"></div>Sample Backlinks
                                            </h5>
                                            <big><?php echo count($backlink_data); ?></big><strong>Links</strong>
                                        </a>
                                    </div>

                                    <div class="pd-bar-data">
                                        <a href="#pdFT">
                                            <h5>
                                                <div class="custom-circle"></div>Google AnaLytics Goals
                                            </h5>
                                            <big class="google_analytics"><?php echo $checkCurrentGoogleGoal['total']; ?>
                                                <span><?php echo  number_format($goal_result, 2, '.', ''); ?></span></big><strong>Goals</strong>
                                        </a>
                                    </div>

                                </div>
                            </div>
                            <div class="blank-link" id="pdF"></div>
                            <div class="panel-body custom-top-heading-pd">
                                <div class="row">
                                    <div class="col-md-12" id="monthly_report_edit_section" style="display:none">
                                        <div id="summernote" class="summernote"></div>
                                        <div class="col-md-12">
                                            <a class="btn btn-success" id="saveAbout">Save</a>
                                        </div>
                                    </div>
                                    <?php
											if(!empty($checkEditNotes0)) {
										?>
                                    <div class="col-md-12">
                                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                                            <div class="panel-body custom-top-heading-pd edit_note_first">
                                                <?php echo $checkEditNotes0['edit_section']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
											}
										?>
                                </div>
                                <div class="row search_console_row">
                                    <div class="col-md-12">
                                        <div class="heading-flex">
                                            <h2>Search Console</h2>
                                            <div class="right-group">
                                                <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10">
                                                    <label class="ui-toggle-inline">
                                                        <input type="checkbox" data-block="search_console" class="toggle_module"
                                                            <?php if(in_array('search_console', $toggle_module)) echo 'checked'; ?>>
                                                        <span></span>
                                                    </label>
                                                </div>

                                                <div class="reportrange" data-module="search_console">
                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                    <span></span> <i class="fa fa-caret-down"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="search_console">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="dynamic-tabs" style="margin-bottom: 30px">
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
                                                                    <?php if(!empty($searchConsole)) {
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
                                                                    <?php if(!empty($searchConsole)) {
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
                                                                    <?php if(!empty($searchConsole)) {
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
                                                                    <?php if(!empty($searchConsole)) {
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
                                        </div>
                                    </div>
                                </div>
                                <div class="row custom-table-pd">
                                    <div class="col-md-6 pd-mb-20">
                                    <div class="panel-heading inner-dash-heading text-right">
                                        <div class="heading-flex">
                                            <h2>Organic Keyword Growth (12 months)</h2>
                                            <div class="right-group">
                                                <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10">
                                                    <label class="ui-toggle-inline">
                                                        <input type="checkbox" data-block="organic_graph"
                                                            class="toggle_module"
                                                            <?php if(in_array('organic_graph', $toggle_module)) echo 'checked'; ?>>
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div id="reportrange" data-module="organic_graph">
                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                    <span></span> <i class="fa fa-caret-down"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                        <?php if(!empty($getMozData)) {?>
                                        <div class="table-data-outer keyword_hide" style="display:none;">
                                            <div class="total_data">
                                                <h5>Page Authority</h5>
                                                <div class="test"><?php echo $getMozData['pageAuthority'].' /100'; ?>
                                                </div>
                                            </div>
                                            <div class="total_data">
                                                <h5>Domain Authority</h5>
                                                <div class="test"><?php echo $getMozData['domainAuthority'].' /100'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <div id="c3chartline" class="keyword_hide" style="opacity:0;"></div>
                                        <div class="chart-loader" id="chart_1"> <img
                                                src="<?php echo 'assets/images/squares.gif'; ?>" /></div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="panel-heading inner-dash-heading text-right">
                                        <div class="heading-flex">
                                            <h2>Organic Traffic Growth (6 months) </h2>
                                            <div class="right-group">
                                                <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10">
                                                    <label class="ui-toggle-inline">
                                                        <input type="checkbox" data-block="organic_traffic"
                                                            class="toggle_module"
                                                            <?php if(in_array('organic_traffic', $toggle_module)) echo 'checked'; ?>>
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <div class="reportrange" data-module="organic_traffice">
                                                    <i class="fa fa-calendar"></i>&nbsp;
                                                    <span></span> <i class="fa fa-caret-down"></i>
                                                </div>
                                            </div>

                                        </div>
                                        </div>

                                        <div class="table-data-outer">
                                            <div class="total_data" style="display:none">
                                                <h5>Sessions</h5>
                                                <small>Organic Traffic</small>
                                                <div class="total_session"></div>
                                                <div class="new_session"></div>
                                            </div>
                                            <div class="total_data" style="display:none">
                                                <h5>Users</h5>
                                                <small>Organic Traffic</small>
                                                <div class="total_users"></div>
                                                <div class="new_users"></div>
                                            </div>
                                            <div class="total_data" style="display:none">
                                                <h5>Pageviews</h5>
                                                <small>Organic Traffic</small>
                                                <div class="total_pageview"></div>
                                                <div class="new_pageview"></div>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>

                                        <div id="chart-1-container" class="chart"></div>
                                        <div class="chart-loader" id="chart_2"> <img src="<?php echo 'assets/images/squares.gif'; ?>" /></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div><!-- #end row -->

                <div class="row">
                    <div class="blank-link" id="pdS"></div>
                    <!-- Data Table -->
                    <div class="col-md-12">
                        <div class="panel panel-default panel-lined table-responsive panel-hovered mb20 data-table keywords-table"
                            style="padding-bottom: 20px">
                            <div class="panel-heading inner-dash-heading text-right">
                                <div class="heading-flex">
                                    <h2>Organic Keywords</h2>
                                    <div class="right-group">
                                        <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10">
                                            <label class="ui-toggle-inline">
                                                <input type="checkbox" data-block="organic_keywords"
                                                    class="toggle_module"
                                                    <?php if(in_array('organic_keywords', $toggle_module)) echo 'checked'; ?>>
                                                <span></span>
                                            </label>
                                        </div>
                                        <div class="about-comp-btn pull-right">
                                            <a href="#" data-hover="tooltip" id="organic_edit_note" title=""
                                                data-placement="left" data-original-title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <?php if(!empty($checkEditNotes1)) { ?>
                                            <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10" style="display:inline-block">
                                                <label class="ui-toggle-inline">
                                                    <input type="checkbox" data-block="organic_keywords_content"
                                                        class="toggle_module"
                                                        <?php if(in_array('organic_keywords_content', $toggle_module)) echo 'checked'; ?>>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <a href="#" data-hover="tooltip" data-pageid="edit_note_second" title=""
                                                data-row="summernote1" data-id="<?php echo $checkEditNotes1['id']; ?>"
                                                class="delete_edit_notes" data-placement="left"
                                                data-original-title="Delete">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </a>
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>

                                            </div>


                                <!-- data table -->
                                <div class="panel-body">

                                    <div class="row">
                                        <div class="col-md-12" id="organic_keyword_edit_section" style="display:none">
                                            <div id="summernote1" class="summernote"></div>
                                            <div class="col-md-12">
                                                <a class="btn btn-success" id="organic_save_note">Save</a>
                                            </div>
                                        </div>


                                        <?php
										if(!empty($checkEditNotes1)) {
									?>
                                        <div class="col-md-12">
                                            <div class="panel panel-default panel-stacked panel-hovered mb30">
                                                <div class="panel-body custom-top-heading-pd edit_note_second">
                                                    <?php echo $checkEditNotes1['edit_section']; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
										}
									?>

                                    </div>



                                    <table id="semrush_organic_table"
                                        class="table table-bordered table-striped table-hover table-condensed dataTable no-footer"
                                        width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Keywords</th>
                                                <th>Current Position</th>
                                                <th>Previous Position</th>
                                                <th>Change</th>
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
                                                <td><?php echo $record['previous_position']?></td>
                                                <td>
                                                    <?php if($record['previous_position'] == 0 ) { ?>
                                                    <i class="fa fa-arrow-up" aria-hidden="true" style="color:green">
                                                        <?php echo 100-$record['position']; ?></i>
                                                    <?php } else if($record['position_difference'] < 0 ) { ?>
                                                    <i class="fa fa-arrow-down" aria-hidden="true" style="color:red">
                                                        <?php echo $record['position_difference']; ?></i>
                                                    <?php } else if($record['position_difference'] > 0) {?>
                                                    <i class="fa fa-arrow-up" aria-hidden="true" style="color:green">
                                                        <?php echo $record['position_difference']; ?></i>
                                                    <?php } ?>
                                                </td>
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
                                    <!-- #end data table -->
                                </div>
                            </div>
                        </div>
                    </div><!-- #end row -->

                    <div class="row">
                        <div class="blank-link" id="pdT"></div>
                        <!-- Data Table -->
                        <div class="col-md-12">
                            <div class="panel panel-default panel-lined table-responsive panel-hovered mb20 data-table"
                                style="padding-bottom: 20px">
                                <div class="panel-heading">
                                    <div class="heading-flex">
                                        <h2>Sample Backlinks</h2>
                                        <div class="right-group">
                                            <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10">
                                                <label class="ui-toggle-inline">
                                                    <input type="checkbox" data-block="organic_backlink"
                                                        class="toggle_module"
                                                        <?php if(in_array('organic_backlink', $toggle_module)) echo 'checked'; ?>>
                                                    <span></span>
                                                </label>
                                            </div>
                                            <div class="about-comp-btn pull-right">
                                                <a href="#" data-hover="tooltip" id="backlink_edit_note" title=""
                                                    data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                <?php
                                                    if(!empty($checkEditNotes2)) {
                                                ?>
                                                <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10" style="display:inline-block">
                                                    <label class="ui-toggle-inline">
                                                        <input type="checkbox" data-block="organic_backlink_content"
                                                            class="toggle_module"
                                                            <?php if(in_array('organic_backlink_content', $toggle_module)) echo 'checked'; ?>>
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_third"
                                                    data-row="summernote2" title=""
                                                    data-id="<?php echo $checkEditNotes2['id']; ?>"
                                                    class="delete_edit_notes" data-placement="left"
                                                    data-original-title="Delete">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- data table -->
                                <div class="panel-body">

                                    <div class="row">

                                        <div class="col-md-12" id="backlink_keyword_edit_section" style="display:none">
                                            <div id="summernote2" class="summernote"></div>
                                            <div class="col-md-12">
                                                <a class="btn btn-success" id="backlink_save_note">Save</a>
                                            </div>
                                        </div>


                                        <?php
										if(!empty($checkEditNotes2)) {
									?>
                                        <div class="col-md-12">
                                            <div class="panel panel-default panel-stacked panel-hovered mb30">
                                                <div class="panel-body custom-top-heading-pd edit_note_third">
                                                    <?php echo $checkEditNotes2['edit_section']; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
										}
									?>

                                    </div>


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
                                                    <div><strong>Source:</strong>
                                                        <?php echo $record['source_url'] ?></div>
                                                    <div><strong>Target:</strong>
                                                        <?php echo $record['target_url'] ?></div>
                                                    <?php /* if(strpos($record['lostlink'],'true') !== false) { ?>
                                                    <div class="ba-table__labels">
                                                        <span data-js-tooltip-name="lostlink"
                                                            class="s-label -danger -xxs ba-widget__tooltip-trigger">
                                                            <span data-test-lostlink="" class="s-label__text"
                                                                data-hover="tooltip"
                                                                title="Lost on <?php echo date('M d, Y', $record['last_seen'])?>. A lost backlink is displayed in the report for 3 months after the date that SEMrush couldn’t find it.">lost</span>
                                                            <div class="ba-widget__tooltip -bottom-left"
                                                                style="margin-left: -7px;">

                                                            </div>
                                                        </span>
                                                    </div>
                                                    <?php } else if($record['newlink'] == 'true') {?>
                                                    <div class="ba-table__labels">
                                                        <span data-js-tooltip-name="newlink"
                                                            class="s-label -success -xxs ba-widget__tooltip-trigger">
                                                            <span data-test-newlink="" class="s-label__text"
                                                                data-hover="tooltip"
                                                                title="Found on <?php echo date('M d, Y', $record['first_seen'])?>. A backlink is considered to be new if less than a month has passed since the date that SEMrush first discovered it.">new</span>
                                                            <div class="ba-widget__tooltip -bottom-left"
                                                                style="margin-left: -7px;">

                                                            </div>
                                                        </span>
                                                    </div>

                                                    <?php } */  ?>
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
                                    <!-- #end data table -->
                                </div>
                            </div>
                        </div>
                    </div><!-- #end row -->
                    <div class="row">
                        <div class="blank-link" id="pdFT"></div>


                        <!-- Google Analytics Goal -->
                        <div class="col-md-12">
                            <div class="panel panel-default panel-stacked panel-hovered mb30">
                                <div class="panel-heading">
                                    <div class="heading-flex">
                                        <h2>Google Analytics Goal Completion</h2>
                                        <div class="right-group">

                                            <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10">
                                                <label class="ui-toggle-inline">
                                                    <input type="checkbox" data-block="goal_completion_analytics"
                                                        class="toggle_module"
                                                        <?php if(in_array('goal_completion_analytics', $toggle_module)) echo 'checked'; ?>>
                                                    <span></span>
                                                </label>
                                            </div>

                                            <div class="about-comp-btn pull-right">
                                                <a href="#" data-hover="tooltip" id="goal_edit_note" title=""
                                                    data-placement="left" data-original-title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <?php
                                                if(!empty($checkEditNotes3)) {
                                            ?>
                                                <div class="ui-toggle ui-toggle-sm ui-toggle-pink mb10" style="display:inline-block">
                                                    <label class="ui-toggle-inline">
                                                        <input type="checkbox" data-block="organic_backlink_content" class="toggle_module"
                                                            <?php if(in_array('organic_backlink_content', $toggle_module)) echo 'checked'; ?>>
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <a href="#" data-hover="tooltip" data-pageid="edit_note_fourth" title=""
                                                    data-row="summernote3" data-id="<?php echo $checkEditNotes3['id']; ?>"
                                                    class="delete_edit_notes" data-placement="left"
                                                    data-original-title="Delete">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">

                                        <div class="col-md-12" id="goal_keyword_edit_section" style="display:none">
                                            <div id="summernote3" class="summernote"></div>
                                            <div class="col-md-12">
                                                <a class="btn btn-success" id="goal_save_note">Save</a>
                                            </div>
                                        </div>


                                        <?php
											if(!empty($checkEditNotes3)) {
										?>
                                        <div class="col-md-12">
                                            <div class="panel panel-default panel-stacked panel-hovered mb30">
                                                <div class="panel-body custom-top-heading-pd edit_note_fourth">
                                                    <?php echo $checkEditNotes3['edit_section']; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
											}
										?>

                                    </div>

                                    <div id="chart-4-container" class="chart"></div>
                                </div>
                            </div>
                        </div>



                        <!-- Notes -->
                        <div class="col-md-12">
                            <div class="panel panel-default panel-stacked panel-hovered mb30 edit-notes-section">
                                <div class="panel-heading">
                                    <h2><span
                                            class="notes_heading"><?php echo ($checkEditNotes4['note_heading'] != '' ? $checkEditNotes4['note_heading'] : 'Notes'); ?></span>
                                        <div id="heading_input_field" style="display:none"> Heading : <input type="text"
                                                name="notes_value" id="notes_value"
                                                value="<?php echo ($checkEditNotes4['note_heading'] != '' ? $checkEditNotes4['note_heading'] : 'Notes'); ?>">
                                        </div>

                                        <div class="about-comp-btn pull-right">
                                            <a href="#" data-hover="tooltip" id="edit_notes" title=""
                                                data-placement="left" data-original-title="Edit">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <?php
											if(!empty($checkEditNotes4)) {
										?>
                                            <a href="#" data-hover="tooltip" data-pageid="edit_note_fifth" title=""
                                                data-row="summernote4" data-id="<?php echo $checkEditNotes4['id']; ?>"
                                                class="delete_edit_notes" data-placement="left"
                                                data-original-title="Delete">
                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                            </a>
                                            <?php } ?>
                                        </div>
                                    </h2>
                                </div>
                                <div class="panel-body" id="edit_bottom_section" style="display:none">
                                    <div id="summernote4" class="summernote"></div>
                                    <div class="col-md-12">
                                        <a class="btn btn-success" id="save_bottom">Save</a>
                                    </div>
                                </div>

                                <?php
									if(!empty($checkEditNotes4)) {
								?>
                                <div class="panel-body edit_note_fifth">
                                    <?php echo $checkEditNotes4['edit_section']; ?>
                                </div>
                                <?php
									}
								?>
                            </div>
                        </div>


                    </div><!-- #end row -->

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
        <link rel="stylesheet" href="assets/styles/jquery.dataTables.min.css">
        </link>
        <link rel="stylesheet"
            href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script src="<?php echo FULL_PATH; ?>assets/scripts/vendors.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/d3.min.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/c3.min.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/screenfull.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/perfect-scrollbar.min.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/waves.min.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/jquery.dataTables.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
        <script src="<?php echo FULL_PATH; ?>assets/scripts/app.js"></script>
        <script src="//code.highcharts.com/highcharts.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js">
        </script>
        <link href="assets/scripts/summernote/summernote.css" rel="stylesheet">
        <script src="assets/scripts/plugins/summernote.min.js"></script>

        <script type="text/javascript">
        $(document).ready(function() {
            $('#semrush_organic_table').DataTable({
                "order": [
                    [1, "asc"]
                ],
                "pageLength": 50,

            });

            $('#semrush_backlink_table').DataTable({
                "pageLength": 25
            });
            jQuery('#summernote').summernote({
                dialogsInBody: true,                
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                focus: true, // set focus to editable area after initializing summernote
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) {
                        sendFile(files[0], editor, welEditable);
                    }
                }

            });

            jQuery('#summernote1').summernote({
                dialogsInBody: true,                
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) {
                        sendFile(files[0], editor, welEditable);
                    }
                }
            });
            jQuery('#summernote2').summernote({
                dialogsInBody: true,                
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) {
                        sendFile(files[0], editor, welEditable);
                    }
                }
            });
            jQuery('#summernote3').summernote({
                dialogsInBody: true,                
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) {
                        sendFile(files[0], editor, welEditable);
                    }
                }
            });
            $('#summernote4').summernote({
                dialogsInBody: true,                
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                callbacks: {
                    onImageUpload: function(files, editor, welEditable) {
                        sendFile(files[0], editor, welEditable);
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
                $('.summernote').eq(0).code(html_text.replace(/\\/g, ''));
            }
            if (html_text1 != null) {
                $('.summernote').eq(1).code(html_text1.replace(/\\/g, ''));
            }
            if (html_text2 !== null && html_text2.length !== 0) {
                //	console.log('backlink');
                //	console.log(html_text2);
                $('.summernote').eq(2).code(html_text2.replace(/\\/g, ''));
            }
            if (html_text3 != null && html_text3.length !== 0) {
                //	console.log(html_text3);
                $('.summernote').eq(3).code(html_text3.replace(/\\/g, ''));
            }
            if (html_text4 != null && html_text4.length !== 0) {
                //	console.log(html_text4);
                $('.summernote').eq(4).code(html_text4.replace(/\\/g, ''));
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
                    //tickInterval: 30 * 24 * 3600 * 1000 // mills in a year.
                    tickPositions: [<?php echo ($organic_date); ?>]
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
            Highcharts.chart('chart-1-container', {
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
                    categories: [<?php echo join($from_dates, ',') ?>],
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
                    data: [<?php echo join($count_session, ','); ?>]
                }]
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
                    tickPositions: organicData
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
            $('#chart1').click(function() {
                $("#chart1").children(".chart").toggle();
            });
            $(".dataTables_paginate").find('a').removeClass("paginate_button");

            $('#key_word').on('click', function(e) {
                e.preventDefault();
                $('#semrush_details').modal();
            });
            $("#semrush_details").on("hidden.bs.modal", function() {
                // put your default event here });
                //window.location.reload();
            });
        });

        $(".dataTables_paginate").find('a').click(function() {
            $(this).removeClass("paginate_button");
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
            var request_id_key = '<?php echo $_REQUEST['id']; ?>';
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

            var data_id_key = '<?php echo $googleAnalytics['property_id'];?>';
            var request_id_key = '<?php echo $_REQUEST['id']; ?>';
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

            var report = new gapi.analytics.report.Data({
                query: {
                    'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>',
                    'metrics': 'ga:sessions, ga:users, ga:pageviews',
                    'segment': 'gaid::-5',
                    'start-date': '365daysAgo',
                    'end-date': 'yesterday'
                }
            });

            var report1 = new gapi.analytics.report.Data({
                query: {
                    'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>',
                    'metrics': 'ga:sessions, ga:users, ga:pageviews',
                    'segment': 'gaid::-5',
                    'start-date': '365daysAgo',
                    'end-date': '182daysAgo'
                }
            });

            report.on('success', function(response) {
                console.log('current');
                console.log(response['rows'][0][0]);
                console.log(response['rows'][0][1]);
                console.log(response['rows'][0][2]);
                $('.session_new').val(response['rows'][0][0]);
                $('.users_new').val(response['rows'][0][1]);
                $('.pageview_new').val(response['rows'][0][2]);
                if ($('.users_new').val() <= 0 || $('.users_old').val() <= 0) {
                    var total_users = "100%";
                } else {
                    var total_users = (($('.users_new').val() - $('.users_old').val()) / $(
                            '.users_old')
                        .val() * 100).toFixed(2) + '%';
                }
                var user_increased = parseInt(total_users);
                if (isNaN(user_increased) || user_increased <= 0) {
                    var traffic_growth_class = 'red';
                } else {
                    var traffic_growth_class = 'green';
                }

                if ($('.session_new').val() <= 0 || $('.session_old').val() <= 0) {
                    var total_session = "100%";
                } else {
                    var total_session = (($('.session_new').val() - $('.session_old').val()) / $(
                        '.session_old').val() * 100).toFixed(2) + '%';
                }
                var session_new = $('.session_new').val() + ' vs ' + $('.session_old').val();
                //console.log(session_new);
                var increased = parseInt(total_session);
                if (isNaN(increased) || increased <= 0) {
                    var traffic_growth_class = 'red';
                } else {
                    var traffic_growth_class = 'green';
                }
                var traffic_growth = $('.session_new').val() +
                    ' <span id="traffic_growth" class="' +
                    traffic_growth_class + '">' + total_session + '</span>';


                if ($('.pageview_new').val() <= 0 || $('.pageview_old').val() <= 0) {
                    var total_pageview = "100%";
                } else {
                    var total_pageview = (($('.pageview_new').val() - $('.pageview_old').val()) / $(
                        '.pageview_old').val() * 100).toFixed(2) + '%';
                }

                var pageview_increased = parseInt(total_pageview);
                if (isNaN(pageview_increased) || pageview_increased <= 0) {
                    var traffic_growth_class = 'red';
                } else {
                    var traffic_growth_class = 'green';
                }

                var pageview_new = $('.pageview_new').val() + ' vs ' + $('.pageview_old').val();


                var session_old = $('.session_old').val();
                var session_new = $('.session_new').val();
                var users_new = $('.users_new').val();
                var users_old = $('.users_old').val();
                var pageview_new = $('.pageview_new').val();
                var pageview_old = $('.pageview_old').val();

                jQuery.ajax({
                    type: 'POST',
                    url: "assets/ajax/ajax_session_response.php",
                    data: {
                        action: 'old_vs_new',
                        view_id: data_id_key,
                        request_id: request_id_key,
                        session_old: session_old,
                        session_new: session_new,
                        total_session: total_session,
                        users_new: users_new,
                        users_old: users_old,
                        total_users: total_users,
                        pageview_new: pageview_new,
                        pageview_old: pageview_old,
                        total_pageview: total_pageview
                    },
                    dataType: 'json',
                    success: function(result) {
                        var status = result['status'];
                        var analytic_id = result['analytic_id'];
                        if (status == 'success') {

                        }
                    }
                });


            });

            report.execute();



            report1.on('success', function(response1) {
                console.log('after');
                console.log(response1['rows'][0][0]);
                console.log(response1['rows'][0][1]);
                console.log(response1['rows'][0][2]);
                //str = JSON.stringify(response, null, 4);
                $('.session_old').val(response1['rows'][0][0]);
                $('.users_old').val(response1['rows'][0][1]);
                $('.pageview_old').val(response1['rows'][0][2]);
                //console.log(str.totalsForAllResults);

            });

            report1.execute(result);

            /*
            	var dataChart3 = new gapi.analytics.googleCharts.DataChart({
            	query: {
            	'ids': 'ga:<?php //echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
            	'start-date': '30daysAgo',
            	'end-date': 'yesterday',
            	'metrics': 'ga:pageviews,ga:uniquePageviews, ga:avgTimeOnPage, ga:entrances, ga:bounceRate, ga:exitRate',
            	'dimensions': 'ga:pagePath'
            	},
            	chart: {
            	'container': 'chart-3-container',
            	'type': 'LINE',
            	'options': {
            	'width': '100%'
            	}
            	}
            	});
            	dataChart3.execute();
            */
            var dataChart4 = new gapi.analytics.googleCharts.DataChart({
                query: {
                    'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'metrics': 'ga:sessions,ga:newUsers, ga:bounceRate, ga:pageviewsPerSession, ga:avgSessionDuration, ga:goalConversionRateAll, ga:goalCompletionsAll, ga:goalValueAll',
                    'dimensions': 'ga:keyword'
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

            dataChart4.on('success', function(response3) {

                var analytics_data = (response3['data']['rows'].length);
                var response = JSON.stringify(response3['data']['rows']);

                if ($('.session_new').val() <= 0 || $('.session_old').val() <= 0) {
                    var total_session = "100%";
                } else {
                    var total_session = (($('.session_new').val() - $('.session_old').val()) / $(
                        '.session_old').val() * 100).toFixed(2) + '%';
                }
                var session_new = $('.session_new').val() + ' vs ' + $('.session_old').val();
                //console.log(session_new);
                var increased = parseInt(total_session);
                if (isNaN(increased) || increased <= 0) {
                    var traffic_growth_class = 'red';
                } else {
                    var traffic_growth_class = 'green';
                }
                var traffic_growth = $('.session_new').val() +
                    ' <span id="traffic_growth" class="' +
                    traffic_growth_class + '">' + total_session + '</span>';
                var analytics_data_class = analytics_data +
                    ' <span id="traffic_growth" class=""></span>';
                $('.total_data').show();
                $('.traffic_growth').html(traffic_growth);
                //$('.google_analytics').html(analytics_data_class);
                $('.new_session').html(session_new);
                $('.total_session').html(total_session);
                $('.total_session').addClass(traffic_growth_class);
                var users_new = $('.users_new').val() + ' vs ' + $('.users_old').val();
                if ($('.users_new').val() <= 0 || $('.users_old').val() <= 0) {
                    var total_users = "100%";
                } else {
                    var total_users = (($('.users_new').val() - $('.users_old').val()) / $(
                            '.users_old')
                        .val() * 100).toFixed(2) + '%';
                }
                var user_increased = parseInt(total_users);
                if (isNaN(user_increased) || user_increased <= 0) {
                    var traffic_growth_class = 'red';
                } else {
                    var traffic_growth_class = 'green';
                }

                $('.new_users').html(users_new);
                $('.total_users').html(total_users);
                $('.total_users').addClass(traffic_growth_class);
                if ($('.pageview_new').val() <= 0 || $('.pageview_old').val() <= 0) {
                    var total_pageview = "100%";
                } else {
                    var total_pageview = (($('.pageview_new').val() - $('.pageview_old').val()) / $(
                        '.pageview_old').val() * 100).toFixed(2) + '%';
                }

                var pageview_increased = parseInt(total_pageview);
                if (isNaN(pageview_increased) || pageview_increased <= 0) {
                    var traffic_growth_class = 'red';
                } else {
                    var traffic_growth_class = 'green';
                }

                var pageview_new = $('.pageview_new').val() + ' vs ' + $('.pageview_old').val();
                $('.new_pageview').html(pageview_new);
                $('.total_pageview').html(total_pageview);
                $('.total_pageview').addClass(traffic_growth_class);

                jQuery.ajax({
                    type: 'POST',
                    url: "assets/ajax/ajax_session_response.php",
                    data: {
                        action: 'session_data',
                        view_id: data_id_key,
                        request_id: request_id_key,
                        rows: response
                    },
                    dataType: 'json',
                    success: function(result) {
                        var goal_count = result['goal_count'];
                        var goal_old = $('.goal_completions').val();
                        var total_google = ((goal_count - goal_old) / goal_old * 100);
                        var increased = parseInt(total_google);
                        if (isNaN(total_google)) {
                            //console.log(total_google);
                            total_google =
                                '<span id="traffic_growth" class="green">0%</span>';
                        } else if (total_google.toFixed(2) == -100) {
                            //console.log(total_google);
                            total_google =
                                '<span id="traffic_growth" class="green">100%</span>';
                        } else {
                            //console.log(total_google);
                            if (isNaN(increased) || increased <= 0) {
                                var google_growth_class = 'red';
                            } else {
                                var google_growth_class = 'green';

                            }
                            total_google = result['goal_count'] +
                                ' <span id="goal_count_old" class="' +
                                google_growth_class +
                                '">' + total_google.toFixed(2) + '% </span>';
                        }
                        //console.log(total_google);
                        $('#goal_count').html(total_google);
                    }
                });

                var session_old = $('.session_old').val();
                var session_new = $('.session_new').val();
                var users_new = $('.users_new').val();
                var users_old = $('.users_old').val();
                var pageview_new = $('.pageview_new').val();
                var pageview_old = $('.pageview_old').val();

                jQuery.ajax({
                    type: 'POST',
                    url: "assets/ajax/ajax_session_response.php",
                    data: {
                        action: 'old_vs_new',
                        view_id: data_id_key,
                        request_id: request_id_key,
                        session_old: session_old,
                        session_new: session_new,
                        total_session: total_session,
                        users_new: users_new,
                        users_old: users_old,
                        total_users: total_users,
                        pageview_new: pageview_new,
                        pageview_old: pageview_old,
                        total_pageview: total_pageview
                    },
                    dataType: 'json',
                    success: function(result) {
                        var status = result['status'];
                        var analytic_id = result['analytic_id'];
                        if (status == 'success') {

                        }
                    }
                });

            });
            dataChart4.execute();

            viewSelector.on('change', function(ids) {
                var data_id = ids;
                var request_id = '<?php echo $_REQUEST['id']; ?>';
                jQuery.ajax({
                    type: 'POST',
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
            });

        });

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
                credits: {
                    enabled: false
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
        </script>

        <?php } ?>
        <script>
        $(document).on("change", ".onchange", function() {
            $('.select_box_loader').show();
            var data_id = $(this).val();
            var request_id = '<?php echo $_REQUEST['id']; ?>';
            jQuery.ajax({
                type: 'POST',
                url: "assets/ajax/ajax_google_view_details.php",
                data: {
                    action: 'update_select_div',
                    request_id: '<?php echo $_REQUEST['id']?>',
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

        /* $(document).on("change", ".onchange", function(){
        	var data_id		=	$(this).val();
        	var request_id	=	'<?php echo $_REQUEST['id']; ?>';
        	jQuery.ajax({
        		type:	'POST',
        		url: 	"assets/ajax/ajax_google_account.php",
        		data:	{action: 'update_google_analytic_id', analytic_id:	data_id, request_id: request_id},
        		dataType: 'json',
        		success: function(result) {
        			var status	=	result['status'];
        			var analytic_id	=	result['analytic_id'];
        			if(status == 'success'){
        				$(".fetch_msg").html("Fetching account information......");
        				window.location.reload();
        			}
        		}
        	});
        });
         */
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
            var sHTML = $('#summernote').summernote('isEmpty') ? '' : $('#summernote').code();
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
                        $('#summernote').eq(0).code('');
                        $('#monthly_report_edit_section').hide();
                        $('.edit-success').addClass('animated bounceInUp');
                        $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-success').removeClass('animated bounceInUp');
                        $('.edit_note_first').html(sHTML);
                        $('#summernote').eq(0).code(sHTML);
                    } else {
                        $('#monthly_report_edit_section').hide();
                        $('.edit-error').addClass('animated bounceInUp');
                        $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-error').removeClass('animated bounceInUp');
                        $('#summernote').eq(0).code(sHTML);
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
            var sHTML = $('#summernote4').summernote('isEmpty') ? '' : $('#summernote4').code();
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
                        $('#summernote4').code('');
                        $('#edit_bottom_section').hide();
                        $('.notes_heading').text(notes);
                        $('#heading_input_field').hide();
                        $('.notes_heading').show();
                        $('.edit-success').addClass('animated bounceInUp');
                        $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-success').removeClass('animated bounceInUp');
                        $('.edit_note_fifth').html(sHTML);
                        $('#summernote4').code(sHTML);
                    } else {
                        $('#edit_bottom_section').hide();
                        $('.notes_heading').text(notes);
                        $('#heading_input_field').hide();
                        $('.notes_heading').show();
                        $('.edit-error').addClass('animated bounceInUp');
                        $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-error').removeClass('animated bounceInUp');
                        $('#summernote4').code(sHTML);
                    }
                }
            });
        });

        $(document).on("click", "#organic_save_note", function() {
            var sHTML = $('#summernote1').summernote('isEmpty') ? '' : $('#summernote1').code();
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
                        $('#summernote1').code('');
                        $('#organic_keyword_edit_section').hide();
                        $('.edit-success').addClass('animated bounceInUp');
                        $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-success').removeClass('animated bounceInUp');
                        $('.edit_note_second').html(sHTML);
                        $('#summernote1').code(sHTML);
                    } else {
                        $('#organic_keyword_edit_section').hide();
                        $('.edit-error').addClass('animated bounceInUp');
                        $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-error').removeClass('animated bounceInUp');
                        $('#summernote1').code(sHTML);
                    }
                }
            });
        });

        $(document).on("click", "#backlink_save_note", function() {
            var sHTML = $('#summernote2').summernote('isEmpty') ? '' : $('#summernote2').code();
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
                        $('#summernote2').code('');
                        $('#backlink_keyword_edit_section').hide();
                        $('.edit-success').addClass('animated bounceInUp');
                        $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-success').removeClass('animated bounceInUp');
                        $('.edit_note_third').html(sHTML);
                        $('#summernote2').code(sHTML);
                    } else {
                        $('#backlink_keyword_edit_section').hide();
                        $('.edit-error').addClass('animated bounceInUp');
                        $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-error').removeClass('animated bounceInUp');
                        $('#summernote2').code(sHTML);
                    }
                }
            });
        });

        $(document).on("click", "#goal_save_note", function() {
            var sHTML = $('#summernote3').summernote('isEmpty') ? '' : $('#summernote3').code();
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
                        $('#summernote3').code('');
                        $('#goal_keyword_edit_section').hide();
                        $('.edit-success').addClass('animated bounceInUp');
                        $(document).find('.edit-success').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-success').removeClass('animated bounceInUp');
                        $('.edit_note_fourth').html(sHTML);
                        $('#summernote3').code(sHTML);
                    } else {
                        $('#goal_keyword_edit_section').hide();
                        $('.edit-error').addClass('animated bounceInUp');
                        $(document).find('.edit-error').fadeIn(300).delay(2500).fadeOut(400);
                        $('.edit-error').removeClass('animated bounceInUp');
                        $('#summernote3').code(sHTML);
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

            $('#organic_keyword_edit_section').toggle('show');
        });

        $(document).on("click", "#goal_edit_note", function(e) {
            e.preventDefault();

            $('#goal_keyword_edit_section').toggle('show');
        });

        $(document).on("click", "#backlink_edit_note", function(e) {
            e.preventDefault();

            $('#backlink_keyword_edit_section').toggle('show');
        });

        $(document).on("click", "#monthly_report_note", function(e) {
            e.preventDefault();

            $('#monthly_report_edit_section').toggle('show');
        });

        $(document).on("click", ".close_modal", function(e) {
            e.preventDefault();

            $('#semrush_details').modal('hide');
            location.reload();
        });

        function loadajax() {

            var request_id = '<?php echo $_REQUEST['id']; ?>';
            var data_id = '<?php echo $googleAnalytics['property_id'];?>';
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
            setTimeout(loadajax, 5000);
        });

        $(document).on("click", ".delete_edit_notes", function(e) {
            e.preventDefault();
            var self = $(this);
            var requestId = $(this).data('id');
            var input_id = $(this).data('row');
            var page_id = $(this).data('pageid');
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
                        $("#" + input_id).code('');
                        $("." + page_id).parent().hide();
                        self.hide();
                        $(this).hide();
                        Command: toastr["success"]('Text delete successfully !');
                    } else {
                        $("." + page_id).parent().hide();
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
            return false;
            data = new FormData();
            data.append("file", file);
            $.ajax({
                data: data,
                type: "POST",
                url: "Your URL POST (php)",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    editor.insertImage(welEditable, url);
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
            var request_id = '<?php echo $_REQUEST['id']; ?>';
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
        </script>
        <script>
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
                }
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
        $(document).on('change', '.toggle_module', function(e) {
            var module = $(this).attr('data-block');
            var data_id_key = '<?php echo $googleAnalytics['property_id'];?>';
            var request_id_key = '<?php echo $_REQUEST['id']; ?>';
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

            function cb(start, end) {
            }

            $('#reportrange').daterangepicker({
                "minDate": moment().subtract(1, 'years'),
                "maxDate": moment(),
                startDate: start,
                endDate: end,
                ranges: {
                }
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
                            if(result['organic_date'] != ''){
                                organicKeywordGrowth(result['organic_date'], result['organic_keywordss']);
                            }
                        } else if (action == 'organic_traffice') {
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
                            if(result['organic_date'] != ''){
                                organicKeywordGrowth(result['organic_date'], result['organic_keywordss']);
                            }
                        } else if (action == 'organic_traffice') {
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
                    }
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
	include("includes/footer.php");
?>
        <input type="hidden" class="session_new" />
        <input type="hidden" class="users_new" />
        <input type="hidden" class="pageview_new" />
        <input type="hidden" class="session_old" />
        <input type="hidden" class="users_old" />
        <input type="hidden" class="pageview_old" />
        <input type="hidden" class="goal_completions" />
        <div class="modal fade" id="semrush_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" style="height:400px !important">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h1 class="Titles-main" id="view-name">Please choose your account</h1>
                        <?php if(!empty($google_account)) { ?>
                        <div class="add-new-acc-btn">
                            <a href="<?php echo FULL_PATH ?>/auth_analytics.php?ids=<?php echo $_REQUEST['id']?>">Add
                                New
                                Account </a>
                        </div>
                        <div class="select-view-cover">
                            <!--<h1 class="Titles-main" id="view-name">Select a View</h1>
					<div id="view-selector" class="chart"></div>-->

                            <select class="select onchange selectpicker" id="google_account" data-live-search="true"
                                data-dropup-auto="false">
                                <optgroup>
                                    <option value="">Please Select</option>
                                    <?php
								foreach($google_account as $account) {
							?>
                                    <option value="<?php echo $account['id'];?>"
                                        <?php if($account['id'] == $googleAnalytics['id']) echo 'selected'; ?>>
                                        <?php echo $account['email'] ?></option>
                                    <?php }?>
                                </optgroup>
                            </select>
                        </div>
                        <?php } else {?>
                        <a href="<?php echo FULL_PATH ?>/auth_google.php?ids=<?php echo $_REQUEST['id'];?>">Click
                            here to
                            Authorize of Google Anayltics Account </a>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="select-view-cover">
                            <h1 class="Titles-main" id="view-name">Select a View</h1>
                            <form name="save_view_data" id="save_view_data" method="post">
                                <div id="view-selector" class="chart">
                                    <select name="analytic_account" id="analytic_account" class="selectpicker"
                                        data-live-search="true" data-dropup-auto="false"
                                        data-id="<?php echo $_REQUEST['id']; ?>">
                                        <option value="">
                                            <--select account-->
                                        </option>
                                        <?php foreach($googleViewSelector as $viewSelector) { ?>
                                        <option value="<?php echo $viewSelector['id']?>" <?php
										if(!empty($googleAnalytics['id'])) {
											echo ($googleAnalytics['google_analytics_id'] == $viewSelector['id'] ? 'selected' : '');
										}
									?>> <?php echo $viewSelector['category_name']; ?></option>
                                        <?php }?>
                                    </select>
                                    <select name="analytic_property" id="analytic_property">
                                        <option value="">
                                            <--select property-->
                                        </option>
                                        <?php
								if(!empty($googleAnalytics['google_property_id'])) {
									$getPropertyFields		=	getGoogleAnalyticsProperty($googleAnalytics['google_analytics_id']);
									foreach($getPropertyFields as $property_field) {
							?>
                                        <option value="<?php echo $property_field['id']?>"
                                            <?php echo ($googleAnalytics['google_property_id'] == $property_field['id'] ? 'selected' : ''); ?>>
                                            <?php echo $property_field['category_name'];?></option>
                                        <?php
									}

								}

							?>
                                    </select>
                                    <select name="analytic_view_id" id="analytic_view_id">
                                        <option value="">
                                            <--select view id-->
                                        </option>
                                        <?php
								if(!empty($googleAnalytics['google_property_id'])) {
									$getPropertyProfileId		=	getGoogleAnalyticsProperty($googleAnalytics['google_property_id']);
									foreach($getPropertyProfileId as $profile_field) {
							?>
                                        <option value="<?php echo $profile_field['category_id']?>"
                                            <?php echo ($googleAnalytics['property_id'] == $profile_field['category_id'] ? 'selected' : ''); ?>>
                                            <?php echo $profile_field['category_name'];?></option>
                                        <?php
									}

								}

							?>
                                    </select>

                                </div>
                                <button type="submit" id="submit_button" value="Save">Save</button>
                            </form>
                        </div>
                        <div class="select_box_loader" style="display:none;">
                            <div class="loader-msg">Please wait it will take maximum 60 seconds</div><img
                                src="<?php echo 'assets/images/squares.gif'; ?>" />
                        </div>
                    </div>

                    <!-- Modal Footer -->

                </div>
            </div>
        </div>


        <!-- Modal Footer -->

    </div>
</div>
</div>
<!-- Location Modal End-->