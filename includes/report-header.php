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
<body id="app" class="app off-canvas seo_view_detail test_seo_view_details pdf_chart">


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