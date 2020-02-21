// JavaScript Validation For Registration Page

jQuery('document').ready(function()
{ 		 

		 // name validation
		 var nameregex = /^[a-zA-Z ]+$/;
		 
		 jQuery.validator.addMethod("validname", function( value, element ) {
		     return this.optional( element ) || nameregex.test( value );
		 }); 
		 
		 // valid email pattern
		 var eregex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		 
		 jQuery.validator.addMethod("validemail", function( value, element ) {
		     return this.optional( element ) || eregex.test( value );
		 });
		 jQuery("#location_form").validate({
					
		  rules:
		  {
				domain_name : {
					required : true,
				},
				domain_url : {
					required : true,
					url: true
				},
				regional_db: {
					required: true,
				},
		   },
		   messages:
		   {
				domain_name : {
					required : "Domain Name is required",
				},
				domain_url : {
					required : "Domain URL is required",
					url : "Please enter a valid URL"
				},
				regional_db:{
					required: "Please select any one regional",
				}
		   },
		   errorPlacement : function(error, element) {
			  $(element).closest('.md-float-label').find('.help-block').html(error.html());
		   },
		   highlight : function(element) {
			  $(element).closest('.md-float-label').removeClass('has-success').addClass('has-error');
		   },
		   unhighlight: function(element, errorClass, validClass) {
			  $(element).closest('.md-float-label').removeClass('has-error');
			  $(element).closest('.md-float-label').find('.help-block').html('');
		   },
				submitHandler: submitForm
		   }); 
		   
		   
		   function submitForm(){
			   
			   $.ajax({
			   		url: 'assets/ajax/ajax-domain.php',
			   		type: 'POST',
			   		data: $('#location_form').serialize(),
			   		dataType: 'json'
			   })
			   .done(function(data){
			   		
			   		$('#btn-signup').html('<img src="assets/images/ajax-loader.gif" /> &nbsp; storing data...').prop('disabled', true);
			   		$('input[type=text],input[type=email],input[type=password]').prop('disabled', true);
			   		
			   		setTimeout(function(){
								   
						if ( data.status==='success' ) {
							Command: toastr["success"]('Your domain added successfully!');
						} else if ( data.status==='2' ) {
							Command: toastr["error"]('Domain Name Alerady Exists, Check In Archieved Projects !');
					    } else {
							Command: toastr["error"]('Please try again, getting error');
						}
								  
					},3000);

				    setTimeout(function(){// wait for 5 secs(2)
						   location.reload(); // then reload the page.(3)
					}, 7000); 			
					
			   		
			   })
			   .fail(function(){
			   		$("#register-form").trigger('reset');
			   		alert('An unknown error occoured, Please try again Later...');
			   });
		   }


	jQuery("#edit_form").validate({
			
	rules:
	{
		domain_name : {
			required : true,
			validname : true
		},
		domain_url : {
			required : true,
			url: true
		},
		regional_db: {
			required: true,
		},
	},
	messages:
	{
		domain_name : {
			required : "Domain Name is required",
			validemail : "Please enter a valid name"
		},
		domain_url : {
			required : "Domain URL is required",
			url : "Please enter a valid URL"
		},
		regional_db:{
			required: "Please select any one regional",
		}
	},
	errorPlacement : function(error, element) {
	  $(element).closest('.md-float-label').find('.help-block').html(error.html());
	},
	highlight : function(element) {
	  $(element).closest('.md-float-label').removeClass('has-success').addClass('has-error');
	},
	unhighlight: function(element, errorClass, validClass) {
	  $(element).closest('.md-float-label').removeClass('has-error');
	  $(element).closest('.md-float-label').find('.help-block').html('');
	},
		submitHandler: submitUpdateForm
	}); 


	function submitUpdateForm(){
	   
	   $.ajax({
			url: 'assets/ajax/ajax-domain.php',
			type: 'POST',
			data: $('#edit_form').serialize(),
			dataType: 'json'
	   })
	   .done(function(data){
			
			$('#btn-signup').html('<img src="assets/images/ajax-loader.gif" /> &nbsp; storing data...').prop('disabled', true);
			$('input[type=text],input[type=email],input[type=password]').prop('disabled', true);
	
			setTimeout(function(){
							
				if ( data.status==='success' ) {
					Command: toastr["success"]('Your domain add successfully!');
				} else if ( data.status==='2' ) {
					Command: toastr["error"]('Domain Name Alerady Exist, Check in arhieved projects !');
				} else {
					Command: toastr["error"]('Please try again getting error');
				}
							
			},3000);
			
	   })
	   .fail(function(){
			$("#register-form").trigger('reset');
			alert('An unknown error occoured, Please try again Later...');
	   });
	}

/*	jQuery("#file_image_modal").validate({
			
	rules:
	{
		avatar: {
			required: true,
			accept: "image/*"			
		},
	},
	messages:
	{
		avatar:{
			required: "This field is required",
			accept: "This field is required",
		}
	},
	errorPlacement : function(error, element) {
	  $(element).closest('.md-float-label').find('.help-block').html(error.html());
	},
	highlight : function(element) {
	  $(element).closest('.md-float-label').removeClass('has-success').addClass('has-error');
	},
	unhighlight: function(element, errorClass, validClass) {
	  $(element).closest('.md-float-label').removeClass('has-error');
	  $(element).closest('.md-float-label').find('.help-block').html('');
	},
		submitHandler: submitImageForm
	}); 


	function submitImageForm(){
	   
	   $.ajax({
			url: 'assets/ajax/ajax-domain.php',
			type: 'POST',
			data: $('#file_image_modal').serialize(),
			dataType: 'json'
	   })
	   .done(function(data){
			
			$('#btn-signup').html('<img src="assets/images/ajax-loader.gif" /> &nbsp; storing data...').prop('disabled', true);
			
						   
				if ( data.status==='success' ) {
					$('#edit_semrush_details').modal('hide');
					$('#banner-success').modal();
				} else {
					$('#edit_semrush_details').modal('hide');
					$('#banner-danger').modal();
				}
			
	   })
	   .fail(function(){
			$("#register-form").trigger('reset');
			alert('An unknown error occoured, Please try again Later...');
	   });
	}
*/	
});