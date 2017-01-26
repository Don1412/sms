$(window).load(function () {
$("#zv-load").fadeOut("slow");
});
function notify(status, text){
  var html = '<div id = "alertBlock" class="alert alert-'+status+' fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Результат: </strong>' + text + '</div>';
  $('#notifications-top-left').html(html);
  $('#notifications-top-left').attr("x", screen.width);
  $('#notifications-top-left').attr("y", screen.height);
}

function setNewModalText(text){
  $('.modal-text').html(text);
}

function showResponse(responseText, statusText, xhr, $form){
  var notificationClass = "";
  if (responseText.search(/К отправке/i) != -1) notificationClass = "success";
  else if (responseText.search(/ошибка/i) != -1) notificationClass = "danger";
  else notificationClass = "warning";

  notify(notificationClass, responseText);
}

function timeDisp()
{
	$.ajax(
	{
		type: "POST",
		url: "functions.php",
		data: {'function':'time'},
		success: function(e)
		{
			$('#timeDisp').html(e);
		}
	});
	t=setTimeout('timeDisp()', 1000);
}

/* window.onload = function(){
    (function(){
        var date = new Date();
        //var time = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
        document.getElementsById('timeDisp').html = "loool";
        window.setTimeout(arguments.callee, 1000);
    })();
}; */
var maxCount = 0;
function SMSLength(value)
{
	if(value == 0) maxCount = 160;
	else if(value == 1) maxCount = 70;
	else if(value == -1) maxCount = 0;
	$('#count').html(maxCount);
}

function PauseSend(ind, st)
{
	//$res3 = mysql_query("DELETE FROM `pending` WHERE `index` = "ind"");
	if(confirm("Вы действительно хотите приостановить рассылку №" + ind + "?"))
	{
 		$.ajax({
			type: "POST",
			url: "pause.php",
			data: {'index':ind, 'status':st},
			success: function(e)
			{
			   location.reload();
			}
		});
		return false;
	}
}

function CheckService()
{
	var elem = document.getElementById('cmbMessageType');
	var curElem = document.getElementById('cmbService');
	var value = curElem.options[curElem.selectedIndex].value;
	if(value == 1) 
	{
		//elem.options[1].selected = true;
		//elem.disabled = true;
		SMSLength(elem.options[elem.selectedIndex].value);
	}
	else if (value == 2)
	{
		//elem.options[1].selected = true;
		//elem.disabled = true;
		SMSLength(elem.options[elem.selectedIndex].value);
	}
	else 
	{
		//elem.disabled = false;
		//elem.options[0].selected = true;
		SMSLength(elem.options[elem.selectedIndex].value);
	}
}

var countNumber = 0;
function countNum()
{
	var box = $("#numberArea").val();//помещаем в var text содержимое текстареи 
	var lines = box.split(/\r|\r\n|\n/);  //разбиваем это содержимое на фрагменты по переносам строк
	countNumber = lines.length;
	for(var i = 0; i < lines.length; i++) if(lines[i].length < 1) countNumber--;

	if($(this).html)
	{
		$('#countNumber').html('К отправке номеров: ' + countNumber);
	}
	return false;
}

var countSMS;
function clicked(ind) 
{
    var numbers = $('#numberArea').val(); var lines = numbers.split(/\n/); var countNumbers = lines.length;
	var message = $('#smsarea').val();
	var typeSend = "";
	if($("[name=PerSend]").prop("checked"))
	{
		var sendHours = $('[name=periodicHours]').val(); var sendMinutes = $('[name=periodicMinutes]').val(); var sendAmount = $('[name=amount]').val();
		typeSend += " Периодическая, "+sendAmount+" смс";
		if(sendHours != 0) typeSend += ", каждые "+sendHours+" часов";
		if(sendMinutes != 0) typeSend += ", каждые "+sendMinutes+" минут";
		if(dateSend == '' || sendHours == '' || sendMinutes == '')
		{
			alert("Не заполненно одно из полей периодической отправки!");
			return false;
		}
	}
	if($("[name=PlanSend]").prop("checked")) 
	{
		var dateSend = $('[name=date]').val(); var sendHours = $('[name=hour]').val(); var sendMinutes = $('[name=minute]').val();
		typeSend += " Запланированная, "+dateSend+" "+sendHours+":"+sendMinutes;
		if(dateSend == '' || sendHours == '' || sendMinutes == '')
		{
			alert("Не заполненно одно из полей запланированной отправки!");
			return false;
		}
	}
	if(!$("[name=PerSend]").prop("checked") && !$("[name=PlanSend]").prop("checked")) typeSend = "Моментальная";
	var channel = $("#cmbService option:selected").text();
	var SMScount = countSMS * countNumbers;
	if(confirm("Имя отправителя: "+$('#selectInput1').val()+"\nНомеров: "+countNumbers+"\nСообщение: "+message+"\nСмс к отправке: "+SMScount+"\nТип отправки: "+typeSend+"\nКанал: "+channel))
	{
		if(ind != 0)
		{
			$.ajax({
				type: "POST",
				url: "delete.php",
				data: {'index':ind},
				success: function(e)
				{
				}
			});
		}
		var ajaxFormOptions = 
		{
			success: showResponse,
			resetForm: true
		};
		$('#newTaskData').ajaxForm(ajaxFormOptions);
		$('#newTaskData').submit();
		setTimeout(function(){location.reload();}, 1000);
	}
    else return false;
}

