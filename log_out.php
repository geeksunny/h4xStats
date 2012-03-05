<?php
require_once("classes/class.login.php");
$auth = new auth();	// default constructor values, uses mysql, does not redirect.

$auth->log_out("index.php");

// If redirect fails, attempt it again?
if ($auth->is_logged_in() == false)
{
	header('HTTP/1.1 301 Moved Permanently');
	header("Location: index.php");
}
else
{
	header('HTTP/1.1 301 Moved Permanently');
	header("Location: ".$_SERVER['HTTP_REFERER']);
}
?>