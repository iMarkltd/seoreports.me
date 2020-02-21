<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/new-header.php");
global $DBcon;
checkUserLoggedIn();
$results            =   getActiveProject($status=0);
$active_projects    =   getActiveProjectDateRange();
$deactive_projects  =   getDeactiveProjectDateRange();
foreach($active_projects as $key=>$active){
    $month_name[]       =   $active['MonthName'];
    $deactive_project[] =   $deactive_projects[$key]['Total'];
    $active_project[]   =   $active['Total'];
}
$getTop10Keywords    =   getTopKeywordDateRange(10);
$getTop20Keywords    =   getTopKeywordDateRange(20);
$getTop100Keywords   =   getTopKeywordDateRange(100);

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

                <div class="three-box">
                    <div class="box purple">
                        <h5>Total Number of Projects</h5>
                        <div id="container" style="display:none"></div>
                        <div class="chart-loader" id="chart_1"> <img src="<?php echo 'assets/images/squares.gif'; ?>" /> </div>
                    </div>

                    <div class="box orange">
                        <h5>Total Keywords</h5>
                        <div id="pie-container-1" style="display:none"></div>
                        <div class="chart-loader" id="chart_2"> <img src="<?php echo 'assets/images/squares.gif'; ?>" /> </div>
                    </div>

                    <div class="box orange">
                        <h5>Total Number of Hours</h5>
                        <div class="d-flex">
                            <figure>
                                <img src="/assets/images/dummy-chart-2.png">
                            </figure>
                            <div>
                                <ul>
                                    <li>
                                        <span></span> On Site Tasks
                                    </li>
                                    <li>
                                        <span></span> Off Site Tasks
                                    </li>
                                    <li>
                                        <span></span> Analysis And Reporting
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- row -->
                <div class="row">

                    <!-- Analytics -->
                    <div class="col-md-12">

                        <div class="white-box">
                            <div id="home_loader" style="display:none !important;">
                                <img src="assets/images/home-loader.gif" id="" />
                                <p class="text-uppercase text-center">loading <strong><span class="dot"></span><span
                                            class="dot"></span><span class="dot"></span></strong></p>
                            </div>

                            <div class="project-table">
                                <div class="top-project-group">
                                    <div class="left">
                                        <h5>
                                            Active Campaigns
                                        </h5>
                                    </div>
                                    <div class="right">
                                        <div class="dropdown">
                                            <a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i
                                                    class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown-header">
                                                    More Options
                                                </li>
                                                <li>
                                                    <a href="archived-campaigns.php"><i class="fa fa-paper-plane"></i> Archived Campaigns</a>
                                                </li>
                                                <li>
                                                    <a href="#"><i class="fa fa-paper-plane"></i> Send E-Mail</a>
                                                </li>
                                                <li>
                                                    <a href="#"><i class="fa fa-trash"></i> Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                <div class="table-cover">
                                    <table id="example">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="my-checkbox">
                                                        <label>
                                                            <input type="checkbox">
                                                            <span class="checkbox"></span>
                                                        </label>
                                                    </div>
                                                </th>
                                                <th>
                                                    Company
                                                </th>
                                                <th>
                                                    Date
                                                </th>
                                                <th>
                                                    Stats
                                                </th>
                                                <th>
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="">
                                        </tbody>
                                    </table>
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

