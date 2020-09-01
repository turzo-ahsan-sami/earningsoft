<div id="container1"></div>

<script type="text/javascript">

// component wise loan info
@php
    $loanInfosByComponentArr = $loanInfosByComponentArr->take(5);
@endphp

$(document).ready(function() {

    var lang = {
        thousandsSep: ','
    };
    var title = {
       text: 'Component Wise Loans Info'
    };
    var subtitle = {
       text: 'Last Update: {{ $lastUpdateFormatedTime }}'
    };
    var xAxis = {

		categories: [
			@foreach ($loanInfosByComponentArr as $key => $component)
			'{{ $component['componentName'] }}',
			@endforeach
		],

    };
    var yAxis = {
       title: {
          text: 'Amount'
       },
       plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
       }]
    };
	var tooltip = {
		headerFormat: '<span style = "font-size:14px">{point.key}</span><table>',
		pointFormat: '<tr><td style = "color:{series.color};padding:0">{series.name}: </td>' + '<td style = "padding:0"><b>{point.y}</b></td></tr>',
		footerFormat: '</table>',
		useHTML: true,
	};
    var legend = {
       layout: 'horizontal',
       align: 'center',
       verticalAlign: 'top',
       borderWidth: 0
    };
    var series =  [{
          name: 'Total Disbursement',
		  data: [
			  @foreach ($loanInfosByComponentArr as $key => $item)
			  {{ $item['totalDisbursement'] }},
			  @endforeach
		  ]
       },
       {
          name: 'Total Recovery',
		  data: [
			  @foreach ($loanInfosByComponentArr as $key => $item)
			  {{ $item['totalRecovery'] }},
			  @endforeach
		  ]
       },
       {
          name: 'Total Outstanding',
		  data: [
			  @foreach ($loanInfosByComponentArr as $key => $item)
			  {{ $item['totalOutstanding'] }},
			  @endforeach
		  ]
       }
    ];

    var json = {};
    json.title = title;
    json.subtitle = subtitle;
    json.lang = lang;
    json.xAxis = xAxis;
    json.yAxis = yAxis;
    json.tooltip = tooltip;
    json.legend = legend;
    json.series = series;

    $('#container1').highcharts(json);
});

</script>
