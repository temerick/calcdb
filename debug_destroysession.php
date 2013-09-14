<?php
session_start(); 
session_destroy();


print_r($_SESSION);
?>

<p>If nothing appears above, session is destroyed. Try refreshing.</p>

<p><a href="index.php">Go home</a></p>

