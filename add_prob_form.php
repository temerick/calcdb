<? // <SCRIPT SRC="MathJax/MathJax.js"> ?>
<!--<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
   MathJax.Hub.Config({
      extensions: ["tex2jax.js"],
      jax: ["input/Tex","output/HTML-CSS"],
      tex2jax: {inlineMath: [["$","$"],["\\(","\\)"]]}
   });
</script>-->
<script type="text/x-mathjax-config">
        MathJax.Hub.Config({"HTML-CSS": { preferredFont: "TeX", availableFonts: ["STIX","TeX"] },
                         tex2jax: { inlineMath: [ ["$", "$"], ["\\\\(","\\\\)"] ], displayMath: [ ["$$","$$"], ["\\[", "\\]"] ], processEscapes: true, ignoreClass: "tex2jax_ignore|dno" },
                         TeX: { noUndefined: { attributes: { mathcolor: "red", mathbackground: "#FFEEEE", mathsize: "90%" } } },
                         messageStyle: "none"
        });
    </script>    
    <script type="text/javascript" src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>

<script src="ajax_magic.js" type="text/javascript"></script>
<script src="add_prob_java.js" type="text/javascript"></script>
<script src="toggle.js" type="text/javascript"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />

<?

// this page exists as a separate html page, loaded in a frame via greybox.
// if we ever decide to load it via an invisible div in index.php, all of these includes and scripts will be unnecessary and probably create errors.

include("connect.php");
include("functions.php");

unset($_SESSION['last_action']);
unset($_SESSION['last_modified_uid']);
unset($_SESSION['last_modified_type']);

if (isset($_GET["uid"]))
{
	$edit_uid = $_GET["uid"];
} 
else {
	$edit_uid = "new";
}


if ($edit_uid != "new")
{
	// Get the problem.
	$q_prob = mysql_query("SELECT * FROM problems WHERE uid=" . $edit_uid);
	$a_prob = mysql_fetch_array($q_prob);
	$add_prob = $a_prob['prob'];
	$add_ans = $a_prob['answer'];
	$add_type = $a_prob['type'];
}

?>



<table width=100%>
<tr>
<td><font size=+1><b>Problem Preview</b></font> <small><? if($edit_uid!="new") echo "(Editing problem - UID ".$edit_uid.")"; else echo "(Creating new problem)"; ?></small></td></tr>
<? if($edit_uid!="new") { echo "<tr><td><b>Current Tags:</b><span name='tag_list' id='tag_list'>"; $uid=$edit_uid; include("add_prob_tag_box.php"); echo "</span></td></tr>"; } ?>
<tr>
	<td colspan=2>
	<hr>

	<form name="add_type_select">
	<p>Directions: 
	<span id="directions_box">
	<select name="type" id="type_box" onchange="javascript:boxes_changed();javascript:directions_changed();" onkeyup="javascript:boxes_changed();javascript:directions_changed();">
	<? 
	
	
	
	// attempt to guess what directions should be selected by default
	if (isset($add_type)) // choice is obvious if we're editing
	{ $desired_type = $add_type;
	} elseif(isset($_SESSION['last_modified_type'])) {  // if a problem was previously created or modified, go with that type
		$desired_type = $_SESSION['last_modified_type'];
	} elseif(isset($_SESSION['last_type'])) { // last chance: was a type search just done?
		//$last_tags = explode(",",$_SESSION['last_value']);
		//foreach($last_tags as $value) {
		//	if (substr_count($value, "type:") > 0)
		//	{
				$desired_type = $_SESSION['last_type'];
		//	}
		//}
	}

	// construct an array of directions (the actual directions!) and print them.
	
	$q_types = mysql_query("SELECT type, directions, general_type FROM directions ORDER BY general_type");
	
	$first_time=true;
	
	while ($row = mysql_fetch_array($q_types)) {
		
		if($row['general_type']!=$general_type) {
			
			if(!$first_time) {
				echo "</optgroup>";
				$first_time=false;
			}
			
			$general_type=$row['general_type'];
			echo "<optgroup label='$general_type'>";
		}
		
		$trimdirections = trim($row{'directions'});
		
		// If the directions are too long, shorten them. (For display in the box only, of course...)
		if(strlen($trimdirections)>100) {
			$trimdirections=substr($trimdirections,0,100)."...";
		}
		
		$trimtype = trim($row{'type'});
		if (!$trimdirections) $trimdirections="(no direction text)";	// if the directions are blank, give a little hint
		//if (!in_array($trimtype, $a_types)) {
			//$a_types[$trimdirections]=$trimtype;
			if ($trimtype == $desired_type) { $sel = "selected"; } else { $sel = ""; }		// determining which to select by default
			echo "<option $sel value=\"$trimtype\">$trimdirections</option>";
		//}
	}
	echo "</optgroup>";
	?>
	<option value="create_new_type">New...</option>
	</select>
	</span>
	</form>
	<span id="new_directions"></span>
	</p>

	</td>
	<td>
	<span id="new_type_span"></span>
	</td>
	</tr>
