<h3>Changelog:</h3>
<?

include("../connect.php");

$query=mysql_query("SELECT * FROM directions WHERE `boolean`=1");
$n=0;
while($result=mysql_fetch_array($query)) {
	$type=$result['type'];
	echo "Type: ".$type."<br />";
	$prob_query=mysql_query("SELECT * FROM problems WHERE type=\"$type\"");
	while($prob_result=mysql_fetch_array($prob_query)) {
		$prob=$prob_result['prob'];
		$uid=$prob_result['uid'];
		if(strpos($prob,"[[f]]")===FALSE) {
			$new_prob=addslashes("$[[f]]=".ltrim($prob,'$ '));
			echo "Reformat ".$prob." as ".$new_prob."<br />";
			$sql="UPDATE problems SET prob=\"$new_prob\" WHERE uid=\"$uid\"";
			if (!mysql_query($sql,$dbhandle)) { die('Error: '.mysql_error()); }
			
			$n++;
		}
	}
	echo "<br />";
}

echo $n." problems changed.";

?>