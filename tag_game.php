<?
include("connect.php");
include("functions.php");

$q_random_uids = mysql_query("SELECT uid,type FROM problems ORDER BY RAND() LIMIT 3");
while ($row = mysql_fetch_array($q_random_uids)) {
	$uid_list[]=$row{'uid'};
}

?>

<h3>Tag game!</h3>

<p>Add any tags the following problems might be missing!</p>

<table width=100% cellpadding="10" cellspacing="0" border=0>





<?
// if anything is left, put it back together for the sql query
if (sizeof($uid_list) > 0) { $uid_string = implode(", ",$uid_list); }
else { echo "No UIDs specified!"; die; }

// query the problems
$q_probs = mysql_query("SELECT uid,prob,answer,type FROM problems WHERE uid IN ($uid_string) ORDER BY type, uid");

// get the tagids for every problem in the query
$q_tagids = mysql_query("SELECT probid,tagid FROM probtags WHERE probid IN ($uid_string)");
while ($row = mysql_fetch_array($q_tagids))
{
	$prob_tags[$row{'probid'}][$row{'tagid'}]="";
	$all_tags[$row{'tagid'}] = ""; // this will be filled with tag names later
}

// get the tag names for every tag found above
$tagid_list = "\"".implode("\",\"",array_keys($all_tags))."\"";
$q_tagnames = mysql_query("SELECT uid,tag FROM tags WHERE uid IN ($tagid_list)");
while ($row = mysql_fetch_array($q_tagnames))
{
	$all_tags[$row{'uid'}] = $row{'tag'};
}

// adjoin the tagids in $prob_tags with their real names
foreach($prob_tags as $uid => $tagarray)
{
	foreach($tagarray as $tagid => $blank)
	{
		$prob_tags[$uid][$tagid] = $all_tags[$tagid];
	}
}

// construct the main array of problems
while ($row = mysql_fetch_array($q_probs))
{
	$probs[$row{'type'}][$row{'uid'}]['prob'] = $row{'prob'};
	$probs[$row{'type'}][$row{'uid'}]['answer'] = $row{'answer'};
	$probs[$row{'type'}][$row{'uid'}]['tags'] = $prob_tags[$row{'uid'}]; // array of tagids from above
}

// get the directions for each type
$type_list = "\"".implode("\",\"",array_keys($probs))."\"";
$q_directions = mysql_query("SELECT type,directions FROM directions WHERE type IN ($type_list)");
while ($row = mysql_fetch_array($q_directions)) { $directions[$row{'type'}] = $row{'directions'}; }

// compute content cell widths
$username=$_COOKIE['username'];
$user_info=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));

$spaces = 2*(1 + $user_info['solution_pref']) + 1;
$big_space = 2*round(100/$spaces);
$small_space = round(100/$spaces);

// begin the table to display results
echo "<table width=100% cellpadding=\"10\" cellspacing=\"0\" border=\"0\">";

// loop through each type
foreach ($probs as $type => $type_probs)
{
	// print a blank spacer row
	echo "<tr bgcolor=white><td></td></tr>";
	
	// print the directions row
	echo "<tr bgcolor=lightblue><td colspan=6><b>".$directions[$type]."</b></td></tr>";
	
	// loop through each problem of that type
	foreach ($type_probs as $uid => $curprob)
	{
		// start counting to flip between colors
		$count += 1;
		if ($count % 2) { $rowcolor = "E1FCFC"; } else { $rowcolor = "CCFFFF"; }
		
		// start the row
		echo "<tr bgcolor = \"$rowcolor\">";

		// print a blank spacer cell
		echo "<td width=\"1px\" bgcolor=white>&nbsp;</td>";

		// print add/edit buttons cell
		echo "<td width=\"50px\" align=center>";
		echo "<p><span id=\"button$uid\">";
		sensitive_button($uid);
		echo "</span>";
		echo "<p><a href='add_prob_form.php?uid=$uid' title='Edit problem number $uid' "; 
		echo "onclick=\"return GB_showCenter('Edit problem', this.href,550,700,function () { query_last();} )\">"; 
		echo "<img border=0 src='img/edit.png'></a>";
		echo "</td>";		
		
		// print the problem
		echo "<td width=$big_space%>";
		echo build_prob($uid,$curprob['prob'],0); 
		echo "</td>";
		
		// if solutions are enabled, print the solution
		if ($user_info['solution_pref'])
		{
			echo "<td width=$big_space%>";
			echo "<span style=\"font-size:x-small;\">Answer</span>"; 
			echo "<p  style=\"border: solid lightgray 1pt; padding:5px;\">";
			echo build_prob($uid,$curprob['answer'],0,"f","a");
			echo "</td>";
		}
		
		// print the tags
			echo "<td width=$small_space%>";
			echo "<span id=\"tagarea$uid\"><small>Tags:&nbsp;(<a class='xbutton' href=\"javascript:load_tag_box($uid)\">add&nbsp;tag</a>)</small></span><br>";
			echo "<span id=\"taglist$uid\">";
			echo implode(", ",$probs[$type][$uid]['tags'])."";
			echo "</span></td>";
		
		// finish the row
		echo "</tr>";
	}
	
	
}

// end the table of results
echo "</table>";

?>










</table>

<center><h2><a href="javascript:play_tag_game();">Play Again</a>!</h2></center>