var flag, flag2;
$(document).ready(function()
{
	$('#SelectDropdown1').hide();
	$('#selectInput1').click(function() 
	{
		$('#SelectDropdown1').show();
		$('#selectInput1').focus();
	});
	$('ul.drop1 li').mouseover(function(){flag = true});
	$('ul.drop1 li').mouseout(function(){flag = false});

	$("#selectInput1").focusout(function()
	{
		if(flag) 
		{
			$('ul.drop1 li').click(function() 
			{
				$('#selectInput1').val($(this).attr("id"));
				$('#SelectDropdown1').hide();
			});
		}			
		else $('#SelectDropdown1').hide();
		
	});
	$('ul.drop1 li button').click(function()
	{
		$('#selectInput1').focus();
		if(confirm("Вы действительно хотите удалить шаблон " + $(this).attr('id') + "?"))
		{
			$('#selectInput1').val('');
			$('li#'+$(this).attr('id')).remove();
			$.ajax(
			{
				type: "POST",
				url: "template.php",
				data: {'name':$(this).attr('id'), 'action':3},
				success: function(e)
				{
				}
			});
			return false;
		}
	});
	$('#selectInput1').keyup(function()
	{
		$('li').each(function(i,elem) 
		{
			if ($(this).text() == $('#selectInput1').val() || $('#selectInput1').val() == "")
			{
				$('#addTplName').hide();
				return false;
			} 
			else 
			{
				$('#addTplName').show();
			}
		});
	});
	$('#addTplName').click(function()
	{
		$('#addTplName').hide();
		var name = $('#selectInput1').val();
		$.ajax({
			type: "POST",
			url: "template.php",
			data: {'text':0, 'type':1, 'name':name, 'action':1},
			success: function(e)
			{
			}
		});
		var addOption = $('<li id="'+name+'">'+name+'<button type="button" id="'+name+'" style="position: absolute; right:5px;"><img src="img/mysor.png" width="10"></button></li>');
		$('ul.drop1').append(addOption);
	});
	
	$('#SelectDropdown2').hide();
	$('#selectInput2').click(function() 
	{
		$('#SelectDropdown2').show();
		$('#selectInput2').focus();
	});
	$('ul.drop2 li').mouseover(function(){flag2 = true});
	$('ul.drop2 li').mouseout(function(){flag2 = false});

	$("#selectInput2").focusout(function()
	{
		if(flag2) 
		{
			$('ul.drop2 li').click(function() 
			{
				$('#selectInput2').val($(this).attr("id"));
				var name = $(this).attr("id");
				$.ajax(
				{
					type: "POST",
					url: "template.php",
					data: {'name':name, 'action':2},
					success: function(e)
					{
						$('#smsarea').val(e);
					}
				});
				$('#SelectDropdown2').hide();
			});
		}			
		else $('#SelectDropdown2').hide();
		
	});
	$('ul.drop2 li button').click(function()
	{
		$('#selectInput2').focus();
		if(confirm("Вы действительно хотите удалить шаблон " + $(this).attr('id') + "?"))
		{
			$('#selectInput2').val('');
			$('li#'+$(this).attr('id')).remove();
			$.ajax(
			{
				type: "POST",
				url: "template.php",
				data: {'name':$(this).attr('id'), 'action':3},
				success: function(e)
				{
				}
			});
			return false;
		}
	});
	$('#selectInput2').keyup(function()
	{
		$('li').each(function(i,elem) 
		{
			if ($(this).text() == $('#selectInput2').val() || $('#selectInput2').val() == "")
			{
				$('#addTplMess').hide();
				return false;
			} 
			else 
			{
				$('#addTplMess').show();
			}
		});
	});
	$('#addTplMess').click(function()
	{
		$('#addTplMess').hide();
		var txt = $('#smsarea').val();
		var name = $('#selectInput2').val();
		$.ajax({
			type: "POST",
			url: "template.php",
			data: {'text':txt, 'type':2, 'name':name, 'action':1},
			success: function(e)
			{
			}
		});
		var addOption = $('<li id="'+name+'">'+name+'<button type="button" id="'+name+'" style="position: absolute; right:5px;"><img src="img/mysor.png" width="10"></button></li>');
		$('ul.drop2').append(addOption);
	});
		
	$("#tplMess").change(function(){

	alert('Selected value: ' + $(this).val());
	});

	$('#logsButton').click(setNewModalText("Text"));
  
	setInterval(function()
	{
		var box = $("#smsarea").val();
		countSMS = Math.ceil(box.length / maxCount);
		var len = box.length - maxCount * (countSMS - 1);
		var main = len * 100;
		var value = (main / maxCount);
		var count = maxCount - len;
		
		if(count == 0 && maxCount > 0) 
		{ 
			count = maxCount; 
			main = len *100; 
			value = (main / maxCount);
			countSMS++;
		}
		
		if(countSMS > 1 && count == maxCount) countSMS--;

		if(len <= maxCount)
		{
			var txt = count+' : '+countSMS+' сообщение';
			$('#count').html(txt);
			$('#bar').animate(
			{
				"width": value+'%',
			}, 1);
		}
		/*else
		{
			count = maxCount; main = box.length *100; value = (main / maxCount);
			countSMS++;
		}*/
		return false;
	}, 100);
	var countNumber = 0;
	//$("#numberArea").onchange(function()
	setInterval(function()
	{
		var box = $("#numberArea").val();//помещаем в var text содержимое текстареи 
		var lines = box.split(/\r|\r\n|\n/);  //разбиваем это содержимое на фрагменты по переносам строк
		countNumber = lines.length;
		for(var i = 0; i < lines.length; i++) if(lines[i].length < 1) countNumber--;

		if($(this).html)
		{
			$('#countNumber').html('К отправке номеров: ' + countNumber);
		}
		return false;
	}, 100);
});