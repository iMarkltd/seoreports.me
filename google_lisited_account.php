<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/header.php");
global $DBcon;
checkUserLoggedIn();

$google_account		=	googleAccountList();

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
									Refresh Google Analytics Account Data
                                    </div>

                                </div>
								<div class="panel-body bs-example">
									<table id="example" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer daseboard-tb" width="100%" cellspacing="0">
										<thead>
											<tr>
												<th>Id</th>
												<th class="col-sm-5">Account Name</th>
												<th class="col-sm-5">Account Email</th>
												<th class="col-sm-1">Action</th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($google_account as $account) { ?>
										<tr>
											<td><i class="fa fa-star-o"></i></td>
											<td class=""><?php echo $account['first_name'].' '.$account['last_name']; ?></td>
											<td class=""><?php echo $account['email']; ?></td>
											<td>
												<ul>
													<li><a  class="btn btn-icon-circle icon waves-effect archive_row revoke_data" href="javascript:;" data-placement="top" data-hover="tooltip" data-original-title="Refresh Analytics Data" data-id="<?php echo $account['id']?>"><i class="fa fa-retweet"></i> </a> <img src="assets/images/ajax-loader.gif" class="update_google_loader" style="display:none" /></li>
												</ul>
											</td>
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

$(document).on("click", ".revoke_data", function (e) {
	e.preventDefault();
	$(this).hide();
	$(this).parents('tr').find('.update_google_loader').show();
	var self		= $(this);
	var requestId 	= $(this).data('id');
	var input_id 	= $(this).data('row');
	$.ajax({
	  type:    "POST",
	  url:     "assets/ajax/ajax-google_lisited_account.php",
	  data:    {action: 'update_google_account', request_id: requestId},
	  dataType: 'json',
	  success: function(result) {
		  console.log(result)
		var status	=	result['status'];
		self.show();
		self.parents('tr').find('.update_google_loader').hide();
		if(status == 'success'){
			Command: toastr["success"]('Congartulation! Account updated successfully');
		}else{
			Command: toastr["error"]('Please try again !');
		}
	  }
	});
});

</script>