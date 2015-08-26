<?php
$cnn = new Cnn();
if (isset($_REQUEST['userID'])) {
	$row = $cnn->user_info(null);
	$clientid = $row['ClientID'];
	if ($clientid == null) $clientid = 0;
	$userid = $_REQUEST['userID'];
	if($userid != $_SESSION['UserID']) {
		if ($_SESSION['AccessLevel'] < 2000) return;
	}
}
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
		//if ($("userID").val() == '') return;
		$.post('../engine/user_save', {
		userid: $("#userID").html(),
		clientid: $("#select_point").val(),
		login: $("#login").val(),
		password: $("#password").val(),
		eMail: $("#eMail").val(),
		userName: $("#userName").val(),
		userPhone: $("#userPhone").val(),
		city: $("#city").val(),
		accessLevel: $("#accessLevel").val(),
		position: $("#position").val(),
	    },
			function (data) {
				$("#dialog>#text").html(data.message);
				$("#dialog").dialog("open");
				$("#dialog").dialog({close: function (event, ui) {
					if (data.new_id > 0)
						window.location = "../lists/seller_info?sellerID=" + data.new_id;
					}});
				if (data.new_id > 0) {
					$("#sellerID").val(data.new_id);
					$("#sellerID_span").html(data.new_id);
				}
		    }, "json");
		});
	
	$.post('../Engine/select2?action=point', function (json) {
		$("#select_point").select2({multiple: false, placeholder: "Выберите магазин", data: {results: json, text: 'text'}});
		$("#select_point").select2("val", <?php echo $clientid ?>);
	});

	var lastsel;
	var lastsel2;
	$("#grid1").jqGrid({
		sortable: true,
		datatype: "json",
		height:400,
		colNames:['Код','Уровень','Название','Доступ'],
		colModel: [
			{name: 'MenuID', index: 'MenuID',	width: 60,	align: "center", sorttype: "text", search: true},                         
			{name: 'Level',index: 'Level',  width: 40, align: "center", sorttype: "text", search: true},
		    {name: 'Name', index: 'Name', width: 250, sorttype: "text", search: true},
		    {name: 'Access', index: 'Access', width: 100, align: "center", search: true, editable:true, 
				formatter:'checkbox', edittype:'checkbox', //stype:'select', editoptions:{value:"1:0"}	
				editoptions:{value:'1:0'}, formatoptions:{disabled:false},
				stype:'select', searchoptions : {value : ":любой;1:разрешен;0:запрещен"},
				//edittype:"checkbox", editoptions:{value:"0:1"}}
			},
		],
		beforeSelectRow : function(rowid, elem) {
			if(elem.target.checked==undefined)return;
			if(elem.target.checked==true) checked = 1;
			if(elem.target.checked==false) checked = 0;
			$.post('../engine/menu_users_save', {
				menuid: rowid,
				value: checked
			},
			function (data) {
				if (data == false) {
					$("#dialog>#text").html('Возникла ошибка при сохранении кодов товаров.<br><br>Сообщите разработчику!');
					$("#dialog").dialog("open");
				}else{
					$("#grid1").trigger('reloadGrid');
				}
			});
		},
		width: 546,
		shrinkToFit: false,
		rowNum: 100,
		rowList: [40, 60, 80, 100],
		sortname: "MenuID",
		viewrecords: true,
		gridview: true,
		toppager: true,
		caption: "Список меню",
		pager: '#pgrid1',
	});
	$("#grid1").jqGrid('navGrid', '#pgrid1', {edit: false, add: false, del: false, search: false, refresh: true, cloneToTop: true});
	$("#grid1").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true});

	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');

	$("#grid1").gridResize();

	$("#grid2").jqGrid({
		sortable: true,
		datatype: "json",
		height:400,
		colNames:['Код','Торговая точка','Город','Цена'],
		colModel: [
			{name: 'c_ClientID', index: 'c.ClientID',	width: 60,	align: "center", sorttype: "text", search: true},                         
			{name: 'c_NameShort',index: 'c.NameShort',  width: 250, sorttype: "text", search: true},
		    {name: 'c_City', index: 'c.City', width: 150, sorttype: "text", search: true},
		    {name: 'p_Price', index: 'p.Price', width: 70, align: "right", search: false},
		],
		width: 546,
		shrinkToFit: false,
		rowNum: 20,
		rowList: [20, 30, 40, 50, 100],
		sortname: "ClientID",
		viewrecords: true,
		gridview: true,
		toppager: true,
		caption: "Цена по торговым точкам",
		pager: '#pgrid2',
	});
	$("#grid2").jqGrid('navGrid', '#pgrid2', {edit: false, add: false, del: false, search: false, refresh: true, cloneToTop: true});
	$("#grid2").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true});

	$("#pg_pgrid2").remove();
	$("#pgrid2").removeClass('ui-jqgrid-pager');
	$("#pgrid2").addClass('ui-jqgrid-pager-empty');

	$("#grid2").gridResize();

	$('#myTab a').click(function (e) {
		e.preventDefault();
		if (this.id == 'a_tab_menu') {
			$("#grid1").jqGrid('setGridParam', { url: "../engine/jqgrid3?action=menu&grouping=<?php echo $_SESSION['UserID']?>&f1=MenuID&f2=Level&f3=Name&f4=Access", page: 1});
			$("#grid1").trigger('reloadGrid');
		}
		if (this.id == 'a_tab_point') {
			$("#grid2").jqGrid('setGridParam', {url: "../engine/jqgrid3?action=good_price&p.GoodID=1&f1=ClientID&f2=NameShort&f3=City&f4=PriceShop", page: 1});
		    $("#grid2").trigger('reloadGrid');
		}
    });
	setTimeout(function(){
		$('#a_tab_menu').click();
	}, 100);
});
</script>
<style>
	#feedback { font-size: 12px; }
	.selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	.selectable li { margin: 3px; padding: 7px 0 0 5px; text-align: left;font-size: 14px; height: 34px; }
