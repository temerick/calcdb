<?php
include("connect.php");
include("functions.php");

// empty the cart

unset($_SESSION['mycart']);

include("index_default_content.php");

?>
