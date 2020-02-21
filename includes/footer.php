<link rel='stylesheet' type='text/css' href='assets/styles/image-crop/imgareaselect.css'>
<link rel="stylesheet" href="assets/styles/toastr.css">
<!-- Latest compiled and minified JavaScript -->
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type='text/javascript' src='assets/scripts/image-crop/jquery.imgareaselect.js'></script>
<script type='text/javascript' src='assets/scripts/image-crop/jQuery_functions.js'></script>

<div class="modal fade" id="personalSetting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                    Personal Setting
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form role="form" name="personal" id="personal" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="action" value="user_info" />
                    <input type="hidden" name="ids" value="<?php echo base64_encode($_SESSION['user_id']); ?>" />
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name"
                            value="<?php echo @$user_info_meta['first_name'][0]?>" />
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                            placeholder="Company Name" value="<?php echo @$user_info_meta['last_name'][0]?>" />
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <span><?php echo @$user_info->data->user_email; ?></span>
                    </div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    <img src="assets/images/ajax-loader.gif" id="personal-loader" style="display:none" />
                </form>
            </div>

            <!-- Modal Footer -->

        </div>
    </div>
</div>

<div class="modal fade" id="profile_image_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                    Profile Image
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div id="kv-avatar-errors-2" class="center-block" style="width:800px;display:none"></div>
                <form class="text-center" action="/avatar_upload.php" method="post" enctype="multipart/form-data">
                    <div class="kv-avatar center-block" style="width:200px">
                        <input id="avatar-2" name="avatar-2" type="file" class="file-loading">
                    </div>
                    <!-- include other inputs if needed and include a form submit (save) button -->
                </form>
            </div>

            <!-- Modal Footer -->

        </div>
    </div>
</div>

<!-- Image Upload Modal End-->

<!-- Modal -->
<div id="editModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal" action="javascript:;">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Website URL</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Project Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Analytics Account</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group" style="text-align:center">
                        <a class="btn btn-success " id="showArc">Save Changes</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="shareModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Share</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal" action="javascript:;">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Share Key</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-addon fa fa-key"></span>
                                <input type="text" class="form-control" id="share_key" value="" readonly="readonly">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

<!-- Share Modal -->
<div id="shareViewKey_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Share</h4>
            </div>
            <div class="modal-body">
                <form role="form" class="form-horizontal" action="javascript:;">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Share Key</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-addon fa fa-key"></span>
                                <input type="text" class="form-control" id="share_key"
                                    value="<?php echo FULL_PATH.'seo_view_details.php?token_id='.@$domain_details['token']; ?>"
                                    readonly="readonly">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
<!-- Email Modal -->
<div id="emailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Email</h4>
            </div>
            <div class="modal-body">
                <form role="form" name="email_modal_form" id="email_modal_form" class="form-horizontal"
                    action="javascript:;">
                    <input type="hidden" name="action" value="email_sent" />
                    <input type="hidden" name="share_id" id="share_id" value="" />
                    <input type="hidden" name="save_mail" id="save_email" value="no" />
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-addon fa fa-pencil"></span>
                                <input type="text" name="subject" class="form-control" id="subject"
                                    placeholder="Subject">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="input-group" for=''>
                                <span class="input-group-addon fa fa-paper-plane"></span>
                                <input type='text' name='to' class='form-control' id="to" placeholder="To">
                            </div>
                        </div>
                    </div>

                    <div class="form-group have-textarea">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-addon fa fa-file-text-o"></span>
                                <div id="summernote3" class="summernote"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon fa fa-envelope-open-o"></span>
                                <input type="text" name="mail_from" class="form-control" id="mail_from"
                                    placeholder="Mail From">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon fa fa-user"></span>
                                <input type="text" name="mail_sender_name" class="form-control" id="mail_sender_name"
                                    placeholder="Mail Sender Name">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="text-align:center">
                        <a href="#" class="btn" id="save_mail_before_send">Save Mail</a>
                        <button type="submit" class="btn" id="" value="Send Mail">Send Mail</button>
                    </div>

                </form>
                <div id='loadingmessage' style='display:none'>
                    <img src='assets/images/squares.gif' />
                </div>
            </div>
        </div>
    </div>
</div>




