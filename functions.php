<?

// This function completely removes all trace of a problem from the system.
// The problem is removed from the database, all of it's tags are removed, and 
// all associated images are removed. USE WITH CAUTION!!!!!
function purge($uid) {
	
	// First remove the problem.
	mysql_query("DELETE FROM problems WHERE uid=$uid");
	
	// Then remove all associated tags.
	mysql_query("DELETE FROM probtags WHERE probid=$uid");
	
	// Then remove all associated images.
	$i=0;
		
		// images associated to the problem.
		while(file_exists("problem_images/".$uid."-".$i.".jpg")) {
//			echo "Removing the file problem_images/".$uid."-".$i.".jpg<br />";
			echo exec("rm problem_images/".$uid."-".$i.".jpg");
			$i++;
		}
	
	$i=0;
	
		// images associated to the solution.
		while(file_exists("problem_images/a".$uid."-".$i.".jpg")) {
//			echo "Removing the file problem_images/a".$uid."-".$i.".jpg<br />";
			echo exec("rm problem_images/a".$uid."-".$i.".jpg");
			$i++;
		}

	// if the problem is in the cart, remove it as well
	$uid_in_cart = array_search($uid,$_SESSION['mycart']);
	if ($uid_in_cart) {
		unset($_SESSION['mycart'][$uid_in_cart]);
	}
}


// This block creates an array of problem instruction types
// in the form $a_types["type name"] = # of that type

function type_list($array_of_uids="all")
{
	$a_types = array();
	$q_types = mysql_query("SELECT (type,general_type) FROM directions ORDER BY general_type");
	while ($row = mysql_fetch_array($q_types)) {
		$trimtype = trim($row{'type'});
		if (!array_key_exists($trimtype, $a_types)) {
			$a_types[$trimtype]=0;
		}
	}
	
	if($array_of_uids=="all") {
		$end_of_query="";
	} else {
		$end_of_query=" WHERE ";
		foreach($array_of_uids as $key) { // Creating the query.
			$end_of_query=$end_of_query."uid='".$key."' OR ";
		}
		$end_of_query=substr($end_of_query,0,-3);
	}
	
	
	
	$q_types = mysql_query("SELECT type FROM problems".$end_of_query);
	while ($row = mysql_fetch_array($q_types)) {
		$trimtype = trim($row{'type'});
		$a_types[$trimtype] +=1;
	}
	
	ksort($a_types);
	return $a_types;
}



// This block returns an array of tags
// in the form $a_tags["tag name"] = # of that tag

/* function tag_list()
{
	$a_tags = array();
	$q_tags = mysql_query("SELECT * FROM probtags");

	while ($row = mysql_fetch_array($q_tags)) {
		$specificTag = mysql_fetch_array(mysql_query("SELECT * FROM tags WHERE uid=\"$row[tagid]\""));
		$trimTag=trim($specificTag[tag]);
		if (!array_key_exists($trimTag,$a_tags)) {
			$a_tags[$trimTag]=1;
		} else {
			$a_tags[$trimTag]+=1;
		}
	}
	ksort($a_tags);
	return $a_tags;
} */


// Let's make a better tag list.
// Return is same as above: $a_tags['tag name']= # of that tag. This will still be used, even with optimized_tags below.
function fast_tags()
{
	$a_tags = array();
	$q_tags = mysql_query("SELECT * FROM tags");
	$q_probtags = mysql_query("SELECT tagid, COUNT(probid) FROM probtags GROUP BY tagid");
	
	while ($row = mysql_fetch_array($q_probtags)) {
		$a_tags[$row['tagid']]=$row['COUNT(probid)'];
	}
	
	while ($row = mysql_fetch_array($q_tags)) {
		if(array_key_exists($row['uid'],$a_tags)) {
			$result[$row['tag']]=$a_tags[$row['uid']];
		}
	}
	ksort($result);
	return $result;
	
}

