<?php
$cnn = new Cnn();
if (isset($_REQUEST['clientID'])) {
	$row = $cnn->point_info();
	if (!$row)
		return;
	//Fn::debugToLog('point attr', $row[1].'	'.  $row['Version']);
} else {
	return;
}
$clientid = $_REQUEST['clientID'];
$balanceActivity = $row['BalanceActivity'];
if ($balanceActivity == null)
	$balanceActivity = 0;
$matrixID = $row['MatrixID'];
if ($matrixID == null)
	$matrixID = 0;
$btn_access = '';
if($_SESSION['AccessLevel']<2000) $btn_access = 'disabled';
?>
<script type="text/javascript">
    $(document).ready(function () {
	$("#dialog").dialog({
		autoOpen: false, modal: true, width: 400,
		buttons: [{text: "Закрыть", click: function () {
			$(this).dialog("close");
		}}]
	});
	$("#button_save").click(function () {
		if ($("#clientID").val() == '')
		return;
		$.post('../engine/point_save', {
		clientID: $("#clientID").val(),
		version: $("#select_version").val(),
		appVersion: $("#appVersion").val(),
		x1C: $("#select_x1C").val(),
		balanceActivity: $("#select_balanceActivity").val(),
		nameShort: $("#nameShort").val(),
		nameValid: $("#nameValid").val(),
		city: $("#city").val(),
		address: $("#address").val(),
		telephone: $("#telephone").val(),
		label: $("#label").val(),
		countTerminal: $("#countTerminal").val(),
		priceType: $("#priceType").val(),
		notes: $("#notes").val(),
		matrixID: $("#select_matrixID").val(),
		},
			function (data) {
			$("#dialog>#text").html(data.message);
			$("#dialog").dialog("open");
			}, "json");
	});

	// выбор версии 
	var a_status = [{id: 0, text: 'ShopV1'}, {id: 1, text: 'ShopV2'}];
	$("#select_version").select2({data: a_status, placeholder: "ShopV2"});
	$("#select_version").select2("val", '<?php echo $row['StatusID']; ?>');

	// выбор 1C
	var a_status = [{id: 'SHOP', text: 'SHOP'}, {id: 'KIEV', text: 'KIEV'}];
	$("#select_x1C").select2({data: a_status, placeholder: ""});
	$("#select_x1C").select2("val", '<?php echo $row['x1C']; ?>');

	// выбор контроля
	var a_status = [{id: 0, text: 'Контроль остатка отключен'}, {id: 1, text: 'Контроль остатка включен'}];
	$("#select_balanceActivity").select2({data: a_status, placeholder: ""});
	$("#select_balanceActivity").select2("val", <?php echo $balanceActivity; ?>);

	// выбор матриці
	var a_status = [{id: 2567, text: 'Матрица A'}, {id: 8553, text: 'Матрица A+'}, {id: 2568, text: 'Матрица B'}, {id: 2571, text: 'Матрица C'}];
	$("#select_matrixID").select2({data: a_status, placeholder: ""});
	$("#select_matrixID").select2("val", <?php echo $matrixID; ?>);

// Creating grid1
	fs = 0;
	$("#grid1").jqGrid({
		sortable: true,
	    url:"../engine/jqgrid3?action=sellers_list&f1=SellerID&f2=Name&f3=Post&f4=Fired&f5=ClientID&f6=City&f7=NameShort&f8=Kod1C&c.ClientID=<?php echo $clientid;?>&UserID=<?php echo $_SESSION['UserID']; ?>",
		datatype: "json",
		height:'auto',
		colNames:['Код','ФИО','Должность','Статус','Код маг.','Город','Магазин', 'Код 1С'],
		colModel:[
			{name:'SellerID',	index:'SellerID', width: 60, align:"center", sorttype:"text", search:true},
			{name:'Name',		index:'Name', 	  width:160, align:"left",   sorttype:"text", search:true},
			{name: 'Post', index: 'Post', width: 100, align: "left", sorttype: "text", search: true},
		{name: 'Fired', index: 'Fired', width: 60, align: "center", sorttype: "text", search: true},
		{name: 'c_ClientID', index: 'c.ClientID', width: 60, align: "center", sorttype: "text", search: true},
		{name: 'c_City', index: 'c.City', width: 100, sorttype: "text", search: true},
		{name: 'c_NameShort', index: 'c.NameShort', width: 160, sorttype: "text", search: true},
		{name: 'Kod1C', index: 'Kod1C', width: 60, align: "center", sorttype: "text", search: true}
	    ],
	    gridComplete: function () {
		if (!fs) {
		    fs = 1;
		    filter_restore("#grid1");
		}
	    },
	    width: 'auto',
	    shrinkToFit: false,
	    rowNum: 20,
	    rowList: [20, 30, 40, 50, 100],
	    sortname: "Name",
	    viewrecords: true,
	    toppager: true,
	    caption: "Список сотрудников",
	    pager: '#pgrid1',
	});
	$("#grid1").jqGrid('navGrid', '#pgrid1', {edit: false, add: false, del: false, search: false, refresh: true, cloneToTop: true});
	$("#grid1").navButtonAdd('#grid1_toppager', {
	    title: 'Добавить сотрудника', buttonicon: "ui-icon-pencil", caption: 'Добавить', position: "last",
	    onClickButton: function () {
		window.location = "../lists/seller_info?sellerID=-1";
	    }
	});
	$("#grid1").navButtonAdd('#grid1_toppager', {
	    title: 'Открыть информационную карту', buttonicon: "ui-icon-pencil", caption: 'Открыть карту', position: "last",
	    onClickButton: function () {
		var id = $("#grid1").jqGrid('getGridParam', 'selrow');
		var node = $("#grid1").jqGrid('getRowData', id);
		//console.log(id,node,node.Name);
		if (id != '')
		    window.location = "../lists/seller_info?sellerID=" + id;
	    }
	});
	$("#grid1").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true, beforeSearch: function () {filter_save("#grid1");}});
	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');
	$("#grid1").gridResize();

