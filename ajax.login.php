<?php
// Error out if no method is supplied.
if (!isset($_POST['method']) || $_POST['method'] == "")
{
	$response["result"] = "error";
	$response["message"] = "No method was supplied.";
	exit(json_encode($response));
}

require_once("classes/class.login.php");

switch ($_POST['method'])
{
	case "login":
		// Error out if username/password are not supplied.
		if (!isset($_POST['username']) || $_POST['username'] == "" || !isset($_POST['password']) || $_POST['password'] == "")
		{
			$response["result"] = "error";
			$response["message"] = "Not enough data supplied.";
			exit(json_encode($response));
		}

		// Open the authentication object. Default values, as MySQL is required.
		$auth = new auth();

		// Attempt to log in with given credentials.
		$result = $auth->auth_check($_POST['username'],$_POST['password']);

		// Check result
		if ($result == true)
		{
			$response["result"] = "success";
			$response["redirect"] = "dashboard.php";
		}
		else
		{
			//echo '<tr><td>'.$insertId.'</td><td>'.$string.'</td><td>'.$_POST['url'].'</td><td><img src="img/stats.png" />&nbsp;<img src="img/pause.png" />&nbsp;<img src="img/delete.png" /></td></tr>';
			$response["result"] = "error";
			$response["message"] = "The username or password was incorrect.";
		}
		exit(json_encode($response));
	break;
	case "logout":
		$auth = new auth(false);	// $mysql = false as we do not need access to a mysql database to log out.
		$auth->log_out();
		if ($auth->is_logged_in() == false)
		{
			$response["result"] = "success";
			$response["message"] = "The user is now logged out.";
		}
		else
		{
			$response["result"] = "error";
			$response["message"] = "The log out process failed.";
		}
		exit(json_encode($response));
	break;
	default:
		$response["result"] = "error";
		$response["message"] = "Invalid method specified.";
		exit(json_encode($response));
	break;
}
?>