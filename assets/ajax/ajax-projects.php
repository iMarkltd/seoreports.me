<?php

	require_once '../../includes/config.php';
	require_once '../../includes/functions.php';
	global $DBcon;
	$query = "SELECT * FROM semrush_users_account WHERE user_id=:user_id AND status=0";
	$stmt = $DBcon->prepare( $query );
	$stmt->bindParam(':user_id', $_SESSION['user_id']);
	$stmt->execute();
	$results = $stmt->fetchAll();
?>
			<?php foreach($results as $result) { 
				  $checkDownloadLink		=	checkDownloadLink($result['id']);
			?>
            <tr>
				<td>
					<div class="my-checkbox">
						<label>
							<input type="checkbox">
							<span class="checkbox"></span>
						</label>
					</div>
				</td>
				<td>
					<figure>
						<a href="#">
							<figcaption>
								G
							</figcaption>
						</a>
					</figure>
					<h6><a  href="seo_analytics_chart.php?id=<?php echo $result['id']; ?>" class="text-info" id="edit_row_ba" data-id="<?php echo $result['id']; ?>"><?php echo $result['domain_name']?><small>Hursh</small></a></h6>
					<cite><?php echo $result['domain_url']?></cite>
				</td>
				<td>
					8/12/2019
				</td>
				<td>
					<p><span>K</span> Total Keywords: <strong>50</strong></p>
					<p><span>T</span> Total Trafic: <strong>99</strong></p>
				</td>

				<td>
					<div class="dropdown">
						<a href="#" data-toggle="dropdown"><i class="fa fa-circle"></i> <i
								class="fa fa-circle"></i> <i class="fa fa-circle"></i></a>
						<ul class="dropdown-menu">
							<li>
								<a href="#"><i class="fa fa-pencil-square-o"></i> Edit</a>
							</li>
							<li>
								 <a data-id="<?php echo $result['id']; ?>" data-name="<?php echo $result['domain_name']?>" data-url="<?php echo $result['domain_url']?>" class="archive_row" href="javascript:;" data-placement="top" title="Archive" data-hover="tooltip"><i class="fa fa-archive"></i> Archive</a>
							</li>
							<li class="show-pdf-div-<?php echo $result['id']; ?>">
								<a class="seo_analytics_pdf" href="javascript:;" data-placement="top" data-id="<?php echo $result['id']; ?>" id="" title="PDF" data-hover="tooltip"><i class="fa fa-file-pdf-o"></i> PDF</a>
							</li>
							<li>
								<a class="" href="javascript:;" data-placement="top" title="Share" data-hover="tooltip" data-toggle="modal" data-id="<?php echo $result['id']; ?>" data-target="#shareModal"><i class="fa fa-share"></i> Share</a>
							</li>
							<li>
								<a class="ssss" href="javascript:;" data-placement="top" title="Email" data-hover="tooltip" data-toggle="modal" data-id="<?php echo $result['id']; ?>" data-target="#emailModal"><i class="fa fa-envelope"></i> Email</a>
							</li>
						</ul>
					</div>
				</td>
            </tr>
			<?php } ?>


