<style>
.listing_container {
	height: auto;
}
.statusDiv{
	float: left;
}
.statusDiv ul{
	padding-left: 15px;
}
.listing_big_image{
	position: relative;
	width: 7%;
}
.listing_container{
	width: 92%;
}
.org_timeline{
	padding: 0 10px;
}
.org_timeline p{
	color: #000;
	font-size: 11px;
}
.listing_head{
	display: inline-block;
	color: #656565;
}
.listing_result{
	display: inline-block;
	float: right;
	color: #118106;
}
</style>

<div class="org_timeline">
	<p class="text-right">Last Update: {{ $lastUpdateFormatedTime }}</p>
</div>
<div class="col-xs-12">
	<div id="at_a_glance">
		<div class="row glance_container">
			{{-- <div class="col-md-12 border_contain"> --}}
			<div class="col-md-12">
				<div style="" class="first_content_column listing_big_image blue_bg animated fadeInLeft">
					<img class="content_logo" src="{{ asset('software/images/dashboards/accDashboard/surplus.png') }}" border="0" width="50px" height="50px">
					{{-- <span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Surplus</span> --}}
				</div>
				<div style="text-align: left;" class="first_content_column listing_container animated fadeInDown">
					<div class="">
						<div class="statusDiv">
							<ul class="blue ul-left">
								<li class="li-left">
									<span class="listing_head">Current Month Surplus</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('currentMonthSurplus'), 2) }}</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Current Year Surplus</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('currentYearSurplus'), 2) }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-right">
								<li class="li-right">
									<span class="listing_head">Last Month Surplus</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('previousMonthSurplus'), 2) }}</span>
								</li>
								<li class="li-right">
									<span class="listing_head">Cumulative Surplus Amount</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('cumulativeSurplus'), 2) }}</span>
								</li>
							</ul>
						</div>
						{{-- <span style="color: green;float: right; margin-top: -44px;">Last Update Date: 10 Oct, 2017 12:48 AM</span> --}}
					</div>
				</div>
			</div>
		</div>

		<div class="row glance_container">
			{{-- <div class="col-md-12 border_contain"> --}}
			<div class="col-md-12">
				<div style="" class="second_content_column listing_big_image blue_bg animated fadeInDown">
					<img class="content_logo" src="{{asset('software/images/dashboards/accDashboard/openingBalance.png')}}" border="0" width="50px" height="50px">
					{{-- <span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Cash & Bank</span> --}}
				</div>
				<div style="text-align: left;" class="second_content_column listing_container animated fadeInUp">
					<div class="">
						<div class="statusDiv">
							<ul class="orange ul-left">
								<li class="li-left">
									<span class="listing_head">Current Cash Amount</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('currentMonthCash'), 2) }}</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Total Balance</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('currentMonthBank'), 2) }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="orange ul-right">
								<li class="li-right">
									<span class="listing_head">Current Bank Amount</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('currentCashAndBank'), 2) }}</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>		{{-- at_a_glance Div --}}

	{{-- graph div --}}
	<style>
	.chart_container{
		border: 1px solid #CCC;
		text-align: center;
		width: 100%;
	}
	.chart_title{
		border: 1px solid #C8C8C8;
		width: 100%;
	}
	.chart_content{
		margin: 5px;
		overflow: auto;
	}
	.highcharts-container{
		border-radius: 5px;
	}
	.chart_box{
		background-color: #F8F8F8;
		border: 1px solid #CCC;
		padding: 15px 22px 15px 15px;
		margin: 15px 0 0 5px;
		width: 100%;
		float: left;
	}
	.chart_box .col-xs-9{
		padding-left: 0;
		padding-right: 0;
	}
	.chart_box .col-xs-3{
		padding-right: 5px;
		padding-left: 0;
	}
	.chart-menu.nav.nav-tabs > li {
		width: 100%;
	}
	.chart-menu.nav.nav-tabs > li > a {
		margin-right: 0;
		min-height: 33.3px;
	}
	.chart-menu li a{
		padding: 7px;
	}
	.chart_title h3{
		padding: 5px 10px;
		padding-left: 20px
	}
	.chart-menu>li>a{
		transition: background-color .15s ease-in;
	}
	.nav.chart-menu>li>a:hover{
		background-color: #c6d0e9;
	}
	.nav.chart-menu>li>a{
		background-color: #f4f4f4;
	}
	.nav.nav-pills>li.active>a, .nav.nav-pills>li.active>a:hover, .nav.nav-pills>li.active>a:focus{
		background-color: #425A6C;
	}
	.nav-stacked>li+li {
		margin-top: 0px;
	}
	.highcharts-credits {
		display: none;
	}
	.nav.nav-tabs + .tab-content {
		margin-bottom: 0;
	}
	.full-chart-link{
		padding-right: 15px;
	}
	.full-chart-link a{
		color: red;
		font-size: 1.05em;
	}
	</style>

	<div class="chart_box">
		<div class="row">
			<div class="col-xs-12">
					{{-- switch tabs div --}}
					<div class="col-xs-3">
						<div class="chart_title chart_column">
							<h3 class="text-bold">Graphical Report</h3>
							<!-- Nav tabs -->
							<ul class="nav nav-tabs nav-pills nav-stacked chart-menu">
								<li class="active">
									<a id="chart1a" href="#chart1" data-toggle="tab">
										Branch Wise Surplus
									</a>
								</li>
								<li>
									<a id="chart2a" href="#chart2" data-toggle="tab">
										Branch Wise Cash And Bank Balance
									</a>
								</li>
								<li>
									<a id="chart3a" href="#chart3" data-toggle="tab">
										Branch Wise Income And Expenses
									</a>
								</li>
							</ul>		{{-- ul nav nav-tabs --}}
						</div>
					</div>

					{{-- chart content divs --}}
					<div class="col-xs-9">
						<div class="chart_container chart_column">
							<div class="tab-content">
								<div class="tab-pane active" id="chart1">

									<div class="row chart_content" id="chartDiv1"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('acc/loadFullChart/Tab1') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart2">

									<div class="row chart_content" id="chartDiv2"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('acc/loadFullChart/Tab2') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart3">

									<div class="row chart_content" id="chartDiv3"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('acc/loadFullChart/Tab3') }}">See all branches</a></div>

								</div>

							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<script>