<link href="assets/styles/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="assets/scripts/plugins/multiple_emails/multiple-emails.css" media="all" rel="stylesheet" type="text/css" />

<script src="assets/scripts/jquery.validate.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js" type="text/javascript"></script>
<script src="assets/scripts/modal.js"></script>
<script src="assets/scripts/canvas-to-blob.min.js" type="text/javascript"></script>
<script src="assets/scripts/fileinput.min.js"></script>
<script src="assets/scripts/jquery-clockpicker.min.js"></script>
<script src="assets/scripts/custom.js"></script>
<script src="assets/scripts/plugins/multiple_emails/multiple-emails.js"></script>
<script src="assets/scripts/plugins/select2.min.js"></script>
<script src="assets/scripts/plugins/bootstrap-colorpicker.min.js"></script>
<script src="assets/scripts/plugins/bootstrap-slider.min.js"></script>
<script src="assets/scripts/plugins/bootstrap-datepicker.min.js"></script>
<script src="assets/scripts/form-elements.init.js"></script>
<link href="assets/scripts/summernote/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js" crossorigin="anonymous"></script>
<style>
.kv-avatar .file-preview-frame,
.kv-avatar .file-preview-frame:hover {
    margin: 0;
    padding: 0;
    border: none;
    box-shadow: none;
    text-align: center;
}

.kv-avatar .file-input {
    display: block;
    max-width: 100%;
}
</style>
<script>
$(document).ready(function() {
    $(document).on("scroll", onScroll);
    //smoothscroll
    $('.pd-fixed-bar .pd-bar-data a').on('click', function(e) {
        e.preventDefault();
        $(document).off("scroll");
        $('.pd-fixed-bar .pd-bar-data a').each(function() {
            $(this).removeClass('active');
        })
        $(this).addClass('active');
        var target = this.hash,
            menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top + 2
        }, 1000, 'swing', function() {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });

    jQuery('#summernote3').summernote({
        height: 300, // set editor height
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true // set focus to editable area after initializing summernote
    });

});

function onScroll(event) {
    var scrollPos = $(document).scrollTop();
    $('.pd-fixed-bar .pd-bar-data a').each(function() {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() >
            scrollPos) {
            $('.pd-fixed-bar .pd-bar-data a').removeClass("active");
            currLink.addClass("active");
        } else {
            currLink.removeClass("active");
        }
    });
}

$(document).ready(function() {
    $('[data-hover="tooltip"]').tooltip();
    $(".about-comp-btn").click(function() {
        $(".about-comp").slideToggle();
    });
});




//    $(window).load(function() {
//	 $('.loader').fadeOut('2000', function() {
//
//		$(this).remove();
//
//
//	});
//    });
//

