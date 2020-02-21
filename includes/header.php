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
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/fonts/font-awesome/css/font-awesome.min.css">

	<!-- Plugins -->
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/c3.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/perfect-scrollbar.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/waves.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/select2.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/bootstrap-colorpicker.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/bootstrap-slider.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/plugins/bootstrap-datepicker.css">
	<!-- Css/Less Stylesheets -->
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo FULL_PATH; ?>assets/styles/main.min.css">
<!--	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">-->



 	<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,700,300' rel='stylesheet' type='text/css'>

	<!-- Match Media polyfill for IE9 -->
	<!--[if IE 9]> <script src="scripts/ie/matchMedia.js"></script>  <![endif]-->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-93838137-2', 'auto');
  ga('send', 'pageview');

</script>
</head>
<body id="app" class="app off-canvas <?php if(PAGE_NAME == 'seo_view_details.php') echo 'seo_view_detail'; else if( PAGE_NAME == 'test_seo_view_details.php') echo 'seo_view_detail test_seo_view_details'; ?> <?php echo (PAGE_NAME == 'home.php') ?  '' : 'pdf_chart' ; ?> ">
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

	<?php if(PAGE_NAME != 'index.php' && PAGE_NAME != 'register.php') {?>
	<header class="site-head" id="site-head">
		<ul class="list-unstyled left-elems">
			<!-- nav trigger/collapse -->
			<li>
				<a href="javascript:;" class="nav-trigger ion ion-drag"></a>
			</li>
			<!-- #end nav-trigger -->

			<!-- Search box -->
			<li>
				<div class="form-search hidden-xs">
					<form id="site-search" action="javascript:;">
						<input type="search" class="form-control search_box_input" placeholder="Type here for search...">
						<button type="submit" class="ion ion-ios-search-strong"></button>
					</form>
				</div>
			</li>	<!-- #end search-box -->
			<div class="result_conatainer">
			</div>
			<!-- site-logo for mobile nav -->
			<li>
				<div class="site-logo visible-xs">
					<a href="javascript:;" class="text-uppercase h3">
						<span class="text">Imark</span>
					</a>
				</div>
			</li> <!-- #end site-logo -->


			<!-- notification drop -->

		</ul>
			<?php if(PAGE_NAME == 'seo_view_details.php' ||  PAGE_NAME == 'test_seo_view_details.php') { ?>
				<div class="text-center"><a class="intro_css waves-effect" href="javascript:void(0);" onclick="javascript:startTour();">how to read this report</a></div>
			<?php } ?>

		<ul class="list-unstyled right-elems">


			<!-- profile drop -->
			<?php if(!empty($_SESSION['user_id'])) { ?>
				<?php $count_noti =	(!empty(getUnreadNotification()) ? 'pending-noti' : ''); ?>
			<?php } ?>
			<li class="update-sem" style="display: table-cell;padding-right: 15px;">
			<a href="test-home.php"><i class="fa fa-code-fork" aria-hidden="true"></i></a>
			</li>
			<li class="pdf-drop dropdown <?php echo $count_noti; ?>">
				<a href="javascript:;" data-toggle="dropdown" id="pdf_notification_dropdown">
					<span class="lines"></span>
					<span class="lines"></span>
					<span class="lines"></span>
					<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
				</a>
			<?php if(PAGE_NAME == 'home.php') { ?>

				<ul class="dropdown-menu dropdown-menu-right" id="notificationsBody">
					<?php echo getPDFDownloadList(); ?>
				</ul>
			<?php } ?>
			</li>

				<?php
				 if(isset($data)) {
					if(strtotime($data[0]['created']) < strtotime('-240 days')) {
					    // this is true
				?>
				<li class="update-sem">

					<a href="#" data-id="<?php echo $data[0]['user_id']?>" data-row="<?php echo $data[0]['domain_name']?>" id="update_semrush_data" data-hover="tooltip" title="" data-placement="left" data-original-title="Update Semrush Data">
							<i class="fa fa-refresh"></i>
						</a>
				</li>
				<?php
				 	}
				 }
				 ?>

			<?php if(PAGE_NAME == 'seo_analytics_chart.php') { ?>
				<li class="client-logo">
					<a href="#" id="profile_logo" data-hover="tooltip" title="" data-placement="left" data-original-title="Profile Info">
						<i class="fa fa-upload"></i>
					</a>
				</li>
				<li class="client-logo">
					<a href="#" id="shareViewKey" data-hover="tooltip" title="" data-placement="left" data-original-title="Share Key">
						<i class="fa fa-share"></i>
					</a>
				</li>
			<?php } ?>

<!--
			<li class="edit-link-head about-comp-btn">
				<a href="#" data-hover="tooltip" title="" data-placement="left" data-original-title="Edit">
					<i class="fa fa-pencil"></i>
				</a>
			</li>
-->

			<div class="alert pdf_dowmload_msg" style="display:none">
				<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">×</span>
				</button>
				<div>Download will start with in few minutes! </div>
			</div>
			<li class="pdf-link-head">
				<?php if(PAGE_NAME == 'seo_analytics_chart.php') { ?>
					<a href="#" data-hover="tooltip" data-id="<?php echo $_REQUEST['id']; ?>" id="seo_analytics_pdf" title="" data-placement="left" data-original-title="Download PDF">
				<?php } else if(PAGE_NAME == 'seo_view_details.php' ||  PAGE_NAME == 'test_seo_view_details.php') { ?>
					<a href="#" data-hover="tooltip" data-id="<?php echo $_REQUEST['token_id']; ?>" id="seo_view_pdf_from_view" title="" data-placement="left" data-original-title="Download PDF">
				<?php } else { ?>
					<a href="#" data-hover="tooltip" data-id="<?php echo PAGE_NAME?>" title="" data-placement="left" data-original-title="Download PDF">
				<?php } ?>
					<i class="fa fa-file-pdf-o"></i>
				</a>
			</li>
			<li class="hide-header-pdf-div" style="display:none"><span><img src="assets/images/ajax-loader.gif" id="personal-loader" style=""/ ></span></li>
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
					<li><a href="#" id="profile_id"><span class="ion ion-person">&nbsp;&nbsp;</span>Profile</a></li>
					<li><a href="#" id="profile_image"><span class="ion ion-settings">&nbsp;&nbsp;</span>User Image</a></li>
					<li><a href="api_unit.php" id="api_unit"><span class="ion ion-ios-crop-strong">&nbsp;&nbsp;</span>Api Unit</a></li>
					<li class="divider"></li>
					<li><a href="logout.php" id=""><span class="ion ion-power">&nbsp;&nbsp;</span>Logout</a></li>
				</ul>
			</li>
			<!-- #end profile-drop -->

			<!-- sidebar contact -->

		</ul>

	</header>
	<?php } ?>
	<!-- #end header -->

<style>
.result_conatainer{
	display:none;
	height: 50px;
    width: 250px;
    position: absolute;
    top: 51px;
    left: 27px;
}
</style>