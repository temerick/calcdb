<? // this page should tag a problem and then display the list of tags a problem has.
$uid = $_GET['uid'];
$to_filter=array("'","\"","\\");

$tag_list = str_replace($to_filter,"",strtolower($_GET['tag']));

include("connect.php");

if (!is_numeric($uid) || !trim($tag_list)) {
	// Maybe put an alert here? Currently we just ignore it...
} else {

	// get all the tagids for the problem uid
	$q_tagids = mysql_query("SELECT tagid FROM probtags WHERE probid='$uid'");
	while ($row = mysql_fetch_array($q_tagids)) { $tagids[] = $row{'tagid'}; }

	$tagid_string = "\"".implode("\",\"",$tagids)."\"";

	// get all the tag names for the problem uid
	$q_tagnames = mysql_query("SELECT tag FROM tags WHERE uid IN ($tagid_string)");
	while ($row = mysql_fetch_array($q_tagnames)) { $tagnames[] = $row{'tag'}; }

	$tag_array=explode(",",$tag_list);
	foreach($tag_array as $tag) {
		if(trim($tag)) {
			$tag=trim($tag);
			// check if the tag already exists; if so, die.
			if (in_array($tag, $tagnames))
			{
				echo implode(", ",$tagnames); die;
			}

			// check if the tag exists in the database already
			$q_newtag = mysql_query("SELECT uid FROM tags WHERE tag=\"$tag\"");
			if ($newtag = mysql_fetch_array($q_newtag))
			{	// the tag exists
				$connect_tag = end($newtag);
			}
			else // the tag doesn't exist, create it
			{
				$q_createtag = mysql_query("INSERT INTO tags (tag) VALUE (\"$tag\")");
				$connect_tag = mysql_insert_id();
			}

			// create the tag connection
			$q_createconn = mysql_query("INSERT INTO probtags (probid, tagid) VALUES (\"$uid\",\"$connect_tag\")");
		}
	}
}

// to be sure, get an accurate list of tags from the database. not efficient, but safe
$q_tagids2 = mysql_query("SELECT tagid FROM probtags WHERE probid='$uid'");
while ($row = mysql_fetch_array($q_tagids2)) { $tagids2[] = $row{'tagid'}; }
$tagid_string2 = "\"".implode("\",\"",$tagids2)."\"";
$q_tagnames2 = mysql_query("SELECT tag FROM tags WHERE uid IN ($tagid_string2)");
while ($row = mysql_fetch_array($q_tagnames2)) { $tagnames2[] = $row{'tag'}; }

echo implode(", ", $tagnames2);




?>
