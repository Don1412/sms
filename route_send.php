<?
require_once("sms/SendSMSRoute.php");
require_once("sms/Functions.php");
require_once("sms/Logger.php");

//Connection
global $sms_username;
$sms_username = "vmcservis";
global $sms_password;
$sms_password = "vmc321";

$message = "";

include("DBConnect.php");

//Logger
$dev_logger = new Logger("logs/dev/route_send.txt");
$logger = new Logger("logs/route_send.txt");

$timeLimit = time();
$res = mysql_query("SELECT * FROM pending2 WHERE timeToSend < $timeLimit AND status = 1");
if (mysql_num_rows($res) < 1){
        echo "There's no work for me!<br>";
        exit;
}

while($row = mysql_fetch_assoc($res))
{
	unset($all_numbers, $numbersToSend, $numbersArr);
	$index = $row['index'];
	$sender = $row['sender'];
	$numbers = $row['numbers']; if(empty($numbers)) { mysql_query("DELETE FROM `pending2` WHERE `index` = '$index'"); continue; }
	$message = $row['message'];
	$type = $row['type'];
	$perH = $row['perHours'];
	$perM = $row['perMinutes'];
	$amount = $row['perAmount'];
	$createTime = $row['createTime'];
	if($type == 0) { $msg = $message; $messageType = 0; }
	else if($type == 1) { $msg = utfToUCS($message); $messageType = 2; }
	else if($type == 100) { $msg = $message; $messageType = 100; }
	else if($type == 101) { $msg = $message; $messageType = 101; }
	if($messageType < 100)
	{
		$all_numbers = explode("\n", $numbers);
		if($amount == 0) { $numbersArr = $all_numbers; mysql_query("DELETE FROM `pending2` WHERE `index` = '$index'"); }
		else
		for($i = 0; $i < $amount; $i++)
		{
			if(empty($all_numbers[$i]))
			{
				mysql_query("DELETE FROM `pending2` WHERE `index` = '$index'");
				continue;
			}
			$numbersArr[$i] = $all_numbers[$i];
			unset($all_numbers[$i]);
			$updateNumbers = implode("\n", $all_numbers);
		}
		$numbersToSend = implode(',', $numbersArr);
		$data = array('username'=>$sms_username,
								'password'=>$sms_password,
								'type'=>$messageType,
								'dlr'=>1,
								'destination'=>$numbersToSend,
								'source'=>$sender,
								'message'=>$msg);
		$request = "http://smsplus.routesms.com:80//bulksms/bulksms?".http_build_query($data)."";
		$request = str_replace("%0D", "", $request);
		$ch = curl_init($request);
		if (!$ch) 
		{
			$errstr = "Could not connect to server.";
			echo $errstr;
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$serverresponse = curl_exec($ch);

		if (!$serverresponse) 
		{
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$errstr = "HTTP error: $code ".curl_error($ch)."\n";
			echo $errstr;
		}
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	}
	$add_hour = $perH * 60 * 60; $add_minutes = $perM * 60;
	$timeToSend = $row['timeToSend'] + $add_hour + $add_minutes;
	mysql_query("UPDATE `pending2` SET `timeToSend` = '$timeToSend', `numbers` = '$updateNumbers' WHERE `index` = '$index'");
	$str = '| Index = '.$index.' | DefaultNumbers = '.$numbers.' | NumbersToSend = '.$numbersToSend.' | NumbersToUpdate = '.$updateNumbers.' | Code = '.$code.' | serverresponse = '.$serverresponse.' | Update = '.mysql_errno() . ": " . mysql_error() . "\n".' |<br>';
	print_r($str);
	$key = md5($index.$sender.$message.$type);
	//$stat = $serverresponse.'|'.$timeToSend;
	unset($nums, $stat, $timeToSends);
	$part = explode(",", $serverresponse);
	for($i = 0; $i < count($part); $i++)
	{
		$numStat = explode("|", $part[$i]);
		$nums .= $numStat[1].'\n';
		$stat .= $numStat[0].'\n';
		$timeToSends .= $timeToSend.'\n';
	}
	mysql_query("UPDATE `log2` SET `numbers` = CONCAT(numbers, '$nums'), `status` = CONCAT(status, '$stat'), `timeToSend` = CONCAT(timeToSend, '$timeToSends') WHERE `key` = '$key'"); $err = mysql_error();
	$str2 = 'Numbers = '.$nums.' | Stat = '.$stat.' | timeToSend = '.$timeToSends;
	print_r($str2);
	$logger->log($err);
}
$logger->dispose();
$dev_logger->dispose();
?>