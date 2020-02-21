<?php
	ini_set('memory_limit', '-1');

	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("includes/header.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once 'vendor/autoload.php';
	error_reporting(0);
	ini_set('display_errors', 0);
	global $DBcon;

	$token 			= 	$_REQUEST['token_id'];
	$domain_details	=	getUserDomainDetailsByToken($token);
	$data			=	'';
	$graph_data		=	array();
	if(!empty($domain_details)){
		$client 			= new Google_Client();
		$profile_info		=	getProfileData($domain_details['id'], $domain_details['user_id']);
		$data				=	checkSemarshApiData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db']);
		$backlink_data		=	checkSemarshBacklinkData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id']);
		$domain_history		=	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db']);
		$googleAnalytics	=	googleAnalyticsWithoutLogin($domain_details['user_id'], $domain_details['id']);
		$session_count		=	googleAnalyticsSession($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$get_old_month		=	strtotime($session_count[0]['created']);
		$profile_data		=	googleAnalyticsProfileData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$profile_old_data	=	googleAnalyticsProfileOldData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		if($get_old_month < strtotime('1 month ago')){
			$googleAnalytics	=	getDomainDetails($domain_details['id']);
		}
		$checkEditNotes0	=	checkEditNotes0($domain_details['user_id'], $domain_details['id']);
		$checkEditNotes1	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '1');
		$checkEditNotes2	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '2');
		$checkEditNotes3	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '3');
		$checkEditNotes4	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '4');
		$checkGoogleGoal	=	checkGoogleGoal($domain_details['user_id'], $domain_details['id']);
		$checkCurrentGoogleGoal	=	checkCurrentGoogleGoal($domain_details['user_id'], $domain_details['id']);
		$getMozData				    =	checkMozData($domain_details['domain_url'], $domain_details['id']);
		$toggle_module			=	getToggleModule($domain_details['id']);
	}else{
		header("Location:404.php");
	}


	$organic_cost = $organic_keywordss = $organic_traffic = $adwords_cost = $adwords_keywords = $adwords_traffic = $rank	=	array();
	$organic_history = '';

	if(!empty($domain_history)) {
		foreach($domain_history as $history) {
			$timeZone 						= 	new DateTimeZone("Asia/Kolkata");
			$dateTime 						= 	\DateTime::createFromFormat('Ymd', $history['date_time'])->format('U') * 1000;
			$organic_keywordss[]	=		array($dateTime, intval($history['organic_keywords']));
		}
		$organic_history			=		$domain_history[0]['organic_keywords'];
	}	


	
	foreach($session_count as $session_details) { 
		$current_session	=	strtotime($session_details['from_date']);
			$from_dates[] 		=  "'".date('M d', strtotime($session_details['from_date']))."'";
			$count_session[]	=  $session_details['total_session'];

	}
	
?>

<!-- main-container -->
<div class="main-container clearfix">
    <!-- main-navigation -->
    <aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>
        <div class="nav-head">
            <!-- site logo -->
            <a href="index.html" class="site-logo text-uppercase">
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
                <?php if(!empty($profile_info)) { ?>
                <div class="client-share-info">
                    <div class="pull-left">
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
                    <div class="pull-right">
                        <a href="tel:<?php echo @$profile_info['contact_no']?>"><i class="fa fa-phone"
                                aria-hidden="true"></i> <?php echo @$profile_info['contact_no']?></a> <a
                            href="mailto:<?php echo @$profile_info['email']?>"><i class="fa fa-envelope"
                                aria-hidden="true"></i> <?php echo @$profile_info['email']?></a>
                    </div>
                </div>
                <?php } ?>



                <div class="pd-fixed-bar-outer">
                    <div class="pd-fixed-bar" data-step="1"
                        data-intro="This area shows important statistics like growth in Google traffic, number of keywords, backlinks and goals completed!">
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
                                <?php $traffic_growth	= round(($profile_old_data['sessions_new']-$profile_old_data['sessions_old'])/$profile_old_data['sessions_old']*100, 2); ?>
                                <big class="traffic_growth"><?php echo $profile_old_data['sessions_new']; ?> <span
                                        id="traffic_growth"
                                        class="<?php echo ($traffic_growth < 0 ? 'red' : 'green' ); ?>"><?php echo ($traffic_growth <= 0 ? '100%' : $traffic_growth."%" ); ?></span></big><strong>Traffic</strong>
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




                <!-- row -->
                <div class="row">
                    <!-- Line Chart -->
                    <div class="col-md-12">
                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                            <div class="panel-heading inner-dash-heading">
                                <h2 class="text-center performance">Monthly Performance Report -
                                    <?php echo date('F, Y')?><small>(<?php echo $domain_details['domain_url'];?>)</small><br />
                                </h2>
                                <h2 class="text-left ">Domain : <?php echo urlToDomain($domain_details['domain_url']);?>
                                </h2>
                            </div>



                            <div class="blank-link" id="pdF"></div>
                            <div class="panel-body custom-top-heading-pd">
                                <div class="row">

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



                                <!--
								<div class="row">
									<div class="col-md-6 pd-mb-20">
										
									</div>
									
									<div class="col-md-6">
										
										
									</div>
								</div>