<div class="popup" pd-popup="PopupAddNewDomain">
    <div class="popup-inner">
        <div class="popup-header">
            <h1>Add Domain</h1>
        </div>
        <form role="form" name="location_form" id="location_form" enctype="multipart/form-data" method="post">
        <div class="popup-body">

                    <input type="hidden" name="action" value="location" />
                    <div class="form-group">
                        <label for="exampleInputEmail1">Domain Name</label>
                        <input type="text" class="form-control" id="domain_name" name="domain_name"
                            placeholder="example" />
                        <span class="help-block" id="error"></span>
                    </div>
                    <div class="form-group ">
                        <label for="exampleInputEmail1">Domain URL</label>
                        <input type="text" class="form-control" id="domain_url" name="domain_url"
                            placeholder="http://example.com" />
                        <span class="help-block" id="error"></span>
                    </div>
                    <div class="form-group ">
                        <label for="exampleInputEmail1">Select regional database</label>
                        <select name="regional_db" id="regional_db" class="select form-control">
                            <option value="ae">"ae" (google.ae)</option>
                            <option value="au">"au" (google.com.au)</option>
                            <option value="az">"az" (google.az)</option>
                            <option value="be">"be" (google.be)</option>
                            <option value="br">"br" (google.com.br)</option>
                            <option value="ca">"ca" (google.ca)</option>
                            <option value="ch">"ch" (google.ch)</option>
                            <option value="cy">"cy" (google.com.cy)</option>
                            <option value="de">"de" (google.de)</option>
                            <option value="de">"dk" (google.dk)</option>
                            <option value="ee">"ee" (google.ee)</option>
                            <option value="eg">"eg" (google.com.eg)</option>
                            <option value="es">"es" (google.es)</option>
                            <option value="fr">"fr" (google.fr)</option>
                            <option value="gr">"gr" (google.gr)</option>
                            <option value="hk">"hk" (google.com.hk)</option>
                            <option value="ie">"ie" (google.ie)</option>
                            <option value="il">"il" (google.co.il)</option>
                            <option value="in">"in" (google.co.in)</option>
                            <option value="it">"it" (google.it)</option>
                            <option value="ke">"ke" (google.co.ke)</option>
                            <option value="sa">"ku" (google.co.sa)</option>
                            <option value="ma">"ma" (google.co.ma)</option>
                            <option value="mu">"mu" (google.mu)</option>
                            <option value="my">"my" (google.com.my)</option>
                            <option value="nl">"nl" (google.nl)</option>
                            <option value="no">"no" (google.no)</option>
                            <option value="nz">"nz" (google.co.nz)</option>
                            <option value="pk">"pk" (google.com.pk)</option>
                            <option value="pl">"pl" (google.pl)</option>
                            <option value="ph">"ph" (google.com.ph)</option>
                            <option value="ru">"ru" (google.ru)</option>
                            <option value="se">"se" (google.se)</option>
                            <option value="sg">"sg" (google.com.sg)</option>
                            <option value="th">"th" (google.co.th)</option>
                            <option value="tr">"tr" (google.com.tr)</option>
                            <option value="us" selected>"us" (google.com)</option>
                            <option value="us.bing">"bing-us" (bing.com)</option>
                            <option value="uk">"uk" (google.co.uk)</option>
                            <option value="za">"za" (google.co.za)</option>
                        </select>
                        <span class="help-block" id="error"></span>

                    </div>

        </div>
        <div class="popup-footer">
        <img src="assets/images/ajax-loader.gif" id="img" style="display:none" />
            <a class="btn btn-white" pd-popup-close="PopupAddNewDomain" href="#">Close</a>
            <button type="submit" class="btn btn-green" id="btn-signup" name="submit">Submit</button>
        </div>
        </form>

    </div>
</div>




