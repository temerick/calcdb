<?php
$to      = $_POST['emailAddress']; 
$email   = "math_prob_db-request@groups.mail.virginia.edu"; // This way a reply will go the the admins.
$name    = "admin";
$subject = "Your problem sheet request"; 
$comment = "Thank you for using our system. If you like our system, please contribute to the community.";

$To          = strip_tags($to);
$TextMessage =strip_tags(nl2br($comment),"<br>");
$HTMLMessage =nl2br($comment);
$FromName    =strip_tags($name);
$FromEmail   =strip_tags($email);
$Subject     =strip_tags($subject);

$boundary1   =rand(0,9)."-"
.rand(10000000000,9999999999)."-"
.rand(10000000000,9999999999)."=:"
.rand(10000,99999);
$boundary2   =rand(0,9)."-".rand(10000000000,9999999999)."-"
.rand(10000000000,9999999999)."=:"
.rand(10000,99999);

     
$attach      ='yes';
$end         ='';

$filename='temp/'.session_id().$time.'/packet.zip';

   $handle      =fopen($filename, 'rb'); 
   $f_contents  =fread($handle, filesize($filename)); 
   $attachment[]=chunk_split(base64_encode($f_contents));
   fclose($handle); 

$ftype[]       ='application/zip';
$fname[]       ='packet.zip';


/***************************************************************
 Creating Email: Headers, BODY
 1- HTML Email WIthout Attachment!! <<-------- H T M L ---------
 ***************************************************************/
#---->Headers Part
$Headers     =<<<AKAM
From: $FromName <$FromEmail>
Reply-To: $FromEmail
MIME-Version: 1.0
Content-Type: multipart/alternative;
    boundary="$boundary1"
AKAM;

#---->BODY Part
$Body        =<<<AKAM
MIME-Version: 1.0
Content-Type: multipart/alternative;
    boundary="$boundary1"

This is a multi-part message in MIME format.

--$boundary1
Content-Type: text/plain;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$TextMessage
--$boundary1
Content-Type: text/html;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$HTMLMessage

--$boundary1--
AKAM;

/***************************************************************
 2- HTML Email WIth Multiple Attachment <<----- Attachment ------
 ***************************************************************/
 
if($attach=='yes') {

$attachments='';
$Headers     =<<<AKAM
From: $FromName <$FromEmail>
Reply-To: $FromEmail
MIME-Version: 1.0
Content-Type: multipart/mixed;
    boundary="$boundary1"
AKAM;

for($j=0;$j<count($ftype); $j++){
$attachments.=<<<ATTA
--$boundary1
Content-Type: $ftype[$j];
    name="$fname[$i]"
Content-Transfer-Encoding: base64
Content-Disposition: attachment;
    filename="$fname[$j]"

$attachment[$j]

ATTA;
}

$Body        =<<<AKAM
This is a multi-part message in MIME format.

--$boundary1
Content-Type: multipart/alternative;
    boundary="$boundary2"

--$boundary2
Content-Type: text/plain;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$TextMessage
--$boundary2
Content-Type: text/html;
    charset="windows-1256"
Content-Transfer-Encoding: quoted-printable

$HTMLMessage

--$boundary2--

$attachments
--$boundary1--
AKAM;
}

/***************************************************************
 Sending Email
 ***************************************************************/
$ok=mail($To, $Subject, $Body, $Headers);
if($ok) {
	header('Location: ../index.php?header=texEmail');
} else {
	echo "<center><h1> Mail not sent</h1>Please try again, or contact the system administrators for assistance.</center><br /><a href=\"../index.php\">Go back to the main page</a>";
}
?>