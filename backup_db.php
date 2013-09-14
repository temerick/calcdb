<?
// This will back up all tables in the database

include("connect.php");

$date=date('Y-m-d');

$backup_file='backup/database/db-backup-'.$date.'.sql';
	
$command="mysqldump -h $hostname -u $username -p$password prob_db > $backup_file";
system($command);
?>