//	setTimeout(function(){
//		//$('#a_tab_menu').click();
//		$('#a_tab_sellers').click();
//	}, 100);
});
</script>
<input id="clientID" name="clientID" type="hidden" value="<?php echo $row['ClientID']; ?>">
<style>
	#feedback { font-size: 12px; }
	.selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	.selectable li { margin: 3px; padding: 7px 0 0 5px; text-align: left;font-size: 14px; height: 34px; }
</style>
<div class="container center">
	<ul id="myTab" class="nav nav-tabs floatL active hidden-print" role="tablist">
		<li class="active"><a href="#tab_filter" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;"><legend class="h20">Информация о торговой точке</legend></a></li>
        <li><a id="a_tab_sellers" href="#tab_sellers" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;"><legend class="h20">Список сотрудников</legend></a></li>
	</ul>
	<div class="floatL">
		<button id="button_save" class="btn btn-sm btn-success frameL m0 h40 hidden-print font14 <?php echo $btn_access;?>">
			<span class="ui-button-text" style='width:120px;height:22px;'>Сохранить данные</span>
		</button>
	</div>
	<div class="tab-content">
		<div class="active tab-pane min530 m0 w100p ui-corner-tab1 borderTop1 borderColor frameL border1" id="tab_filter">
			<div class='p5 ui-corner-all frameL border0 w500' style='display1:table;'>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Код торговой точки:</span>
					<span class="input-group-addon form-control TAL"><?php echo $row['ClientID']; ?></span>
<!--					<input id="clientID" name="clientID" type="text" class="form-control TAL" value="<?php echo $row['ClientID']; ?>" disabled>-->
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">ПО:</span>
					<div class="w100p" id="select_version"></div>
					<span class="input-group-addon w10p"></span>
				</div>               
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Версия:</span>
					<input id="appVersion" name="appVersion" type="text" class="form-control TAL" value="<?php echo $row['AppVersion']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">1C:</span>
					<div class="w100p" id="select_x1C"></div>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Контроль:</span>
					<div class="w100p" id="select_balanceActivity"></div>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Дата акт. остатков:</span>
					<span class="input-group-addon form-control TAL"><?php echo $row['DateAct']; ?></span>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Магазин:</span>
					<input id="nameShort" name="nameShort" type="text" class="form-control TAL" value="<?php echo $row['NameShort']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Название:</span>
					<input id="nameValid" name="nameValid" type="text" class="form-control TAL" value="<?php echo $row['NameValid']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
			</div>
			<!--*********************-->
			<div class='p5 ui-corner-all frameL ml10 border0 w500' style='float:left;'>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Город:</span>
					<input id="city" name="city" type="text" class="form-control TAL" value="<?php echo $row['City']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Адрес:</span>
					<input id="address" name="address" type="text" class="form-control TAL" value="<?php echo $row['Address']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Телефон:</span>
					<input id="telephone" name="telephone" type="text" class="form-control TAL" value ="<?php echo $row['Telephone']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Вид собственности: </span>
					<input id="label" name="label" type="text" class="form-control TAL" value="<?php echo $row['Label']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">К-во комп: </span>
					<input id="countTerminal" name="countTerminal" type="text" class="form-control TAL" value="<?php echo $row['CountTerminal']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Тип цены: </span>
					<input id="priceType" name="priceType" type="text" class="form-control TAL" value="<?php echo $row['PriceType']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Матрица:</span>
					<div class="w100p" id="select_matrixID"></div>
					<span class="input-group-addon w10p"></span>
				</div>
			</div>
		</div>
		<div  class="tab-pane min530 m0 w100p ui-corner-all borderTop1 borderColor frameL border1" id="tab_sellers">
			<div class='p5'>
				<table id="grid1"></table>
				<div id="pgrid1"></div>
			</div>
		</div> 
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
