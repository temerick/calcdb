<?



function rrmdir($str,$verbose=false){
    if(is_file($str)){
        return @unlink($str);
    }
    elseif(is_dir($str)){
	
		$dir=opendir($str);
		while($file=readdir($dir)) {
			if($file!="." && $file!="..") {
				if($verbose) echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trying to remove ".$str."/".$file.".<br />";
				rrmdir($str."/".$file);
			}
		}
        return @rmdir($str);
    }
}


/* This file should remove files more than two days old. 
   Set $verbose=true if you wish to see output. */
function remove_old_files($verbose=false) {
	$current_date=date('z',time());

	$stuff_to_remove=array();
	$age=array();

	if ($handle = opendir('latex/temp')) {

	    // This is the correct way to loop over the directory. 
	    while (false !== ($file = readdir($handle))) {
			$file_creation_date=date('z',substr($file,32));
			$how_old=abs(($current_date-$file_creation_date)%365);
			if($how_old>1 && $file!="." && $file!=".." && $how_old<363) {
			
				$stuff_to_remove[]=(string) $file;
				$age[]=$how_old;
			}
	    }
	
	    closedir($handle);
	
		foreach($stuff_to_remove as $index => $file_name) {
			if(rrmdir("latex/temp/".$file_name,$verbose)) {
				if($verbose) echo "Successfully removed the file/folder ".$file_name.", which is ".$age[$index]." days old.<br />";
			} else {
				if($verbose) echo "<b><font color='red'>Failed</font></b> to remove the file/folder ".$file_name.", which is ".$age[$index]." days old.<br />";
			}
		}
	}

	if($verbose) echo "Complete!";
}


/* This file should remove all unused tags and probtags 
   Set $verbose=true if you wish to see output. */
function remove_old_tags($verbose=false) {
	/* cleaning probtags */
	$query=mysql_query("SELECT * FROM probtags");

	while($result=mysql_fetch_array($query)) {
		$problem=mysql_fetch_array(mysql_query("SELECT * FROM problems WHERE uid=$result[probid]"));
		if($problem==false) {
			if($verbose) {
				echo "The probtag ";
				print_r($result);
				echo " has become obsolete has been deleted.<br />";
			}
			mysql_query("DELETE FROM probtags where uid=$result[uid]");
		}
	}

	if($verbose) echo "done with probtag purge.<br />";


	/* cleaning tags */
	$query=mysql_query("SELECT * FROM tags");

	while($result=mysql_fetch_array($query)) {
		$problem=mysql_fetch_array(mysql_query("SELECT * FROM probtags WHERE tagid=$result[uid]"));
		if($problem==false) {
			if($verbose) echo "The tag $result[tag] has become obsolete has been deleted.<br />";
			mysql_query("DELETE FROM tags where uid=$result[uid]");
		}
	}

	if($verbose) echo "done with tag purge.<br />";
}
?>