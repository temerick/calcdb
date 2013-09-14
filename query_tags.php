<?
include("connect.php");
include("functions.php");

$personal_info=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='".$_COOKIE['username']."'"));
$tag_disp=$personal_info['tag_pref'];
$soln_disp=$personal_info['solution_pref'];

// make sure something was actually given
if (strlen($_GET['tags']) == 0 || $_GET['tags'] == "undefined") { echo "<br>No tags specified! <a href='javascript:home();'>Go home</a>."; die; }



$full_tag_list=$_GET['tags'].str_replace("%",",",$personal_info['bad_tags']);


// get the list of search operators
$search_orig = explode(",",strtolower($_GET['tags']));
$search_prefs=array();

// sort through the various operators
foreach ($search_orig as $key => $curtag)
{
	$curtag=trim($curtag); // Trim these suckers first.
	if(!in_array($curtag,$search_prefs)) { $search_prefs[]=$curtag; }
}

// build the search clue
foreach ($search_prefs as $value) { 
	$removed_text = implode(",",array_diff($search_prefs,array($value)));
	$search_text[] = "<span style='border:solid lightgray 1pt; padding:2px; line-height:+1.5; -moz-border-radius:5px; border-radius:5px; white-space:nowrap;'>$value&nbsp;<sup>(<a class='xbutton' href=\"javascript:query_tags('$removed_text')\">x</a>)</sup></span>"; 
}



// get the list of search operators
$search = explode(",",strtolower($full_tag_list));
$search_all=array();

// sort through the various operators
foreach ($search as $key => $curtag)
{
	$curtag=trim($curtag); // Trim these suckers first.
	
	if(!in_array($curtag,$search_all)) { $search_all[]=$curtag; }
	
	if (substr_count($curtag,"type:")) {		// restrict to a particular type
		$type_include = trim(str_replace("type:","",$curtag));
		$_SESSION['last_type'] = $type_include; 	// for guessing the next problem type to add
	} elseif (substr_count($curtag,"not:")) {	// remove tags with not: operator
		$temp = trim(str_replace("not:","",$curtag));
		if(!in_array($temp,$tag_exclude)) { // avoid duplicates
			$tag_exclude[] = $temp;
		}
	} else {							// otherwise, search for problems with that tag
		if(!in_array($curtag,$tag_include)) { // avoid duplicates
			$tag_include[] = $curtag;
		}
	}
}


// if a type restriction is set, get all the UIDs for that type.
// in the future, it would be nice if types were marked the same way as tags in the database.
$typeprobids = array();
if ($type_include)
{
	// possible sql injection here
	$q_type = mysql_query("SELECT uid FROM problems WHERE type=\"$type_include\"");
	while ($row = mysql_fetch_array($q_type)) { $typeprobids[] = $row{'uid'}; }
}

// get an array of all tags we're interested in 
$all_tags = array_merge($tag_include, $tag_exclude);

// if anything is there, put it together for mysql
if (sizeof($all_tags) > 0) 
{ 
	$all_tags_string = "\"".implode("\",\"",$all_tags)."\""; 

	// query for the tagids for all interesting tags -- possible sql injection here.
	$q_tagids = mysql_query("SELECT uid,tag FROM tags WHERE tag IN ($all_tags_string)");
	while ($row = mysql_fetch_array($q_tagids)) { $tagids[$row{'tag'}] = $row{'uid'}; }

	// for each tag, get the UIDs associated to it
	foreach($all_tags as $value)
	{
		$q_uids = mysql_query("SELECT probid FROM probtags WHERE tagid=".$tagids[$value]);
		while ($row = mysql_fetch_array($q_uids)) { $tagprobids[$value][] = $row{'probid'}; }
	}
}
elseif ($type_include) {
	// do nothing, but don't die in the next case. this could be nicer.
}
else { 
	echo "<br>No search criteria were specified! <a href='javascript:home();'>Go home</a>."; die; 
}

// intersect all UIDs in include category
foreach ($tag_include as $tag)
{
	$include_uids_by_tag[$tag] = $tagprobids[$tag]; // pick out all the uids by tag to include
}
if (sizeof($include_uids_by_tag) > 1) 
{
	$include_uids = call_user_func_array('array_intersect', $include_uids_by_tag);
} else {
	$include_uids = end($include_uids_by_tag);
}


// if a type was specified, subtract everything that wasn't that type
if ($type_include && sizeof($tag_include) > 0) // type and include tag specified
{
	$include_uids = array_intersect($typeprobids, $include_uids);
} 
elseif ($type_include) // type but no include tags specified
{
	$include_uids = $typeprobids;
}
elseif (sizeof($tag_exclude) > 0 && sizeof($tag_include) == 0) // no type and no include tags... only not: operators specified.
{
	echo "<br>Searching with only \"not:\" operators is not supported. <a href='javascript:home();'>Go home</a>."; die;
}
else {

}

