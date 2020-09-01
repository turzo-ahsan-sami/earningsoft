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
	color: #656565;
}
.listing_result{
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
							<ul class="blue">
								<li>
									<span class="listing_head">Branch :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalBranch'] }}</span>
								</li>
								<li>
									<span class="listing_head">Samity :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalSamity'] }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue">
								<li>
									<span class="listing_head">Active Member</span>
								</li>
								<li>
									<span class="listing_head">Total :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalActiveMember'] }}</span>
								</li>
								<li>
									<span class="listing_head">Male :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalActiveMaleMember'] }}</span>
								</li>
								<li>
									<span class="listing_head">Female :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalActiveFemaleMember'] }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue">
								<li>
									<span class="listing_head">Inactive Member</span>
								</li>
								<li>
									<span class="listing_head">Total :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalInctiveMember'] }}</span>
								</li>
								<li>
									<span class="listing_head">Male :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalInctiveMaleMember'] }}</span>
								</li>
								<li>
									<span class="listing_head">Female :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalInctiveFemaleMember'] }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue">
								<li>
									<span class="listing_head">Total Member :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalMember'] }}</span>
								</li>
								<li>
									<span class="listing_head">Total Closed Member :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalClosedMember'] }}</span>
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
							<ul class="orange">
								<li>
									<span class="listing_head">Today's Deposit :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayDeposit') }}</span>
								</li>
								<li>
									<span class="listing_head">Today's Refund :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayRefund') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="orange">
								<li>
									<span class="listing_head">Total Saving balance :</span>
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
							<ul class="blue">
								<li>
									<span class="listing_head">Total Borrower :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalLoanee'] }}</span>
								</li>
								<li>
									<span class="listing_head">Male :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalMaleLoanee'] }}</span>
								</li>
								<li>
									<span class="listing_head">Female :</span>
									<span class="listing_result">{{ $organisationGeneralInfo['totalFemaleLoanee'] }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue">
								<li>
									<span class="listing_head">Today's Disbursement :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayDisbursement') }}</span>
								</li>
								<li>
									<span class="listing_head">Today's Recovery :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayRecovery') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue">
								<li>
									<span class="listing_head">Today's Due :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('todayDueAmount') }}</span>
								</li>
								<li>
									<span class="listing_head">Total Due :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalDueAmount') }}</span>
								</li>
								<li>
									<span class="listing_head">Current Due :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('currentDueAmount') }}</span>
								</li>
								<li>
									<span class="listing_head">Total Overdue :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalOverdue') }}</span>
								</li>
							</ul>
						</div>
						<div class="statusDiv">
							<ul class="blue">
								<li>
									<span class="listing_head">Total Cumulative Disbursement :</span>
									<span class="listing_result">{{ $branchStatusInfos->sum('totalCumulativeDisbursement') }}</span>
								</li>
								<li>
									<span class="listing_head">Total Outstanding :</span>
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
	.chart-menu li a{
		padding: 5px;
	}
	.chart_title h4{
		padding: 5px;
	}
	.chart-menu>li>a{
		transition: background-color .15s ease-in;
	}
	.chart-menu>li>a:hover, .chart-menu>li>a:focus{
		background-color: #c6d0e9;
	}
	.nav-pills>li.active>a, .nav-pills>li.active>a:hover, .nav-pills>li.active>a:focus{
		background-color: #425A6C;
	}
	.nav-stacked>li+li {
		margin-top: 0px;
	}
	.highcharts-credits {
		display: none;
	}
	</style>

	<div class="chart_box">
		<div class="row">
			<div class="col-xs-12">
					{{-- switch tabs div --}}
					<div class="col-xs-3">
						<div class="chart_title">
							<h4 class="text-bold">Graphical Report</h4>
							<!-- Nav tabs -->
							<ul class="nav nav-pills nav-stacked chart-menu">
								<li class="active">
									<a href="#container" data-target-id="container1">
										Component Wise Loans Info
									</a>
								</li>
								<li><a href="" data-target-id="container2">Branch Wise Total Active Member</a></li>
								<li><a href="" data-target-id="container3">Branch Wise Total Deposit</a></li>
								<li><a href="" data-target-id="container4">Branch Wise Total Refund</a></li>
								<li><a href="" data-target-id="container5">Branch Wise Total Borrower</a></li>
								<li><a href="" data-target-id="container6">Branch Wise Total Disbursement</a></li>
								<li><a href="" data-target-id="container7">Branch Wise Total Recovery</a></li>
								<li><a href="" data-target-id="container8">Branch Wise Total Due</a></li>
								<li><a href="" data-target-id="container9">Branch Wise Overdue</a></li>
								<li><a href="" data-target-id="container10">Branch Wise Current Due</a></li>
								<li><a href="" data-target-id="container11">Branch Wise Total Outstanding</a></li>
							</ul>
						</div>
					</div>

					{{-- chart content divs --}}
					<div class="col-xs-9">
						<div class="chart_container">
							<div class="chart_content" id="container1"></div>
							<div class="chart_content" id="container2"></div>
							<div class="chart_content" id="container3"></div>
							<div class="chart_content" id="container4"></div>
							<div class="chart_content" id="container5"></div>
							<div class="chart_content" id="container6"></div>
							<div class="chart_content" id="container7"></div>
							<div class="chart_content" id="container8"></div>
							<div class="chart_content" id="container9"></div>
							<div class="chart_content" id="container10"></div>
							<div class="chart_content" id="container11"></div>
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
</script>
@include('homePages/mfnHomePages/orgStatusChartsScript')
