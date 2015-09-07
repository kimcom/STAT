<?php

?>
<script type="text/javascript">
$(document).ready(function () {
	$("#DT_plan").datepicker({numberOfMonths: 1, dateFormat: 'dd/mm/yy', showButtonPanel: true, closeText: "Закрыть", showAnim: "fold"});	
	dt = new Date();
	dt.setMonth(dt.getMonth() - 1, 1);
	//datepickers обработчик нажатий кнопок
	$("#datapickers a").click(function () {
	if ($(this).attr("type") != 'button')
		return;
	var command = this.parentNode.previousSibling.previousSibling;
	if (command.tagName == 'SPAN')
		command = command.previousSibling.previousSibling;
	if (command.tagName == "INPUT")
		operid = command.id;
	if ($(this).html() == 'X')
		$("#" + operid).val("");
	if ($(this).html() == '...')
		$("#" + operid).datepicker("show");
	});
});
</script>
<input id="sellerID" name="sellerID" type="hidden" value="<?php echo $row['SellerID']; ?>">
<style>
	#feedback { font-size: 12px; }
	.selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	.selectable li { margin: 3px; padding: 7px 0 0 5px; text-align: left;font-size: 14px; height: 34px; }
</style>
<div class="container center">
	<ul id="myTab" class="nav nav-tabs floatL active hidden-print" role="tablist">
		<li class="active"><a href="#tab_filter" role="tab" data-toggle="tab" style="padding-top: 5px; padding-bottom: 5px;"><legend class="h20">Заявка на получение ДС</legend></a></li>
	</ul>
	<div class="floatL">
		<button id="button_save" class="btn btn-sm btn-success frameL m0 h40 hidden-print font14">
			<span class="ui-button-text">Сохранить данные</span>
		</button>
	</div>
	<div class="tab-content">
		<div class="active tab-pane min530 w100p m0 ui-corner-all borderTop1 borderColor frameL border1" id="tab_filter">
			<div class='p5 ui-corner-all frameL border0 w500' style='display1:table;'>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w20p TAL">Заявка на получение ДС:</span>
					<span id="sellerID_span" class="input-group-addon form-control TAC"><?php echo $row['SellerID']; ?></span>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Дата подачи:</span>
					<span id="kod1C_span" class="input-group-addon form-control TAC"><?php echo $row['Kod1C']; ?></span>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p" id="datapickers">
					<span class="input-group-addon w25p TAL">Дата получения средств:</span>
					<input id="DT_plan" name="DT_plan" type="text" class="form-control TAC" value="">
					<span class="input-group-btn"><a class="btn btn-default w100p" type="button">X</a></span>
					<span class="input-group-btn w32"><a class="btn btn-default w100p" type="button">...</a></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Отдел:</span>
					<span id="sellerID_span" class="input-group-addon form-control TAL"><?php echo $row['SellerID']; ?></span>
					<span class="input-group-addon w10p"></span>
				</div>               
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Заявитель:</span>
					<span id="sellerID_span" class="input-group-addon form-control TAL"><?php echo $row['SellerID']; ?></span>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Получатель:</span>
					<input id="nameValid" name="nameValid" type="text" class="form-control TAL" value="<?php echo $row['NameValid']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Сумма заявки:</span>
					<input id="nameValid" name="nameValid" type="text" class="form-control TAR" value="<?php echo $row['NameValid']; ?>">
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Назначение платежа:</span>
					<span id="sellerID_span" class="input-group-addon form-control TAL"><?php echo $row['SellerID']; ?></span>
					<span class="input-group-addon w10p"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon w150 TAL">Описание платежа:</span>
					<textarea id="description" name="description" rows="6" type="text" style="height: auto;" class="form-control TAL p5">
					</textarea>
					<span class="input-group-addon w10p"></span>
				</div>
			</div>
			<!--*<input class="form-control2" type="text">********************-->
			<table class="table table-striped table-bordered">
					<tr>
						<td class="TAC">1.</td>
						<td class="TAL">Подтверждение</td>
						<td class="TAL">Отв.лицо</td>
						<td></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
					<tr>
						<td class="TAC">1.</td>
						<td class="TAL">Оформлена</td>
						<td class="TAL">Отв.лицо</td>
						<td><input type="checkbox"></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
					<tr>
						<td class="TAC">2.</td>
						<td class="TAL">Согласование</td>
						<td class="TAL">Отв.лицо</td>
						<td><input type="checkbox"></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
					<tr>
						<td class="TAC">3.</td>
						<td class="TAL">Подтверждение</td>
						<td class="TAL">Отв.лицо</td>
						<td><input type="checkbox"></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
					<tr>
						<td class="TAC">4.</td>
						<td class="TAL">Выдача ДС</td>
						<td class="TAL">Отв.лицо</td>
						<td><input type="checkbox"></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
					<tr>
						<td class="TAC">5.</td>
						<td class="TAL">Отчет по факту</td>
						<td class="TAL">Отв.лицо</td>
						<td><input type="checkbox"></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
					<tr>
						<td class="TAC">6.</td>
						<td class="TAL">Закрыта</td>
						<td class="TAL">Бухгалтер</td>
						<td><input type="checkbox"></td>
						<td>Дата</td>
						<td>Время</td>
						<td>На согласование</td>
						<td>Отказ</td>
						<td>Причина отказ</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>

<!--			<div class='p5 ui-corner-all frameL ml10 border0 w600' style='float:left;'>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC"></span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС">Примечание</span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC">1.</span>
					<span class="input-group-addon TAL">Оформлена</span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC">2.</span>
					<span class="input-group-addon TAL">Согласование</span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC">3.</span>
					<span class="input-group-addon TAL">Подтверждение</span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC">4.</span>
					<span class="input-group-addon TAL">Выдача ДС</span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC">5.</span>
					<span class="input-group-addon TAL">Отчет по факту</span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
				</div>
				<div class="input-group input-group-sm w100p">
					<span class="input-group-addon TAC">6.</span>
					<span class="input-group-addon TAL">Закрыта</span>
					<span class="input-group-addon TAС">Отв. лицо</span>
					<span class="input-group-addon TAL"></span>
					<span class="input-group-addon TAС">Дата</span>
					<span class="input-group-addon TAС">Время</span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
					<span class="input-group-addon TAС"></span>
				</div>-->




