<?
include("connect.php");
$username=$_COOKIE['username'];
$to_toggle=$_GET['q'];
if($to_toggle=="solutions") {
	mysql_query("UPDATE user_data SET solution_pref=(solution_pref+1)%2 WHERE username='$username'");
	echo mysql_error();
} else if($to_toggle=="tags") {
	mysql_query("UPDATE user_data SET tag_pref=(tag_pref+1)%2 WHERE username='$username'");
	echo mysql_error();
} else {
	echo "Something went wrong. ";
}

?>