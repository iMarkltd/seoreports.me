<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/header.php");
global $DBcon;
checkUserLoggedIn();

$api_unit			=	getApiUnitList();
$semrush_api_unit	=	getApiUnit();

?>
	<!-- main-container --> 
	<div class="main-container clearfix">
		<!-- main-navigation -->
		<aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>
			<div class="nav-head">
				<!-- site logo -->
				<a href="home.php" class="site-logo text-uppercase">
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
			<!-- dashboard page -->
			<div class="page page-dashboard">

				<div class="page-wrap">
					<!-- mini boxes -->
					<!--<div class="row">
						<div class="col-md-12 col-sm-6">
							<div class="panel panel-default mb20 mini-box panel-hovered">
								<div class="panel-body">
									<div class="clearfix">
										<div class="info left">
											<h4 class="mt0 text-primary text-bold">Your Categories</h4>
										</div>
									</div>
								</div>
							</div>
						</div>


					</div> -->

					<!-- row -->
					<div class="row">

						<!-- Analytics -->
						<div class="col-md-12">
							<div class="panel panel-default mb20 panel-hovered project-stats table-responsive google_analytics_2">
								<div class="panel-heading with-upgrade-btn">
                                   <div class="panel-heading-data text-capitalize">
									Api Unit Details
                                    </div>
									<div style="float:right">
										<div class="panel-heading-data text-capitalize">Semrush Api Balance <h4><?php echo $semrush_api_unit; ?></h4></div>
									</div>
                                </div>
								<div class="panel-body bs-example"> 
									<table id="example" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer daseboard-tb" width="100%" cellspacing="0">
										<thead>
											<tr>
												<th>Id</th>
												<th>Domain Name</th>
												<th>Fetch Record</th>
												<th>Api Unit Consumed</th>
												<th>Api Name</th>
												<th>Created</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th colspan="4" style="text-align:right">Total:</th>
												<th colspan="2"></th>
											</tr>
										</tfoot>
										<tbody>
										<?php if(!empty($api_unit)) {
												foreach($api_unit as $account) { ?>
										<tr>
											<td><i class="fa fa-star-o"></i></td>
											<td class=""><?php echo $account['domain_url']; ?></td>
											<td class=""><?php echo $account['record_count']; ?></td>
											<td class=""><?php echo ($account['api_status'] == 2 ? $account['record_count'] * 40 : $account['record_count'] * 10); ?></td>
											<td class=""><?php echo ($account['api_type']); ?></td>
											<td class=""><?php echo date('d-m-Y', strtotime($account['created'])); ?></td>
										</tr>
										<?php } } else { ?>
										<tr>
											<td colspan="4">No Record Found!</td>
										</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div> <!-- #end analytics -->

					</div> <!-- #end row -->



				</div> <!-- #end page-wrap -->
			</div>
			<!-- #end dashboard page -->
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
			</div>
		</div>
	</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<script src="assets/scripts/vendors.js"></script>
<script src="assets/scripts/plugins/d3.min.js"></script>
<script src="assets/scripts/plugins/c3.min.js"></script>
<script src="assets/scripts/plugins/screenfull.js"></script>
<script src="assets/scripts/plugins/perfect-scrollbar.min.js"></script>
<script src="assets/scripts/plugins/waves.min.js"></script>
<script src="assets/scripts/plugins/jquery.dataTables.min.js"></script>
<script src="assets/scripts/app.js"></script>
<style type="text/css">
    .bs-example{
    	margin: 20px;
    }
</style>
<?php 
	$result = check_semrush_details($_SESSION['user_id']);
	if($result == 0 ) {
?>
<script type="text/javascript">
$(function(){
	$('#semrush_details').modal();
});
</script>
<?php } ?>

<script type="text/javascript">
$(document).ready(function() {
	waitForMsg();	
	$('#example').DataTable();
});

</script>

<?php require_once("includes/footer.php"); ?>

<script>
function addmsg(type, msg) {
	if(msg <= 0)
	{
		$('.pdf-drop').removeClass('pending-noti');

	}else{
		$('.pdf-drop').addClass('pending-noti');
	}	
}

function waitForMsg() {
	$.ajax({
		type: "POST",
        url: "assets/ajax/ajax-count_notification.php",
		async: true,
		cache: false,
		timeout: 50000,
	   data: {
			action: 'skilled'
	   },
		success: function(data) {
			addmsg("new", data);
			setTimeout(
				waitForMsg,
				7000
			);
		}
	});
};

</script>