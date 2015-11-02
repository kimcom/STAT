<!--<link rel="stylesheet" type="text/css" href="../../css/chartist.min.css">
<script src="../../css/chartist.min.js" charset="utf-8"></script>-->
<link rel="stylesheet" href="http://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<div class="ct-chart ct-perfect-fourth"></div>
<script src="http://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
<script>
var data = {
  // A labels array that can contain any sort of values
  labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
  // Our series array that contains series objects or in this case series data arrays
  series: [
    [5, 2, 4, 2, 0]
  ]
};

// Create a new line chart object where as first parameter we pass in a selector
// that is resolving to our chart container element. The Second parameter
// is the actual data object.
//    new Chartist.Line('.ct-chart', data);

var chart = new Chartist.Line('.ct-chart', {
  labels: [1, 2, 3],
	series: [
	    [
		{meta: 'description', value: 1},
		{meta: 'description', value: 5},
		{meta: 'description', value: 3}
	    ],
	    [
		{meta: 'other description', value: 2},
		{meta: 'other description', value: 4},
		{meta: 'other description', value: 2}
	    ]
	]
    }, {
	low: 0,
	high: 8,
	fullWidth: false,
//	plugins: [
//	    Chartist.plugins.tooltip()
//	]
});
</script>
<!--<script type="text/javascript">
$(document).ready(function () {
});
</script>
<div class="container center">

</div>-->
