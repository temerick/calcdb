<?
require ("pclzip.lib.php");
$zipfile = new PclZip('temp/zipfile.zip');
$v_list = $zipfile->create('pclzip.lib.php');
if ($v_list == 0) {
die ("Error: " . $zipfile->errorInfo(true));
}
header("Content-type: application/octet-stream");
header("Content-disposition: attachment; filename=temp/zipfile.zip");
readfile("temp/zipfile.zip");
?>