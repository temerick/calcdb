<?
include("../connect.php");
include("../functions.php");



/* 
 *  This function takes in a csv list of uids and returns an array
 *  of the form $array[$type]=csv list of problems associated to that type
 *  which preserves the order of the list. Basically, this function is 
 *  super kick-ass.
 */
function tex_sort($uidlist) {
	if(!$uidlist) {
		return 0;
		exit;
	}
	
	$probs=mysql_query("SELECT uid,type FROM problems WHERE uid IN ($uidlist) ORDER BY FIELD(uid,$uidlist)");
	
	// building an array of arrays. The index is given by the type, and the resulting array should spit out a list of uids associated to that type.
	while($result=mysql_fetch_array($probs)) {
		$interm_array[$result['type']][]=$result['uid'];
	}
	
	// condensing the array to an array of csv's.
	foreach($interm_array as $type => $uids) {
		$array[$type]=implode($uids,",");
	}
	return $array;
}


// This file takes two inputs:
//    - $whichPage is either "problems", "answers", or "hybrid". and corresponds to
// 		the file which needs to be rendered.
//	  - $time is the time corresponding to where you want the files to be put.
// 	  - This also relies on the value of the $_SESSION['mycart']


function build_tex($whichPage,$time) {

	$urlbase="http://people.virginia.edu/~tre8a/calc_db";
	
	
	$ourFileName = "temp/".session_id().$time."/".$whichPage.".tex";
	$handle = fopen($ourFileName, 'w') or die("can't open file");


	fwrite($handle,"
% This file was created by CalcDB. -- $urlbase
%
% To restore this problem set in CalcDB, visit the following URL:
% ");
	
	$uid_list = implode(",",$_SESSION['mycart']);
	
	fwrite($handle,"$urlbase/#restorecart:".$uid_list);
	
	fwrite($handle,"
%
% If you find an error in a problem, please contribute by correcting it in CalcDB. Use the link above, or search for the UID of the problem shown below. Correcting a problem is easy and saves future generations much heartache!
%
%
\documentclass[letterpaper]{article}
\usepackage{amsmath, amsfonts, amsthm, graphicx, amssymb, textcomp, enumerate}
\usepackage[margin=.75in]{geometry}
\everymath=\expandafter{\\the\everymath\displaystyle} %causes all math to be \\displaystyle
\pagestyle{empty}
\begin{document}
\begin{enumerate}"); // Beginning of document

	if(!empty($_SESSION['mycart'])) {
		
		// used to label the parts.
		$part_list=range('a','z');
		$problem_number=1;
		
		$prob_list=tex_sort($uid_list);
		$a_types=implode(array_keys($prob_list),",");
		
		// run through the types
		foreach($prob_list as $type => $list) {
			$directions=mysql_fetch_array(mysql_query("SELECT directions FROM directions WHERE type='$type'"));
			fwrite($handle,"\n\n% Problem number $problem_number \n\item ".$directions['directions']);
			
			// Determine if the problem has multiple parts.
			if(strrpos($list,",")===FALSE) { $multipart=FALSE; } else { $multipart=TRUE; }
			
			// Gather the list of problems associated to this type
			$q_probs=mysql_query("SELECT uid,prob,answer FROM problems WHERE uid IN ($list) ORDER BY FIELD(uid,$list)");
			
			// Single part case
			if(!$multipart) {
				
				$problem=mysql_fetch_array($q_probs);
				fwrite($handle,"\n\t");
				if($whichPage=="problems") { fwrite($handle,build_prob($problem['uid'],$problem['prob'],1)); } else if($whichPage=="answers") { fwrite($handle,build_prob($problem['uid'],$problem['answer'],1,"","a")); } else if($whichPage=="hybrid") { fwrite($handle,build_prob($problem['uid'],$problem['prob'],1)."\n\t\\\\ \\textbf{Answer:} ".build_prob($problem['uid'],$problem['answer'],1,"","a")); }
				
			} else {
				// multi part case
				$part_number=0;
				
				fwrite($handle,"\n\t\\begin{enumerate}");
				
				while($problem=mysql_fetch_array($q_probs)) {
					
					$temp_part_number=$part_number; // I want to keep a temporary counter around for this next step
					
					// determine which part it is.
					
					$part_letter=$part_list[$part_number%26];
					
					while($temp_part_number>25) {
						$temp_part_number=($temp_part_number-($temp_part_number%26))/26-1;
						$part_letter=$part_list[$temp_part_number%26].$part_letter;
					} 
					
					
					fwrite($handle,"\n\n\t% Problem ".$problem_number.$part_letter." - CalcDB UID ".$problem['uid']."\n\t");
					
					$item_letter="";
					if($part_number>25) {
						$item_letter="[($part_letter)]";
					}
					
					if($whichPage=="problems") { 
						fwrite($handle,"\\item".$item_letter." ".build_prob($problem['uid'],$problem['prob'],1)); 
					} else if($whichPage=="answers") { 
						fwrite($handle,"\\item".$item_letter." ".build_prob($problem['uid'],$problem['answer'],1,"","a")); 
					} else if($whichPage=="hybrid") { 
						fwrite($handle,"\\item".$item_letter." ".build_prob($problem['uid'],$problem['prob'],1)."\n\t\\\\ \\textbf{Answer:} ".build_prob($problem['uid'],$problem['answer'],1,"","a")); 
					}
					
					$part_number++;
				}
				
				fwrite($handle,"\n\t\\end{enumerate}\n");
			}
			
			$problem_number++;
			
		}
		
		
	} else {
		fwrite($handle,"\n\n\item Dude, there wasn't anything in your cart.
		                                 __ 
		                       _ ,___,-'\",-=-. 
		           __,-- _ _,-'_)_  (\"\"`'-._\ `. 
		        _,'  __ |,' ,-' __)  ,-     /. | 
		      ,'_,--'   |     -'  _)/         `\ 
		    ,','      ,'       ,-'_,`           : 
		    ,'     ,-'       ,(,-(              : 
		         ,'       ,-' ,    _            ; 
		        /        ,-._/`---'            / 
		       /        (____)(----. )       ,' 
		      /         (      `.__,     /\ /, 
		     :           ;-.___         /__\/| 
		     |         ,'      `--.      -,\ | 
		     :        /            \    .__/ 
		      \      (__            \    |_ 
		       \       ,`-, *       /   _|,\ 
		        \    ,'   `-.     ,'_,-'    \ 
		       (_\,-'    ,'\\\")--,'-'       __\ 
		        \       /  // ,'|      ,--'  `-. 
		         `-.    `-/ \'  |   _,'         `. 
		            `-._ /      `--'/             \ 
		    -DOH!-     ,'           |              \ 
		              /             |               \ 
		           ,-'              |               / 
		          /                 |             -' 
");
	}

	fwrite($handle,"\n\n\end{enumerate}\n\end{document}");
	fclose($handle);
	
}


$whichPage=$_GET['p'];
$time=time();

if(!mkdir("./temp/".session_id().$time)) { // Make a directory to store all of this stuff in.
	echo "unable to create directory. :(";
}

if($whichPage=="zip") { // this will be used for zipping and downloading / emailing.
	
	if(array_key_exists("email",$_POST)) { // Determine integrity of email address.
		include("validEmail.php");
		if(!validEmail($_POST['emailAddress'])) { // Huh. For some reason this hangs when I enter in a crazy domain that doesn't exist. Better than it going through, but it would be nice if it picked up on that. I don't know enough about the script to debug it right now, though.
			
			header( 'Location: http://people.virginia.edu/~tre8a/calc_db/latex.php?p=notvalid' ); // I can't get this to work as a relative path. i.e. I wanted to use ../latex.php?p=notvalid , but that didn't fly for some reason. :(
			// This non-relative path is something it'd be really nice to fix. I'm not entirely sure what's wrong. I was thinking about using the server referrer and just tacking on "?p=notvalid" at the end, but that would break if the user entered in a bad email address more than once. (You'd get "?p=notvalid?p=notvalid", which would error.)
			
		}
	}
	
	
	
	// Grab the UIDs you're looking for
	$list_of_uids=array_values($_SESSION['mycart']);
	
	// Let's hunt for images:
	
	foreach($list_of_uids as $id) {
		$images=array_merge($images,find_images($id,2,"../"));
	}

	
	if(array_key_exists("problems",$_POST)) { // build "problems"
		build_tex("problems",$time); 
	}
	if(array_key_exists("answers",$_POST)) { // build "answers"
		build_tex("answers",$time);
	}
	if(array_key_exists("hybrid",$_POST)) { // build "hybrid"
		build_tex("hybrid",$time);
	}
	// Zip the files
	require ("pclzip.lib.php");
	$zipfile = new PclZip('temp/'.session_id().$time.'/packet.zip');
	
	
	print_r($images);
	
	// add problems
	if(array_key_exists("problems",$_POST)) { 
		
		$v_list = $zipfile->add('temp/'.session_id().$time.'/problems.tex','','temp/'.session_id().$time); 
		if ($v_list == 0) { // error handling
			die ("Error: " . $zipfile->errorInfo(true));
		}	
	}
	
	if(array_key_exists("answers",$_POST)) { // add answers
		
		$v_list = $zipfile->add('temp/'.session_id().$time.'/answers.tex','','temp/'.session_id().$time);
		if ($v_list == 0) {
			die ("Error: " . $zipfile->errorInfo(true));
		}
	}
	
	if(array_key_exists("hybrid",$_POST)) { // add hybrid
		
		$v_list = $zipfile->add('temp/'.session_id().$time.'/hybrid.tex','','temp/'.session_id().$time);
		if ($v_list == 0) {
			die ("Error: " . $zipfile->errorInfo(true));
		}
	}
	
	// Now to add the images:
	$v_list = $zipfile->add($images,'images/','../problem_images/');
	if ($v_list ==0) {
		die ("Error: " . $zipfile->errorInfo(true));
	}
	
	
	
	if(array_key_exists("download",$_POST)) {  // Download.
		header( 'Location: temp/'.session_id().$time.'/packet.zip');
	}
	
	if(array_key_exists("email",$_POST)) { // Email.
		include("email.php");
	}
	
} else { // if p=problems, answers, or hybrid	
	build_tex($whichPage,$time);
	header( 'Location: temp/'.session_id().$time.'/'.$whichPage. '.tex' );
}

?>
