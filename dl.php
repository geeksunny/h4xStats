<?php
// Error out if no link code is supplied, or if link code is not an integer.
// TODO: Fix up the error messaging.
if (!isset($_GET['url']) || $_GET['url'] == "")
{
	echo "error - incorrect parameters provided.";
	return false;
}

require_once("classes/class.mysql.php");
$dbh = new dbh();
include("config/config.php");

$link = $dbh->getRow("SELECT * FROM `".$dbh->prefix."links` WHERE `string`='".$_GET['url']."';");

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
		require_once("classes/class.geoip.php");
		$geoip = new geoip($dbh);
		$params['country'] = $geoip->checkIP($_SERVER['REMOTE_ADDR']);
	}
	else
		$params['country'] = "" ;

	$insertId = $dbh->insertRow("h4x_stats_log",$params);

	// Check for errors
	if ($dbh->checkError(true) == true)
	{
		// E-mail the admin of the error.
		// TODO: Add the e-mail alerts here. The "echo error" is a temporary solution.
		echo "error - could not log visit.";
		return false;
	}
	else
	{
		header('HTTP/1.1 301 Moved Permanently');
		header("Location: ".$link['url']);
	}
}
else
{
	echo "error - link does not exist.";
	return false;
}
?>