</style>
<div class="container center">
	<ul id="myTab" class="nav nav-tabs floatL active hidden-print" role="tablist">
		<li class="active"><a href="#tab_info" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;"><legend class="h20">Информация о пользователе</legend></a></li>
<?php
if ($_SESSION['AccessLevel'] >= 2000){
?>
        <li><a id="a_tab_menu" href="#tab_menu" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;">
                <legend class="h20">Доступные меню</legend></a></li>
        <li><a id="a_tab_point" href="#tab_point" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;">
                <legend class="h20">Доступные магазины</legend></a></li>
	</ul>
<?php
}
?>
	<div class="floatL">
		<button id="button_save" class="btn btn-sm btn-success frameL m0 h40 hidden-print font14">
			<span class="ui-button-text" style='width:120px;height:22px;'>Сохранить данные</span>
		</button>
	</div>
<div class="tab-content">
	<div class="active tab-pane min530 m0 w100p ui-corner-tab1 borderTop1 borderColor frameL border1" id="tab_info">
		<div class='p5 ui-corner-all frameL border0 w500' style='display1:table;'>

			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">ID пользователя:</span>
				<span id="userID" name="userID" type="text" class="input-group-addon form-control TAL"><?php echo $row['UserID']; ?></span>
				<span class="input-group-addon w20p"></span>
			</div>

			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">Логин:</span>
				<input id="login" name="login" type="text" class="form-control TAL" value="<?php echo $row['Login']; ?>">
				<span class="input-group-addon w20p"></span>
			</div>               

			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">ФИО</span>
				<input id="userName" name="userName" type="text" class="form-control TAL" value="<?php echo $row['UserName']; ?>">
				<span class="input-group-addon w20p"></span>
			</div>

			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">E-mail:</span>
				<input id="eMail" name="eMail" type="text" class="form-control TAL" value="<?php echo $row['EMail']; ?>">
				<span class="input-group-addon w20p"></span>
			</div>


			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">Должность:</span>
				<input id="position" name="position" type="text" class="form-control TAL" value="<?php echo $row['Position']; ?>">
				<span class="input-group-addon w20p"></span>
			</div>


			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">Телефон:</span>
				<input id="userPhone" name="userPhone" type="text" class="form-control TAL" value ="<?php echo $row['UserPhone']; ?>">
				<span class="input-group-addon w20p"></span>
			</div>

			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">Подразделение:</span>
				<div class="w100p" id="select_point"></div>
				<span class="input-group-addon w20p"></span>
			</div>
			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">Город: </span>
				<input id="city" name="city" type="text" class="form-control TAL" value="<?php echo $row['City']; ?>" disabled>
				<span class="input-group-addon w20p"></span>
			</div>

			<div class="input-group input-group-sm w100p">
				<span class="input-group-addon w25p TAL">Уровень доступа:</span>
				<input id="accessLevel" name="accessLevel" type="text" class="form-control TAL" value="<?php echo $row['AccessLevel'].'"';if ($_SESSION['AccessLevel'] < 2000) echo ' disabled';?>>
				<span class="input-group-addon w20p"></span>
			</div>
		</div>
    </div>
	<div  class="tab-pane min530 m0 w100p ui-corner-all borderTop1 borderColor frameL border1" id="tab_menu">
		<div class='p5'>
			<table id="grid1"></table>
			<div id="pgrid1"></div>
		</div>
	</div> 
	<div  class="tab-pane min530 m0 w100p ui-corner-all borderTop1 borderColor frameL border1" id="tab_point">
		<div class='p5'>
			<table id="grid2"></table>
			<div id="pgrid2"></div>
		</div>
	</div>
</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