function optimized_tags()
{
	$person_id=$_COOKIE['username'];
	$person=mysql_fetch_array(mysql_query("SELECT `bad_tags` FROM user_data WHERE username='$person_id'"));
	if($person['bad_tags']==="") {
		return fast_tags();
	}
	$bad_tags=substr(str_replace("%not:","', '",$person['bad_tags']),2)."'";
	
	$q_probtags=mysql_query("SELECT tagid, COUNT(probid) FROM probtags WHERE probid NOT IN (SELECT probid FROM probtags WHERE tagid IN (SELECT uid FROM tags WHERE tag IN ($bad_tags))) GROUP BY tagid");
	if(mysql_error()) echo mysql_error();
	
	// Everything below this is from fast_tags	
	$a_tags = array();
	$q_tags = mysql_query("SELECT * FROM tags");
	
	while ($row = mysql_fetch_array($q_probtags)) {
		$a_tags[$row['tagid']]=$row['COUNT(probid)'];
	}

	while ($row = mysql_fetch_array($q_tags)) {
		if(array_key_exists($row['uid'],$a_tags)) {
			$result[$row['tag']]=$a_tags[$row['uid']];
		}
	}
	ksort($result);
	return $result;
}


// This function renders a button to add/remove problem from cart, depending on whether or not the problem is already in the cart.

function sensitive_button($uid)
{

	if (in_array($uid,$_SESSION['mycart']))
		echo "<a href='javascript:add_to_cart(" . $uid. ")'><img border=0 src='img/rem.png'></a>";
	else
		echo "<a href='javascript:add_to_cart(" . $uid. ")'><img border=0 src='img/add.png'></a>";


}



/* This function formats a string associated to a problem with uid=$uid. 
   The output will be for use either on the web ($format=0),
   in the tex source ($format=1) or in the web preview ($format=2). 
   It will add the function name to the random function which is random if not specified.
   The filename_prepend deals with images occuring in a string. When parsing, one may want to
   distinguish between problems and answers. (so we have one image for problems and another
   for answers.) Answers has filename prepend="a" */

