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
	//Get user profile data from google
	// Set the access token on the client. 
	$_tokenArray	=	json_encode($_SESSION['service_token']);
	$client->setAccessToken($_tokenArray);
	if ($client->isAccessTokenExpired()) {
		$client->refreshToken($_SESSION['refresh_token']);
	}
	// Create an authorized analytics service object.
	$analytics = new Google_Service_Analytics($client);
	// Get the first view (profile) id for the authorized user.
	$profile 		= getFirstProfileId($analytics);
	$property_id 	= getFirstPropertyId($analytics);
	echo $property_id; 
	// Get the results from the Core Reporting API and print the results.
/**	if($profile != 'false') {
		$results = getResults($analytics, $profile);
		printResults($results);
	}else{
		echo "You dont have google analytics service account. if you want to use this service please enable it" ;
	}
	
**/	
	$myToken = $client->getAccessToken();

} else {
	$redirect_uri = FULL_PATH.'/php-auth.php';
	header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


?>	
        <script>(function(w,d,s,g,js,fs){
        g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
        js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
        js.src='https://apis.google.com/js/platform.js';
        fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
		</script>
	<script src="https://apis.google.com/js/platform.js"></script>
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


<div class="Dashboard Dashboard--full">
  <header class="Dashboard-header">
    <div class="Titles">
      <h1 class="Titles-main" id="view-name">Select a View</h1>
      <div class="Titles-sub">Comparing sessions from
        <b id="from-dates">last week</b>
        to <b id="to-dates">this week</b>
      </div>
    </div>
    <div id="view-selector-container"></div>
  </header>

  <ul class="FlexGrid">
    <li class="FlexGrid-item">
      <div id="data-chart-1-container"></div>
      <div id="date-range-selector-1-container"></div>

    </li>
    <li class="FlexGrid-item">
      <div id="data-chart-2-container"></div>
      <div id="date-range-selector-2-container"></div>
    </li>
  </ul>
</div>
<div id="view-selector-container"></div>
<div id="data-chart-1-container"></div>
<div id="date-range-selector-1-container"></div>
<div id="data-chart-2-container"></div>
<div id="date-range-selector-2-container"></div>
<section>
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
 	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
	<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/view-selector2.js"></script>
	<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/date-range-selector.js"></script>
	<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/components/active-users.js"></script>
       <!-- Load Google's Embed API Library -->
        
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
   * Store a set of common DataChart config options since they're shared by
   * both of the charts we're about to make.
   */
  var commonConfig = {
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:date'
    },
    chart: {
      type: 'LINE',
      options: {
        width: '100%'
      }
    }
  };


  /**
   * Query params representing the first chart's date range.
   */
  var dateRange1 = {
    'start-date': '14daysAgo',
    'end-date': '8daysAgo'
  };


  /**
   * Query params representing the second chart's date range.
   */
  var dateRange2 = {
    'start-date': '7daysAgo',
    'end-date': 'yesterday'
  };


  /**
   * Create a new ViewSelector2 instance to be rendered inside of an
   * element with the id "view-selector-container".
   */
  var viewSelector = new gapi.analytics.ext.ViewSelector2({
    container: 'view-selector-container',
  }).execute();


  /**
   * Create a new DateRangeSelector instance to be rendered inside of an
   * element with the id "date-range-selector-1-container", set its date range
   * and then render it to the page.
   */
  var dateRangeSelector1 = new gapi.analytics.ext.DateRangeSelector({
    container: 'date-range-selector-1-container'
  })
  .set(dateRange1)
  .execute();


  /**
   * Create a new DateRangeSelector instance to be rendered inside of an
   * element with the id "date-range-selector-2-container", set its date range
   * and then render it to the page.
   */
  var dateRangeSelector2 = new gapi.analytics.ext.DateRangeSelector({
    container: 'date-range-selector-2-container'
  })
  .set(dateRange2)
  .execute();


  /**
   * Create a new DataChart instance with the given query parameters
   * and Google chart options. It will be rendered inside an element
   * with the id "data-chart-1-container".
   */
  var dataChart1 = new gapi.analytics.googleCharts.DataChart(commonConfig)
      .set({query: dateRange1})
      .set({chart: {container: 'data-chart-1-container'}});


  /**
   * Create a new DataChart instance with the given query parameters
   * and Google chart options. It will be rendered inside an element
   * with the id "data-chart-2-container".
   */
  var dataChart2 = new gapi.analytics.googleCharts.DataChart(commonConfig)
      .set({query: dateRange2})
      .set({chart: {container: 'data-chart-2-container'}});


  /**
   * Register a handler to run whenever the user changes the view.
   * The handler will update both dataCharts as well as updating the title
   * of the dashboard.
   */
  viewSelector.on('viewChange', function(data) {
    dataChart1.set({query: {ids: data.ids}}).execute();
    dataChart2.set({query: {ids: data.ids}}).execute();

    var title = document.getElementById('view-name');
    title.textContent = data.property.name + ' (' + data.view.name + ')';
  });


  /**
   * Register a handler to run whenever the user changes the date range from
   * the first datepicker. The handler will update the first dataChart
   * instance as well as change the dashboard subtitle to reflect the range.
   */
  dateRangeSelector1.on('change', function(data) {
    dataChart1.set({query: data}).execute();

    // Update the "from" dates text.
    var datefield = document.getElementById('from-dates');
    datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
  });


  /**
   * Register a handler to run whenever the user changes the date range from
   * the second datepicker. The handler will update the second dataChart
   * instance as well as change the dashboard subtitle to reflect the range.
   */
  dateRangeSelector2.on('change', function(data) {
    dataChart2.set({query: data}).execute();

    // Update the "to" dates text.
    var datefield = document.getElementById('to-dates');
    datefield.textContent = data['start-date'] + '&mdash;' + data['end-date'];
  });

});

</script>

<?php 
include("includes/footer.php");
?>