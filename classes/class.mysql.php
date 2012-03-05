<?php
class dbh
{
	// Database handler
	public $conn;
	public $prefix = false;

	public function __construct($db_connection = "default")
	{
		$db_file = dirname(dirname(__FILE__)) . "/config/mysql.".$db_connection.".php";

		if (!is_file($db_file))
		{
			//echo "Cannot open db file!"; die;
			return false;
		}
		else
		{
			require_once($db_file);
		}

		/*---MySQL connection---*/
		if ($this->conn = mysql_connect($db['db_host'], $db['db_user'], $db['db_pass']))
		{
			if (!mysql_select_db($db['db_name'], $this->conn))
			{
				//$this->error = mysql_error();
				return false;
			}
		}
		else
		{
			//$this->error = mysql_error();
			return false;
		}

		// Set the table prefix if being used.
		if ($db['db_prefix'])
			$this->prefix = $db['db_prefix'];

		return true;
	}

	public function __destruct()
	{
		mysql_close($this->conn);
	}

	// Checks for any error from previous query
	public function checkError($simple = false)
	{
		$error = mysql_error();

		if ($error == "")
		{
			return false;
		}
		else
		{
			// if $simple is set to true, will only return a true/false
			if ($simple == true)
				return true;
			// if $simple is set to false, will return the full error string
			else
				return $error;
		}
	}
	
	function insertRow( $table, $values, $htmlspecial=false)
	{
		$sets = $this->dbBuildSets( $values, $htmlspecial );
		if ( $sets == '' )
			return 0;
		$sql = "INSERT INTO $table SET $sets";
		$this->dbDoQuery( $sql );
		return mysql_insert_id($this->conn);
	}

	function insertRowIgnore( $table, $values, $htmlspecial=false) {
		$sets = $this->dbBuildSets( $values, $htmlspecial );
		if ( $sets == '' )
			return 0;
		$sql = "INSERT IGNORE INTO $table SET $sets";
		$this->dbDoQuery( $sql );
		return mysql_insert_id($this->conn);
	}

	function updateRow( $table, $where, $values, $htmlspecial=false ) {
		$sets = $this->dbBuildSets( $values, $htmlspecial );
		if ( $sets == '' )
			return 0;
		$sql = 	 "UPDATE $table SET $sets WHERE $where";
		return $this->sqlQuery( $sql, false, false );
	}

	function deleteRow( $table, $where) {
		$sql = 	 "DELETE FROM $table WHERE $where";
		return $this->sqlQuery( $sql, false, false );
	}

	function dbBuildSets( $values, $htmlspecial=false)
	{
		$sets = '';
		foreach ( $values as $field => $value ) {
			if ( $sets != '' )
				$sets .= ', ';
			$sets .= "$field=";
			if ( strtolower($value) == 'now()' or $value == "$field+1" )
				$sets .= $value;					   // leave alone if setting to now() or incrementing by one
			else if ( $htmlspecial )
				$sets .= "'" . addslashes( htmlspecialchars( trim($value), ENT_QUOTES ) ) . "'";
			else
				$sets .= "'" . addslashes( trim($value) ) . "'";
		}
		return $sets;
	}

	function dbDoQuery( $sql ) {
		$dbresult = mysql_query( $sql, $this->conn )
			         or die ( "Query '$sql': " . mysql_error() );
		return $dbresult;
	}

	function cleanInput($value){
		// Stripslashes
		if (get_magic_quotes_gpc()){
		  $value = stripslashes($value);
		}
		// Quote if not a number
		if (!is_numeric($value)){
		  $value =  mysql_real_escape_string($value, $this->conn);
		}
		return $value;
	}

###### Database Query
###### $return FALSE indicates returns boolean for INSERTS, UPDATES, etc
###### $return TRUE indicates SELECTS
###### $complex TRUE indicates multiple rows returned
###### $complex FALSE indicates single row returned
###########################################################
	public function sqlQuery($query, $return = TRUE, $complex = TRUE) {
		$this->results = mysql_query($query, $this->conn);

		if($return)
		{
			$total = 0;
			if($this->results != false)
				$total = mysql_num_rows($this->results);
			// get field names
			if($total==0) {
				return FALSE;
			} else {
				$i = 0;
				$this->field_names = array();
				while($i < mysql_num_fields($this->results)) {
					$name = mysql_field_name($this->results, $i);
					array_push($this->field_names, $name);
					$i++;
				}

				// get results as an array
				$this->mysql_results = array();
				if($complex) {
					while($rows = mysql_fetch_array($this->results)) {
						$this_arr = array();
						foreach($this->field_names as $field) {
							$this_arr[$field] = $rows[$field];
						}
						array_push($this->mysql_results, $this_arr);
					}
				} else {
					$thisrow = mysql_fetch_assoc($this->results);
					foreach($this->field_names as $field) {
						$this->mysql_results[$field] = $thisrow[$field];
					}
				}

				// return results
				return $this->mysql_results;
			}
		}
		else
		{
			// Determines if mysql_affected_rows() is relavent to $query.	// For "INSERT","UPDATE","REPLACE","DELETE" queries.
			$type = strtolower(substr($query,0,strpos($query," ")));		// Grabs first word of $query.
			if (in_array($type,array("insert","update","replace","delete")))
			{
				$affected = mysql_affected_rows($this->conn);
				if($affected == -1) {
					return FALSE;
				} elseif ($affected == 0) {
					return 0;
				} else {
					return TRUE;
				}
			}
			else	// For all other query types.
			{
				$error = mysql_error($this->conn);
				if ($error == "")
					return TRUE;
				else
					return FALSE;
			}
		}
	}

	//wrapper function for single row return
	public function getRow($query){
		return $this->sqlQuery($query, true, false);
	}

	//wrapper function for multiple row return
	public function getAllRows($query){
		return $this->sqlQuery($query, true, true);
	}

	public function getValue($query){
		$resArr = $this->sqlQuery($query, true, false);
		if(is_array($resArr))
			return array_shift($resArr);
		else
			return false;
	}

	public function recordExists($table, $where){
		$response = $this->sqlQuery("SELECT 1 FROM $table $where", true, false);
		if(is_array($response) && $response[1]==1){
			return true;
		}else{
			return false;
		}
	}
}
?>