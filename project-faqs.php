<?php
	ini_set('memory_limit', '-1');

	require_once("includes/config.php");
	require_once("includes/functions.php");
	require_once("includes/report-header.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once('vendor/autoload.php');

    global $DBcon;
	$token 			= 	$_REQUEST['token_id'];
	$domain_details	=	getUserDomainDetailsByToken($token);
	$data			=	'';
	$graph_data		=	array();
	if(!empty($domain_details)){
		$profile_info		=	getProfileData($domain_details['id'], $domain_details['user_id']);
        $faqData            =   getFaqData();

	}else{
		header("Location:404.php");
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="report-box faq-report-box">
                            <h5>Frequently Asked Questions</h5>

                            <div class="faq-new-group">
                                <div class="panel-group faqAccordion" id="faqAccordion">
                                <?php
                                    $html_even =    '';
                                    $html_odd  =    '';
                                     if(!empty($faqData)) {
                                        $i = 1;
                                        foreach($faqData as $faq) {
                                            if($i%2==0) {
                                                $html_even .= '<div class="panel">
                                                                <div class="panel-heading">
                                                                    <h4 class="panel-title">
                                                                        <a data-toggle="collapse" href="#collapse'.$i.'">'.$faq['faq_title'].'</a>
                                                                    </h4>
                                                                </div>
                                                                <div id="collapse'.$i.'" class="panel-collapse collapse in">
                                                                    <div class="panel-body">'.$faq['faq_content'].'</div>
                                                                </div>
                                                            </div>';
                                            }else{
                                                $html_odd .= '<div class="panel">
                                                                <div class="panel-heading">
                                                                    <h4 class="panel-title">
                                                                        <a data-toggle="collapse" href="#collapse'.$i.'">'.$faq['faq_title'].'</a>
                                                                    </h4>
                                                                </div>
                                                                <div id="collapse'.$i.'" class="panel-collapse collapse in">
                                                                    <div class="panel-body">'.$faq['faq_content'].'</div>
                                                                </div>
                                                            </div>';
                                            }
                                            $i++;
                                        }
                                    }
                                ?>
                                <div class="row">
                                    <div class="col-md-6"><?php echo $html_odd; ?> </div>
                                    <div class="col-md-6"><?php echo $html_even; ?></div>
                                </div>

                                </div>
                            </div>

                            <?php /* <div class="faq-cover">
                                <div class="row">
                                    <div class="col-md-4">
                                        <?php if(!empty($faqData)) {
                                                $i = 1;
                                        ?>
                                            <ul class="nav nav-pills nav-stacked">
                                            <?php foreach($faqData as $faq) { ?>
                                                <li class="<?php if($i == 1) echo 'active'; ?>"><a data-toggle="tab" href="#m<?php echo $i; ?>"><?php echo $faq['faq_title']?></a></li>
                                            <?php   $i++;
                                                }
                                            ?>
                                        </ul>
                                    <?php } ?>
                                    </div>
                                    <div class="col-md-8">
                                        <?php if(!empty($faqData)) { $i = 1; ?>
                                            <div class="tab-content">
                                                <?php foreach($faqData as $faq) { ?>
                                                <div id="m<?php echo $i; ?>" class="tab-pane fade in <?php if($i == 1) echo 'active'; ?>"><?php echo $faq['faq_content'] ?></div>
                                                <?php   $i++;
                                                    }
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>


                            <div class="panel-group faqAccordion" id="faqAccordion">
                            <?php if(!empty($faqData)) {
                                    $i = 1;
                            ?>
                            <?php foreach($faqData as $faq) { ?>
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#faqAccordion" href="#collapse<?php echo $i; ?>"><?php echo $faq['faq_title']?></a>
                                        </h4>
                                    </div>
                                    <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse <?php echo ($i == 1) ? 'in' : ''; ?>">
                                        <div class="panel-body"><?php echo $faq['faq_content'] ?></div>
                                    </div>
                                </div>
                            <?php   $i++;
                                    }
                                }
                            ?>
                            </div>
*/ ?>
                        </div>
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
    <?php include("includes/footer.php"); ?>
