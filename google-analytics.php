<?php 
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/header.php");
require_once 'vendor/autoload.php';
global $DBcon;
$client = new Google_Client();
$client->setAuthConfig(PATH.'/client_secret_660210681878-mo4hm531u1890rsisl5dbuf6gg4kcqpa.apps.googleusercontent.com.json');
$client->setAccessType('offline');
$query = "SELECT * FROM google_analytics_users WHERE user_id=:user_id";
$stmt = $DBcon->prepare( $query );
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute() or die(print_r($stmt->errorInfo(), true));
if ($stmt->rowCount() > 0) {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$_SESSION['access_token'] 	= $row['google_access_token'];
		$_SESSION['refresh_token'] 	= $row['google_refresh_token'];
		$_SESSION['service_token']['access_token'] = $row['google_access_token'];
		$_SESSION['service_token']['token_type'] = $row['token_type'];
		$_SESSION['service_token']['expires_in'] = $row['expires_in'];
		$_SESSION['service_token']['id_token'] = $row['id_token'];
		$_SESSION['service_token']['created'] = $row['service_created']; 
	}			
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$_tokenArray	=	json_encode($_SESSION['service_token']);
	$client->setAccessToken($_tokenArray);
	$analytics = new Google_Service_Analytics($client);
	$profile 		= getFirstProfileId($analytics);
	$property_id 	= getFirstPropertyId($analytics);
	echo $property_id; 

} else {
	$redirect_uri = FULL_PATH.'/php-auth.php';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


?>	

	<!-- main-container -->
	<!-- main-container -->
	<div class="main-container clearfix">
		<!-- main-navigation -->
		<aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>
			<div class="nav-head">
				<!-- site logo -->
				<a href="index.html" class="site-logo text-uppercase">
					<i class="ion ion-disc"></i>
					<span class="text">Imark </span>
				</a>
			</div>

			<!-- Site nav (vertical) -->
			<?php require_once("includes/nav-sidebar.php"); ?>
			
		</aside>
		<!-- #end main-navigation -->

		<!-- content-here -->
		<div class="content-container" id="content">
			<div class="page page-charts-c3" ng-controller="c3ChartDemoCtrl">

				<ol class="breadcrumb breadcrumb-small">
					<li>Charts</li>
					<li class="active"><a href="charts.c3.html">C3</a></li>
				</ol>

				<div class="page-wrap">

					<div class="row">

						<!-- Data Table -->
						<div class="col-md-12">
							<div class="panel panel-lined table-responsive panel-hovered mb20 data-table" style="padding-bottom: 20px">
							<!-- data table -->
							<?php 									$accounts = $analytics->management_accountSummaries->listManagementAccountSummaries();																/**
									$analytics = new Google_Service_Analytics($client);
									$accounts = $analytics->management_accountSummaries->listManagementAccountSummaries();
									foreach ($accounts->getItems() as $item) {
									
									echo "Account: ",$item['name'], "  " , $item['id'], "
								\n";
									
										foreach($item->getWebProperties() as $wp) {
											echo '-----WebProperty: ' ,$wp['name'], "  " , $wp['id'], "
									\n";    
											$views = $wp->getProfiles();
											if (!is_null($views)) {
																// note sometimes a web property does not have a profile / view

												foreach($wp->getProfiles() as $view) {

													echo '----------View: ' ,$view['name'], "  " , $view['id'], "
									\n";    
												}  // closes profile
											}
										} // Closes web property
									
									} // closes account summaries
											<div>
											<div>Accounts</div>
											<select name="category" onchange="ajaxfunction(this.value)">													
												<option value="0">None</option>													
												<?php 	if (count($accounts->getItems()) > 0) {
															foreach($accounts as $account) { 													
												?>																	
																<option value="<?php echo $account['id']?>"><?php echo $account['name']?></option>													
												<?php 		} 															
													} else {													
												?>																	
														<option value="0">Not Available</option>													
												<?php 															
													}													
												?>	
											</select>											
											</div>
										**/							
							?>        
		<h1>Traffic For The Last 30 Days</h1>
		  <h1 class="Titles-main" id="view-name">Select a View</h1>
        <div id="view-selector" class="chart"></div>        
        <div id="chart-1-container" class="chart"></div>
        <div id="chart-2-container" class="chart"></div>
        <div id="chart-3-container" class="chart"></div>
        <div id="chart-4-container" class="chart"></div>
        <div id="my-table" class="chart"></div>

											<!-- #end data table -->	
							
						</div>
						</div>
					</div><!-- #end row -->
					
					





				</div> <!-- #end page wrap -->
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
								<input type="checkbox"  id="fixedHeader"> 
								<span>&nbsp;</span> 
							</label>
						</div>
					</li>
					<li class="clearfix mb10">
						<div class="left small">Nav Full</div>
						<div class="md-switch right">
							<label>
								<input type="checkbox"  id="navFull"> 
								<span>&nbsp;</span> 
							</label>
						</div>
					</li>
				</ul>
				<hr/>
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
	<style type="text/css">
		.bs-example{
			margin: 20px;
		}
	</style>	
	<script src="assets/scripts/vendors.js"></script>
	<script src="assets/scripts/plugins/d3.min.js"></script>
	<script src="assets/scripts/plugins/c3.min.js"></script>
	<script src="assets/scripts/plugins/screenfull.js"></script>
	<script src="assets/scripts/plugins/perfect-scrollbar.min.js"></script>
	<script src="assets/scripts/plugins/waves.min.js"></script>
	<script src="assets/scripts/plugins/jquery.dataTables.min.js"></script>
	<script src="assets/scripts/app.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>	
        <!-- Load Google's Embed API Library -->
        <script>(function(w,d,s,g,js,fs){
        g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
        js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
        js.src='https://apis.google.com/js/platform.js';
        fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));</script>
        
        <script>
        gapi.analytics.ready(function() {
          /**
           * Authorize the user with an access token obtained server side.
           */
          gapi.analytics.auth.authorize({
            'serverAuth': {
              'access_token': '<?php echo ($myToken['access_token']); ?>'
            }
          });
          /**
           * Creates a new DataChart instance showing sessions over the past 30 days.
           * It will be rendered inside an element with the id "chart-1-container".
           */
		  var viewSelector = new gapi.analytics.ViewSelector({
			container: 'view-selector'
		  });
    	  viewSelector.execute();

            var timeline = new gapi.analytics.googleCharts.DataChart({
                reportType: 'ga',                
				query: {                    
				'dimensions': 'ga:sessionCount',                    
				'metrics': 'ga:users',                    
				'start-date': '7daysAgo',                    
				'end-date': 'today',                    
				'max-results': '10',                
				},                
				chart: {                    
					type: 'LINE',                    
					container: 'timeline',                    
					options: {                        
						colors: ['#918A09'],                        
						fontName: 'montserratregular',                    
						}                
				}            
			});            
			// Step 6: Hook up the components to work together.            
			gapi.analytics.auth.on('success', function (response) {
                viewSelector.execute();            
			});            
			viewSelector.on('change', function (ids) {
                var newIds = {                    
				query: {ids: ids } 
				}
                timeline.set(newIds).execute();            
			});
			viewSelector.on('change', function(ids) {
				dataChart1.set({query: {ids: ids}}).execute();				
				dataChart2.set({query: {ids: ids}}).execute();				
				dataChart3.set({query: {ids: ids}}).execute();				
				dataChart4.set({query: {ids: ids}}).execute();				
				var title = document.getElementById('view-name');
				title.textContent = data.property.name + ' (' + data.view.name + ')';				
			});
          var dataChart1 = new gapi.analytics.googleCharts.DataChart({
            query: {
              'ids': 'ga:<?php echo $property_id;?>', // The Demos & Tools website view.
              'start-date': '30daysAgo',
              'end-date': 'yesterday',
              'metrics': 'ga:sessions,ga:users',
              'dimensions': 'ga:date'
            },
            chart: {
              'container': 'chart-1-container',
              'type': 'LINE',
              'options': {
                'width': '100%'
              }
            }
          });
          dataChart1.execute();
          /**
           * Creates a new DataChart instance showing top 5 most popular demos/tools
           * amongst returning users only.
           * It will be rendered inside an element with the id "chart-3-container".
           */
          var dataChart2 = new gapi.analytics.googleCharts.DataChart({
            query: {
              'ids': 'ga:<?php echo $property_id;?>', // The Demos & Tools website view.
              'start-date': '30daysAgo',
              'end-date': 'yesterday',
              'metrics': 'ga:pageviews',
              'dimensions': 'ga:pagePathLevel2',
              'sort': '-ga:pageviews',
              'filters': 'ga:pagePathLevel1!=/',
              'max-results': 10
            },
            chart: {
              'container': 'chart-2-container',
              'type': 'PIE',
              'options': {
                'width': '100%',
                'pieHole': 4/9,
              }
            }
          });
          dataChart2.execute();

          var dataChart3 = new gapi.analytics.googleCharts.DataChart({
            query: {
              'ids': 'ga:<?php echo $property_id;?>', // The Demos & Tools website view.
              'start-date': '30daysAgo',
              'end-date': 'yesterday',
              'metrics': 'ga:pageviews,ga:uniquePageviews, ga:avgTimeOnPage, ga:entrances, ga:bounceRate, ga:exitRate',
              'dimensions': 'ga:pagePath'
            },
            chart: {
              'container': 'chart-3-container',
              'type': 'LINE',
              'options': {
                'width': '100%'
              }
            }
          });
          dataChart3.execute();
		  
          var dataChart4 = new gapi.analytics.googleCharts.DataChart({
            query: {
              'ids': 'ga:<?php echo $property_id; ?>', // The Demos & Tools website view.
              'start-date': '30daysAgo',
              'end-date': 'yesterday',
              'metrics': 'ga:sessions,ga:percentNewSessions, ga:newUsers, ga:bounceRate, ga:pageviewsPerSession, ga:avgSessionDuration, ga:goalConversionRateAll, ga:goalCompletionsAll, ga:goalValueAll',
              'dimensions': 'ga:keyword'
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

			var report = new gapi.analytics.report.Data({
			  query: {
              'ids': 'ga:<?php echo $property_id; ?>', // The Demos & Tools website view.
              'start-date': '30daysAgo',
              'end-date': 'yesterday',
              'metrics': 'ga:sessions,ga:percentNewSessions, ga:newUsers, ga:bounceRate, ga:pageviewsPerSession, ga:avgSessionDuration, ga:goalConversionRateAll, ga:goalCompletionsAll, ga:goalValueAll',
              'dimensions': 'ga:keyword'
			  }
			});

			report.on('success', function(response) {
			  var data = new google.visualization.DataTable(response.dataTable);
			  var formatter = new google.visualization.NumberFormat({fractionDigits: 2});

			  formatter.format(data, 1);

			  var table = new google.visualization.Table(document.getElementById('my-table'));
			  table.draw(data);
			});

			report.execute();

        });

		
		$(document).ready(function(){
			$('#chart1').click(function(){
				$("#chart1").children(".chart").toggle();				
				
			});
		});
		
        </script>
		<script type="text/javascript">    
		function ajaxfunction(parent)    {        
			$.ajax({            
				url: 'assets/ajax/property.php',
				type: 'post',			
				data:{'action': 'webproperty', 'account_id': parent},            
				success: function(data) {                
					$("#sub").html(data);            
				}        
			});    
		}
		</script>	
		<?php 
		include("includes/footer.php");
		?>