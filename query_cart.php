<?
include("connect.php");
include("functions.php");
$username=$_COOKIE['username'];
$uinfo=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));
$toggle_sols=$uinfo['solution_pref'];
$toggle_tags=$uinfo['tag_pref'];
// get the list of UIDs
$temp_uid_list = $_SESSION['mycart'];

// validate the UIDs (make sure they are numeric)
$uid_list = array();
foreach($temp_uid_list as $uid) { if (is_numeric($uid)) $uid_list[] = $uid; }

// if anything is left, put it back together for the sql query
if (sizeof($uid_list) > 0) { $uid_string = implode(", ",$uid_list); }
else { echo "<br><br><br><br><br><center><h3>You haven't selected any problems yet.<br><br>Use the search box above to search for tags,<br>or <a href='javascript:home()'>go home</a> to see the tag cloud.</h3></center>"; die; }

// query the problems
$q_probs = mysql_query("SELECT uid,prob,answer,type FROM problems WHERE uid IN ($uid_string) ORDER BY FIELD(uid,$uid_string)");

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

// print the header and latex box
echo "<br><center><h2>Your Selected Problems</h2></center>";
echo "<center><h3><a href=\"javascript:switchMenu('latex_options')\">Download LaTeX</a></h3>";
echo "<span id=\"latex_options\" style=\"display:none\">";
include("latex.php");
echo "</span></center>";

// empty cart link
echo "<table width=100% cellpadding=\"10\" cellspacing=\"0\" border=\"0\"><tr><td><a href=\"javascript:empty_cart()\">Remove All Problems</a></td>";


// Print "save cart" option.
echo "<td><form align='right' id='save_cart' name='save_cart'><input type='textbox' name='cart_name' value='Name' style='color:gray;' onfocus=\"this.value=''; this.style.color='black';\"><input type='button' value='Save Problem Set' id='save' onclick='javascript:save_my_cart()'></form></td></tr></table>";


// compute content cell widths
$spaces = 2*(1 + $toggle_sols) + $toggle_tags;
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
		echo "onclick=\"return GB_showCenter('Edit problem', this.href,550,720,function () { query_last();} )\">"; 
		echo "<img border=0 src='img/edit.png'></a>";
		echo "</td>";		
		
		// print the problem
		echo "<td width=$big_space%>";
		echo build_prob($uid,$curprob['prob'],0); 
		echo "</td>";
		
		// if solutions are enabled, print the solution
		if ($toggle_sols)
		{
			echo "<td width=$big_space%>";
			echo "<span style=\"font-size:x-small;\">Answer</span>"; 
			echo "<p  style=\"border: solid lightgray 1pt; padding:5px;\">";
			echo build_prob($uid,$curprob['answer'],0,"f","a");
			echo "</td>";
		}
		
		// if tags are enabled, print the tags
		if ($toggle_tags)
		{
			echo "<td width=$small_space%>";
			echo "<span id=\"tagarea$uid\"><small>Tags:&nbsp;(<a class='xbutton' href=\"javascript:load_tag_box($uid)\">add&nbsp;tag</a>)</small></span><br>";
			echo "<span id=\"taglist$uid\">";
			echo implode(", ",$probs[$type][$uid]['tags'])."";
			echo "</span></td>";
		}
		
		// finish the row
		echo "</tr>";
	}
	
	
}

// end the table of results
echo "</table>";

?>
