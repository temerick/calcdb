<?
// This will back up the given tables in the database

include("../connect.php");

$table_array[]="directions";
$table_array[]="problems";
$table_array[]="probtags";
$table_array[]="tags";
$table_array[]="user_data";

$date=date('Y-m-d');

mkdir('../backup/database/'.$date);

foreach($table_array as $table_name) {
	
	$backup_file='../backup/database/'.$date.'/'.$date.'-'.$table_name.'.sql';
	
	$query = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name";
	mysql_query($query) or die(mysql_error());
	
}
echo "DONE!";
?>
