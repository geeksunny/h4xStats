<?php
// Reads the GeoIP .csv file and imports it into the database for usage.

// TODO: Implement this into a full setup process.
// TODO: Implement a live-download.

// Initialize the MySQL database handler object
require_once("../classes/class.mysql.php");
$dbh = new dbh();

// Create the database to receive the data.
$insert = "CREATE TABLE `".$dbh->prefix."geoip` (
	`id` INT(10) UNSIGNED NULL AUTO_INCREMENT,
	`begin_ip` VARCHAR(20) NULL,
	`end_ip` VARCHAR(20) NULL,
	`begin_ip_num` INT(11) UNSIGNED NULL,
	`end_ip_num` INT(11) UNSIGNED NULL,
	`country_code` VARCHAR(3) NULL,
	`country_name` VARCHAR(150) NULL,
	PRIMARY KEY (`id`),
	INDEX `ip_str` (`begin_ip`, `end_ip`),
	INDEX `ip_num` (`begin_ip_num`, `end_ip_num`)
)
COMMENT='GeoIP database'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT";
if (!$dbh->sqlQuery($insert,false))
	die("Could not create the database.");
// Get filesize of the GeoIP .csv file.
$fsize = @filesize('GeoIPCountryWhois.csv');
if(!$fsize)
	die("PHP does not have read permission to or the file 'GeoIPCountryWhois.csv' does not exists.");
// Open the file for reading.
$f = @fopen('GeoIPCountryWhois.csv','r');
if(!$f)
	die("PHP does not have read permission to or the file 'GeoIPCountryWhois.csv' does not exists.");
// Read the file into memory.
$str = @fread($f,$fsize);
@fclose($f);
unset($fsize);
// Format the data for use in a MySQL statement.
$str = str_replace("\n",'),(',trim(trim($str,"\n")));
// Insert into the database.
if (!$dbh->sqlQuery("INSERT INTO ".$dbh->prefix."geoip (`begin_ip`,`end_ip`,`begin_ip_num`,`end_ip_num`,`country_code`,`country_name`) values(".$str.")",FALSE))
	die("Could not insert data into database.");
unset($str);

echo "GeoIP database created and populated successfully!";
// Delete the .csv file after we're done with it.
//@unlink('GeoIPCountryWhois.csv');
//echo 'Successfully inserted data. If the file GeoIPCountryWhois.csv is not deleted, please delete them.';
?>