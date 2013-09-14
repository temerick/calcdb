<center>
<br>
<h2>Welcome to Calc DB.</h2>

<? include("1production.php"); ?>

<ul>
<li class="block_float">Select Course: 
		</li>
		<li class="block_float">
			<span id="list_of_current_course_options_top">
			<span id="current_selected_course">
			<a class="boxed_link" href="javascript:close_everything_but('list_of_current_course_options');visibility_toggle('list_of_current_course_options')">
			<?
			$person_prefs=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));
			if(!$person_prefs['current_course']) {
				echo "none.";
			} else {
				echo $person_prefs['current_course'];
			}
		
			?>
			</a>
			</span>
			</span>
			<div class="courses_pop_down" id="list_of_current_course_options" style="display:none;">
				<ul>
					<li class="block"><a class="boxed_link" href="javascript:change_current_course('Math 1210')">Math 1210</a></li>
					<li class="block"><a class="boxed_link" href="javascript:change_current_course('Math 1220')">Math 1220</a></li>
					<li class="block"><a class="boxed_link" href="javascript:change_current_course('Math 1310')">Math 1310</a></li>
					<li class="block"><a class="boxed_link" href="javascript:change_current_course('Math 1320')">Math 1320</a></li>
					<li class="block"><a class="boxed_link" href="javascript:change_current_course('Custom');list_tags();">Custom</a></li>
					<li class="block"><a class="boxed_link" href="javascript:change_current_course('')">none.</a></li>
				</ul>
			</div>
		</li>
</ul>

<?
if(!function_exists(tag_list)) {
	include("connect.php");
	include("functions.php");
}

$username=$_COOKIE['username'];

if($username=="tre8a" || $username=="svd5d") $experimental=true;

// if(!$experimental) $a_types = type_list(); This was when we were displaying the types. BLEH!

$a_tags = fast_tags();

// Get rid of unwanted tags.
$user_data=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));
$unwanted_tags=explode("%",$user_data['bad_tags']);
foreach($unwanted_tags as $tag) {
	$tag=trim(str_replace("not:","",$tag));
	unset($a_tags[$tag]);
}
?>

<p>&nbsp;
</center>

<table width=100% cellpadding="20px">
	<tr>

	<td width="50%">
		<!--<center><h3>Screencast to go here</h3></center>-->
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



<center><p><font size=+1 color=red>Firefox:</font> Some versions of Firefox seem to render text in a very ugly way. Try using <a href="http://www.google.com/chrome">Google Chrome</a> instead.</center>
