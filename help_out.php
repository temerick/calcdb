<?
include("connect.php");
include("functions.php");


// find all untagged problems
$q_all_uids = mysql_query("SELECT uid FROM problems");
while ($row = mysql_fetch_array($q_all_uids)) { $all_uids[] = $row{'uid'}; }
$q_all_tagged_uids = mysql_query("SELECT DISTINCT probid FROM probtags");
while ($row = mysql_fetch_array($q_all_tagged_uids)) { $all_tagged_uids[] = $row{'probid'}; }
$untagged_uids = array_diff($all_uids, $all_tagged_uids);
$num_untagged = sizeof($untagged_uids);
$untagged_string = implode(",", $untagged_uids);


// find problems without solution text
$q_no_sols = mysql_query("SELECT uid FROM problems WHERE answer=\"\"");
while ($row = mysql_fetch_array($q_no_sols)) { $no_sols[] = $row{'uid'}; }
$num_no_sols = sizeof($no_sols);
$no_sols_string = implode(",",$no_sols);



echo "<br><center><p><b>We'd love some help maintaining the database! Here are some things you can do to help out:</b></p>";

if ($num_untagged)
	echo "<p>There are <b>$num_untagged</b> problems without tags. Tagging problems allows people to find them easily. <font size=+1><a href=\"javascript:query_uids('$untagged_string')\">Add tags!</a></font></p>";

if ($num_no_sols)
	echo "<p>There are <b>$num_no_sols</b> problems without a solution written up. Solutions are important for creating review sheets. <font size=+1><a href=\"javascript:query_uids('$no_sols_string')\">Do some calculus!</a></font></p>";

echo "<p>Play the <a href=\"javascript:play_tag_game();\">tag game</a>! Three random problems will appear. Add any appropriate tags they might be missing!</p></center>";



?>
