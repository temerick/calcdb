<center>

<h2 style="padding-top:20px">Welcome to CalcDB</h2>

<?
if(!function_exists(tag_list)) {
	include("connect.php");
	include("functions.php");
	
	
	$prob_count = mysql_fetch_array(mysql_query("SELECT COUNT(uid) from problems;"));
	$num_probs = $prob_count["COUNT(uid)"];
	echo "<h4>$num_probs problems in the database</h4>";
}

$username=$_COOKIE['username'];

if($username=="tre8a" || $username=="svd5d") $experimental=true;

$a_tags = optimized_tags();
/* Optimized tags does this already
// Get rid of unwanted tags.
$user_data=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));
$unwanted_tags=explode("%",$user_data['bad_tags']);
foreach($unwanted_tags as $tag) {
	$tag=trim(str_replace("not:","",$tag));
	unset($a_tags[$tag]);
} */
?>

</center>

<table width=100% cellpadding="20px">
	<tr>

	<td width="50%">
		<center><iframe width="560" height="349" src="http://www.youtube.com/embed/eWqLpzkAB9I?wmode=Opaque" frameborder="0" allowfullscreen></iframe></center>
	</td>

	<td valign="top">
<center>
<h3>Popular Tags</h3>
<? print_tag_cloud($a_tags,"tags"); ?></center>
</td>
</tr>
</table>
<a href="javascript:close_everything_but();help_out();" class="help_out">
<div role="button" class="help_out">Help out</div>
</a>



<!--<center><p><font size=+1 color=red>Firefox:</font> Some versions of Firefox render math poorly. Try using <a href="http://www.google.com/chrome">Google Chrome</a> instead.</center>-->
