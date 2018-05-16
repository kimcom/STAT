<script type="text/javascript">
$(document).ready(function () {
	var reportID = 12; 
//Object Converter
	oconv	= function (a) {var o = {};for(var i=0;i<a.length;i++) {o[a[i]] = '';} return o;}
	strJoin = function (obj){ var ar = []; for (key in obj){ar[ar.length] = obj[key];}return ar;}
	keyJoin = function (obj){ var ar = []; for (key in obj){ar[ar.length] = key;}return ar;}
	clearObj= function (obj){ for(key in obj){for(k in obj[key]){delete obj[key][k];}}return obj;}
	var settings = new Object();
	$("#dialog").dialog({
		autoOpen: false, modal: true, width: 400, //height: 300,
		buttons: [{text: "Закрыть", click: function () {
			    $(this).dialog("close");}}],
		show: {effect: "clip",duration: 500},
		hide: {effect: "clip",duration: 500}
    });
	$("#dialog_progress").dialog({
		autoOpen: false, modal: true, width: 400, height: 400,
		show: {effect: "explode",duration: 1000},
		hide: {effect: "explode",duration: 1000}
    });
	dt = new Date();
//	dt.setMonth(dt.getMonth() - 1, 1);
	$("#DT_start").datepicker({
		//showOn: "both", 
		numberOfMonths: 1,
		showButtonPanel: true, 
		dateFormat: 'dd/mm/yy',
		showWeek: true,
		//showAnim: "fold"
	});
	$("#DT_start").datepicker("setDate", dt);
	dt = new Date();
//	dt.setDate(0);
    $("#DT_stop").datepicker({
		//showOn: "both", 
		numberOfMonths: 1,
		showButtonPanel: true,
		showWeek: true,
		dateFormat: 'dd/mm/yy'
	});
	$("#DT_stop").datepicker("setDate", dt);
	$(".ui-datepicker-trigger").addClass("hidden-print");

	//заполнение данных для анализа
	var data = [
		{id: 'rep1', text: 'Кол-во чеков: наполнитель + сухой корм для кошек'}, 
//		{id: 'rep2', text: 'Количество аннулированных дисконтных карт'}, 
//		{id: 'rep3', text: 'Количество дисконтных карт, которые были активны'}, 
//		{id: 'rep4', text: 'Количество временных карт, замененных на постоянные'},
//		{id: 'rep5', text: 'Сумма покупок по дискотным картам'}
	];
	$("#select_report").select2({data: data, placeholder: "Выберите вид отчета", minimumResultsForSearch: Infinity});
	$("#select_report").select2("val", '');
		
	//заполнение данных для анализа
    var data = [
		{id: 'cl.ClientID', text: 'По торговым точкам'},
		{id: 'GoodID', text: 'По товарам'},
		{id: 'g.Brand', text: 'По брендам'},
		{id: 's.SellerID', text: 'По сотрудникам'},
		{id: 'p.PromoID', text: 'По акциям'},
		{id: 'ch.CheckID', text: 'По чекам'},
		{id: 'cl.City', text: 'По городам'},
		{id: 'catName', text: 'По категориям товара'},
		{id: 'cattypeName', text: 'По виду животных'},
    ];
	$("#group_report").select2({data: data, placeholder: "Выберите вид группировки", minimumResultsForSearch: Infinity});
	$("#group_report").select2("val", 'cl.ClientID');

	sidx = function () { 
		if ($("#group_report").select2("val")=='cl.ClientID')	return 'NameShort asc';
		if ($("#group_report").select2("val")=='GoodID')	return 'Article asc, Name asc';
		if ($("#group_report").select2("val")=='g.Brand')	return 'g.Brand asc';
		if ($("#group_report").select2("val")=='s.SellerID')return 's.Name asc';
		if ($("#group_report").select2("val")=='p.PromoID')	return 'p.Name asc';
		if ($("#group_report").select2("val")=='ch.CheckID')return 'ch.ClientID asc, ch.CheckID asc';
		if ($("#group_report").select2("val")=='cl.City')	return 'cl.City asc';
		if ($("#group_report").select2("val")=='catName')	return 'catName asc';
		if ($("#group_report").select2("val")=='cattypeName')	return 'cattypeName asc';
		return '';
	}
	$.post('../Engine/setting_get?sid='+reportID, function (json) {
		$("#select_report_setting").select2({
		    createSearchChoice: function (term, data){
				if ($(data).filter(function(){return this.text.localeCompare(term) === 0;}).length === 0) {
				    return {id: term, text: term};
				}
			},
			//multiple: true,
			placeholder: "Выберите настройку отчета",
		    data: {results: json, text: 'text'}
		});
		$("#select_report_setting").select2("val", "тест");
		$("#select_report_setting").click();
    });

	$("#select_report").click(function () { 
		$('#a_tab_report').html('Отчет "'+$("#select_report").select2("data").text+'"');
//		if ($("#select_report").select2("val")=='rep5') {
//			$('#div_group').removeClass('hide');
//		}else{
//			$('#div_group').addClass('hide');
//		}
	});
	$("#select_report_setting").click(function () { 
		var setting = $("#select_report_setting").select2("data");
		if (setting == null) return;
		clearObj(settings);
		$.post('../Engine/setting_get_byName?sid='+reportID+'&sname='+setting.text,
		function (json) {
			var set = json.Setting;
			var aset = set.split('&');
			for(key in aset){
				var k = aset[key].split('=');
				if(k[1]=='')continue;
				if(k[0]=='DT_start') {if(json.UserID!=11)$("#DT_start").val(k[1]);continue;}
				if(k[0]=='DT_stop') {if(json.UserID!=11)$("#DT_stop").val(k[1]);continue;}
				if(k[0]=='repid') {	$("#select_report").select2("val", k[1]);continue;}
				var l = k[1].split('|');
				var m = l[0].split(';');
				var n = l[1].split(';');
				for(i=0;i<m.length;i++){
					if(m[i]=='') continue;
					if(k[0]=='grouping'){
						settings[k[0]][i]=n[i];
					}else{
						settings[k[0]][m[i]]=n[i];
					}
				}
			}
			$("#select_report").click();
//$('#button_report_run').click();
		});
	});
		
	$("#setting_filter a").click(function() {
		operid = '';
		var command = this.parentNode.previousSibling.previousSibling.previousSibling.previousSibling;
		if(command.tagName=='SPAN'){
			command = this.parentNode.previousSibling.previousSibling;
		}
//console.log(command,$(this).html(),this.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.id);
		if(command.tagName=="INPUT"){
			operid = command.id;
		}else if(command.tagName=="DIV"){
			operid = this.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.id;
	    } else{
			alert('Ошибка определения действия!');
			return;
		}
		if($(this).html()=='X'){
			for(k in settings[operid]){
				delete settings[operid][k];
			}
			$("#"+operid).val(strJoin(settings[operid]).join(';'));
			$("#"+operid).attr("title", strJoin(settings[operid]).join("\n"));
			return;
		}
		if(operid=='select_report_setting'){
			setting = $("#select_report_setting").select2("data");
			if(setting==null){
				$("#dialog").css('background-color','');
				$("#dialog>#text").html('Введите название для сохранения настройки!');
				$("#dialog").dialog("open");
				return;
			}
			setID = setting.id;
			if(setting.id==setting.text) setID='';
			$.post("../Engine/setting_set"+
					"?DT_start="+ $("#DT_start").val()+
					"&DT_stop="	+ $("#DT_stop").val()+
					"&repid="	+ $("#select_report").select2("val"),
				{	sid:	reportID,
					sname:	setting.text,
				}, 
				function (data) {
					if (data == 0) {
						$("#dialog").css('background-color','linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
						$("#dialog>#text").html('Возникла ошибка.<br/>Сообщите разработчику!');
						$("#dialog").dialog("open");
					} else {
						$("#dialog").css('background-color','');
						$("#dialog>#text").html('Настройки успешно сохранены!');
						$("#dialog").dialog("open");
					}
			});
		}
		if(operid=='DT_start')
			$("#DT_start").datepicker("show");
		if(operid=='DT_stop')
			$("#DT_stop").datepicker("show");
	});

	$('#button_report_export').click(function (e) {
		$("#dialog_progress").dialog( "option", "title", 'Ожидайте! Готовим данные для XLS файла');
		$("#dialog_progress").dialog("open");
		setTimeout(function () {
			var file_name = $("#select_report").select2("data").text;
			var html = file_name + "<br>" + $("#tab_report").html();
			html = html.split("<table").join("<table border='1' ");
			var report_name = 'report' + reportID;
			$.ajax({
				type: "POST",
				data: ({report_name: report_name, file_name: file_name, html: html}),
				url: '../Engine/set_file',
				dataType: "html",
				success: function (data) {
					$("#dialog_progress").dialog("close");
					var $frame = $('<iframe src="../Engine/get_file?report_name=' + report_name + '&file_name=' + file_name + '" style="display:none;"></iframe>');
					$('html').append($frame);
				}
			});
		}, 1000);
	});
	
	$('#button_report_run').click(function (e) {
//		if ($("#select_report").select2('val') == 'rep5' && $("#group_report").select2("val")==''){
//			$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
//			$("#dialog>#text").html('Необходимо выбрать группировку!');
//			$("#dialog").dialog("open");
//			return;
//		}
		$("#dialog_progress").dialog("option", "title", 'Ожидайте! Выполняется формирование отчета...');
		$("#dialog_progress").dialog("open");
		$("#a_tab_report").tab('show');
		
		prmRep = "<b>Отбор данных выполнен по критериям:</b> ";
		prmRep += "<br>" + "Период с " + $("#DT_start").val() + " по " + $("#DT_stop").val();
		prmRep += "<br>Вид отчета: " + $("#select_report").select2("data").text + ".";
		if ($("#select_report").select2('val')=='rep5')
			prmRep += "<br>Вид группировки: " + $("#group_report").select2("data").text + ".";

		$("#report_param_str").html(prmRep);
		$.post("../reports/report"+reportID+"_data" +
				"?sid=" + reportID +
				"&repid=" + $("#select_report").select2("val") +
				//"&grouping=" + $("#group_report").select2("val") +
				"&UserID=<?php echo $_SESSION['UserID']; ?>" +
				"&DT_start=" + $("#DT_start").val() +
				"&DT_stop=" + $("#DT_stop").val()
				//"&sidx=" + sidx()
				,
				function (json) {
					$('#div_report').html(json.table1);
					setTimeout(function () {
						$("#dialog_progress").dialog("close");
						$("#button_report_export").removeClass("disabled");
					}, 300);
					setTimeout(function () {
						if (json.error!=''){
							$("#dialog").css('background-color','linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
							$("#dialog>#text").html('Возникла ошибка.<br>Сообщите разработчику!<br><br>'+json.error[2].toString());
							$("#dialog").dialog("open");
						}
					}, 1500);
		});
	});
});

</script>
<style>
 #feedback { font-size: 12px; }
 .selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
 .selectable li { margin: 3px; padding: 7px 0 0 5px; text-align: left;font-size: 14px; height: 34px; }
</style>
<div class="container center">
	<ul id="myTab" class="nav nav-tabs floatL active hidden-print" role="tablist">
		<li class="active"><a href="#tab_filter" role="tab" data-toggle="tab">Настройки отбора</a></li>
		<li><a id="a_tab_report" href="#tab_report" role="tab" data-toggle="tab">Отчет</a></li>
	</ul>
	<div class="floatL">
		<button id="button_report_run" class="btn btn-sm btn-info frameL m0 h40 hidden-print font14">
			<span class="ui-button-text" >Сформировать отчет</span>
		</button>
	</div>
	<div class="floatL">
		<button id="button_report_export" class="btn btn-sm btn-default frameL m0 h40 hidden-print font14 w150 disabled">
			<span class="ui-button-text" >Экспорт в EXCEL</span>
		</button>
	</div>
	<div class="tab-content">
		<div class="active tab-pane min530 m0 w100p ui-corner-tab1 borderColor frameL border1" id="tab_filter">
			<div id="setting_filter" class='p5 frameL w500 h400 ml0 border0' style='display:table;'>
				<legend>Параметры отбора данных:</legend>
				<div class="input-group input-group-sm mt10 w100p">
					<span class="input-group-addon w130">Настройки:</span>
					<div class="w100p" id="select_report_setting" name="select_report_setting"></div>
					<span class="input-group-btn hide">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button"><img class="img-rounded h20 m0" src="../../images/save-as.png"></a>
					</span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Период с:</span>
					<input id="DT_start" name="DT_start" type="text" class="form-control" placeholder="Дата нач." required>
					<span class="input-group-btn hide">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
					<span class="input-group-addon">по:</span>
					<input id="DT_stop"  name="DT_stop"  type="text" class="form-control" placeholder="Дата кон." required>
					<span class="input-group-btn hide">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Вид отчета:</span>
					<div class="w328 maxw328" id="select_report" name="select_report"></div>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm mt5 w100p hide" id="div_group">
					<span class="input-group-addon w130">Вид группировки:</span>
					<div class="w328 maxw328" id="group_report" name="group_report"></div>
					<span class="input-group-addon w32"></span>
				</div>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 borderColor borderTop1 frameL center border0" id="tab_report">
			<div id='report_param_str' class="mt10 TAL font14"></div>
			<div id='div_report' class='center frame0 mt10'></div>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
<div id="dialog_progress" title="Ожидайте!">
	<img class="ml30 mt20 border0 w300" src="../../img/progress_circle5.gif">
</div>
