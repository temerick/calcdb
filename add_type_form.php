<?
// this form is to add a new type. it will appear in a span in add_prob_form when the "New" type option is selected.

include("connect.php");
include("functions.php");

// grab an array with the keys being all the general types.
$query_output=mysql_query("SELECT general_type FROM directions ORDER BY general_type");
while($result=mysql_fetch_array($query_output)) {
	$gen_types[$result['general_type']]=1;
}

if ($_POST['typename'])
{
	$converted_type = ereg_replace("[^A-Za-z0-9[:space:]_-]", "", $_POST['typename']);  //strip all special characters except dash
	
	if (!$converted_type || print_directions($converted_type)) // if true, the type name already exists or is empty
	{
		echo "That type name already exists or has a problem with it! Use a different one (no special characters allowed).<br><br>";
		unset($_POST['typename']); // so that the form prints again below.
	} else {
		// quotation marks are killing us. replace them with double '.
		$directions=str_replace('"',"''",$_POST['directions']);
		$directions = addslashes($directions);
		$time=time();
		$user=$_COOKIE['username'];
		$sql="INSERT INTO directions (type,general_type,directions,boolean) VALUES (\"$user$time\",\"$converted_type\", \"$directions\", \"0\");";
		if (!mysql_query($sql,$dbhandle)) {
			die('Error: ' . mysql_error());
		} else {
			echo "<script type='text/javascript'>refresh_directions();</script>";
		}
	}
} 

if (!$_POST['typename']): ?>

	<form name="add_type">
	<table width=100%><tr><td>
	<p>General type of problem:<br>
	<select name="type_name">
		<? 
		foreach($gen_types as $key => $value) {
			echo "<option value='$key'>$key</option>";
		}
		?>
	</select>
	</td>
	<td>
	<p>Directions for this type of problem:<br>
	<textarea rows="2" cols="35" name="directions_text"></textarea>
	</td></tr>
	</table>
	</form>
	<p><!-- <input type="button" onclick="javascript:create_type();" value="Create"> -->
<? endif; ?>