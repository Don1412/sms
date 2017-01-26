<?
header("Content-Type: text/html; charset=utf-8");
require_once("sms/Functions.php");

include("DBConnect.php");

function checkbox_verify($_name) // Выполняет: проверку checkbox
{
	$result = 0; // обязательно прописываем, чтобы функция всегда возвращала результат
	if (isset($_REQUEST[$_name])) if ($_REQUEST[$_name] == 'on') $result = 1; // проверяем, а есть ли вообще такой checkbox на HTML форме, а то часто промахиваются
	return $result;
}
$count = 0; $indexCount[] = 1; $smsCount[] = 0; $partInfo = array(array()); $curInd = 0;
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/theme.blue.css">
<link rel="stylesheet" type="text/css" href="css/jquery.formstyler.css">
<script src = "js/jquery-1.12.3.min.js"></script>
<script src = "js/jquery.form.min.js"></script>
<script src = "js/bootstrap.min.js"></script>
<script src = "js/date.js"></script>
<script src = "js/functions.js"></script>
<!--<script src = "js/functions.js"></script>!-->
<meta charset="utf-8">
<title>SMS Sending</title>
</head>
<body>
	<div style="position:relative" id="zv-load">
	<div class="zv-load-css">
	<img src="img/loading.gif" alt="Загружаем страницу..." style="vertical-align: middle;" > Загружаем страницу...</div>
	</div>
	<div class="modal fade" id="main-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel">Изменение рассылки</h4>
		  </div>
		  <div id = "modal-text" class="modal-body">
			<form action = "manager.php" id = "newTaskData" role="form" method = "POST">
				<div class="form-group">
					<label>Выбор сервиса</label>
					<select name='Service'  id="cmbService" title="Please Select Service" onchange="CheckService();" class="form-control">
					  <option value="0">Routesms</option>
					  <option value="1">Буквенный</option>
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
				</div>
				<div class="form-group">
					<label for="pass">Номера получателей</label>
					<textarea class="form-control" id="numberArea" placeholder="Номера" rows = "20" name = "numbers" required></textarea>
					<div id="countNumber">К отправке номеров: 0</div>
				</div>
				<div class="form-group">
					<label>Тип сообщения</label>
					<select name='MessageType'  id="cmbMessageType" title="Please Select Message Type" onchange="SMSLength(this.value);" class="form-control">
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
				<input type = "text" name = "index" value = "<? echo $index; ?>" hidden></input>
				<input type="button" id="beginSend" class = "btn btn-large btn-success" value="Сохранить изменения"></input>
				<button type="button" class="btn btn-default" id="Close" data-dismiss="modal">Закрыть</button>
			</form>
		  </div>
		</div>
	  </div>
	</div>
	<div class="modal fade" id="main-modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" id="btnMClose" data-dismiss="modal" aria-hidden="true">&times</button>
			<h4 class="modal-title" id="myModalLabel">Смс в ожидании</h4>
		  </div>
		  <div id = "modal-text" class="modal-body">
			<div class="form-group">
				<label for="pass">Номера получателей</label>
				<textarea class="form-control" id="numberArea2" placeholder="Номера" rows = "20" name = "numbers" required></textarea>
			</div>
		  </div>
		</div>
	  </div>
	</div>

				<img src="img/back.png" id="btnBack" style="width: 25px" onclick="updateTable(0)" hidden/>
                <button id = "SMSSender" class="myButton" onclick="location.href='<?=getUrl();?>/SMS/'">Расширение</button>
				<button onclick="location.href='<?=getUrl();?>/SMS/SMSReport.php'" id = "SMSReport" class="myButton" style="width: 70px">Отчёт</button>
				<div id="timeDisp"><script>timeDisp();</script></div>
<table id="myTable" class="tablesorter tablesorter-blue">
	<thead>
		<tr class="tablesorter-headerRow">
			<td data-colum="0">№</td>
			<td data-colum="1">Cмс</td>
			<td data-colum="2">Номеров</td>
			<td data-colum="3">Время создания</td>
			<td data-colum="4">Периодичность</td>
			<td data-colum="5">Имя отправителя</td>
			<td data-colum="6">Сообщение</td>
			<td data-colum="7">Начало отправки</td>
			<td data-colum="8">Окончание отправки</td>
			<td data-colum="9">Действие</td>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>
