<div id="container1"></div>
@php
$branchStatusInfos = $branchStatusInfos->sortByDesc('currentYearSurplus')->where('branchIdFk', '!=', 1)->take(10);
@endphp

<script type="text/javascript">

// component wise loan info

$(document).ready(function() {

    var lang = {
        thousandsSep: ','
    };
    var title = {
       text: 'Branch Wise Surplus'
    };
    var subtitle = {
       text: 'As on Current Year Upto: {{ $lastUpdateFormatedTime }}'
    };
    var chart = {
               plotBackgroundColor: null,
               plotBorderWidth: null,
               plotShadow: false,
               type: 'pie',
               backgroundColor: 'rgba(0,0,0,0)',
               y:100
            };

    var plotOptions = {
        pie: {
            y:1,
            shadow: false,
            center: ['50%', '50%'],
            borderWidth: 0,
            showInLegend: false,
            size: '80%',
            // innerSize: '60%',
            // allowPointSelect: true,
            cursor: 'pointer',
            data: [
                @foreach ($branchStatusInfos as $key => $branch)
                ['{{ $branchInfos->where('id', $branch->branchIdFk)->first()->name }}', {{ $branch->currentYearSurplus }} ],
                @endforeach
            ],
        }
    };

	var tooltip = {
		headerFormat: '<span style = "font-size:14px">{point.key}</span><table>',
		pointFormat: '<tr><td style = "color:{series.color};padding:0">{series.name}: </td>' + '<td style = "padding:0"><b>{point.y}</b></td></tr>',
		footerFormat: '</table>',
		useHTML: true,
	};
    // var tooltip = {
    //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    // };
    var legend = {
       layout: 'horizontal',
       align: 'center',
       verticalAlign: 'top',
       borderWidth: 0
    };
    var series =  [
        {
            type: 'pie',
            name: 'Surplus',

            dataLabels: {
                color:'white',
                distance: -20,
                formatter: function () {
                    if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                }
            }
        },
        {
            type: 'pie',
            name: 'Surplus',

            dataLabels: {
                connectorColor: 'grey',
                color:'black',
                //                            y:-10,
                // softConnector: false,
                connectorWidth:1,
                verticalAlign:'top',
                distance: 20,
                formatter: function () {
                    if(this.percentage!=0)  return this.point.name;

                }
            }
        },
    ];

    var json = {};
    json.title = title;
    json.subtitle = subtitle;
    json.lang = lang;
    json.chart = chart;
    json.plotOptions = plotOptions;
    json.tooltip = tooltip;
    json.legend = legend;
    json.series = series;

    $('#container1').highcharts(json);
});

</script>
