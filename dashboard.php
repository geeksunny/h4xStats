<?php
//TODO: Implement username/password system.
//TODO: Set `userid` calls to user's id number when implemented
// Initialize the MySQL database handle
//require_once("classes/class.mysql.php");
//$dbh = new dbh();

require_once("classes/class.login.php");
$auth = new auth(true,true,"index.php");
$dbh = $auth->dbh;

// Get any existing links from the database
$links = $dbh->sqlQuery("SELECT * FROM `".$dbh->prefix."links` WHERE `userid`='".$auth->get_uid()."' ORDER BY `date_added` DESC");
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>h4x.stats | Add File</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width,initial-scale=1">

	<!-- Libs -->
	<script src="js/libs/jquery-1.7.1.min.js"></script>
	<script src="js/libs/jquery.simplemodal.js"></script>
	<link rel="stylesheet" href="css/confirm.css">
	<script src="js/confirm.js"></script>
	<link rel="stylesheet" href="js/libs/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/libs/fancybox/jquery.fancybox.pack.js"></script>
	<!-- CSS / JavaScript Functions -->
	<script src="js/functions.js"></script>
	<link rel="stylesheet" href="css/style.css">
</head>

<body>
	<div id="container">
		<header>
			Logged in as <?=$auth->get_username()?> | <a href="log_out.php">Log Out</a>
		</header>
		<div id="main" role="main">
			<div class="title clearfix">
				h4x.stats
				<span class="subtitle">Add a link.</span>
			</div>

			<form>
				<input type="submit" value="Add" id="add_url" class="bigsubmit button" style="float:right;" />
				<div style="overflow: hidden; padding-right: 30px;">
					<input type="text" name="url" id="url_box" class="biginput wide" value="Enter a URL..." style="color:#ccc;" onfocus="inputClick(this, 'Enter a URL...');" onblur="inputBlur(this, 'Enter a URL...');" />
				</div>
				<div id="error_container">&nbsp;<label class="error" for="url" id="url_error"></label></div>
				<div>
					<!--&nbsp;-->
					<table width="100%" id="links_table" <?php if (!$links) echo 'style="display: none;"'; ?>>
						<thead>
							<tr>
								<td style="width:5%;">#</td>
								<td style="width:9%;">String</td>
								<td style="width:80%;">URL</td>
								<td style="width:6%; min-width: 65px; text-align: center;">Actions</td>
							</tr>
						</thead>
						<tbody id="links_listing">
						<?php
						if ($links)
						{
							foreach ($links as $link)
							{
								$enabled = ($link['enabled'] == "1") ? "pause" : "play";
								echo '<tr><td>'.$link['id'].'</td><td>'.$link['string'].'</td><td>'.$link['url'].'</td><td id="'.$link['id'].'"><img src="img/link.png" class="link" /><img src="img/stats.png" class="stats" /><img src="img/'.$enabled.'.png" class="toggle" /><img src="img/delete.png" class="delete" /></td></tr>';
							}
						}
						?>
						</tbody>
					</table>
				</div>
			</form>
		</div>
		<footer>
			h4x.stats written by <a href="http://www.h4xful.net/">Justin Swanson</a>.
		</footer>

		<!-- modal content -->
		<div id='confirm'>
			<div class='header'><span>Confirm</span></div>
			<div class='message'></div>
			<div class='buttons'>
				<div class='no simplemodal-close'>No</div><div class='yes'>Yes</div>
			</div>
		</div>
	</div> <!--! end of #container -->
</body>
</html>