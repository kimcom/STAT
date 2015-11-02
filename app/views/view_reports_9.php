<script type="text/javascript" src="../../js/Chart.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	var myChart = null;
    var optionsLine = {
		///Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines: true,
		//String - Colour of the grid lines
		scaleGridLineColor: "rgba(0,0,0,.05)",
		//Number - Width of the grid lines
		scaleGridLineWidth: 1,
		//Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		//Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines: true,
		//Boolean - Whether the line is curved between points
		bezierCurve: true,
		//Number - Tension of the bezier curve between points
		bezierCurveTension: 0.4,
		//Boolean - Whether to show a dot for each point
		pointDot: true,
		//Number - Radius of each point dot in pixels
		pointDotRadius: 4,
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth: 1,
		//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius: 20,
		//Boolean - Whether to show a stroke for datasets
		datasetStroke: true,
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth: 2,
		//Boolean - Whether to fill the dataset with a colour
		datasetFill: true,
		//String - A legend template
		//legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
		legendTemplate: "<ul class=\"list-unstyled <%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%=datasets[i].label%></li><%}%></ul>",
		tooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>"
    };
    var optionsBar = {
		//Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
		scaleBeginAtZero : true,
		//Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines : true,
		//String - Colour of the grid lines
		scaleGridLineColor : "rgba(0,0,0,.05)",
		//Number - Width of the grid lines
		scaleGridLineWidth : 1,
		//Boolean - Whether to show horizontal lines (except X axis)
		scaleShowHorizontalLines: true,
		//Boolean - Whether to show vertical lines (except Y axis)
		scaleShowVerticalLines: true,
		//Boolean - If there is a stroke on each bar
		barShowStroke : true,
		//Number - Pixel width of the bar stroke
		barStrokeWidth : 2,
		//Number - Spacing between each of the X value sets
		barValueSpacing : 5,
		//Number - Spacing between data sets within X values
		barDatasetSpacing : 1,
		//String - A legend template
		legendTemplate : "<ul class=\"list-unstyled <%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
		tooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>"
    };
	var optionsRadar = {
		//Boolean - Whether to show lines for each scale point
		scaleShowLine: true,
		//Boolean - Whether we show the angle lines out of the radar
		angleShowLineOut: true,
		//Boolean - Whether to show labels on the scale
		scaleShowLabels: false,
		// Boolean - Whether the scale should begin at zero
		scaleBeginAtZero: true,
		//String - Colour of the angle line
		angleLineColor: "rgba(0,0,0,.1)",
		//Number - Pixel width of the angle line
		angleLineWidth: 1,
		//String - Point label font declaration
		pointLabelFontFamily: "'Arial'",
		//String - Point label font weight
		pointLabelFontStyle: "normal",
		//Number - Point label font size in pixels
		pointLabelFontSize: 10,
		//String - Point label font colour
		pointLabelFontColor: "#666",
		//Boolean - Whether to show a dot for each point
		pointDot: true,
		//Number - Radius of each point dot in pixels
		pointDotRadius: 3,
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth: 1,
		//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
		pointHitDetectionRadius: 20,
		//Boolean - Whether to show a stroke for datasets
		datasetStroke: true,
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth: 2,
		//Boolean - Whether to fill the dataset with a colour
		datasetFill: true,
		//String - A legend template
		legendTemplate: "<ul class=\"list-unstyled <%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].strokeColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
		tooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>"
	};
	var optionsPolar = {
		//Boolean - Show a backdrop to the scale label
		scaleShowLabelBackdrop: true,
		//String - The colour of the label backdrop
		scaleBackdropColor: "rgba(255,255,255,0.75)",
		// Boolean - Whether the scale should begin at zero
		scaleBeginAtZero: true,
		//Number - The backdrop padding above & below the label in pixels
		scaleBackdropPaddingY: 2,
		//Number - The backdrop padding to the side of the label in pixels
		scaleBackdropPaddingX: 2,
		//Boolean - Show line for each value in the scale
		scaleShowLine: true,
		//Boolean - Stroke a line around each segment in the chart
		segmentShowStroke: true,
		//String - The colour of the stroke on each segement.
		segmentStrokeColor: "#fff",
		//Number - The width of the stroke value in pixels
		segmentStrokeWidth: 2,
		//Number - Amount of animation steps
		animationSteps: 100,
		//String - Animation easing effect.
		animationEasing: "easeOutBounce",
		//Boolean - Whether to animate the rotation of the chart
		animateRotate: true,
		//Boolean - Whether to animate scaling the chart from the centre
		animateScale: false,
		//String - A legend template
		legendTemplate: "<ul class=\"list-unstyled <%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%>: <%=segments[i].value%><%}%></li><%}%></ul>",
		tooltipTemplate: "<%= label %>: <%= value %>"
	};
    var optionsPie = {
		//Boolean - Whether we should show a stroke on each segment
		segmentShowStroke : true,
		//String - The colour of each segment stroke
		segmentStrokeColor : "#fff",
		//Number - The width of each segment stroke
		segmentStrokeWidth : 1,
		//Number - The percentage of the chart that we cut out of the middle
		percentageInnerCutout : 0, // This is 0 for Pie charts
		//Number - Amount of animation steps
		animationSteps : 100,
		//String - Animation easing effect
		animationEasing : "easeOutBounce",
		//Boolean - Whether we animate the rotation of the Doughnut
		animateRotate : true,
		//Boolean - Whether we animate scaling the Doughnut from the centre
		animateScale : false,
		//String - A legend template
		legendTemplate : "<ul class=\"list-unstyled <%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%>: <%=segments[i].value%><%}%></li><%}%></ul>",
		tooltipTemplate: "<%= label %>: <%= value %>"
    };
    var optionsDoughnut = {
		//Boolean - Whether we should show a stroke on each segment
		segmentShowStroke : true,
		//String - The colour of each segment stroke
		segmentStrokeColor : "#fff",
		//Number - The width of each segment stroke
		segmentStrokeWidth : 1,
		//Number - The percentage of the chart that we cut out of the middle
		percentageInnerCutout : 40, // This is 0 for Pie charts
		//Number - Amount of animation steps
		animationSteps : 100,
		//String - Animation easing effect
		animationEasing : "easeOutBounce",
		//Boolean - Whether we animate the rotation of the Doughnut
		animateRotate : true,
		//Boolean - Whether we animate scaling the Doughnut from the centre
		animateScale : false,
		//String - A legend template
		legendTemplate : "<ul class=\"list-unstyled <%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%>: <%=segments[i].value%><%}%></li><%}%></ul>",
		tooltipTemplate: "<%= label %>: <%= value %>"
    };
