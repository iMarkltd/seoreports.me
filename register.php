<?php 
require_once('includes/config.php');
require_once('includes/header.php');
?>	

	
	<!-- main-container -->
	<div class="main-container clearfix login-page register-page">
		
		<div class="video-container">
			 <video autoplay loop class="fillWidth">
            <source src="assets/video/mp4/The-Coast.mp4" type="video/mp4" />Your browser does not support the video tag. I suggest you upgrade your browser.
            <source src="assets/video/webm/The-Coast.webm" type="video/webm" />Your browser does not support the video tag. I suggest you upgrade your browser.
        </video>
		</div>
		
		<!-- content-here -->
		<div class="content-container" >
			<div class="page page-auth">

				<div class="auth-container">

					<div class="form-head mb20">
						<h1 class="site-logo h2 mb5 mt5 text-center text-uppercase text-bold"><a href="index.html"><img src="assets/images/logo.png" alt="iMark Infotech Pvt. Ltd."></a></h1>
						<p class="text-normal h5 text-center">Already have an account. <a href="login.php">Sign In Now</a></p>
					</div>
					
					  <!-- json response will be here -->
					  <div id="errorDiv"></div>
					  <!-- json response will be here -->
							  

					<div class="form-container">
						<form class="form-horizontal" method="post" role="form" id="register-form" autocomplete="off">
							<div class="md-input-container md-float-label">
							   <input name="name" type="text" id="name" class="form-control md-input"  maxlength="40" autofocus="true">
								<label>Full Name</label>
								<span class="help-block" id="error"></span>                    
							</div>
							<div class="md-input-container md-float-label">
								<input name="email" id="email" type="text" class="form-control md-input" maxlength="50">
								<label>Email Id</label>
								<span class="help-block" id="error"></span>                    
							</div>
							<div class="md-input-container md-float-label">
								<input name="password" id="password" type="password" class="form-control md-input" >
								<label>Password</label>
								<span class="help-block" id="error"></span>                    
							</div>
							<div class="md-input-container md-float-label">
								<input name="cpassword" type="password" class="form-control md-input">
								<label>Retype Password</label>
								<span class="help-block" id="error"></span>                    
							</div>

							<div class="clearfix mt10">
								<div class="ui-checkbox ui-checkbox-primary md-float-label">
									<label>
										<input type="checkbox" name="terms" id="terms" value="1"> 
										<span>I agree to the terms and conditions for use.</span>
									</label>
								<span class="help-block" id="error"></span>                    
								</div>
							</div>
							<div class="clearfix text-center">
								<button type="submit" class="btn btn-success" id="btn-signup">Sign Up</button>
							</div>	

						</form>

					</div>

				</div> <!-- #end signin-container -->
			</div>



		</div> 
		<!-- #end content-container -->

	</div> <!-- #end main-container -->


	





	

	<!-- Dev only -->
	<!-- Vendors -->

<script src="assets/scripts/jquery-1.12.4-jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/scripts/jquery.validate.min.js"></script>
<script src="assets/scripts/register.js"></script>


<?php require_once("includes/footer.php"); ?>