<tr>
<td>

<span id="prob_preview">
<?
if ($edit_uid != "new")
{
	echo "<script>window.onload=add_form_preview_init($edit_uid);</script>";
}
else
{
	echo "Type the LaTeX code for your problem in the boxes below, then click Preview.<br><br>";
}

?>
</span>

<hr>

</td>
</tr>
<tr>
<td>
<span id="add_boxes">
	<form id="add_prob" name="add_prob">
	<table>
	<tr>
	<td>
	<p>Problem:<br><textarea rows="5" cols="35" id="prob_box" name="prob" onchange="javascript:boxes_changed()" onkeyup="javascript:boxes_changed()"><? echo $add_prob; ?></textarea></p>
	</td>
	<td>
	<p>Answer:<br><textarea rows="5" cols="35" id="answer_box" name="answer" onchange="javascript:boxes_changed()" onkeyup="javascript:boxes_changed()"><? echo $add_ans; ?></textarea></p>
	</td>
	</tr>
	<tr>
	<td colspan=2>
	<small><a href="javascript:switchMenu('p_format_notes')">Show formatting notes</a></small>
	</td>
	</tr>
	</table>
	</form>
</span>
</td>
</tr>
<tr>
<td>
<hr>
<center>
<span id="add_controls">

<? if($edit_uid == "new") { $save_button = "hidden"; } else { $save_button = "button"; } ?>

<input type="button" id="add_preview" onclick="javascript:add_form_preview('<? echo $edit_uid; ?>')" value="Preview" DISABLED>

<input type="<? echo $save_button; ?>" id="add_revert" onclick="javascript:add_form_revert('<? echo $edit_uid; ?>')" value="Revert" DISABLED>

<? // disabling this functionality for now. perhaps we'll have it never come back. ?>
<input type="hidden" id="add_copy" onclick="javascript:add_form_copy_to_cart()" value="Copy to Cart" DISABLED>


<input type="<? echo $save_button; ?>" id="add_save" onclick="javascript:add_form_save_to_db('<? echo $edit_uid; ?>')" value="Save to DB" DISABLED>

<input type=button id="add_add" onclick="javascript:add_form_add_to_db('<? echo $edit_uid; ?>')" value="Add to DB" DISABLED>
<?
if($a_prob['user']==$_COOKIE['username'] || $_COOKIE['username']=="tre8a" || $_COOKIE['username']=="svd5d"): ?>
<input type="<? echo $save_button; ?>" id="add_delete" onclick="javascript:add_form_delete_from_db('<? echo $edit_uid; ?>')" value="Delete from DB">
<? endif; ?>
<input type=button value="Cancel" onclick="parent.parent.GB_hide();">

<p><small><a href="javascript:switchMenu('p_explain')">Explain these buttons</a></small></p>

</span>
</center>
<hr>
</td>
</tr>
<tr>
<td>
<span  id="p_format_notes" style="display:none">
<p  class="tex2jax_ignore">Formatting instructions: 
<ul>
<li class="tex2jax_ignore">Use full LaTeX markup, using $'s as appropriate. Do not use $$ -- use \[ and \] instead. Do not use \displaystyle.</li>
<li class="tex2jax_ignore">Type [[f]] for a random function name.</li>
<li class="tex2jax_ignore">Type [[IMAGE]] to include an image at that location. Images can only be uploaded once the problem has been saved to the database. Use the (Add Image) links.</li>
</ul>
<br /></p>
</span>

<span  id="p_explain" style="display:none">
<p>Button explanation:
<ul>
<li>You must <b>preview</b> your changes before any other action is taken!</li>
<? if ($edit_uid != "new") echo "<li><b>Revert</b> returns the problem to its original state.</li>"; ?>
<? //<li><b>Copy to Cart</b> puts this problem in your cart, but does not alter the database at all. If you close your browser window, the problem will be lost.</li> ?>
<? if ($edit_uid != "new") echo "<li><b>Save to DB</b> alters the problem in the database for everyone to see. Use this if you found an error in the problem.</li>"; ?>
<li><b>Add to DB</b> adds the problem to the database as a new problem<? if ($edit_uid != "new") echo ", leaving the original problem unaltered. Use this to add new problems using an existing problem as a template"?>.</li>
<? if ($edit_uid != "new") echo "<li><b>Delete from DB</b> deletes the problem from the database. This cannot be undone."; ?></li>
<li><b>Cancel</b> closes this window without changing anything.</li>
</ul>
</span>
</td>
</tr>
</table>
