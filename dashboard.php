<?php
//TODO: Implement a "link name" and "link description" system
// Initialize the MySQL database handle
//require_once("classes/class.mysql.php");
//$dbh = new dbh();

require_once("classes/class.login.php");
$auth = new auth(true,true,"index.php");
$dbh = $auth->dbh;

// Get any existing links from the database
$links = $dbh->sqlQuery("SELECT * FROM `".$dbh->prefix."links` WHERE `userid`='".$auth->get_uid()."' ORDER BY `date_added` DESC");
// Get any existing pixels from the database
$pixels = $dbh->sqlQuery("SELECT * FROM `".$dbh->prefix."pixels` WHERE `userid`='".$auth->get_uid()."' ORDER BY `date_added` DESC");
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>h4x.stats | Dashboard</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width,initial-scale=1">

	<!-- Libs -->
	<script src="js/libs/jquery-1.7.1.min.js"></script>
	<script src="js/libs/jquery.simplemodal.js"></script>
	<link rel="stylesheet" href="css/confirm.css">
	<script src="js/confirm.js"></script>
	<link rel="stylesheet" href="css/basic.css">
	<link rel="stylesheet" href="js/libs/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/libs/fancybox/jquery.fancybox.pack.js"></script>
	<!-- CSS / JavaScript Functions -->
	<script src="js/functions.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<div id="url_root" style="display:none;"><?=$auth->get_server_path(0,false,true,true)?></div>
	<div id="container">
		<header>
			Logged in as <?=$auth->get_username()?> | <a href="log_out.php">Log Out</a>
		</header>
		<div id="nav">
			<ul>
				<li class="links selected">Links</li>
				<li class="pixels">Pixels</li>
			</ul>
		</div>
		<div id="main" role="main">
			<div class="title clearfix">
				h4x.stats
				<span id="links_subtitle" class="subtitle">Add a link.</span>
				<span id="pixels_subtitle" class="subtitle" style="display:none;">Manage your pixels.</span>
			</div>


			<div id="links" class="page">
				<form>
					<input type="submit" value="Add" id="add_url" class="bigsubmit button" style="float:right;" />
					<div style="overflow: hidden; padding-right: 30px;">
						<input type="text" name="url" id="url_box" class="biginput wide" value="Enter a URL..." style="color:#ccc;" onfocus="inputClick(this, 'Enter a URL...');" onblur="inputBlur(this, 'Enter a URL...');" />
					</div>
					<div id="url_error_container">&nbsp;<label class="error" for="url_box" id="url_error"></label></div>
					<div>
						<!--&nbsp;-->
						<table width="100%" id="links_table" class="table" <?php if (!$links) echo 'style="display: none;"'; ?>>
							<thead>
								<tr>
									<td class="round_topleft" style="width:80%;">Target URL</td>
									<td class="round_topright" style="width:6%; min-width: 65px; text-align: center;">Actions</td>
								</tr>
							</thead>
							<tbody id="links_listing">
							<?php
							if ($links)
							{
								$zebra = false;				// Initializing the zebra stripe flag.
								$count = count($links);		// Used for bottom-row rounded borders.
								$i = 1;						// loop iterator.
								foreach ($links as $link)
								{
									$row_class = ($zebra) ? ' class="zebra"' : "";		// Sets class to use for the table row.
									// Sets classes to use on outside cells on the last row, so the corners are rounded.
									if ($i == $count)
									{
										$left_class = ' class="round_bottomleft"';
										$right_class = ' class="round_bottomright"';
									}
									if ($link['enabled'] == "1")
									{
										$enabled = "pause";
									}
									else
									{
										$enabled = "play";
										$row_class = ($row_class == "") ? ' class="paused"' : ' class="zebra paused"';
									}
									$enabled = ($link['enabled'] == "1") ? "pause" : "play";
									// TODO: Make row go gray or faded when the link is paused.
									echo '<tr'.$row_class.'><td'.$left_class.'>'.$link['url'].'</td><td'.$right_class.' id="'.$link['id'].'"><img id="'.$link['string'].'" src="img/link.png" class="link" /><img src="img/stats.png" class="stats" /><img src="img/'.$enabled.'.png" class="toggle" /><img src="img/delete.png" class="delete" /></td></tr>';
									$zebra = !$zebra;	// Reversing the zebra stripe flag.
									$i++;
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</form>
			</div>
			<div id="pixels" class="page" style="display:none;">
				<form>
					<input type="submit" value="Add" id="pixel_url" class="bigsubmit button" style="float:right;" />
					<div style="overflow: hidden; padding-right: 30px;">
						<input type="text" name="url" id="pixel_box" class="biginput wide" value="Name your Pixel..." style="color:#ccc;" onfocus="inputClick(this, 'Name your Pixel...');" onblur="inputBlur(this, 'Enter a URL...');" />
					</div>
					<div id="pixel_error_container">&nbsp;<label class="error" for="url_box" id="pixel_error"></label></div>
					<div>
						<!--&nbsp;-->
						<table width="100%" id="pixels_table" class="table" <?php if (!$links) echo 'style="display: none;"'; ?>>
							<thead>
								<tr>
									<td class="round_topleft" style="width:80%;">Pixel Description</td>
									<td class="round_topright" style="width:6%; min-width: 65px; text-align: center;">Actions</td>
								</tr>
							</thead>
							<tbody id="links_listing">
							<?php
							if ($pixels)
							{
								$zebra = false;				// Initializing the zebra stripe flag.
								$count = count($pixels);		// Used for bottom-row rounded borders.
								$i = 1;						// loop iterator.
								foreach ($pixels as $pixel)
								{
									$row_class = ($zebra) ? ' class="zebra"' : "";		// Sets class to use for the table row.
									// Sets classes to use on outside cells on the last row, so the corners are rounded.
									if ($i == $count)
									{
										$left_class = ' class="round_bottomleft"';
										$right_class = ' class="round_bottomright"';
									}
									if ($pixel['enabled'] == "1")
									{
										$enabled = "pause";
									}
									else
									{
										$enabled = "play";
										$row_class = ($row_class == "") ? ' class="paused"' : ' class="zebra paused"';
									}
									$enabled = ($pixel['enabled'] == "1") ? "pause" : "play";
									// TODO: Make row go gray or faded when the link is paused.
									echo '<tr'.$row_class.'><td'.$left_class.'>'.$pixel['pixel_name'].'</td><td'.$right_class.' id="'.$pixel['id'].'"><img id="'.$pixel['string'].'" src="img/link.png" class="link" /><img src="img/stats.png" class="stats" /><img src="img/'.$enabled.'.png" class="toggle" /><img src="img/delete.png" class="delete" /></td></tr>';
									$zebra = !$zebra;	// Reversing the zebra stripe flag.
									$i++;
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</form>
			</div>
		</div>
		<footer>
			h4x.stats written by <a href="http://www.faecbawks.com/">Justin Swanson</a>.
		</footer>

		<!-- modal content (delete) -->
		<div id='confirm'>
			<div class='header'><span>Confirm</span></div>
			<div class='message'></div>
			<div class='buttons'>
				<div class='no simplemodal-close'>No</div><div class='yes'>Yes</div>
			</div>
		</div>
		<!-- modal content (link) -->
		<div id='modal-link-content'>
			<div class="title">Copy Link...</div><br />
			<input type="text" value="" id="modal-link-value" />
		</div>
	</div> <!--! end of #container -->
</body>
</html>