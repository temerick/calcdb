<?
// INSERTS a problem in the database based on POST input. currently called from add_prob_java.js
// returns a success message

include("connect.php");

$update_uid = addslashes($_POST['uid']);
$update_type = addslashes($_POST['type']);
$update_prob = trim(addslashes($_POST['prob']));
$typename = trim(addslashes($_POST['typename'])); // General Type.
$directions = trim(addslashes($_POST['directions']));
$update_answer = trim(addslashes($_POST['sol']));
$base_uid = $_POST['baseuid']; // the uid of the problem that was getting edited (or "new")

$username=$_COOKIE['username'];

if($typename!="") { // If this type doesn't exist yet
	$update_type=$_COOKIE['username'].time();
	$query = "INSERT INTO directions (type,directions,general_type) VALUES (\"$update_type\", \"$directions\", \"$typename\")";
	if(!mysql_query($query,$dbhandle)) {
		die('Error: '.mysql_error());
	}
}

$sql="INSERT INTO problems (prob,answer,type,user) VALUES (\"$update_prob\", \"$update_answer\", \"$update_type\",\"$username\");";

if (!mysql_query($sql,$dbhandle)) {
  die('Error: ' . mysql_error());
} else {
	
	$new_id=mysql_insert_id();
	// echo "Base UID: ".$base_uid."<br />";
	// Copy over images. This won't work if they remove images from the middle of a problem,
	// but with our current setup, there isn't anything I can do about that...
	$number_of_images['prob']=substr_count($update_prob,"[[IMAGE]]");
	$number_of_images['answer']=substr_count($update_answer,"[[IMAGE]]");
	
	// Copy problem images
	for($i=0;$i<=$number_of_images['prob'];$i++) {
		if(file_exists("problem_images/".$base_uid."-".$i.".jpg")) {
			echo exec("cp problem_images/".$base_uid."-".$i.".jpg problem_images/".$new_id."-".$i.".jpg");
		}
		if(file_exists("problem_images/".$base_uid."-".$i.".png")) {
			echo exec("cp problem_images/".$base_uid."-".$i.".png problem_images/".$new_id."-".$i.".png");
		}
		if(file_exists("problem_images/".$base_uid."-".$i.".pdf")) {
			echo exec("cp problem_images/".$base_uid."-".$i.".pdf problem_images/".$new_id."-".$i.".pdf");
		}
	}
	
	// Copy answer images
	for($i=0;$i<=$number_of_images['answer'];$i++) {
		if(file_exists("problem_images/a".$base_uid."-".$i.".jpg")) {
			echo exec("cp problem_images/a".$base_uid."-".$i.".jpg problem_images/a".$new_id."-".$i.".jpg");
		}
		if(file_exists("problem_images/a".$base_uid."-".$i.".png")) {
			echo exec("cp problem_images/a".$base_uid."-".$i.".png problem_images/a".$new_id."-".$i.".png");
		}
		if(file_exists("problem_images/a".$base_uid."-".$i.".pdf")) {
			echo exec("cp problem_images/a".$base_uid."-".$i.".pdf problem_images/a".$new_id."-".$i.".pdf");
		}
	}
	
	echo "<h3>Problem created in database as UID ".$new_id.".</h3><p><input type=button value=\"Close Window\" onclick=\"parent.parent.GB_hide();\">";
	$_SESSION['last_action'] = "insert";
	$_SESSION['last_modified_uid'] = $new_id;
	$_SESSION['last_modified_type'] = stripslashes($update_type);
}

mysql_close($dbhandle);

?>
