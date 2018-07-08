<script type="text/javascript">
$(document).ready(function () {
	var reportID = 42;
//Object Converter
	oconv	= function (a) {var o = {};for(var i=0;i<a.length;i++) {o[a[i]] = '';} return o;}
	strJoin = function (obj){ var ar = []; for (key in obj){ar[ar.length] = obj[key];}return ar;}
	keyJoin = function (obj){ var ar = []; for (key in obj){ar[ar.length] = key;}return ar;}
	clearObj= function (obj){ for(key in obj){for(k in obj[key]){delete obj[key][k];}}return obj;}
	var settings = new Object();
	var grouping = new Object();
	var group = new Object();
	var good = new Object();
	var cat = new Object();
	var cat_type = new Object();
	var markup = new Object();
	var point = new Object();
	var seller = new Object();
	var promo = new Object();
	var card = new Object();
	settings['grouping']=grouping;
	settings['group']=group;
	settings['good']=good;
	settings['cat']=cat;
	settings['cat_type']=cat_type;
	settings['markup']=markup;
	settings['point']=point;
	settings['seller']=seller;
	settings['promo']=promo;
	settings['card']=card;
	var colnames = ['Кол-во','Себест.', 'Оборот', 'Доход', '% наценки', '% скидки'];
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
	//dt.setMonth(dt.getMonth() - 1, 1);
	$("#DT_start").datepicker({
		//showOn: "both", 
		numberOfMonths: 1,
		showButtonPanel: true, 
		showWeek: true,
		dateFormat: 'dd/mm/yy',
		//showAnim: "fold"
	});
	$("#DT_start").datepicker("setDate", dt);
	dt = new Date();
	//dt.setDate(0);
    $("#DT_stop").datepicker({
		//showOn: "both", 
		numberOfMonths: 1,
		showButtonPanel: true,
		showWeek: true,
		dateFormat: 'dd/mm/yy'
	});
	$("#DT_stop").datepicker("setDate", dt);
	$(".ui-datepicker-trigger").addClass("hidden-print");
	
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
				if(k[0]=='DT_stop') {continue;}//$("#DT_stop").val(k[1]);continue;}
				//if(k[0]=='DT_stop') {if(json.UserID!=11)$("#DT_stop").val(k[1]);continue;}
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
			$("#grouping li").each(function( index ) {
				var id = this.id;
				$("#" + id).appendTo($('#grouping_add'));
				$("#" + id + ">#a1").removeClass('hide').addClass('show');
				$("#" + id + ">#a2").removeClass('show').addClass('hide');
			});
			for(id in grouping){
				$("#divGridGrouping_add #" + grouping[id]).appendTo($('#grouping'));
				$("#" + grouping[id] + ">#a2").removeClass('hide').addClass('show');
				$("#" + grouping[id] + ">#a1").removeClass('show').addClass('hide');
			}
			if(Object.keys(grouping).length==0){
				id = 'g_goodID';
				$("#"+id).appendTo($('#grouping'));
				$("#" + id + ">#a2").removeClass('hide').addClass('show');
			    $("#" + id + ">#a1").removeClass('show').addClass('hide');
			}
			$("#group").val(strJoin(group).join(';'));
			$("#group").attr("title", strJoin(group).join("\n"));
			$("#good").val(strJoin(good).join(';'));
			$("#good").attr("title", strJoin(good).join("\n"));
			$("#cat").val(strJoin(cat).join(';'));
			$("#cat").attr("title",strJoin(cat).join("\n"));
			$("#cat_type").val(strJoin(cat_type).join(';'));
			$("#cat_type").attr("title",strJoin(cat_type).join("\n"));
			$("#markup").val(strJoin(markup).join(';'));
			$("#markup").attr("title", strJoin(markup).join("\n"));
			$("#point").val(strJoin(point).join(';'));
			$("#point").attr("title", strJoin(point).join("\n"));
			$("#seller").val(strJoin(seller).join(';'));
			$("#seller").attr("title", strJoin(seller).join("\n"));
			$("#promo").val(strJoin(promo).join(';'));
			$("#promo").attr("title", strJoin(promo).join("\n"));
			$("#card").val(strJoin(card).join(';'));
			$("#card").attr("title", strJoin(card).join("\n"));
//$('#button_report_run').click();
		});
	});
		
