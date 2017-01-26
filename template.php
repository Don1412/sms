<?php
	require_once("sms/Functions.php");
	require_once("sms/Logger.php");
	
	include("DBConnect.php");
	
	$logger = new Logger("route.txt");
	$type = $_POST['type']; $text = $_POST['text']; $name = $_POST['name']; $action = $_POST['action'];
	if($action == 1)
	{
		$type = $_POST['type']; $text = $_POST['text']; $name = $_POST['name'];
		$insert_into = mysql_query("INSERT INTO `templates`(`type`, `text`, `name`) VALUES('$type', '$text', '$name')");
		$err = mysql_error();
		$logString = "Type: $type | Name: $name | Text: $text | Success: $insert_into | Error: $err";
		$logger->log($logString);
	}
	else if($action == 2)
	{
		$res = mysql_query("SELECT * FROM `templates` WHERE `name` = '$name'");
		$err = mysql_error();
		while($res2 = mysql_fetch_array($res))
		{
			$type = $res2['type']; $id = $res2['id']; $text = $res2['text'];
			print($res2['text']);
			$logString = "ID: $id | Type: $type | Text: $text | Success: $res | Error: $err";
			$logger->log($logString);
		}
	}
	else if($action == 3)
	{
		$res = mysql_query("DELETE FROM `templates` WHERE `name` = '$name'");
	}
?>