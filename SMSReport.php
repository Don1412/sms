<?
header("Content-Type: text/html; charset=utf-8");
require_once("sms/Functions.php");
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/theme.blue.css">
<script src = "js/jquery-1.12.3.min.js"></script>
<script src = "js/jquery.form.min.js"></script>
<script src = "js/bootstrap.min.js"></script>
<script src = "js/date.js"></script>
<script src = "js/md5.js"></script>
<meta charset="utf-8">
<title>SMS Report</title>
</head>
<body>
	<img src="img/back.png" id="btnBack" style="width: 25px" onclick="tableSort = 1;" hidden/>
	<button id = "SMSSender" class="myButton" data-toggle="modal" data-target="#main-modal1" onclick="location.href='<?=getUrl();?>/SMS/'">Расширение</button>
	<button onclick="location.href='<?=getUrl();?>/SMS/SMSManager.php'" id = "SMSReport" class="myButton" data-toggle="modal" data-target="#main-modal2">Менеджер</button>
	<button onclick="truncate();" id = "Truncate" class="myButton">Очистить таблицу</button>
<table id="myTable" class="tablesorter tablesorter-blue">
	<thead id="short">
		<tr class="tablesorter-headerRow">
		   <td data-column="0" class="tablesorter-header" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">№<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="1" class="tablesorter-header tablesorter-headerAsc primary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Кол-во номеров<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="2" class="tablesorter-header tablesorter-headerAsc secondary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Время отправления<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="3" class="tablesorter-header tablesorter-headerAsc tertiary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Имя отправителя<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="4" class="tablesorter-header tablesorter-headerAsc fourtiary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Сообщение<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="5" class="tablesorter-header tablesorter-headerAsc fivetiary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Статус<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="6" class="tablesorter-header tablesorter-headerAsc sixtiary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Тип сообщения<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		</tr>
	</thead>
	<thead id="full" hidden>
		<tr class="tablesorter-headerRow">
		   <td data-column="0" class="tablesorter-header" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Номер<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="1" class="tablesorter-header tablesorter-headerAsc primary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Статус<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		   <td data-column="2" class="tablesorter-header tablesorter-headerAsc secondary" tabindex="0" unselectable="on" style="-webkit-user-select: none;"><div class="tablesorter-header-inner">Время отправления<i class="tablesorter-icon tablesorter-icon"></i></div></td>
		</tr>
	</thead>
    <tbody>
   
	</tbody>
</table>
<script>
var flag = 1, sortHead = 0, sortOrder = 0, tableSort = 1, tableData = 0, countArr;
$('td').click(function(){
    var table = $(this).parents('table').eq(0)
    var rows = table.find('tr:visible:gt(0)').toArray().sort(comparer($(this).index()))
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
		else if($.isNumeric(valA) && $.isNumeric(valB)) 
		{ 
			return valA - valB; 
		}
		else  
		{
			return valA.localeCompare(valB);
		}
        //return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB)
    }
}
function getCellValue(row, index){ return $(row).children('td').eq(index).html() }
function truncate()
{
	if(confirm("Вы действительно хотите очистить логи?"))
	{
 		$.ajax({
			type: "POST",
			url: "truncate.php",
			success: function(e)
			{
			   location.reload();
			}
		});
		return false;
	}
}
function updateTable()
{
	if(tableSort == 1 && $('thead#short').is(':hidden'))
	{
		tableData = 0;
		$('thead#full').hide();
		$('#btnBack').hide();
		$('thead#short').show();
	}
	else if(tableSort != 1 && $('thead#full').is(':hidden'))
	{
		tableData = 0;
		$('thead#short').hide();
		$('thead#full').show();
		$('#btnBack').show();
	}
	if(flag)
	{
		$.ajax({
			type: "POST",
			url: "functions.php",
			data: {'function':'updateTable', 'table':'report', 'forTable':tableSort, 'tableData':tableData},
			success: function(e)
			{
				if(tableSort != 1) $('tbody').html(e);
				else 
				{
					if(tableData == 0) { $('tbody').html(e); tableData = 1; }
					else
					{
						var arr = JSON.parse(e);
						if(countArr != 0 && countArr != arr.length) tableData = 0;
						countArr = arr.length;
						//console.log(arr[0]);
						for(var i = 0; i < arr.length; i++)
						{
							$('tr#'+i).children('td#countNum').text(arr[i]['countNum']);
							var dateStart = new Date(arr[i]['timeToSend']*1000).toString('dd.MM.yyyy hh:mm:ss');
							$('tr#'+i).children('td#timeToSend').text(dateStart);
							$('tr#'+i).children('td#status').text(arr[i]['status']);
						}
					}
				}
			}
		});
	}
}
$(document).ready(function()
{
	setInterval(function()
	{
		updateTable();
		// pendingSMS(pendingFlag);
		// $('button').mouseenter(function(){flag = false;});
		// $('button').mouseleave(function(){flag = true});
	}, 300);
});
</script>
</body>
</html>