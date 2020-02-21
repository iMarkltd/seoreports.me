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
		$client 			=   new Google_Client();
		$profile_info		=	getProfileData($domain_details['id'], $domain_details['user_id']);
		$googleAnalytics	=	googleAnalyticsWithoutLogin($domain_details['user_id'], $domain_details['id']);
		$session_count		=	googleAnalyticsSession($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$get_old_month		=	strtotime($session_count[0]['created']);
        $plot_month         =   '';
		$profile_data		=	googleAnalyticsProfileData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$profile_old_data	=	googleAnalyticsProfileOldData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
        $analytics 		    = 	initializeAnalyticsWithoutLogin($domain_details['google_account_id'], $domain_details['user_id']);
        $domainHistoryRange =   getModuleDateRange($domain_details['id'], 'organic_graph');
        $getCompareChart    =   getCompareChart($domain_details['id']);

	}else{
		header("Location:404.php");
	}

    $sessionHistoryRange    =   getModuleDateRange($domain_details['id'], 'organic_traffice');
    $toggle_module			=	getToggleModule($domain_details['id']);

    if(!empty($domain_details['domain_register'])){
        $plot_month     =   date('M', strtotime($domain_details['domain_register']));
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
//                print_r($session_count); exit;
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
        $from_dates	=	'';
        $count_session = '';
        $combine_session = '';
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

                    <div class="col-md-12 four-report-box three-report-box ">
                        <div class="report-box purple">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5>
                                <?php                                        
                                        if ($getCurrentStats['sessions'] <= 0 || $getPreviousStats['sessions'] <= 0 ) {
                                            echo $total_users = "100%";
                                        } else {
                                            echo $total_users = number_format(($getCurrentStats['sessions']  - $getPreviousStats['sessions']) / $getPreviousStats['sessions'] * 100, 2).'%';
                                        }
                                ?>        
                                <span>
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

                        <div class="report-box green">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5>
                                <?php                                        
                                    if ($getCurrentStats['users'] <= 0 || $getPreviousStats['users'] <= 0 ) {
                                        echo $total_users = "100%";
                                    } else {
                                        echo $total_users = number_format(($getCurrentStats['users']  - $getPreviousStats['users']) / $getPreviousStats['users'] * 100, 2).'%';
                                    }
                                ?>        
                                <span>
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

                        <div class="report-box pink">
                            <figure>
                                <img src="/assets/images/google-logo-icon.png" alt="">
                            </figure>
                            <h5>
                                <?php                                        
                                    if ($getCurrentStats['pageview'] <= 0 || $getPreviousStats['pageview'] <= 0 ) {
                                        echo $total_pageview = "100%";
                                    } else {
                                        echo $total_pageview = number_format(($getCurrentStats['pageview']  - $getPreviousStats['pageview']) / $getPreviousStats['pageview'] * 100, 2).'%';
                                    }
                                ?>        
                                <span>
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


                </div>
                <input type="hidden" name="compare_graph" id="compare_graph" value="<?php if(!empty($getCompareChart) && $getCompareChart['compare_status'] == 1) echo 1; ?>"  >

                <div class="row">
                    <?php if(!in_array('organic_traffic', $toggle_module)) {?>
                    <div class="col-md-12">
                        <div class="report-box">
                            <h5>Organic Traffic Growth (6 months)</h5>
                            <div class="table-data-outer">
                                <div class="total_data" style=""></div>
                            </div>
                            <div id="chart-1-container" class="chart"></div>
                        </div>
                    </div>
                    <?php } ?>
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
        <?php if(!in_array('organic_traffic', $toggle_module)) {?>
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
        <?php } } ?>
    });
    </script>

    <style>
    /*.table-responsive .panel-body{
	min-width: 1500px;
	}*/


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