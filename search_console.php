<?php
	require_once("includes/config.php");
	require_once("includes/functions.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once('vendor/autoload.php');
	error_reporting(0);
	ini_set('display_errors', 0);
	global $DBcon;
	checkUserLoggedIn();
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$ids 			= 	$_REQUEST['id'];
	$domain_details	=	getUserDomainDetails($ids);
	$data			=	'';
	$graph_data		=	array();
	$client 		= new Google_Client();
	$googleAnalytics	=	googleSearchConsole($_REQUEST['id']);
 
?>


<!DOCTYPE html>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">

<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="Materia - Admin Template">
	<meta name="keywords" content="materia, webapp, admin, dashboard, template, ui">
	<meta name="author" content="solutionportal">
	<!-- <base href="/"> -->

	<title>SEO Dashboard</title>
    
    <link rel="icon" type="image/x-icon" href="favicon.ico" />

	
	<!-- Icons -->
	<link rel="stylesheet" href="https://seoreports.me/assets/fonts/ionicons/css/ionicons.min.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/fonts/font-awesome/css/font-awesome.min.css">

	<!-- Plugins -->
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/c3.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/perfect-scrollbar.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/waves.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/select2.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/bootstrap-colorpicker.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/bootstrap-slider.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/plugins/bootstrap-datepicker.css">
	<!-- Css/Less Stylesheets -->
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/bootstrap.min.css">
	<link rel="stylesheet" href="https://seoreports.me/assets/styles/main.min.css">
<!--	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">-->


	 
 	<link href='https://fonts.googleapis.com/css?family=Roboto:400,500,700,300' rel='stylesheet' type='text/css'>
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	 <script src="//code.highcharts.com/highcharts.js"></script>
	 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

<style>
body{font-family: 'Roboto', sans-serif;}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family: 'Roboto', sans-serif;
}
th, td {
  padding: 15px;
  text-align: left;
}
table#t01 {
  width: 100%;    
  background-color: #f1f1c1;
}
</style>
</head>
<body>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<?php if(!empty($googleAnalytics)) { 
		$clicks		=	array();
		$impression	=	array();
		foreach($googleAnalytics['data'] as $analytics)	{
			$clicks[]		=	array(strtotime($analytics->keys[0])*1000, $analytics->clicks);
			$impression[]	=	array(strtotime($analytics->keys[0])*1000, $analytics->impressions);			
		}
?>
<?php } else { ?>
		<h1>Data Not Found</h1>
<?php } ?>
<div class="container">
  <h2>Dynamic Tabs</h2>

  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">QUERIES</a></li>
    <li><a data-toggle="tab" href="#menu1">PAGES</a></li>
    <li><a data-toggle="tab" href="#menu2">COUNTRIES</a></li>
    <li><a data-toggle="tab" href="#menu3">DEVICES</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
		<table class="table">
			<thead>
				<tr>
					<th>Query</th>
					<th>Clicks</th>
					<th>Impression</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($googleAnalytics)) { 
					foreach($googleAnalytics['query'] as $query)	{
				?>
						<tr>
							<td><?php echo $query->keys[0]; ?></td>
							<td><?php echo $query->clicks; ?></td>
							<td><?php echo $query->impressions; ?></td>
						</tr>
				<?php	}
					}
				?>
			</tbody>
		</table>
		
    </div>
    <div id="menu1" class="tab-pane fade">
		<table class="table">
			<thead>
				<tr>
					<th>Query</th>
					<th>Clicks</th>
					<th>Impression</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($googleAnalytics)) { 
					foreach($googleAnalytics['pages'] as $page)	{
				?>
						<tr>
							<td><?php echo $page->keys[0]; ?></td>
							<td><?php echo $page->clicks; ?></td>
							<td><?php echo $page->impressions; ?></td>
						</tr>
				<?php	}
					}
				?>
			</tbody>
		</table>
		
    </div>
    <div id="menu2" class="tab-pane fade">
		<table class="table">
			<thead>
				<tr>
					<th>Query</th>
					<th>Clicks</th>
					<th>Impression</th>
					<th>CTR</th>
					<th>Position</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($googleAnalytics)) { 
					foreach($googleAnalytics['countries'] as $country)	{
				?>
						<tr>
							<td><?php echo $country->keys[0]; ?></td>
							<td><?php echo $country->clicks; ?></td>
							<td><?php echo $country->impressions; ?></td>
							<td><?php echo $country->ctr; ?></td>
							<td><?php echo $country->position; ?></td>
						</tr>
				<?php	}
					}
				?>
			</tbody>
		</table>
    </div>
    <div id="menu3" class="tab-pane fade">
		<table class="table">
			<thead>
				<tr>
					<th>Query</th>
					<th>Clicks</th>
					<th>Impression</th>
					<th>CTR</th>
					<th>Position</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($googleAnalytics)) { 
					foreach($googleAnalytics['device'] as $device)	{
				?>
						<tr>
							<td><?php echo $device->keys[0]; ?></td>
							<td><?php echo $device->clicks; ?></td>
							<td><?php echo $device->impressions; ?></td>
							<td><?php echo $device->ctr; ?></td>
							<td><?php echo $device->position; ?></td>
						</tr>
				<?php	}
					}
				?>
			</tbody>
		</table>
    </div>
  </div>
</div>

<script>
Highcharts.chart('container', {

chart: {
	scrollablePlotArea: {
		minWidth: 700
	}
},

title: {
	text: 'Daily sessions at www.highcharts.com'
},

subtitle: {
	text: 'Source: Google Analytics'
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
	}
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
				click: function (e) {
					hs.htmlExpand(null, {
						pageOrigin: {
							x: e.pageX || e.clientX,
							y: e.pageY || e.clientY
						},
						headingText: this.series.name,
						maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) + ':<br/> ' +
							this.y + ' sessions',
						width: 200
					});
				}
			}
		},
		marker: {
			lineWidth: 1
		}
	}
},

series: [{
		name: 'Clicks',
		data: <?php echo json_encode($clicks); ?>
	}, {
        name: 'Impression',
		data: <?php echo json_encode($impression); ?>,
		yAxis: 1
	}]
});

</script>
</body>

</html>

