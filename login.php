<?

if($_GET['do']=="drop") {
	setcookie("username","",time()-1000);
	setcookie("password","",time()-1000);
}


function genRandomString() {
    $length = 10;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}


include("connect.php");

if($_GET['guest']) {
	setcookie("username","Guest",0);
	header('Location: index.php');
}

/* handle people who already have a login */
if($_POST['usr_type']=="previous") {
	
	$username=trim(addslashes(str_replace("@virginia.edu","",strtolower($_POST['username']))));
	$password=md5($_POST['password']);
	$query=mysql_query("SELECT * FROM user_data WHERE username=\"$username\"");
	
	if($_POST['keep_logged_in']) {
		$time=time()+60*60*24*30*3;
	} else {
		$time=0;
	}
	
	/* determine if the username exists in the database */
	if($result=mysql_fetch_array($query)) {
		
		$password_hash=$result['password'];
		
		/* determine if password matches the one associated to given username */
		if($password_hash==$password) {
			
			/* setting up their cookies. Error if cookies are not enabled. Set to expire in 3 months.
			   Also, cookies will store the md5 hash of the thing stored in the database (so it'll be
			   the md5 hash of the md5 hash of the password). */
			setcookie("username",$username,$time);
			setcookie("password",md5($password_hash),$time);
			header('Location: index.php');
			
			
		} else {
			$error_text="<font color='red'>Password does not match given username</font>";
		}
		
	} else {
		$error_text="<font color='red'>Username does not exist</font>";
	}
}

/* handle people who want to create a new login */
if($_POST['usr_type']=="new") {
	
	$username=trim(addslashes(str_replace("@virginia.edu","",strtolower($_POST['username']))));
	
	/* Determine whether the user is currently in the math department 
	   Note: this is fickle. If the structure of the department homepage changes, this code
	   will need to change as well. If there is something going wrong with logins not being 
	   accepted, the problem is probably here. */
	$is_grad_student=(strpos(file_get_contents("http://artsandsciences.virginia.edu/mathematics/people/graduatestudents/index.html"),"<td> ".$username." </td>")!==false);
	$is_instructor=(strpos(file_get_contents("http://artsandsciences.virginia.edu/mathematics/people/instructors/index.html"),"javascript:sendTo('".$username."');")!==false);
	$is_faculty=(strpos(file_get_contents("http://artsandsciences.virginia.edu/mathematics/people/faculty/index.html"),"javascript:sendTo('".$username."');")!==false);
	$is_visitor=(strpos(file_get_contents("http://artsandsciences.virginia.edu/mathematics/people/visitors/index.html"),"javascript:sendTo('".$username."');")!==false);
	
	// $is_beta_tester=(($username=="cmy3d") || ($username=="klm4tu") || ($username=="jcs4hb") || ($username=="jcj5h") || ($username=="jnh5y") || ($username=="mcz5r") || ($username=="onecooldude"));
	
	if($is_grad_student || $is_instructor || $is_faculty || $is_visitor) {
		
		/* if the account already exists */
		$query=mysql_query("SELECT * FROM user_data WHERE `username`='$username'");
		$random_password=genRandomString();
		$password_hash=md5($random_password);
		
		if($result=mysql_fetch_array($query)) {
			
			/* do the database stuff */
			$id=$result['uid'];
			mysql_query("UPDATE user_data SET password=\"$password_hash\" WHERE uid=\"$id\"");
			
			$message="Your request to reset your CALC DB account has been processed. Your new account information is given below. \n\nUSERNAME: ".$username."\nPASSWORD: ".$random_password."\n\nBe sure that you are accessing the website from a browser with cookies enabled. You may change your password once you are logged into the system.\n\nhttp://people.virginia.edu/~tre8a/calc_db/";
			
		} else { /* if the account is new */
			
			/* do the database stuff */
			mysql_query("INSERT INTO user_data (`username`,`password`) VALUES ('$username','$password_hash')");
			
			$message="Your account with CALC DB has been created. Your username and password are given below. \n\nUSERNAME: ".$username."\nPASSWORD: ".$random_password."\n\nYou may change your password once you are logged into the system.\n\nhttp://people.virginia.edu/~tre8a/calc_db/";
		}
			
		mail( $username."@virginia.edu", "Your login information", $message);
		
		$error_text="Please check your email for login details.";
		
		
	} else {
		$error_text="<font color='red'>CALC DB is currently only available for UVA Mathematics Faculty.</font>";
	}
	
}

?>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	
</head>


<center>
<img src="img/logo.png" style="padding:5px 0">
	
<table height="150px" cellspacing="0px">
<tr><td id="current-user">
	<form action="login.php" method=POST>
	<input type="hidden" name="usr_type" value="previous">
	<table>
		<tr>
		<td valign="center">
		<img src="img/current_users.png" alt="Current Users" height="150px">
		</td>
		<td>
			<table>
			<tr>
				<td><b>Login ID:</b><br /><input type="text" name="username"></td>
			</tr>
			<tr>
				<td><b>Password:</b><br /><input type="password" name="password"></td>
			</tr>
			<tr>
				<td align="right"><input type="checkbox" name="keep_logged_in">Keep me logged in</td>
			<tr>
				<td align="right"><input type="submit" value="sign in"></td>
			</tr>
			</table>
		</td></tr>
	</table>
	</form>
</td>

<td id="new-user">
	<form action="login.php" method=POST>
	<input type="hidden" name="usr_type" value="new">
	<table cellpadding="0px">
		<tr><td valign="center" align="center">
			<img src="img/new_users.png" alt="New Users" height="132px">
		</td>
		<td valign="center">
			<table>
				<tr>
					<td><b>Login ID:</b><br /><input type="text" name="username" style="color:grey;" value="UVA computing ID" onfocus="this.value=''; this.onfocus=null; this.style.color='black';"></td>
				</tr>
				<tr>
					<td align="right"><input type="submit" value="register"></td>
				</tr>
			</table>
		</td></tr>
	</table>
	</form>
</td></tr>
</table>


<br /><? echo $error_text; // Error text ?>
<br /><font size="2">Make sure that cookies are enabled in your browser before use.</font></center>

</body>

</html>
