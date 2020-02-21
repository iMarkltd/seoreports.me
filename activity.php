<?php
	ini_set('memory_limit', '-1');

	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("includes/report-header.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once 'vendor/autoload.php';

    global $DBcon;
	$token 			= 	$_REQUEST['token_id'];
	$domain_details	=	getUserDomainDetailsByToken($token);
    $profile_info	=	getProfileData($domain_details['id'], $domain_details['user_id']);
    $getActivityDetails         =   getActivityDetails($domain_details['id'], $domain_details['user_id']);
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
                    <div class="col-md-12">
                        <div class="report-box">
                            <h5>Activity Performed</h5>
                            <?php if(!empty($getActivityDetails)){ 
                                    $div_status      =  'left';
                            ?>
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
                                                        <p class="description">
                                                            <?php echo $getActivityData['activity_desc']?>
                                                        </p>
                                                        <span class="date"><?php echo $getActivityData['activity_hour']?> Hours</span>
                                                    </div>
                                                </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
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
