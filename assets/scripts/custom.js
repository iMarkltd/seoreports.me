//--------FUNCTION TO CHECK IF BLOCK EXIST--------//

function check_blocks(value) {
	var check_value = $.trim(value);

	if ($(value).length) {
		return true;
	} else {
		return false;
	}
}


//--------FUNCTION TO OPEN MODAL--------//
//var jQuery = $.noConflict();
$(document).ready(function () {
	$('#profile_id').on('click', function () {
		$('#personalSetting').modal('show');
	});

	$('#profile_image').on('click', function () {
		$('#profile_image_modal').modal('show');
	});


});
$('#add_domain').on('click', function (e) {
	e.preventDefault();
	$('#semrush_details').modal();
});

function editRow(id) {
	if ('undefined' != typeof id) {
		jQuery.ajax({
			action: 'personal',
			type: 'POST',
			dataType: 'json',
			url: "assets/ajax/domain_details.php",
			data: {
				action: 'edit',
				edit: id
			},
			success: function (result) {
				var user_id = result['user_id'];
				var domain_name = result['domain_name'];
				var domain_url = result['domain_url'];
				var id = result['id'];
				$('#edit_semrush_details').modal();
				$("#edit_semrush_details #myModalLabel").html('Edit Domain');
				$("#edit_semrush_details .modal-body input#ids").val(id);
				$("#edit_semrush_details .modal-body input#domain_name").val(domain_name);
				$("#edit_semrush_details .modal-body input#domain_url").val(domain_url);
			}
		});
	} else alert('Unknown row id.');
}

function archiveRow(id) {
	if ('undefined' != typeof id) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: "assets/ajax/ajax-domain.php",
			data: {
				action: 'archive',
				ids: id
			},
			success: function (data) {
				if (data.status === 'success') {
					$('#archive-success').modal();
				} else {
					$('#banner-danger').modal();
				}
				setTimeout(function () { // wait for 5 secs(2)
					location.reload(); // then reload the page.(3)
				}, 3000);
			}
		});
	} else alert('Unknown row id.');
}

function restorDomain(id) {
	if ('undefined' != typeof id) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: "assets/ajax/ajax-domain.php",
			data: {
				action: 'restore_domain',
				ids: id
			},
			success: function (data) {
				if (data.status === 'success') {
					$('#archive-remove').modal();
				} else {
					$('#banner-danger').modal();
				}
				setTimeout(function () { // wait for 5 secs(2)
					location.reload(); // then reload the page.(3)
				}, 3000);
			}
		});
	} else alert('Unknown row id.');
}



function showModal() {
	jQuery('#personalSetting').modal();
}

/*
$("#avatar").fileinput({
	overwriteInitial: true,
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
	allowedFileExtensions: ["jpg", "png", "gif"]
});

*/
function editDomainName() {
	$(function () {
		$('.edit_name').on('click', function () {
			var edit = $(this).quickEdit('create', {
				prefix: '.qe_?',
				blur: true,
				autosubmit: false,
				checkold: true,
				space: false,
				submit: function (dom, newValue) {
					jQuery.ajax({
						action: 'personal',
						type: "POST",
						url: "assets/ajax/ajax-domain.php",
						data: {
							action: 'row_edit',
							domain_name: newValue,
							ids: dom.attr('data-id')
						},
						dataType: 'json',
						success: function (result) {
							var status = result['status'];
							if (status = 'success') {
								$(".edit-success").fadeIn(300).delay(2500).fadeOut(400);
								dom.text(newValue);
							}
						}
					});
				},
				tmpl: '<span class="qe_scope input-group"><span><textarea rows="5" class="qe_input form-control" ></textarea></span>' +
					'<span class="input-group-btn" style="vertical-align: top;"><button class="btn btn-primary qe_submit" >Save</button>' +
					'<button class="btn btn-danger qe_cancel">Cancle</button></span></span>'
			});
			$(this).after(edit);
			$('textarea', edit)[0].select();
		});
	})
}

/* INPUT LABEL EFFECT */
$(document).on('blur', '.style2 input, .style2 textarea, .style2 select', function () {
	if ($(this).val() != '')
		$(this).addClass('has-content');
	else
		$(this).removeClass('has-content');
});
$('.style2 input, .style2 textarea, .style2 select').each(function () {
	if ($(this).val() != '')
		$(this).addClass('has-content');
	else
		$(this).removeClass('has-content');
});

function checkForInput(element) {

	const $label = $(element);

	if ($(element).val().length > 0) {
		$label.addClass('input-has-value');
	} else {
		$label.removeClass('input-has-value');
	}
}

