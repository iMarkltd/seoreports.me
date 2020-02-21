<?php
require_once('includes/config.php');
session_destroy();

include_once("includes/header.php");
?>



	<!-- main-container -->
	<div class="main-container clearfix login-page">

		 <div class="video-container">
			 <video autoplay loop class="fillWidth">
            <source src="assets/video/mp4/The-Coast.mp4" type="video/mp4" />Your browser does not support the video tag. I suggest you upgrade your browser.
            <source src="assets/video/webm/The-Coast.webm" type="video/webm" />Your browser does not support the video tag. I suggest you upgrade your browser.
        </video>
		</div>

		<!-- content-here -->
		<div class="content-container" id="content">
			<div class="page page-auth">
				<div class="auth-container">

					<div class="form-head mb10">
						<h1 class="site-logo h2 mb5 mt5 text-center text-uppercase text-bold"><a href="index.html"><img src="assets/images/logo.png" alt="iMark Infotech Pvt. Ltd." style="    max-width: 120px;" ></a></h1>
						<h5 class="text-normal h5 text-center">Sign In to Dashboard</h5>
					</div>

					<div class="form-container">

						<!-- json response will be here -->
						<div id="errorDiv"></div>
						<!-- json response will be here -->
						  <form method="post" role="form" class="form-horizontal" id="register-form" autocomplete="off">
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

							<div class="clearfix">
								<div class="ui-checkbox ui-checkbox-primary left">
									<label>
										<input type="checkbox">
										<span>Remember me</span>
									</label>
								</div>
								<div class="right">
									<a href="forget-pass.php" class="text-success small">Forget your password?</a>
								</div>
							</div>

							<div class="btn-group btn-group-justified mb15">
								<button type="submit" class="btn btn-success" id="btn-signup" style="width: 100%;">Sign In</button>
								<!-- <div class="btn-group">
									<button type="button" class="btn btn-facebook"><span class="ion ion-social-facebook"></span>&nbsp;&nbsp;Facebook</button>
								</div>
								<div class="btn-group">
									<button type="submit" class="btn btn-success" id="btn-signup">Sign In</button>
								</div> -->
							</div>
							<div class="clearfix text-center small">
								<p><a href="/new-page/privacy_policy.html">Privacy Policy </a></p>
							</div>

<?php /*							<div class="clearfix text-center small">
								<p>Don't have an account? <a href="register.php">Create Now</a></p>
							</div>
*/ ?>
						</form>
					</div>

				</div> <!-- #end signin-container -->
			</div>



		</div>
		<!-- #end content-container -->

	</div> <!-- #end main-container -->


<script src="assets/scripts/jquery-1.12.4-jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/scripts/jquery.validate.min.js"></script>
<script src="assets/scripts/login.js"></script>

<?php
include_once("includes/footer.php");
?>