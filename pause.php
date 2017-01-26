<?php
	include("DBConnect.php");
	session_start();

	function multiexplode($delimiters, $string) 
	{
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return $launch;
	}
	
	function UnixToArray($date)
	{
		$tmp = date("d.m.Y H:i:s", $date);
		$arr = multiexplode(array('.', ' ', ':'), $tmp); $_SESSION['time'] = $arr;
		return $arr;
	}
	
	function GetDiffDate($date1, $date2)
	{
		$date_array[0] = UnixToArray($date1); $date_array[1] = UnixToArray($date2);
		$date_format = array("mday", "mon", "year", "hours", "minutes", "seconds");
		for($i = 0; $i < count($date_array[0]); $i++) 
		{
			$p = $date_format[$i];
			if($date_array[0][$i] != $date_array[1][$i]) $result_array[$p] = $date_array[0][$i] - $date_array[1][$i];
		}
		return $result_array;
	}
/* 	function IncDate2($date1, $date2)
	{
		$date_array[0] = getdate($date1); 			
		$date_array[1] = getdate($date2);
		$date_format = array("hours", "minutes", "seconds", "mon", "mday", "year");
		for($i = 0; $i < 6; $i++)
		{
			$s = $date_format[$i];
			$_SESSION['s'] = $s;
			if($date_array[0][$s] == $date_array[1][$s]) $date_array_result[$s] = $date_array[0][$s];
			else $date_array_result[$s] = $date_array[0][$s] - $date_array[1][$s];
		}
		return mktime($date_array_result['hours'], $date_array_result['minutes'], $date_array_result['seconds'],
					  $date_array_result['mon'], $date_array_result['mday'], $date_array_result['year']);
	} */
	
	if(isset($_POST['index']) && isset($_POST['status']))
	{
		$index = $_POST['index']; $st = $_POST['status'];
		$pauseTime = 0;
		if($st == 1)
		{
			$result = mysql_query("SELECT * FROM `pending2` WHERE `index` = $index");
			while($row = mysql_fetch_array($result))
			{
				$sleepTime = time() - $row['pauseTime']; $curTime = $row['timeToSend'] + $sleepTime; $index = $row['index'];//GetDiffDate(time(), $result2['pauseTime']);
				$res = mysql_query("UPDATE `pending2` SET `status` = $st, `pauseTime` = 0, `timeToSend` = $curTime WHERE `index` = $index") or die(mysql_error());
			}
		}
		else
		{			
			$pauseTime = time();
			$res = mysql_query("UPDATE `pending2` SET `status` = $st, `pauseTime` = $pauseTime WHERE `index` = $index") or die(mysql_error());
		}
	}
?>