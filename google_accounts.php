<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/header.php");
global $DBcon;
checkUserLoggedIn();

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
							<div class="panel panel-default mb20 panel-hovered project-stats table-responsive google_analytics">
								<div class="panel-heading with-upgrade-btn">
                                   <div class="panel-heading-data text-capitalize">
                                    Google Analytics - Account Mapping
                                    </div>
																		<div class="add-new-acc-btn">
																		<a href="<?php echo FULL_PATH ?>auth.php" id="add_new_google">Add New Google Account </a>
																		</div>

                                </div>

                                <div id="home_loader">
			  				    	<img src="assets/images/home-loader.gif" id="" />
									<p class="text-uppercase text-center">loading <strong><span class="dot"></span><span class="dot"></span><span class="dot"></span></strong></p>
			  				    </div>
								<div class="panel-body bs-example">
									<table class="table">
										<thead>
<!--
											<tr>
												<th>Id</th>
												<th class="col-sm-5">Categories</th>
												<th class="col-sm-1">Options</th>
											</tr>
-->
										</thead>
										<tbody>
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
	<!-- #end theme settings -->




	<!-- Dev only -->

<!-- Add Domain Name-->
<div class="modal fade" id="semrush_details" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Add Domain
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form role="form" name="location_form" id="location_form" enctype="multipart/form-data" method="post">
					<input type="hidden" name="action" value="location" />
                  <div class="form-group md-float-label">
                    <label for="exampleInputEmail1">Domain Name</label>
                      <input type="text" class="form-control" id="domain_name" name="domain_name" placeholder="example" />
                        <span class="help-block" id="error"></span>
                  </div>
                  <div class="form-group md-float-label">
                    <label for="exampleInputEmail1">Domain URL</label>
                      <input type="text" class="form-control" id="domain_url" name="domain_url" placeholder="http://example.com" />
                        <span class="help-block" id="error"></span>
                  </div>
                  <div class="form-group md-float-label">
                    <label for="exampleInputEmail1">Select regional database</label>
					<select name="regional_db" id="regional_db" class="select">
						<option value="nl">"nl" (google.nl)</option>
						<option value="in">"in" (google.co.in)</option>
						<option value="be">"be" (google.be)</option>
						<option value="no">"no" (google.no)</option>
						<option value="ae">"ae" (google.ae)</option>
						<option value="us" selected>"us" (google.com)</option>
						<option value="nz">"nz" (google.co.nz)</option>
						<option value="uk">"uk" (google.co.uk)</option>
						<option value="ca">"ca" (google.ca)</option>
						<option value="ru">"ru" (google.ru)</option>
						<option value="de">"de" (google.de)</option>
						<option value="fr">"fr" (google.fr)</option>
						<option value="es">"es" (google.es)</option>
						<option value="it">"it" (google.it)</option>
						<option value="br">"br" (google.com.br)</option>
						<option value="au">"au" (google.com.au)</option>
						<option value="ph">"ph" (google.com.ph)</option>
						<option value="my">"my" (google.com.my)</option>
						<option value="th">"th" (google.co.th)</option>
						<option value="cy">"cy" (google.com.cy)</option>
						<option value="us.bing">"bing-us" (bing.com)</option>
					</select>
                        <span class="help-block" id="error"></span>

                  </div>
                  <button type="submit" class="btn btn-primary" id="btn-signup" name="submit">Submit</button>
				  <img src="assets/images/ajax-loader.gif" id="img" style="display:none"/ >
                </form>
            </div>

            <!-- Modal Footer -->

        </div>
    </div>
</div>
<!-- Location Modal End-->
<!-- Edit Domain Name-->
<div class="modal fade" id="edit_semrush_details" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Add Domain
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form role="form" name="edit_form" id="edit_form" enctype="multipart/form-data" method="post">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="ids"  id="ids" value="" />
                  <div class="form-group md-float-label">
                    <label for="exampleInputEmail1">Domain Name</label>
                      <input type="text" class="form-control" id="domain_name" name="domain_name" placeholder="example" />
                        <span class="help-block" id="error"></span>
                  </div>
                  <div class="form-group md-float-label">
                    <label for="exampleInputEmail1">Domain URL</label>
                      <input type="text" class="form-control" id="domain_url" name="domain_url" placeholder="http://example.com" />
                        <span class="help-block" id="error"></span>
                  </div>
                  <div class="form-group md-float-label">
                    <label for="exampleInputEmail1">Select regional database</label>
					<select name="regional_db" id="regional_db" class="select">
						<option value="us" selected>"us" (google.com)</option>
						<option value="uk">"uk" (google.co.uk)</option>
						<option value="ca">"ca" (google.ca)</option>
						<option value="nz">"nz" (google.co.nz)</option>
						<option value="ru">"ru" (google.ru)</option>
						<option value="de">"de" (google.de)</option>
						<option value="fr">"fr" (google.fr)</option>
						<option value="es">"es" (google.es)</option>
						<option value="it">"it" (google.it)</option>
						<option value="br">"br" (google.com.br)</option>
						<option value="au">"au" (google.com.au)</option>
						<option value="cy">"cy" (google.com.cy)</option>
						<option value="us.bing">"bing-us" (bing.com)</option>
					</select>
                        <span class="help-block" id="error"></span>

                  </div>
                  <button type="submit" class="btn btn-primary" id="btn-signup" name="submit">Submit</button>
				  <img src="assets/images/ajax-loader.gif" id="img" style="display:none"/ >
                </form>
            </div>

            <!-- Modal Footer -->

        </div>
    </div>
