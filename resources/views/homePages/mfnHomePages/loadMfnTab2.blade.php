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
					<img class="content_logo" src="{{ asset('images/dashboards/accDashboard/surplus.png') }}" border="0" width="50px" height="50px">
					{{-- <span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Surplus</span> --}}
				</div>
				<div style="text-align: left;" class="first_content_column listing_container animated fadeInDown">
					<div class="">
						<div class="statusDiv">
							<ul class="blue ul-left">
								<li class="li-left">
									<span class="listing_head">Branch</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $totalBranch }}</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Samity</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $totalSamity }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-middle-left">
								<li class="li-middle-left">
									<span class="listing_head">Active Member</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">
										{{ $branchStatusInfos->sum('totalActiveMaleMember') + $branchStatusInfos->sum('totalActiveFemaleMember') }}
									</span>
								</li>
								<li class="li-middle-left">
									<span class="listing_head">Male</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalActiveMaleMember') }}</span>
								</li>
								<li class="li-middle-left">
									<span class="listing_head">Female</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalActiveFemaleMember') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-middle-right">
								<li class="li-middle-right">
									<span class="listing_head">Inactive Member</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">
										{{ $branchStatusInfos->sum('totalInctiveMaleMember') + $branchStatusInfos->sum('totalInctiveFemaleMember') }}
									</span>
								</li>
								<li class="li-middle-right">
									<span class="listing_head">Male</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalInctiveMaleMember') }}</span>
								</li>
								<li class="li-middle-right">
									<span class="listing_head">Female</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalInctiveFemaleMember') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-right">
								<li class="li-right">
									<span class="listing_head">Total Member</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalMember') }}</span>
								</li>
								<li class="li-right">
									<span class="listing_head">Total Closed Member</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalClosedMember') }}</span>
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
					<img class="content_logo" src="{{asset('images/dashboards/accDashboard/openingBalance.png')}}" border="0" width="50px" height="50px">
					{{-- <span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Cash & Bank</span> --}}
				</div>
				<div style="text-align: left;" class="second_content_column listing_container animated fadeInUp">
					<div class="">
						<div class="statusDiv">
							<ul class="orange ul-left">
								<li class="li-left">
									<span class="listing_head">Today's Deposit</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayDeposit') }}</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Today's Refund</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayRefund') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="orange ul-middle-left">
								<li class="li-middle-left">
									<span class="listing_head">Total Saving balance</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalSavingBalance') }}</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row glance_container">
			{{-- <div class="col-md-12 border_contain"> --}}
			<div class="col-md-12">
				<div style="" class="third_content_column listing_big_image blue_bg animated fadeInLeft">
					<img class="content_logo" src="{{ asset('images/dashboards/accDashboard/surplus.png') }}" border="0" width="50px" height="50px">
					{{-- <span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Surplus</span> --}}
				</div>
				<div style="text-align: left;" class="third_content_column listing_container animated fadeInDown">
					<div class="">
						<div class="statusDiv">
							<ul class="blue ul-left">
								<li class="li-left">
									<span class="listing_head">Total Borrower</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">
										{{ $branchStatusInfos->sum('totalMaleLoanee') + $branchStatusInfos->sum('totalFemaleLoanee') }}
									</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Male</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalMaleLoanee') }}</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Female</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalFemaleLoanee') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-middle-left">
								<li class="li-middle-left">
									<span class="listing_head">Today's Disbursement</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayDisbursement') }}</span>
								</li>
								<li class="li-middle-left">
									<span class="listing_head">Today's Recovery</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayRecovery') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-middle-right">
								<li class="li-middle-right">
									<span class="listing_head">Today's Due</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayDueAmount') }}</span>
								</li>
								<li class="li-middle-right">
									<span class="listing_head">Total Due</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalDueAmount') }}</span>
								</li>
								<li class="li-middle-right">
									<span class="listing_head">Current Due</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('currentDueAmount') }}</span>
								</li>
								<li class="li-middle-right">
									<span class="listing_head">Total Overdue</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalOverdue') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue ul-right">
								<li class="li-right">
									<span class="listing_head">Cumulative Disbursement</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalCumulativeDisbursement') }}</span>
								</li>
								<li class="li-right">
									<span class="listing_head">Total Outstanding</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalOutstanding') }}</span>
								</li>
							</ul>
						</div>

						{{-- <span style="color: green;float: right; margin-top: -44px;">Last Update Date: 10 Oct, 2017 12:48 AM</span> --}}
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
										Component Wise Loans Info
									</a>
								</li>
								<li>
									<a id="chart2a" href="#chart2" data-toggle="tab">
										Branch Wise Total Active Member
									</a>
								</li>
								<li>
									<a id="chart3a" href="#chart3" data-toggle="tab">
										Branch Wise Total Deposit
									</a>
								</li>
								<li>
									<a id="chart4a" href="#chart4" data-toggle="tab">
										Branch Wise Total Refund
									</a>
								</li>
								<li>
									<a id="chart5a" href="#chart5" data-toggle="tab">
										Branch Wise Total Borrower
									</a>
								</li>
								<li>
									<a id="chart6a" href="#chart6" data-toggle="tab">
										Branch Wise Total Disbursement
									</a>
								</li>
								<li>
									<a id="chart7a" href="#chart7" data-toggle="tab">
										Branch Wise Total Recovery
									</a>
								</li>
								<li>
									<a id="chart8a" href="#chart8" data-toggle="tab">
										Branch Wise Total Due
									</a>
								</li>
								<li>
									<a id="chart9a" href="#chart9" data-toggle="tab">
										Branch Wise Overdue
									</a>
								</li>
								<li>
									<a id="chart10a" href="#chart10" data-toggle="tab">
										Branch Wise Current Due
									</a>
								</li>
								<li>
									<a id="chart11a" href="#chart11" data-toggle="tab">
										Branch Wise Total Outstanding
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
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab1') }}">See all components</a></div>

								</div>
								<div class="tab-pane" id="chart2">

									<div class="row chart_content" id="chartDiv2"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab2') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart3">

									<div class="row chart_content" id="chartDiv3"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab3') }}">See all branches</a></div>

								</div>

								<div class="tab-pane" id="chart4">

									<div class="row chart_content" id="chartDiv4"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab4') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart5">

									<div class="row chart_content" id="chartDiv5"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab5') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart6">

									<div class="row chart_content" id="chartDiv6"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab6') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart7">

									<div class="row chart_content" id="chartDiv7"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab7') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart8">

									<div class="row chart_content" id="chartDiv8"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab8') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart9">

									<div class="row chart_content" id="chartDiv9"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab9') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart10">

									<div class="row chart_content" id="chartDiv10"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab10') }}">See all branches</a></div>

								</div>
								<div class="tab-pane" id="chart11">

									<div class="row chart_content" id="chartDiv11"></div>
									<div class="text-right full-chart-link"><a target="_blank" href="{{ url('mfn/loadFullChart/Tab11') }}">See all branches</a></div>

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
var listingHeadMIddleLeftWidth = 0;
var listingHeadMiddleRightWidth = 0;
var listingHeadRightWidth = 0;
var statusDivLeftWidth = 0;
var statusDivMiddleLeftWidth = 0;
var statusDivMiddleRightWidth = 0;
var statusDivRightWidth = 0;