$(window).load(function() {

    (function() {
        var fakeLoad, gray, green, incLoad, orange, percentage;

        percentage = 0;

        gray = $('.loading');

        orange = $('.loading span');

        green = $('.loaded');

        incLoad = function() {
            gray.attr('data-loader', Math.floor(percentage));
            orange.css({
                'width': percentage + '%',
                'display': 'block'
            });
            if (percentage >= 100) {
                clearInterval(fakeLoad);
                $('.loader').fadeOut('2000', function() {
                    $(this).remove();
                });
                gray.fadeOut(1000);
                orange.delay(500).fadeOut(1000);
                return green.delay(1000).fadeIn(1000);
            } else {
                return percentage += Math.random() / 2;
            }
        };

        fakeLoad = window.setInterval(incLoad, 15);

    }).call(this);


});
$(document).ready(function() {

    $.validator.addMethod("validate_email", function(value, element) {


        if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
            return true;
        } else {
            return false;
        }
    }, "Please enter a valid Email.");

    // Multiple, comma separated email validation
    $.validator.addMethod('multiEmail', function(value, element) {
        if (this.optional(element)) {
            console.log(element);
            return true;
        } else {
            var valid = true;

            $.each($.trim(value).replace(/,$/, '').split(','), $.proxy(function(index, email) {
                if (!$.validator.methods.email.call(this, $.trim(email), element)) {
                    valid = false;
                }
            }, this));

            return valid;
        }
    }, 'One or more email addresses are invalid');

    $.validator.addMethod(
        "multiemails",
        function(value, element) {
            if (this.optional(element)) // return true on optional element
                return true;
            var emails = value.split(/[;,]+/); // split element by , and ;
            valid = true;
            for (var i in emails) {
                value = emails[i];
                valid = valid &&
                    $.validator.methods.email.call(this, $.trim(value), element);
            }
            return valid;
        },

        $.validator.messages.multiemails
    );

    $.validator.addMethod("alpha", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z\s]+$/);
    });

    $("#email_modal_form").validate({
        rules: {
            subject: "required",
            to: {
                required: true,
                multiemails: true
            },
            mail_from: {
                required: true,
                validate_email: true
            },
            mail_sender_name: {
                required: true
            }
        },
        messages: {
            subject: "Please provide email subject",
            to: {
                required: "Please provide email address",
                multiemails: "One or more email addresses are invalid"
            },
            mail_from: {
                required: "Please provide email address",
                validate_email: "Email address is not valid"
            },
            mail_sender_name: {
                required: "Please provide email sender name",
            }
        },
        submitHandler: function(form) {
            var ids = $('#share_id').val();
            $('#emailModal').modal('hide');
            $(document).find('.show-div-' + ids).hide();
            $(document).find('.hide-div-' + ids).show();
            $(document).find('.email-queue-msg').fadeIn(300).delay(2500).fadeOut(4000);
            //$(document).find('#loadingmessage').show();  // show the loading message.
            var new_data = $('#email_modal_form').serializeArray();
            var messag_content = $('.summernote').eq(0).code();
            new_data.push({
                name: 'message',
                value: messag_content
            });
            $.ajax({
                action: 'security',
                type: "POST",
                url: "assets/ajax/ajax-email.php",
                data: new_data,
                success: function(result) {
                    var status = result['status'];
                    var analytic_id = result['analytic_id'];
                    if (status == 'success') {
                        $(document).find('#loadingmessage')
                            .hide(); // show the loading message.
                        $('#emailModal').modal('hide');
                        $(document).find('.show-div-' + ids).show();
                        $(document).find('.hide-div-' + ids).hide();
                        $(document).find('.message_saved').fadeIn(300).delay(2500)
                            .fadeOut(4000);
                    } else {
                        $(document).find('#loadingmessage')
                            .hide(); // show the loading message.
                        $('#emailModal').modal('hide');
                        $(document).find('.show-div-' + ids).show();
                        $(document).find('.hide-div-' + ids).hide();
                        $(document).find('.message_errror').fadeIn(300).delay(2500)
                            .fadeOut(4000);
                    }
                }
            });
        }
    });

    $(document).on('click', "#save_mail_before_send", function(e) {
        e.preventDefault();
        $('#save_email').val('yes');
        if ($("#email_modal_form").validate().form()) {
            var new_data = $('#email_modal_form').serializeArray();
            var messag_content = $('.summernote').eq(0).code();
            new_data.push({
                name: 'message',
                value: messag_content
            });
            $.ajax({
                action: 'security',
                type: "POST",
                url: "assets/ajax/ajax-save-email.php",
                data: new_data,
                success: function(result) {
                    var status = result['status'];
                    var analytic_id = result['analytic_id'];
                    if (status == 'success') {
                        $(document).find('#loadingmessage')
                            .hide(); // show the loading message.
                        $('#emailModal').modal('hide');
                        //					$(document).find('.hide-div-'+ids).hide();
                        Command: toastr["success"]('Your email detail saved successfully');
                    } else {
                        $(document).find('#loadingmessage')
                            .hide(); // show the loading message.
                        $('#emailModal').modal('hide');
                        //					$(document).find('.hide-div-'+ids).hide();
                        Command: toastr["error"]('Please try again getting error');
                    }
                }
            });

        }
    });

    $("#personal").validate({
        rules: {
            full_name: {
                required: true,
                alpha: true
            },
            company_name: "required"
        },
        messages: {
            full_name: {
                required: "Please provide details",
                alpha: "Letters only please "
            },
            company_name: "Please provide details",
        },
        submitHandler: function(form) {
            $(document).find('#loadingmessage').show(); // show the loading message.
            $.ajax({
                action: 'security',
                type: "POST",
                url: "assets/ajax/ajax-user_info.php",
                data: $(form).serialize(),
                dataType: 'json',
                success: function(result) {
                    var status = result['status'];
                    if (status == 'success') {
                        $(document).find('#loadingmessage')
                            .hide(); // show the loading message.
                        $('#personalSetting').modal('hide');
                        $(document).find('.email-success').fadeIn(300).delay(7000)
                            .fadeOut(4000);
                    } else {
                        $(document).find('#loadingmessage')
                            .hide(); // show the loading message.
                        $('#personalSetting').modal('hide');
                        $(document).find('.email-error').fadeIn(300).delay(7000)
                            .fadeOut(4000);
                    }
                }
            });
        }
    });
});

