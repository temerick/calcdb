<?

$current_date=date("Y-M");
$handle=fopen("cleanup/last_cleanup.txt","r");
$last_cleanup_date=fread($handle,100);
fclose($handle);

if($current_date!=$last_cleanup_date) {
	include("cleanup/cleanup_functions.php");

	remove_old_files();
	remove_old_tags();
	include("backup_db.php");
	$handle=fopen("cleanup/last_cleanup.txt","w");
	fwrite($handle,$current_date);
	fclose($handle);
	
}

?>