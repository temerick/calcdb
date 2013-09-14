<?
// DELETES a problem in the database based on POST input. currently called from add_prob_java.js
// returns a success message or an error.

include("connect.php");
include("functions.php");

$update_uid = addslashes($_POST['uid']);

if (is_numeric($update_uid))
{
	purge($update_uid);

	echo "<h3>Problem UID ".$update_uid." deleted from database.</h3><p><input type=button value=\"Close Window\" onclick=\"parent.parent.GB_hide();\">";
		$_SESSION['last_action'] = "delete";
	$_SESSION['last_modified_uid'] = 0;
} else {
	echo "An error occurred... the UID passed was not numeric.";
}

mysql_close($dbhandle);

?>