function build_prob($uid,$string,$format,$function_name="",$filename_prepend="") {
	

	$to_replace=array();
	$replacements=array();
		
		/* Let's randomize the functions a little bit. Multivariable. */
		if($function_name=="") {
			$rand=rand(0,50);
			
			if($rand<15) {
				$function_name="f";
			} else if($rand<25) {
				$function_name="g";
			} else if($rand<35) {
				$function_name="h";
			} else {
				$rand=rand(0,24);
				$alphabet_minus_x="abcdefghijklmnopqrstuvwz";
				$function_name=$alphabet_minus_x[$rand];
			}
		}
	
		// replacing functions
		$to_replace="{{f}}";
		$replacement=$function_name."(x,y)";
		$string=str_replace($to_replace,$replacement,$string);
		
		/* Let's randomize the functions a little bit. Single Variable. */
		if($function_name=="") {
			$rand=rand(0,50);
			
			if($rand<15) {
				$function_name="f";
			} else if($rand<25) {
				$function_name="g";
			} else if($rand<35) {
				$function_name="h";
			} else {
				$rand=rand(0,24);
				$alphabet_minus_x="abcdefghijklmnopqrstuvwyz";
				$function_name=$alphabet_minus_x[$rand];
			}
		}
		
		// replacing functions
		$to_replace="[[f]]";
		$replacement=$function_name."(x)";
		$string=str_replace($to_replace,$replacement,$string);

	// Web formatting array.
	if($format==0) { 
		
		// Replacing images
		$to_replace="[[IMAGE]]";
		$start_position=strpos($string, $to_replace);
		$i=0;
		while($start_position!==false) {
			$file_path="problem_images/".$filename_prepend.$uid."-".$i;
			if (file_exists($file_path.".jpg")) { // Checking for jpg.  // changed <p> to <br><br> below to fix some formatting issues.
				$replace_with="<br><br><a href=\"".$file_path.".jpg\" onclick=\"return GB_showImage('Problem ".$uid."', this.href)\"><img width=50 border=2 src=\"".$file_path.".jpg\" alt=\"Image not found.\"></a><br><small><a href=\"image_upload.php?filename=".$filename_prepend.$uid."-".$i."\" onclick=\"return GB_showCenter('Upload Image', this.href,150,350,function () { query_last();})\">(change)</a></small><br><br>"; 
			} else {
				$replace_with="<br><br><a href=\"image_upload.php?filename=".$filename_prepend.$uid."-".$i."\" onclick=\"return GB_showCenter('Upload Image', this.href,150,350,function () { query_last();})\">Add Image</a><br><br>";
			}
			$string=substr_replace($string,$replace_with,$start_position,strlen($to_replace));
			$start_position=strpos($string, $to_replace);
			$i++;
		}
		$string=str_replace_every_other("$","$\displaystyle ",$string);

		// Replacing new lines.
		$string=nl2br($string);
		
		// Replacing lists
		unset($to_replace);
		unset($replacements);
		$to_replace=array("\begin{enumerate}","\end{enumerate}","\begin{itemize}","\end{itemize}","\item");
		$replacements=array("<ol>","</ol>","<ul class='web_preview'>","</ul>","<li>");
		$string=str_replace($to_replace,$replacements,$string);
			
	}
	
	// LaTeX formatting array.
	if($format==1) { 
		
		// Replacing images.
		$to_replace="[[IMAGE]]";
		$start_position=strpos($string, $to_replace);
		$i=0;
		while($start_position!==false) { 
			$replace_with="~\begin{center}\includegraphics[width=4in]{images/".$filename_prepend.$uid."-".$i.".jpg}\end{center}";
			$string=substr_replace($string,$replace_with,$start_position,strlen($to_replace));
			$start_position=strpos($string, $to_replace);
			$i++;
		}		
	}
	
	// Web preview formatting array.
	if($format==2) { 
		
		// Replacing images.
		
		$to_replace="[[IMAGE]]";
		$start_position=strpos($string, $to_replace);
		$i=0;
		while($start_position!==false) {
			$file_path="problem_images/".$filename_prepend.$uid."-".$i;
			if (file_exists($file_path.".jpg")) { // Checking for jpg.     // changed <p> to <br><br> below to fix some formatting issues.
				$replace_with="<br><br><img width=50 border=2 src=\"".$file_path.".jpg\" alt=\"Image not found.\"><br><br>"; 
			} else {
				$replace_with="<br><br>[[IMAGE -- can be added later]]<br><br>";
			}
			$string=substr_replace($string,$replace_with,$start_position,strlen($to_replace));
			$start_position=strpos($string, $to_replace);
			$i++;
		}
		$string=str_replace_every_other("$","$\displaystyle ",$string);

		// Replacing new lines.
		$string=nl2br($string);
		
		// Replacing lists
		unset($to_replace);
		unset($replacements);
		$to_replace=array("\begin{enumerate}","\end{enumerate}","\begin{itemize}","\end{itemize}","\item");
		$replacements=array("<ol>","</ol>","<ul class='web_preview'>","</ul>","<li>");
		$string=str_replace($to_replace,$replacements,$string);
	}
	
	// Returning the result.
	return $string;
	
}



function replace_all($string,$to_replace,$replace_with) {
	$start_position=strpos($string, $to_replace);
	$i=0;
	while($start_position!==false) {
		$string=substr_replace($string,$replace_with.$i,$start_position,strlen($to_replace));
		$start_position=strpos($string, $to_replace);
		$i++;
	}
	return $string;
}



function print_tag_cloud($tags,$field) {
        // $tags is the array
      
	if ($field=="tags") 
	{
		$field="";   // tags dont need a modifier
	} else {
		$field=$field.":";
	}

        arsort($tags);
       
        $max_size = 40; // max font size in pixels
        $min_size = 12; // min font size in pixels
       
        // largest and smallest array values
        $max_qty = max(array_values($tags));
        $min_qty = min(array_values($tags));
       
        // find the range of values
        $spread = $max_qty - $min_qty;
        if ($spread == 0) { // we don't want to divide by zero
                $spread = 1;
        }
       
        // set the font-size increment
        $step = ($max_size - $min_size) / ($spread);
       
        // loop through the tag array

		$n=30; // Number of times to loop through the array.
		
        foreach ($tags as $key => $value) {
                // calculate font-size
                // find the $value in excess of $min_qty
                // multiply by the font-size increment ($size)
                // and add the $min_size set above
				if($key!="trig" && $key!="exp" && $key!="log") {
					$size = round($min_size + (($value - $min_qty) * $step));
					echo "<a href=\"javascript:query_tags('$field$key')\" style='font-size: " . $size . "px' title='" . $value . " things tagged with " . $key . "'>" . $key . "</a> ";
				}
				$n--;
				if($n==0) { break; }
        }
}



