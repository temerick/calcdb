<br>
<center><h2>Public Problem Sets</h2>
<table width=75%>
<?

include("connect.php");

$query=mysql_query("SELECT uid,username,saved_carts FROM user_data");

$shared_cart_exists=false;

// Will be the collection of all carts.
$carts=array();


// Cycle through people and add all of their carts.
while($result=mysql_fetch_array($query)) {
	$username=$result['username'];
	$user_carts=explode("%",$result['saved_carts']);
	
	// Append the username onto each cart. This is hacky, and really sucks.
	foreach($user_carts as $key => $value) {
		$user_carts[$key]=$value.";".$username.";".$result['uid'].";".$key;
	}
	
	$carts=array_merge($carts,$user_carts);
}

// Sort the list of carts.
arsort($carts);


//print_r($carts);


// Print the relevant carts.
foreach($carts as $cart) {
	$cart_data=explode(";",$cart);
	if($cart_data[3]=="Yes") {
		echo "<tr><td>".$cart_data[1]."</td><td>Created by ".$cart_data[4]." on ".date("m/d/Y",$cart_data[0])."</td><td><a href='javascript:restore_cart(\"".$cart_data[2]."\")'>Load Problem Set</a></td>";
		if($cart_data[4]==$_COOKIE['username']) {
			$personid=$cart_data[5];
			$cart_number=$cart_data[6];
			echo "<td><span name='cart".$cart_number."_".$cart_data[5]."' id='cart".$cart_number."_".$cart_data[5]."'>";
			include("cart_share.php");
			echo "</span></td>";
		}
		
		echo "</tr>";
		$shared_cart_exists=true;
	}
}


if(!$shared_cart_exists) echo "There are no shared carts at this time. Feel free to make one and share it with the community.";

?>
</table>
