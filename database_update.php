<?
// UPDATES a problem in the database based on POST input. currently called from add_prob_java.js
// returns a success message

include("connect.php");

$update_uid = addslashes($_POST['uid']);
$update_type = addslashes($_POST['type']);
$update_prob = addslashes($_POST['prob']);
$update_answer = addslashes($_POST['sol']);

$sql="UPDATE problems SET prob=\"$update_prob\", answer=\"$update_answer\", type=\"$update_type\" WHERE uid=\"$update_uid\";";

if (!mysql_query($sql,$dbhandle)) {
  die('Error: ' . mysql_error());
} else {
	echo "<h3>Problem UID $update_uid updated in database.</h3><p><input type=button value=\"Close Window\" onclick=\"parent.parent.GB_hide();\">";
	$_SESSION['last_action'] = "update";
	$_SESSION['last_modified_uid'] = $update_uid;
}

mysql_close($dbhandle);

?>
