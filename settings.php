<?php
require_once("includes/config.php");
require_once("includes/functions.php");
global $DBcon;
checkUserLoggedIn();
$faqData            =   getFaqDataWithoutId();

// comman function
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
                                    <li class="active"><a data-toggle="tab" href="#settingTab1">Profile Settings</a>
                                    </li>
                                    <li><a data-toggle="tab" href="#settingTab2">User Image</a></li>
                                    <li><a data-toggle="tab" href="#settingTab3">API Unit</a></li>
                                    <li><a data-toggle="tab" href="#settingTab5">FAQ</a></li>
                                </ul>

                                <div class="tab-content">

                                    <div id="settingTab1" class="tab-pane fade in active">
                                        <form class="d-flex" role="form" name="personal" id="personal" enctype="multipart/form-data" method="post" novalidate="novalidate">
                                            <input type="hidden" name="action" value="user_info">
                                            <input type="hidden" name="ids" value="Ng==">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label for="exampleInputEmail1">Name</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                <div class="row">
                                                        <div class="col-sm-4">
                                                        <label for="exampleInputEmail1">Company Name</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group">
                                                    <label for="exampleInputEmail1">Email</label>
                                                    <span></span>
                                                </div> -->
                                                <div class="text-left row">
                                                    <div class="col-sm-8 col-sm-offset-4">
                                                        <button type="submit" class="btn btn-default" name="submit"><i class="fa fa-paper-plane-o"></i> Submit</button>
                                                        <button type="submit" class="btn btn-white"><i class="fa fa-refresh"></i> Reset</button>
                                                    </div>
                                                </div>
                                            <img src="assets/images/ajax-loader.gif" id="personal-loader" style="display:none">
                                        </form>
                                    </div>

                                    <div id="settingTab2" class="tab-pane fade">

                                    </div>

                                    <div id="settingTab3" class="tab-pane fade">

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
                                                            <input type="hidden" name="request_id" value="" />
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

<script type="text/javascript">
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


    function getHtmlFaq(){
        $.ajax({
            type: "POST",
            url: "assets/ajax/saveFaqs.php",
            data: {action: 'getHTMLFaq'},
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
            data: {action: 'getDeleteHTMLFaq'},
            dataType: 'json',
            success: function(result) {
                $('#faqAccordion').html(result['data']);
            }
        })
    }




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


</script>
<?php require_once("includes/nav-footer.php"); ?>
