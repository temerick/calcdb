<?

include("connect.php");

/**
 * This function will save a cart into the database with the associated name. The entry in the database will be stored
 * as date;cart_name;uid_list;shared
 */

function save_cart($cart_name,$uid_list) {
	$username=$_COOKIE['username'];
	$user_info=mysql_fetch_array(mysql_query("SELECT saved_carts FROM user_data WHERE username='$username'"));
	
	
	if($user_info['saved_carts']=="") {
		$carts=time().";".$cart_name.";".$uid_list.";No";
	} else {
		$carts=$user_info['saved_carts']."%".time().";".$cart_name.";".$uid_list.";No";
	}
	
	mysql_query("UPDATE user_data SET saved_carts='$carts' WHERE username='$username'");
}

if(array_key_exists("cartname",$_POST)) {
	$uids=implode(",",$_SESSION['mycart']);
	
	// This will change all of the semicolons in the cart name into commas. Hopefully that won't mess with people too much.
	$name=rawurldecode($_POST['cartname']);
	save_cart(str_replace("%","",str_replace(";",",",$name)),$uids); 
}
?>

<? // Now to display the carts ?>
<br><center><h2>Your Saved Problem Sets</h2>
<table width=75%><tr><td><b>Name</b></td><td><b>Date</b></td><td><b>Public</b></td><td width=50px></td><td width=50px></td></tr>
	<?
	$a_user=mysql_fetch_array(mysql_query("SELECT uid,saved_carts FROM user_data WHERE username='".$_COOKIE['username']."'"));
	
	$user_array=explode("%",$a_user['saved_carts']);
	
	$n=0; // Run through the carts
	$cart_exists=false;
	while($user_array[$n]!="") {
		$cart_exists=true;
		$cart=explode(";",$user_array[$n]);
		
		// If the cart doesn't specify whether it is shared, it probably isn't.
		if(!array_key_exists(3,$cart)) 
			$cart[3]="No";
		
		
		// If the cart's name is empty
		if($cart[1]=="") 
			$cart[1]="None.";
		
		echo "<tr><td>$cart[1]</td><td>".date("m/d/Y",$cart[0])."</td><td><span id='".$n."_".$a_user['uid']."'>".$cart[3]."</span> ( <span id='cart".$n."_".$a_user['uid']."'>";
		$personid=$a_user['uid'];
		$cart_number=$n;
		include("cart_share.php");
		echo "</span> )</td><td><a href='javascript:restore_cart(\"".$cart[2]."\")'>Restore</a></td><td><a href='edit_carts.php?cart=$n' onclick=\"return GB_showCenter('Editing Your Cart', this.href,200,330,function () { saved_carts();})\">Edit</a></td></tr>";
		
		$n++;
	}
	?>
</table>
<? if(!$cart_exists) echo "<br>You don't have any saved carts. :("; ?>
</center>
