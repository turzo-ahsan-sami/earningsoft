@extends('layouts/microfin_layout')
@section('content')
@section('title', '| Microfinance Home')

<link rel="stylesheet" href="{{ asset('../resources/views/homePages/homeDashboards.css') }}">
{{-- @include('homePages.accHomePages.accHome') --}}
<style type="text/css">
	.blue li {background: url(../images/dashboards/liBlueCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
	.orange li {background: url(../images/dashboards/liOrangeCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
</style>

<div class="row"  style="padding-bottom: 1%">
	<div class="col-md-1"></div>
	<div class="col-md-10 fullbody">
		<div class="viewTitle" >
        	<a href="{{ url('mfn/home') }}"><img src="{{ asset('images/dashboards/mfnDashboard/mfnDashboard.png') }}"></a>
    	</div>
    	<div id="container1"></div>

        <div class="footerTitle" style="border-top:1px solid white"></div>
	</div> {{-- Div col-md-10 fullbody --}}
	<div class="col-md-1"></div>
</div>

<script type="text/javascript">

// component wise loan info

$(document).ready(function() {
	var chart = {
		type: 'bar',
		height: 700
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
	var credits = {
		enabled: false
	};
	var yAxis = [
		{
			min: 0,
			title: {
				text: 'Amount'
			},
			labels: {
				overflow: 'justify'
			}
		},
		{
			min: 0,
			opposite: true,
			title: {
				text: 'Amount'
			},
			labels: {
				overflow: 'justify'
			}
		}
	];
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
    json.chart = chart;
    json.title = title;
    json.subtitle = subtitle;
    json.xAxis = xAxis;
    json.yAxis = yAxis;
    json.tooltip = tooltip;
    json.legend = legend;
    json.credits = credits;
    json.series = series;

    $('#container1').highcharts(json);
});

</script>

@endsection
