<?

include("../connect.php");


/* cleaning probtags */
$query=mysql_query("SELECT * FROM probtags");

while($result=mysql_fetch_array($query)) {
	$problem=mysql_fetch_array(mysql_query("SELECT * FROM problems WHERE uid=$result[probid]"));
	if($problem==false) {
		echo "The probtag ";
		print_r($result);
		echo " has become obsolete has been deleted.<br />";
		mysql_query("DELETE FROM probtags where uid=$result[uid]");
	}
}

echo "<br />done with probtag purge.<br />";


/* cleaning tags */
$query=mysql_query("SELECT * FROM tags");

while($result=mysql_fetch_array($query)) {
	$problem=mysql_fetch_array(mysql_query("SELECT * FROM probtags WHERE tagid=$result[uid]"));
	if($problem==false) {
		echo "The tag $result[tag] has become obsolete has been deleted.<br />";
		mysql_query("DELETE FROM tags where uid=$result[uid]");
	}
}

echo "<br />done with tag purge.<br />";
?>