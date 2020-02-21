<?php
require_once("includes/config.php");
require_once("includes/functions.php");
global $DBcon;
checkUserLoggedIn();

// comman function
$domain_details	    =	getUserDomainDetails($_REQUEST['id']);
$onSiteInput   	    =	getActivateTask(1);
$offSiteInput  	    =	getActivateTask(2);
$anlSiteInput  	    =	getActivateTask(3);
$tagStatus   	    =	getActivateStatus();
$faqData            =   getFaqData($_REQUEST['id']);
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
                                    <h5>AdWords - Summary <small>25/09/2019 - 26/09/2019</small></h5>
                                </div>
                            </div>

                            <div class="three-box four-box">
                                <div class="box purple">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52</h5>
                                    <cite>Clicks</cite>
                                </div>

                                <div class="box green">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52</h5>
                                    <cite>Impressions</cite>
                                </div>

                                <div class="box pink">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52</h5>
                                    <cite>CTR</cite>
                                </div>


                                <div class="box yellow">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52 </h5>
                                    <cite>Cost</cite>
                                </div>

                                <div class="box purple2 mb-0">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52 </h5>
                                    <cite>Avg CPC</cite>
                                </div>

                                <div class="box orange mb-0">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52 </h5>
                                    <cite>Conversions</cite>
                                </div>

                                <div class="box darkGreen mb-0">
                                    <figure>
                                    <img src="/assets/images/google-ads-logo.png" alt="">
                                    </figure>
                                    <h5 class="big">52 </h5>
                                    <cite>Cost Per Conversion</cite>
                                </div>

                            </div>


                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>Campaigns Table</h5>
                                </div>
                            </div>

                            <div class="table-cover">
                                <table>
                                    <tr>
                                        <th>
                                            Sr. No
                                        </th>
                                        <th>
                                            Campaign
                                        </th>
                                        <th>
                                            Impressions
                                        </th>
                                        <th>
                                            Clicks
                                        </th>
                                        <th>
                                            CTR
                                        </th>
                                        <th>
                                            Cost
                                        </th>
                                        <th>
                                            Conversions
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                            <h6>New Patients <small>All Locations</small></h6>
                                        </td>
                                        <td>2,086</td>
                                        <td>52</td>
                                        <td>2.49%</td>
                                        <td>$360.98</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                            <h6>New Patients <small>All Locations</small></h6>
                                        </td>
                                        <td>2,086</td>
                                        <td>52</td>
                                        <td>2.49%</td>
                                        <td>$360.98</td>
                                        <td>5</td>
                                    </tr>
                                </table>
                            </div>

                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>Google Ads - Performance <small>25/09/2019 - 26/09/2019</small></h5>
                                </div>
                            </div>

                            <div class="settings-tab">

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#PerformanceTab1">Networks</a></li>
                                    <li><a data-toggle="tab" href="#PerformanceTab2">Devices</a></li>
                                    <li><a data-toggle="tab" href="#PerformanceTab3">Click Types</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="PerformanceTab1">
                                        <div class="table-cover">
                                            <table>
                                                <tr>
                                                    <th>
                                                        Sr. No
                                                    </th>
                                                    <th>
                                                        Publisher by Network
                                                    </th>
                                                    <th>
                                                        Impressions
                                                    </th>
                                                    <th>
                                                        Clicks
                                                    </th>
                                                    <th>
                                                        CTR
                                                    </th>
                                                    <th>
                                                        Cost
                                                    </th>
                                                    <th>
                                                        Conversions
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-logo-icon.png" alt=""></figure>
                                                        <h6>Google Search</h6>
                                                    </td>
                                                    <td>2,086</td>
                                                    <td>52</td>
                                                    <td>2.49%</td>
                                                    <td>$360.98</td>
                                                    <td>5</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Google Search</h6>
                                                    </td>
                                                    <td>2,086</td>
                                                    <td>52</td>
                                                    <td>2.49%</td>
                                                    <td>$360.98</td>
                                                    <td>5</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in " id="PerformanceTab2">
                                        <div class="table-cover">
                                            <table>
                                                <tr>
                                                    <th>
                                                        Sr. No
                                                    </th>
                                                    <th>
                                                        Device
                                                    </th>
                                                    <th>
                                                        Impressions
                                                    </th>
                                                    <th>
                                                        Clicks
                                                    </th>
                                                    <th>
                                                        CTR
                                                    </th>
                                                    <th>
                                                        Cost
                                                    </th>
                                                    <th>
                                                        Conversions
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <figure class="with-img">
                                                            <i class="fa fa-desktop"></i>
                                                        </figure>
                                                        <h6>Computers</h6>
                                                    </td>
                                                    <td>330</td>
                                                    <td>4</td>
                                                    <td>1.21%</td>
                                                    <td>$21.18</td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <figure class="with-img">
                                                            <i class="fa fa-mobile"></i>
                                                        </figure>
                                                        <h6>Mobile devices with full browsers</h6>
                                                    </td>
                                                    <td>1,651</td>
                                                    <td>44</td>
                                                    <td>2.67%</td>
                                                    <td>$316.93</td>
                                                    <td>5</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <figure class="with-img">
                                                            <i class="fa fa-tablet"></i>
                                                        </figure>
                                                        <h6>Tablets with full browsers</h6>
                                                    </td>
                                                    <td>105</td>
                                                    <td>4</td>
                                                    <td>3.81%</td>
                                                    <td>$22.87</td>
                                                    <td>--</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in " id="PerformanceTab3">
                                        <div class="table-cover">
                                            <table>
                                                <tr>
                                                    <th>
                                                        Sr. No
                                                    </th>
                                                    <th>
                                                        Click Type
                                                    </th>
                                                    <th>
                                                        Impressions
                                                    </th>
                                                    <th>
                                                        Clicks
                                                    </th>
                                                    <th>
                                                        CTR
                                                    </th>
                                                    <th>
                                                        Cost
                                                    </th>
                                                    <th>
                                                        Conversions
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Click to message</h6>
                                                    </td>
                                                    <td>552</td>
                                                    <td>1</td>
                                                    <td>0.18%</td>
                                                    <td>$7.89</td>
                                                    <td>--</td>
                                                </tr>

                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Driving direction</h6>
                                                    </td>
                                                    <td>204</td>
                                                    <td>--</td>
                                                    <td>0.00%</td>
                                                    <td>$0.00</td>
                                                    <td>--</td>
                                                </tr>

                                                <tr>
                                                    <td>3</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Get location details</h6>
                                                    </td>
                                                    <td>544</td>
                                                    <td>7</td>
                                                    <td>1.29%</td>
                                                    <td>$41.67</td>
                                                    <td>--</td>
                                                </tr>

                                                <tr>
                                                    <td>4</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Headline</h6>
                                                    </td>
                                                    <td>2,086</td>
                                                    <td>34</td>
                                                    <td>1.63%</td>
                                                    <td>$215.80</td>
                                                    <td>--</td>
                                                </tr>

                                                <tr>
                                                    <td>5</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Phone calls</h6>
                                                    </td>
                                                    <td>902</td>
                                                    <td>9</td>
                                                    <td>1.00%</td>
                                                    <td>$88.19</td>
                                                    <td>5</td>
                                                </tr>

                                                <tr>
                                                    <td>6</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Show nearby locations</h6>
                                                    </td>
                                                    <td>2</td>
                                                    <td>--</td>
                                                    <td>0.00%</td>
                                                    <td>$00.00</td>
                                                    <td>--</td>
                                                </tr>

                                                <tr>
                                                    <td>7</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Sitelink</h6>
                                                    </td>
                                                    <td>777</td>
                                                    <td>1</td>
                                                    <td>0.13%</td>
                                                    <td>$7.43</td>
                                                    <td>--</td>
                                                </tr>




                                            </table>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="white-box">
                            <div class="top-project-group">
                                <div class="left">
                                    <h5>Google Ads - Top Performers <small>25/09/2019 - 26/09/2019</small></h5>
                                </div>
                            </div>

                            <div class="settings-tab">

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#TopPerformersTab1">Keywords</a></li>
                                    <li><a data-toggle="tab" href="#TopPerformersTab2">AD Groups</a></li>
                                    <li><a data-toggle="tab" href="#TopPerformersTab3">ADS</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="TopPerformersTab1">
                                        <div class="table-cover">
                                            <table>
                                                <tr>
                                                    <th>
                                                        Sr. No
                                                    </th>
                                                    <th>
                                                        Keyword
                                                    </th>
                                                    <th>
                                                        Impressions
                                                    </th>
                                                    <th>
                                                        Clicks
                                                    </th>
                                                    <th>
                                                        CTR
                                                    </th>
                                                    <th>
                                                        Cost
                                                    </th>
                                                    <th>
                                                        Conversions
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-logo-icon.png" alt=""></figure>
                                                        <h6>+apnea +treatment</h6>
                                                    </td>
                                                    <td>2</td>
                                                    <td>--</td>
                                                    <td>0.00%</td>
                                                    <td>$0.00</td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-logo-icon.png" alt=""></figure>
                                                        <h6>+children +dentist</h6>
                                                    </td>
                                                    <td>54</td>
                                                    <td>3</td>
                                                    <td>1.11%</td>
                                                    <td>$26.62</td>
                                                    <td>1</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-logo-icon.png" alt=""></figure>
                                                        <h6>+dentists</h6>
                                                    </td>
                                                    <td>1,434</td>
                                                    <td>36	</td>
                                                    <td>2.51%</td>
                                                    <td>$256.85</td>
                                                    <td>3</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="TopPerformersTab2">
                                        <div class="table-cover">
                                            <table>
                                                <tr>
                                                    <th>
                                                        Sr. No
                                                    </th>
                                                    <th>
                                                        AD Group
                                                    </th>
                                                    <th>
                                                        Impressions
                                                    </th>
                                                    <th>
                                                        Clicks
                                                    </th>
                                                    <th>
                                                        CTR
                                                    </th>
                                                    <th>
                                                        Cost
                                                    </th>
                                                    <th>
                                                        Conversions
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Dentist</h6>
                                                    </td>
                                                    <td>1,623</td>
                                                    <td>39</td>
                                                    <td>2.40%</td>
                                                    <td>$276.17</td>
                                                    <td>3</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Invisalign</h6>
                                                    </td>
                                                    <td>90</td>
                                                    <td>1</td>
                                                    <td>1.11%</td>
                                                    <td>$7.23</td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>
                                                        <figure class="with-img"><img src="/assets/images/google-ads-logo.png" alt=""></figure>
                                                        <h6>Pediatric Dentistry</h6>
                                                    </td>
                                                    <td>198</td>
                                                    <td>7</td>
                                                    <td>3.54%</td>
                                                    <td>$58.99</td>
                                                    <td>2</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="TopPerformersTab3">
                                        <div class="table-cover">
                                            <table>
                                                <tr>
                                                    <th>
                                                        Sr. No
                                                    </th>
                                                    <th>
                                                        AD
                                                    </th>
                                                    <th>
                                                        AD Type
                                                    </th>
                                                    <th>
                                                        Impressions
                                                    </th>
                                                    <th>
                                                        Clicks
                                                    </th>
                                                    <th>
                                                        CTR
                                                    </th>
                                                    <th>
                                                        Cost
                                                    </th>
                                                    <th>
                                                        Conversions
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td>1</td>
                                                    <td class="pl-0">
                                                    <img src="/assets/images/dummy-ad-img.png" alt="">
                                                    </td>
                                                    <td>Expanded text ad</td>
                                                    <td>24</td>
                                                    <td>1</td>
                                                    <td>4.17%</td>
                                                    <td>$4.58</td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td class="pl-0">
                                                    <img src="/assets/images/dummy-ad-img.png" alt="">
                                                    </td>
                                                    <td>Expanded text ad</td>
                                                    <td>3</td>
                                                    <td>--</td>
                                                    <td>0.00%</td>
                                                    <td>$0.00</td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td class="pl-0">
                                                    <img src="/assets/images/dummy-ad-img.png" alt="">
                                                    </td>
                                                    <td>Expanded text ad</td>
                                                    <td>24</td>
                                                    <td>1</td>
                                                    <td>4.17%</td>
                                                    <td>$4.58</td>
                                                    <td>--</td>
                                                </tr>
                                            </table>
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
