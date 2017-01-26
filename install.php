<?
header("Content-Type: text/html; charset=utf-8");
require_once("sms/Functions.php");
if(empty($_POST))
{
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/jquery.formstyler.css">
<script src = "js/jquery-1.12.3.min.js"></script>
<script src = "js/jquery.form.min.js"></script>
<script src = "js/bootstrap.min.js"></script>
<script src = "js/functions.js"></script>
<meta charset="utf-8">
<title>Установка SMS Manager</title>
</head>
<body>
	<div class = "row" style = "margin-top: 50px;">
        <div class = "col-md-8 col-md-offset-2">
            <h2>Установка SMS Manager</h2><br>
				<hr>
                <form action = "install.php" id = "install" role="form" method = "POST">
					<div class="form-group">
						<label for="text">Имя базы данных</label><input id="bdName" name="bdName" class="form-control">
						<label for="text">Логин базы данных</label><input id="bdLogin" name="bdLogin" class="form-control">
						<label for="text">Пароль базы данных</label><input id="bdPassword" type="password" name="bdPassword" class="form-control">
					</div>
                    <input type="submit" id="bdSave" class = "btn btn-large btn-success" value="Установить"></input>
                </form>
        </div>
</div>
</body>
</html>
<?
}
else
{
	$bdName = $_POST['bdName'];
	$bdLogin = $_POST['bdLogin'];
	$bdPassword = $_POST['bdPassword'];
	
	$dbconnect = mysql_connect ('localhost', $bdLogin, $bdPassword);
	if (!$dbconnect) { echo "Не могу подключиться к серверу базы данных! Проверьте правильно ли введены данные либо обратитесь в техподдержку хостинга."; exit; }
	if(@mysql_select_db($bdName)) { //echo "Подключение к базе $dbname установлено!";}
		else die("Не могу подключиться к базе данных $bdName! Проверьте правильно ли введены данные либо обратитесь в техподдержку хостинга.");
	
	$query = "CREATE TABLE IF NOT EXISTS `log2` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `key` varchar(500) NOT NULL,
			  `index` int(9) NOT NULL,
			  `sender` varchar(30) NOT NULL,
			  `numbers` text NOT NULL,
			  `message` varchar(500) NOT NULL,
			  `type` int(11) NOT NULL,
			  `timeToSend` varchar(255) NOT NULL,
			  `status` varchar(256) NOT NULL,
			  `createTime` int(11) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `key` (`key`)
			) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=cp1251;"; mysql_query($query);
	$query = "CREATE TABLE IF NOT EXISTS `pending2` (
			  `index` int(11) NOT NULL AUTO_INCREMENT,
			  `key` varchar(500) CHARACTER SET cp1251 NOT NULL,
			  `sender` varchar(30) NOT NULL,
			  `numbers` text NOT NULL,
			  `message` varchar(500) NOT NULL,
			  `type` int(11) NOT NULL,
			  `perHours` int(2) NOT NULL,
			  `perMinutes` int(2) NOT NULL,
			  `perAmount` int(11) NOT NULL,
			  `planDate` date NOT NULL,
			  `planHours` int(2) NOT NULL,
			  `planMinutes` int(2) NOT NULL,
			  `timeToSend` int(255) NOT NULL,
			  `status` int(11) NOT NULL,
			  `createTime` int(255) NOT NULL,
			  `pauseTime` int(255) NOT NULL,
			  PRIMARY KEY (`index`),
			  UNIQUE KEY `key` (`key`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;"; mysql_query($query);
	$str = '
	[database]
		host = "localhost";
		username = "'.$bdLogin.'";
		pass = "'.$bdPassword.'";
		name = "'.$bdName.'";
	
	[install]
		status = "success";';
	file_put_contents('config.ini', $str);
}
?>