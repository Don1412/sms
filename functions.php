<?php
	require_once("sms/Functions.php");
	require_once("sms/Logger.php");
	
	include("DBConnect.php");
	$index = $_POST['index'];
	if($_POST['function'] == 'template')
	{
		$type = $_POST['type']; $text = $_POST['text']; $name = $_POST['name']; $action = $_POST['action'];
		if($action == 1)
		{
			$type = $_POST['type']; $text = $_POST['text']; $name = $_POST['name'];
			$insert_into = mysql_query("INSERT INTO `templates`(`type`, `text`, `name`) VALUES('$type', '$text', '$name')");
			$err = mysql_error();
		}
		else if($action == 2)
		{
			$res = mysql_query("SELECT * FROM `templates` WHERE `name` = '$name'");
			$err = mysql_error();
			while($row = mysql_fetch_array($res))
			{
				$type = $row['type']; $id = $row['id']; $text = $row['text'];
				print($row['text']);
			}
		}
		else if($action == 3)
		{
			$res = mysql_query("DELETE FROM `templates` WHERE `name` = '$name'");
		}
	}
	else if($_POST['function'] == 'time')
	{
		echo (date("d.m.Y H:i:s")); 
	}
	else if($_POST['function'] == 'delete')
	{
		if(isset($_POST['index']))
		{
			if($_POST['type'] == 1) $res = mysql_query("DELETE FROM `pending2` WHERE `index` = $index");
			//if($_POST['type'] == 2) $res = mysql_query("DELETE FROM `pending2` WHERE `id` = $index");
		}
	}
	else if($_POST['function'] == 'pause')
	{
		if(isset($_POST['index']) && isset($_POST['status']))
		{
			$st = $_POST['status'];
			$pauseTime = 0;
			if($st == 1)
			{
				$result = mysql_query("SELECT * FROM `pending2` WHERE `index` = $index");
				while($row = mysql_fetch_array($result))
				{
					$sleepTime = time() - $row['pauseTime']; $curTime = $row['timeToSend'] + $sleepTime; $index = $row['index'];//GetDiffDate(time(), $result2['pauseTime']);
					mysql_query("UPDATE `pending2` SET `status` = $st, `pauseTime` = 0, `timeToSend` = $curTime WHERE `index` = $index") or die(mysql_error());
				}
			}
			else
			{			
				$pauseTime = time();
				$res = mysql_query("UPDATE `pending2` SET `status` = $st, `pauseTime` = $pauseTime WHERE `index` = $index") or die(mysql_error());
			}
		}
	}
	else if($_POST['function'] == 'edit')
	{
		$index = $_POST['index'];
		if(isset($index))
		{
			$row = mysql_query("SELECT * FROM `pending2` WHERE `index` = $index") or die(mysql_error());
			$array = mysql_fetch_row($row); 
			echo json_encode($array);
		}
	}
	else if($_POST['function'] == 'clearDB')
	{
		$res = mysql_query("TRUNCATE TABLE log");
	}
	else if($_POST['function'] == 'updateTable')
	{
		if($_POST['table'] == 'manager')
		{
			$arr = array();
			$query = "SELECT * FROM `pending2` ORDER BY `index` ASC";
			$res = mysql_query($query); 
			$i = 0;
			while($row = mysql_fetch_array($res))
			{
				$arr[$i] = $row;
				$count = explode("\n", $row['numbers']);
				$numCount = count($count); $arr[$i]['countNum'] = $numCount;
				$index = $row['index']; 
				$tmpIndex = $index;
				$sender = $row['sender'];
				$message = $row['message']; 
				/*if(strlen($message)>70) 
				{
					$per = round(strlen($message) / 70);
					for($j = 1; $j <= $per; $j++)
					{
						$pos = 70 * $j;
						$message = substr_replace($message, '\n', $pos, 0); //substr($message,0,50).'...';
					}
				}*/
				$createTime = $row['createTime'];
				$perAmount = $row['perAmount']; $perMinutes = $row['perMinutes'];
				$startSend = $row['timeToSend']; $endSend = $startSend + ($numCount*$perMinutes/$perAmount)*60; $arr[$i]['endSend'] = $endSend;
				$status = $row['status'];
				$len = mb_strlen($row['message'], 'utf-8');
				if($row['type'] == 0) 
				{
				   $messageType = "[RouteSMS]Text";
				   $smsCount = ceil($len / 160) * $numCount;
				}
				else if ($row['type'] == 1) 
				{
				   $messageType = "[RouteSMS]Unicode";
				   $smsCount = ceil($len / 70) * $numCount;
				}
				else if ($row['type'] >= 100) 
				{
				   $messageType = "Буквенный";
				   $smsCount = ceil($len / 160) * $numCount;
				}
				else if ($row['type'] >= 101) 
				{
				   $messageType = "Цифровой";
				   $smsCount = ceil($len / 160) * $numCount;
				}
				$arr[$i]['countSMS'] = $smsCount;
				if($perAmount == 0) $period = 'Моментальная отправка';
				else $period = $perAmount.' смс в '.$perMinutes.' мин';
				if($status == 1)
				{
					$pauseBtn = '<button type="button" class="btn btn-warning btn-xs active" onclick="PauseSend('.$index.', 2); event.stopPropagation();">Pause</button></td>';
				}
				else if($status == 2)
				{
					$pauseBtn = '<button type="button" class="btn btn-success btn-xs active" onclick="PauseSend('.$index.', 1); event.stopPropagation();">Start</button></td>';
				}
				$bodyTable .= '
					<tr name="tables" id="'.$i.'" data-toggle="modal" data-target="#main-modal2" onclick="pendingSMS('.$index.');">
						<td id="index">'.$index.'</td>
						<td id="countSMS">'.$smsCount.'</td>
						<td id="countNum">'.$numCount.'</td>
						<td>'.date("d.m.Y H:i:s", $createTime).'</td>
						<td>'.$period.'</td>
						<td>'.$sender.'</td>
						<td>'.$message.'</td>
						<td id="startSend">'.date("d.m.Y H:i:s", $startSend).'</td>
						<td id="endSend">'.date("d.m.Y H:i:s", $endSend).'</td>
						<td><button type="button" class="btn btn-danger btn-xs active" onclick="StopSend('.$index.', 1); event.stopPropagation();">Stop</button>
							<button type="button" class="btn btn-primary btn-xs active" onclick="event.stopPropagation(); EditSend('.$index.');">Edit</button>
						'.$pauseBtn.'
					</tr>';
				$i++;
			}
			if($_POST['tableData'] == 0) echo $bodyTable;
			else echo json_encode($arr);
		}
		else if($_POST['table'] == 'report')
		{
			$res = mysql_query("SELECT * FROM `log2`");
			$i = 0;
			while($row = mysql_fetch_array($res))
			{
				$arr[$i] = $row;
				$errors[0] = array(
							"1701" => "Отправлено",
							"1702" => "Ошибка URL",
							"1703" => "Неправильный логин или пароль",
							"1704" => "Неверный тип",
							"1705" => "Некорректно введено сообщение",
							"1706" => "Неверно указан номер получателя",
							"1707" => "Неверно указан номер отправителя",
							"1708" => "Ошибка dlr",
							"1709" => "Ошибка проверки пользователя",
							"1710" => "Внутренняя ошибка",
							"1025" => "Недостаточно средств",
							"1715" => "Ожидание ответа",
							"send" => "Отправлено",
							);
				$answer = "Unknown";
				$key = $row['key'];
				$index = $row['index'];
				$sender = $row['sender'];
				$message = $row['message'];
				$type = $row['type'];
				$createTime = $row['createTime'];
				$key = $row['key'];
				$stats = explode("\n", $row['status']); array_pop($stats);
				$nums = explode("\n", $row['numbers']); array_pop($nums);
				$times = explode("\n", $row['timeToSend']); array_pop($times);
				$count = 0; $status = '';
				for($j = 0; $j < count($stats); $j++)
				{
					$count++;
					$timeToSend = $times[$j];
					$status = $stats[$j];
					if($_POST['forTable'] == $key)
					{
						$bodyTable2 .= 
							'<tr>
								<td id="nums">'.$nums[$j].'</td>
								<td id="status">'.$errors[0][$status].'</td>
								<td id="timeToSend">'.date("d.m.Y H:i:s", $timeToSend).'</td>
							</tr>';
					}
				}
				if($type == 0) { $typeMess = "[RouteSMS]Text"; }
				else if($type == 1) { $typeMess = "[RouteSMS]Unicode"; }
				else if($type == 100) { $typeMess = "Буквенный"; }
				else if($type == 101) { $typeMess = "Цифровой"; }
				else $typeMess = "Unknown";
				$strKey = "'".$key."'";
				$arr[$i]['countNum'] = $count; 
				$arr[$i]['timeToSend'] = $timeToSend; 
				$arr[$i]['status'] = $errors[0][$status];
				$arr[$i]['type'] = $typeMess;
				$bodyTable .= '<tr id="'.$i.'" name="1" onclick="tableSort = '.$strKey.';">
								<td id="index">'.$index.'</td>
								<td id="countNum">'.$count.'</td>
								<td id="timeToSend">'.date("d.m.Y H:i:s", $timeToSend).'</td>
								<td id="sender">'.$sender.'</td>
								<td id="message" width="800">'.$message.'</td>
								<td id="status">'.$errors[0][$status].'</td>
								<td id="type">'.$typeMess.'</td>
							</tr>';
				$i++;
			}
			unset($timeToSend);
			if($_POST['forTable'] == 1)
			{
				if($_POST['tableData'] == 1) echo json_encode($arr); 
				else echo $bodyTable;
			}
			else
			{
				echo $bodyTable2;
			}
		}
	}
?>