<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid.css">
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid-bootstrap-ui.css">
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid-bootstrap.css">
<link rel="stylesheet" type="text/css" media="screen" href="/css/ui.multiselect.css" />
<script src="/js/jquery.jqGrid.setColWidth.js" type="text/javascript"></script>
<script src="/js/bootstrap3-typeahead.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/ui.multiselect.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var section = 'discount_list';
    var fs = false;
	$.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";
	$("#dialog").dialog({autoOpen: false, modal: true, width: 400, //height: 300,
		buttons: [{text: "Закрыть", click: function () {
			    $(this).dialog("close");
			}}],
		show: {effect: "clip", duration: 500},
		hide: {effect: "clip", duration: 500}
    });
    $("#question").dialog({autoOpen: false, modal: true, width: 285,
		show: {effect: "blind", duration: 500},
		hide: {effect: "explode", duration: 500}
    });
	$("#dialog_progress").dialog({
		autoOpen: false, modal: true, width: 400, height: 400,
		show: {effect: "explode",duration: 1000},
		hide: {effect: "explode",duration: 1000}
    });
	$("#grid1").jqGrid({
		styleUI : 'Bootstrap',
		caption: "Список дисконтных карт",
		mtype: "GET",
		url:"/engine/jqgrid3?action=discountCards_list&f1=CardID&f2=Family&f3=Name&f4=MiddleName&f5=Address&f6=Phone1&f7=Phone2&f8=EMail&f9=Magazine&f10=PercentOfDiscount&f11=AmountOfBuying&f12=DateOfIssue&f13=DateOfCancellation",
		responsive: true,
		height: 462, // если виртуальная подгрузка страниц
		scroll: true, // если виртуальная подгрузка страниц
//		height: 'auto', //если надо управлять страницами
		datatype: "local",
		colModel: [
			{label:'Карта',		name:'CardID',		index:'CardID',		width: 100, align:"center", sorttype:"text",  search:true},
			{label:'Фамилия',	name:'Family',      index:'Family',		width: 200, align:"left",	sorttype:"text",  search:true},
			{label:'Имя',		name:'Name',        index:'Name',       width: 100, align:"left",	sorttype:"text",  search:true},
			{label:'Отчество',	name:'MiddleName',	index:'MiddleName', width: 100, align:"left",	sorttype:"text",  search:true},
			{label:'Адрес',		name:'cs_Address',	index:'cs.Address',	width: 200, align:"left",	sorttype:"text",  search:true},
			{label:'Тел.',		name:'Phone1',		index:'Phone1',		width: 120, align:"left",	sorttype:"text",  search:true},
			{label:'Тел.доп.',	name:'Phone2',		index:'Phone2',		width: 90, align:"left",	sorttype:"text",  search:true},
			{label:'E-mail',	name:'EMail',		index:'EMail',		width: 90, align:"left",	sorttype:"text",  search:true},
			{label:'Магазин',	name:'NameShort',	index:'NameShort',width: 150, align:"left",	sorttype:"text",  search:true},
			{label:'%',			name:'PercentOfDiscount',	index:'PercentOfDiscount',	width:  50, align:"center", sorttype:"number", search:true},
			{label:'Накопл.',	name:'AmountOfBuying',		index:'AmountOfBuying',		width: 100, align:"right",	sorttype:"number", search:false, sortable: true},
			{label:'Выдана',	name:'DateOfIssue',			index:'DateOfIssue',        width: 120, align:"center", sorttype:"date",   search:true},
			{label:'Анулир.',	name:'DateOfCancellation',	index:'DateOfCancellation', width: 120, align:"center", sorttype:"date",   search:true},
		],
		rowNum: 17,
		rowList: [20, 30, 40, 50, 100, 200, 300],
		sortname: "CardID",
		viewrecords: true,
		toppager: true,
		gridview: true,
		pager: "#pgrid1",
		shrinkToFit: false,
		search: false,
		gridComplete: function() {if(!fs) {fs = true; filter_restore("#grid1");}},
		ondblClickRow: function(rowid) {
			if (rowid != '')
				window.location = "../goods/map_discountcard_edit?cardid=" + rowid;
	    },
    });
	$('#gbox_grid1 .ui-jqgrid-caption').addClass('btn-info');
	$("#grid1").jqGrid('navGrid', '#pgrid1', {edit: false, add: false, del: false, search: false, refresh: false, cloneToTop: true});
	$("#grid1").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true, beforeSearch: function(){filter_save("#grid1");}});
	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');
	$("#grid1").navButtonAdd('#grid1_toppager', {
		title: 'Открыть информационную карту', buttonicon: "glyphicon-pencil", caption: 'Открыть инф. карту', position: "last",
		onClickButton: function () {
		    var id = $("#grid1").jqGrid('getGridParam', 'selrow');
		    var node = $("#grid1").jqGrid('getRowData', id);
			//console.log(id,node,node.Name);
		    if (id != '')
				window.location = "../goods/map_discountcard_edit?cardid=" + id;
		}
    });
	$("#grid1").navButtonAdd("#grid1_toppager",{
		caption: 'Экспорт в XLS', 
		title: 'to XLS', 
		icon: "glyphicon-export",
		onClickButton: function () {
			$("#dialog_progress").dialog( "option", "title", 'Ожидайте! Готовим данные для XLS файла');
			$("#dialog_progress").dialog("open");
			setTimeout(function () {
				var gr = $("#gview_grid1").clone();
				$(gr).find("#pg_grid1_toppager").remove();
				$(gr).find(".ui-search-toolbar").remove();
				$(gr).find("#btns").remove();
				$(gr).find("#grid1_toppager").html($("#report_param_str").html());
				$(gr).find("th").filter(function () {
					if ($(this).css('display') == 'none')
					$(this).remove();
				});
				$(gr).find("td").filter(function () {
					if ($(this).css('display') == 'none')
					$(this).remove();
				});
				$(gr).find("table").filter(function () {
					if ($(this).attr('border') == '0')
					$(this).attr('border', '1');
				});
				$(gr).find("td").filter(function () {
					if ($(this).attr('colspan') > 1)
					$(this).attr('colspan', '6');
				});
				$(gr).find("a").remove();
				$(gr).find("div").removeAttr("id");
				$(gr).find("div").removeAttr("style");
				$(gr).find("div").removeAttr("class");
				$(gr).find("div").removeAttr("role");
				$(gr).find("div").removeAttr("dir");
				$(gr).find("span").removeAttr("class");
				$(gr).find("span").removeAttr("style");
				$(gr).find("span").removeAttr("sort");
				$(gr).find("table").removeAttr("id");
				$(gr).find("table").removeAttr("class");
				$(gr).find("table").removeAttr("role");
				$(gr).find("table").removeAttr("tabindex");
				$(gr).find("table").removeAttr("aria-labelledby");
				$(gr).find("table").removeAttr("aria-multiselectable");
				$(gr).find("th").removeAttr("id");
				$(gr).find("th").removeAttr("class");
				$(gr).find("th").removeAttr("role");
				$(gr).find("tr").removeAttr("id");
				$(gr).find("tr").removeAttr("class");
				$(gr).find("tr").removeAttr("role");
				$(gr).find("tr").removeAttr("tabindex");
				$(gr).find("td").removeAttr("id");
				$(gr).find("td").removeAttr("role");
				$(gr).find("td").removeAttr("title");
				$(gr).find("td").removeAttr("aria-describedby");
				$(gr).find("table").removeAttr("style");
				$(gr).find("th").removeAttr("style");
				$(gr).find("tr").removeAttr("style");
				$(gr).find("td").removeAttr("style");

				var html = $(gr).html();
				html = html.split(" грн.").join("");
				html = html.split("<table ").join("<table border='1' ");

				var file_name = 'Список дисконтных карт';
				var report_name = 'discount1';
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
		}
    });
	$('#gbox_grid1 .ui-jqgrid-caption > SPAN').append($('#btns'));
	
	$('#btn_setting').click(function (){
		$('#grid1').jqGrid('columnChooser',{caption:'Настройки каталога',modal:true,
			done: function (remapColumns){
				if (remapColumns) this.jqGrid("remapColumns", remapColumns, true);
				grid_size();
				config_save();
			}
		});
		div = $('[aria-describedby=colchooser_grid1]');
		div_header = $('[aria-describedby=colchooser_grid1]').find('.ui-dialog-titlebar');
		div_setting = $('#div_setting').clone().removeClass('hide');
		$(div_setting).attr('id', 'div_setting_open');
		$(div_header).after($(div_setting));
		$(div).find('#help1').popover({title: 'Сохранение настроек', trigger: 'hover', delay: {show: 200, hide: 200}, html: true});
		$(div).find('#help2').popover({title: 'Настройка поиска товаров', trigger: 'hover', delay: {show: 200, hide: 200}, html: true});
		btn = $(div).find('.ui-dialog-buttonset > button')[0];
		$(btn).removeClass('ui-state-default').addClass('btn btn-success');
		$(btn).prepend('<span class="glyphicon glyphicon-ok m5 pull-left"></span>');
		$(btn).parent().prepend('<button onclick="config_reset();" type="button" class="ui-button ui-widget ui-corner-all ui-button-text-only btn btn-warning 0btn-xs minw150 mb5" title="Восстановить настройки по умолчанию"><span class="glyphicon glyphicon-edit m5 pull-left"></span><span class="ui-button-text">Сбросить все настройки</span></button>');
    });
	
	config_reset = function (){
		filter_reset("#grid1");
		conf = new Object();
		conf.action = 'reset'; conf.section = section; 
		$("#question>#text").html("Восстановить настройки по умолчанию?");
		$("#question").dialog('option', 'buttons', [{text: "Удалить", click: function () {
			$.post('/engine/config',{ action: conf.action, section:conf.section, object: conf.object, param: conf.param, value: conf.value}, function (json) {
				if (json.success==false) {
					$("#question").dialog('close');
				    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				    $("#dialog>#text").html(json.message);
				    $("#dialog").dialog("open");
				} else {
				    window.location.href = window.location.href;
				}
			    });
			}}, {text: "Отмена", click: function () {
			    $(this).dialog("close");
		}}]);
		$("#question").dialog('open');
    }
    config_save = function () {
		conf = new Object();
		conf.action = 'set';
		conf.section = section;
		//получаем конфигурацию grid1
		var cm = $('#grid1').jqGrid('getGridParam');
		cm = JSON.parse(JSON.stringify(cm));
		fld = ['width', 'height', 'remapColumns'];
		for (field in cm) {
		    if (fld.indexOf(field) < 0) delete cm[field];
		}
		conf.object = 'grid1';
		conf.param = 'param';
		conf.value = JSON.stringify(cm);
		$.post('/engine/config', {action: conf.action, section: conf.section, object: conf.object, param: conf.param, value: conf.value}, function (json) {
		    if (!json.success) {
			$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
			$("#dialog>#text").html(json.message);
			$("#dialog").dialog("open");
		    }
		});
		//получаем конфигурацию колонок grid1
		var cm = $('#grid1').jqGrid('getGridParam', 'colModel');
		cm = JSON.parse(JSON.stringify(cm));
		fld = ['name', 'hidden', 'width'];
		for (key in cm) {
		    for (field in cm[key]) {
			if (fld.indexOf(field) < 0)
			    delete cm[key][field];
		    }
		}
		conf.object = 'grid1';
		conf.param = 'colModel';
		conf.value = JSON.stringify(cm);
		$.post('/engine/config', {action: conf.action, section: conf.section, object: conf.object, param: conf.param, value: conf.value}, function (json) {
		    if (!json.success) {
			$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
			$("#dialog>#text").html(json.message);
			$("#dialog").dialog("open");
		    }
		});
	}
	grid_size = function () {	
		var cm = $("#grid1").jqGrid('getGridParam','colModel');
		for (key in cm) {
			$('#grid1').jqGrid('setColWidth', cm[key]['name'], parseInt(cm[key]['width']), true);
			$('#grid1').jqGrid((cm[key]['hidden']) ? 'hideCol' : 'showCol', cm[key]['name']);
		}
		var gw = $("#grid1").jqGrid('getGridParam','width');
		$("#grid1").jqGrid('setGridWidth',gw+17)
	}
	$('#grid1').jqGrid('hideCol','Name');
	$('#grid1').jqGrid('hideCol','MiddleName');
	$('#grid1').jqGrid('hideCol','Phone2');
	$('#grid1').jqGrid('hideCol','Email');
	$('#grid1').jqGrid('hideCol','AmountOfBuying');
	$('#grid1').jqGrid('hideCol','DateOfCancellation');

//восстановление сохраненных настроек
	$.post('/engine/config',{action: 'get', section: section}, function (json) {
		//console.log(json,json.setting.length);
		if (json.success) {
			//console.log(json.setting);
			for (key in json.setting) {
				obj = json.setting[key].Object;
				param = json.setting[key].Param;
				value = json.setting[key].Value;
				if (param== 'param') {
					cm = JSON.parse(value);
					//console.log(cm);
					for (field in cm) { //for (fld in cm[key]) {
						//console.log(field, cm[field]);
						if (field == 'height')
						$('#' + obj).jqGrid('setGridHeight', parseInt(cm[field]));
//						if (field == 'width')
//						$('#' + obj).jqGrid('setGridWidth', parseInt(cm[field]));
						if (field == 'remapColumns' && cm[field].length > 0)
						$('#' + obj).jqGrid("remapColumns", cm[field], true);
					}
				}
				if (param == 'colModel') {
		//console.log(obj,method,param,value);
					cm = JSON.parse(value);
					for (key in cm) {
						for (fld in cm[key]) {
						//console.log(key, fld, cm[key][fld],cm[key]['name']);
						if (fld == 'width')	$('#' + obj).jqGrid('setColWidth', cm[key]['name'], parseInt(cm[key][fld]), true);
						if (fld == 'hidden')$('#' + obj).jqGrid((cm[key][fld]) ? 'hideCol' : 'showCol', cm[key]['name']);
						}
					}
				}
			}
			grid_size();
		}
    });

	$("#grid1").gridResize();
	grid_size();
});
</script>
<div class="container-fluid min500">
	<div class="panel pull-left m0 ml5">
		<table id="grid1"></table>
		<div id="pgrid1"></div>
	</div>
</div>
<div class="hide">
	<div id="btns" class="pull-right mr20 hidden-print" style="">
		<button id="btn_setting" type="button" class="btn btn-lilac btn-xs pull-left mr5 mt2"><span class="glyphicon glyphicon-list-alt	mr5"></span>Настройки</button>
	</div>
</div>
<div id="div_setting" class="hide">
	<div class="row mt5">
		<div class="col-md-12 pl30 mt5">
			<h4 class="font13 fontb mb0 text-primary">Вы можете выбрать нужные Вам колонки для списка:</h4>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
<div id="question" title="ВНИМАНИЕ!">
	<p id='text' class="center"></p>
</div>
<div id="dialog_progress" title="Ожидайте!">
	<img class="ml30 mt20 border0 w300" src="../../img/progress_circle5.gif">
</div>
