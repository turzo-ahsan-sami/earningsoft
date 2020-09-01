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
    	<div id="container2"></div>

        <div class="footerTitle" style="border-top:1px solid white"></div>
	</div> {{-- Div col-md-10 fullbody --}}
	<div class="col-md-1"></div>
</div>

<script type="text/javascript">

@php
foreach ($branchStatusInfos as $key => $branch){
	$numberOfMembers = $branch->totalActiveMaleMember + $branch->totalActiveFemaleMember;
	$membersArr[] = array(
		'branchName' => $branchInfos->where('id', $branch->branchIdFk)->first()->name,
		'members' => $numberOfMembers,
	);
}

$members = collect($membersArr);
$members = $members->sortByDesc('members');
@endphp

// Branch Wise Total Active Member
$(document).ready(function() {

	var chart = {
		type: 'bar',
		height: 3500
	};
	var credits = {
		enabled: false
	};
	var title = {
		text: 'Branch Wise Total Active Member'
	};
	var subtitle = {
		text: 'Last Update: {{ $lastUpdateFormatedTime }}'
	};
	var xAxis = {

		categories: [
			@foreach ($members as $key => $member)
			'{{ $member['branchName'] }}',
			@endforeach
		],
		title: {
			text: null
		},
	};
	var yAxis = {
		min: 0,
		title: {
			text: 'Active/Inactive Member',
		},
		labels: {
			overflow: 'justify'
		}
	};
	var tooltip = {
		headerFormat: '<span style = "font-size:14px">{point.key}</span><table>',
		pointFormat: '<tr><td style = "color:{series.color};padding:0">{series.name}: </td>' + '<td style = "padding:0"><b>{point.y}</b></td></tr>',
		footerFormat: '</table>',
		useHTML: true,
	};

	var series= [
		{
			name: 'Active Members',
			data: [
				@foreach ($members as $key => $member)
				{{ $member['members'] }},
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
	json.legend = legend;
	json.credits = credits;
	$('#container2').highcharts(json);

});

</script>

@endsection
