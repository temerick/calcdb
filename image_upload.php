<html>
<body>
	<center><h2>Upload your image</h2></center>
	<form action="upload_file.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="filename" value="<? echo $_GET['filename']; ?>">
	<table width=100%>
		<tr><td>
			<input type="file" name="file" id="file" /> 
		</td><td>
			<input type="submit" name="submit" value="Submit" />
		</td></tr>
	</table>
	</form>
	<b>Acceptable image formats:</b> .jpeg, .jpg, .gif, .png
</body>
</html>


