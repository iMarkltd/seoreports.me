<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once('vendor/autoload.php');

global $DBcon;
checkUserLoggedIn();

// comman function
$domain_details	    =	getUserDomainDetails($_REQUEST['id']);
$client         	=   new Google_Client();

$searchDateRange    =   getModuleDateRange($_REQUEST['id'], 'search_console');

if(!empty($searchDateRange)){
    //print_r($searchDateRange); exit;
    $start_data                 =   date('Y-m-d', strtotime($searchDateRange['start_date']));
    $end_data                   =   date('Y-m-d', strtotime($searchDateRange['end_date']));
}else {
    $start_data                 =   date('Y-m-d', strtotime('-1 year'));
    $end_data                   =   date('Y-m-d');
}

$searchConsole  	=	googleSearchConsole($_REQUEST['id'], $domain_details['domain_url'], $domain_details['google_account_id'], $start_data, $end_data);
$keyWordSearch      =   getKeyWordSearch($_REQUEST['id']);

//print_r($domain_details); exit;
$onSiteInput   	    =	getActivateTask(1);
$offSiteInput  	    =	getActivateTask(2);
$anlSiteInput  	    =	getActivateTask(3);
$tagStatus   	    =	getActivateStatus();
$faqData            =   getFaqData();
if(!empty($keyWordSearch)) {
    $keyWordSearch           =   implode(",", array_column($keyWordSearch, "keyword"));
}else
    $keyWordSearch           =   '';

if(!empty($onSiteInput)) {
    $onSiteInput           =   implode(",", array_column($onSiteInput, "category_name"));
}else
    $onSiteInput           =   '';

if(!empty($offSiteInput)) {
    $offSiteInput           =   implode(",", array_column($offSiteInput, "category_name"));
}else
    $offSiteInput           =   '';

if(!empty($anlSiteInput)) {
    $anlSiteInput           =   implode(",", array_column($anlSiteInput, "category_name"));
}else
    $anlSiteInput           =   '';


// end comman function
$profile_info		=	getProfileData($_REQUEST['id'], $domain_details['user_id']);
$result             =   getUserDomainDetails($_REQUEST['id']);
$getProfileData     =   getUserProfileData($_REQUEST['id']);
$toggle_module		=   getToggleModule($_REQUEST['id']);


if(empty($toggle_module)){
    $toggle_module  =   array();
}
$google_account		=	googleAccountList();