//группы товара
	$("#treeGrid").jqGrid({
		treeGrid: true,
		treeGridModel: 'nested',
		treedatatype: 'json',
		datatype: "json",
		mtype: "POST",
		width: 230,
		height: 380,
		ExpandColumn: 'name',
//		url: '../category/get_tree_NS?nodeid=20',
		colNames: ["id", "Категории"],
		colModel: [
		    {name: 'id', index: 'id', width: 1, hidden: true, key: true},
		    {name: 'name', index: 'name', width: 190, resizable: false, editable: true, sorttype: "text", edittype: 'text', stype: "text", search: true}
		],
		sortname: "Name",
		//sortable: true,
		sortorder: "asc",
		pager: "#ptreeGrid",
		//caption: "Группы товаров",
		toppager: true,
		onSelectRow: function (cat_id) {
		    if (cat_id == null)
			cat_id = 0;
		    $("#grid1").jqGrid('setGridParam', {datatype: "json", url: "../goods/list?col=cat&param=in category&cat_id=" + cat_id, page: 1});
		    $("#grid1").trigger('reloadGrid');
		}
    });
	$("#treeGrid").jqGrid('navGrid','#ptreeGrid', {edit:false, add:false, del:false, search: false, refresh: true, cloneToTop: true});
	$("#treeGrid").navButtonAdd('#treeGrid_toppager',{
		buttonicon: "ui-icon-plusthick", caption: 'Выбрать', position: "last",
		onClickButton: function () {
		    var id = $("#treeGrid").jqGrid('getGridParam', 'selrow');
		    var node = $("#treeGrid").jqGrid('getRowData', id);
			datastr = $("#treeGrid").getGridParam('datastr');
			if (datastr=='group'){
				group[id] = node.name;
				$("#group").val(strJoin(group).join(';'));
				$("#group").attr("title",strJoin(group).join("\n"));
			}
			if (datastr=='cat'){
				cat[id] = node.name;
				$("#cat").val(strJoin(cat).join(';'));
				$("#cat").attr("title",strJoin(cat).join("\n"));
		    }
			if (datastr=='cat_type'){
				cat_type[id] = node.name;
				$("#cat_type").val(strJoin(cat_type).join(';'));
				$("#cat_type").attr("title",strJoin(cat_type).join("\n"));
		    }
			if (datastr=='markup'){
				markup[id] = node.name;
				$("#markup").val(strJoin(markup).join(';'));
				$("#markup").attr("title",strJoin(markup).join("\n"));
		    }
		}
    });
	$("#pg_ptreeGrid").remove();
	$(".ui-jqgrid-hdiv").remove();
	$("#ptreeGrid").removeClass('ui-jqgrid-pager');
    $("#ptreeGrid").addClass('ui-jqgrid-pager-empty');