<script>
var flag = true; var pendingFlag = 0; var indexEdit; var sortHeadOld = 0; var sortHead = 0; var sortOrder = 1; var tableData = 0, countArr;
$('td').click(function(){
    var table = $(this).parents('table').eq(0)
    var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
    this.asc = !this.asc
    if (!this.asc){rows = rows.reverse()}
    for (var i = 0; i < rows.length; i++){table.append(rows[i])}
})
function isDate(val) 
{
	var arr = new Array({'0': true, '-1': false});
    return arr[0][val.search(/\d\d.\d\d.\d\d\d\d \d\d:\d\d:\d\d/)];
}
function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index), valB = getCellValue(b, index);
		if(isDate(valA) && isDate(valB))
		{ 
			var dayA = valA.substring(0,2); var dayB = valB.substring(0,2);
			var monthA = valA.substring(3,5); var monthB = valB.substring(3,5);
			var yearA = valA.substring(6,10); var yearB = valB.substring(6,10);
			var hourA = valA.substring(11,13); var hourB = valB.substring(11,13);
			var minutesA = valA.substring(14,16); var minutesB = valB.substring(14,16);
			var secondsA = valA.substring(17,19); var secondsB = valB.substring(17,19);
			var unixA = Date.UTC(yearA, monthA, dayA, hourA, minutesA, secondsA); 
			var unixB = Date.UTC(yearB, monthB, dayB, hourB, minutesB, secondsB);
			return unixA - unixB;
		}
		else if($.isNumeric(valA) && $.isNumeric(valB)) return valA - valB; else  return valA.localeCompare(valB);
        //return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB)
    }
}
function getCellValue(row, index){ return $(row).children('td').eq(index).html() }
/* $('table').on('click', 'td', function() {
	sortHead = $(this).data("colum"); 
	console.log('sortHead = '+sortHead);
  $('.ui-sortable tr').sort(function(a, b) { // сортируем
  if(sortOrder == 1) { sortOrder = 0; return +$(b).find('[data-sort='+sortHead+']').text() - +$(a).find('[data-sort='+sortHead+']').text(); }
  else { sortOrder = 1; return +$(a).find('[data-sort='+sortHead+']').text() - +$(b).find('[data-sort='+sortHead+']').text(); }
}).appendTo('.ui-sortable');
}); */
function pendingSMS(ind)
{
	if(flag)
	{
		pendingFlag = ind;
		$.ajax({
			type: "POST",
			url: "functions.php",
			data: {'function':'edit', 'index':ind},
			dataType: 'json',  
			success: function(e)
			{
				$('#numberArea2').val(e[3]);
			}
		});
	}
}
function EditSend(ind)
{
	$('#main-modal').modal('show');
	flag = true;
	$.ajax({
		type: "POST",
		url: "functions.php",
		data: {'function':'edit', 'index':ind},
		dataType: 'json',  
		success: function(e)
		{
			var messageType = e[5]; var service = 0;
			if(e[5] == 100){ messageType = 0; service = 1; $('#cmbMessageType').prop("disabled", true); }
			else if(e[5] == 101) { messageType = 1; service = 1; }
			$('#text').val(e[2]);
			$('#selectInput1').val(e[2]);
			$('#numberArea').val(e[3]);
			$('#cmbMessageType').val(messageType).trigger("change");
			$('#smsarea').val(e[4]);
			$('#cmbService').val(service);
			if(e[9] == "0000-00-00") $('[name=PlanSend]').attr('checked', false); 
			else $('[name=PlanSend]').attr('checked', true); 
			$('[name=date]').val(e[9]);
			$('[name=hour]').val(e[10]);
			$('[name=minute]').val(e[11]);
			$('[name=index]').val(ind);
			if(e[6] == 0 && e[7] == 0) $('[name=PerSend]').attr('checked', false);
			else $('[name=PerSend]').attr('checked', true);
			indexEdit = ind;
			
		}
	});
}
function PauseSendE(ind, st)
{
	$.ajax({
		type: "POST",
		url: "functions.php",
		data: {'function':'pause', 'index':ind, 'status':st},
		success: function(e)
		{
		}
	});
}
function StopSend(ind, type)
{
	if(type == 1)
	{
		if(confirm("Вы действительно хотите удалить рассылку №" + ind + "?"))
		{
			$.ajax({
				type: "POST",
				url: "functions.php",
				data: {'function':'delete', 'index':ind, 'type':type},
				success: function(e)
				{
				}
			});
			return false;
		}
	}
	else if(type == 2)
	{
		if(confirm("Вы действительно хотите удалить сообщение ID:" + ind + "?"))
		{
			$.ajax({
				type: "POST",
				url: "functions.php",
				data: {'function':'delete', 'index':ind, 'type':type},
				success: function(e)
				{
				   location.reload();
				}
			});
			return false;
		}
	}
}
function updateTable()
{
	if(flag)
	{
		$.ajax({
			type: "POST",
			url: "functions.php",
			data: {'function':'updateTable', 'table':'manager', 'tableData':tableData},
			success: function(e)
			{
				if(tableData == 0) { $('tbody').html(e); tableData = 1; }
				else if($("#index").length)
				{
					var arr = JSON.parse(e);
					if(countArr != 0 && countArr != arr.length) tableData = 0;
					countArr = arr.length;
					console.log(tableData);
					for(var i = 0; i < arr.length; i++)
					{
						$('tr#'+i).children('td#name').text(arr[i]['index']);
						$('tr#'+i).children('td#countSMS').text(arr[i]['countSMS']);
						$('tr#'+i).children('td#countNum').text(arr[i]['countNum']);
						var dateStart = new Date(arr[i]['timeToSend']*1000).toString('dd.MM.yyyy HH:mm:ss');
						$('tr#'+i).children('td#startSend').text(dateStart);
						var dateEnd = new Date(arr[i]['endSend']*1000).toString('dd.MM.yyyy HH:mm:ss');
						$('tr#'+i).children('td#endSend').text(dateEnd);
					}
				}
			}
		});
	}
}
function flagBtn(state)
{
	if(state == 0) {flag = false; alert(1);}
	else if(state == 1) {flag = true}
}
$(document).ready(function()
{
	setInterval(function()
	{
		updateTable();
		pendingSMS(pendingFlag);
	}, 300);
	// $('button').mouseover(function(){flag = false});
	// $('button').mouseout(function(){flag = true});
	$('#beginSend').click(function()
	{
		clicked(indexEdit);
	});
});
</script>
</body>
</html>