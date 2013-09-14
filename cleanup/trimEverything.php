<?

include("../connect.php");

$query=mysql_query("SELECT * FROM problems");

while($row=mysql_fetch_array($query)) {
	$uid=$row['uid'];
	$prob=addslashes(trim($row['prob']," "));
	$answer=addslashes(trim($row['answer']," "));
	$type=addslashes(trim($row['type']," "));
	$comment=addslashes(trim($row['comment']," "));
	
	$sql="UPDATE problems SET 
	prob=\"$prob\",
	answer=\"$answer\",
	type=\"$type\",
	comment=\"$comment\"
	WHERE uid=\"$uid\";
	";
	
	if (!mysql_query($sql,$dbhandle))
	  {
	  die('Error: ' . mysql_error());
	  }
}
echo "Trimming completed. <a href=\"../index.php\">Return to the homepage.</a>";

?>