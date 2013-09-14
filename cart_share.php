<?

include("connect.php");

if(isset($_GET['personid'])) {
	$personid=$_GET['personid'];
	$cart_number=$_GET['cart'];
}

// Grab the cart
$user_info=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE uid='$personid'"));
$carts=explode("%",$user_info['saved_carts']);
$cart=explode(";",$carts[$cart_number]);
//echo $cart_number;
//print_r($_SESSION);

// Don't change
if(!isset($_GET['change'])) {
	if($cart[3]=="Yes") {
		$todo="Make Private";
	} else if($cart[3]=="No") {
		$todo="Make Public";
	}
}


// Change the cart
if(isset($_GET['change'])) {
	if($cart[3]=="Yes") {
		$cart[3]="No";
		$todo="Make Public";
	} else if($cart[3]=="No") {
		$cart[3]="Yes";
		$todo="Make Private";
	} else {
		echo "This shouldn't have happened.";
	}
	
	// Put the carts back together
	$carts[$cart_number]=implode(";",$cart);
	$changed_carts=implode("%",$carts);
	mysql_query("UPDATE user_data SET saved_carts='$changed_carts' WHERE uid='$personid'");
}

echo "<a href='javascript:share_cart($cart_number,$personid);yesToNo($personid,$cart_number)'>$todo</a>";
?>
