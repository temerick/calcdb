<?
include("connect.php");

$course=$_GET['course'];
if($course=="") $course="none.";

$username=$_COOKIE['username'];


//$presets["Math 1210"]="%not:trig%not:integration by parts%not:lhopital%not:hyperbolic trig%not:trig sub%not:partial fractions%not:long division";
//$presets["Math 1220"]="%not:lhopital%not:hyperbolic trig%not:trig sub%not:partial fractions%not:long division%not:root test%not:limit comparison test";
//$presets["Math 1320"]="%not:level curves";
//$presets["none."]="";

include("course_presets.php");

$bad_tags=$presets[$course];
if($course!="Custom") 
	mysql_query("UPDATE user_data SET `current_course`='$course',`bad_tags`='$bad_tags' WHERE `username`='$username'");
else 
	mysql_query("UPDATE user_data SET `current_course`='$course' WHERE `username`='$username'");
echo $course;

?>
