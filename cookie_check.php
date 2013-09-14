<?
include("connect.php");

if(array_key_exists("username",$_COOKIE) && array_key_exists("password", $_COOKIE)) {
	$username=$_COOKIE['username'];
	$query=mysql_query("SELECT * FROM user_data WHERE username=\"$username\"");
	if($result=mysql_fetch_array($query)) {
		
		/* send them to login if their password is wrong */
		if(md5($result['password'])!=$_COOKIE['password']) {
			header('Location: login.php');
		}
	} else {
		
		/* send them to login if the username cookie doesn't match up with the database */
		header('Location: login.php');
	}
} else if($_COOKIE['username']=="Guest") {
	$username="Guest";
} else {
	/* send them to login if they don't have any cookies. */
	header('Location: login.php');
}
?>