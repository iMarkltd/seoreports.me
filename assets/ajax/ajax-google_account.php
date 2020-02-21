<?php

	require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
	global $DBcon;
	$google_account		=	googleAccountList();
	$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id AND status=0";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute();
	$results = $stmt->fetchAll();
?>
		<link rel="stylesheet" href="assets/styles/toastr.css">
	
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
		
		
		<table id="example" class="table table-bordered table-striped table-hover table-condensed dataTable no-footer daseboard-tb" width="100%" cellspacing="0">
		<thead>
			<tr>
				<th class="col-sm-2">Project Name</th>
				<th class="col-sm-3">Analytic Account</th>
				<th class="col-sm-3">Account ID</th>
				<th class="col-sm-1">Property ID</th>
				<th class="col-sm-1">View ID</th>
<!--				<th><button type="button" id="selectall" class="btn btn-default btn-sm fa fa-paper-plane waves-effect makeMeGreen"></button></th>-->
                <th>Action</th>
			</tr>
        </thead>
        <tbody>
			<?php foreach($results as $result) { 
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
						<a  href="seo_analytics_chart.php?id=<?php echo $result['id']; ?>" class="text-info" data-id="<?php echo $result['id']; ?>"><?php echo $result['domain_name']?></a>
					</label>				
				</td>
                <td id="google_account_id" data-id="<?php echo $result['google_account_id']; ?>"><?php if(!empty($result['google_account_id'])) echo $google_account_details['email']; else echo '---'; ?></td>
                <td><?php if(!empty($result['google_analytics_id'])) echo $google_analytics_details['category_name']; else echo '---'; ?></td>
                <td><?php if(!empty($result['google_property_id'])) echo $google_property_details['category_name']; else echo '---'; ?></td>
                <td><?php if(!empty($result['google_profile_id'])) echo $google_profile_details['category_name']; else echo '---'; ?></td>
                <td>
				<ul>				
					<li><a  class="btn btn-icon-circle icon waves-effect key_word" href="javascript:;" data-placement="top" data-hover="tooltip" data-original-title="Update" data-id="<?php echo $result['id']?>"><i class="fa fa-retweet"></i> </a> </li>
					<li><a  class="btn btn-icon-circle icon waves-effect " href="javascript:;" data-placement="top" data-hover="tooltip" data-original-title="Revoke" id="revoke_data" data-id="<?php echo $result['id']?>"><i class="fa fa-times" ></i></a></li>
				</ul>
				
				</td>
            </tr>
			<?php } ?>
        </tbody>
    </table>
<div class="modal fade" id="google_account_model" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="height:400px !important">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                       <span aria-hidden="true">&times;</span>
                       <span class="sr-only">Close</span>
                </button>
                <h1 class="Titles-main" id="view-name">Please choose your account</h1>
				<?php if(!empty($google_account)) { ?>
<!-- 				<div class="add-new-acc-btn">
					<a href="#" id="add_google_account">Add New Account </a>
				</div>
-->				<div class="select-view-cover">
					<!--<h1 class="Titles-main" id="view-name">Select a View</h1>
					<div id="view-selector" class="chart"></div>-->
					
						<select class="select onchange js-data-example-ajax" data-show-subtext="true" data-live-search="true" id="google_account" data-live-search="true" data-dropup-auto="false"  data-id="">
							<optgroup>
							<option value="">Please Select</option>
							<?php 
								foreach($google_account as $account) {
							?>
								<option value="<?php echo $account['id'];?>"><?php echo $account['email'] ?></option>
							<?php }?>
							</optgroup>
						</select>
				</div>
				<?php } else {?>
					<a href="#" id="click_here" >Click here to  Authorize of Google Anayltics Account </a>
				<?php } ?>		
				<div class="clearfix"></div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
				<div class="select-view-cover">
					<h1 class="Titles-main" id="view-name">Select a View</h1>
					<form name="save_view_data" id="save_view_data" method="post" >
					<div id="view-selector" class="chart">
						<select name="analytic_account" id="analytic_account" class="selectpicker" data-live-search="true" data-dropup-auto="false" data-id="">
							<option value=""><--select account--></option>
						</select>
						<select name="analytic_property" id="analytic_property">
							<option value=""><--select property--></option>
						</select>
						<select name="analytic_view_id" id="analytic_view_id" data-id="">
							<option value=""><--select view id--></option>
						</select>

					</div>
						<button type="submit" id="submit_button" value="Save">Save</button>	
					</form>						
				</div>
				<div class="select_box_loader" style="display:none;">
					<div class="loader-msg">Please wait it will take maximum 60 seconds</div>
					<img src="<?php echo 'assets/images/squares.gif'; ?>" />
				</div>
            </div>

            <!-- Modal Footer -->

        </div>
    </div>
