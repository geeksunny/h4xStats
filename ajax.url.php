<?php
// Error out if no method is supplied.
if (!isset($_POST['method']) || $_POST['method'] == "")
{
	$response["result"] = "error";
	$response["message"] = "No method was specified.";
	exit(json_encode($response));
}

require_once("classes/class.login.php");
$auth = new auth();
$dbh = $auth->dbh;

switch ($_POST['method'])
{
	case "add":
		// Error out if url is not specified.
		if (!isset($_POST['url']) || $_POST['url'] == "" || $_POST['url'] == "Enter a URL...")
		{
			$response["result"] = "error";
			$response["message"] = "No URL was specified.";
			exit(json_encode($response));
		}

		// Generate the short string. // TODO: Add dupe checking.
		$string = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5);

		// Insert the row
		$params = array();
		$params['userid'] = $auth->get_uid();
		$params['url'] = $_POST['url'];
		$params['string'] = $string;
		$params['date_added'] = date("Y-m-d H:i:s");
		$params['enabled'] = '1';
		$insertId = $dbh->insertRow($dbh->prefix."links",$params);

		// Check for errors
		if ($dbh->checkError(true) == true)
		{
			$response["result"] = "error";
			$response["message"] = "There was an error with the database.";
		}
		else
		{
			//echo '<tr><td>'.$insertId.'</td><td>'.$string.'</td><td>'.$_POST['url'].'</td><td><img src="img/stats.png" />&nbsp;<img src="img/pause.png" />&nbsp;<img src="img/delete.png" /></td></tr>';
			$response["result"] = "success";
			$response["id"] = $insertId;
			$response["string"] = $string;
			$response["url"] = $_POST['url'];
		}
		exit(json_encode($response));
	break;
	case "toggle":
		// Toggle the link's active status
	break;
	case "delete":
		// Delete the link
	break;
	default:
		$response["result"] = "error";
		$response["message"] = "Invalid method specified.";
		exit(json_encode($response));
	break;
}
?>