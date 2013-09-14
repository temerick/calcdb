<?
include("connect.php");

// If we want to delete a tag.
if(isset($_POST['probuid'])) {
	$uid=$_POST['probuid'];
	mysql_query("DELETE FROM probtags WHERE uid=".$_POST['probtaguid']);
}

// Get the list of tags.
$q_tags = mysql_query("SELECT * FROM probtags WHERE probid=".$uid);
while($a_tags=mysql_fetch_array($q_tags)) {
	$tag=mysql_fetch_array(mysql_query("SELECT * FROM tags WHERE uid=".$a_tags['tagid']));
	$tags[$a_tags['uid']]=$tag['tag'];
}

// build the list of current tags
foreach($tags as $probtag_uid => $tag) { 
	$list[]="<span style='border:solid lightgray 1pt; padding:2px; -moz-border-radius:5px; border-radius:5px;'>$tag&nbsp;<sup>(<a class='xbutton' href=\"javascript:remove_tag($uid,$probtag_uid)\" title=\"Remove tag '$tag' from problem\">x</a>)</sup></span>"; 
}

echo implode(", ",$list).".";


?>