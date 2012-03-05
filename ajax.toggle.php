<?php
// Error out if no method is supplied.
if (!isset($_POST['method']) || $_POST['method'] == "")
{
	$response["result"] = "error";
	$response["message"] = "No method was supplied.";
	exit(json_encode($response));
}

require_once("classes/class.login.php");
$auth = new auth(true);
$dbh = $auth->dbh;

// Error out if user isn't logged in.
if ($auth->is_logged_in() == false)
{
	$response["result"] = "error";
	$response["message"] = "User is not logged in.";
	exit(json_encode($response));
}

// Grab the uid
$uid = $auth->get_uid();

switch ($_POST['method'])
{
	case "toggle":
		// Error out if link ID is not supplied.
		if (!isset($_POST['id']) || $_POST['id'] == "")
		{
			$response["result"] = "error";
			$response["message"] = "Link ID not supplied.";
			exit(json_encode($response));
		}

		// Verify link and ownership.
		$link = $dbh->getRow("SELECT * FROM `".$dbh->prefix."links` WHERE `id`='".$_POST['id']."' AND `userid`='".$uid."';");
		// TODO: Make MySQL table names modular?

		// Check result
		if ($link)
		{
			if ($link['enabled'] == "1")
				$new_state = "0";
			else
				$new_state = "1";
			$dbh->updateRow($dbh->prefix."links","`id`='".$_POST['id']."'",array("enabled"=>$new_state));
			if ($dbh->checkError(true) == false)
			{
				$response["result"] = "success";
				$response["state"] = $new_state;
			}
			else
			{
				$response["result"] = "error";
				$response["message"] = "The link could not be updated.";
			}
		}
		else
		{
			//echo '<tr><td>'.$insertId.'</td><td>'.$string.'</td><td>'.$_POST['url'].'</td><td><img src="img/stats.png" />&nbsp;<img src="img/pause.png" />&nbsp;<img src="img/delete.png" /></td></tr>';
			$response["result"] = "error";
			$response["message"] = "The given link does not exist.";
		}
		exit(json_encode($response));
	break;
	case "delete":
		$delete = $dbh->deleteRow($dbh->prefix."links","`id`='".$_POST['id']."'");
		if ($delete)
		{
			$response["result"] = "success";
		}
		else
		{
			$response["result"] = "error";
			$response["message"] = "There was an error deleting your link.";
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