$('.li-left .listing_head').each(function(){
	if($(this).width() > listingHeadLeftWidth){
		listingHeadLeftWidth = $(this).width();
	}
});
$('.li-left .listing_head').width(listingHeadLeftWidth);

$('.li-middle-left .listing_head').each(function(){
	if($(this).width() > listingHeadMIddleLeftWidth){
		listingHeadMIddleLeftWidth = $(this).width();
	}
});
$('.li-middle-left .listing_head').width(listingHeadMIddleLeftWidth);

$('.li-middle-right .listing_head').each(function(){
	if($(this).width() > listingHeadMiddleRightWidth){
		listingHeadMiddleRightWidth = $(this).width();
	}
});
$('.li-middle-right .listing_head').width(listingHeadMiddleRightWidth);

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

$('.statusDiv .ul-middle-left').each(function(){
	if($(this).width() > statusDivMiddleLeftWidth){
		statusDivMiddleLeftWidth = $(this).width();
	}
});
$('.statusDiv .ul-middle-left').width(statusDivMiddleLeftWidth);

$('.statusDiv .ul-middle-right').each(function(){
	if($(this).width() > statusDivMiddleRightWidth){
		statusDivMiddleRightWidth = $(this).width();
	}
});
$('.statusDiv .ul-middle-right').width(statusDivMiddleRightWidth);

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

	var loadingDiv='<div align="center" class="chart_column" style="height: 400px;"><img style="position:relative; top: 45%;" src="{{ asset('images/dashboards/loading.gif') }}"></div>';

	$("#chartDiv1").html(loadingDiv);
	$("#chartDiv1").load('{{URL::to("mfn/loadChart/Tab1")}}');

    $("#chart1a").click(function(){
    	$("#chartDiv1").html(loadingDiv);
    	$("#chartDiv1").load('{{URL::to("mfn/loadChart/Tab1")}}');
	});

    $("#chart2a").click(function(){
    	$("#chartDiv2").html(loadingDiv);
    	$("#chartDiv2").load('{{URL::to("mfn/loadChart/Tab2")}}');
	});

    $("#chart3a").click(function(){
    	$("#chartDiv3").html(loadingDiv);
    	$("#chartDiv3").load('{{URL::to("mfn/loadChart/Tab3")}}');
	});

    $("#chart4a").click(function(){
    	$("#chartDiv4").html(loadingDiv);
    	$("#chartDiv4").load('{{URL::to("mfn/loadChart/Tab4")}}');
	});

    $("#chart5a").click(function(){
    	$("#chartDiv5").html(loadingDiv);
    	$("#chartDiv5").load('{{URL::to("mfn/loadChart/Tab5")}}');
	});

    $("#chart6a").click(function(){
    	$("#chartDiv6").html(loadingDiv);
    	$("#chartDiv6").load('{{URL::to("mfn/loadChart/Tab6")}}');
	});

    $("#chart6a").click(function(){
    	$("#chartDiv6").html(loadingDiv);
    	$("#chartDiv6").load('{{URL::to("mfn/loadChart/Tab6")}}');
	});

    $("#chart7a").click(function(){
    	$("#chartDiv7").html(loadingDiv);
    	$("#chartDiv7").load('{{URL::to("mfn/loadChart/Tab7")}}');
	});

    $("#chart8a").click(function(){
    	$("#chartDiv8").html(loadingDiv);
    	$("#chartDiv8").load('{{URL::to("mfn/loadChart/Tab8")}}');
	});

    $("#chart9a").click(function(){
    	$("#chartDiv9").html(loadingDiv);
    	$("#chartDiv9").load('{{URL::to("mfn/loadChart/Tab9")}}');
	});

    $("#chart10a").click(function(){
    	$("#chartDiv10").html(loadingDiv);
    	$("#chartDiv10").load('{{URL::to("mfn/loadChart/Tab10")}}');
	});

    $("#chart11a").click(function(){
    	$("#chartDiv11").html(loadingDiv);
    	$("#chartDiv11").load('{{URL::to("mfn/loadChart/Tab11")}}');
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
