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
    	<div id="container5"></div>

        <div class="footerTitle" style="border-top:1px solid white"></div>
	</div> {{-- Div col-md-10 fullbody --}}
	<div class="col-md-1"></div>
</div>

@php
foreach ($branchStatusInfos as $key => $branch){
	$numberOfLoanees = $branch->totalMaleLoanee + $branch->totalFemaleLoanee;
	$loaneesArr[] = array(
		'branchName' => $branchInfos->where('id', $branch->branchIdFk)->first()->name,
		'loanees' => $numberOfLoanees,
	);
}
$loanees = collect($loaneesArr);
$loanees = $loanees->sortByDesc('loanees');
// dd($loanees);
@endphp

<script type="text/javascript">

// Branch Wise Total Borrower
$(document).ready(function() {
	var chart = {
		type: 'bar',
		height: 3500
	};
	var credits = {
		enabled: false
	};
	var title = {
		text: 'Branch Wise Total Borrower'
	};
	var subtitle = {
		text: 'Last Update: {{ $lastUpdateFormatedTime }}'
	};
	var xAxis = {

		categories: [
			@foreach ($loanees as $key => $loanee)
			'{{ $loanee['branchName'] }}',
			@endforeach
		],
	};
	var yAxis = {
		title: {
			text: 'Borrower'
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

	var series= [
		{
			name: 'Borrowers',
			data: [
				@foreach ($loanees as $key => $loanee)
				{{ $loanee['loanees'] }},
				@endforeach
			],
			color: '#9b1212'
		}
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
	json.tooltip = tooltip;
	json.xAxis = xAxis;
	json.yAxis = yAxis;
	json.series = series;
	json.credits = credits;
	json.legend = legend;
		$('#container5').highcharts(json);

});

</script>
@endsection