<!-- Location Modal End-->
<!-- Edit Domain Name-->
<div class="modal fade" id="edit_semrush_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
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
                    <input type="hidden" name="ids" id="ids" value="" />
                    <div class="form-group md-float-label">
                        <label for="exampleInputEmail1">Domain Name</label>
                        <input type="text" class="form-control" id="domain_name" name="domain_name"
                            placeholder="example" />
                        <span class="help-block" id="error"></span>
                    </div>
                    <div class="form-group md-float-label">
                        <label for="exampleInputEmail1">Domain URL</label>
                        <input type="text" class="form-control" id="domain_url" name="domain_url"
                            placeholder="http://example.com" />
                        <span class="help-block" id="error"></span>
                    </div>
                    <div class="form-group md-float-label">
                        <label for="exampleInputEmail1">Select regional database</label>
                        <select name="regional_db" id="regional_db" class="select">
                            <option value="ae">"ae" (google.ae)</option>
                            <option value="au">"au" (google.com.au)</option>
                            <option value="az">"az" (google.az)</option>
                            <option value="be">"be" (google.be)</option>
                            <option value="br">"br" (google.com.br)</option>
                            <option value="ca">"ca" (google.ca)</option>
                            <option value="ch">"ch" (google.ch)</option>
                            <option value="cy">"cy" (google.com.cy)</option>
                            <option value="de">"de" (google.de)</option>
                            <option value="de">"dk" (google.dk)</option>
                            <option value="ee">"ee" (google.ee)</option>
                            <option value="eg">"eg" (google.com.eg)</option>
                            <option value="es">"es" (google.es)</option>
                            <option value="fr">"fr" (google.fr)</option>
                            <option value="fr">"fr" (google.fr)</option>
                            <option value="gr">"gr" (google.gr)</option>
                            <option value="hk">"hk" (google.com.hk)</option>
                            <option value="ie">"ie" (google.ie)</option>
                            <option value="il">"il" (google.co.il)</option>
                            <option value="in">"in" (google.co.in)</option>
                            <option value="it">"it" (google.it)</option>
                            <option value="ke">"ke" (google.co.ke)</option>
                            <option value="sa">"ku" (google.co.sa)</option>
                            <option value="ma">"ma" (google.co.ma)</option>
                            <option value="mu">"mu" (google.mu)</option>
                            <option value="my">"my" (google.com.my)</option>
                            <option value="nl">"nl" (google.nl)</option>
                            <option value="no">"no" (google.no)</option>
                            <option value="nz">"nz" (google.co.nz)</option>
                            <option value="ph">"ph" (google.com.ph)</option>
                            <option value="pk">"pk" (google.com.pk)</option>
                            <option value="pl">"pl" (google.pl)</option>
                            <option value="ru">"ru" (google.ru)</option>
                            <option value="se">"se" (google.se)</option>
                            <option value="sg">"sg" (google.com.sg)</option>
                            <option value="th">"th" (google.co.th)</option>
                            <option value="tr">"tr" (google.com.tr)</option>
                            <option value="us" selected>"us" (google.com)</option>
                            <option value="us.bing">"bing-us" (bing.com)</option>
                            <option value="uk">"uk" (google.co.uk)</option>
                            <option value="za">"za" (google.co.za)</option>
                        </select>
                        <span class="help-block" id="error"></span>

                    </div>
                    <button type="submit" class="btn btn-primary" id="btn-signup" name="submit">Submit</button>
                    <img src="assets/images/ajax-loader.gif" id="img" style="display:none" />
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
<script src="//code.highcharts.com/highcharts.js"></script>
<style type="text/css">
.bs-example {
    margin: 20px;
}
</style>
<?php
	$result = check_semrush_details($_SESSION['user_id']);
	if($result == 0 ) {
?>
<script type="text/javascript">
$(function() {
    $('#semrush_details').modal();
});
</script>
<?php } ?>

<script type="text/javascript">
$(document).ready(function() {
    waitForMsg();
    // $('#example').dataTable({
    //     language: {
    //         searchPlaceholder: "Search"
    //     }
    // });
    $('#example').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "assets/ajax/server_processing.php",
        language : {
            processing : '<div><img src="assets/images/squares.gif"></div>'
    },
        columns: [
           {data: null,
            render: function (data, type, row) {
                   if (data['domain_first_name']) {
                       return '<div class="my-checkbox"><label><input type="checkbox"><span class="checkbox"></span></label></div>';
                   }

               }
           },
           {data: null,
               render: function (data, type, row) {
                       return '<figure><a href="#"><figcaption>'+data['domain_first_name']+'</figcaption></a></figure><h6><a href="test_seo_analytics_chart.php?id='+data['domain_id']+'" class="text-info" id="edit_row_ba" data-id="'+data['domain_id']+'">'+data['domain_name']+' <small>'+data['client_name']+'</small></a></h6><cite>'+data['domain_url']+'</cite>';
               }
           },
           {data: null,
            render: function (data, type, row) {
                    return data['date'];
               }
           },
           {data: null,
            render: function (data, type, row) {
                    return '<p><span>K</span>Total Keywords:<strong>'+data['totalKeywords']+'</strong></p><p><span>T</span> Total Trafic:<strong>'+data['totalTraffic']+'</strong></p>';
               }
           },
           {data: null,
            render: function (data, type, row) {
                    return '<div class="dropdown"><a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i><i class="fa fa-circle"></i> <i class="fa fa-circle"></i></a><ul class="dropdown-menu"><li><a href="project-settings.php?id='+data['domain_id']+'"><i class="fa fa-pencil-square-o"></i>Edit</a></li><li><a data-id="'+data['domain_id']+'" data-name="Cool Room Hire Perth - Hursh" data-url="'+data['domain_url']+'" class="archive_row" href="javascript:;" data-placement="top" title="" data-hover="tooltip" data-original-title="Archive"><i class="fa fa-archive"></i>Archive</a></li><li class="show-pdf-div-'+data['domain_id']+'"><a class="seo_analytics_pdf" href="javascript:;" data-placement="top" data-id="'+data['domain_id']+'" id="" title="" data-hover="tooltip" data-original-title="PDF"><i class="fa fa-file-pdf-o"></i> PDF</a></li><li><a class="" href="javascript:;" data-placement="top" title="" data-hover="tooltip" data-id="'+data['domain_id']+'" id="shareModal"  pd-popup-open="shareModal" data-original-title="Share"><i class="fa fa-share"></i> Share</a></li><li><a class="ssss" href="javascript:;" data-placement="top" title="" data-hover="tooltip" data-toggle="modal" data-id="'+data['domain_id']+'" data-target="#emailModal" data-original-title="Email"><i class="fa fa-envelope"></i> Email</a></li></ul></div>';
               }
           },

        ],
        "aoColumnDefs": [ {
                     "aTargets": [ 2 ],
                     "mRender": function ( data, type, full ) {
                          return $("<div/>").html(data).text();
                      }
        } ],

      });


    $('#example').on( 'page.dt', function () {
            $('html, body').animate({
            scrollTop: $("#example_wrapper").offset().top
        }, 500);
    });
    
    setTimeout(function() {
        $('#chart_2').fadeOut('slow');
        $('#chart_1').fadeOut('slow');
        $('#container').show();
        $('#pie-container-1').show();
    }, 3000); //

    Highcharts.chart('container', {
        chart: {
            height: 287,
            type: 'column'
        },
        title: {
            text: ''
        },
        credits: {
            enabled: false
        },
        xAxis: {
            categories: <?php echo json_encode($month_name); ?> ,
            crosshair : true
        },
        legend: {
            align: 'center',
            verticalAlign: 'top',
            layout: 'vertical'
        },
        yAxis: {
            min: 0,
        },
        plotOptions: {
            column: {
                pointPadding: 0,
                borderWidth: 0
            }
        },
        series: [{
            name: 'New Projects',
            data: <?php echo json_encode($active_project, JSON_NUMERIC_CHECK); ?>,
            color: '#374afb'
        }, {
            name: 'Last Projects',
            data: <?php echo json_encode($deactive_project, JSON_NUMERIC_CHECK); ?>,
            color: '#34bfa3'

        }]
    });

});


