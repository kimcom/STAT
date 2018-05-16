<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid-bootstrap-ui.css">
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid-bootstrap.css">
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid.alik.css">
<script type="text/javascript">
$(document).ready(function () {
	$("#dialog").dialog({autoOpen: false, modal: true, width: 400, //height: 300,
		buttons: [{text: "Закрыть", click: function () { $(this).dialog("close");}}],
		show: {effect: "clip", duration: 500},
		hide: {effect: "clip", duration: 500}
    });
	$("#dialog_progress").dialog({
		autoOpen: false, modal: true, width: 400, height: 400,
		show: {effect: "explode",duration: 1000},
		hide: {effect: "explode",duration: 1000}
    });
	$("#view").dialog({autoOpen: false, modal: true, width: 'auto', height: 600,
		show: { effect: "blind", duration: 800 },
		hide: { effect: "blind", duration: 800 }
	});
	$("#question").dialog({autoOpen: false, modal: true, width: 285,
		show: { effect: "blind",   duration: 500 },
		hide: { effect: "explode", duration: 500 }
	});

	$.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";
	$("#grid1").jqGrid({
		caption: "Список документов",
		mtype: "GET",
		styleUI: 'Bootstrap',
		url: "/engine/jqgrid3?action=order_list&grouping=CheckID&CheckStatus=0&f1=CheckID&f2=State&f3=CreateDateTime&f4=CloseDateTime&f5=OrderType&f6=CountGood&f7=Sum&f8=ClientName&f9=Notes&f10=Author",
		//responsive: true,
		scroll: 1, height: 360, // если виртуальная подгрузка страниц
		//height: 'auto', //если надо управлять страницами
		//multiSort: true,
		datatype: "json",
		colModel: [
		    {label: '№ документа', name: 'o_CheckID', index: 'o.CheckID', width: 120, sorttype: "number", search: true, align: "center"},
		    {label: 'Состояние', name: 'State', index: 'State', width: 120, sorttype: "text", search: false, align: "center"},
		    {label: 'Дата создания', name: 'CreateDateTime', index: 'CreateDateTime', width: 120, sorttype: "date", search: true, align: "center"},
		    {label: 'Дата', name: 'CloseDateTime', index: 'CloseDateTime', width: 120, sorttype: "date", search: true, align: "center"},
		    {label: 'Тип', name: 'OrderType', index: 'OrderType', width: 50, sorttype: "text", search: true, align: "center", stype:"select", searchoptions: {value: ":любой;1:авто;0:ручной" }},
		    {label: 'Строк', name: 'CountGood', index: 'CountGood', width: 60, sorttype: "number", search: false, align: "right"},
		    {label: 'Сумма', name: 'Sum', index: 'Sum', width: 60, sorttype: "number", search: false, align: "right"},
		    {label: 'Магазин', name: 'ClientName', index: 'cl.NameShort', width: 150, sorttype: "text", search: true, align: "left"},
		    {label: 'Примечание', name: 'Notes', index: 'Notes', width: 200, sorttype: "text", search: true, align: "left"},
		    {label: 'Автор', name: 'Author', index: 'u.UserName', width: 130, sorttype: "text", search: true, align: "left"},
		],
		rowNum: 20,
		rowList: [20, 30, 40, 50, 100, 200, 300],
		sortname: "o.CheckID",
		sortorder: 'desc',
		viewrecords: true,
//		autowidth:false,
		shrinkToFit: true,
//		forceFit:true,
		toppager: true,
		gridview: true,
		pager: "#pgrid1",
		pagerpos: "left",
//		altclass:"ui-priority-secondary2",
//		altRows:true,
//		onSelectRow: function (id, status, e) {
//			console.log(id, status, e);
//		}
		ondblClickRow: function(rowid) {
			$("#div_doc_list #doc_edit:first").click();
		}
    });
	$("#grid1").jqGrid('navGrid', '#pgrid1', {edit: false, add: false, del: false, search: false, refresh: false, cloneToTop: true});
	$("#grid1").jqGrid('filterToolbar', { autosearch: true, searchOnEnter: true});
	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');

	$('#myTab a').click(function (e) {
		e.preventDefault();
		//console.log($(this).attr('href'),$(this).attr('state'));
		if ($(this).attr('state')) {
			$("#divGrid").appendTo( $($(this).attr('href')));
			$("#divGrid").removeClass("hide");
			if($(this).attr('state')=='all') {
				$("#grid1").jqGrid('setGridParam', {datatype: "json", url: "/engine/jqgrid3?action=order_list&grouping=CheckID&f1=CheckID&f2=State&f3=CreateDateTime&f4=CloseDateTime&f5=OrderType&f6=CountGood&f7=Sum&f8=ClientName&f9=Notes&f10=Author", page: 1});
			}else{
				$("#grid1").jqGrid('setGridParam', {datatype: "json", url: "/engine/jqgrid3?action=order_list&grouping=CheckID&CheckStatus="+$(this).attr('state')+"&f1=CheckID&f2=State&f3=CreateDateTime&f4=CloseDateTime&f5=OrderType&f6=CountGood&f7=Sum&f8=ClientName&f9=Notes&f10=Author", page: 1});
			}
			$("#grid1").trigger('reloadGrid');
		}
		$(this).tab('show');
	});
	
	$("#div_doc_list button").click(function(e){
		id = e.target.id;
		//console.log(id);
		if (id == 'doc_edit') {
			var id = $("#grid1").jqGrid('getGridParam', 'selrow');
			//console.log(id);
			if (id == null) {
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html('Пожалуйста, выберите документ в списке!');
				$("#dialog").dialog("open");
				return false;
			}
			$.post('/engine/doc_edit', {action: 'order_setcurrent',docid:id}, function (json) {
			//console.log(json);
			    if (!json.success) {
					$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
					$("#dialog>#text").html(json.message);
					$("#dialog").dialog("open");
			    } else {
					//window.location.href = window.location.href;
					doc_info();
					$('#a_tab_0').click();
			    }
			});
		}
		if (id == 'doc_view')	{
			var id = $("#grid1").jqGrid('getGridParam', 'selrow');
			if (id == null) {
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html('Пожалуйста, выберите документ в списке!');
				$("#dialog").dialog("open");
				return false;
		    }
			$.post('/engine/doc_info_full',{action: 'order_info', docid: id, view: true}, function (json) {
				if (json.success){
					$("#view").html(json.html);
					$("#view").dialog({title:'Просмотр информации о документе №'+id});
					$("#view").dialog("open");
					$('#button_report_export').click(function (e) {
						$("#dialog_progress").dialog( "option", "title", 'Ожидайте! Готовим данные для XLS файла');
						$("#dialog_progress").dialog("open");
						setTimeout(function () {
							var file_name = 'Просмотр информации о документе №'+id;
							var html = file_name + "<br>" + json.html;
							html = html.split("<table").join("<table border='1' ");
							var report_name = 'order';
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
				}else{
				    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
					$("#dialog>#text").html(json.message);
					$("#dialog").dialog("open");
				}
			});
		}
		if (id == 'doc_delete') {
			var id = $("#grid1").jqGrid('getGridParam', 'selrow');
			if (id == null) {
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html('Пожалуйста, выберите документ в списке!');
				$("#dialog").dialog("open");
				return false;
		    }
			$("#question>#text").html("После удаления<br>документ восстановить невозможно!<br><br>Удалить документ № " + id + "?");
			$("#question").dialog('option', 'buttons', [{text: "Удалить", click: doc_delete_from_list}, {text: "Отмена", click: function () { $(this).dialog("close"); }}]);
			$("#question").dialog('open');
		}
		if (id == 'doc_add') {
		    $.post('/engine/doc_edit', {action: 'order_new'}, function (json) {
				//console.log(json);
				if (!json.success) {
				    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				    $("#dialog>#text").html(json.message);
				    $("#dialog").dialog("open");
				} else {
				    //window.location.href = window.location.href;
					doc_info();
					$('#a_tab_0').click();
				}
		    });
		}
	});
	doc_info = function () {
		$.post('/engine/doc_info_full',{action: 'order_info'}, function (json) {
			//console.log(json);
			if (json.success){
				clientid = json.clientid;
				ordertype = json.ordertype;
				partnerid = json.partnerid;
				$("#div_doc_active").html(json.html);
				$.post('/engine/select2?action=point', function (json) {
					$("#select_companyID").select2({enable: false, multiple: false, placeholder: "Укажите торговую точку", data: {results: json, text: 'text'}});
					$("#select_companyID").on("change", function (e) {
					    if (e.val.length > 0)
						good_edit('order_edit_client', null, 0, 0, 0, 0, 0, 0, e.val);
					});
					$("#select_companyID").select2("val", clientid);
					//$("#select_companyID").select2("enable", mode_manager);
			    });
				$("#select_ordertype").select2({enable: false, multiple: false, placeholder: "Укажите тип заказа", data: [{id: 0, text: 'ручной'}, {id: 1, text: 'автоматический'}]});
				$("#select_ordertype").on("change", function (e) {
					if (e.val.length > 0)
					good_edit('order_edit_type', null, 0, 0, 0, 0, 0, 0, 0, e.val);
				});
				$("#select_ordertype").select2("val", ordertype);
				$("#div_doc_buttons button").click(function(e){
					id = e.target.id;
					//console.log(id, e.target);
					if (id == 'state') {
						$("#question>#text").html("После проведения<br>редактировать документ невозможно!<br><br>Провести документ № " + $('#docid').val() + "?");
						$("#question").dialog('option', 'buttons', [{text: "Отправить", click: doc_send}, {text: "Отмена", click: function () {$(this).dialog("close");}}]);
						$("#question").dialog('open');
					}
					if (id == 'print') {
						$('#table_doc input').addClass("hidden");
						$('#table_doc #qty').removeClass("hidden");
						$("#tab_doc_action").removeClass("border1");
						$("#tab_doc_action").addClass("border0");
						window.print();
						$('#table_doc input').removeClass("hidden");
						$('#table_doc #qty').addClass("hidden");
						$("#tab_doc_action").removeClass("border0");
						$("#tab_doc_action").addClass("border1");
					}
					if (id == 'delete') {
						$("#question>#text").html("После удаления<br>документ восстановить невозможно!<br><br>Удалить документ № "+$('#docid').val()+"?");
						$("#question").dialog('option', 'buttons', [{text: "Удалить", click: doc_delete},{text: "Отмена", click: function () {$(this).dialog("close");}}]);
						$("#question").dialog('open');
					}
					if (id == 'good_add') {window.location.href='/main/catalog?oper=order&operid=1';}
					if (id == 'doc_add') {$("#div_doc_list #doc_add:first").click();}
				});
			}
		});
	}
	doc_send = function (e) {
		$(this).dialog("close");
		$.post('/engine/doc_edit', {action: 'order_send'}, function (json) {
			//console.log(json);
			if (!json.success) {
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html(json.message);
				$("#dialog").dialog("open");
			} else {
				//window.location.href = window.location.href;
				doc_info();
				$('#a_tab_0').click();
			}
		});
	}
	doc_delete = function (e) {
		$(this).dialog("close");
		$.post('/engine/doc_edit', {action: 'order_delete'}, function (json) {
			//console.log(json);
			if (!json.success) {
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html(json.message);
				$("#dialog").dialog("open");
			} else {
				//window.location.href = window.location.href;
				doc_info();
				$('#a_tab_0').click();
			}
		});
	}
	doc_delete_from_list = function (e) {
		$(this).dialog("close");
		var id = $("#grid1").jqGrid('getGridParam', 'selrow');
		if (id == null) return false;
		$.post('/engine/doc_edit', {action: 'order_delete', docid: id}, function (json) {
			//console.log(json);
			if (!json.success) {
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html(json.message);
				$("#dialog").dialog("open");
			} else {
				if ($('#docid').val()==id) {
					//window.location.href = window.location.href;
					doc_info();
					$('#a_tab_0').click();
				}
				$("#grid1").trigger('reloadGrid');
			}
		});
	}
	
	good_edit = function (action, el, goodid, size, qty, info, delivery, notes, newclientid, ordertype) {
		//console.log(action, el, goodid, size, qty, info);
		$.post('/engine/doc_edit', {action: action, goodid: goodid, modifiers:size, qty: qty, info: info, delivery: delivery, notes: notes, clientid: newclientid, partnerid: ordertype}, function (json) {
			//console.log(JSON.stringify(json));
			if (!json.success){
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html(json.message);
				$("#dialog").dialog("open");
				return;
			}
		    if (json.success && action == 'order_edit' && qty == 0) {
				next = $(el).parent().parent().next();
				$(el).parent().parent().remove();
				$(next).focus();
		    }
		});
    }
	doc_info();

	$("#divGrid").appendTo($('#tab_doc_list1'));
	$("#divGrid").removeClass("hide");
	
	if (document.referrer.search('catalog')>0){
		setTimeout(function(){
			$("#a_tab_0").click();
		}, 100);
	}
});
</script>

<div class="container center min300">
<?php
//if ($_SESSION['ClientID']!=0) {
?>
	<ul id="myTab" class="nav nav-tabs floatL active hidden-print" role="tablist">
		<li>				<a id="a_tab_0" href="#tab_doc_action" role="tab" data-toggle="tab">Заказ товара</a></li>
		<li class="active">	<a id="a_tab_1" href="#tab_doc_list1"  state="0" role="tab" data-toggle="tab">Предварительные</a></li>
		<li>				<a href="#tab_doc_list2"	role="tab" state="10" data-toggle="tab">Отправленные</a></li>
		<li>				<a href="#tab_doc_list3"	role="tab" state="20" data-toggle="tab">Обработанные</a></li>
		<li>				<a href="#tab_doc_list4"	role="tab" state="all" data-toggle="tab">Все документы</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane m0 w100p min530 ui-corner-tab1 borderColor frameL border1" id="tab_doc_action">
			<div class="ml5 mt5" id="div_doc_active" accept="text/csv"></div>
		</div>
		<div class="tab-pane m0 w100p min530 ui-corner-all borderColor frameL border1 active" id="tab_doc_list1">
			<div class="ml5 mt5" id="div_doc_list" >
				<div class="row">
					<div class = "col-md-12 col-xs-12 TAL hidden-print">
						<button id="doc_add"		type="button" class="btn btn-lilac		btn-sm minw150 mb5"><span class="glyphicon glyphicon-plus		mr5"></span>Новый документ</button>
						<button id="doc_edit"		type="button" class="btn btn-success	btn-sm minw150 mb5"><span class="glyphicon glyphicon-edit		mr5"></span>Редактировать документ</button>
						<button id="doc_delete"		type="button" class="btn btn-danger		btn-sm minw150 mb5"><span class="glyphicon glyphicon-trash		mr5"></span>Удалить документ</button>
						<button id="doc_view"		type="button" class="btn btn-info		btn-sm minw150 mb5"><span class="glyphicon glyphicon-list-alt	mr5"></span>Просморт документа</button>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 ui-corner-all borderColor frameL border1" id="tab_doc_list2">
			<div class="ml5 mt5" id="div_doc_list" >
				<div class="row">
					<div class = "col-md-12 col-xs-12 TAL hidden-print">
						<button id="doc_view"		type="button" class="btn btn-info		btn-sm minw150 mb5"><span class="glyphicon glyphicon-list-alt	mr5"></span>Просморт документа</button>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 ui-corner-all borderColor frameL border1" id="tab_doc_list3">
			<div class="ml5 mt5" id="div_doc_list" >
				<div class="row">
					<div class = "col-md-12 col-xs-12 TAL hidden-print">
						<button id="doc_view"		type="button" class="btn btn-info		btn-sm minw150 mb5"><span class="glyphicon glyphicon-list-alt mr5"></span>Просморт документа</button>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 ui-corner-all borderColor frameL border1" id="tab_doc_list4">
			<div class="ml5 mt5" id="div_doc_list" >
				<div class="row">
					<div class = "col-md-12 col-xs-12 TAL hidden-print">
						<button id="doc_view"		type="button" class="btn btn-info		btn-sm minw150 mb5"><span class="glyphicon glyphicon-list-alt mr5"></span>Просморт документа</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
//}
?>
</div>
<div id="divGrid" class="panel pull-left ml5 mb0 hide">
	<table id="grid1"></table>
	<div id="pgrid1"></div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
<div id="dialog_progress" title="Ожидайте!">
	<img class="ml30 mt20 border0 w300" src="../../img/progress_circle5.gif">
</div>
<div id="view" title="ВНИМАНИЕ!">
</div>
<div id="question" title="ВНИМАНИЕ!">
	<p id='text' class="center"></p>
</div>