//Plug-in function for the bootstrap version of the multiple email
$(function() {

    $('#profile_logo').on('click', function() {
        $('#logo_modal').modal('show');
    });

    $('#shareViewKey').on('click', function() {
        $('#shareViewKey_modal').modal('show');
    });

    //To render the input device to multiple email input using BootStrap icon
    $('#example_emailBS').multiple_emails({
        position: "bottom"
    });
    //OR $('#example_emailBS').multiple_emails("Bootstrap");

    //Shows the value of the input device, which is in JSON format
    $('#example_emailBS').change(function() {
        $('#current_emailsBS').text($(this).val());
    });
});

$(document).on("click", "#seo_analytics_pdf", function(e) {
    e.preventDefault();
    $(document).find('.pdf_dowmload_msg').fadeIn(300).delay(2500).fadeOut(4000);
    $(document).find('.pdf-link-head').hide();
    $(document).find('.hide-header-pdf-div').show();
    var ids = $(this).data('id');
    $(document).find('.show-pdf-div-' + ids).hide();
    $(document).find('.hide-pdf-div-' + ids).show();
    //	$(document).find('#download_pdf_loader').show();  // show the loading message.
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "download_pdf.php",
        data: {
            action: 'seo_analytics_pdf',
            ids: ids
        },
        success: function(result) {
            $(document).find('.show-pdf-div-' + ids).show();
            $(document).find('.hide-pdf-div-' + ids).hide();
            //			$(document).find('#download_pdf_loader').hide();  // show the loading message.
            console.log(result);
            $(document).find('.hide-header-pdf-div').hide();
            $(document).find('.pdf-link-head').show();
            var link = document.createElement("a");
            document.body.appendChild(link);
            link.download = result['file_name'];
            link.href = result['file_path'];
            link.click();
        }
    });
});

$(document).on("click", "#seo_view_pdf", function(e) {
    e.preventDefault();
    $(document).find('#download_pdf_loader').show(); // show the loading message.
    var ids = $(this).data('id');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "save_pdf.php",
        data: {
            action: 'seo_view_pdf',
            ids: ids
        },
        success: function(result) {
            $(document).find('#download_pdf_loader').hide(); // show the loading message.
            console.log(result);
            var link = document.createElement("a");
            document.body.appendChild(link);
            link.download = result['file_name'];
            link.href = result['file_path'];
            link.click();
        }
    });
});

$(document).on("click", "#seo_view_pdf_from_view", function(e) {
    e.preventDefault();
    $(document).find('#download_pdf_loader').show(); // show the loading message.
    var ids = $(this).data('id');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "download_pdf.php",
        data: {
            action: 'seo_view_pdf',
            ids: ids
        },
        success: function(result) {
            $(document).find('#download_pdf_loader').hide(); // show the loading message.
            console.log(result);
            var link = document.createElement("a");
            document.body.appendChild(link);
            link.download = result['file_name'];
            link.href = result['file_path'];
            link.click();
        }
    });
});

$(document).on('submit', '#personal', function(e) {
    e.preventDefault();
    $(document).find('#personal-loader').show(); // show the loading message.
});

