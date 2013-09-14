<?
// currently used as the preview update process for add_prob_form.php

include("connect.php");
include("functions.php");


// if a UID is posted, render that problem
// otherwise, render the "temporary problem" with the given attributes

if (isset($_POST['uid']))
{
	$uid = $_POST['uid']; // get the problem info
	$problem = mysql_fetch_array(mysql_query("SELECT prob,answer FROM problems WHERE uid=$uid"));
	$prob_text = $problem['prob'];
	$sol_text = $problem['answer'];
} else {
	$uid=rawurldecode($_POST['baseuid']);
	$prob_text = rawurldecode($_POST['prob']);
	$sol_text = rawurldecode($_POST['sol']);
}

// begin the table
echo "<table width=100%>";

// start the row
echo "<tr>";

// print the problem
echo "<td width=50%>";
echo build_prob($uid,$prob_text,2); 
echo "</td>";

// print the solution
echo "<td width=50%>";
echo "<span style=\"font-size:x-small;\">Answer</span>"; 
echo "<p  style=\"border: solid lightgray 1pt; padding:5px;\">";
echo build_prob($uid,$sol_text,2,"","a");
echo "</td>";

// finish the row
echo "</tr>";

// finish the table
echo "</table>";

?>
