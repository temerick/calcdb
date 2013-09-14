<?

include("connect.php");

if($_GET['restore_cart']!="") {
	// session_unset();
	$uid_list=$_GET['restore_cart'];
	$_SESSION['mycart'] = explode(",",$uid_list);
}
?>


<script src="ajax_magic.js" type="text/javascript"></script>
<center>Your cart has been restored.<br><br><a href="javascript:query_cart()">View your cart</a>


<script type='text/javascript'>
update_cart_count();
</script>