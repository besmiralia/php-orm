<?php
mysql_pconnect($TheServer,$TheUser,$ThePassword)
or die(mysql_error());
mysql_select_db($TheDatabase)
or die (mysql_error());
ini_set('date.timezone', 'Europe/Rome');

//die("Serveri eshte i ngarkuar, provojeni me vone.");
function write_log($title,$message)
{
	include_once('cls/cls_log_table.php');
	include_once('cls/cls_usr_users.php');

	$usr = new cls_usr_users();
	$usr = $usr->GetByIdUser($_SESSION["id"]);
	$log = new cls_log_table();
	$log->title=$title;
	$log->message=$message."---skripti:".$_SERVER["PHP_SELF"]."--User:".$usr->Name;
	$log->host = $_SERVER["REMOTE_ADDR"];
	$log->Insert($log);
}
function CONFIG($configName)
{
	//TODO set db_pref
	$res = mysql_query("select dvalue from ip_settings where dname='".$configName."'") or die(mysql_error());
	if($r=mysql_fetch_array($res)){
	return $r["dvalue"];
}
return "";
}
?>