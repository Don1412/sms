<?
require_once("sms/SendSMS.php");
require_once("sms/Functions.php");

global $sms_username;
$sms_username = "fartvan4";
global $sms_password;
$sms_password = "JFytCoqA";

$message = "";


include("DBConnect.php");

$destination = "";
$k = 0;
$source = "";

$timeLimit = time();

$res = mysql_query("SELECT * FROM pending WHERE timeToSend < $timeLimit");
echo mysql_num_rows($res);
if (mysql_num_rows($res) == 0){
	exit;
}

$sms = new SMS();


while($row = mysql_fetch_array($res)){
	$source = $row['source'];
	$to = $row['destination'];
	$message = $row['message'];
	$time = $row['timeToSend'];

	$sms->setDA($to) or mydie($errstr);
	$sms->setSA($source) or mydie($errstr);
	$sms->setUR("AF31C0D") or mydie($errstr);
	$sms->setDR("1") or mydie ($errstr);
	$sms->setMSG($message) or mydie ($errstr);
	$responses = send_sms_object($sms) or mydie ($errstr);

	mysql_query("DELETE FROM pending WHERE timeToSend < $timeLimit");
}

?>
