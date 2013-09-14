<? 

// This file should remove files more than two days old. (Something funky happens at the change of the year that it would be nice to fix, as I'm only working with the day of the year here, but that isn't super important right now.)

function rrmdir($str){
    if(is_file($str)){
        return @unlink($str);
    }
    elseif(is_dir($str)){
	
		$dir=opendir($str);
		while($file=readdir($dir)) {
			if($file!="." && $file!="..") {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Trying to remove ".$str."/".$file.".<br />";
				rrmdir($str."/".$file);
			}
		}
        return @rmdir($str);
    }
}


$current_date=date('z',time());

$stuff_to_remove=array();
$age=array();

if ($handle = opendir('../latex/temp')) {

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
		if(rrmdir("../latex/temp/".$file_name)) {
			echo "Successfully removed the file/folder ".$file_name.", which is ".$age[$index]." days old.<br />";
		} else {
			echo "<b><font color='red'>Failed</font></b> to remove the file/folder ".$file_name.", which is ".$age[$index]." days old.<br />";
		}
	}
}

echo "Complete!";

?>