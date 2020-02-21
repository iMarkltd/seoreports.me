<?php
date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)

require_once('includes/config.php');
require_once("includes/functions.php");
header("Access-Control-Allow-Origin: *");
?>


<!DOCTYPE html>
<html lang="en">

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
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/fonts/ionicons/css/ionicons.min.css">
    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/c3.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/waves.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/select2.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/bootstrap-colorpicker.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/bootstrap-slider.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/bootstrap-datepicker.css">
    <!-- Css/Less Stylesheets -->
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/new/main.css">
    <link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/new/custom.css">

    <!-- Match Media polyfill for IE9 -->
    <!--[if IE 9]> <script src="scripts/ie/matchMedia.js"></script>  <![endif]-->

    <script>
    (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-93838137-2', 'auto');
    ga('send', 'pageview');
    </script>

</head>

<body id="app"
    class="app off-canvas <?php if(PAGE_NAME == 'seo_view_details.php') echo 'seo_view_detail'; else if( PAGE_NAME == 'test_seo_view_details.php') echo 'seo_view_detail test_seo_view_details'; ?> <?php echo (PAGE_NAME == 'home.php') ?  '' : 'pdf_chart' ; ?> ">
    <?php if(PAGE_NAME == 'seo_view_details.php' ) { ?>
    <div class="loader">
        <div id="vcent"></div>
        <p class="loading" data-loader="0">
            Preparing your report, It's looking good<span>Preparing your report, It's looking good</span>
        </p>
        <p class="loaded">
            Looking good!
        </p>

    </div>
    <?php } ?>
    <!-- header -->

    <?php if(PAGE_NAME != 'login.php' && PAGE_NAME != 'register.php') {?>


    <header class="<?php if(PAGE_NAME != 'test-home.php' && PAGE_NAME != 'dataforseo.php' && PAGE_NAME != 'archived-campaigns.php' && PAGE_NAME != 'settings.php' ) { echo 'inner-dashboard'; } ?>">
        <div class="top-head">
            <div class="container">
                <div class="left">
                    <div class="form-search">
                        <form id="site-search" action="javascript:;">
                            <input type="text" class="form-control search_box_input" placeholder="Type here for search...">
                            <button type="submit" class="ion ion-ios-search-strong"></button>
                        </form>
                    </div>
                    <div class="result_conatainer"></div>
                </div>
                <h2>SEO Dashboard </h2>
                <div class="right">
                    <ul class="list-unstyled right-elems">
                        <?php if(PAGE_NAME != 'test-home.php' && PAGE_NAME != 'dataforseo.php' && PAGE_NAME != 'archived-campaigns.php' && PAGE_NAME != 'settings.php') { ?>
                            <li class="activity">
                                <a href="#"  data-hover="tooltip" title="" data-placement="bottom" data-original-title="Add Activity" pd-popup-open="PopupAddActivity">
                                    <i class="fa fa-address-book-o"></i>
                                </a>
                            </li>
                            <li class="pdf-drop dropdown <?php echo @$count_noti; ?>">
                                <a href="javascript:;" data-toggle="dropdown" id="pdf_notification_dropdown" data-hover="tooltip" title="" data-placement="bottom" data-original-title="PDF Files">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                                <?php if(PAGE_NAME == 'test-home.php' && PAGE_NAME != 'dataforseo.php' && PAGE_NAME != 'archived-campaigns.php' && PAGE_NAME != 'project-settings.php'  && PAGE_NAME != 'settings.php') { ?>

                                <ul class="dropdown-menu dropdown-menu-right" id="notificationsBody">
                                    <?php echo getPDFDownloadList(); ?>
                                </ul>
                                <?php } ?>
                            </li>
                        <?php } ?>
                        <!-- profile drop -->
                        <?php if(!empty($_SESSION['user_id'])) { ?>
                        <?php $count_noti =	(!empty(getUnreadNotification()) ? 'pending-noti' : ''); ?>
                        <?php } ?>
                        <?php if(isset($data)) {
					            if(!empty($data[0]['created']) && strtotime($data[0]['created']) < strtotime('-240 days')) {
                                // this is true
                        ?>
                                    <li class="update-sem">

                                        <a href="#" data-id="<?php echo $data[0]['user_id']?>" data-row="<?php echo $data[0]['domain_name']?>" id="update_semrush_data" data-hover="tooltip" title="" data-placement="bottom" data-original-title="Update Semrush Data">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                    </li>
                        <?php   }
                            }
                        ?>

                        <?php if(PAGE_NAME != 'test-home.php' && PAGE_NAME != 'dataforseo.php' && PAGE_NAME != 'archived-campaigns.php' && PAGE_NAME != 'settings.php') { ?>
                        <li class="client-logo">
                            <a href="project-settings.php?id=<?php echo $_REQUEST['id']; ?>" id="" data-hover="tooltip" title="" data-placement="bottom" data-original-title="Report Setting">
                                <i class="fa fa-cog"></i>
                            </a>
                        </li>
                        <li class="client-logo">
                            <a href="#" pd-popup-open="shareModal" data-hover="tooltip" title="" data-placement="bottom" data-original-title="Share Key">
                                <i class="fa fa-share"></i>
                            </a>
                        </li>
                        <?php } ?>

                        <div class="alert pdf_dowmload_msg" style="display:none">
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">×</span>
                            </button>
                            <div>Download will start with in few minutes! </div>
                        </div>
                        <li class="hide-header-pdf-div" style="display:none"><span><img src="assets/images/ajax-loader.gif" id="personal-loader" style="" /></span></li>
                        <li class="profile-drop hidden-xs dropdown">
                            <?php if(PAGE_NAME != 'seo_view_details.php' && PAGE_NAME != 'seo_pdf.php' &&  PAGE_NAME != 'test_seo_view_details.php') { ?>
                            <a href="javascript:;" data-toggle="dropdown">
                                <?php
						$user_image	=	'assets/images/no-user-image.png';
						$url	=	checkUserProfileImage();
						if(!empty($url['file_name'])) {
							$user_image	=	$url['return_path'];
						}
					?>
                                <img src="<?php echo $user_image; ?>" alt="user-pic" class="user_pic">
                            </a>
                            <?php } ?>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="#" id="profile_id"><span class="ion ion-person">&nbsp;&nbsp;</span>Profile</a>
                                </li>
                                <li><a href="#" id="profile_image"><span class="ion ion-settings">&nbsp;&nbsp;</span>User Image</a>
                                </li>
                                <li><a href="api_unit.php" id="api_unit"><span class="ion ion-ios-crop-strong">&nbsp;&nbsp;</span>Api Unit</a></li>
                                <li class="divider"></li>
                                <li><a href="logout.php" id=""><span class="ion ion-power">&nbsp;&nbsp;</span>Logout</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>


            <?php if(PAGE_NAME == 'seo_view_details.php' ||  PAGE_NAME == 'test_seo_view_details.php') { ?>
            <div class="text-center"><a class="intro_css waves-effect" href="javascript:void(0);"
                    onclick="javascript:startTour();">how to read this report</a></div>
            <?php } ?>
        </div>
        <div class="nav">
            <div class="container">
            <?php if(PAGE_NAME != 'test-home.php' && PAGE_NAME != 'dataforseo.php' && PAGE_NAME != 'archived-campaigns.php' && PAGE_NAME != 'settings.php') { ?>
                <div class="left">
                    <?php
						$user_id	=	@$profile_info['user_id'];
						$request_id	=	@$profile_info['request_id'];
                        $path 		= 	'assets/ajax/uploads/'.$user_id.'/'.$request_id."/";
                        if(file_exists($path)) {
                            $files 		= 	scandir($path);
                            $files 		= 	array_diff(scandir($path), array('.', '..'));
                        }
					?>

                    <div class="client-share-logo" style="background-image: url('<?php if(!empty($files)) echo $path.$files[2]; ?>');">
                    </div>
                </div>
                <p> <i class="fa fa-globe"></i> <?php echo urlToDomain($domain_details['domain_url']);?></p>
                <div class="right">
                    <a href="tel:<?php echo @$profile_info['contact_no']?>"><i class="fa fa-phone"
                            aria-hidden="true"></i> <?php echo @$profile_info['contact_no']?></a> <a
                        href="mailto:<?php echo @$profile_info['email']?>"><i class="fa fa-envelope"
                            aria-hidden="true"></i> <?php echo @$profile_info['email']?></a>
                </div>
                <?php } else { ?>

                <div class="right-side-btns">
                    <a><i class="fa fa-arrow-circle-up"></i> Upgrade</a>
                    <a id="add_domain" pd-popup-open="PopupAddNewDomain"><i class="fa fa-plus-circle"></i> Add New Project</a>
                </div>


               <?php } ?>
            </div>
        </div>

    </header>

    <?php } ?>
    <!-- #end header -->