$(document).on("click", ".seo_analytics_pdf", function(e) {
    e.preventDefault();
    var ids = $(this).data('id');
    $(document).find('.show-pdf-div-' + ids).hide();
    $(document).find('.hide-pdf-div-' + ids).show();
    //	$(document).find('#download_pdf_loader').show();  // show the loading message.
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "save_pdf.php",
        data: {
            action: 'seo_analytics_pdf',
            ids: ids
        },
        success: function(result) {
            //			$(document).find('.show-pdf-div-'+ids).show();
            $(document).find('.hide-pdf-div-' + ids).hide();
            //$('.not_found').hide();

            //$('.pdf-drop ul .dropdown-header').after(result['recent_li']);
            $(document).find('.hide-pdf-icon-' + ids).show();
            //			$(document).find('#download_pdf_loader').hide();  // show the loading message.
            Command: toastr["info"]('Your pdf is preparing for download');
            console.log(result);
            //			var link = document.createElement("a");
            //			document.body.appendChild(link);
            //			link.download = result['file_name'];
            //			link.href = result['file_path'];
            //			link.click();
        }
    });
});


$(document).on("click", ".remove_all_pdf", function(e) {
    e.preventDefault();
    var ids = $(this).data('id');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "assets/ajax/ajax-remove_pdf.php",
        data: {
            action: 'remove_all_pdf'
        },
        success: function(result) {
            location.reload();
        }
    });

});

$(document).on("click", ".remove-drop-pdf", function(e) {
    e.preventDefault();
    var self = $(this);
    var ids = $(this).data('id');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "assets/ajax/ajax-remove_pdf.php",
        data: {
            action: 'remove_pdf',
            ids: ids
        },
        success: function(result) {
            location.reload();
        }
    });


});

$(document).on("keyup", ".search_box_input", function(e) {
    e.preventDefault();
    var items = $(this).val();
    if (items == "") {
        $(document).find('.result_conatainer').hide();
    } else {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "assets/ajax/ajax-autocomplete.php",
            data: {
                action: 'auto_search',
                text: items
            },
            success: function(result) {
                var res = result['html'];
                $(document).find('.result_conatainer').html(res);
                $(document).find('.result_conatainer').show();

            }
        });
    }
});

$(document).on("click", ".jquery_pdf", function(e) {
    $(this).parent('li').addClass('unread');
});

$('#emailModal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('.summernote').eq(0).code('');
});
$(document).on('click', '.mail_sent', function(e) {
    var id = $(this).data('id');
    var self = $(this);
    $.ajax({
        type: "POST",
        url: "assets/ajax/check_email_data.php",
        data: {
            action: 'change_email_status',
            request_ids: id
        },
        dataType: 'json',
        success: function(result) {
            var status = result['status'];

            if (status == 'success') {
                self.parents('div.ui-checkbox').html('<label><input name="send_mail[]" value="' +
                    id + '" data-id="' + id +
                    '" class="mail_checkbox" type="checkbox"><span></span></label>');
                Command: toastr["success"]('You Send Request Again');
            } else {
                Command: toastr["error"]('Getting Error! Please try again getting error');
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
    allowedFileExtensions: ["jpg", "png", "jpeg","gif"],
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
		if(!empty($url)) echo $url['return_path']; ?>"],
            initialPreviewFileType: 'image', // image is the default and can be overridden in config below
            initialPreviewConfig: [{
				caption: "<?php  $url = checkUserProfileImage(); if(!empty($url))
				echo $url['file_name']; ?>",
                size: 576237,
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
    var image_id = '<?php echo @$_SESSION['
    user_id '] ?>';
    $('.user_pic').attr('src', "<?php echo FULL_PATH; ?>assets/ajax/uploads/" + image_id + "/profile/" +
        image_name);

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
    allowedFileExtensions: ["jpg", "png", "gif", "jpeg"],
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
				"<?php $url = checkProfileLogo(@$_REQUEST['id']); if(!empty($url))
				echo $url['return_path']; ?>"
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
    var image_id = '<?php echo @$_SESSION['
    user_id ']?>';
    //$('.user_pic').attr('src', "<?php echo FULL_PATH; ?>assets/ajax/uploads/"+image_id+"/profile/"+image_name);

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
</script>

<div id='download_pdf_loader' style='display:none'>
    <img src='assets/images/squares.gif' />
</div>
<script src="assets/scripts/getclick.js" type="text/javascript"></script>
<script type="text/javascript">
try {
    clicky.init(100918994);
} catch (e) {}
</script>
<noscript>
    <p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100918994ns.gif" /></p>
</noscript>
<!-- Dev only -->
<!-- Vendors -->
</body>

</html>