Highcharts.chart('pie-container-1', {
    chart: {
        height: 287,
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: ''
    },
    credits: {
        enabled: false
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    legend: {
        align: 'left',
        verticalAlign: 'top',
    },
    series: [{
        name: 'Total Keywords',
        colorByPoint: true,
        data: [{
            name: '<?php echo 'Top 10 Keywords: '.$getTop10Keywords; ?>',
            y: <?php echo $getTop10Keywords; ?> ,
            color: '#fd3995',
            sliced: true,
            selected: true
        }, {
            name: '<?php echo 'Top 20 Keywords: '.$getTop20Keywords; ?>',
            y: <?php echo $getTop20Keywords; ?> ,
            color: '#34bfa3',
        }, {
            name: '<?php echo 'Top 100 Keywords: '.$getTop100Keywords; ?>',
            y: <?php echo $getTop100Keywords; ?>,
            color: '#374afb',
        }]
    }]
});


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

<?php require_once("includes/nav-footer.php"); ?>

<script>
function addmsg(type, msg) {
    if (msg <= 0) {
        $('.pdf-drop').removeClass('pending-noti');

    } else {
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

$(document).on('click', '#shareModal', function (e) {

    var rowid = $(this).attr('data-id');
	$.ajax({
		type : 'POST',
		url : 'assets/ajax/ajax-domain.php', //Here you will fetch records
		data : {action: 'replace_key', rowid: rowid }, //Pass $id
		dataType: 'json',
		success : function(data){
			var key	=	'<?php echo FULL_PATH; ?>test_seo_view_details.php?token_id='+(data['data'][0]['token']);
            $('#copy_share_key').val(key);//Show fetched data from database
            var targeted_popup_class = 'shareModal';
            $('[pd-popup="' + targeted_popup_class + '"]').fadeIn(100);
            $('body').addClass("hideScroll");
		}
	});
 });

 $(document).on('click', '.archive_row', function(e){
		e.preventDefault();
		var self = $(this);
		var id   = self.attr('data-id');
		var name = self.attr('data-name');
		var dataUrl = self.attr('data-url');
							
		if ( 'undefined' != typeof id ) {
			jQuery.ajax({
			  type:  'POST',
			  dataType:'json',
			  url: "assets/ajax/ajax-domain.php",
			  data: {action: 'archive', ids: id},
			  success: function(data) {
					if ( data.status==='success' ) {
						self.parents('tr').remove();
						$('#archive-success').fadeIn( 300 ).delay( 2500 ).fadeOut( 400 );
					} else {
						$('#banner-danger').modal();
					}
			
			  }
			});		
		} else alert('Unknown row id.');
	});


</script>