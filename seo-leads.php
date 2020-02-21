<?php
	ini_set('memory_limit', '-1');

	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("includes/report-header.php");
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
        if(!empty($googleAnalytics['access_token'])) {
            $profile_data		=	googleAnalyticsProfileData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
            $profile_old_data	=	googleAnalyticsProfileOldData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
        } else {
            $profile_data		=	array();
            $profile_old_data	=	0;
        }
		if($get_old_month < strtotime('1 month ago')){
			$googleAnalytics	=	getDomainDetails($domain_details['id']);
        }
        
	}else{
		header("Location:404.php");
	}
    $toggle_module			=	getToggleModule($domain_details['id']);
    if(empty($toggle_module))
        $toggle_module      =   array();


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


                <div class="row">
                    <?php if(!in_array('google_goal_summary', $toggle_module) || !in_array('google_goal_table', $toggle_module)) { ?>
                    <div class="col-md-12">
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
                                        foreach($profile_data as $profile) {
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
                                </table>
                            <?php       }
									} else {
                            ?>
                                        <div id="chart-4-container" class="chart"></div>
                            <?php   }            
                                }
                            ?>
                        </div>
                    <?php } ?>
                    </div>
                </div>



            </div>
        </div>

    </div> <!-- #end main-container -->


    <!-- Vendors -->
    <link rel="stylesheet" href="assets/styles/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/styles/introjs.css">
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
    
    <?php include("includes/footer.php"); ?>