</div>


            <!-- Modal Footer -->

        </div>
    </div>
</div>
<!-- Location Modal End-->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
	
<script>
$(document).ready(function() {
  $('[data-hover="tooltip"]').tooltip()
  $("#showArc").click(function(){
		$(".makeMeSlide").slideToggle();
				 $('#showArc').html($('#showArc').text() == 'Hide Archived Projects' ? 'Show Archived Projects' : 'Hide Archived Projects');
	});

	$('#example').change(function(){
		if($("td:last-child .ui-checkbox input").is(":checked")) {
			$("#example th button#delete_all").addClass("makeMeRed animated flash ");
		} else {
			$("#example th button#delete_all").removeClass("makeMeRed animated flash ");
		}
	});
	
//	$('#example').change(function(){
//		if($("td:nth-child(5) .ui-checkbox input").is(":checked")) {
//			$("#example th button#selectall").addClass("makeMeGreen animated flash ");
//		} else {
//			$("#example th button#selectall").removeClass("makeMeGreen animated flash ");
//		}
//	});


	jQuery('#delete_all').on('click', function(e) { 
		var allVals = [];  
		$(".sub_chk:checked").each(function() {  
			allVals.push($(this).attr('data-id'));
		});  
		//alert(allVals.length); return false;  
		if(allVals.length <=0)  
		{  
			alert("Please select row.");  
		}  
		else {  
			WRN_PROFILE_DELETE = "Are you sure you want to delete this row?";  
			var check = confirm(WRN_PROFILE_DELETE);  
			if(check == true){  
				//for server side
				var join_selected_values = allVals.join(","); 
				$.ajax({   
					type: "POST",  
					url: "assets/ajax/ajax-domain.php",  
					cache:false,  
					data: {action: 'delete_domain', ids: join_selected_values},
					success: function(response)  
					{   
					  setTimeout(function(){// wait for 5 secs(2)
						   location.reload(); // then reload the page.(3)
					  }, 3000); 			
						//referesh table
					}   
				});
			}  
		}  
	});

	jQuery('#delete_all_archive').on('click', function(e) { 
		var allVals = [];  
		$(".archive_sub_chk:checked").each(function() {  
			allVals.push($(this).attr('data-id'));
		});  
		//alert(allVals.length); return false;  
		if(allVals.length <=0)  
		{  
			alert("Please select row.");  
		}  
		else {  
			WRN_PROFILE_DELETE = "Are you sure you want to delete this row?";  
			var check = confirm(WRN_PROFILE_DELETE);  
			if(check == true){  
				//for server side
				var join_selected_values = allVals.join(","); 
				$.ajax({   
					type: "POST",  
					url: "assets/ajax/ajax-domain.php",  
					cache:false,  
					data: {action: 'delete_domain', ids: join_selected_values},
					success: function(response)  
					{   
					  setTimeout(function(){// wait for 5 secs(2)
						   location.reload(); // then reload the page.(3)
					  }, 3000); 			
						//referesh table
					}   
				});
			}  
		}  
	});
	$(document).on('click', '.archive_row', function(e){
		e.preventDefault();
		var self = $(this);
		var id   = self.attr('data-id');
		var name = self.attr('data-name');
		var dataUrl = self.attr('data-url');
		var append_archive_row	=	'<tr><td>'+name+'</td><td><button type="button" data-id="'+id+'" data-name="'+name+'" data-url="'+dataUrl+'" class="btn btn-success btn-xs waves-effect restore_domain">Restore</button></td><td><div class="ui-checkbox ui-checkbox-primary ml5"><label><input name="delete[]" value="'+id+'" data-id= "'+id+'" class="archive_sub_chk" type="checkbox"><span></span></label></label></div></td></tr>';
							
		if ( 'undefined' != typeof id ) {
			jQuery.ajax({
			  type:  'POST',
			  dataType:'json',
			  url: "assets/ajax/ajax-domain.php",
			  data: {action: 'archive', ids: id},
			  success: function(data) {
					if ( data.status==='success' ) {
						self.parents('tr').remove();
						$('.archive-success').fadeIn( 300 ).delay( 2500 ).fadeOut( 400 );
						$('#archive_table tbody').append(append_archive_row);
						$('#archive_table').DataTable();						
					} else {
						$('#banner-danger').modal();
					}
			
			  }
			});		
		} else alert('Unknown row id.');
	});
	
	$(document).on('click', '.restore_domain', function(e){
		e.preventDefault();
		var self = $(this);
		var id   = self.attr('data-id');
		var name = self.attr('data-name');
		var dataUrl = self.attr('data-url');
		var pdf_li 	=	 function () {
						var tmp = null;
						$.ajax({
							'async': false,
							'type': "POST",
							'global': false,
							'dataType': 'json',
							'url': "assets/ajax/ajax-checkdowload_link.php",
							'data': {ids:id },
							'success': function (data) {
								tmp = data['result'];
							}
						});
						return tmp;
					}();
		var cron_li	=	 function () {
						var new_tmp = null;
						$.ajax({
							'async': false,
							'type': "POST",
							'global': false,
							'dataType': 'json',
							'url': "assets/ajax/check_email_data.php",
							'data': {action:'check_email_status', ids:id },
							'success': function (data) {
								var status = data['status'];
								if(status == 'success') {
									new_tmp = data['mail_sent'];
								}
							}
						});
						return new_tmp;
					}();
		console.log(cron_li);
	    if(pdf_li == '1'){
			var li = '<li class="hide-pdf-icon"> <i class="fa fa-check" ></i></a></li>';
		}else{
			var li = '<li> <a class="btn btn-icon-circle icon waves-effect seo_analytics_pdf2" href="javascript:;" data-placement="top"  data-id="'+id+'" id="" title="PDF" data-hover="tooltip"><i class="fa fa-file-pdf-o" ></i></a></li>'; 
		}
		console.log(cron_li);
	    if(cron_li == '1'){
			var new_li = '<input name="" value="" data-id= "" class="" type="checkbox" checked disabled><span></span>';
	    }else if(cron_li == '2'){
			var new_li = '<span class="mail_sent" data-id= "'+id+'"></span>';
		}else{
			var new_li = '<input name="send_mail[]" value="'+id+'" data-id= "'+id+'" class="mail_checkbox" type="checkbox"><span></span>'; 
		}
		var append_archive_row	=	'<tr><td><a href="#"><i class="fa fa-star-o"></i></a></td><td class=""><label for="name" class="control-label"><p class="text-info" data-id="'+id+'">'+name+'</p></label><input type="text" id="exampleGrid" class="input-medium" style="display:none;" placeholder="">			<div class="controls-edit"> <a href="#" onclick="edit(this);" class="demo">Edit</a></div><div class="controls-update" style="display:none;"><a href="#" onclick="update(this);">update</a></div></td><td><a href="seo_analytics_chart.php?id='+id+'">'+dataUrl+'<td><ul><li> <a data-id="'+id+'" data-name="'+name+'" data-url="'+dataUrl+'" class="btn btn-icon-circle icon waves-effect archive_row" href="#" data-placement="top" title="Archive" data-hover="tooltip"><i class="fa fa fa-archive" ></i></a></li>'+li+'<li> <a class="btn btn-icon-circle icon waves-effect" href="javascript:;" data-placement="top" date-id="'+id+'" title="Share" data-hover="tooltip" data-toggle="modal" data-target="#shareModal"><i class="fa fa-share" ></i></a></li><li> <a class="btn btn-icon-circle icon waves-effect ssss" href="javascript:;" data-placement="top" title="Email" data-hover="tooltip" data-toggle="modal" date-id="'+id+'" data-target="#emailModal"><i class="fa fa-envelope" ></i></a></li></ul></td><td><div class="ui-checkbox ui-checkbox-primary ml5"><label>'+new_li+'</label></div></td></td><td><div class="ui-checkbox ui-checkbox-primary ml5"><label><input name="delete[]" value="'+id+'" data-id= "'+name+'" class="sub_chk" type="checkbox"><span></span></label></div></td></tr>';
		
		if ( 'undefined' != typeof id ) {
			jQuery.ajax({
			  type:  'POST',
			  dataType:'json',
			  url: "assets/ajax/ajax-domain.php",
			  data: {action: 'restore_domain', ids: id},
			  success: function(data) {
					if ( data.status==='success' ) {
						self.parents('tr').remove();
						$('.archive-success').fadeIn( 300 ).delay( 2500 ).fadeOut( 400 );
						$('#example tbody').append(append_archive_row);
						var  tableLength	=	$('#archive_table tbody tr td .restore_domain').length;
						if(tableLength < 1) {
							$(document).find('.archive-outer .makeMeSlide').hide();
							$('#showArc').html($('#showArc').text() == 'Hide Archived Projects' ? 'Show Archived Projects' : 'Hide Archived Projects');		
							$('[data-hover="tooltip"]').tooltip(); 
							editDomainName();							
						}
						$('#example').DataTable();						
						$("#example table tbody tr:nth-child(odd)").addClass("odd");
						$("#example table tbody tr:nth-child(even)").addClass("even");						
					} else {
						$('#banner-danger').modal();
					}			
			  }
			});		
		} else alert('Unknown row id.');
	});
	
});
$('.save_edit').on('focusout', function(e){
	alert('here');
})

