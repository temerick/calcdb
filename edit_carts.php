<script src="ajax_magic.js" type="text/javascript"></script>

<?
// this page exists as a separate html page, loaded in a frame via greybox.
// if we ever decide to load it via an invisible div in index.php, the include and script will be unnecessary and probably create errors.

include("connect.php");
$username=$_COOKIE['username'];


// If I want to open the form
if(array_key_exists("cart",$_GET)): 

	$cart=$_GET['cart']; // just the cart number
	$array=mysql_fetch_array(mysql_query("SELECT saved_carts FROM user_data WHERE username='$username'"));
	$user_array=explode("%",$array['saved_carts']);
	
	$cart_info=explode(";",$user_array[$cart]);
	if($cart_info[3]=="Yes") $checked="CHECKED";
?>
<span id="form">
<form id="edit_name" name="edit_name" method="POST" action="edit_carts.php">
<input type="hidden" name="cart_id" id="cart_id" value=<? echo "'$cart'"; ?>>
<center>
<h1>Cart Info</h1>
<table>
	<tr><td><strong>Name</strong></td><td><strong>Date</strong></td><td><strong>Public</strong></td></tr>
	<tr><td><input type="textbox" name="new_name" id="new_name" <? echo "value=\"".$cart_info[1]."\""; ?>></td><td><? echo date("m/d/Y",$cart_info[0]); ?></td><td><input type="checkbox" name="shared" id="shared" <? echo $checked; ?>></td></tr>
	<tr><td><input type="submit" id="save_cart_name" name="save_cart_name" value="Save Cart"></td><td><input type="submit" id="delete" name="delete" value="Delete Cart"></td></tr>
</table>
</form>
</center>
</span>
<?


// If I want to save a cart name
elseif(array_key_exists("save_cart_name",$_POST)):
	$cart=$_POST['cart_id'];
	
	if($_POST['shared']=="on") $shared="Yes"; else $shared="No";
	
	$array=mysql_fetch_array(mysql_query("SELECT saved_carts FROM user_data WHERE username='$username'"));
	
	// Take it all apart
	$user_array=explode("%",$array['saved_carts']);
	$cart_info=explode(";",$user_array[$cart]);
	
	// Fix some stuff
	$cart_info[1]=str_replace("%","",str_replace(";",",",$_POST['new_name']));
	$cart_info[3]=$shared;
	
	// Put it all back together
	$user_array[$cart]=implode(";",$cart_info);
	$result=implode("%",$user_array);
	
	// update the database
	mysql_query("UPDATE user_data SET saved_carts='$result' WHERE username='$username'");
	echo "Your cart has been successfully updated.";
	
// If I want to delete a cart
elseif(array_key_exists("delete",$_POST)):
	$cart=$_POST['cart_id'];
	$array=mysql_fetch_array(mysql_query("SELECT saved_carts FROM user_data WHERE username='$username'"));
	
	// Take it all apart
	$user_array=explode("%",$array['saved_carts']);
	
	// Remove the undesired entry
	unset($user_array[$cart]);
	
	// Put it all back together
	$result=implode("%",$user_array);
	
	// update the database
	mysql_query("UPDATE user_data SET saved_carts='$result' WHERE username='$username'");
	echo "Your cart has been successfully deleted.";
endif;
?>
