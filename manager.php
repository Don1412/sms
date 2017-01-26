<?
	require_once("sms/Functions.php");
	require_once("sms/Logger.php");
	include("DBConnect.php");
	$logger = new Logger("keyManager.txt");

	$res = mysql_query("SELECT * FROM `pending2` ORDER BY `index` DESC LIMIT 1");
	$res2 = mysql_fetch_array($res);
	if ($res2['index'] < 1) $index = 1;
	else $index = $res2['index'] + 1;

	if(!empty($_POST['index'])) $index = $_POST['index']; 
	$name = $_POST['selectInput1'];
	$date = $_POST['date'];
	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$periodicHours = $_POST['periodicHours'];
	$periodicMinutes = $_POST['periodicMinutes'];
	$numbers = $_POST['numbers'];
	$chkService = $_POST['Service'];
	$type = $_POST['MessageType'];
	$message = $_POST['message'];
	$amount = $_POST['amount'];
	if($chkService == 1) $type = 100;
	else if($chkService == 2) $type = 101;

	$timeString = $hour.':'.$minute.' '.$date;
	$time = strtotime($timeString);
	$realtime = time();

	function checkbox_verify($_name) // Выполняет: проверку checkbox
	{
		$result = 0; // обязательно прописываем, чтобы функция всегда возвращала результат
		if(isset($_REQUEST[$_name])) if($_REQUEST[$_name] == 'on') $result = 1; // проверяем, а есть ли вообще такой checkbox на HTML форме, а то часто промахиваются
		return $result;
	}

	if (empty($numbers) || empty($message)){
	  echo "Ошибка! Не заполнены некоторые поля!";
	}
	else
	{
		if(!checkbox_verify('PerSend')) 
		{ 
			$periodicHours = 0; 
			$periodicMinutes = 0;
			$amount = 0;
		}
		if(!checkbox_verify('PlanSend')) 
		{ 
			$date = 0; 
			$hour = 0;
			$minute = 0;
		}
		if(!checkbox_verify('PlanSend') && !checkbox_verify('PerSend')) $amount = 0;
		if($date == "0000-00-00") $timeToSend = time(); 
		else $timeToSend = $time;
		$createTime = time();
		$key = md5($index.$name.$message.$type);
		$logString = ' | '.$index.' | '.$name.' | '.$message.' | '.$type.' | '.$createTime.' | '.$key.' | ';
		$logger->log($logString);
		$logger->dispose();
		mysql_query("INSERT INTO `log2`(`key`, `index`, `sender`, `message`, `type`, `createTime`) VALUES
									   ('$key', '$index', '$name', '$message', '$type', '$createTime')") 
									   or die(mysql_error());
		$insert_into = mysql_query("INSERT INTO `pending2`(`index`, `key`, `sender`, `numbers`, `message`, `type`, `perHours`, `perMinutes`, 
										`perAmount`, `planDate`, `planHours`, `planMinutes`, `timeToSend`, `status`, `createTime`) VALUES
										('$index', '$key', '$name', '$numbers', '$message', '$type', '$periodicHours', '$periodicMinutes', '$amount', 
										'$date', '$hour', '$minute', '$timeToSend', '1', '$createTime')") or die(mysql_error());
		echo 'К отправке - '.count($numbers).' номеров.';
	}

?>