$('#shareModal').on('show.bs.modal', function (e) {
	var rowid = $(e.relatedTarget).data('id');
	$.ajax({
		type : 'POST',
		url : 'assets/ajax/ajax-domain.php', //Here you will fetch records 
		data : {action: 'replace_key', rowid: rowid }, //Pass $id
		dataType: 'json',
		success : function(data){
			var key	=	'<?php echo FULL_PATH; ?>seo_view_details.php?token_id='+(data['data'][0]['token']);
			$('#share_key').val(key);//Show fetched data from database
		}
	});
 });
 $(document).on('click','.demo',function(e){
	 e.preventDefault();
	 var check = $(this).parents('td').find('label .text-info').text();
	 $(this).parents('td').find('#exampleGrid').focus();
	 $(this).parents('td').find('#exampleGrid').val(check);
	 var self	= $(this);
	 edit(self);
	 
 })

 $(document).on('click','.update_row',function(e){
	 e.preventDefault();
	 var self	=	$(this);
	 update(self);
 })
 
	
function edit(element) {
    var parent = $(element).parent().parent();
    var placeholder = $(parent).find('.text-info').text();
    //hide label
    $(parent).find('label').hide();
    //show input, set placeholder
    var input = $(parent).find('input[type="text"]');
	//$("#edit").focus();
    var edit = $(parent).find('.controls-edit');
    var update = $(parent).find('.controls-update');
    $(input).show();
    $(edit).hide();
    $(update).show();
	
    //$(input).attr('placeholder', placeholder);
}