// merge all UIDs in exclude category
$exclude_uids = array();
foreach ($tag_exclude as $tag)
{
	$exclude_uids = array_merge($exclude_uids, $tagprobids[$tag]);
}

// remove excluded UIDs from included UIDs
$uid_list = array_diff($include_uids, $exclude_uids);


// building the search clue came from here... in case something goes wrong with what I'm about to do...



// print the search clue
$search_clue = implode(", ",$search_text);
echo "<div id=\"current_search\" style=\"border: solid lightgray 1pt; line-height:140%; margin-bottom:5px; overflow:hidden;\"><div style='padding-left:5px;'><b>Your search</b> <small>(Click <small>(<font color='grey'>x</font>)</small> to remove a critereon.)</small></div><div style='padding:5px;'>$search_clue. </div></div>";


// if anything is left, put it back together for the sql query
if (sizeof($uid_list) > 0) { $uid_string = implode(", ",$uid_list); }
else { echo "Nothing matched your search query. Try removing a criterion above."; die; }


// get the tagids for every problem in the query -- THIS SHOULD BE REMOVED AND EARLIER RESULT USED.
unset($all_tags);
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

// query the problems
$q_probs = mysql_query("SELECT uid,prob,answer,type FROM problems WHERE uid IN ($uid_string) ORDER BY type, uid");

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

// build and print the related tags field
sort($all_tags);
foreach ($all_tags as $value)
{
	if (!in_array($value, $search_all)) 
	{ 
		$added_text = implode(",",array_merge($search_orig,array($value))); // *****
		$notted_text = implode(",",array_merge(array_diff($search_orig,array($value)),array("not:$value")));
		$related_text[] = "<span style='border:solid lightgray 1pt; padding:2px; line-height:+1.5; -moz-border-radius:5px; border-radius:5px; white-space:nowrap;'><a class='tag' href=\"javascript:query_tags('$added_text')\">$value</a>&nbsp;<sup>(<a class='xbutton' href=\"javascript:query_tags('$notted_text')\" title='Remove $value'>x</a>)</sup></span>"; 
	}
}
if (sizeof($related_text) > 0)
{
	$related_clue = implode(", ", $related_text);
	echo "<div id=\"related_search\" style='border:solid lightgray 1pt; height:60px; line-height:140%; margin-bottom:5px; overflow:hidden;'><div style='padding-left:5px;'><b>Narrow your search</b> to a tag by clicking its name, or exclude that tag by clicking <small>(<font color=grey>x</font>)</small>.</div><table width=100% style='border-spacing:0px'><tr><td style='padding:5px;'>$related_clue.</td><td id='related_search_more_td' width=50px valign='top' style='padding-top:15px; padding-right:0px; padding-bottom:0px;'><div id=\"related_search_more\" style='background-color:lightgray; float:right; padding:2px; border-top-left-radius: 4px; -moz-border-top-left-radius: 4px;'><a href='javascript:related_more_toggle();' style='color: black; text-decoration: none;'>More</a></div></td></tr></table></div>";
}

// add all to cart link
echo "<a href=\"javascript:add_to_cart_bulk($uid_string);\">Add All Displayed Problems</a>";

// compute content cell widths
$spaces = 2*(1 + $soln_disp) + $tag_disp;
$big_space = 2*round(100/$spaces);
$small_space = round(100/$spaces);

// begin the table to display results
echo "<table width=100% cellpadding=\"10\" cellspacing=\"0\" border=\"0\">";
$n=1;

// loop through each type
foreach ($probs as $type => $type_probs)
{
	// print a blank spacer row
	echo "<tr bgcolor=white><td></td></tr>";
	$n++;
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
		echo "<p><a href='";
		if($_COOKIE['username']!="Guest") {
			echo "add_prob_form.php?uid=$uid";
		} else {
			echo "javascript:alert(\"Guests do not have access to this feature.\")";
		}
		echo "' title='Edit problem number $uid'";
		if($_COOKIE['username']!="Guest") {
			echo " onclick=\"return GB_showCenter('Edit problem', this.href,550,720,function () { query_last();} )\""; 
		}
		echo "><img border=0 src='img/edit.png'></a>";
		echo "</td>";
		
		// print the problem
		echo "<td width=$big_space%>";
		echo build_prob($uid,$curprob['prob'],0); 
		echo "</td>";
		
		// if solutions are enabled, print the solution
		if ($soln_disp)
		{
			echo "<td width=$big_space%>";
			echo "<span style=\"font-size:x-small;\">Answer</span>"; 
			echo "<p  style=\"border: solid lightgray 1pt; padding:5px;\">";
			echo build_prob($uid,$curprob['answer'],0,"f","a");
			echo "</td>";
		}
		
		// if tags are enabled, print the tags
		if ($tag_disp)
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



echo "<br><br>$count results found.";





?>
