
<center><h3 style="padding-top:20px">Select Course</h3>
<p>Selecting a course here will automatically hide search results from topics you don't need.<br>You can also pick custom filters. Click "none" to never filter results.</p>

<p>Current selection:
<span id="current_selected_course">
<?
include("connect.php");
$username=$_COOKIE['username'];
			$person_prefs=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));
			if(!$person_prefs['current_course']) {
				echo "none";
			} else {
				echo $person_prefs['current_course'];
			}
		
			?>
</span>

<p><a href="javascript:change_current_course('Math 1210')">Math 1210</a> - <a href="javascript:change_current_course('Math 1220')">Math 1220</a> - <a href="javascript:change_current_course('Math 1310')">Math 1310</a> - <a href="javascript:change_current_course('Math 1320')">Math 1320</a> - <a href="javascript:change_current_course('Custom');list_tags();">Custom</a> - <a href="javascript:change_current_course('')">none</a>

</center>









<br><br>


<form id="edit_pw" name="edit_pw">
<?

include("connect.php");


if(array_key_exists('oldpw',$_POST)) {
	
	$oldPW=rawurldecode($_POST['oldpw']);
	$newPW1=rawurldecode($_POST['newpw1']);
	$newPW2=rawurldecode($_POST['newpw2']);
	
	$username=$_COOKIE['username'];
	
	/* grab appropriate user from the database. */
	$user_info=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username=\"$username\""));
	$password=$user_info['password'];
	
	/* check to make sure that the old password is correct. */
	
	if(md5($oldPW)==$password) {
		
		/* check to make sure that the two new passwords match */
		if($newPW1==$newPW2) {
			$md5_pw=md5($newPW1);
			mysql_query("UPDATE user_data SET password=\"$md5_pw\" WHERE username=\"$username\"");
			
			/* reset cookies */
			setcookie("username",$username,time()+60*60*24*30*3);
			setcookie("password",md5($md5_pw),time()+60*60*24*30*3);
			$error_text="Your password has been successfully updated.";
			
		} else {
			$error_text="<font color='red'>The two copies of the new password you entered don't match.</font>";
		}
	} else {
		$error_text="<font color='red'>The old password you entered was incorrect.</font>";
	}
	
}


?>


<center><h3>Change password for <? echo $_COOKIE['username']; ?></h3>
<? echo $error_text; ?>

<table>
	<tr>
		<td>Old Password:</td>
		<td><input type="password" name="oldpw"></td>
	</tr>
	<tr>
		<td>New Password:</td>
		<td><input type="password" name="newpw1"></td>
	</tr>
	<tr>
		<td>Re-Enter New Password:</td>
		<td><input type="password" name="newpw2"></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="button" id="save_pw" value="Save Changes" onclick="javascript:save_new_pw()"></td>
	</tr>
</table>
</form>
</center>
