<?php
//session_start();
//var_dump($_SESSION);
//die;
require_once("classes/class.login.php");
$auth = new auth();	// default constructor values, uses mysql, does not redirect.

//$auth->log_out();

if ($auth->is_logged_in() == false)
	echo "NO";
else
	echo "YES";

/*$result = $auth->auth_check("geeksunny","kickman");
if ($result == true)
{
	echo "LOGGED IN!!<br /><br />";
	echo "Session Variable dump: ".var_dump($_SESSION)."<br /><br />";
	echo "Session name: ".session_name()."<br /><br />";
	echo "Logging out...";
	$auth->log_out();
}
else
	echo "Log in failed...";
*/

//$auth = new auth(false,true,"http://beta.h4xful.net/tools/download/");
//echo "NOT REDIRECTED!!!";

//echo md5("geeksunny_._._kickman");
?>