//список товаров
	$("#grid1").jqGrid({
		sortable: true,
		datatype: "json",
		width: 370,
		height: 330,
		colNames: ['Артикул', 'Название','field3'],
		colModel: [
		    {name: 'field1', index: 'field1', width: 80, sorttype: "text", search: true},
		    {name: 'field2', index: 'field2', sorttype: "text", search: true},
		    {name: 'field3', index: 'field3', sorttype: "text", search: true, hidden: true}
		],
		rowNum: 15,
		rowList: [15, 30, 40, 50, 100, 200, 300],
		sortname: "Name",
		viewrecords: true,
		multiselect: true,
		//loadonce: true,
		gridview: true,
		toppager: true,
		caption: "",
		pager: '#pgrid1'
	    });
	    $("#grid1").jqGrid('navGrid', '#pgrid1', {edit: false, add: false, del: false, search: false, refresh: false, cloneToTop: true});
	    $("#grid1").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true});

	    $("#grid1").navButtonAdd('#grid1_toppager', {
		buttonicon: 'ui-icon-plusthick', caption: 'Выбрать', position: "last",
		onClickButton: function () {
		    var sel;
		    sel = jQuery("#grid1").jqGrid('getGridParam', 'selarrrow');
		    if (sel == '') {
				$("#dialog").css('background-color','');
				$("#dialog>#text").html('Вы не выбрали ни одной записи!');
				$("#dialog").dialog("open");
				return;
		    }
			datastr = $("#grid1").getGridParam('datastr');
			for(key in sel){
				var node = $("#grid1").jqGrid('getRowData', sel[key]);
				//alert('key='+key+'\nsel[key]='+sel[key]+'\nnode.field2='+node.field2);
				if (datastr=='good') good[sel[key]] = node.field2;
				if (datastr == 'point')	point[sel[key]] = node.field2;
				if (datastr == 'seller')seller[sel[key]] = node.field2;
				if (datastr == 'promo')	promo[sel[key]] = node.field2;
				if (datastr == 'card')	card[sel[key]] = node.field1;
			}
			if (datastr=='good'){
				$("#good").val(strJoin(good).join(';'));
				$("#good").attr("title", strJoin(good).join("\n"));
			}
			if (datastr == 'point') {
				$("#point").val(strJoin(point).join(';'));
				$("#point").attr("title", strJoin(point).join("\n"));
			}
			if (datastr == 'seller') {
				$("#seller").val(strJoin(seller).join(';'));
				$("#seller").attr("title", strJoin(seller).join("\n"));
			}
			if (datastr == 'promo') {
				$("#promo").val(strJoin(promo).join(';'));
				$("#promo").attr("title", strJoin(promo).join("\n"));
			}
			if (datastr == 'card') {
				$("#card").val(strJoin(card).join(';'));
				$("#card").attr("title", strJoin(card).join("\n"));
			}
		}
	});

	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');

	$("#treeGrid").gridResize();
	//$("#grid1").gridResize();
	
	$("#divGrid").hide();

	$("#setting_filter a").click(function() {
		operid = '';
		var command = this.parentNode.previousSibling.previousSibling.previousSibling.previousSibling;
		if(command.tagName=='SPAN'){
			command = this.parentNode.previousSibling.previousSibling;
		}
//		console.log(command,$(this).html(),this.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.id);
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
			grouping = [];
			$("#grouping li").each(function( index ) {grouping[index] = this.id;});
			setID = setting.id;
			if(setting.id==setting.text) setID='';
			$.post("../Engine/setting_set"+
					"?DT_start="+ $("#DT_start").val()+
					"&DT_stop="	+ $("#DT_stop").val()+
					"&grouping="+ keyJoin(grouping).join(';')+"|"+strJoin(grouping).join(';')+
					"&group="	+ keyJoin(group).join(';')	+"|"+strJoin(group).join(';')+
					"&good="	+ keyJoin(good).join(';')	+"|"+strJoin(good).join(';')+
					"&cat="		+ keyJoin(cat).join(';')	+"|"+strJoin(cat).join(';')+
					"&cat_type="+ keyJoin(cat_type).join(';')+"|"+strJoin(cat_type).join(';')+
					"&markup="	+ keyJoin(markup).join(';') +"|"+strJoin(markup).join(';')+
					"&point="	+ keyJoin(point).join(';')	+"|"+strJoin(point).join(';')+
					"&seller="	+ keyJoin(seller).join(';')	+"|"+strJoin(seller).join(';')+
					"&promo="	+ keyJoin(promo).join(';')	+"|"+strJoin(promo).join(';')+
					"&card="	+ keyJoin(card).join(';')	+"|"+strJoin(card).join(';'),
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
		if(operid=='group'){
			$("#legendGrid").html('Выбор товара или группы:');
			$("#treeGrid").jqGrid('setGridParam',{datastr:"group"});
			$("#treeGrid").jqGrid('setCaption','Группы товаров');
		    $("#treeGrid").jqGrid('setGridParam', {url: "../category/get_tree_NS?nodeid=10", page: 1}).trigger('reloadGrid');
			$("#divTable").hide();
			$("#divTree").show();
			$("#divGrid").show();
		}
		if(operid=='good'){
			$("#grid1").jqGrid('setLabel', "field1","Артикул");
			$("#grid1").jqGrid('setLabel', "field2", "Название");
			$("#legendGrid").html('Выбор товара или группы:');
			$("#treeGrid").jqGrid('setGridParam',{datastr:"good"});
			$("#treeGrid").jqGrid('setCaption','Группы товаров');
		    $("#treeGrid").jqGrid('setGridParam', {url: "../category/get_tree_NS?nodeid=10", page: 1}).trigger('reloadGrid');
			$("#grid1").jqGrid('setGridParam',{datastr:"good"});
		    $("#grid1").hideCol("field3");
		    $(".ui-search-input>input").val("");
			//$("#grid1").jqGrid("clearGridData", true).trigger("reloadGrid");
		    $("#grid1").jqGrid('setGridParam', {datatype: "json", url: "../goods/list?col=cat&param=in category&cat_id=-1", page: 1}).trigger('reloadGrid');
			$("#divTable").addClass('ml10');
			$("#divTable").show();
			$("#divTree").show();
			$("#divGrid").show();
		}
		if(operid=='cat'){
			$("#legendGrid").html('Выбор категории товара:');
			$("#treeGrid").jqGrid('setGridParam',{datastr:"cat"});
			$("#treeGrid").jqGrid('setCaption', 'Категории товаров');
		    $("#treeGrid").jqGrid('setGridParam', {datatype: "json", url: "../category/get_tree_NS?nodeid=50", page: 1}).trigger('reloadGrid');
			$("#divTable").hide();
			$("#divTree").show();
			$("#divGrid").show();
	    }
		if(operid=='cat_type'){
			$("#legendGrid").html('Выбор категории товара:');
			$("#treeGrid").jqGrid('setGridParam',{datastr:"cat_type"});
			$("#treeGrid").jqGrid('setCaption', 'Кат. по видам животных');
		    $("#treeGrid").jqGrid('setGridParam', {datatype: "json", url: "../category/get_tree_NS?nodeid=70", page: 1}).trigger('reloadGrid');
			$("#divTable").hide();
			$("#divTree").show();
			$("#divGrid").show();
	    }
		if(operid=='markup'){
			$("#legendGrid").html('Выбор категории наценки:');
			$("#treeGrid").jqGrid('setGridParam',{datastr:"markup"});
			$("#treeGrid").jqGrid('setCaption', 'Категории наценки');
		    $("#treeGrid").jqGrid('setGridParam', {datatype: "json", url: "../category/get_tree_NS?nodeid=60", page: 1}).trigger('reloadGrid');
			$("#divTable").hide();
			$("#divTree").show();
			$("#divGrid").show();
	    }
		if(operid=='point'){
			$("#grid1").jqGrid('setLabel', "field1","Код магазина");
			$("#grid1").jqGrid('setLabel', "field2","Название");
			$("#grid1").jqGrid('setLabel', "field3","Матрица");
			$("#legendGrid").html('Выбор торговой точки:');
			$("#grid1").jqGrid('setGridParam',{datastr:"point"});
			$("#grid1").jqGrid('setCaption', 'Торговые точки');
		    $("#grid1").showCol("field3");
		    $(".ui-search-input>input").val("");
			//$("#grid1").jqGrid("clearGridData", true).trigger("reloadGrid");
		    $("#grid1").jqGrid('setGridParam', {datatype: "json", url: "../reports/jqgrid3?action=point_list&f1=PointID&f2=Name&f3=MatrixName&pu.UserID=<?php echo $_SESSION['UserID'];?>", page: 1}).trigger('reloadGrid');
			$("#divTable").removeClass('ml10');
			$("#divTree").hide();
			$("#divTable").show();
			$("#divGrid").show();
	    }
		if(operid=='seller'){
			$("#grid1").jqGrid('setLabel', "field1","Магазин");
			$("#grid1").jqGrid('setLabel', "field2","ФИО");
			$("#grid1").jqGrid('setLabel', "field3","Должность");
			$("#legendGrid").html('Выбор сотрудника:');
			$("#grid1").jqGrid('setGridParam',{datastr:"seller"});
			$("#grid1").jqGrid('setCaption', 'Сотрудники');
		    $("#grid1").showCol("field3");
		    $(".ui-search-input>input").val("");
			//$("#grid1").jqGrid("clearGridData", true).trigger("reloadGrid");
		    $("#grid1").jqGrid('setGridParam', {datatype: "json", url: "../reports/jqgrid3?action=sellers_list&f1=NameShort&f2=Name&f3=Post", page: 1}).trigger('reloadGrid');
			$("#divTable").removeClass('ml10');
			$("#divTree").hide();
			$("#divTable").show();
			$("#divGrid").show();
	    }
		if(operid=='promo'){
			$("#grid1").jqGrid('setLabel', "field1","Код акции");
			$("#grid1").jqGrid('setLabel', "field2","Название");
			$("#grid1").jqGrid('setLabel', "field3","Тип акции");
			$("#legendGrid").html('Выбор акции:');
			$("#grid1").jqGrid('setGridParam',{datastr:"promo"});
			$("#grid1").jqGrid('setCaption', 'Акции');
		    $("#grid1").showCol("field3");
		    $(".ui-search-input>input").val("");
			//$("#grid1").jqGrid("clearGridData", true).trigger("reloadGrid");
		    $("#grid1").jqGrid('setGridParam', {datatype: "json", url: "../reports/jqgrid3?action=promo_list&f1=PromoID&f2=Name&f3=PromoType", page: 1}).trigger('reloadGrid');
			$("#divTable").removeClass('ml10');
			$("#divTree").hide();
			$("#divTable").show();
			$("#divGrid").show();
	    }
		if(operid=='card'){
			$("#grid1").jqGrid('setLabel', "field1","Код карты");
			$("#grid1").jqGrid('setLabel', "field2","ФИО клиента");
			$("#grid1").jqGrid('setLabel', "field3","Процент");
			$("#legendGrid").html('Выбор дисконтной карты:');
			$("#grid1").jqGrid('setGridParam',{datastr:"card"});
			$("#grid1").jqGrid('setCaption', 'Дисконтные карты');
		    $("#grid1").showCol("field3");
		    $(".ui-search-input>input").val("");
			//$("#grid1").jqGrid("clearGridData", true).trigger("reloadGrid");
		    $("#grid1").jqGrid('setGridParam', {datatype: "json", url: "../reports/jqgrid3?action=discountcards_list&f1=CardID&f2=Name&f3=PercentOfDiscount", page: 1}).trigger('reloadGrid');
			$("#divTable").removeClass('ml10');
			$("#divTree").hide();
			$("#divTable").show();
			$("#divGrid").show();
	    }
	});
	$('#grouping_add').selectable({
		selected: function(event, ui){
			if(ui.selected.tagName!='LI') return;
			$(ui.selected).appendTo($('#grouping'));
			$("#"+ui.selected.id+">#a2").removeClass('hide').addClass('show');
			$("#"+ui.selected.id+">#a1").removeClass('show').addClass('hide');
		}
	});
	$('#grouping').selectable({
		selected: function(event, ui){
			if(ui.selected.tagName!='LI') return;
			$(ui.selected).appendTo($('#grouping_add'));
			$("#"+ui.selected.id+">#a1").removeClass('hide').addClass('show');
			$("#"+ui.selected.id+">#a2").removeClass('show').addClass('hide');
		}
	});
	function profitType(value, name, record){
		if(typeof value === 'string'){
			value = {totalAmount: 0, totalProfit: 0};
		}
		// perform summary
		if (record['field11']) {
			value.totalAmount += parseFloat(record['field11']);
		}
		if (record['field13']) {
			value.totalProfit += parseFloat(record['field13']);
		}
		return value;
	}
	function profitFormat(cellval, opts, rwdat, act){
		// get the regional options and pass it to the custom formatter
		opts = $.extend({}, $.jgrid.getRegional(this, 'formatter'), opts);
		// determine if we are in summary row to put the value
		//console.log(cellval);
		if (cellval == null) return '';
		if (opts.rowId === '') {
		    if (cellval.totalAmount > 0) {
				var val = cellval.totalProfit * 100 / cellval.totalAmount;
				return $.fn.fmatter('number', val, opts, rwdat, act) + ' %';
		    } else {
				return $.fn.fmatter('number', cellval, opts, rwdat, act) + ' %';
		    }
		} else {
		    return $.fn.fmatter('number', cellval, opts, rwdat, act);
		}
	}

// Creating gridRep
	var gridRep = function(){
	$("#gridRep").jqGrid({
		sortable: true,
	    //datatype: "json",
		datatype: 'local',
	    height: 'auto',
	    colModel: [
			{name: 'field0' , index: 'field0' , width: 250, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field1' , index: 'field1' , width: 250, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field2' , index: 'field2' , width: 250, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field3' , index: 'field3' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field4' , index: 'field4' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field5' , index: 'field5' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field6' , index: 'field6' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field7' , index: 'field7' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field8' , index: 'field8' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field9' , index: 'field9' , width: 200, align: "left", sorttype: "text", summaryType: 'count', summaryTpl: '<b class="ml10">Итого ({0} эл.):</b>'},
			{name: 'field10', index: 'field10', width: 90, align: "right", sorttype: "number", formatter:"number", summaryType:'sum', summaryTpl:'<b>{0} </b>'		, formatoptions:{decimalSeparator:",", thousandsSeparator: " "}},
			{name: 'field11', index: 'field11', width: 90, align: "right", sorttype: "number", formatter:"number", summaryType:'sum', summaryTpl:'<b>{0} грн.</b>'	, formatoptions:{decimalSeparator:",", thousandsSeparator: " "}},
			{name: 'field12', index: 'field12', width: 90, align: "right", sorttype: "number", formatter:"number", summaryType:'sum', summaryTpl:'<b>{0} грн.</b>'	, formatoptions:{decimalSeparator:",", thousandsSeparator: " "}},
			{name: 'field13', index: 'field13', width: 90, align: "right", sorttype: "number", formatter:"number", summaryType:'sum', summaryTpl:'<b>{0} грн.</b>'	, formatoptions:{decimalSeparator:",", thousandsSeparator: " "}},
			{name: 'field14', index: 'field14', width: 60, align: "right", sorttype: "number", formatter:profitFormat, summaryType:profitType,summaryTpl:'<b>{0}</b>'		, formatoptions:{decimalSeparator:",", thousandsSeparator: " "}},
			{name: 'field15', index: 'field15', width: 60, align: "right", sorttype: "number", formatter:"number", summaryType:'avg', summaryTpl:'<b>{0} %</b>'		, formatoptions:{decimalSeparator:",", thousandsSeparator: " "}},
	    ],
	    //width: 'auto',
		frozen : true,
	    shrinkToFit: true,
		loadonce: true,
		rowNum:10000000,
	    gridview: true,
		footerrow:true,
		//userDataOnFooter: true,
	    toppager: true,
		loadComplete: function(data) {
			//console.log(data);
			if(data['error']){
				$("#dialog_progress").dialog("close");
				setTimeout(function () {
					$("#dialog").css('background-color','linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
					$("#dialog>#text").html("При выполнении запроса возникла ошибка: <br><br>"
						+ data.error[2] + "<br><br>"
						+ "Сообщите разработчику!");
					$("#dialog").dialog("open");
				},1200);
				return;
			}
			if(data['total']>0&&data['records']==0){
				$("#dialog_progress").dialog("close");
				setTimeout(function () {
					$("#dialog").css('background-color', 'linear-gradient(to bottom, #def0de 0%, #419641 100%)');
					$("#dialog>#text").html("По Вашему запросу: <br><br>"
						+ "Не найдено ни одной записи!");
					$("#dialog").dialog("open");
				},1200);
			    return;
			}
			$("#grouping li").each(function( index ) {
				var cl = this.className.substr(0,3);
				$(".jqgroup.ui-row-ltr.gridRepghead_"+index).css("background-image","none");
				$(".jqgroup.ui-row-ltr.gridRepghead_"+index).addClass(cl);
			});
			var ar = new Object();
			for(i=10;i<15;i++){
				var summary = $("#gridRep").jqGrid('getCol', "field"+i, false, 'sum');
				ar["field"+i] = summary;
			}
			i = 14;
			var summary = ar["field13"]*100/ar["field11"];
			ar["field" + i] = summary;
			$("#gridRep").jqGrid('footerData','set', ar);
			$("#dialog_progress").dialog("close");
			if(data.records>2000){
				$("#dialog").css('background-color','');
				$("#dialog>#text").html("По Вашему запросу найдено: "+data.records+" записей.<br><br>"
						+ "В отчет выведены первые 5000 записей.<br><br>"
						+ "Итоговые суммы рассчитаны только по выведенным записям.<br><br>"
						+ "Вы можете исправить параметры отбора и группировки отчета "
						+ "для получения правильных итоговых расчетов."
				);
			    $("#dialog").dialog("open");
			}
//			var test = data.query;
//			test = test.replace(new RegExp("\t","g"),"&nbsp&nbsp&nbsp&nbsp&nbsp");
//			test = test.replace(new RegExp("\r\n","g"),"<br>");
//			$("#test").html(test);
		},
	    caption: 'Отчет "Продажи товаров в рознице"',
	    pager: '#pgridRep',
	});
	$("#gridRep").jqGrid('navGrid', '#pgridRep', {edit: false, add: false, del: false, search: false, refresh: true, cloneToTop: true});
	$("#gridRep").navButtonAdd("#gridRep_toppager",{
		caption: 'Экспорт в Excel', 
		title: 'to Excel', 
		icon: "ui-extlink",
		onClickButton: function () {
			$("#dialog_progress").dialog( "option", "title", 'Ожидайте! Готовим данные для XLS файла');
			$("#dialog_progress").dialog("open");
			setTimeout(function () {
				var gr = $("#gview_gridRep").clone();
				$(gr).find("#pg_gridRep_toppager").remove();
				$(gr).find("#gridRep_toppager").html($("#report_param_str").html());
				$(gr).find("th").filter(function () {if ($(this).css('display') == 'none')$(this).remove();});
				$(gr).find("td").filter(function () {if ($(this).css('display') == 'none')$(this).remove();});
				$(gr).find("table").filter(function () {if ($(this).attr('border') == '0')$(this).attr('border', '1');});
				$(gr).find("td").filter(function () {if ($(this).attr('colspan') > 1)$(this).attr('colspan', '6');});
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
				html = html.split("<table").join("<table border='1' ");
				
				var file_name = 'Продажи в рознице';
				var report_name = 'report'+reportID;
				$.ajax({
					type: "POST",
					data: ({report_name: report_name, file_name: file_name, html: html}),
					url: '../Engine/set_file',
					dataType: "html",
					success: function (data) {
						$("#dialog_progress").dialog("close");
						var $frame = $('<iframe src="../Engine/get_file?report_name='+report_name+'&file_name='+file_name+'" style="display:none;"></iframe>');
						$('html').append($frame);
					}
				});
			}, 1000);
		}
	});
	//	$("#gridRep").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true});
	$("#pg_pgridRep").remove();
	$("#pgridRep").removeClass('ui-jqgrid-pager');
	$("#pgridRep").addClass('ui-jqgrid-pager-empty');
	$("#gridRep").gridResize();
		
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
	}

	$('#button_report_run').click(function (e) {
		var count = $("#grouping").children().length;
		if (count == 0) {
			$("#dialog").css('background-color', 'linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
			$("#dialog>#text").html('Необходимо выбрать группировку!');
			$("#dialog").dialog("open");
			return;
	    }
		if($("#gridRep").jqGrid('getRowData').length>0) $.jgrid.gridUnload("#gridRep");
		gridRep();
		$("#dialog_progress").dialog( "option", "title", 'Ожидайте! Выполняется формирование отчета...');
		$("#dialog_progress").dialog("open");
		$("#a_tab_report").tab('show');
//return;
		grouping = [];
		$("#grouping li").each(function( index ) {grouping[index] = this.id;});
		for(i=0; i<10; i++){
			$("#gridRep").jqGrid('showCol',"field"+i);
		}
		var grlen = Object.keys(grouping).length;
		var ar = [];
		var view_reason = false;
		for(var id=0; id<grlen; id++){
			if(id==grlen-1) break;
			//console.log("field"+id, grouping[id]);
			if (grouping[id]=='cc_checkID') view_reason = true;
			ar[id] = 'field'+id;
			//console.log(ar[id],grouping[id]);
			$("#gridRep").jqGrid('setLabel', "field"+id, grouping[id]);
		}
		if(grouping[id]=='groupName2')  $("#gridRep").jqGrid('setLabel', "field" + id, "Группа товара (2 уровня)");
	    if(grouping[id]=='groupName3')  $("#gridRep").jqGrid('setLabel', "field" + id, "Группа товара (3 уровня)");
		if(grouping[id]=='catName')     $("#gridRep").jqGrid('setLabel', "field" +id, "Категория товара");
		if(grouping[id]=='cattypeName1') $("#gridRep").jqGrid('setLabel', "field"+id, "Вид товара");
		if(grouping[id]=='cattypeName2') $("#gridRep").jqGrid('setLabel', "field"+id, "Вид товара (разверн.)");
		if(grouping[id]=='markupName')  $("#gridRep").jqGrid('setLabel', "field"+id, "Категория наценки");
		if(grouping[id]=='cc_checkID')  $("#gridRep").jqGrid('setLabel', "field"+id, "Документ");
		if(grouping[id]=='pr_promoID')  $("#gridRep").jqGrid('setLabel', "field"+id, "Акция");
		if(grouping[id]=='c_clientID')  $("#gridRep").jqGrid('setLabel', "field"+id, "Торговая точка");
		if(grouping[id]=='c_City')	    $("#gridRep").jqGrid('setLabel', "field" + id, "Город");
		if(grouping[id]=='DiscountType')$("#gridRep").jqGrid('setLabel', "field" + id, "Вид скидки");
		if(grouping[id]=='s_sellerID')  $("#gridRep").jqGrid('setLabel', "field"+id, "Сотрудник");
		if(grouping[id]=='cards_cardID')$("#gridRep").jqGrid('setLabel', "field"+id, "Дисконтная карта");
		if(grouping[id]=='g_goodID'){
			$("#gridRep").jqGrid('setLabel', "field"+id, "Артикул");
			id++;
			$("#gridRep").jqGrid('setLabel', "field"+id, "Название");
			id++;
			$("#gridRep").jqGrid('setLabel', "field"+id, "Код 1С");
			if (view_reason){
				id++;
				$("#gridRep").jqGrid('setLabel', "field"+id, "Причина скидки");
			}
		}
		id++;
		for(var fi=0; fi<6; fi++){
			$("#gridRep").jqGrid('setLabel', "field"+(fi+10), colnames[fi]);
		}
		for(i=id; i<10; i++){
			$("#gridRep").jqGrid('hideCol',"field"+i);
		}
//console.log("grlen:"+grlen);
		if(grlen<=1){
			$("#gridRep").jqGrid('setGridParam', {
				grouping: true,
				groupingView : {
					groupField: ar,
					//groupColumnShow: [false, false, false, false, false, false, false, false, false, false],
					groupColumnShow: [true, true, true, true, true, true, true, true, true, true],
					groupText: ['<b>{0}</b>'],
					//groupCollapse: false,
					groupDataSorted: true,
					//groupOrder: ['asc', 'asc'],
					groupSummary : [true, true, true, true, true, true, true, true, true, true],
					showSummaryOnHide: true,
				}
			});
		}else{
			$("#gridRep").jqGrid('setGridParam', {
				grouping: true,
				groupingView : {
					groupField: ar,
					groupColumnShow: [false, false, false, false, false, false, false, false, false, false],
					//groupColumnShow: [true, true, true, true, true, true, true, true, true, true],
					groupText: ['<b>{0}</b>'],
					//groupCollapse: false,
					//groupDataSorted: true,
					//groupOrder: ['asc', 'asc'],
					groupSummary : [true,true,true,true,true,true,true,true,true,true],
					showSummaryOnHide: true,
				}
			});
		}
		var grouping_str = '';
		$("#grouping li span").each(function( index ) { grouping_str += ((grouping_str.length==0) ? '' : ', ') + $(this).html();});
		prmRep = "<b>Отбор данных выполнен по критериям:</b> ";
		prmRep += "<br>" + "Период с " + $("#DT_start").val() + " по " + $("#DT_stop").val();
		prmRep += (Object.keys(group).length == 0) ? "" : "<br>" + "Группа товара: " + strJoin(group).join(', ');
		prmRep += (Object.keys(good).length == 0) ? "" : "<br>" + "Товары: " + strJoin(good).join(', ');
		prmRep += (Object.keys(cat).length == 0) ? "" : "<br>" + "Категории товаров: " + strJoin(cat).join(', ');
		prmRep += (Object.keys(cat_type).length == 0) ? "" : "<br>" + "Категории товаров: " + strJoin(cat_type).join(', ');
		prmRep += (Object.keys(markup).length == 0) ? "" : "<br>" + "Категории наценок: " + strJoin(markup).join(', ');
		prmRep += (Object.keys(point).length == 0) ? "" : "<br>" + "Торговые точки: " + strJoin(point).join(', ');
		prmRep += (Object.keys(seller).length == 0) ? "" : "<br>" + "Сотрудники: " + strJoin(seller).join(', ');
		prmRep += (Object.keys(promo).length == 0) ? "" : "<br>" + "Акции: " + strJoin(promo).join(', ');
		prmRep += (Object.keys(card).length == 0) ? "" : "<br>" + "Дисконтные карты: " + strJoin(card).join(', ');
		prmRep += (grouping_str.length == 0) ? "" : "<br>" + "Группировки отчета: " + grouping_str;
		$("#report_param_str").html(prmRep);
//return;
		orderby = ""; len = Object.keys(grouping).length-1;
		for (id in grouping) {
			orderby += grouping[id].replace('_', '.') + " asc";
			if(len != parseInt(id)) orderby += ', ';
		}
		orderby = orderby.split("g.goodID").join("g.Name");
		orderby = orderby.split("c.clientID").join("c.NameShort");
		orderby = orderby.split("s.sellerID").join("s.Name");
		$("#gridRep").jqGrid('setGridParam', {datatype: "json", url: "../reports/report"+reportID+"_data" +
			"?sid=" + reportID +
			"&DT_start=" + $("#DT_start").val() +
			"&DT_stop=" + $("#DT_stop").val() +
			"&grouping=" + strJoin(grouping).join(';') +
			"&group=" + keyJoin(group).join(';') +
			"&good=" + keyJoin(good).join(';') +
			"&cat=" + keyJoin(cat).join(';') +
			"&cat_type=" + keyJoin(cat_type).join(';') +
			"&markup=" + keyJoin(markup).join(';') +
			"&point=" + keyJoin(point).join(';') +
			"&seller=" + keyJoin(seller).join(';') +
			"&promo=" + keyJoin(promo).join(';') +
			"&card=" + keyJoin(card).join(';') +
			"&UserID=<?php echo $_SESSION['UserID']; ?>" +
			"&orderby=" + orderby +
			"",
		}).trigger('reloadGrid');
		$("#gridRep").gridResize();
	});

	//$("#dialog_progress").dialog("open");
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
		<li><a href="#tab_grouping"  role="tab" data-toggle="tab">Настройки группировок</a></li>
		<li><a id="a_tab_report" href="#tab_report" role="tab" data-toggle="tab">Отчет "Продажи товаров в рознице (себест.)"</a></li>
	</ul>
	<div class="floatL">
		<button id="button_report_run" class="btn btn-sm btn-info frameL m0 h40 hidden-print font14">
			<span class="ui-button-text" style1='width:120px;height:22px;'>Сформировать отчет</span>
		</button>
	</div>
<!--	<div class="floatL ml30">
		<button type="text" class="btn btn-sm btn-default frameL m0 h40 w250 hidden-print font14">
			<span class="ui-button-text" style1='width:120px;height:22px;'>Дата акт. себест.: <?php echo $_SESSION['DT_sebest']?></span>
		</button>
	</div>
-->
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
						<a class="btn btn-default w100p" type="button"><img class="img-rounded h20 m0" src="../../images/save-as.png">
						</a>
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
				<div class="input-group input-group-sm mt20 w100p">
					<span class="input-group-addon w130">Группа товара:</span>
					<input id="group" name="group" type="text" class="form-control">
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Товар:</span>
					<input id="good" name="good" type="text" class="form-control">
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Категория товара:</span>
					<input id="cat" name="cat" type="text" class="form-control" >
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Кат. по виду товара:</span>
					<input id="cat_type" name="cat_type" type="text" class="form-control" >
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt20 w100p">
					<span class="input-group-addon w130">Категория наценки:</span>
					<input id="markup" name="markup" type="text" class="form-control" >
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt20 w100p">
					<span class="input-group-addon w130">Торговая точка:</span>
					<input id="point" name="point" type="text" class="form-control" >
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt20 w100p">
					<span class="input-group-addon w130">Сотрудник:</span>
					<input id="seller" name="seller" type="text" class="form-control" >
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt20 w100p">
					<span class="input-group-addon w130">Акция:</span>
					<input id="promo" name="promo" type="text" class="form-control" >
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
				<div class="input-group input-group-sm mt20 w100p">
					<span class="input-group-addon w130">Дисконтная карта:</span>
					<input id="card" name="card" type="text" class="form-control">
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">X</a>
					</span>
					<span class="input-group-btn w32">
						<a class="btn btn-default w100p" type="button">...</a>
					</span>
				</div>
			</div>
			<div id="divGrid" class='p5 ui-corner-all frameL ml5 border0'>
				<legend id="legendGrid"></legend>
				<div id="divTree" class='frameL'>
					<table id="treeGrid"></table>
					<div id="ptreeGrid"></div>
				</div>
				<div id="divTable" class='frameL ml10'>
					<table id="grid1"></table>
					<div id="pgrid1"></div>
				</div>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 ui-corner-all borderColor frameL border1" id="tab_grouping">
			<div id="divGridGrouping" class='p5 ui-corner-all frameL m10 border1 w250'>
				<legend>Выбранные группировки</legend>
				<ol id="grouping" class="w100p selectable">
				</ol>
			</div>
			<div id="divGridGrouping_add" class='p5 ui-corner-all frameL m10 border1 w250'>
				<legend>Возможные группировки</legend>
				<ul id="grouping_add" class="w100p selectable">
					<li class="bc1 ui-corner-all" id="groupName1">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Группа товара (1 уровень)</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc1 ui-corner-all" id="groupName2">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Группа товара (2 уровня)</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc1 ui-corner-all" id="groupName3">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Группа товара (3 уровня)</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc2 ui-corner-all" id="g_goodID">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Товар</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc3 ui-corner-all" id="catName">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Категория товара</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc15 ui-corner-all" id="cattypeName1">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">По видам товаров</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc16 ui-corner-all" id="cattypeName2">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">По видам товаров (разв.)</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc4 ui-corner-all" id="markupName">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Категория наценки</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc5 ui-corner-all" id="c_clientID">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Торговая точка</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc6 ui-corner-all" id="s_sellerID">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Сотрудник</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc7 ui-corner-all" id="pr_promoID">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Акция</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc8 ui-corner-all" id="cards_cardID">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Дисконтная карта</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc9 ui-corner-all" id="cc_checkID">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Документ</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc10 ui-corner-all" id="c_City">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Город</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
					<li class="bc11 ui-corner-all" id="DiscountType">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Вид скидки</span>
						<a id="a2" class="floatL ui-icon ui-icon-triangle-1-e mt2 hide" type="button"></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 borderColor borderTop1 frameL center border0" id="tab_report">
			<div id='report_param_str' class="mt10 TAL font14">
			</div>
			<div id='div1' class='center frameL mt10'>
				<table id="gridRep"></table>
				<div id="pgridRep"></div>
			</div>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
<div id="dialog_progress" title="Ожидайте!">
	<img class="ml30 mt20 border0 w300" src="../../img/progress_circle5.gif">
</div>
