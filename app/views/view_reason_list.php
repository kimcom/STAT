<script type="text/javascript">
$(document).ready(function(){
//************************************//
	$( "#dialog" ).dialog({
		autoOpen: false, modal: true, width: 400,
		buttons: [{text: "Закрыть", click: function() {$( this ).dialog( "close" );}}]
	});

	fs = 0; lastSel = 0;
// Creating grid1
	$("#grid1").jqGrid({
		sortable: true,
	    url:"../engine/jqgrid3?action=reasons_list&f1=TypeReasonID&f2=Reason&f3=NoShow&UserID=<?php echo $_SESSION['UserID']; ?>",
		datatype: "json",
		height:'auto',
		colNames:['Код','Основание','Статус'],
		colModel:[
			{name:'TypeReasonID',index:'TypeReasonID', width: 60, align:"center", sorttype:"text", search:true, key: true, editable: false},
			{name:'Reason',		index:'Reason', 	  width:300, align:"left",   sorttype:"text", search:true, editable: true, edittype:"text"},
			{name:'NoShow',		index:'NoShow',     width:100, align:"center",   sorttype:"text", search:true, editable: true,
					formatter:'checkbox', edittype:'checkbox', editoptions: {value: '1:0'}, formatoptions: {disabled: true},
				    stype: 'select', searchoptions: {value: ":любой;1:не отображать;0:отображать"},
			},
		],
		gridComplete: function() {if (!fs) {fs = 1;	filter_restore("#grid1");}},
		width:'auto',
		shrinkToFit:false,
		rowNum:20,
		rowList:[20,30,40],
		sortname: "TypeReasonID",
		viewrecords: true,
		toppager: true,
		caption: "Список оснований для ручных скидок",
		pager: '#pgrid1',
		editurl: "../engine/reasons_save",
		grouping: true
	});
	$("#grid1").jqGrid('navGrid','#pgrid1', {edit:true, add:true, del:false, search:false, refresh: true, cloneToTop: true},
		{//edit
			modal:true,
			closeOnEscape:true,
			closeAfterEdit: true,
			reloadAfterSubmit: false,
			viewPagerButtons: false,
			savekey: [true, 13],
			navkeys: [false, 38, 40],
			afterSubmit: function (json, postdata) {
			    var result = $.parseJSON(json.responseText);
			    return [result.success, result.message, result.new_id];
			}
		}, {//add
			modal:true,
		    closeOnEscape:true,
		    closeAfterAdd: true,
		    reloadAfterSubmit: true,
		    afterSubmit : function(json, postdata) {
			    var result = $.parseJSON(json.responseText);
			    return [result.success, result.message, result.new_id];
		    },
		    savekey : [ true, 13 ]
	    }
	);
	$("#grid1").jqGrid('filterToolbar', {autosearch: true, searchOnEnter: true, beforeSearch: function () {filter_save("#grid1");}});
	$("#pg_pgrid1").remove();
	$("#pgrid1").removeClass('ui-jqgrid-pager');
	$("#pgrid1").addClass('ui-jqgrid-pager-empty');
	$("#grid1").gridResize();
});
</script>
<div class="container min570">
	<div style='display:table;'>
		<div id='div1' class='frameL pt5'>
			<table id="grid1"></table>
			<table id="pgrid1"></table>
		</div>
	</div>
</div>
<div id="dialog" title="ВНИМАНИЕ!">
	<p id='text'></p>
</div>