-->

                                <div class="row ">
                                    <div class="col-md-12 pd-add-pt" data-step="2"
                                        data-intro="This is how the number of keywords you are ranking for has been growing. More keywords leads to more traffic">
										<?php if(!in_array('organic_graph', $toggle_module)) {?>
                                        <h2>Organic Keyword Growth (12 months)</h2>

                                        <?php if(!empty($getMozData)) {?>
                                        <div class="table-data-outer">
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

                                        <div id="c3chartline"></div>
										<?php } ?>
                                    </div>
                                    <div class="col-md-12" data-step="3"
                                        data-intro="This is how your traffic is growing in last 6 months">
										<?php if(!in_array('organic_traffic', $toggle_module)) {?>

                                        <h2>Organic Traffic Growth (6 months)</h2>
                                        <div class="table-data-outer">
                                            <div class="total_data" style="">
                                                <h5>Sessions</h5>
                                                <small>Organic Traffic</small>
                                                <?php 
														if($profile_old_data['sessions_total'] == "Infinity%" || $profile_old_data['sessions_total'] == "0%") $profile_session_total = "100"; else $profile_session_total = $profile_old_data['sessions_total']; 
												?>
                                                <div
                                                    class="total_session <?php if($profile_session_total < 0) echo 'red'; ?>">
                                                    <?php if($profile_old_data['sessions_total'] == "Infinity%" || $profile_old_data['sessions_total'] == "0%") echo "100%"; else echo $profile_old_data['sessions_total'];?>
                                                </div>
                                                <div class="new_session">
                                                    <?php echo ($profile_old_data['sessions_new'] <=0 ? '0' : $profile_old_data['sessions_new'] ).' vs '.($profile_old_data['sessions_old'] <=0 ? '0' : $profile_old_data['sessions_old'] ); ?>
                                                </div>
                                            </div>
                                            <div class="total_data" style="">
                                                <h5>Users</h5>
                                                <small>Organic Traffic</small>
                                                <?php 
														if($profile_old_data['users_total'] == "Infinity%" || $profile_old_data['users_total'] == "0%") $profile_user_total = "100"; else $profile_user_total = $profile_old_data['users_total']; 
												?>
                                                <div
                                                    class="total_users <?php if($profile_user_total < 0) echo "red"; ?>">
                                                    <?php if($profile_old_data['users_total'] == "Infinity%" || $profile_old_data['users_total'] == "0%") echo "100%"; else echo $profile_old_data['users_total']; ?>
                                                </div>
                                                <div class="new_users">
                                                    <?php echo ($profile_old_data['users_new'] <=0 ? '0' : $profile_old_data['users_new'] ).' vs '.($profile_old_data['users_old'] <=0 ? '0' : $profile_old_data['users_old'] ); ?>
                                                </div>
                                            </div>
                                            <div class="total_data" style="">
                                                <h5>Pageviews</h5>
                                                <small>Organic Traffic</small>
                                                <?php 
														if($profile_old_data['pageviews_total'] == "Infinity%" || $profile_old_data['pageviews_total'] == "0%") $profile_pageview_total = "100"; else $profile_pageview_total = $profile_old_data['pageviews_total']; 
												?>
                                                <div
                                                    class="total_pageview <?php if($profile_pageview_total < 0 ) echo 'red'; ?>">
                                                    <?php if($profile_old_data['pageviews_total'] == "Infinity%" || $profile_old_data['pageviews_total'] == "0%") echo "100%"; else  echo $profile_old_data['pageviews_total']?>
                                                </div>
                                                <div class="new_pageview">
                                                    <?php echo ($profile_old_data['pageviews_old'] <=0 ? '0' : $profile_old_data['pageviews_old'] ).' vs '.($profile_old_data['pageviews_new'] <=0 ? '0' : $profile_old_data['pageviews_new'] ); ?>
                                                </div>
                                            </div>
                                        </div>


                                        <div id="chart-1-container" class="chart"></div>
										<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- #end row -->
                <div class="row" data-step="4"
                    data-intro="These are keywords you are ranking for and some other important metrics">
                    <div class="blank-link" id="pdS"></div>
                    <!-- Data Table -->
                    <div class="col-md-12">
                        <div class="panel panel-default panel-lined table-responsive panel-hovered mb20 data-table keywords-table"
                            style="padding-bottom: 20px">
                            <div class="panel-heading">
                                <h2>Organic Keywords</h2>


                            </div>
                            <!-- data table -->
                            <div class="panel-body">

                                <div class="row">

                                    <?php 
											if(!empty($checkEditNotes1)) {
										?>
                                    <div class="col-md-12">
                                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                                            <div class="panel-body custom-top-heading-pd edit_note_first">
                                                <?php echo $checkEditNotes1['edit_section']; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php
											}
										?>
                                </div>
								<?php if(!in_array('organic_keywords', $toggle_module)) {?>

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
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div><!-- #end row -->
                <div class="row" data-step="5" data-intro="These are your recent 10 backlinks.">
                    <div class="blank-link" id="pdT"></div>
                    <!-- Data Table -->
                    <div class="col-md-12">
                        <div class="panel panel-default panel-lined table-responsive panel-hovered mb20 data-table"
                            style="padding-bottom: 20px">
                            <div class="panel-heading">
                                <h2>Sample Backlinks</h2>
                            </div>
                            <!-- data table -->
                            <div class="panel-body">

                                <div class="row">
                                    <?php if(!empty($checkEditNotes2)) { ?>
                                    <div class="col-md-12">
                                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                                            <div class="panel-body custom-top-heading-pd edit_note_first">
                                                <?php echo $checkEditNotes2['edit_section']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
								<?php if(!in_array('organic_backlink', $toggle_module)) {?>
								<table id="semrush_backlink_table" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer" width="100%" cellspacing="0">
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
                                <!-- #end data table -->
								<?php  } ?>
                            </div>
                        </div>
                    </div>
                </div><!-- #end row -->
                <div class="row">
                    <div class="blank-link" id="pdFT"></div>

                    <!-- Line Chart -->
                    <div class="col-md-12" data-step="6"
                        data-intro="This area shows how many goals has been completed in Google analytics, don't worry if you haven't set goals as off now, we will do it for you.">
                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                            <div class="panel-heading">
                                <h2>Google Analytics Goal Completion</h2>
                            </div>
                            <div class="panel-body">

                                <div class="row">
                                    <?php 
											if(!empty($checkEditNotes3)) {
										?>
                                    <div class="col-md-12">
                                        <div class="panel panel-default panel-stacked panel-hovered mb30">
                                            <div class="panel-body custom-top-heading-pd edit_note_first">
                                                <?php echo $checkEditNotes3['edit_section']; ?>

                                            </div>
                                        </div>
                                    </div>
                                    <?php
											}
										?>

                                </div>

								<?php if(!in_array('goal_completion_analytics', $toggle_module)) {?>

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
                                    <tbody>
                                        <?php
												if(!empty($profile_data)) {
													foreach($profile_data as $profile) {
													?>
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
                                        <?php }
												}
											?>
                                    </tbody>
                                </table>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- Notes -->
                    <!--
						<div class="col-md-12">
							<?php 
								//if(!empty($checkEditNotes1)) {
							?>
								<div class="panel-body edit_note_second"><?php //echo $checkEditNotes1['edit_section']; ?></div>
							<?php
								//}
							?>
						</div>	
-->




                    <?php 
					if(!empty($checkEditNotes4)) {
					?>
                    <div class="col-md-12">
                        <div class="panel panel-default panel-stacked panel-hovered mb30 edit-notes-section">
                            <div class="panel-heading">
                                <h2>Notes


                                </h2>

                            </div>


                            <div class="panel-body edit_note_second"><?php echo $checkEditNotes4['edit_section']; ?>
                            </div>

                        </div>
                    </div>
                    <?php
						}
					?>
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
    <link rel="stylesheet" href="assets/styles/jquery.dataTables.min.css">
    </link>
    <link rel="stylesheet" href="assets/styles/introjs.css">
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
                type: 'datetime',
                labels: {
                    formatter: function() {
                        //							return Highcharts.dateFormat('%a %d %b', this.value);
                        return Highcharts.dateFormat('%d %b', this.value);

                    }
                },
                //tickInterval: 30 * 24 * 3600 * 1000 // mills in a year. 
                tickInterval: 86400000 * 30

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
        <?php  if(empty($googleAnalytics['access_token'])) {?>
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
            window.location.reload();
        });
    });

    $(".dataTables_paginate").find('a').click(function() {
        $(this).removeClass("paginate_button");
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

        var dataChart1 = new gapi.analytics.report.Data({
            query: {
                'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
                'start-date': '180daysAgo',
                'end-date': 'yesterday',
                'metrics': 'ga:sessions',
                'dimensions': 'ga:date',
                'segment': 'gaid::-5'
            },

        });

        var data_id_key = '<?php echo $googleAnalytics['property_id'];?>';
        var request_id_key = '<?php echo $domain_details['id']; ?>';
        var response = [];
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

        var dataChart2 = new gapi.analytics.report.Data({
            query: {
                'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>', // The Demos & Tools website view.
                'start-date': '360daysAgo',
                'end-date': '180daysAgo',
                'metrics': 'ga:goalCompletionsAll',
                'dimensions': 'ga:keyword'
            },
        });

        var data_id_key = '<?php echo $googleAnalytics['property_id'];?>';
        var request_id_key = '<?php echo $domain_details['id']; ?>';
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
                'start-date': '180daysAgo',
                'end-date': 'yesterday'
            }
        });

        var report1 = new gapi.analytics.report.Data({
            query: {
                'ids': 'ga:<?php echo $googleAnalytics['property_id'];?>',
                'metrics': 'ga:sessions, ga:users, ga:pageviews',
                'segment': 'gaid::-5',
                'start-date': '360daysAgo',
                'end-date': '180daysAgo'
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
                var total_users = (($('.users_new').val() - $('.users_old').val()) / $('.users_old')
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
            var traffic_growth = $('.session_new').val() + ' <span id="traffic_growth" class="' +
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
            var traffic_growth = $('.session_new').val() + ' <span id="traffic_growth" class="' +
                traffic_growth_class + '">' + total_session + '</span>';
            var analytics_data_class = analytics_data + ' <span id="traffic_growth" class=""></span>';
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
                var total_users = (($('.users_new').val() - $('.users_old').val()) / $('.users_old')
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
                        total_google = '<span id="traffic_growth" class="green">0%</span>';
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
                            ' <span id="goal_count_old" class="' + google_growth_class +
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
            var request_id = '<?php echo $domain_details['id']; ?>';
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
    <style>
    /*.table-responsive .panel-body{
	min-width: 1500px;
	}*/

    #c3chartline,
    div#chart-1-container {
        clear: both;
    }
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
    <!-- Location Modal End-->
    <script>
    function startTour() {
        var tour = introJs()
        tour.setOption('tooltipPosition', 'auto');
        tour.setOption('positionPrecedence', ['left', 'right', 'bottom', 'top'])
        tour.start()
    }
    </script>