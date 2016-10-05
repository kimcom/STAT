<script type="text/javascript" src="/js/bootstrap3-typeahead.js"></script>
<script type="text/javascript" src="/js/ui.multiselect.js"></script>
<script type="text/javascript" src="/js/jquery.jqGrid.setColWidth.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="/css/ui.multiselect.css" />
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid-bootstrap-ui.css">
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid-bootstrap.css">
<link rel="stylesheet" type="text/css" href="/css/jqgrid/ui.jqgrid.alik.css">
<script type="text/javascript">
$(document).ready(function () {
	var search_global = true;
	var cats = [-1];
	$.jgrid.styleUI.Bootstrap.base.rowTable = "table table-bordered table-striped";
	$("#dialog").dialog({autoOpen: false, modal: true, width: 400, //height: 300,
		buttons: [{text: "Закрыть", click: function () { $(this).dialog("close");}}],
		show: {effect: "clip", duration: 500},
		hide: {effect: "clip", duration: 500}
    });
	$("#question").dialog({autoOpen: false, modal: true, width: 285,
		show: { effect: "blind",   duration: 500 },
		hide: { effect: "explode", duration: 500}
    });
	treeclick = function (id, el) {
		if ( el.checked) cats[cats.length]=id;
		if (!el.checked) cats.splice(cats.indexOf(id),1);
		//console.log(new Date(), id, el.checked, cats, cats.join(';'));
		if (!search_global) {
			newurl = "/engine/jqgrid3?action=good_list_doc&sid=5&group_search=" + cats.join(';') + "&catalog_oper=<?php echo $_REQUEST['oper']; ?>&f1=Article&f2=Name&f3=Brand&f4=Unit_in_pack&f5=Qty";
			//console.log(new Date(), newurl);
			$("#grid1").jqGrid('setGridParam', {url: newurl, page: 1, width: 800});
			$("#grid1").trigger('reloadGrid');
		}
	}
	formatFld = function (cellValue, options, rowObject) {
		//console.log(cellValue, options, rowObject);
		if (search_global) return cellValue;
		//var html = '<input type="checkbox" class="checkbox-inline" onclick="treeclick('+options.rowId+',this);"><span class="cell-wrapper ml5" style="cursor: pointer;">'+cellValue+'</span>';
		var html = '<input type="checkbox" class="checkbox-inline cell-wrapper" style="cursor: pointer;" onchange="treeclick('+options.rowId+',this);"><span class="cell-wrapper ml5" style="cursor: pointer;">'+cellValue+'</span>';
		return html;
    }
	$("#treegrid").jqGrid({
		styleUI : 'Bootstrap',
		treeGrid: true,
		treeGridModel: 'nested',
		treedatatype: 'json',
		datatype: "json",
		mtype: "POST",
		height: 540,
		ExpandColumn : 'name',
		ExpandColClick: true,
		//multiselect: true,
		url: '/engine/tree_NS?nodeid=0',
		colNames:["id","Каталог"],
		colModel:[
			{name:'id',index: 'id', width: 1, hidden: true, key: true},
		    {name: 'name', index: 'name', width: 240, resizable: false, editable: true, sorttype: "text", edittype: 'text', stype: "text", search: true, formatter: formatFld }
		],
		sortname: "Name",
		sortorder: "asc",
		caption: "Категории товаров",
		toppager: false,
		onSelectRow: function (cat_id, status, e) {
			//console.log(cat_id, status, e);
			if (cat_id == null) cat_id = 0;
			row = $("#treegrid").jqGrid('getLocalRow',cat_id);
			if (search_global) {
				if (row.rgt - row.lft!=1) {$('#grid1').jqGrid('clearGridData');return;}
				newurl = "/engine/jqgrid3?action=good_list_doc&sid=5&group=" + cat_id + "&catalog_oper=<?php echo $_REQUEST['oper']; ?>&f1=Article&f2=Name&f3=Brand&f4=Unit_in_pack&f5=Qty";
				$("#grid1").jqGrid('setGridParam', {url: newurl, page: 1, width: 800});
				$("#grid1").jqGrid('setCaption', 'Список товаров из категории: '+row.name);
				$("#grid1").trigger('reloadGrid');
			}
		}
    });
	$("#treegrid_name").remove();
	$('#gbox_treegrid .ui-jqgrid-caption').addClass('btn-success');

	good_edit = function (el,goodid,val){
		//console.log(el,goodid,val);
		$.post('/engine/doc_edit',{action:'<?php echo $_REQUEST['oper']; ?>_edit', operid:<?php echo $_REQUEST['operid'];?>, goodid:goodid, qty:val}, function (json) {
//			console.log(json);
//			console.log(JSON.stringify(json.row));
			if (json.success) {
				$(el).val(val==0?'':val);
				$.post('/engine/doc_info',{action: '<?php echo $_REQUEST['oper']; ?>_info', operid:<?php echo $_REQUEST['operid']; ?>}, function (json) {
					if (json.success) {
						$("#div_doc").html(json.html);
						$('#div_doc #table_doc .btn-warning').append($('#div_open_doc').clone());
						$('#div_doc #table_doc .btn-warning #btn_open_doc').click(btn_open_doc_click);
					}
				});
			}else{
				$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html(json.message);
				$("#dialog").dialog("open");
			}
		});
	}
	formatQty = function (cellValue, options, rowObject) {
		//console.log(cellValue, options, rowObject);
		var html = '<input type="number" class="TAC editable inline-edit-cell" style="line-height:17px;width:60%;" value="'+rowObject[4]+'" onkeypress="if(event.keyCode==13)$(this).emulateTab();" onchange="good_edit(this,'+options.rowId+',$(this).val());">' + 
				   '<span class="ml5 mr5 glyphicon glyphicon-remove" onclick="good_edit($(this).prev(),'+options.rowId+',0);"></span>';
		return html;
    }

	$("#grid1").jqGrid({
		styleUI : 'Bootstrap',
		caption: "Список товаров",
		mtype: "GET",
		url: "/engine/jqgrid3?action=good_list_doc&sid=5&group=-1&catalog_oper=<?php echo $_REQUEST['oper']; ?>&f1=Article&f2=Name&f3=Brand&f4=Unit_in_pack&f5=Qty",
		responsive: true,
		scroll: true, 
		height: 462, // если виртуальная подгрузка страниц
		//width:'550',
		//height: 'auto', //если надо управлять страницами
		//multiSort: true,
		datatype: "json",
//		colModel: colm,
	    colModel: [
			{label:'Артикул',		name:'Article',		index:'Article',	width: 120, sorttype: "text",	search: true,
				searchoptions: { 
					dataInit: function (element) {
					    $(element).attr("autocomplete", "off").typeahead({
						autoSelect: false, items: '20', minLength: 3, appendTo: "body",
						source: function (query, proxy) {
						    $.ajax({url: '/engine/select_search?action=good_article', dataType: "json", data: {name: query}, success: proxy});
						}
					    });
					}
			    }
			},
			{label:'Название',		name:'Name',		index:'Name',		width: 250, sorttype: "text",	search: true, hidedlg: false,
				searchoptions: { 
					dataInit: function (element) {
						$(element).attr("autocomplete","off").typeahead({ 
							autoSelect: false, items:'20', minLength:3,	appendTo : "body",
							source: function(query, proxy) {
									$.ajax({ url: '/engine/select_search?action=good_name', dataType: "json", data: {name: query}, success : proxy });
								}
							});
					}
				}
			},
			{label:'Бренд',		name:'Brand',		index:'Brand',		width: 130, sorttype: "text", align: "center", 	search: true, hidedlg: false,
				searchoptions: { 
					dataInit: function (element) {
						$(element).attr("autocomplete","off").typeahead({ 
							autoSelect: false, items:'20', minLength:3,	appendTo : "body",
							source: function(query, proxy) {
									$.ajax({ url: '/engine/select_search?action=brand_name', dataType: "json", data: {name: query}, success : proxy });
								}
							});
					}
				}
			},
//			{label:'Цена ОПТ',		name:'PriceBase',	index:'PriceBase',	width: 50, sorttype: "number", search: false, align: "right"},
//			{label:'Цена',			name:'Price',		index:'Price',		width: 50, sorttype: "number", search: false, align: "right"},
//			{label:'РРЦ',			name:'PriceRR',		index:'PriceRR',	width: 50, sorttype: "number", search: false, align: "right"},
			{label:'В уп.',			name:'Unit_in_pack',index:'Unit_in_pack',width:50, sorttype: "number", search: false, align: "center"},
//			{label:'Харьков',		name:'FreeBalance', index:'FreeBalance',width: 50, sorttype: "number", search: true, align: "center",
//				stype:'integer',
//				searchoptions: { clearSearch:false,
//					dataInit: function (element) {
//						p = $(this).jqGrid('getGridParam', 'postData');
//					    p.FreeBalance = true;
//						$(element).prop("checked",true);
//						$(element).css('margin-left','15px');
//						$(element).width("18px");
//						$(element).attr("type", "checkbox");
//						$(element).change(function(e){ $("#grid1").trigger('triggerToolbar'); });
//					}
//			    }
//			},
//			{label:'Киев',		name:'FreeBalance23', index:'FreeBalance23',width: 50, sorttype: "number", search: true, align: "center",
//				stype:'integer',
//				searchoptions: { clearSearch:false,
//					dataInit: function (element) {
//						$(element).css('margin-left','15px');
//						$(element).width("18px");
//						$(element).attr("type", "checkbox");
//						$(element).change(function(e){ $("#grid1").trigger('triggerToolbar'); });
//					}
//			    }
//			},
			{label:'Заказ',			name:'Qty',			index:'Qty',		width: 90, sorttype: "number", search: false, align: "center", //hidedlg: true,
				formatter: formatQty
			},
		],
		rowNum: 20,
		rowList: [20, 30, 40, 50, 100, 200, 300],
	    sortname: "Name",
	    viewrecords: true,
		toppager: true,
		gridview : true,
	    pager: "#pgrid1", 
		pagerpos: "left",
		//altclass:"ui-priority-secondary2",
		//altRows:true,
	});
	$('#gbox_grid1 .ui-jqgrid-caption').addClass('btn-info');
	$("#grid1").jqGrid('navGrid','#pgrid1', {edit:false, add:false, del:false, search:false, refresh: false, cloneToTop: true});
	$("#grid1").jqGrid('filterToolbar', { autosearch: true, searchOnEnter: true,
		beforeSearch: function () {
			p = $(this).jqGrid('getGridParam', 'postData');
			var catid = $('#treegrid').jqGrid('getGridParam','selrow');
			if (!search_global) {
				if (catid==null) {
					$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
					$("#dialog>#text").html('Вы настроили поиск с учетом категории товара<br><br>Укажите в какой категории произвести поиск!');
					$("#dialog").dialog("open");
					return true;
				} else {
					p.group_search = cats.join(';');
				}
			}
			if($("#gs_FreeBalance").prop("checked")) p.FreeBalance = true;
			if($("#gs_FreeBalance23").prop("checked")) p.FreeBalance23 = true;
		},
		afterSearch: function () {
			p = $(this).jqGrid('getGridParam', 'postData');
			flt = '';
			if (p.Article) flt += ' Артикул: '+p.Article;
			if (p.Name) flt += ' Название: '+p.Name;
			$("#grid1").jqGrid('setCaption', 'Список найденных товаров.'+flt);
		}
	});
	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');

	$("#treegrid").gridResize();
    $("#grid1").gridResize();
	
	$('#btn_setting').click(function (){
		$('#grid1').jqGrid('columnChooser',{caption:'Настройки каталога',modal:true,
			done: function (remapColumns){
				if (remapColumns) this.jqGrid("remapColumns", remapColumns, true);
				config_save();
			}
		});
		div = $('[aria-describedby=colchooser_grid1]');
		div_header = $('[aria-describedby=colchooser_grid1]').find('.ui-dialog-titlebar');
		//$(div_header).addClass('btn-yellow');
		//$(div_header).addClass('btn-primary');
		//$(div_header).addClass('btn-blue');
		$(div_header).addClass('btn-orange');
		div_setting = $('#div_setting').clone().removeClass('hide');
		$(div_setting).attr('id','div_setting_open');
		$(div_header).after($(div_setting));
		$(div).find('#help1').popover({title:'Сохранение настроек', trigger:'hover', delay: { show: 200, hide: 200 }, html:true});
		$(div).find('#help2').popover({title:'Настройка поиска товаров', trigger:'hover', delay: { show: 200, hide: 200 }, html:true});
		btn = $(div).find('.ui-dialog-buttonset > button')[0];
		$(btn).removeClass('ui-state-default').addClass('btn btn-success');
		$(btn).prepend('<span class="glyphicon glyphicon-ok m5 pull-left"></span>');
		$(btn).parent().prepend('<button onclick="config_reset();" type="button" class="ui-button ui-widget ui-corner-all ui-button-text-only btn btn-yellow 0btn-xs minw150 mb5" title="Восстановить настройки по умолчанию"><span class="glyphicon glyphicon-edit m5 pull-left"></span><span class="ui-button-text">Сбросить все настройки</span></button>');
		toogle_search(false);
//		console.log($(div).find('.popover'));
	});	

	btn_open_doc_click = function (e){
		window.location.href = window.location.origin + '/documents/<?php echo $_REQUEST['oper'];?>_list?operid=<?php echo $_REQUEST['operid']; ?>'
	}
	
	$.post('/engine/doc_info',{action: '<?php echo $_REQUEST['oper']; ?>_info', operid:<?php echo $_REQUEST['operid']; ?>}, function (json) {
		//console.log(json);
		if (json.success) {
			$("#div_doc").html(json.html);
			$('#div_doc #table_doc .btn-warning').append($('#div_open_doc').clone());
			$('#div_doc #table_doc .btn-warning #btn_open_doc').click(btn_open_doc_click);
		}
	});

	$('#gbox_grid1 .ui-jqgrid-caption').prepend($('#btns'));

	toogle_search = function (toogle){
		div_setting_open = $('#div_setting_open').find('#help2');
		if (toogle) search_global = !search_global;
		if (search_global) {
			$(div_setting_open).html('<span id="help2_icon" class="glyphicon glyphicon-check"></span> Вы используете глобальный поиск');
		} else {
			$(div_setting_open).html('<span id="help2_icon" class="glyphicon glyphicon-unchecked"></span> Вы используете поиск по категориям');
		}
		//console.log(search_global);
		$('#treegrid').trigger('reloadGrid');
	}

	$.post('/engine/config',{action: 'get', section: 'catalog'}, function (json) {
//		console.log(json,json.setting.length);
		if (json.success) {
			//console.log(json.setting);
			for (key in json.setting) {
				obj = json.setting[key].Object;
				param = json.setting[key].Param;
				value = json.setting[key].Value;
				if (param=='search' && obj=='global') {
					search_global = (value === 'true');
					if (!search_global) $('#treegrid').trigger('reloadGrid');
				}
				if (param=='param') {
					cm = JSON.parse(value);
					//console.log(cm);
					for (field in cm) { //for (fld in cm[key]) {
						//console.log(field, cm[field]);
						if (field=='height') $('#'+obj).jqGrid('setGridHeight', parseInt(cm[field]));
						if (field=='width') $('#'+obj).jqGrid('setGridWidth', parseInt(cm[field]));
						if (field=='remapColumns' && cm[field].length > 0) $('#'+obj).jqGrid("remapColumns", cm[field], true);
					}
				}
				if (param=='colModel') {
//console.log(obj,method,param,value);
					cm = JSON.parse(value);
					for (key in cm) { for (fld in cm[key]) {
						//console.log(key, fld, cm[key][fld],cm[key]['name']);
						if (fld == 'width')		$('#' + obj).jqGrid('setColWidth', cm[key]['name'], parseInt(cm[key][fld]));
						if (fld == 'hidden')	$('#' + obj).jqGrid((cm[key][fld]) ? 'hideCol' : 'showCol', cm[key]['name']);
					}}
				}
			}
		}
	});
//	setTimeout(function(){
//	}, 300);
	
	config_reset = function (){
		conf = new Object();
		conf.action = 'reset'; conf.section = 'catalog'; 
		//conf.object = 'treegrid'; conf.param = 'param'; conf.value = JSON.stringify(cm);
		$("#question>#text").html("Восстановить настройки по умолчанию?");
		$("#question").dialog('option', 'buttons', [{text: "Удалить", click: function () {
			$.post('/engine/config',{ action: conf.action, section:conf.section, object: conf.object, param: conf.param, value:	conf.value}, function (json) {
				if(!json.success){
					$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
					$("#dialog>#text").html(json.message);
					$("#dialog").dialog("open");
				}else{
					window.location.href = window.location.href;
				}
			});
		}}, {text: "Отмена", click: function () { $(this).dialog("close");
		}}]);
		$("#question").dialog('open');
	}
	config_save = function (){
		conf = new Object();
		conf.action = 'set'; conf.section = 'catalog'; 
		//получаем search_global
		conf.object = 'global'; conf.param = 'search'; conf.value = ''+search_global+'';
		$.post('/engine/config',{ action: conf.action, section:conf.section, object: conf.object, param: conf.param, value:	conf.value}, function (json) {
			if (!json.success) {
			    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
			    $("#dialog>#text").html(json.message);
			    $("#dialog").dialog("open");
			}
	    });
		//получаем конфигурацию treegrid
		var cm = $('#treegrid').jqGrid('getGridParam');
		cm = JSON.parse(JSON.stringify(cm));
		fld = ['width','height'];
		for(field in cm){ if (fld.indexOf(field)<0) delete cm[field]; }
		conf.object = 'treegrid'; conf.param = 'param'; conf.value = JSON.stringify(cm);
		$.post('/engine/config',{ action: conf.action, section:conf.section, object: conf.object, param: conf.param, value:	conf.value}, function (json) {
			if(!json.success){
			    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html(json.message);
			    $("#dialog").dialog("open");
			}
	    });
		//получаем конфигурацию grid1
		var cm = $('#grid1').jqGrid('getGridParam');
		cm = JSON.parse(JSON.stringify(cm));
		fld = ['width','height','remapColumns'];
		for(field in cm){ if (fld.indexOf(field)<0) delete cm[field]; }
		conf.object = 'grid1'; conf.param = 'param'; conf.value = JSON.stringify(cm);
		$.post('/engine/config',{ action: conf.action, section:conf.section, object: conf.object, param: conf.param, value: conf.value}, function (json) {
			if (!json.success) {
			    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
			    $("#dialog>#text").html(json.message);
			    $("#dialog").dialog("open");
			}
	    });
		//получаем конфигурацию колонок grid1
		var cm = $('#grid1').jqGrid('getGridParam','colModel');
		cm = JSON.parse(JSON.stringify(cm));
		fld = ['name','hidden','width'];
		for(key in cm){ for(field in cm[key]){ if (fld.indexOf(field)<0) delete cm[key][field]; }}
		conf.object = 'grid1'; conf.param = 'colModel'; conf.value = JSON.stringify(cm);
		$.post('/engine/config',{ action: conf.action, section:conf.section, object: conf.object, param: conf.param, value: conf.value}, function (json) {
			if (!json.success) {
			    $("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
			    $("#dialog>#text").html(json.message);
			    $("#dialog").dialog("open");
			}
	    });
	}
	//config_save();
});
</script>
<div class="container-fluid min500">
	<div class="row">
		<div id="info" class="col-md-12">
		</div>
	</div>
	<div class="panel pull-left m0 ml5">
		<table id="treegrid"></table>
		<div id="ptreegrid"></div>
	</div>
	<div class="panel pull-left m0 ml5">
		<table id="grid1"></table>
		<div id="pgrid1"></div>
	</div>
	<div class="panel panel-default pull-left m0 ml5" id="div_doc" ></div>
</div>
<div class="hide">
	<div id="btns" class="pull-right mr30 hidden-print">
		<button id="btn_setting" type="button" class="btn btn-warning btn-xs pull-left mr5"><span class="glyphicon glyphicon-list-alt	mr5"></span>Настройки</button>
	</div>
</div>
<div class="hide">
	<div id="div_open_doc" class="pull-right mr30 hidden-print">
		<button id="btn_open_doc" type="button" class="btn btn-info btn-xs pull-left mr5"><span class="glyphicon glyphicon-list-alt	mr5"></span>Открыть документ</button>
	</div>
</div>
<div id="div_setting" class="hide" style="z-index: 1;">
	<div class="row mt5">
	<div class="col-md-12 pl30 mt5">
		<button id="help1" type="button" class="btn btn-info btn-xs w430" data-container="body" data-toggle="popover" data-placement="right" data-content="
				<ul class='list-unstyled font12 m0 mt5'>
					<li class='m0'>При входе в каталог будут восстановлены:
						<ul>
							<li>Высота и ширина `Категорий товаров`</li>
							<li>Высота и ширина `Списка товаров`</li>
							<li>Настройки видимости колонок в `Списке товаров`</li>
							<li>Настройки ширины колонок в `Списке товаров`</li>
							<li>Настройка поиска в `Списке товаров`</li>
						</ul>
					</li>
				</ul>">
			<span class="glyphicon glyphicon-save-file"></span> Ваши настройки для каталога будут сохранены! 
		</button>
		<h4 class="font13 fontb mb2 text-success">Настройка поиска в списке товаров:</h4>
		<button id="help2" state="" type="button" onclick="toogle_search(true);" class="btn btn-success btn-xs w430" data-container="body" data-toggle="popover" data-placement="right" data-content="
			<ul class='list-unstyled font12 m0 mt5'>
				<li class='m0'>Данная настройка влияет на поиск товаров:
					<ul>
						<li>Если установлен глобальный поиск:
							при наборе артикула или наименования товара
							- поиск выполняется по всем товарам.</li>
						<li>Если установлен поиск по категории:
							при наборе артикула или наименования товара
							- поиск выполняется только по выбранной категории.</li>
					</ul>
				</li>
			</ul>">
		</button>
		<h4 class="font13 fontb mb0 text-primary">Вы можете выбрать нужные Вам колонки для списка товаров:</h4>
	</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
<div id="question" title="ВНИМАНИЕ!">
	<p id='text' class="center"></p>
</div>
