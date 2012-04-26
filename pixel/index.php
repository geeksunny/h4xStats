<?php
// Kill the script if no id is passed.
if (!isset($_GET['id']) || $_GET['id'] == "")
{
	die();
}

require_once("../classes/class.mysql.php");
$dbh = new dbh();
include("../config/config.php");

$link = $dbh->getRow("SELECT * FROM `".$dbh->prefix."pixels` WHERE `string`='".$_GET['id']."' AND `enabled`='1';");

//if ($dbh->checkError(true) == false)
if ($link != 0)
{
	// Insert the new record into the logs
	$params = array();
	$params['link_id'] = $link['id'];
	$params['ip'] = $_SERVER['REMOTE_ADDR'];
	$params['referer'] = $_SERVER['HTTP_REFERER'];
	$params['datetime'] = date("Y-m-d H:i:s");

	// Checks the GeoIP database if GeoIP is enabled.
	if ($config['use_geoip'] == true)
	{
		require_once("../classes/class.geoip.php");
		$geoip = new geoip($dbh);
		$params['country'] = $geoip->checkIP($_SERVER['REMOTE_ADDR']);
	}
	else
		$params['country'] = "" ;

	$insertId = $dbh->insertRow("h4x_stats_pixel_log",$params);

	// Check for errors
	if ($dbh->checkError(true) == true)
	{
		//echo "error - could not log visit.";
	}
	else
	{
		//echo "success";
	}
}
/*
else
{
	echo "error - pixel does not exist.";
}
*/
?>