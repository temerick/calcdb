<?
session_start();
$username = "math_prob_db";
$password = "mathmath";
$hostname = "dbm2.itc.virginia.edu"; 
//connection to the database
$dbhandle = mysql_connect($hostname, $username, $password) 
  or die("Unable to connect to MySQL");
//select a database to work with
$selected = mysql_select_db("prob_db",$dbhandle) 
  or die("Could not select prob_db");
?>
