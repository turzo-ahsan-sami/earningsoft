<div id="container2"></div>

@php
$branchStatusInfos = $branchStatusInfos->sortByDesc('currentMonthBank')->where('branchIdFk', '!=', 1)->take(10);
// dd($branchStatusInfos);
@endphp

<script type="text/javascript">

// Branch Wise Total Deposits
$(document).ready(function() {

	var chart = {
               type: 'column'
            };
	var title = {
		text: 'Branch Wise Cash And Bank Balance'
	};
	var subtitle = {
		text: 'Last Update: {{ $lastUpdateFormatedTime }}'
	};
	var xAxis = {
		categories: [
			@foreach ($branchStatusInfos as $key => $branch)
			'{{ $branchInfos->where('id', $branch->branchIdFk)->first()->name }}',
			@endforeach
		],
	};
	var yAxis = {
		title: {
			text: 'Amount (BDT)'
		}
	};
	var plotOptions = {
		column: {
			pointPadding: 0.2,
			borderWidth: 0
		}
	};
	var credits = {
		enabled: false
	};
	var tooltip = {
		headerFormat: '<span style = "font-size:14px">{point.key}</span><table>',
		pointFormat: '<tr><td style = "color:{series.color};padding:0">{series.name}: </td>' + '<td style = "padding:0"><b>{point.y}</b></td></tr>',
		footerFormat: '</table>',
		useHTML: true,
	};
	var series= [
		{
			name: 'Cash',
			data: [
				@foreach ($branchStatusInfos as $key => $branch)
				{{ $branch->currentMonthCash }},
				@endforeach
			]
		},
		{
			name: 'Bank',
			color: 'brown',
			data: [
				@foreach ($branchStatusInfos as $key => $branch)
				{{ $branch->currentMonthBank }},
				@endforeach
			]
		},

	];

	var legend = {
       layout: 'horizontal',
       align: 'center',
       verticalAlign: 'top',
       borderWidth: 0
    };

	var json = {};
		json.chart = chart;
		json.title = title;
		json.subtitle = subtitle;
		json.xAxis = xAxis;
		json.yAxis = yAxis;
		json.tooltip = tooltip;
		json.legend = legend;
		json.plotOptions = plotOptions;
		json.credits = credits;
		json.series = series;
		$('#container2').highcharts(json);

});

</script>
