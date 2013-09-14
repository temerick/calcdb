<?

include("connect.php");
include("functions.php");


// This was built to clear out all of the displaystyles.
$query=mysql_query("SELECT * FROM problems");
while($result=mysql_fetch_array($query)) {
	$uid=$result['uid'];
	if(strpos($result["prob"],"\displaystyle")!==FALSE) {
		echo $result["prob"]." will be replaced with ";
		$prob=addslashes(str_replace("\displaystyle","",$result["prob"]));
		echo "$prob in the database.<br />";
		//mysql_query("UPDATE problems SET `prob`='$prob' WHERE `uid`='$uid'");
	}
	if(strpos($result["answer"],"\displaystyle")!==FALSE) {
		echo $result["answer"]." will be replaced with ";
		$answer=addslashes(str_replace("\displaystyle","",$result["answer"]));
		echo "$answer in the database.<br />";
		//mysql_query("UPDATE problems SET `answer`='$answer' WHERE `uid`='$uid'");
	}
}
?>