require_once("includes/new-header.php");
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
                                <div class="right">
                                    <a href="test_seo_analytics_chart.php?id=<?php echo $result['id']; ?>" class="btn btn-default" >Back</a>
                                </div>
                            </div>
                            <div class="settings-tab">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#settingTab1">General Settings</a>
                                    </li>
                                    <li><a data-toggle="tab" href="#settingTab2">White Label</a></li>
                                    <li><a data-toggle="tab" href="#settingTab3">Integration</a></li>
                                    <li><a data-toggle="tab" href="#settingTab4">Customize Report</a></li>
                                    <!-- <li><a data-toggle="tab" href="#settingTab5">FAQ</a></li> -->
                                    <li><a data-toggle="tab" href="#settingTab6">Activity</a></li>
                                </ul>

                                <div class="tab-content">

                                    <div id="settingTab1" class="tab-pane fade in active">
                                        <form class="d-flex" name="general_settings" method="post" id="general_settings" enctype="multipart/form-data">
                                            <input type="hidden" name="action" value="general_settings">
                                            <input type="hidden" name="request_id" value="<?php echo $_REQUEST['id']?>">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Project Start Date </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <?php
                                                            if(!empty($domain_details['domain_register']) && $domain_details['domain_register'] != '0000-00-00' )
                                                                $datet  = date('d-m-Y', strtotime($domain_details['domain_register']));
                                                            else
                                                                $datet  = date('d-m-Y');

                                                        ?>
                                                        <input type="text" name="domain_register" id="domain_register" class="form-control" placeholder="Date" value="<?php echo @$datet;?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Target Location</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                    <select name="regional_db" id="regional_db" class="select form-control">
                                                        <option value="ae" <?php if($domain_details['regional_db'] == 'ae') echo 'selected'; ?>>"ae" (google.ae)</option>
                                                        <option value="au" <?php if($domain_details['regional_db'] == 'au') echo 'selected'; ?>>"au" (google.com.au)</option>
                                                        <option value="az" <?php if($domain_details['regional_db'] == 'az') echo 'selected'; ?>>"az" (google.az)</option>
                                                        <option value="be" <?php if($domain_details['regional_db'] == 'be') echo 'selected'; ?>>"be" (google.be)</option>
                                                        <option value="br" <?php if($domain_details['regional_db'] == 'br') echo 'selected'; ?>>"br" (google.com.br)</option>
                                                        <option value="ca" <?php if($domain_details['regional_db'] == 'ca') echo 'selected'; ?>>"ca" (google.ca)</option>
                                                        <option value="ch" <?php if($domain_details['regional_db'] == 'ch') echo 'selected'; ?>>"ch" (google.ch)</option>
                                                        <option value="cy" <?php if($domain_details['regional_db'] == 'cy') echo 'selected'; ?>>"cy" (google.com.cy)</option>
                                                        <option value="de" <?php if($domain_details['regional_db'] == 'de') echo 'selected'; ?>>"de" (google.de)</option>
                                                        <option value="dk" <?php if($domain_details['regional_db'] == 'dk') echo 'selected'; ?>>"dk" (google.dk)</option>
                                                        <option value="ee" <?php if($domain_details['regional_db'] == 'ee') echo 'selected'; ?>>"ee" (google.ee)</option>
                                                        <option value="eg" <?php if($domain_details['regional_db'] == 'eg') echo 'selected'; ?>>"eg" (google.com.eg)</option>
                                                        <option value="es" <?php if($domain_details['regional_db'] == 'es') echo 'selected'; ?>>"es" (google.es)</option>
                                                        <option value="fr" <?php if($domain_details['regional_db'] == 'fr') echo 'selected'; ?>>"fr" (google.fr)</option>
                                                        <option value="gr" <?php if($domain_details['regional_db'] == 'gr') echo 'selected'; ?>>"gr" (google.gr)</option>
                                                        <option value="hk" <?php if($domain_details['regional_db'] == 'hk') echo 'selected'; ?>>"hk" (google.com.hk)</option>
                                                        <option value="ie" <?php if($domain_details['regional_db'] == 'ie') echo 'selected'; ?>>"ie" (google.ie)</option>
                                                        <option value="il" <?php if($domain_details['regional_db'] == 'il') echo 'selected'; ?>>"il" (google.il)</option>
                                                        <option value="in" <?php if($domain_details['regional_db'] == 'in') echo 'selected'; ?>>"in" (google.co.in)</option>
                                                        <option value="it" <?php if($domain_details['regional_db'] == 'it') echo 'selected'; ?>>"it" (google.it)</option>
                                                        <option value="ke" <?php if($domain_details['regional_db'] == 'ke') echo 'selected'; ?>>"ke" (google.co.ke)</option>
                                                        <option value="sa" <?php if($domain_details['regional_db'] == 'sa') echo 'selected'; ?>>"kw" (google.com.sa)</option>
                                                        <option value="ma" <?php if($domain_details['regional_db'] == 'ma') echo 'selected'; ?>>"ma" (google.co.ma)</option>
                                                        <option value="mu" <?php if($domain_details['regional_db'] == 'mu') echo 'selected'; ?>>"mu" (google.mu)</option>
                                                        <option value="my" <?php if($domain_details['regional_db'] == 'my') echo 'selected'; ?>>"my" (google.com.my)</option>
                                                        <option value="nl" <?php if($domain_details['regional_db'] == 'nl') echo 'selected'; ?>>"nl" (google.nl)</option>
                                                        <option value="no" <?php if($domain_details['regional_db'] == 'no') echo 'selected'; ?>>"no" (google.no)</option>
                                                        <option value="nz" <?php if($domain_details['regional_db'] == 'nz') echo 'selected'; ?>>"nz" (google.co.nz)</option>
                                                        <option value="ph" <?php if($domain_details['regional_db'] == 'ph') echo 'selected'; ?>>"ph" (google.com.ph)</option>
                                                        <option value="ph" <?php if($domain_details['regional_db'] == 'pk') echo 'selected'; ?>>"pk" (google.com.pk)</option>
                                                        <option value="pl" <?php if($domain_details['regional_db'] == 'pl') echo 'selected'; ?>>"pl" (google.pl)</option>
                                                        <option value="ru" <?php if($domain_details['regional_db'] == 'ru') echo 'selected'; ?>>"ru" (google.ru)</option>
                                                        <option value="se" <?php if($domain_details['regional_db'] == 'se') echo 'selected'; ?>>"se" (google.se)</option>
                                                        <option value="sg" <?php if($domain_details['regional_db'] == 'sg') echo 'selected'; ?>>"sg" (google.com.sg)</option>
                                                        <option value="th" <?php if($domain_details['regional_db'] == 'th') echo 'selected'; ?>>"th" (google.co.th)</option>
                                                        <option value="tr" <?php if($domain_details['regional_db'] == 'tr') echo 'selected'; ?>>"tr" (google.com.tr)</option>
                                                        <option value="us" <?php if($domain_details['regional_db'] == 'us') echo 'selected'; ?>>"us" (google.com)</option>
                                                        <option value="us.bing" <?php if($domain_details['regional_db'] == 'us.bing') echo 'selected'; ?>>"bing-us" (bing.com)</option>
                                                        <option value="uk" <?php if($domain_details['regional_db'] == 'uk') echo 'selected'; ?>>"uk" (google.co.uk)</option>
                                                        <option value="za" <?php if($domain_details['regional_db'] == 'za') echo 'selected'; ?>>"za" (google.co.za)</option>
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
                                                        <input type="text" name="domain_name" class="form-control" placeholder="Your site name here" value="<?php echo $domain_details['domain_name']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Website URL</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="domain_url" class="form-control"  value="<?php echo $domain_details['domain_url']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Client Name</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="clientName" class="form-control" placeholder="John Doe" value="<?php echo $domain_details['clientName']; ?>">
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
                                            <div class="box <?php if(empty($searchConsole['message'])) echo 'active'; ?>">
                                                <figure><img src="/assets/images/google-logo-icon.png" alt=""></figure>
                                                <h5>Search Console</h5>
                                                <p>Select an existing account or connect a new Search Console Account </p>
                                                <a href="#" pd-popup-open="PopupAddSearchConsoleAccount" class="btn">Add Google Search Console Account</a>
                                            </div>
                                        </div>

                                        <div class="table-cover">


                                            <table id="example" class="table" width="100%" cellspacing="0">
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
                                                            <div class="dropdown">
                                                                <a href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-circle"></i>
                                                                    <i class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a data-id="<?php echo $result['id']?>" href="javascript:;"  id="revoke_data"><i class="fa fa-trash"></i>
                                                                        Delete</a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php } else { ?>
                                                        <tr><td colspan="5">Not Found Record</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>

                                    <div id="settingTab4" class="tab-pane fade">

                                        <p>Show/Hide section on the <span>View Key</span> and <span>PDF File</span></p>
                                        <form class="d-flex">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Top Summary</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Keywords
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="top_keyword" type="checkbox" <?php if(!in_array('top_keyword', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 mb-2"></div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Traffic
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="top_traffic" type="checkbox" <?php if(!in_array('top_traffic', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 mb-2"></div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Backlinks

                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="top_backlink" type="checkbox" <?php if(!in_array('top_backlink', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 mb-2"></div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Goals
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="top_goal" type="checkbox" <?php if(!in_array('top_goal', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>

                                            <hr>

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
                                                    <h6>Live Keyword Tracking</h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Keyword Table
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" data-block="serp_ranking_table"  type="checkbox" <?php if(!in_array('serp_ranking_table', $toggle_module)) echo 'checked'; ?> />
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
                                                    <h6>Backlink Profile</h6>
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

                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6>Activities </h6>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>
                                                        Summary
                                                    </label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="switch">
                                                        <label>
                                                            <input class="check-toggle check-toggle-round-flat" type="checkbox" data-block="activities" <?php if(!in_array('activities', $toggle_module)) echo 'checked'; ?> />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div id="settingTab5" class="tab-pane fade">
                                        <div class="row d-flex">
                                            <div class="col-sm-5">
                                                <div class="faq-sidebar">
                                                    <div class="faq-cover">
                                                        <div class="faqList">
                                                            <?php if(!empty($faqData)) { ?>
                                                            <ul class="faq_data">
                                                                <?php foreach($faqData as $faq) { ?>
                                                                <li>
                                                                    <a href="#" class="faq_detail" data-id="<?php echo $faq['id']; ?>">
                                                                        <?php echo $faq['faq_title'];  ?>
                                                                        <span class="text-center delete" data-id="<?php echo $faq['id']; ?>" >
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                </li>
                                                                <?php } ?>
                                                            </ul>
                                                            <?php }  ?>
                                                        </div>

                                                        <div class="addFaq">
                                                            <a href="#" class="btn btn-default"><i class="fa fa-plus"></i> Add FAQ</a>
                                                        </div>
                                                    </div>


                                                    <div class="newFaq">
                                                        <a href="#" class="backfaq">
                                                            <i class="fa fa-angle-left"></i>
                                                            Back
                                                        </a>
                                                        <form name="faq_form" method="post" id="faq_form">
                                                            <input type="hidden" name="action" value="save_faq" />
                                                            <input type="hidden" name="request_id" value="<?php echo $_REQUEST['id']; ?>" />
                                                            <div class="form-group">
                                                                <label>
                                                                Question
                                                                </label>
                                                                <input type="text" name="faq_title" class="form-control" placeholder="What is this?">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>
                                                                Answer
                                                                </label>
                                                                <textarea class="form-control summernote" name="faq_content" placeholder="Great question! This is FAQ"></textarea>

                                                            </div>
                                                            <div class="form-group text-right">
                                                                <a href="#" class="deletefaq">
                                                                    <i class="fa fa-trash"></i>
                                                                    Delete FAQ
                                                                </a>
                                                            </div>
                                                            <div class="text-center">
                                                            <button type="submit" class="btn btn-default"><i class="fa fa-paper-plane-o"></i> Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class="editFaq" style="display:none">
                                                        <a href="#" class="backfaq">
                                                            <i class="fa fa-angle-left"></i>
                                                            Back
                                                        </a>
                                                        <form name="edit_faq_form" method="post" id="edit_faq_form">
                                                            <input type="hidden" name="action" value="edit_faq" />
                                                            <input type="hidden" name="faq_ids" id="faq_ids"  value="" />
                                                            <input type="hidden" name="request_id" value="<?php echo $_REQUEST['id']?>" />
                                                            <div class="form-group">
                                                                <label>
                                                                Question
                                                                </label>
                                                                <input type="text" name="faq_title" id="faq_title" class="form-control" placeholder="What is this?">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>
                                                                Answer
                                                                </label>
                                                                <textarea class="form-control" id="edit_summernote" name="faq_content" placeholder="Great question! This is FAQ"></textarea>

                                                            </div>
                                                            <div class="form-group text-right">
                                                                <a href="#" class="deletefaq">
                                                                    <i class="fa fa-trash"></i>
                                                                    Delete FAQ
                                                                </a>
                                                            </div>
                                                            <div class="text-center">
                                                            <button type="submit" class="btn btn-default"><i class="fa fa-paper-plane-o"></i> Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-sm-7">
                                                <div class="panel-group faqAccordion" id="faqAccordion">
                                                    <?php if(!empty($faqData)) {
                                                            $i = 1;
                                                    ?>
                                                        <?php foreach($faqData as $faq) { ?>
                                                            <div class="panel">
                                                                <div class="panel-heading">
                                                                    <h4 class="panel-title">
                                                                        <a data-toggle="collapse" data-parent="#faqAccordion" href="#collapse<?php echo $i; ?>"><?php echo $faq['faq_title']?></a>
                                                                    </h4>
                                                                </div>
                                                                <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse <?php echo ($i == 1) ? 'in' : ''; ?>">
                                                                    <div class="panel-body"><?php echo $faq['faq_content'] ?></div>
                                                                </div>
                                                            </div>
                                                    <?php   $i++;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="settingTab6" class="tab-pane fade">
                                        <div class="form-group">
                                            <form class="d-flex task_form" name="onSite_task_form" method="post">
                                                <input type="hidden" name="action" value="save_tags" />
                                                <input type="hidden" name="onsite" value="1" />
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label>On Site Task</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control activityType" data-task="1" data-role="tagsinput"  value="<?php echo $onSiteInput; ?>" name="task_report"  >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="form-group">
                                            <form class="d-flex task_form" name="offSite_task_form" method="post">
                                                <input type="hidden" name="action" value="save_tags" />
                                                <input type="hidden" name="onsite" value="2" />
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label>Off Site Task</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control activityType" data-task="2"  data-role="tagsinput" value="<?php echo $offSiteInput; ?>" name="task_report"  >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="form-group">
                                            <form class="d-flex task_form" name="analyses_task_form" method="post">
                                                <input type="hidden" name="action" value="save_tags" />
                                                <input type="hidden" name="onsite" value="3" />
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <label>
                                                            Analyses Site Task
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" class="form-control activityType" data-task="3" data-role="tagsinput"   value="<?php echo $anlSiteInput; ?>" name="task_report"  >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
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
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
<link rel="stylesheet" href="assets/scripts/country/css/bootstrap-select-country.min.css" />

<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<script src="assets/scripts/vendors.js"></script>
<script src="assets/scripts/jquery.validate.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js" type="text/javascript"></script>
<script src="assets/scripts/modal.js"></script>
<script src="assets/scripts/fileinput.min.js"></script>
<script src="assets/scripts/plugins/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>
<script src="assets/scripts/country/js/bootstrap-select-country.min.js"></script>

<script type="text/javascript">
    $('.summernote').summernote();
    $('#edit_summernote').summernote();

    $('#faq_form').on('submit', function(e){
        e.preventDefault();
        var new_data    =   $(this).serializeArray();
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: new_data,
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    Command: toastr["success"]('Faq Saved Successfully');
                    $('#faq_form')[0].reset();
                    $(".summernote").summernote("reset");
                    getHtmlFaq();
                    getDeleteHTMLFAQ();
                    $('.newFaq').hide();
                    $('.faq-cover').show();
                } else {
                    Command: toastr["success"]('Error on Saved data');
                    $('.newFaq').hide();
                    $('.faq-cover').show();
                }

            }
        })
    })

    $('#edit_faq_form').on('submit', function(e){
        e.preventDefault();
        var new_data    =   $(this).serializeArray();
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: new_data,
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    Command: toastr["success"]('Data updated successfully');
                    getHtmlFaq();
                    getDeleteHTMLFAQ();
                    $('.editFaq').hide();
                    $('.faq-cover').show();
                } else {
                    Command: toastr["success"]('Error on updated data');
                    $('.editFaq').hide();
                    $('.faq-cover').show();
                }

            }
        })
    })

    $(document).on('click', '.faq_detail', function(e){
        e.preventDefault();
        var slide_id    =   $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: {action: 'get_faq', slide_id: slide_id},
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    $("#edit_summernote").summernote('code', result['data']['faq_content']);
                    $("#faq_title").val(result['data']['faq_title']);
                    $("#faq_ids").val(result['data']['id']);
                    $(".faq-sidebar .faq-cover").slideUp();
                    $('.editFaq').show();
                } else {
                    return false;
                }

            }
        })
    })

	$(document).on("click", ".backfaq", function(e){
        e.preventDefault();
		$(".faq-sidebar .faq-cover").slideDown();
		$("body").find(".newFaq").fadeOut();
		$("body").find(".editFaq").fadeOut();
	});

    $(document).on("click", ".delete", function(e){
        e.preventDefault();
        var data_id     =   $(this).attr('data-id');
        var self        =   $(this);
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: {action: 'delete_faq', data_id: data_id},
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    Command: toastr["success"]('Data updated successfully');
                    self.parents('li').remove();
                    getDeleteHTMLFAQ();
                } else {
                    Command: toastr["success"]('Data updated successfully');
                }

            }
        })

        return false;
    })


    $('.activityType').tagsinput({
        allowDuplicates: false,
//        maxTags: 5,
        trimValue: true,
    });

    $('.keywords').tagsinput({
        allowDuplicates: false,
//        maxTags: 5,
        trimValue: true,
    });


    $('.activityType').on('beforeItemAdd', function(event) {
        if (event.item !== event.item.toLowerCase()) {
            event.cancel = true;
            $(this).tagsinput('add', event.item.toLowerCase());
        }
    });

    $('.activityType').on('itemRemoved', function(event) {
    // event.item: contains the item
        event.preventDefault();
        var tag = event.item;
        var task_type =  $(this).attr('data-task');
        var action = 'remove_task';
        // Do some processing here
        $.ajax({
            type: "POST",
            url: "assets/ajax/categoryTags.php",
            data: {action: action, task_type: task_type, tag: tag},
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    Command: toastr["success"]('Task Delete Successfully');
                }

            }
        });
    });

    $('#activity_status').tagsinput({
        allowDuplicates: false,
        maxTags: 5,
        trimValue: true,
    });

    $('.activityType').on('itemAdded', function(event) {
        event.preventDefault();
        var on_site =  $(this).attr('data-task');
        var tag     =   event.item;

        $.ajax({
            type: "POST",
            url: "assets/ajax/categoryTags.php",
            data: {action: 'save_tags', onsite:on_site, task_report:tag},
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    Command: toastr["success"]('Task Saved Successfully');
                }
            }
        });
    })

    $(document).on('submit', '#activity_status_form', function(e){
        e.preventDefault();
        var new_data = $('#activity_status_form').serializeArray();
        $.ajax({
            type: "POST",
            url: "assets/ajax/categoryTags.php",
            data: new_data,
            dataType: 'json',
            success: function(result) {
                if (result['status'] == '1') {
                    Command: toastr["success"]('Status Saved Successfully');
                }

            }
        });
    })

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
                        request_id: '<?php echo @$_REQUEST['id']; ?>'
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
        dataType: 'json',
        success: function(result) {
            var status	=	result['status'];
            if (status == 'success') {
                Command: toastr["success"]('Your detail saved successfully');
            } else {
                Command: toastr["error"]('Please try again getting error');
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

    function getHtmlFaq(){
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: {action: 'getHTMLFaq', ids: <?php echo $_REQUEST['id'] ?>},
            dataType: 'json',
            success: function(result) {
                $('.faq_data').html(result['data']);
            }
        })
    }

    function getDeleteHTMLFAQ(){
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: {action: 'getDeleteHTMLFaq', ids: <?php echo $_REQUEST['id'] ?>},
            dataType: 'json',
            success: function(result) {
                $('#faqAccordion').html(result['data']);
            }
        })
    }

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
            <h2 id="view-name">Select a View</h2> 

            <form name="save_view_data" id="save_view_data" method="post" >
                <div id="view-selector" class="chart">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select name="analytic_account" id="analytic_account" class="selectpicker form-control" data-live-search="true" data-dropup-auto="false" data-id="<?php echo $_REQUEST['id']; ?>">
                                <option value=""><--select account--></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select name="analytic_property" id="analytic_property" class="selectpicker form-control ">
                                <option value=""><--select property--></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select name="analytic_view_id" id="analytic_view_id" data-id="<?php echo $_REQUEST['id']; ?>" class="selectpicker form-control">
                                <option value=""><--select view id--></option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" id="submit_button" value="Save" class="btn btn-default  waves-effect google-list">
                        Save
                    </button>
                </div>

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

        </div>
        <div class="popup-footer">
            <a class="btn btn-white" pd-popup-close="PopupAddSearchConsoleAccount" href="#">Close</a>
            <a class="btn btn-green" href="<?php echo FULL_PATH ?>auth.php?id=<?php echo $_REQUEST['id']; ?>" id="add_new_google">Add New Account</a>
        </div>

    </div>
</div>



<?php require_once("includes/nav-footer.php"); ?>
<script>

$('#domain_register').datepicker({
    format: 'dd-mm-yyyy',

});

</script>