$('.d-flex input, .d-flex select').each(function () {
	//checkForInput(this);
});

// The lines below (inside) are executed on change & keyup
$('.d-flex input, .d-flex select').on('change keyup', function () {
	//checkForInput(this);
});

$('#AgencyLogo').change(function () {
	var filename = $('#AgencyLogo').val();
	if (filename.substring(3, 11) == 'fakepath') {
		filename = filename.substring(12);
	} // For Remove fakepath
	$(".customFile label[for='file_name'] b").html(filename);
	$(".customFile label[for='file_default']").text('Selected File: ');
	if (filename == "") {
		$(".customFile label[for='file_default']").text('No File Choosen');
	}
});


/* Custom PopUp */

$(function () {

	$('[pd-popup-open]').on('click', function (e) {
		var targeted_popup_class = jQuery(this).attr('pd-popup-open');
		$('[pd-popup="' + targeted_popup_class + '"]').fadeIn(100);
		$('body').addClass("hideScroll");
		e.preventDefault();
	});

	$('[pd-popup-close]').on('click', function (e) {
		var targeted_popup_class = jQuery(this).attr('pd-popup-close');
		$('[pd-popup="' + targeted_popup_class + '"]').fadeOut(200);
		$('body').removeClass("hideScroll");
		e.preventDefault();
	});
});


$(document).ready(function () {
	// var input = $('#input-a');
	// input.clockpicker({
	// 	autoclose: true
	// });
	var i = 1;

	$(document).on("click", ".add", function (e) {
		e.preventDefault();
		var div 	= 	$("<div class='ActivityRow' />");
		var html 	= 	'<div class="row"><div class="col-md-3"><div class="form-group"> <label>Date:</label><input type="text" id="activity_date_'+i+'" name="activity_date[]" value="" class="form-control datepicker work_emp_name"></div></div><div class="col-md-3"><div class="form-group"> <label>Type:</label> <select class="form-control activity_type" name="activity_type[]" data-id="activity_task_'+i+'"><option value="1">On Site</option><option value="2">Off Site</option><option value="3">Analysis</option> </select></div></div><div class="col-md-3"><div class="form-group"><label>Task:</label> <select class="form-control activity_task work_emp_name" name="activity_task[]" id="activity_task_'+i+'" >'+active_html+'</select></div></div><div class="col-md-3"><div class="form-group"> <label>Status:</label> <select class="form-control" name="activity_status[]"><option value="1">Working</option><option value="2">Completed</option></select></div></div><div class="col-md-12"><div class="form-group"> <label>No. of Hours:</label><div class="range-slider"> <input class="rangeSlider range-slider__range" data-id="slider_val_'+i+'" type="text" data-slider-min="1" data-slider-max="10" data-slider-step="0.5" data-slider-value="0" name="task_hours[]"/> <span class="range-slider__value" id="slider_val_'+i+'">0</span></div></div></div></div><div class="form-group"> <label>Description:</label><textarea class="form-control desc work_emp_name" name="desc[]" id="desc_'+i+'" placeholder="Explain the task in detail"></textarea></div><button class="remove" type="button"><i class="fa fa-times" aria-hidden="true"></i></button>';


		div.html(html);
		$("#ActivityCover").append(div);
        $('#activity_date_'+i).each(function() {
            $(this).rules("add",
                {
                    required: true
                })
        });
        $('#activity_task_'+i).each(function() {
            $(this).rules("add",
                {
                    required: true
                })
        });
        $('#desc_'+i).each(function() {
            $(this).rules("add",
                {
                    required: true
                })
        });

		i++;
		$('.datepicker').datepicker();
		$(".rangeSlider").slider();
		$('.work_emp_name').each(function () {
			$(this).rules("add", {
				required: true
			});
		});

	    $("#add_activity_form").validate({
			rules: {
				'activity_date[]': "required",
				'activity_task[]': "required",
				'task_hours[]': "required",
				'desc[]': "required",
			},
		})

	});

	$("body").on("click", ".remove", function () {
		$(this).closest(".ActivityRow").remove();
	});


	function copyToClipboard(element) {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val($(element).text()).select();
		document.execCommand("copy");
		$temp.remove();
	}

	$(".copyText").on("click", function () {
		copyToClipboard();
	})


	var faqSidebar = $("body").find(".faq-sidebar");

	if(faqSidebar.length > 0){
		$("body").find(".newFaq").hide();
		$("body").find(".addFaq a").on("click", function(){
			$(".faq-sidebar .faq-cover").slideUp();
			$("body").find(".newFaq").fadeIn(500);
		});
	}


});

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-hover="tooltip"]').tooltip();
});