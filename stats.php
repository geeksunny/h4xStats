<?php
// Initialize the Auth/MySQL database handle
require_once("classes/class.login.php");
$auth = new auth(true,true,"index.php");
$dbh = $auth->dbh;

$id = $_GET['id'];
$path = $auth->get_server_path(false);// Grab the current server path.

// Check if the link exists.
$link = $dbh->sqlQuery("SELECT `userid`,`url`,`string`,`date_added`,`enabled` FROM `".$dbh->prefix."links` WHERE `id`='".$id."' AND `userid`='".$auth->get_uid()."';",true,false);
// Error out if link could not be found... Touch this up with some CSS styling later!
if (!$link)
	die("Link could not be found.");

// amCharts class object initializations.
require_once("classes/class.amcharts.php");
$charts = new amCharts();
$charts->addChart($pie_country,"pie","Country","Clicks","country");
$charts->addChart($pie_referer,"pie","Referer","Clicks","referer");

// Grab all data for stats report!
$data = $dbh->sqlQuery("SELECT `ip`,`referer`,`datetime`,`country` FROM `".$dbh->prefix."log` WHERE `link_id`='".$id."';");

// Compile data...
$stats_countries = array();
$stats_referers = array();
$total_clicks = 0;
foreach ($data as $row)
{
	if ($row['referer'] == '')
		$row['referer'] = "None";

	$stats_countries[$row['country']]++;
	$stats_referers[$row['referer']]++;
	$total_clicks++;
}
// Get full names for countries...
$country_names = array();
foreach ($stats_countries as $code=>$row)
{
	$query = $dbh->sqlQuery("SELECT `country_name` FROM `".$dbh->prefix."geoip` WHERE `country_code`='".$code."';",TRUE,FALSE);
	$country_names[$code] = $query['country_name'];
}

// Populate the pie charts!
foreach ($stats_countries as $code=>$value)
{
	$pie_country->add(array($country_names[$code]=>$value));
}
foreach ($stats_referers as $referer=>$value)
{
	$pie_referer->add(array($referer=>$value));
}
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>h4x.stats | Statistics</title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width,initial-scale=1">

	<link rel="stylesheet" href="css/style.css">
	<script src="js/libs/jquery-1.7.1.min.js"></script>
	<script src="js/functions.js"></script>
    <?=$charts->get_js_includes()?>
	<?=$charts->get_js_chart()?>
</head>

<body>
	<div id="container">
		<div id="stats" role="main">
			<div class="title clearfix">
				<?=$path.$link['string']?>
				<!-- <span class="subtitle">Stats.</span> -->
			</div>
			<table>
				<thead>
					<tr><td colspan="2"><?=$link['url']?></td></tr>
				</thead>
				<tbody>
					<tr>
						<td><?=$total_clicks?></td>
						<td>Clicks</td>
					</tr>
				</tbody>
			</table>
		</div>
		<!--<div id="chartdiv" style="width: 640px; height: 400px;"></div>-->
		Clicks by country!
		<?=$pie_country->get_chart_div()?>
		Click referers!
		<?=$pie_referer->get_chart_div()?>
	</div> <!--! end of #container -->
</body>
</html>