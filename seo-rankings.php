<?php
	ini_set('memory_limit', '-1');

	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("includes/report-header.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once 'vendor/autoload.php';
	error_reporting(0);
    ini_set('display_errors', 0);
//	error_reporting(E_ALL);
//	ini_set('display_errors', 1);

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
        $domainHistoryRange =   getModuleDateRange($domain_details['id'], 'organic_graph');
        $searchDateRange    =   getModuleDateRange($domain_details['id'], 'search_console');
        $getSerpValue       =   getSerpValue($domain_details['id']);

        $start_data                 =   date('Y-m-d', strtotime('-1 year'));
        $end_data                   =   date('Y-m-d');
        if(!empty($searchDateRange)){
            //print_r($searchDateRange); exit;
            $start_data                 =   date('Y-m-d', strtotime($searchDateRange['start_date']));
            $end_data                   =   date('Y-m-d', strtotime($searchDateRange['end_date']));
        }

        $searchConsole				=	googleSearchConsole($domain_details['id'], $domain_details['domain_url'], $domain_details['google_account_id'], $start_data, $end_data);
        $toggle_module				=	getToggleModule($domain_details['id']);
//		print_r($searchConsole); exit;
//		print_r($getMozData); exit;
		if($checkGoogle){
			if(!empty($googleAnalytics['id']) && $googleAnalytics['id'] != 0) {
				$googleViewSelector	=	getAnalytcsDomainName($_REQUEST['id'], $googleAnalytics['id']);
			}else{
				$googleViewSelector	=	'';
			}
		}

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




        if(!empty($domainHistoryRange)){
            $domain_history	    =	dateRangeDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db'], $domainHistoryRange['start_date'],$domainHistoryRange['end_date']);
        }else{
            $domain_history		=	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db']);
        }
        

	}else{
		header("Location:404.php");
	}


	$organic_cost = $organic_keywordss = $organic_traffic = $adwords_cost = $adwords_keywords = $adwords_traffic = $rank	=	array();
	$organic_history = '';

	if(!empty($domain_history)) {
        if(!empty($domain_details['domain_register'])){
            $plot_month     =   date('Y,m', strtotime($domain_details['domain_register']));
        }
		foreach($domain_history as $history) {
			$timeZone 				= 	new DateTimeZone("Asia/Kolkata");
			$dateTime 				= 	\DateTime::createFromFormat('Ymd', $history['date_time'])->format('U') * 1000;
			$organic_keywordss[]	=	array($dateTime, intval($history['organic_keywords']));
			$organic_date		    .=	$dateTime.", ";
            $organic_plot[]         =   date('Y,m', strtotime($history['date_time']));
		}
        $organic_history			=		$domain_history[0]['organic_keywords'];

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



	foreach($session_count as $session_details) {
		$current_session	=	strtotime($session_details['from_date']);
			$from_dates[] 		=  "'".date('M d', strtotime($session_details['from_date']))."'";
			$count_session[]	=  $session_details['total_session'];

    }
    $toggle_module			=	getToggleModule($domain_details['id']);
    if(empty($toggle_module))
        $toggle_module      =   array();

    $getTop10Keywords    =   getPositionData($domain_details['id'], 1, 10);
    $getTop20Keywords    =   getPositionData($domain_details['id'], 1, 20);
    $getTop100Keywords   =   getPositionData($domain_details['id'], 1, 100);
    $improvedKeywords    =   getImprovedKeywords($domain_details['id'], 1, 100);

?>

<!-- main-container -->
<div class="main-container report-page clearfix">

    <?php include("includes/ranking-nav-sidebar.php") ?>

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
                    <a href="tel:<?php echo @$profile_info['contact_no']?>"><i class="fa fa-phone"
                            aria-hidden="true"></i> <?php echo @$profile_info['contact_no']?></a> <a
                        href="mailto:<?php echo @$profile_info['email']?>"><i class="fa fa-envelope"
                            aria-hidden="true"></i> <?php echo @$profile_info['email']?></a>
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

                    <div class="col-md-12 four-report-box three-report-box single-line">
                        <div class="report-box purple text-center">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5>
                                <?php echo $getTop100Keywords['total']; ?>
                                <small>Total Keywords In Google</small>
                            </h5>
                        </div>

                        <div class="report-box green text-center">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5>
                                <?php echo $getTop10Keywords['total']; ?>
                                <small>Total Keywords On Page 1 In Google</small>
                            </h5>

                        </div>

                        <div class="report-box pink text-center">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5>
                                <?php echo $getTop20Keywords['total']; ?>
                                <small>Total Keywords On Page 2 In Google</small>
                            </h5>

                        </div>
                        <div class="report-box yellow text-center">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5><?php echo $improvedKeywords['total']; ?> <small>Keywords Improved In Google</small></h5>

                        </div>

                    </div>


                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php if(!in_array('organic_graph', $toggle_module) || !in_array('search_console', $toggle_module)) {?>
                            <div class="report-box">
                                <div class="row">
                                    <?php if(!in_array('organic_graph', $toggle_module)) {
                                            $colm   =   (!in_array('search_console', $toggle_module) ? 'col-md-6' : 'col-md-12' );
                                    ?>
                                        <div class="<?php echo $colm; ?>">
                                            <h5>Organic Keyword Growth (12 months)</h5>
                                            <div id="c3chartline"></div>
                                        </div>
                                    <?php } ?>
                                    <?php if(!in_array('search_console', $toggle_module)) {
                                            $colm1   =   (!in_array('organic_graph', $toggle_module) ? 'col-md-6' : 'col-md-12' );
                                    ?>
                                            <div class="<?php echo $colm1; ?>">
                                                <h5>Visibility In Google</h5>
                                            <?php if(empty($searchConsole['message'])) { ?>
                                                <div id="search_console"></div>
                                            <?php } else { ?>
                                                <div class="search-console-error-msg"><p><?php echo $searchConsole['message']; ?></p></div>
                                            <?php } ?>
                                            </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if(!in_array('serp_ranking_table', $toggle_module)) { ?>
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
                        <?php } ?>
                        <?php
                            if(!in_array('organic_keywords_summary', $toggle_module) || !in_array('organic_keywords_table', $toggle_module)) {
                        ?>

                            <div class="report-box">
                                <div class="report-box-head">
                                    <h5>Organic Keywords</h5>

                                    <div class="report-box-button">
                                        <ul class="nav nav-tabs">
                                            <?php  if(!in_array('organic_keywords', $toggle_module)) {
                                                        $colm2   =   (!in_array('search_console', $toggle_module) ? 'active' : '' );
                                            ?>
                                                <li class="<?php echo $colm2; ?>"><a data-toggle="tab" href="#reportTab1"><figure><img src="/assets/images/semrush-logo.png"></figure> SEMrush</a></li>
                                            <?php }  if(!in_array('search_console', $toggle_module)) {
                                                        $colm3   =   (!in_array('organic_keywords', $toggle_module) ? '' : 'active' );
                                            ?>
                                                <li class="<?php echo $colm3; ?>"><a data-toggle="tab" href="#reportTab2"><figure><img src="/assets/images/google-logo-icon.png"></figure> Search Console</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <div id="reportTab1" class="tab-pane fade in active">
                                    <?php
                                        if(!in_array('organic_keywords_summary', $toggle_module)) {
                                            if(!empty($checkEditNotes1)) {
                                                echo $checkEditNotes1['edit_section'];
                                            }
                                        }
                                    ?>
                                    <?php if(!in_array('organic_keywords_table', $toggle_module)) {?>
                                        <table id="semrush_organic_table" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer" width="100%" cellspacing="0">
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

                                    <div id="reportTab2" class="tab-pane fade in">
                                        <?php if(empty($searchConsole['message'])) { ?>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Query</th>
                                                    <th>Clicks</th>
                                                    <th>Impression</th>
                                                </tr>
                                            </thead>
                                            <tbody class="query_table">
                                            <?php foreach($searchConsole['query'] as $query) { ?>
                                                    <tr>
                                                        <td><?php echo $query->keys[0]; ?></td>
                                                        <td><?php echo $query->clicks; ?></td>
                                                        <td><?php echo $query->impressions; ?></td>
                                                    </tr>
                                            <?php }

                                            ?>
                                            </tbody>
                                        </table>
                                        <?php } else { ?>
                                            <div class="search-console-error-msg"><p><?php echo $searchConsole['message']; ?></p></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

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
        <?php if(empty($searchConsole['message'])) {?>
        Highcharts.chart('search_console', {
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
                                    maincontentText: Highcharts.dateFormat('%A, %b %e, %Y',
                                            this
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
                    },
                }
            },

            series: [{
                color: '#cec937',
                name: 'Clicks',
                data: <?php echo json_encode($clicks); ?>,
                pointInterval   : 30 * 24 * 3600 * 1000

            }, {
                color: '#439009',
                name: 'Impression',
                data: <?php echo json_encode($impression); ?>,
                pointInterval   : 30 * 24 * 3600 * 1000,
                yAxis: 1
            }]
        });
        <?php } ?>
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