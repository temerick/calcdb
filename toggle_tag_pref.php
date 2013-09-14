<?
include("connect.php");
$tag=$_GET['tag'];
$username=$_COOKIE['username'];

$user_info=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE `username`='$username'")); 
$bad_tags=explode("%",$user_info['bad_tags']);
$key=array_search("not:".$tag,$bad_tags);
if($key!==false) {
	unset($bad_tags[$key]);
} else {
	$bad_tags[]="not:".$tag;
}
$new_bad=implode("%",$bad_tags);
mysql_query("UPDATE user_data SET `bad_tags`='$new_bad' WHERE `username`='$username'");
echo mysql_error();
?>
