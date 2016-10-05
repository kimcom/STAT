function filter_reset(gridName) {
    section = decodeURIComponent(window.location.pathname.split('/')[1]);
    if (window.location.pathname.split('/')[2] !== undefined) {
	section += '_' + decodeURIComponent(window.location.pathname.split('/')[2]);
    }
    $.post('../Engine/filter_reset', {section: section, gridid: gridName});
}
function filter_save(gridName){
    section = decodeURIComponent(window.location.pathname.split('/')[1]);
    if(window.location.pathname.split('/')[2]!==undefined){
	section += '_'+decodeURIComponent(window.location.pathname.split('/')[2]);
    }
    p = $(gridName).jqGrid('getGridParam','postData');
    filter = JSON.stringify(p);
    $.post('../Engine/filter_save',{ section: section, gridid: gridName, filter: filter });
}
filter_restore = function (gridName, defFilter, valFilter){
    section = decodeURIComponent(window.location.pathname.split('/')[1]);
    if (window.location.pathname.split('/')[2] !== undefined) {
	section += '_' + decodeURIComponent(window.location.pathname.split('/')[2]);
    }
    $.post('../Engine/filter_restore',{ section: section, gridid: gridName },
	function(filter){
	    var p = jQuery.parseJSON(filter);
	    if(p.success){
		var post = $(gridName).jqGrid('getGridParam', 'postData');
		filter = jQuery.parseJSON(p.data);
		$.each(filter,function(key,value) {
			key = key.replace('.','_');
			$('#gs_'+key).val(value);
			post[key] = value;
		});
		if (defFilter != undefined) {
		    if (filter[defFilter] == undefined) {
			filter[defFilter] = valFilter;
			$('#gs_'+defFilter).val(valFilter);
		    }
		}
//		filter._search = true;
//		filter.nd = null;
		//$(gridName).jqGrid('setGridParam',{'postData':filter}).trigger("reloadGrid");
		post._search = true;
		post.nd = Math.random().toString().replace('0.','');
		$(gridName).jqGrid('setGridParam',{'postData':post, datatype: "json"}).trigger("reloadGrid");
	    }else{
		$(gridName).jqGrid('setGridParam', {datatype: "json"}).trigger("reloadGrid");
	    }
	}
    );
}
