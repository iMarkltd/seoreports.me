<?php
	ini_set('memory_limit', '-1');
	require_once("includes/config.php");
	require_once("includes/functions.php");

	include_once('assets/ajax/api/semrush_api.php');
	require_once 'vendor/autoload.php';
	error_reporting(0);
	ini_set('display_errors', 0);
	global $DBcon;

	$token 			= 	$_REQUEST['token'];
	//$token 			= 	$attch_token;
	$domain_details	=	getUserDomainDetailsByToken($token);
	$data			=	'';
	$graph_data		=	array();
	if(!empty($domain_details)){
		$client 			= 	new Google_Client();
		$data				=	checkSemarshApiData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id'], $domain_details['regional_db']);
		$backlink_data		=	checkSemarshBacklinkData($domain_details['user_id'], $domain_details['domain_url'], $domain_details['id']);
		$domain_history		=	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id'], $domain_details['regional_db']);
		$googleAnalytics	=	googleAnalyticsWithoutLogin($domain_details['user_id'], $domain_details['id']);
		$session_count		=	googleAnalyticsSession($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$profile_data		=	googleAnalyticsProfileData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$profile_old_data	=	googleAnalyticsProfileOldData($domain_details['user_id'], $domain_details['id'], $googleAnalytics['access_token']);
		$checkEditNotes0	=	checkEditNotes0($domain_details['user_id'], $domain_details['id']);
		$checkEditNotes1	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '1');
		$checkEditNotes2	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '2');
		$checkEditNotes3	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '3');
		$checkEditNotes4	=	checkEditNotes1($domain_details['user_id'], $domain_details['id'], '4');
		$checkGoogleGoal	=	checkGoogleGoal($domain_details['user_id'], $domain_details['id']);
		$checkCurrentGoogleGoal	=	checkCurrentGoogleGoal($domain_details['user_id'], $domain_details['id']);
		$getMozData			=	getMozData($domain_details['domain_url']);
		$toggle_module		=	getToggleModule($domain_details['id']);

	}else{
		header("Location:404.php");
	}
	$count_session 	= 	'';
	$current_date	=	strtotime($session_count[0]['from_date']);
	$new_time		=	'';
	$from_dates		=	'';
	$count_session 	= 	'';
	foreach($session_count as $session_details) {
		$from_dates[] 		=  "'".date('M d', strtotime($session_details['from_date']))."'";
		$count_session[]	=  $session_details['total_session'];
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



?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="Materia - Admin Template">
	<meta name="keywords" content="materia, webapp, admin, dashboard, template, ui">
	<meta name="author" content="solutionportal">
	<!-- <base href="/"> -->

	<title>SEO Dashboard</title>

    <link rel="icon" type="image/x-icon" href="favicon.ico" />
	<!-- Icons -->
	<link rel="stylesheet" href="assets/fonts/ionicons/css/ionicons.min.css">
	<link rel="stylesheet" href="assets/fonts/font-awesome/css/font-awesome.min.css">
	<!-- Plugins -->
	<!-- Css/Less Stylesheets -->
	<link rel="stylesheet" href="assets/styles/bootstrap.min.css">
	<link rel="stylesheet" href="assets/styles/main.min.css">



 	<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,700,300' rel='stylesheet' type='text/css'>

	<!-- Match Media polyfill for IE9 -->
	<!--[if IE 9]> <script src="scripts/ie/matchMedia.js"></script>  <![endif]-->
	<link rel="stylesheet" href="assets/styles/jquery.dataTables.min.css"></link>
	<style type="text/css">
		.bs-example{
		margin: 20px;
		}
		#example tr td div{
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
		.-success span.s-label__text{
		background: #069856;
		}

        .highcharts-series-5 .highcharts-point{
		stroke:#2675b9;
        }
		.dcs-a-dcs-gb{ z-index: 9999; }

		.pd-client-logo{
			display:block;
			margin:0 auto 30px;
			width:300px;
			height:240px;
			background-repeat:no-repeat;
			background-size:contain;
			background-position:center center;

		}

/*
		div.panel{
        page-break-after: avoid;
        page-break-inside: avoid;
      }
*/

#c3chartline,  div#chart-1-container{
clear: both;   }

	</style>
	<script src="assets/scripts/vendors.js"></script>
	<script src="assets/scripts/plugins/jquery.dataTables.min.js"></script>
	<script src="//code.highcharts.com/highcharts.js"></script>

	<script type="text/javascript">
		$(document).ready(function() {
				$('#semrush_organic_table').DataTable( {
					"order": [[ 1, "asc" ]],
					"pageLength": 25,
					"bPaginate": false,
					"paging": false,
					"showNEntries" : false,
					"bInfo": false
				} );

				$('#semrush_backlink_table').DataTable({
					"pageLength": 25,
					"bPaginate": false,
					"showNEntries" : false,
					"bInfo": false,
					"paging": false
				});
				$('#google_profile_table').DataTable({
					"pageLength": 25,
					"bPaginate": false,
					"showNEntries" : false,
					"bInfo": false,
					"paging": false
				});
		});
		$(function () {
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
					tickInterval:86400000 * 30

				},
				title:{
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

				Highcharts.chart('chart-1-container', {
					chart: {
						type: 'area'
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

		});
	</script>

</head>
<body id="app" class="app off-canvas seo_view_detail">


<!-- main-container -->
<div class="main-container clearfix">
	<input type="hidden" class="session_new" />
	<input type="hidden" class="users_new" />
	<input type="hidden" class="pageview_new" />
	<input type="hidden" class="session_old" />
	<input type="hidden" class="users_old" />
	<input type="hidden" class="pageview_old" />
	<!-- content-here -->
	<div class="content-container" id="content">


		<div class="brand-cover-section">

			<div class="brand-cover-inner">
				<?php
					$path 		= 	'assets/ajax/uploads/'.$domain_details['user_id'].'/'.$domain_details['id']."/";
					if (is_dir($path)) {
						if ($dh = opendir($path)) {
							while (($file = readdir($dh)) !== false) {
								if($file != '..' && $file != '.')
									$file_name[]	=	$file;
							}
							closedir($dh);
						}
					}
					$file_src	=	$path.$file_name[0];
					if(!empty($file_name)) {
				?>
					<div class='pd-client-logo' style='background-image:url("<?php echo $file_src; ?>");' /></div>
				<?php } ?>
				<h1>Seo performance REPORT</h1>


				<p>Prepared for <strong><?php echo $domain_details['domain_url']; ?></strong></p>
				<date><?php echo date('d/m/Y'); ?></date>
			</div>


		</div>
		<div class="page m134 page-charts-c3" ng-controller="c3ChartDemoCtrl">
			<div class="pd-fixed-bar-outer" style="position: static;">
                            <div class="pd-fixed-bar" >
                                <div class="pd-bar-data" >
                                    <a href="#pdS"><h5><div class="custom-circle"></div>Organic keywords</h5>
									<?php $domain_history		= $domain_history[1]['organic_keywords']; ?>
									<?php $organic_keywords	= round((count($data)-$domain_history)/$domain_history*100, 2); ?>
									<big><?php echo $organic_history; ?> <span id="traffic_growth" class="<?php echo ($organic_keywords < 0 ? 'red' : 'green' ); ?>"><?php echo ($organic_keywords> 0 ? '+' : '' );  echo $organic_keywords."%"; ?></span></big><strong>Keywords</strong></a>
                                </div>
                                <div class="pd-bar-data" >
                                    <a href="#pdF"><h5><div class="custom-circle"></div>Organic traffic growth</h5>
									 <?php $traffic_growth	= round(($profile_old_data['sessions_new']-$profile_old_data['sessions_old'])/$profile_old_data['sessions_old']*100, 2); ?>
                                    <big class="traffic_growth"><?php echo $profile_old_data['sessions_new']; ?> <span id="traffic_growth" class="<?php echo ($traffic_growth < 0 ? 'red' : 'green' ); ?>"><?php echo ($traffic_growth <= 0 ? '100%' : $traffic_growth."%" ); ?></span></big><strong>Traffic</strong>
                                        </a>
                                </div>
                                <div class="pd-bar-data" >
                                    <a href="#pdT"><h5><div class="custom-circle"></div>Sample Backlinks</h5>
                                    <big><?php echo count($backlink_data); ?></big><strong>Links</strong></a>
                                </div>

                                <div class="pd-bar-data" >
                                    <a href="#pdFT"><h5><div class="custom-circle"></div>Google AnaLytics Goals</h5>
                                    <big class="google_analytics"><?php echo $checkCurrentGoogleGoal['total']; ?> <span id="traffic_growth" class="<?php echo ($goal_result < 0 ? 'red' : 'green' ); ?>"><?php echo  $goal_result; ?></span></big><strong>Goals</strong></a>
                                </div>

                            </div>
                            </div>
			<div class="page-wrap">
				<!-- row -->
				<div class="row">

					<!-- Line Chart -->
					<div class="col-md-12">
						<div class="panel panel-default panel-stacked panel-hovered mb30">
							<div class="panel-heading inner-dash-heading">
								Monthly Performance Report <small>(<?php echo $domain_details['domain_name'];?>)</small>
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
								<div class="row custom-table-pd">
									<div class="col-md-12 pd-add-mb">
										<h2>Organic Keyword Growth (12 months)</h2>
										<?php if(!empty($getMozData)) {?>
										<div class="table-data-outer pd-add-mb">
											<div class="total_data">
												<h5>Page Authority</h5>
												<div class="test"><?php echo $getMozData['pageAuthority'].' /100'; ?></div>
											</div>
											<div class="total_data">
												<h5>Domain Authority</h5>
												<div class="test"><?php echo $getMozData['domainAuthority'].' /100'; ?></div>
											</div>
										</div>
										<?php } ?>
										<div id="c3chartline"></div>
									</div>
									<div class="col-md-12">
										<h2>Organic Traffic Growth  (6 months)</h2>
										<div class="table-data-outer pd-add-mb">
										<div class="table-data-outer">
											<div class="total_data" style="">
												<h5>Sessions</h5>
												<small>Organic Traffic</small>
												<?php
														if($profile_old_data['sessions_total'] == "Infinity%" || $profile_old_data['sessions_total'] == "0%") $profile_session_total = "100"; else $profile_session_total = $profile_old_data['sessions_total'];
												?>
												<div class="total_session <?php if($profile_session_total < 0) echo 'red'; ?>"><?php if($profile_old_data['sessions_total'] == "Infinity%" || $profile_old_data['sessions_total'] == "0%") echo "100%"; else echo $profile_old_data['sessions_total'];?></div>
												<div class="new_session"><?php echo ($profile_old_data['sessions_new'] <=0 ? '0' : $profile_old_data['sessions_new'] ).' vs '.($profile_old_data['sessions_old'] <=0 ? '0' : $profile_old_data['sessions_old'] ); ?></div>
											</div>
											<div class="total_data" style="">
												<h5>Users</h5>
												<small>Organic Traffic</small>
												<?php
														if($profile_old_data['users_total'] == "Infinity%" || $profile_old_data['users_total'] == "0%") $profile_user_total = "100"; else $profile_user_total = $profile_old_data['users_total'];
												?>
												<div class="total_users <?php if($profile_user_total < 0) echo "red"; ?>"><?php if($profile_old_data['users_total'] == "Infinity%" || $profile_old_data['users_total'] == "0%") echo "100%"; else echo $profile_old_data['users_total']; ?></div>
												<div class="new_users"><?php echo ($profile_old_data['users_new'] <=0 ? '0' : $profile_old_data['users_new'] ).' vs '.($profile_old_data['users_old'] <=0 ? '0' : $profile_old_data['users_old'] ); ?></div>
											</div>
											<div class="total_data" style="">
												<h5>Pageviews</h5>
												<small>Organic Traffic</small>
												<?php
														if($profile_old_data['pageviews_total'] == "Infinity%" || $profile_old_data['pageviews_total'] == "0%") $profile_pageview_total = "100"; else $profile_pageview_total = $profile_old_data['pageviews_total'];
												?>
												<div class="total_pageview <?php if($profile_pageview_total < 0 ) echo 'red'; ?>"><?php if($profile_old_data['pageviews_total'] == "Infinity%" || $profile_old_data['pageviews_total'] == "0%") echo "100%"; else  echo $profile_old_data['pageviews_total']?></div>
												<div class="new_pageview"><?php echo ($profile_old_data['pageviews_old'] <=0 ? '0' : $profile_old_data['pageviews_old'] ).' vs '.($profile_old_data['pageviews_new'] <=0 ? '0' : $profile_old_data['pageviews_new'] ); ?></div>
											</div>
										</div>
										<div class="clearfix">
										</div>
										<div id="chart-1-container" class="chart"></div>
									</div>
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
						<div class="panel panel-default panel-lined table-responsive panel-hovered mb20 data-table keywords-table" style="padding-bottom: 20px">
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

								<table id="semrush_organic_table" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer" width="100%" cellspacing="0">
									<thead>
										<tr>
											<th>Keywords</th>
											<th>Current Position</th>
											<th>Previous Position</th>
											<th>Change</th>
											<th>Traffic %</th>
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
															<i class="fa fa-arrow-up" aria-hidden="true" style="color:green"> <?php echo 100-$record['position']; ?></i>
															<?php } else if($record['position_difference'] < 0 ) { ?>
															<i class="fa fa-arrow-down" aria-hidden="true" style="color:red"> <?php echo $record['position_difference']; ?></i>
															<?php } else if($record['position_difference'] > 0) {?>
															<i class="fa fa-arrow-up" aria-hidden="true" style="color:green"> <?php echo $record['position_difference']; ?></i>
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
					<div class="blank-link"  id="pdT"></div>
					<!-- Data Table -->
					<div class="col-md-12">
						<div class="panel panel-default panel-lined table-responsive panel-hovered mb20 data-table" style="padding-bottom: 20px">
							<div class="panel-heading">
                                <h2>Sample Backlinks</h2>
							</div>
							<!-- data table -->
							<div class="panel-body">
								<div class="row">
									<?php
										if(!empty($checkEditNotes2)) {
									?>
										<div class="col-md-12">
											<div class="panel panel-default panel-stacked panel-hovered mb30">
												<div class="panel-body custom-top-heading-pd edit_note_first">
											<?php echo $checkEditNotes2['edit_section']; ?>

											</div>
												</div>
									</div>
									<?php
										}
									?>
								</div>
								<?php /*
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
													<td><div><?php echo $record['source_title']?></div>
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
															<div>  FRAME </div>
															<?php } else if( $record['form'] == 'image' ) { ?>
															<div> IMAGE  </div>
															<?php } else { ?>
															<div> Text</div>
														<?php } ?>
													</td>
													<td><div class="<?php if($record['newlink'] == 'true') { echo "lostlink-success"; } ?>"><?php echo date('d M Y', $record['first_seen'])?></div></td>
													<td><div class="<?php if($record['lostlink'] == 'true') { echo "lostlink-success"; }?>"><?php echo date('d M Y', $record['last_seen'])?></div></td>
												</tr>
												<?php }
											}
										?>
									</tbody>
								</table>
								<!-- #end data table -->
								*/ ?>
							</div>
						</div>
					</div>
				</div><!-- #end row -->
				<div class="row">
					<div class="blank-link"  id="pdFT"></div>
						<!-- Line Chart -->
						<div class="col-md-12">
							<div class="panel panel-default panel-stacked panel-hovered mb30">
								<div class="panel-heading"><h2>Google Analytics Goal Completion</h2></div>
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
									<table id="google_profile_table" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer" width="100%" cellspacing="0">
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
								</div>
							</div>
						</div>
					<?php
					if(!empty($checkEditNotes4)) {
					?>
					<div class="col-md-12">
							<div class="panel panel-default panel-stacked panel-hovered mb30 edit-notes-section">
								<div class="panel-heading">
									<h2><?php echo ($checkEditNotes4['note_heading'] != '' ? $checkEditNotes4['note_heading'] : 'NOTES'); ?></h2>
								</div>
								<div class="panel-body edit_note_second"><?php echo $checkEditNotes4['edit_section']; ?></div>
							</div>
						</div>
					<?php
						}
					?>

				</div>
			</div>
		</div>
	</div> <!-- #end main-container -->
	<!-- Dev only -->
	<!-- Vendors -->

	<script src="assets/scripts/canvas-to-blob.min.js" type="text/javascript"></script>
	<script>

	</script>
</body>
</html>
