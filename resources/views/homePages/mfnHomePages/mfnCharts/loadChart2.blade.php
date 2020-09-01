<div id="container2"></div>

@php
foreach ($branchStatusInfos as $key => $branch){
	$numberOfMembers = $branch->totalActiveMaleMember + $branch->totalActiveFemaleMember;
	$membersArr[] = array(
		'branchName' => $branchInfos->where('id', $branch->branchIdFk)->first()->name,
		'members' => $numberOfMembers,
	);
}

$members = collect($membersArr);
$members = $members->sortByDesc('members')->take(10);
@endphp

<script type="text/javascript">

// Branch Wise Total Active Member
$(document).ready(function() {

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
	};
	var yAxis = {
		title: {
			text: 'Active/Inactive Member'
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
			type: 'column',
			name: 'Active Members',
			color: 'brown',
			data: [
				@foreach ($members as $key => $member)
				{{ $member['members'] }},
				@endforeach
			]
		},
		{
			type: 'line',
			name: 'Active Members',
			data: [
				@foreach ($members as $key => $member)
				{{ $member['members'] }},
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
		json.title = title;
		json.subtitle = subtitle;
		json.xAxis = xAxis;
		json.yAxis = yAxis;
		json.tooltip = tooltip;
		json.legend = legend;
		json.series = series;
		$('#container2').highcharts(json);

});

</script>
