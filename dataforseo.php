<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("includes/config.php");
require_once("includes/functions.php");
require_once('vendor/autoload.php');
require_once('DataforceApi/RestClient.php');
require_once("includes/new-header.php");

global $DBcon;

$api_unit				=	getDFSApiUnitList();
$dataforseo_api_unit	=	'';

try {
	$client 				= 	new RestClient('https://api.dataforseo.com/', null, 'ishan@siliconbeachdigital.com', 'TB5IdHh0B28vagDB');
    $user_get_result 		= 	$client->get('v2/cmn_user');
	$dataforseo_api_unit	=	$user_get_result['results'][0]['balance'];

} catch (RestClientException $e) {
    echo "\n";
    print "HTTP code: {$e->getHttpCode()}\n";
    print "Error code: {$e->getCode()}\n";
    print "Message: {$e->getMessage()}\n";
    print  $e->getTraceAsString();
    echo "\n";
    exit();
}



?>
	<!-- main-container -->
	<div class="main-container clearfix">
		<!-- main-navigation -->
		<aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>
			<?php require_once("includes/new-nav-sidebar.php"); ?>

		</aside>
		<!-- #end main-navigation -->

		<!-- content-here -->
		<div class="content-container" id="content">
			<!-- dashboard page -->
			<div class="page page-dashboard">

				<div class="page-wrap">
					<div class="row">

						<!-- Analytics -->
						<div class="col-md-12">
							<div class="panel panel-default mb20 panel-hovered project-stats table-responsive google_analytics_2">
								<div class="panel-heading with-upgrade-btn">
                                   <div class="panel-heading-data text-capitalize">
									Api Unit Details
                                    </div>
									<div style="float:right">
										<div class="panel-heading-data text-capitalize">DataForSeo Api Balance <h4><?php echo $dataforseo_api_unit; ?></h4></div>
									</div>
                                </div>
								<div class="panel-body bs-example"> 
									<table id="example" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer daseboard-tb" width="100%" cellspacing="0">
										<thead>
											<tr>
												<th>Id</th>
												<th>Domain Name</th>
												<th>Keyword Name</th>
												<th>Api Name</th>
												<th>Api Unit Consumed</th>
												<th>Created</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
											<tr>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</tfoot>
										<tbody>
										<?php if(!empty($api_unit)) {
												foreach($api_unit as $account) { ?>
										<tr>
											<td><i class="fa fa-star-o"></i></td>
											<td class=""><?php echo $account['domain_name']; ?></td>
											<td class=""><?php echo $account['keyword']; ?></td>
											<td class=""><?php echo ($account['api_name']); ?></td>
											<td class=""><?php echo ( $account['api_name'] == 'New Keyword - Search Volumne Api' ? $account['api_credit'] * 7.5 : $account['api_credit'] ); ?></td>
											<td class=""><?php echo date('d-m-Y', strtotime($account['created_at'])); ?></td>
										</tr>
											<?php } 
											} else { ?>
										<tr>
											<td colspan="6">No Record Found!</td>
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<script src="assets/scripts/vendors.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
<script src="<?php echo FULL_PATH; ?>assets/scripts/plugins/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.flash.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('#example').DataTable();

//likha kefiDiwa

</script>

<?php require_once("includes/nav-footer.php"); ?>