//	setTimeout(function(){
//		$('#a_tab_report').click();
//	}, 100);

	var reportID = 9; 
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
	settings['grouping']=grouping;
	settings['group']=group;
	settings['good']=good;
	settings['cat']=cat;
	settings['cat_type']=cat_type;
	settings['markup']=markup;
	settings['point']=point;
	settings['seller']=seller;
	settings['promo']=promo;
	var colnames = ['Кол-во','Себест.','Оборот','Доход','% наценки'];
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
		dateFormat: 'dd/mm/yy',
		showWeek: true,
	});
	$("#DT_stop").datepicker("setDate", dt);
	$(".ui-datepicker-trigger").addClass("hidden-print");

	//заполнение интервалов
	var interval = [{id: 'day', text: 'день'}, {id: 'week', text: 'неделя'}, {id: 'month', text: 'месяц'}, {id: 'year', text: 'год'}];
	$("#select_interval").select2({data: interval, placeholder: "Выберите интервал", minimumResultsForSearch: Infinity});
	$("#select_interval").select2("val", 'month');
	//заполнение данных для анализа
	var data = [{id: 'Quantity', text: 'количество'}, {id: 'Oborot', text: 'оборот'}, {id: 'Dohod', text: 'доход'}, {id: 'all', text: 'кол-во, оборот, доход'}];
	$("#select_data").select2({data: data, placeholder: "Выберите данные для вывода в отчет", minimumResultsForSearch: Infinity});
	$("#select_data").select2("val", 'qty');
	//заполнение вид графика
	var chart_type = [{id: 'line', text: 'линейный'}, {id: 'bar', text: 'гистограмма'}, {id: 'radar', text: 'радиолокационная карта'}, {id: 'polar', text: 'полярная диаграмма (по интервалам)'}, {id: 'polar2', text: 'полярная диаграмма (по группировке)'}, {id: 'pie', text: 'круговая диаграмма (по интервалам)'}, {id: 'pie2', text: 'круговая диаграмма (по группировке)'}, {id: 'doughnut', text: 'кольцевая диаграмма (по интервалам)'}, {id: 'doughnut2', text: 'кольцевая диаграмма (по группировке)'}];
	$("#select_chart_type").select2({data: chart_type, minimumResultsForSearch: 9, placeholder: "Выберите вид графика", minimumResultsForSearch: Infinity});
	$("#select_chart_type").select2("val", 'month');
		
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
		if(window.location.pathname=="/reports/report9_start"){
			$("#select_report_setting").click();
			setTimeout(function(){
				$("#button_report_run").click();
			}, 300);
		}else{
			$("#select_report_setting").select2("val", "тест");
			$("#select_report_setting").click();
		}
    });

	$("#select_report_setting").click(function () {
		var parameter = '';
		if(window.location.pathname!="/reports/report9_start"){
			var setting = $("#select_report_setting").select2("data");
			if (setting == null) return;
			clearObj(settings);
			parameter = '&sname='+setting.text;
		}
		$.post('../Engine/setting_get_byName?sid='+reportID+parameter,
		function (json) {
			if(json==null) return;
			var set = json.Setting;
			var aset = set.split('&');
			for(key in aset){
				var k = aset[key].split('=');
				if(k[1]=='')continue;
				if(k[0]=='DT_start') {if(json.UserID!=11)$("#DT_start").val(k[1]);continue;}
				if(k[0]=='DT_stop') {if(json.UserID!=11)$("#DT_stop").val(k[1]);continue;}
				if(k[0]=='interval')	{$("#select_interval").select2("val", k[1]);continue;}
				if(k[0]=='data')		{$("#select_data").select2("val", k[1]);continue;}
				if(k[0]=='chart')		{$("#select_chart_type").select2("val", k[1]);continue;}
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
//				if (datastr == 'card')	card[sel[key]] = node.field1;
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
//			if (datastr == 'card') {
//				$("#card").val(strJoin(card).join(';'));
//				$("#card").attr("title", strJoin(card).join("\n"));
//			}
		}
	});

	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');

	$("#treeGrid").gridResize();
	$("#grid1").gridResize();
	
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
					"&interval="+ $("#select_interval").select2("val")+
					"&data="	+ $("#select_data").select2("val")+
					"&chart="	+ $("#select_chart_type").select2("val"),
//					"&card="	+ keyJoin(card).join(';')	+"|"+strJoin(card).join(';'),
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
	});
	$('#grouping_add').selectable({
		selected: function(event, ui){
			if(ui.selected.tagName!='LI') return;
			var count = $("#grouping").children().length;
			if (count==1) {
				$("#dialog").css('background-color','linear-gradient(to bottom, #f7dcdb 0%, #c12e2a 100%)');
				$("#dialog>#text").html('В данном отчете возможен выбор<br>только 1-ой группировки!');
				$("#dialog").dialog("open");
				return;
			};
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

	$('#button_report_run').click(function (e) {
		if ($("#select_interval").select2("val")=='' ||
			$("#select_data").select2("val")=='' ||
			$("#select_chart_type").select2("val")=='') {
				$("#dialog").css('background-color','');
				$("#dialog>#text").html('Необходимо указать интервал, показатель и вид графика!');
				$("#dialog").dialog("open");
			return;
		};
		$("#a_tab_report").tab('show');
//return;
		grouping = [];
		$("#grouping li").each(function(index) {grouping[index] = this.id;});
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
		prmRep += (grouping_str.length == 0) ? "" : "<br>" + "Группировки отчета: " + grouping_str;
		prmRep += "<br>" + "Интервал: " + $("#select_interval").select2("data").text;
		prmRep += ". Показатель: " + $("#select_data").select2("data").text + ".";
		prmRep += " Вид графика: " + $("#select_chart_type").select2("data").text + ".";
		$("#report_param_str").html(prmRep);

		orderby = ""; groupName1 = ""; groupName2 = "";
		if(grouping[0])	{
			orderby += grouping[0].replace('_','.')+" asc,";
			groupName1 = $("#"+grouping[0]+" span").html();
		}
		if(grouping[1])	{
			orderby += grouping[1].replace('_','.')+" asc,";
			groupName2 = $("#"+grouping[1]+" span").html();
		}
		orderby = orderby.split("g.goodID").join("g.Name");
		orderby = orderby.split("c.clientID").join("c.NameShort");
		orderby = orderby.split("s.sellerID").join("s.Name");

		if (myChart!=null) 	myChart.destroy();
		var chart = $("#select_chart_type").select2("val");
		str_group = 'intervals';
		if (grouping.length>=1) str_group += ";" + strJoin(grouping).join(';');
		if (chart == 'polar' || chart == 'pie' || chart == 'doughnut') str_group = 'intervals';
		if (chart == 'polar2' || chart == 'pie2' || chart == 'doughnut2') {
			if (grouping.length==0) {
				$("#dialog").css('background-color','');
				$("#dialog>#text").html('Вы не выбрали группировку отчета!');
				$("#dialog").dialog("open");
				return;
			}else{
				str_group = strJoin(grouping).join(';');
			}
		}
		$("#dialog_progress").dialog( "option", "title", 'Ожидайте! Выполняется формирование отчета...');
	    $("#dialog_progress").dialog("open");
		$.post("../reports/report"+reportID+"_data" +
				"?sid=" + reportID +
			    "&DT_start=" + $("#DT_start").val() +
			    "&DT_stop=" + $("#DT_stop").val() +
			    "&grouping=" + str_group +
			    "&group=" + keyJoin(group).join(';') +
			    "&good=" + keyJoin(good).join(';') +
			    "&cat=" + keyJoin(cat).join(';') +
			    "&cat_type=" + keyJoin(cat_type).join(';') +
			    "&markup=" + keyJoin(markup).join(';') +
			    "&point=" + keyJoin(point).join(';') +
			    "&seller=" + keyJoin(seller).join(';') +
			    "&promo=" + keyJoin(promo).join(';') +
			    "&interval=" + $("#select_interval").select2("val") +
			    "&data=" + $("#select_data").select2("val") +
			    "&chart=" + $("#select_chart_type").select2("val") +
			    "&groupName1=" + groupName1 +
			    "&groupName2=" + groupName2 +
			    "&UserID=<?php echo $_SESSION['UserID']; ?>" +
			    "&sidx="+orderby+" intervals asc", 
				function (json) {
					//console.log(json.data);
					//$('#div_report').html(JSON.stringify(json.data));
//					str = JSON.stringify(data2)+'<br>'+'<br>';
//					str += JSON.stringify(json.data);
//					str = str.split('},{').join('},<br>{');
//					$('#div_report').html(str);
					$("#dialog_progress").dialog("close");
					setTimeout(function () {
						Chart.defaults.global.responsive = true;
						//Chart.defaults.global.showTooltips = true;
						var ctx2 = $("#myChart").get(0).getContext("2d");
						if (chart=='line')
							myChart = new Chart(ctx2).Line(json.data, optionsLine);
						if (chart=='bar')
							myChart = new Chart(ctx2).Bar(json.data, optionsBar);
						if (chart=='radar')
							myChart = new Chart(ctx2).Radar(json.data, optionsRadar);
						if (chart=='polar' || chart=='polar2')
							myChart = new Chart(ctx2).PolarArea(json.data, optionsPolar);
						if (chart=='pie' || chart=='pie2')
							myChart = new Chart(ctx2).Pie(json.data, optionsPie);
						if (chart=='doughnut' || chart=='doughnut2')
							myChart = new Chart(ctx2).Doughnut(json.data, optionsDoughnut);
						$("#myChart").resizable();
						$("#chart_legend").html(myChart.generateLegend());
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
		<li><a href="#tab_grouping"  role="tab" data-toggle="tab">Настройки группировок</a></li>
		<li><a id="a_tab_report" href="#tab_report" role="tab" data-toggle="tab">График "Продажи товаров в рознице по периодам"</a></li>
	</ul>
	<div class="floatL">
		<button id="button_report_run" class="btn btn-sm btn-info frameL m0 h40 hidden-print font14">
			<span class="ui-button-text" >Сформировать отчет</span>
		</button>
	</div>
<!--	<div class="floatL">
		<button id="button_report_export" class="btn btn-sm btn-default frameL m0 h40 hidden-print font14 disabled">
			<span class="ui-button-text" >Сохранить</span>
		</button>
	</div>-->
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
					<span class="input-group-addon w130">Интервал:</span>
					<div class="w100p" id="select_interval" name="select_interval"></div>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Показатель:</span>
					<div class="w100p" id="select_data" name="select_data"></div>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm mt5 w100p">
					<span class="input-group-addon w130">Вид графика:</span>
					<div class="w100p" id="select_chart_type" name="select_chart_type"></div>
					<span class="input-group-addon w32"></span>
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
					<span class="input-group-addon w130">Кат.по видам живот.:</span>
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
<!--				<div class="input-group input-group-sm mt20 w100p">
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
				</div>-->
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
					<li class="bc3 ui-corner-all" id="cattypeName">
						<a id="a1" class="floatL ui-icon ui-icon-triangle-1-w mt2 show" type="button"></a>
						<span class="pl5 floatL w80p">Кат.по виду живот.</span>
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
					<li class="bc7 ui-corner-all" id="cc_promoID">
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
				</ul>
			</div>
		</div>
		<div class="tab-pane m0 w100p min530 borderColor borderTop1 frameL center border0" id="tab_report">
			<div id='report_param_str' class="mt10 TAL font14"></div>
			<div id='div_report' class='center frame0 mt10'></div>
			<div id="div_chart" class="w80p h400 floatL">
				<canvas id = "myChart" class=""></canvas>
			</div>
			<div id='chart_legend' class="TAL w20p floatL"></div>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
<div id="dialog_progress" title="Ожидайте!">
	<img class="ml30 mt20 border0 w300" src="../../img/progress_circle5.gif">
</div>
