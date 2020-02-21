<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/new-header.php");
global $DBcon;
checkUserLoggedIn();

// comman function
$domain_details	    =	getUserDomainDetails($_REQUEST['id']);
// end comman function

$result             =   getUserDomainDetails($_REQUEST['id']);
$getProfileData     =   getUserProfileData($_REQUEST['id']);
$toggle_module		=   getToggleModule($_REQUEST['id']);
if(empty($toggle_module)){
    $toggle_module  =   array();
}
$google_account		=	googleAccountList();

?>
<!-- main-container -->
<div class="main-container clearfix">


    <!-- main-navigation -->
    <aside class="nav-wrap" id="site-nav" data-perfect-scrollbar>

        <!-- Site nav (vertical) -->
        <?php require_once("includes/new-nav-sidebar.php"); ?>

    </aside>
    <!-- #end main-navigation -->

    <!-- content-here -->
    <div class="content-container container" id="content">
        <!-- dashboard page -->
        <div class="page page-dashboard">

            <div class="page-wrap">
                <!-- mini boxes -->

                <!-- row -->
                <div class="row">

                    <!-- Analytics -->
                    <div class="col-md-12">

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>Settings</h5>
                                </div>
                            </div>
                            <div class="settings-tab">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#settingTab1">General Settings</a>
                                    </li>
                                    <li><a data-toggle="tab" href="#settingTab2">White Label</a></li>
                                    <li><a data-toggle="tab" href="#settingTab3">Integration</a></li>
                                    <li><a data-toggle="tab" href="#settingTab4">Customize Report</a></li>
                                </ul>

                                <div class="tab-content">

                                    <div id="settingTab1" class="tab-pane fade in active">
                                        <form class="d-flex" name="general_settings" method="post" id="general_settings" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="general_settings">
                                            <input type="hidden" name="request_id" value="<?php echo $_REQUEST['id']?>">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Target Location</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                    <select name="regional_db" id="regional_db" class="select form-control">
                                                        <option value="ae" <?php if($getProfileData['regional_db'] == 'ae') echo 'selected'; ?>>"ae" (google.ae)</option>
                                                        <option value="au" <?php if($getProfileData['regional_db'] == 'au') echo 'selected'; ?>>"au" (google.com.au)</option>
                                                        <option value="be" <?php if($getProfileData['regional_db'] == 'be') echo 'selected'; ?>>"be" (google.be)</option>
                                                        <option value="br" <?php if($getProfileData['regional_db'] == 'br') echo 'selected'; ?>>"br" (google.com.br)</option>
                                                        <option value="ca" <?php if($getProfileData['regional_db'] == 'ca') echo 'selected'; ?>>"ca" (google.ca)</option>
                                                        <option value="cy" <?php if($getProfileData['regional_db'] == 'cy') echo 'selected'; ?>>"cy" (google.com.cy)</option>
                                                        <option value="de" <?php if($getProfileData['regional_db'] == 'de') echo 'selected'; ?>>"de" (google.de)</option>
                                                        <option value="es" <?php if($getProfileData['regional_db'] == 'es') echo 'selected'; ?>>"es" (google.es)</option>
                                                        <option value="fr" <?php if($getProfileData['regional_db'] == 'fr') echo 'selected'; ?>>"fr" (google.fr)</option>
                                                        <option value="gr" <?php if($getProfileData['regional_db'] == 'gr') echo 'selected'; ?>>"gr" (google.gr)</option>
                                                        <option value="ie" <?php if($getProfileData['regional_db'] == 'ie') echo 'selected'; ?>>"ie" (google.ie)</option>
                                                        <option value="in" <?php if($getProfileData['regional_db'] == 'in') echo 'selected'; ?>>"in" (google.co.in)</option>
                                                        <option value="it" <?php if($getProfileData['regional_db'] == 'it') echo 'selected'; ?>>"it" (google.it)</option>
                                                        <option value="ma" <?php if($getProfileData['regional_db'] == 'ma') echo 'selected'; ?>>"ma" (google.co.ma)</option>
                                                        <option value="my" <?php if($getProfileData['regional_db'] == 'my') echo 'selected'; ?>>"my" (google.com.my)</option>
                                                        <option value="nl" <?php if($getProfileData['regional_db'] == 'nl') echo 'selected'; ?>>"nl" (google.nl)</option>
                                                        <option value="no" <?php if($getProfileData['regional_db'] == 'no') echo 'selected'; ?>>"no" (google.no)</option>
                                                        <option value="nz" <?php if($getProfileData['regional_db'] == 'nz') echo 'selected'; ?>>"nz" (google.co.nz)</option>
                                                        <option value="pl" <?php if($getProfileData['regional_db'] == 'pl') echo 'selected'; ?>>"pl" (google.pl)</option>
                                                        <option value="ph" <?php if($getProfileData['regional_db'] == 'ph') echo 'selected'; ?>>"ph" (google.com.ph)</option>
                                                        <option value="ru" <?php if($getProfileData['regional_db'] == 'ru') echo 'selected'; ?>>"ru" (google.ru)</option>
                                                        <option value="sa" <?php if($getProfileData['regional_db'] == 'sa') echo 'selected'; ?>>"sa" (google.com.sa)</option>
                                                        <option value="se" <?php if($getProfileData['regional_db'] == 'se') echo 'selected'; ?>>"se" (google.se)</option>
                                                        <option value="sg" <?php if($getProfileData['regional_db'] == 'sg') echo 'selected'; ?>>"sg" (google.com.sg)</option>
                                                        <option value="th" <?php if($getProfileData['regional_db'] == 'th') echo 'selected'; ?>>"th" (google.co.th)</option>
                                                        <option value="us" <?php if($getProfileData['regional_db'] == 'us') echo 'selected'; ?>>"us" (google.com)</option>
                                                        <option value="us.bing" <?php if($getProfileData['regional_db'] == 'us.bing') echo 'selected'; ?>>"bing-us" (bing.com)</option>
                                                        <option value="uk" <?php if($getProfileData['regional_db'] == 'uk') echo 'selected'; ?>>"uk" (google.co.uk)</option>
                                                        <option value="za" <?php if($getProfileData['regional_db'] == 'za') echo 'selected'; ?>>"za" (google.co.za)</option>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Website Name </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="domain_name" class="form-control" placeholder="Your site name here" value="<?php echo $getProfileData['domain_name']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Website URL</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="domain_url" class="form-control"  value="<?php echo $getProfileData['domain_url']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Client Name</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="clientName" class="form-control" placeholder="John Doe" value="<?php echo $getProfileData['clientName']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-left row">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button type="submit" class="btn btn-default"><i class="fa fa-paper-plane-o"></i> Submit</button>
                                                    <button type="submit" class="btn btn-white"><i class="fa fa-refresh"></i> Reset</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div id="settingTab2" class="tab-pane fade">
                                        <form class="d-flex" name="profile_details" method="post" id="profile_details" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="profile_info">
                                            <input type="hidden" name="request_id" value="<?php echo $_REQUEST['id']?>">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>
                                                            Agency Name
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="company_name" class="form-control" placeholder="Agency name here" value="<?php echo $getProfileData['company_name']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>
                                                            Agency Owner Name
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="client_name" class="form-control" placeholder="Agency owner name here" value="<?php echo $getProfileData['client_name']; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>
                                                            Agency Phone
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" name="mobile" placeholder="(xxx) xxx-xxxx" value="<?php echo $getProfileData['contact_no']; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>
                                                            Agency Email
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control" placeholder="agency@email.com" name="email" value="<?php echo $getProfileData['email']; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>
                                                            Agency Logo
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8 customFile">
                                                        <input id="logo-2" name="logo" type="file" class="file-loading">
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="text-left row">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button type="submit" class="btn btn-default"><i
                                                            class="fa fa fa-paper-plane-o"></i> Submit</button>
                                                    <button type="submit" class="btn btn-white"><i
                                                            class="fa fa-refresh"></i> Reset</button>
                                                </div>

                                            </div>

                                        </form>
                                    </div>

                                    <div id="settingTab3" class="tab-pane fade">
                                        <div class="integration-box">
                                            <div class="box <?php if(!empty($result)) echo 'active';?>">
                                                <figure>
                                                    <img src="/assets/images/ReportGoogleAnaLyticsGoals.png" alt="">
                                                </figure>
                                                <h5>Connect Google Analytics</h5>
                                                <p>Select an existing account or connect a new Google Account</p>
                                                <a href="#" pd-popup-open="PopupAddGoogleAnalyticsAccount" class="btn">Add Google Analytics Account</a>
                                            </div>
                                            <div class="box">
                                                <figure><img src="/assets/images/google-logo-icon.png" alt=""></figure>
                                                <h5>Search Console</h5>
                                                <p>Select an existing account or connect a new Search Console Account </p>
                                                <a href="#" pd-popup-open="PopupAddGoogleAnalyticsAccount" class="btn">Add Google Search Console Account</a>
                                            </div>
                                        </div>
                                        <table id="example" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer daseboard-tb" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th class="col-sm-2">Project Name</th>
                                                    <th class="col-sm-3">Analytic Account</th>
                                                    <th class="col-sm-3">Account ID</th>
                                                    <th class="col-sm-1">Property ID</th>
                                                    <th class="col-sm-1">View ID</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($result)) {
                                                        $checkDownloadLink		=	checkDownloadLink($result['id']);
                                                        if(!empty($result['google_account_id'])) {
                                                                $google_account_details 		=	googleAccessDetails($result['google_account_id']);
                                                                $google_analytics_details 		=	getGoogleAnalyticsDetails($result['google_analytics_id']);
                                                                $google_property_details 		=	getGoogleAnalyticsDetails($result['google_property_id']);
                                                                $google_profile_details 		=	getGoogleAnalyticsProfileDetails($result['google_profile_id']);
                                                        }
                                                ?>
                                                    <tr>
                                                        <td class="" >
                                                            <label for="name" class="control-label">
                                                                <a  href="test_seo_analytics_chart.php?id=<?php echo $result['id']; ?>" class="text-info" data-id="<?php echo $result['id']; ?>"><?php echo $result['domain_name']?></a>
                                                            </label>
                                                        </td>
                                                        <td id="google_account_id" data-id="<?php echo $result['google_account_id']; ?>"><?php if(!empty($result['google_account_id'])) echo $google_account_details['email']; else echo '---'; ?></td>
                                                        <td><?php if(!empty($result['google_analytics_id'])) echo $google_analytics_details['category_name']; else echo '---'; ?></td>
                                                        <td><?php if(!empty($result['google_property_id'])) echo $google_property_details['category_name']; else echo '---'; ?></td>
                                                        <td><?php if(!empty($result['google_profile_id'])) echo $google_profile_details['category_name']; else echo '---'; ?></td>
                                                        <td>
                                                        <ul>
                                                            <li><a  class="btn btn-icon-circle icon waves-effect " href="javascript:;" data-placement="top" data-hover="tooltip" data-original-title="Revoke" id="revoke_data" data-id="<?php echo $result['id']?>"><i class="fa fa-times" ></i></a></li>
                                                        </ul>

                                                        </td>
                                                    </tr>
                                                    <?php } else { ?>
                                                        <tr><td colspan="5">Not Found Record</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                    </div>

                                    <div id="settingTab4" class="tab-pane fade">

                                        <p>Show/Hide section on the <span>View Key</span> and <span>PDF File</span></p>
                                        <form class="d-flex">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Project</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Summary
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="company_note"  type="checkbox" <?php if(!in_array('company_note', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Search Console</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Google Graph
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="search_console" type="checkbox" <?php if(!in_array('search_console', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Organic Keyword Growth (12 months)</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Semrush Graph
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="organic_graph" type="checkbox" <?php if(!in_array('organic_graph', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Organic Traffic Growth (6 months) </h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Google Graph
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="organic_traffic" type="checkbox" <?php if(!in_array('organic_traffic', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Organic Keywords </h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Summary
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="organic_keywords_summary" <?php if(!in_array('organic_keywords_summary', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 mb-2"></div>

                                                <div class="col-sm-4">
                                                    <label>
                                                        Semrush Table
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="organic_keywords_table" <?php if(!in_array('organic_keywords_table', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Sample Backlinks </h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Summary
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="organic_backlink_summary" <?php if(!in_array('organic_backlink_summary', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 mb-2"></div>

                                                <div class="col-sm-4">
                                                    <label>
                                                        Semrush Table
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="organic_backlink_table" <?php if(!in_array('organic_backlink_table', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Google Analytics Goal Completion</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Summary
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="google_goal_summary" <?php if(!in_array('google_goal_summary', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 mb-2"></div>

                                                <div class="col-sm-4">
                                                    <label>
                                                        Google Table
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="google_goal_table" <?php if(!in_array('google_goal_table', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Notes</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Summary
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="summary_note" <?php if(!in_array('summary_note', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>



                                        </form>
                                    </div>

                                </div>
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
<div class="site-settings clearfix hidden-xs" style="display:none;">
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
                            <input type="checkbox" id="fixedHeader">
                            <span>&nbsp;</span>
                        </label>
                    </div>
                </li>
                <li class="clearfix mb10">
                    <div class="left small">Nav Full</div>
                    <div class="md-switch right">
                        <label>
                            <input type="checkbox" id="navFull">
                            <span>&nbsp;</span>
                        </label>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- #end theme settings -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link href="assets/styles/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="assets/scripts/vendors.js"></script>
<script src="//code.highcharts.com/highcharts.js"></script>
<script src="assets/scripts/jquery.validate.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js" type="text/javascript"></script>
<script src="assets/scripts/modal.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script src="assets/scripts/canvas-to-blob.min.js" type="text/javascript"></script>
<script src="assets/scripts/fileinput.min.js"></script>

<script type="text/javascript">
    $(document).on("click", ".ssss", function() {
        var myBookId = $(this).data('id');
        $(".modal-body #share_id").val(myBookId);
        $.ajax({
            action: 'email_data',
            type: "POST",
            url: "assets/ajax/ajax-email-data.php",
            data: {
                action: 'email_data',
                share_id: myBookId
            },
            success: function(result) {
                var status = result['status'];
                var analytic_id = result['analytic_id'];
                if (status == 'success') {
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

    $("#avatar-2").fileinput({
        maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-2',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: '<img src="assets/uploads/default_avatar_male.jpg" alt="Your Avatar" style="width:160px"><h6 class="text-muted">Click to select</h6>',
        layoutTemplates: {
            main2: '{preview} {remove} {browse}'
        },
        allowedFileExtensions: ["jpg", "png", "gif"],
        uploadUrl: "assets/ajax/ajax-user_info.php", // server upload action
        uploadAsync: true,
        minFileCount: 1,
        maxFileCount: 1,
        overwriteInitial: false,
        initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup


        <?php
        if (!empty(checkUserProfileImage())) {
            ?>
            initialPreview: ["<?php $url = checkUserProfileImage();
                    if (!empty($url)) echo $url['return_path']; ?> "],
                    initialPreviewFileType : 'image', // image is the default and can be overridden in config below
                    initialPreviewConfig: [{
                        caption: "<?php  $url = checkUserProfileImage(); if(!empty($url))
                        echo $url['file_name']; ?> ",
                        size : 576237,
                        width: "120px",
                        url: "assets/ajax/ajax-user_info.php",
                        key: 'user_image_delete',
                        extra: {
                            action: 'user_image_delete'
                        }
                    }], <?php
                } ?>
                uploadExtraData : {
                    action: "user_image_upload",
                }
        });
    $('#avatar-2').on('fileuploaderror', function(event, data, previewId, index) {
        var form = data.form,
            files = data.files,
            extra = data.extra,
            response = data.response,
            reader = data.reader;

    });


    $('#avatar-2').on('fileuploaded', function(event, data, previewId, index) {
        var image_name = (data.files[0]['name']);
        var image_id = '<?php echo @$_SESSION['user_id '] ?>';
        $('.user_pic').attr('src', "<?php echo FULL_PATH; ?>assets/ajax/uploads/" + image_id + "/profile/" + image_name);

    });


    $("#logo-2").fileinput({
        maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#logo-avatar-errors-2',
        msgErrorClass: 'alert alert-block alert-danger',
        layoutTemplates: {
            main2: '{preview} {remove} {browse}'
        },
        allowedFileExtensions: ["jpg", "png", "gif"],
        uploadUrl: "assets/ajax/ajax-profile_logo.php", // server upload action
        uploadAsync: true,
        minFileCount: 1,
        maxFileCount: 1,
        overwriteInitial: false,
        initialPreviewAsData: true, // identify if you are sending preview data only and not the raw markup
        <?php
        if (!empty($_REQUEST['id']) && !empty(checkProfileLogo(@$_REQUEST['id']))) {
            ?>
            initialPreview: [
                    "<?php $url = checkProfileLogo(@$_REQUEST['id']); if(!empty($url)) echo $url['return_path']; ?> "
                ],
                initialPreviewFileType: 'image', // image is the default and can be overridden in config below
                initialPreviewConfig: [{
                    caption: "<?php  $url = checkProfileLogo(@$_REQUEST['id']); if(!empty($url)) echo $url['file_name']; ?>",
                    size: 576237,
                    width: "120px",
                    url: "assets/ajax/ajax-profile_logo.php",
                    key: 'logo_image_delete',
                    extra: {
                        action: 'logo_image_delete',
                        request_id: '<?php echo @$_REQUEST['
                        id ']; ?>'
                    }
                }], <?php
        } ?>
        uploadExtraData : {
            action: "profile_logo_upload",
            request_id: "<?php echo @$_REQUEST['id']; ?>"
        }
    });

    $('#logo-2').on('fileuploaderror', function(event, data, previewId, index) {
        var form = data.form,
            files = data.files,
            extra = data.extra,
            response = data.response,
            reader = data.reader;

    });


    $('#logo-2').on('fileuploaded', function(event, data, previewId, index) {
        var image_name = (data.files[0]['name']);
        var image_id = '<?php echo @$_SESSION['user_id ']?>';
    });

    $("#profile_details").validate({
        rules: {
            company_name: {
                required: true
            },
            email: {
                required: true,
                validate_email: true
            },
            mobile: {
                required: true
            }
        },
        messages: {
            company_name: {
                required: "Please provide company name address",
            },
            email: {
                required: "Please provide email address",
                validate_email: "Email address is not valid"
            },
            mobile: {
                required: "Please provide contact number",
            }
        },
        submitHandler: function(form) {
            var new_data = $('#profile_details').serializeArray();
            $.ajax({
                action: 'security',
                type: "POST",
                url: "assets/ajax/profile_info.php",
                data: new_data,
                dataType: 'json',
                success: function(result) {
                    var status = result['status'];
                    var analytic_id = result['analytic_id'];
                    if (status == 'success') {
                        $('#logo_modal').modal('hide');
                        Command: toastr["success"]('Your detail saved successfully');
                    } else {
                        Command: toastr["error"]('Please try again getting error');

                    }
                }
            });
        }
    });

    $('#general_settings').on('submit', function(e){
        e.preventDefault();
        var new_data = $('#general_settings').serializeArray();
        $.ajax({
            type: "POST",
            url: "assets/ajax/profile_info.php",
            data: new_data,
            dataType: 'json',
            success: function(result) {
                var status = result['status'];
                var analytic_id = result['analytic_id'];
                if (status == 'success') {
                    $('#logo_modal').modal('hide');
                    Command: toastr["success"]('Your detail saved successfully');
                } else {
                    Command: toastr["error"]('Please try again getting error');

                }
            }
        });
    });

    $(document).on("submit", "#google-list", function(e){
        e.preventDefault();
        $('.select_box_loader').show();
        var data_id		=	$('#google_account').val();
        var request_id	=	<?php echo $_REQUEST['id']?>;
        jQuery.ajax({
            type:	'POST',
            url: 	"assets/ajax/ajax_google_view_details.php",
            data:	{action: 'update_select_div', request_id: request_id, analytic_id: data_id},
            success: function(result) {
                console.log(result);
                $('.select_box_loader').hide();
                $('#analytic_account').html(result);
                $('#analytic_account').selectpicker('refresh');
                $('#analytic_account').selectpicker();
                var li 		=	'<option value=""><--Select Property --></option>';
                $('#analytic_property').html(li);
                var li 		=	'<option value=""><--Select View ID  --></option>';
                $('#analytic_view_id').html(li);
            }
        });
    });

    $(document).on("change", "#analytic_account", function (e) {
        var property_id = $(this).val();
        if(property_id != '') {
            $.ajax({
                type:    "POST",
                url:     "assets/ajax/ajax-getviewData.php",
                data:    {action: 'property_data', property_id: property_id},
                success: function(result) {
                    $('#analytic_property').html(result);
                    $('#analytic_property').selectpicker('refresh');
                    $('#analytic_property').selectpicker();
                }
            });
        }
    });

    $(document).on("change", "#analytic_property", function (e) {
        var property_id = $(this).val();
        $.ajax({
        type:    "POST",
        url:     "assets/ajax/ajax-getviewData.php",
        data:    {action: 'property_view_data', property_id: property_id},
        success: function(result) {
            $('#analytic_view_id').html(result);
            $('#analytic_view_id').selectpicker('refresh');
            $('#analytic_view_id').selectpicker();
        }
        });
    });

    $(document).on("submit", "#save_view_data", function (e) {
        e.preventDefault();
        var analytic_view_id		= $('#analytic_view_id').val();
        var analytic_property_id	= $('#analytic_property').val();
        var analytic_account_id 	= $('#analytic_account').val();
        var google_account_id	 	= $('#google_account').val();
        var request_id 				= <?php echo $_REQUEST['id']; ?>;
        $.ajax({
        type:    "POST",
        url:     "assets/ajax/ajax-getviewData.php",
        data:    {action: 'save_property_id', analytic_view_id: analytic_view_id, google_account_id: google_account_id, analytic_property_id: analytic_property_id, analytic_account_id: analytic_account_id, request_id: request_id},
        success: function(result) {
            var status	=	result['status'];
            if(status == 'success'){

            }else{
            }
        }
        });
    });

    $(document).on('click','#revoke_data',function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var self =$(this);
        $.ajax({
            type:	"POST",
            url:	"assets/ajax/ajax-domain.php",
            data:	{action: 'remove_analaytic_data', request_ids: id },
            dataType: 'json',
            success: function(result) {
                var status	=	result['status'];
                if(status == 'empty') {
                Command: toastr["error"](" Please try again");
                } else if(status == 'success') {
                    Command: toastr["success"](" Congratualtion: Account revoke successfully");
                location.reload(); // then reload the page.(3)
                }
            }
        });
    })


    $(document).on('change', '.check-toggle', function(e) {
        var module = $(this).attr('data-block');
        var request_id_key = '<?php echo $_REQUEST['id']; ?>';
        if ($(this).prop("checked") == true)
            var status = 1;
        else
            var status = 0;

        jQuery.ajax({
            type: 'POST',
            url: "assets/ajax/toggle_module.php",
            data: {
                action: 'toggle_module',
                request_id: request_id_key,
                module: module,
                status: status
            },
            dataType: 'json',
            success: function(result) {}
        });
    })

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


<div class="popup" pd-popup="PopupAddGoogleAnalyticsAccount">
    <div class="popup-inner">
        <div class="popup-header">
            <h1>Connect Your Services: Google Analytics</h1>
        </div>
        <div class="popup-body">
            <h2>Connect a New or Existing Account</h2>
            <p>Choose an existing account below, or click the "Add New Account" button to connect a new account.</p>
            <form method="post" name="google-list" id="google-list">
                <div class="form-group">
                    <label>Choose an existing account:</label>
                    <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="google_account" data-live-search="true" data-dropup-auto="false"  data-id="">
                        <optgroup>
                            <option value="">Please Select</option>
                            <?php foreach($google_account as $account) { ?>
                                <option value="<?php echo $account['id'];?>"><?php echo $account['email'] ?></option>
                            <?php } ?>
                        </optgroup>
                    </select>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-default  waves-effect google-list">
                        Use This Account
                    </button>
                </div>
            </form>
            <div class="select-view-cover">
                <h1 class="Titles-main" id="view-name">Select a View</h1>
                <form name="save_view_data" id="save_view_data" method="post" >
                    <div id="view-selector" class="chart">
                        <select name="analytic_account" id="analytic_account" class="selectpicker" data-live-search="true" data-dropup-auto="false" data-id="<?php echo $_REQUEST['id']; ?>">
                            <option value=""><--select account--></option>
                        </select>
                        <select name="analytic_property" id="analytic_property" class="selectpicker">
                            <option value=""><--select property--></option>
                        </select>
                        <select name="analytic_view_id" id="analytic_view_id" data-id="<?php echo $_REQUEST['id']; ?>" class="selectpicker">
                            <option value=""><--select view id--></option>
                        </select>

                    </div>
                    <button type="submit" id="submit_button" value="Save" >Save</button>
                </form>
            </div>
            <div class="select_box_loader" style="display:none;">
                <div class="loader-msg">Please wait it will take maximum 60 seconds</div>
                <img src="<?php echo 'assets/images/squares.gif'; ?>" />
            </div>
            <div class="select_box_loader" style="display:none;">
                <div class="loader-msg">Please wait it will take maximum 60 seconds</div>
                <img src="<?php echo 'assets/images/squares.gif'; ?>" />
            </div>
        </div>
        <div class="popup-footer">
            <a class="btn btn-white" pd-popup-close="PopupAddGoogleAnalyticsAccount" href="#">Close</a>
            <a class="btn btn-green" href="<?php echo FULL_PATH ?>auth.php?id=<?php echo $_REQUEST['id']; ?>" id="add_new_google">Add New Account</a>
        </div>

    </div>
</div>


<div class="popup" pd-popup="PopupAddSearchConsoleAccount">
    <div class="popup-inner">
        <div class="popup-header">
            <h1>Connect Your Services: Search Console</h1>
        </div>
        <div class="popup-body">
            <h2>Connect a New or Existing Account</h2>
            <p>Choose an existing account below, or click the "Add New Account" button to connect a new account.</p>
            <form>
                <div class="form-group">
                    <label>Choose an existing account:</label>
                    <select class="form-control">
                        <option>List of projects here</option>
                        <option>List of projects here</option>
                        <option>List of projects here</option>
                    </select>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-default  waves-effect">
                        Use This Account
                    </button>
                </div>
            </form>
        </div>
        <div class="popup-footer">
            <a class="btn btn-white" pd-popup-close="PopupAddSearchConsoleAccount" href="#">Close</a>
            <a class="btn btn-green" href="#">Add New Account</a>
        </div>

    </div>
</div>


<?php require_once("includes/nav-footer.php"); ?>