// This function takes in a .csv formatted string, and returns the appropriate array.
if (!function_exists('str_getcsv')) { 
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') { 
        if (is_string($input) && !empty($input)) { 
            $output = array(); 
            $tmp    = preg_split("/".$eol."/",$input); 
            if (is_array($tmp) && !empty($tmp)) { 
                while (list($line_num, $line) = each($tmp)) { 
                    if (preg_match("/".$escape.$enclosure."/",$line)) { 
                        while ($strlen = strlen($line)) { 
                            $pos_delimiter       = strpos($line,$delimiter); 
                            $pos_enclosure_start = strpos($line,$enclosure); 
                            if ( 
                                is_int($pos_delimiter) && is_int($pos_enclosure_start) 
                                && ($pos_enclosure_start < $pos_delimiter) 
                                ) { 
                                $enclosed_str = substr($line,1); 
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure); 
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end); 
                                $output[$line_num][] = $enclosed_str; 
                                $offset = $pos_enclosure_end+3; 
                            } else { 
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) { 
                                    $output[$line_num][] = substr($line,0); 
                                    $offset = strlen($line); 
                                } else { 
                                    $output[$line_num][] = substr($line,0,$pos_delimiter); 
                                    $offset = ( 
                                                !empty($pos_enclosure_start) 
                                                && ($pos_enclosure_start < $pos_delimiter) 
                                                ) 
                                                ?$pos_enclosure_start 
                                                :$pos_delimiter+1; 
                                } 
                            } 
                            $line = substr($line,$offset); 
                        } 
                    } else { 
                        $line = preg_split("/".$delimiter."/",$line); 
    
                        /* 
                         * Validating against pesky extra line breaks creating false rows. 
                         */ 
                        if (is_array($line) && !empty($line[0])) { 
                            $output[$line_num] = $line; 
                        }  
                    } 
                } 
                return $output; 
            } else { 
                return false; 
            } 
        } else { 
            return false; 
        } 
    } 
}




// This function takes a UID and spits out an array of image file locations 
// which are attached to that uid.
//    - type=0 if you want the images associated to the problem.
//    - type=1 if you want the images associated to the answer.
//    - type=2 if you want both.
// Since this relies on the folder in which the images are located, we need to worry
// about where this thing is being called from. Hence the $path variable. It's a 
// string which gives the relative path back to home. e.g. if the file in which I'm 
// calling "find_images" is located inside of /latex, I'd have "../" as my relative
// path. :(
function find_images($uid,$type=2,$path="") {
	
	$result=array();
	if($type==0 || $type==2) {
		$i=0; // image index.
	
		while(file_exists($path."problem_images/".$uid."-".$i.".jpg")) {
			$result[]=$path."problem_images/".$uid."-".$i.".jpg";
			$i++;
		}
	}
	
	if($type==1 || $type==2) {
		$i=0; // reset index.
	
		while(file_exists($path."problem_images/a".$uid."-".$i.".jpg")) {
			$result[]=$path."problem_images/a".$uid."-".$i.".jpg";
			$i++;
		}
	}
	
	return $result;
}


function print_directions($type)
{
	$prob_instructions = mysql_fetch_array(mysql_query("SELECT directions FROM directions WHERE type=\"". trim($type) . "\""));
	return $prob_instructions['directions'];
}




// sweet. from http://xavisys.com/replace-every-other-occurrence-with-str_replace/
function str_replace_every_other($needle, $replace, $haystack, $replace_first=true) {
    $offset = strpos($haystack, $needle);
    //If we don't replace the first, go ahead and skip it
    if (!$replace_first) {
        $offset += strlen($needle);
        $offset = strpos($haystack, $needle, $offset);
    }
    while ($offset !== false) {
        $haystack = substr_replace($haystack, $replace, $offset, strlen($needle));
        $offset += strlen($replace);
        $offset = strpos($haystack, $needle, $offset);
        if ($offset !== false) {
            $offset += strlen($needle);
            $offset = strpos($haystack, $needle, $offset);
        }
    }
    return $haystack;
}


?>
