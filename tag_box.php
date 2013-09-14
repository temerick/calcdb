<?
$uid = $_GET['uid'];
if(!is_numeric($uid)) {die("non numeric input");}

echo "<small>Add&nbsp;tag:&nbsp;";
echo "<input id=\"text$uid\" size=10 type=\"textbox\" onkeydown=\"if (event.keyCode == 13) javascript:add_tag($uid,document.getElementById('text$uid').value);\" onBlur=\"javascript:addTagRevert($uid)\">&nbsp;&nbsp;(hit enter to add tag)</small>";
?>