function update(element) {
	
	var parent_var 		= $(element).parents('td');
    var data_id		 	= parent_var.find('.text-info').attr('data-id');
    //hide label
    $(parent_var).find('label').show();
    //show input, set placeholder
    var input_var =  parent_var.find('input[type="text"]');
    var input_val =  parent_var.find('#exampleGrid').val();
	var edit_var = parent_var.find('.controls-edit');
    var update_var = parent_var.find('.controls-update');
	input_var.hide();
	parent_var.find('label .text-info').text(input_val);
	$.ajax({
	  action:  'personal',
	  type:    "POST",
	  url:     "assets/ajax/ajax-domain.php",
	  data:		{action: 'row_edit', domain_name: input_val, ids:data_id},
	  dataType: 'json',
	  success: function(result) {
		  var status = result['status'];
			//$(input).attr('placeholder', placeholder);
			update_var.hide();
			edit_var.show();
	  }
	});
}

$('#selectall').change(function () {
    if ($(this).prop('checked')) {
		$('.mail_checkbox').prop('checked', true);
		var sendMailId	=	[];
		$('.mail_checkbox').each(function(){
			sendMailId.push($(this).data('id'));
		});
		var data_arr	=	JSON.stringify(sendMailId);
		$.ajax({
			type:	"POST",
			url:	"assets/ajax/check_email_data.php",
			data:	{action: 'check_email_data', request_ids: data_arr },
			dataType: 'json',
			success: function(result) {
				var status	=	result['status'];
				if(status == 'success') {
					var domain_name 	=	result['domain_name'];
					var ids				=	result['ids'];
					$('.mail_checkbox').each(function(){
						var unit_id =$(this).data('id');
						if(inArray(unit_id,ids)  == true)
						{
							$(this).removeAttr("checked");
						}else{
							$(this).attr("disabled", "disabled");
						} 
					});
					var names = make_name_list(domain_name);
					Command: toastr["error"]('Please Provide email content of '+names+'');
				}
			}
		});
    }
    else {
        $('.mail_checkbox').prop('checked', false);
    }
});

