<div id="container5"></div>
@php
foreach ($branchStatusInfos as $key => $branch){
	$numberOfLoanees = $branch->totalMaleLoanee + $branch->totalFemaleLoanee;
	$loaneesArr[] = array(
		'branchName' => $branchInfos->where('id', $branch->branchIdFk)->first()->name,
		'loanees' => $numberOfLoanees,
	);
}
$loanees = collect($loaneesArr);
$loanees = $loanees->sortByDesc('loanees')->take(10);
// dd($loanees);
@endphp

<script type="text/javascript">

// Branch Wise Total Borrower
$(document).ready(function() {

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
			type: 'column',
			name: 'Borrowers',
			color: 'brown',
			data: [
				@foreach ($loanees as $key => $loanee)
				{{ $loanee['loanees'] }},
				@endforeach
			]
		},
		{
			type: 'line',
			name: 'Borrowers',
			data: [
				@foreach ($loanees as $key => $loanee)
				{{ $loanee['loanees'] }},
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
		$('#container5').highcharts(json);

});

</script>
