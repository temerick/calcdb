<? 

include("connect.php");
$username=$_COOKIE['username'];
$person_prefs=mysql_fetch_array(mysql_query("SELECT * FROM user_data WHERE username='$username'"));
?>
<div class="topbar">
<div class="left">
	<a href="javascript:close_everything_but('');home()"><img src="img/logo.png" alt="Calc|DB" height="26px" style="padding:2px;border-style:none;"></a>
</div>
<div class="left">
	<ul>
		<li class="block_float"><a class="boxed_link" href="<? if($username!="Guest") {  echo "add_prob_form.php"; } else { echo "javascript:alert('Guests do not have access to this feature.')"; }?>" title="Add problem" onclick="javascript:close_everything_but('');
		<? if($username!="Guest") { 
			echo "return GB_showCenter('Add problem', this.href,550,720,function () { query_my_probs();})";
			} ?>"><img src="img/addsmaller.png" border=0> Add Problem</a>
		</li>
		<li class="block_float"><? include("1production.php"); ?><font color=black>Show:</font></li>
		<li class="block_float"><span id="solutions_topbar"></span>Solutions 
		<?
			if($person_prefs['solution_pref']==0) { $value=""; }
			else { $value="checked"; }
			echo "<input type='checkbox' onclick='javascript:toggle_sol_disp_checkbox()' $value>";
		?>
		</li>
		<li class="block_float"><span id="tags_topbar"></span>Tags 
		<?
			if($person_prefs['tag_pref']==0) { $value=""; }
			else { $value="checked"; }
			echo "<input type='checkbox' onclick='javascript:toggle_tag_disp_checkbox()' $value>";
		?>
		</li>

	</ul>
</div>


<div class="right">
	<ul>
		<li class="block_float">
			<? include("search_box.php"); ?>
		</li>
		<li class="block_float">
			<a class="boxed_link" href="javascript:close_everything_but('');query_cart()">Selected Problems (<span id="cartcount"><? include("print_cart_count.php"); ?></span>)</a>
		</li>

		<li class="block_float">
			<a class="boxed_link" href="javascript:close_everything_but('login_pop_down_options');visibility_toggle('login_pop_down_options')"><? echo $_COOKIE['username']; ?></a>
		<div class="login_pop_down" id="login_pop_down_options" style="display:none;">
			<ul>
				<li class="block"><a class="boxed_link" href="javascript:saved_carts();visibility_toggle('login_pop_down_options')">Saved Problem Sets</a></li>
				<li class="block"><a class="boxed_link" href="javascript:shared_carts();visibility_toggle('login_pop_down_options')">Public Problem Sets</a></li>
				<li class="block"><a class="boxed_link" href="javascript:query_my_probs();visibility_toggle('login_pop_down_options')">My Problems</a></li>
				<li class="block"><a class="boxed_link" href="javascript:user_info();visibility_toggle('login_pop_down_options')">Settings</a></li>
				<li class="block"><a class="boxed_link" href="http://people.virginia.edu/~tre8a/calc_db/login.php?do=drop">Logout</a></li>
			</ul>
		</div>
		</li>
	</ul>
</div>
</div>
