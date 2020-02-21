
			<nav class="site-nav clearfix" role="navigation">
				<div class="profile clearfix mb15">
					<?php 
						if(PAGE_NAME != 'seo_view_details.php' || PAGE_NAME != 'seo_pdf.php') { 
						$user_data 	= getUserImage($_SESSION['user_id']);
						$user_image	=	'assets/images/no-user-image.png';
						$url	=	checkUserProfileImage();
						if(!empty($url['file_name'])) {
							$user_image	=	$url['return_path'];
						}
					?>
					<img src="<?php echo $user_image; ?>" alt="user-pic" class="user_pic">
					<div class="group">
						<h5 class="name"><?php echo $user_data[0]['name']; ?></h5>
					</div>
					<?php } ?>
				</div>

				<!-- navigation -->
				<ul class="list-unstyled clearfix nav-list mb15">
					<li class="active">
						<a href="home.php">
							<i class="ion ion-monitor"></i>
							<span class="text">Dashboard</span>
						</a>
					</li>
					<li class="active">
						<a href="google_accounts.php" data-toggle="tooltip" data-placement="right" title="Add New Analytics Account!">
							<i class="ion ion-arrow-graph-up-right"></i>
							<span class="text">Google Analytics Linked</span>
						</a>
					</li>
					<li class="active">
						<a href="google_lisited_account.php" data-toggle="tooltip" data-placement="right" title="Update Analytics Account Data">
							<i class="ion ion-refresh"></i>
							<span class="text">Google Account Details</span>
						</a>
					</li>
<!--
					<li>
						<a href="#">
							<i class="fa fa-google"></i>
							<span class="text">Google Analytics</span>
						</a>
					</li>
-->

				</ul> <!-- #end navigation -->
			</nav>

			<!-- nav-foot -->

			<footer class="nav-foot">
				<p><?php echo date('Y'); ?> &copy; <span>Imark</span></p>
			</footer>
