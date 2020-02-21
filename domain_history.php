<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	require_once("includes/config.php");
	require_once("includes/functions.php");
	include_once('assets/ajax/api/semrush_api.php');
	require_once('vendor/autoload.php');
	global $DBcon;
	checkUserLoggedIn();
	
	$ids 			= 	$_REQUEST['id'];
	$domain_details	=	getUserDomainDetails($ids);
	$data			=	'';
	$graph_data		=	array();
	if(!empty($domain_details)){
		$domain_history	=	checkSemarshDomainHistoryData($domain_details['user_id'], $domain_details['domain_url'], $_REQUEST['id']);
		$range1			=	getPositionData($ids, 1, 3);
		$range4			=	getPositionData($ids, 4, 10);
		$range11		=	getPositionData($ids, 11, 20);
		$range21		=	getPositionData($ids, 21, 50);
		$range50		=	getPositionData($ids, 51, 500);
		$total_keywords = 	getTotalOrganicKeywords($ids);
		$top_3			=	getTopKeywords($ids, 1,	3);
		$top_10			=	getTopKeywords($ids, 1, 10);
		$top_100		=	getTopKeywords($ids, 1, 100);
	}


?>
<!DOCTYPE html>
<html>
<body>


<div id="container" class="keyword_hide" ></div>
Total keywords = <?php echo $total_keywords['total']?>
<table class="table">
	<thead>
		<tr>
			<th>Keyword Range</th>
			<th> Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> 1-3 </td>
			<td><?php echo $range1['total'] ?> </td>
		</tr>
		<tr>
			<td> 4-10 </td>
			<td><?php echo $range4['total'] ?> </td>
		</tr>
		<tr>
			<td> 11-20 </td>
			<td><?php echo $range11['total'] ?> </td>
		</tr>
		<tr>
			<td> 21-50 </td>
			<td><?php echo $range21['total'] ?> </td>
		</tr>
		<tr>
			<td> Not Ranked </td>
			<td><?php echo $range50['total'] ?> </td>
		</tr>
	</tbody>
</table>

<table class="table">
	<thead>
		<tr>
			<th>Top Range</th>
			<th> Gain</th>
			<th> Loss</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> Top 3 Positions </td>
			<td><?php echo $top_3['Gain'] ?> </td>
			<td><?php echo $top_3['Loss'] ?> </td>
		</tr>
		<tr>
			<td> Top 10 Positions </td>
			<td><?php echo $top_10['Gain'] ?> </td>
			<td><?php echo $top_10['Loss'] ?> </td>
		</tr>
		<tr>
			<td> Top 100 Positions </td>
			<td><?php echo $top_100['Gain'] ?> </td>
			<td><?php echo $top_100['Loss'] ?> </td>
		</tr>
	</tbody>
</table>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/vendors.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/d3.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/c3.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/screenfull.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/perfect-scrollbar.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/waves.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/jquery.dataTables.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script src="//code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>
Highcharts.chart('container', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Keyword Ranks'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false,
            },
			showInLegend: true
       }
    },
    series: [{
        name: 'KeyWord Rank',
        colorByPoint: true,
        data: [{
            name: '1-3',
            y: <?php echo $range1['total'] ?>
        }, {
            name: '4-10',
            y: <?php echo $range4['total'] ?>
        }, {
            name: '11-20',
            y: <?php echo $range11['total'] ?>
        }, {
            name: '21-50',
            y: <?php echo $range21['total'] ?>,
			
        }, {
            name: 'Not Ranked',
            y: <?php echo $range50['total'] ?>
        }]
    }]
});
</script>

</html>
