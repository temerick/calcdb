<?php include("connect.php"); ?>

<html>
<body>
<center>
<form action="latex/texIt.php?p=zip" method="post">
<table width=700px>
	<tr>
		<td colspan=2>
		<hr>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<b>Option 1:</b> download individual files.
		</td>
		<td>
			<a href="latex/texIt.php?p=problems">problems</a>, <a href="latex/texIt.php?p=answers">answers</a>, or <a href="latex/texIt.php?p=hybrid">hybrid</a>. (Do not use this option if your sheet has pictures, as you won't be able to download the images separately here.)
		</td>
	</tr>
	<tr>
		<td valign="top">
			<br />
			<b>Option 2:</b> Get selected files in a .zip archive. 
		</td>
		<td valign="top">
			<br />
			<?
			if($_GET['p']=="notvalid") {
				echo "<font color=\"red\">Please enter a valid email address</font>";
			}
			?>
			<br />
			Email address:
			<input type="text" name="emailAddress" size="40" value="<? echo $_COOKIE['username'] ?>@virginia.edu" onfocus="this.value=''; this.onfocus=null;">
			<br />
			<input type="checkbox" name="problems" CHECKED> Problems
			<input type="checkbox" name="answers" CHECKED> Answers
			<input type="checkbox" name="hybrid" CHECKED> Hybrid
			<br />
			<input type="submit" name="email" value="Email me"> <input type="submit" name="download" value="Download .zip">
		</td>
	</tr>
	<tr>
		<td colspan=2>
		<hr>
		</td>
	</tr>
</table>
</form>			
</center>
</body>
</html>