$(document).on('click','.mail_checkbox',function(e){
	if($(this).prop("checked"))
	{
		var id = $(this).data('id');
		var self =$(this);
		$.ajax({
			type:	"POST",
			url:	"assets/ajax/check_email_data.php",
			data:	{action: 'check_single_email_data', request_ids: id },
			dataType: 'json',
			success: function(result) {
				var status	=	result['status'];
				if(status == 'empty') {
				  self.removeAttr('checked');	
				  Command: toastr["error"](" You havn't  Provided email content yet ");
				} else if(status == 'success') {
					self.attr("disabled", "disabled");
				    Command: toastr["success"](" When Mail sent icon move to green ");
				}
			}
		});
	}
	
})

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


function inArray(needle,haystack)
{
    var count=haystack.length;
    for(var i=0;i<count;i++)
    {
		if(haystack[i]==needle){return true;}
    }
    return false;
}
function make_name_list(haystack)
{
	var messege = "";
    var count=haystack.length;
    for(var i=0;i<count;i++)
    {
		if(i == count-1 ){ messege+= " and "; }
		else if(i > 0) {  messege+= " , ";} else  {}
		messege+=haystack[i];
		
    }
    return messege;
}
$('#selectall').trigger('change');
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


$(document).on("change", ".onchange", function(){
	$('.select_box_loader').show();
	var data_id		=	$(this).val();
	var request_id	=	$(this).data('id');
	jQuery.ajax({
		type:	'POST',
		url: 	"assets/ajax/ajax_google_view_details.php",
		data:	{action: 'update_select_div', request_id: request_id, analytic_id: data_id},		
		success: function(result) {
			console.log(result);
			$('.select_box_loader').hide();
			$('#analytic_account').html(result);
			$('#analytic_account').selectpicker('refresh');	
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
	  }
	});
});

$(document).on("change", "#analytic_view_id", function (e) {
	var analytic_view_id		= $(this).val();
	var analytic_property_id	= $('#analytic_property').val();
	var analytic_account_id 	= $('#analytic_account').val();
	var google_account_id	 	= $('#google_account').val();
	var request_id 				= $(this).data('id');
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
$(".js-data-example-ajax").select2();
</script>
