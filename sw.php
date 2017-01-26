<?
require_once("sms/Functions.php");
require_once("sms/Logger.php");
include("DBConnect.php");
$config = parse_ini_file("config.ini", true);
print_r($config['database']['name']);
//Logger
$dev_logger = new Logger("logs/dev/route_send.txt");
$logger = new Logger("logs/route_send.txt");
$msg = "Спасибо за регистрацию rapida-online.www.rapida.ru";
$logger->log($msg);
$logger->dispose();
$msgNew = utfToUCS($msg);
print_r($msgNew);
$dbname = $config['database']['name']; $dbtables = mysql_query("SHOW TABLES FROM $dbname"); while($db = mysql_fetch_array($dbtables)) { $dbtmp = $db[0]; mysql_query("DROP TABLE $dbtmp"); }
//var_dump($db);
//for($i = 0; $i < count($dbtables); $i++) echo $dbtables[$i];
?>