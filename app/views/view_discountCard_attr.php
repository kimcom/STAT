<?php
$cnn = new Cnn();
if (isset($_REQUEST['cardid'])) {
	$cardid = $_REQUEST['cardid'];
	$row = $cnn->discountcard_info();
} else {
	echo "не могу найти параметр cardid";
	return;
}
?>
<?php echo $row['DateOfIssue']; ?>
<script type="text/javascript">
$(document).ready(function () {
	$("#dialog").dialog({
	    autoOpen: false, modal: true, width: 400,
	    buttons: [{text: "Закрыть", click: function () {
			$(this).dialog("close");
	    }}]
	});
	$("#button_save").click(function () {
	    if ($("#cardid").val() == '') return;
	    $.post('../engine/discoundcard_save', {
			cardid: $("#cardid").val(),
			name: $("#name").val(),
			dateOfIssue: $("#dateOfIssue").val(),
			dateOfCancellation: $("#dateOfCancellation").val(),
			clientID: $("#select_point").val(),
			address: $("#address").val(),
			eMail: $("#eMail").val(),
			phone: $("#phone").val(),
			animal: $("#animal").val(),
			startPercent: $("#startPercent").val(),
			startSum: $("#startSum").val(),
			dopSum: $("#dopSum").val(),
			percentOfDiscount: $("#percentOfDiscount").val(),
			howWeLearn: $("#howWeLearn").val(),
			notes: $("#notes").val()
	    },
	    function (data) {
			$("#dialog>#text").html(data.message);
			$("#dialog").dialog("open");
	    },"json");
	});
	
	// выбор магазина 
	$.post('../Engine/select2?action=point', function (json) {
		$("#select_point").select2({multiple: false, placeholder: "Выберите магазин", data: {results: json, text: 'text'}});
		$("#select_point").select2("val", "<?php echo $row['ClientID']; ?>");
	});
	
	// выбор даты выдачи 
	$("#DT_issue").datepicker({numberOfMonths: 1, dateFormat: 'dd/mm/yy', showButtonPanel: true, closeText: "Закрыть", showAnim: "fold"});

});
</script>
<input id="cardid" name="cardid" type="hidden" value="<?php echo $row['CardID']; ?>">
<style>
 #feedback { font-size: 12px; }
 .selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
 .selectable li { margin: 3px; padding: 7px 0 0 5px; text-align: left;font-size: 14px; height: 34px; }
</style>
<div class="container center">
	<ul id="myTab" class="nav nav-tabs floatL active hidden-print" role="tablist">
		<li class="active"><a href="#tab_filter" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;"><legend class="h20">Информация о дисконтной карте</legend></a></li>
	</ul>
	<div class="floatL">
		<button id="button_save" class="btn btn-sm btn-success frameL m0 h40 hidden-print font14">
			<span class="ui-button-text" style='width:120px;height:22px;'>Сохранить данные</span>
		</button>
	</div>
	<div class="tab-content">
		<div class="active tab-pane min530 m0 w100p ui-corner-all borderTop1 borderColor frameL border1" id="tab_filter">
			<div class='p5 ui-corner-all frameL border0 w400' style='display1:table;'>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Дисконтная карта:</span>
					<span class="input-group-addon form-control TAL"><?php echo $row['CardID']; ?></span>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Ф.И.О. клиента:</span>
					<input id="name" name="name" type="text" class="form-control TAL" value="<?php echo $row['Name']; ?>">
					<span class="input-group-addon w32"></span>
				</div>               
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Дата выдачи:</span>
					<input id="DT_issue" name="DT_issue" type="text" class="form-control TAL" value="<?php echo $row['DateOfIssue']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Дата анулирования:</span>
					<input id="dateOfCancellation" name="dateOfCancellation" type="text" class="form-control TAL" value="<?php echo $row['DateOfCancellation']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Магазин:</span>
					<div class="w100p" id="select_point"></div>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Адрес:</span>
					<input id="address" name="address" type="text" class="form-control TAL" value="<?php echo $row['Address']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">EMail:</span>
					<input id="eMail" name="eMail" type="text" class="form-control TAL" value="<?php echo $row['EMail']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Телефон:</span>
					<input id="phone" name="phone" type="text" class="form-control TAL" value="<?php echo $row['Phone']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Животное: </span>
					<input id="animal" name="animal" type="text" class="form-control TAL" value="<?php echo $row['Animal']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Причина выдачи:</span>
					<input id="howWeLearn" name="howWeLearn" type="text" class="form-control TAL" value="<?php echo $row['HowWeLearn']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w25p TAL">Примечание:</span>
					<input id="notes" name="notes" type="text" class="form-control TAL" value="<?php echo $row['Notes']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
			</div>
			<!--*********************-->
			<div class='p5 ui-corner-all frameL ml10 border0 w300' style='float:left;'>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w130 TAL">Стартовый процент:</span>
					<input id="startPercent" name="startPercent" type="text" class="form-control TAR" value="<?php echo $row['StartPercent']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w130 TAL">Стартовая сумма:</span>
					<input id="startSum" name="startSum" type="text" class="form-control TAR" value="<?php echo $row['StartSum']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w130 TAL">Доп. сумма:</span>
					<input id="dopSum" name="dopSum" type="text" class="form-control TAR" value ="<?php echo $row['DopSum']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w130 TAL">Сумма покупок: </span>
					<span class="input-group-addon form-control TAR"><?php echo $row['SummaAmount']; ?></span>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w130 TAL">Сумма накопления: </span>
					<span class="input-group-addon form-control TAR"><?php echo $row['AmountOfBuying']; ?></span>
					<span class="input-group-addon w32"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w130 TAL">% скидки: </span>
					<input id="percentOfDiscount" name="percentOfDiscount" type="text" class="form-control TAR" value="<?php echo $row['PercentOfDiscount']; ?>">
					<span class="input-group-addon w32"></span>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
