<script src="add_prob_java.js" type="text/javascript"></script>


<select name="type" id="type_box" onchange="javascript:boxes_changed();javascript:directions_changed();" onkeyup="javascript:boxes_changed();javascript:directions_changed();">
<? 
include("connect.php");
include("functions.php");


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
	$trimtype = trim($row{'type'});
	if (!$trimdirections) $trimdirections="$trimtype (no direction text)";	// if the directions are blank, give a little hint
	echo "<option value=\"$trimtype\">$trimdirections</option>";
}
echo "</optgroup>";
?>
<option value="create_new_type">New...</option>
</select>