<?php
class auth
{
	/*---Database config---*/
	public $dbh;									// Database handler for MySQL class usage.
	private $db_tbl = "users";			// Database table name.

	// $_SESSION[] key names...
	private $s_key_uid			= "uid";			// Array key name to use to store the current uid.
	private $s_key_username		= "username";		// Array key name to use to store the current username.
	private $s_key_displayname	= "displayname";	// Array key name to use to store the current display name.
	private $s_key_email		= "email";			// Array key name to use to store the current email address.

	// Other default configurations...
	public $redirect_target = "login.php";			// Default login script location

	public function __construct($mysql = true, $redirect = false, $redirect_target = false, $logged_in = false)
	{
		// Start the session.
		session_start();

		// Redirection process.
		if ($redirect)
		{
			// Set the new login script location if given.
			if ($redirect_target)
				$this->redirect_target = $redirect_target;

			// If the user is not logged in, redirect to the login script location.
			if ($this->is_logged_in() == $logged_in)
			{
				header('HTTP/1.1 301 Moved Permanently');
				header("Location: ".$this->redirect_target);
			}
		}

		// Initializes MySQL object if requested.
		if ($mysql)
		{
			require_once(dirname(__FILE__) . "/class.mysql.php");
			$this->dbh = new dbh();
			if ($this->dbh->prefix)
				$this->db_tbl = $this->dbh->prefix.$this->db_tbl;
		}
	}

	// Authorization function.
	public function auth_check($username, $password)
	{
		$hash = $this->gen_password_hash($username, $password);

		$query = "SELECT `id`,`password`,`name`,`email` FROM `".$this->db_tbl."` WHERE `username`='".$username."';";
		$result = $this->dbh->getRow($query);

		// If the $hash matches the hash stored in the database, log the user in.
		if ($hash == $result['password'])
		{
			$this->log_in($result['id'], $username, $result['name'], $result['email']);
			return true;
		}
		// If the hashes do not match, return false.
		else
		{
			return false;
		}
	}

	// Register the session and log in the user.
	public function log_in($uid, $username, $displayname = false, $email = false)
	{
		$_SESSION[$this->s_key_uid] = $uid;
		$_SESSION[$this->s_key_username] = $username;
		if ($displayname)
			$_SESSION[$this->s_key_displayname] = $displayname;
		if ($email)
			$_SESSION[$this->s_key_email] = $email;
	}

	// Register the session and log in the user.
	public function log_out($redirect = false)
	{
		$_SESSION = array();
		if ($redirect)
		{
			header('HTTP/1.1 301 Moved Permanently');
			header("Location: ".$redirect);
		}
	}

	// Check to see if user is logged in. Returns true / false
	public function is_logged_in()
	{
		if (isset($_SESSION[$this->s_key_uid]) && !empty($_SESSION[$this->s_key_uid]))
			return true;
		else
			return false;
	}

	// Create a password hash.
	public function gen_password_hash($username, $password)
	{
		// Hash is salted with the username and extra characters.
		return md5($username."_._._".$password);
	}

	// Returns the user id value
	public function get_uid()
	{
		if ($this->is_logged_in())
			return $_SESSION[$this->s_key_uid];
		else
			return false;
	}
	// Returns the user name
	public function get_username()
	{
		if ($this->is_logged_in())
			return $_SESSION[$this->s_key_username];
		else
			return false;
	}
	// Returns the user display name
	public function get_displayname()
	{
		if ($this->is_logged_in())
			return $_SESSION[$this->s_key_displayname];
		else
			return false;
	}
	// Returns the user name
	public function get_email()
	{
		if ($this->is_logged_in())
			return $_SESSION[$this->s_key_email];
		else
			return false;
	}

	// Returns the current server directory. // TODO: MIGHT BE MOVED TO ANOTHER CLASS IN THE FUTURE.
	public function get_server_path($steps = 0, $suffix = false, $url = false, $protocol = false)
	{
		// Get the current subdirectory.
		//$current_subdir = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1); //Grabs PHP_SELF and removes the filename.
		$current_subdir = str_replace( basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['PHP_SELF'] );
		// Get the current filename path of this filename.
		$target_directory = dirname(__FILE__);
		// If $steps is set, cycle through and step out however many times given.
		if ($steps)
		{
			for ($step = 0; $step < $steps; $step++)
			$target_directory = dirname($target_directory);
		}
		// Append a trailing slash to the directory.
		$target_directory .= "/";
		// Go to the root of the web server path. Removes the local filesystem prefix.
		$path = substr($target_directory,strrpos($target_directory,$current_subdir));
		// If a suffix is provided, append it to the end-result.
		if ($suffix)
			$path .= $suffix;
		// If URL is set to true
		if ($url)
		{
			$path = $_SERVER['HTTP_HOST'].$path;
			if ($protocol)
			{
				$protocol_array = explode('/',$_SERVER['SERVER_PROTOCOL']);
				$protocol_string = strtolower($protocol_array[0]).'://';
				$path = $protocol_string.$path;
			}
		}

		return $path;
	}

	public function __destruct()
	{
		// session_close(); ??
	}
}
?>