<script src="http://ccchart.com/js/ccchart.js" charset="utf-8"></script>
<canvas id="hoge"></canvas>
<script>
    var chartdata2 = {
	"config": {
	    "title": "Line Chart",
	    "subTitle": "Canvasを使ったシンプルなラインチャートです",
	    "type": "line",
	    "lineWidth": 4,
	    "colorSet":
		    ["red", "#FF9114", "#3CB000", "#00A8A2", "#0036C0", "#C328FF", "#FF34C0"],
	    "bgGradient": {
		"direction": "vertical",
		"from": "#687478",
		"to": "#222222"
	    },
	    "useMarker": "css-ring",
	    "markerWidth": 12
	},
	"data": [
	    ["年度", 2007, 2008, 2009, 2010, 2011, 2012, 2013],
	    ["紅茶", 435, 332, 524, 688, 774, 825, 999],
	    ["コーヒー", 600, 335, 584, 333, 457, 788, 900],
	    ["ジュース", 60, 435, 456, 352, 567, 678, 1260],
	    ["ウーロン", 200, 123, 312, 200, 402, 300, 512]
	]
    };
    ccchart.init('hoge', chartdata2)
</script>
<!--<script type="text/javascript">
$(document).ready(function () {
});
</script>
<div class="container center">

</div>-->
