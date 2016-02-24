<?php
require("start.php");
require("config.php");
require("db.php");
require("dbORM.class.php");
$db=$TheDatabase;


if(isset($_GET['db']))
{
	$db=$_GET['db'];
	mysql_select_db($db) or die("Nuk mund te lidhet me ".$db);
}


function beautify($strin)
{
	return strtolower($strin);
	//return str_replace(" ","",ucwords(str_replace("_"," ",$strin)));
}

?>
<html>
<head>
<style>

a,a:visited,a:active{text-decoration:none;color:blue;}
.strong{font-weight:bold;color:#FF0000;font-size:1.3em;}
</style>
</head>

<body>
<table><tr><td><ul>
<?php
$res =mysql_query("select SCHEMA_NAME from information_schema.SCHEMATA");
$class='';
while($r=mysql_fetch_array($res)){
if($db==$r["SCHEMA_NAME"]) $class='strong'; else $class='';
?>

<li><a href='?db=<?php echo $r['SCHEMA_NAME']."' class='".$class;?>'><?php echo $r['SCHEMA_NAME'];?></a></li>
<?php }?>
</ul>
</td><td>
<form method='post' action=''>
<select name='tables[]' multiple='multiple' size='20'>
<?php
$res=mysql_query("select * from information_schema.TABLES T where `TABLE_SCHEMA`='".$db."' AND `TABLE_TYPE`='BASE TABLE'") or die(mysql_error());
while($r=mysql_fetch_array($res))
{
echo "<option value='".$r["TABLE_NAME"]."' selected >".$r["TABLE_NAME"]."</option>";
}
?>
</select>
<input type='submit' value='Generate' name='submit'>
</form>
</td></tr>
<tr><td colspan='2'>
<?php
if($_POST){

/*
 Instantiate the class, passing in the name of the class you want to generate
 along with the name of the table you are creating the class for
*/
	

	foreach($_POST["tables"] as $k=>$v){
	
	echo "=======================================================================<br/>";
	$class = new dbORM($v,$v);
	//write columns
	$ress=mysql_query("SELECT * FROM information_schema.COLUMNS where TABLE_SCHEMA='".$db."' AND TABLE_NAME='".$v."'") or die(mysql_error());
	while($rr=mysql_fetch_array($ress))
	{
		$class->addDataMember($rr["COLUMN_NAME"],beautify($rr["COLUMN_NAME"]),$rr["COLUMN_TYPE"]);
		echo "<br/>Added COLUMN: ".$rr["COLUMN_NAME"];//."-".beautify($rr["COLUMN_NAME"])."-type:".$rr["COLUMN_TYPE"]."-Pri:".$rr["COLUMN_KEY"]="PRI";
	}
	echo "<br/>";
	
	//write primary key
	$ress=mysql_query("SELECT COLUMN_NAME FROM information_schema.`KEY_COLUMN_USAGE` K INNER JOIN information_schema.TABLE_CONSTRAINTS T ON K.CONSTRAINT_NAME=T.CONSTRAINT_NAME AND K.TABLE_NAME=T.TABLE_NAME AND K.TABLE_SCHEMA=T.TABLE_SCHEMA	where K.TABLE_SCHEMA='".$db."' AND K.TABLE_NAME='".$v."' AND CONSTRAINT_TYPE='PRIMARY KEY' order by K.ORDINAL_POSITION") or die(mysql_error());
	while($rr=mysql_fetch_array($ress))
	{
		$class->addKeyMember($rr["COLUMN_NAME"],beautify($rr["COLUMN_NAME"]),'PRIMARY KEY');
		echo "<br/>Added PRIMARY KEY: ".$rr["COLUMN_NAME"];//."-".beautify($rr["COLUMN_NAME"])."-type:".$rr["COLUMN_TYPE"]."-Pri:".$rr["COLUMN_KEY"]="PRI";
	}
	echo "<br/>";
	
	//write UNIQUE KEY
	$ress=mysql_query("SELECT COLUMN_NAME FROM information_schema.`KEY_COLUMN_USAGE` K INNER JOIN information_schema.TABLE_CONSTRAINTS T ON K.CONSTRAINT_NAME=T.CONSTRAINT_NAME AND K.TABLE_NAME=T.TABLE_NAME AND K.TABLE_SCHEMA=T.TABLE_SCHEMA where K.TABLE_SCHEMA='".$db."' AND K.TABLE_NAME='".$v."' AND CONSTRAINT_TYPE='UNIQUE' order by K.ORDINAL_POSITION") or die(mysql_error());
	while($rr=mysql_fetch_array($ress))
	{
		/*
		  Add in the unique key field for this table, the fields are
		  $fieldName 		- the name of the DB Field
		  $displayName 	- what you want the name to be in your class
		  $dataType 		- Optional, sets the type in the PHPDoc comments for you
		  $keyField		- Optional, set to true on your primary key field
		*/

		$class->addKeyMember($rr["COLUMN_NAME"],beautify($rr["COLUMN_NAME"]),'UNIQUE');
		echo "<br/>Added UNIQUE KEY: ".$rr["COLUMN_NAME"];//."-".beautify($rr["COLUMN_NAME"])."-type:".$rr["COLUMN_TYPE"]."-Pri:".$rr["COLUMN_KEY"]="PRI";
	}
	echo "<br/>";
	//write foreign KEY
	$ress=mysql_query("SELECT COLUMN_NAME FROM information_schema.`KEY_COLUMN_USAGE` K INNER JOIN information_schema.TABLE_CONSTRAINTS T ON K.CONSTRAINT_NAME=T.CONSTRAINT_NAME AND K.TABLE_NAME=T.TABLE_NAME AND K.TABLE_SCHEMA=T.TABLE_SCHEMA where K.TABLE_SCHEMA='".$db."' AND K.TABLE_NAME='".$v."' AND CONSTRAINT_TYPE='FOREIGN KEY' order by K.ORDINAL_POSITION") or die(mysql_error());
	while($rr=mysql_fetch_array($ress))
	{
		/*
		  Add in the foregin key field for this table, the fields are
		  $fieldName 		- the name of the DB Field
		  $displayName 	- what you want the name to be in your class
		  $dataType 		- Optional, sets the type in the PHPDoc comments for you
		  $keyField		- Optional, set to true on your primary key field
		*/

		$class->addKeyMember($rr["COLUMN_NAME"],beautify($rr["COLUMN_NAME"]),'INDEX');
		echo "<br/>Added FOREIGN KEY: ".$rr["COLUMN_NAME"];//."-".beautify($rr["COLUMN_NAME"])."-type:".$rr["COLUMN_TYPE"]."-Pri:".$rr["COLUMN_KEY"]="PRI";
	}
	echo "<br/>";
	//write indexes
	$ress=mysql_query("SHOW INDEX FROM ".$v." WHERE Non_Unique=1") or die(mysql_error());
	while($rr=mysql_fetch_array($ress))
	{
		/*
		  Add in the primary key field for this table, the fields are
		  $fieldName 		- the name of the DB Field
		  $displayName 	- what you want the name to be in your class
		  $dataType 		- Optional, sets the type in the PHPDoc comments for you
		  $keyField		- Optional, set to true on your primary key field
		*/

		$class->addKeyMember($rr["Column_name"],beautify($rr["Column_name"]),'INDEX');
		echo "<br/>Added INDEX: ".$rr["Column_name"];//."-".beautify($rr["COLUMN_NAME"])."-type:".$rr["COLUMN_TYPE"]."-Pri:".$rr["COLUMN_KEY"]="PRI";
	}
	echo "<br/>";
	if (!$class->generateClass())
	
	echo $r["TABLE_NAME"]."---Failed!";
	else 
	echo $r["TABLE_NAME"]."-Success!<br/>\n";
	}
echo "Finished";
}
?>
</td></tr>
</table>
</body>
</html>