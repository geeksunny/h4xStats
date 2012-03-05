<?php
class geoip
{
	public $dbh = false;							// Database handler for MySQL class usage.
	private $db_tbl = "geoip";						// Database table name.
	public $imgdir = "img/flags/";					// Directory for flag icons. Relative to the root of the file using this class.

	public function __construct($dbh = false)
	{
		$this->imgdir = $this->get_server_dir(1, $this->imgdir);

		// If a database handler is passed, this class will tie into the included MySQL class for built in database functions.
		if ($dbh)
		{
			$this->dbh = $dbh;
			if ($this->dbh->prefix)
				$this->db_tbl = $this->dbh->prefix.$this->db_tbl;
		}
	}

	// Checks the IP address against the database
	public function checkIP($ip, $fullname = false)
	{
		// This function relies on the $dbh variable being established. If this is still FALSE, return -1.
		if (!$this->dbh)
			return "-1";

		$ipnum = $this->toIpNum($ip);
		$query = "SELECT `country_code`,`country_name` FROM `".$this->db_tbl."` WHERE '".$ipnum."' >= `begin_ip_num` AND '".$ipnum."' <= `end_ip_num` LIMIT 1;";
		$result = $this->dbh->getRow($query);
		if ($fullname)
			return $result['country_name'];
		else
			return $result['country_code'];
	}

	// Convert an IP address to a decimal value
	public function toIpNum($ip)
	{
		$ipArr = explode(".",$ip);
		$ipnum = (16777216 * $ipArr[0]) + (65536 * $ipArr[1]) + (256 * $ipArr[2]) + ($ipArr[3]);
		return $ipnum;
	}
	// Convert a decimal value to an IP address
	public function toIpStr($num)
	{
		$w = ($num / 16777216) % 256;
		$x = ($num / 65536) % 256;
		$y = ($num / 256) % 256;
		$z = $num % 256;
		return "$w.$x.$y.$z";
	}

	// Returns the current server directory. // TODO: MIGHT BE MOVED TO ANOTHER CLASS IN THE FUTURE.
	public function get_server_dir($steps = 0, $suffix = false)
	{
		// Get the current subdirectory. Grabs PHP_SELF and removes the filename.
		$current_subdir = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],"/")+1);
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
		$destination = substr($target_directory,strrpos($target_directory,$current_subdir));
		// If a suffix is provided, append it to the end-result.
		if ($suffix)
			$destination .= $suffix;

		return $destination;
	}

	public function __destruct()
	{
	}
}
?>