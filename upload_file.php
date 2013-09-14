<?

if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 200000)) {
	
	$filename=$_POST['filename']; // This is the filename I want to assign on the server.
	
	/* Check to see if the file being uploaded currently exists. If it does, delete it. 
	   This is used for image replacement and for general cleanliness */
	if(file_exists("problem_images/".$filename.".jpg")) {
		if(unlink("problem_images/".$filename.".jpg")!=true) {
			echo "Error. Could not remove old image file.";
		} 
	}
	
	
	
	if ($_FILES["file"]["error"] > 0) {
    	echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    } else {
	
		// Let's convert this file.
		
		// Convert from gif.
		if($_FILES["file"]["type"] == "image/gif") {
			imagejpeg(imagecreatefromgif($_FILES["file"]["tmp_name"]),"problem_images/".$filename.".jpg");
			echo "File successfully uploaded to server.";
		} else if($_FILES["file"]["type"] == "image/png") { // Convert from png
			imagejpeg(imagecreatefrompng($_FILES["file"]["tmp_name"]),"problem_images/".$filename.".jpg");
			echo "File successfully uploaded to server.";
		} else { // If the thing is already a jpg
			move_uploaded_file($_FILES["file"]["tmp_name"],"problem_images/".$filename.".jpg");
			echo "File successfully uploaded to server.";
		}
	}
} else if($_FILES["file"]["size"] >= 200000) {
	echo "File size too big. Reduce the size of the file and re-upload.";
} else {
  	echo "Invalid filetype.";
}
?>