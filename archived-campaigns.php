<?php
require_once("includes/config.php");
require_once("includes/functions.php");
require_once("includes/new-header.php");
global $DBcon;
checkUserLoggedIn();
$results            =   getActiveProject($status=1);

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
                                        <h5>Archive Campaigns</h5>
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
                                                    <a href="test-home.php"><i class="fa fa-paper-plane"></i> Active Campaigns</a>
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
<!-- #end theme settings -->


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
                            <option value="be">"be" (google.be)</option>
                            <option value="br">"br" (google.com.br)</option>
                            <option value="ca">"ca" (google.ca)</option>
                            <option value="cy">"cy" (google.com.cy)</option>
                            <option value="de">"de" (google.de)</option>
                            <option value="es">"es" (google.es)</option>
                            <option value="fr">"fr" (google.fr)</option>
                            <option value="gr">"gr" (google.gr)</option>
                            <option value="ie">"ie" (google.ie)</option>
                            <option value="in">"in" (google.co.in)</option>
                            <option value="it">"it" (google.it)</option>
                            <option value="ma">"ma" (google.co.ma)</option>
                            <option value="my">"my" (google.com.my)</option>
                            <option value="nl">"nl" (google.nl)</option>
                            <option value="no">"no" (google.no)</option>
                            <option value="nz">"nz" (google.co.nz)</option>
                            <option value="pl">"pl" (google.pl)</option>
                            <option value="ph">"ph" (google.com.ph)</option>
                            <option value="ru">"ru" (google.ru)</option>
                            <option value="sa">"sa" (google.com.sa)</option>
                            <option value="se">"se" (google.se)</option>
                            <option value="sg">"sg" (google.com.sg)</option>
                            <option value="th">"th" (google.co.th)</option>
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
                            <option value="be">"be" (google.be)</option>
                            <option value="br">"br" (google.com.br)</option>
                            <option value="ca">"ca" (google.ca)</option>
                            <option value="cy">"cy" (google.com.cy)</option>
                            <option value="de">"de" (google.de)</option>
                            <option value="es">"es" (google.es)</option>
                            <option value="fr">"fr" (google.fr)</option>
                            <option value="gr">"gr" (google.gr)</option>
                            <option value="ie">"ie" (google.ie)</option>
                            <option value="in">"in" (google.co.in)</option>
                            <option value="it">"it" (google.it)</option>
                            <option value="ma">"ma" (google.co.ma)</option>
                            <option value="my">"my" (google.com.my)</option>
                            <option value="nl">"nl" (google.nl)</option>
                            <option value="no">"no" (google.no)</option>
                            <option value="nz">"nz" (google.co.nz)</option>
                            <option value="pl">"pl" (google.pl)</option>
                            <option value="ph">"ph" (google.com.ph)</option>
                            <option value="ru">"ru" (google.ru)</option>
                            <option value="sa">"sa" (google.com.sa)</option>
                            <option value="se">"se" (google.se)</option>
                            <option value="sg">"sg" (google.com.sg)</option>
                            <option value="th">"th" (google.co.th)</option>
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
        "ajax": "assets/ajax/archive_processing.php",
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
                       return '<figure><a href="#"><figcaption>'+data['domain_first_name']+'</figcaption></a></figure><h6>'+data['domain_name']+' <small>'+data['client_name']+'</small></h6><cite>'+data['domain_url']+'</cite>';
               }
           },
           {data: null,
            render: function (data, type, row) {
                    return data['date'];
               }
           },
           {data: null,
            render: function (data, type, row) {
                    return '<div class="dropdown"><a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i><i class="fa fa-circle"></i> <i class="fa fa-circle"></i></a><ul class="dropdown-menu"><li><a data-id="'+data['domain_id']+'" data-name="'+data['domain_name']+'" class="restore_domain" href="javascript:;" data-placement="top" title="" data-hover="tooltip" data-original-title="Restore Domain"><i class="fa fa-restore"></i>Restore</a></li><li><a data-id="'+data['domain_id']+'" data-name="" class="delete_archive" href="javascript:;" data-placement="top" title="" data-hover="tooltip" data-original-title="Restore Domain"><i class="fa fa-trash"></i>Delete</a></li></ul></div>';
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
} );
    setTimeout(function() {
        $('#chart_2').fadeOut('slow');
        $('#chart_1').fadeOut('slow');
        $('#container').show();
        $('#pie-container-1').show();
    }, 3000); //


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

 $(document).on('click', '.restore_domain', function(e){
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
			  data: {action: 'restore_domain', ids: id},
			  success: function(data) {
					if ( data.status==='success' ) {
						self.parents('tr').remove();
						$('#archive-remove').fadeIn( 300 ).delay( 2500 ).fadeOut( 400 );
					} else {
						$('#banner-danger').modal();
					}			
			  }
			});		
		} else alert('Unknown row id.');
	});

    $(document).on('click', '.delete_archive', function(e){
		var allVals = $(this).attr('data-id');  

        WRN_PROFILE_DELETE = "Are you sure want to delete this row?";  
        var check = confirm(WRN_PROFILE_DELETE);  
        if(check == true){  
            //for server side
            $.ajax({   
                type: "POST",  
                url: "assets/ajax/ajax-domain.php",  
                cache:false,  
                data: {action: 'delete_domain', ids: allVals},
                success: function(response)  
                {   
                    setTimeout(function(){// wait for 5 secs(2)
                        location.reload(); // then reload the page.(3)
                    }, 3000); 			
                    //referesh table
                }   
            });
        }  
	});


</script>