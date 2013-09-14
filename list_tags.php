<br>
<center><h2>Custom Tag Filter</h2>
<p>Uncheck tags to remove them from search results.</p></center>
<br><br>


<?
include("connect.php");
include("functions.php");

$username=$_COOKIE['username'];
$user_info=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE `username`='$username'")); 
$bad_tags=explode("%",$user_info['bad_tags']);
$list_of_tags=fast_tags();

if($user_info['current_course']!="Custom") { // Make checking/unchecking anything change the course to "Custom".
	$to_custom="change_current_course('Custom');";
}

$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
echo "<span id='error_box'></span>";

echo "<div class='multicol'>";

for($n=0; $n<26;$n++) {
	echo "<div id='$n'>";
	echo "<h2>$alphabet[$n]</h2>";
	foreach($list_of_tags as $tag => $number) {
		if($tag[0]==strtolower($alphabet[$n])) { // See if we're in the right place in the alphabet
			$default="";
			if(!in_array("not:".$tag,$bad_tags)) { // If this is a good tag
				$default="CHECKED";
			}
			echo "<input type='checkbox' onclick=\"javascript:$to_custom toggle_tag_pref('$tag')\" $default><a href=\"javascript:query_tags('$tag')\">$tag</a> ($number)<br />";
			
			unset($list_of_tags[$tag]);
		} else { break; }
	}
	echo "</div>";
}
echo "</div>";
?>