</div>
<!-- Edit Location Modal End-->

<!--Danger Modal Templates-->
<div id="banner-danger" class="modal modal-message modal-danger fade" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-danger">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h1><i class="glyphicon glyphicon-thumbs-up"></i> Alert</h1>
			</div>
			<div class="modal-body">Error: Try Again !</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
			</div>
		</div> <!-- / .modal-content -->
	</div> <!-- / .modal-dialog -->
</div>
<!--End Danger Modal Templates-->

<!--Danger Modal Templates-->
<div id="domain-banner-danger" class="modal modal-message modal-danger fade" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-danger">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h1><i class="glyphicon glyphicon-thumbs-up"></i> Alert</h1>
			</div>
			<div class="modal-body">Error: Domain Name Alerady Registered !</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
			</div>
		</div> <!-- / .modal-content -->
	</div> <!-- / .modal-dialog -->
</div>
<!--End Danger Modal Templates-->


 <!--Success Modal Templates-->
<div id="banner-success" class="modal modal-message modal-success fade" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-success">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h1><i class="glyphicon glyphicon-thumbs-up"></i> Alert</h1>
			</div>
			<div class="modal-body">Success: Your domain add successfully!</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div> <!-- / .modal-content -->
	</div> <!-- / .modal-dialog -->
</div>
<!--End Success Modal Templates-->

 <!--Success Archive Modal Templates-->
<div id="archive-success" class="modal modal-message modal-success fade" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-success">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h1><i class="glyphicon glyphicon-thumbs-up"></i> Alert</h1>
			</div>
			<div class="modal-body">Success: Your domain archive successfully!</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div> <!-- / .modal-content -->
	</div> <!-- / .modal-dialog -->
</div>
<!--End Success Archive Modal Templates-->
<!--Remove Archive Modal Templates-->
<div id="archive-remove" class="modal modal-message modal-success fade" style="display: none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-success">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h1><i class="glyphicon glyphicon-thumbs-up"></i> Alert</h1>
			</div>
			<div class="modal-body">Success: Your domain restore successfully!</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div> <!-- / .modal-content -->
	</div> <!-- / .modal-dialog -->
</div>
<!--End Remove Archive Modal Templates-->


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
	jQuery.ajax({
	  action:  'personal',
	  type:    "POST",
	  url:     "assets/ajax/ajax-google_account.php",
	  success: function(result) {
	  	$('#home_loader').css('display', 'none');
		$('.bs-example').html(result);
		$('#example').DataTable( {
			"pageLength": 100,
		});
	  }
	});



});

//    jQuery(window).load(function() {
//
//
//var kuchbhi =  jQuery( "#example_filter input" ).length;
//        alert(kuchbhi);

//    jQuery("#example_filter input").focus(function() {
//       jQuery("#example_filter").addClass("focused");
//       });
//
//    jQuery("#example_filter input").blur(function() {
//       jQuery("#example_filter").removeClass("focused");
//       });

// });
$(document).on("click", ".ssss", function () {
     var myBookId = $(this).data('id');
     $(".modal-body #share_id").val( myBookId );
	$.ajax({
	  action:  'email_data',
	  type:    "POST",
	  url:     "assets/ajax/ajax-email-data.php",
	  data:    {action: 'email_data', share_id: myBookId},
	  success: function(result) {
		var status	=	result['status'];
		var analytic_id	=	result['analytic_id'];
		if(status == 'success'){
			$('#subject').val(result['subject']);
			$('#to').val(result['email_to']);
			$('.summernote').eq(0).code(result['email_message']);
			$('#message').val(result['email_message']);
			$('#mail_from').val(result['email_sender']);
			$('#mail_sender_name').val(result['email_sender_name']);
		}
	  }
	});
});
</script>

	<div class="alert email-error" style="display:none">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">×</span>
		</button>
		<div>Getting Error! Please try again.</div>
	</div>
	<div class="alert email-success" style="display:none">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">×</span>
		</button>
		<div>Well done! Mail Sent Successfully.</div>
	</div>

	<div class="alert email-queue-msg" style="display:none">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">×</span>
		</button>
		<div>Hi! Your Mail in queue. We will update you soon.</div>
	</div>

	<div class="alert message_saved" style="display:none">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">×</span>
		</button>
		<div>Well done! Message Saved Successfully.</div>
	</div>

	<div class="alert message_errror" style="display:none">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">×</span>
		</button>
		<div>Getting Error! Please try again.</div>
	</div>

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
$(document).on('click', '.key_word', function(e){
	e.preventDefault();
	var request_id = $(this).data('id');
	var href_value = '<?php echo FULL_PATH ?>auth_google.php?ids='+request_id;
	var href_value1 = '<?php echo FULL_PATH ?>auth_analytics.php?ids='+request_id;
	$("#click_here").attr("href", href_value);
	$("#add_google_account").attr("href", href_value1);
	$(".onchange").data("id", request_id);
	$("#analytic_account").data("id", request_id);
	$("#analytic_view_id").data("id", request_id);
	$('#google_account_model').modal();
});


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