// script for matching column height
$(document).ready(function(){

	var firstHighestBox = 0;
	var secondHighestBox = 0;
	var thirdHighestBox = 0;
	var fourthHighestBox = 0;
	$('.glance_container .first_content_column').each(function(){
		if($(this).height() > firstHighestBox){
			firstHighestBox = $(this).height();
		}
	});
	$('.glance_container .first_content_column').height(firstHighestBox);

	$('.glance_container .second_content_column').each(function(){
		if($(this).height() > secondHighestBox){
			secondHighestBox = $(this).height();
		}
	});
	$('.glance_container .second_content_column').height(secondHighestBox);

	$('.glance_container .third_content_column').each(function(){
		if($(this).height() > thirdHighestBox){
			thirdHighestBox = $(this).height();
		}
	});
	$('.glance_container .third_content_column').height(thirdHighestBox);

});

// match width before colon
var listingHeadLeftWidth = 0;
var listingHeadRightWidth = 0;
var statusDivLeftWidth = 0;
var statusDivRightWidth = 0;

$('.li-left .listing_head').each(function(){
	if($(this).width() > listingHeadLeftWidth){
		listingHeadLeftWidth = $(this).width();
	}
});
$('.li-left .listing_head').width(listingHeadLeftWidth);

$('.li-right .listing_head').each(function(){
	if($(this).width() > listingHeadRightWidth){
		listingHeadRightWidth = $(this).width();
	}
});
$('.li-right .listing_head').width(listingHeadRightWidth);

$('.statusDiv .ul-left').each(function(){
	if($(this).width() > statusDivLeftWidth){
		statusDivLeftWidth = $(this).width();
	}
});
$('.statusDiv .ul-left').width(statusDivLeftWidth);

$('.statusDiv .ul-right').each(function(){
	if($(this).width() > statusDivRightWidth){
		statusDivRightWidth = $(this).width();
	}
});
$('.statusDiv .ul-right').width(statusDivRightWidth);

// script for centering an element
$(function() {
    $('.content_logo').css({
        'position' : 'absolute',
        'left' : '50%',
        'top' : '50%',
        'margin-left' : -$('.content_logo').outerWidth()/2,
        'margin-top' : -$('.content_logo').outerHeight()/2
    });
});

// load chart items
$(document).ready(function(){

	var loadingDiv='<div align="center" class="chart_column" style="height: 400px;"><img style="position:relative; top: 45%;" src="{{ asset('software/images/dashboards/loading.gif') }}"></div>';

	$("#chartDiv1").html(loadingDiv);
	$("#chartDiv1").load('{{URL::to("acc/loadChart/Tab1")}}');

    $("#chart1a").click(function(){
    	$("#chartDiv1").html(loadingDiv);
    	$("#chartDiv1").load('{{URL::to("acc/loadChart/Tab1")}}');
	});

    $("#chart2a").click(function(){
    	$("#chartDiv2").html(loadingDiv);
    	$("#chartDiv2").load('{{URL::to("acc/loadChart/Tab2")}}');
	});

    $("#chart3a").click(function(){
    	$("#chartDiv3").html(loadingDiv);
    	$("#chartDiv3").load('{{URL::to("acc/loadChart/Tab3")}}');
	});

});

//Stop and start tab moving on hover
$('.chart_content').hover(function() {
	clearInterval(tabCycle);
}, function() {
	tabCycle = setInterval(tabChange, 10000);
});

// chart columns height matching
$(document).ready(function(){

	var fourthHighestBox = 0;

	$('.chart_column').each(function(){
		if($(this).height() > fourthHighestBox){
			fourthHighestBox = $(this).height();
		}
	});
	$('.chart_column').height(fourthHighestBox);

});

</script>
