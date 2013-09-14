<?php
include("connect.php");
include("functions.php");

// this file adds or (depite its name) removes a problem from the cart. it is a simple toggle: if it's not there add it and if it is there remove it. UNLESS if "force" option is set, it will not toggle it out of the cart

// the output of this file is the context sensitive button


// uid should be passed in url
$add_uid = $_GET['uid'];

if(!in_array($add_uid,$_SESSION['mycart'])){		// problem not in cart already
	$_SESSION['mycart'][]=$add_uid; // added to cart
}
elseif (!$_GET['force']) {						// problem already there but we're not forcing add
	unset($_SESSION['mycart'][array_search($add_uid,$_SESSION['mycart'])]); // removed from cart
}

// update the add/remove button
sensitive_button($add_uid);

?>
