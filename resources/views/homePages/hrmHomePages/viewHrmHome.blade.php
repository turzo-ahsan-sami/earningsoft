@extends('hr_main')
@section('title', '| HRM Home')
@section('content')
{{-- <link rel="stylesheet" href="{{ asset('../resources/views/homePages/mfnHomePages/mfnHome.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('../resources/views/homePages/homeDashboards.css') }}">
<link rel="stylesheet" href="{{ asset('../resources/views/homePages/hrmHomePages/hrmDashboards.css') }}">
{{-- @include('homePages.accHomePages.accHome') --}}

<style type="text/css">
	.pl-0{
		padding-left:0;
	}
	.pr-0{
		padding-right:0;
	}
	.nav.nav-tabs > li{
		float: left;
	}
	.blue li {background: url(../images/dashboards/liBlueCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
	.orange li {background: url(../images/dashboards/liOrangeCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
</style>

<script type="text/javascript" src="{{ asset('https://www.gstatic.com/charts/loader.js') }}"></script>

<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10 fullbody">
		<div class="viewTitle">
			{{-- <span class="panel-title" style="color: white; font-size:40px;">MIS Dashboard</span> --}}
        	<img src="{{ asset('images/dashboards/hrmDashboard/hrmDashboard.png') }}">
    	</div>
    	<div class="panel panel-default panel-border">
        	<div class="panel-body">
				<div class="row">
					<ul class="nav nav-tabs">
						<li class="col-md-2  pr-0 active">
							<a id="loadProfile" href="#loadProfileTab" data-toggle="tab">
								<span class=""><i class="fa-cog"></i></span>
								<span class="hidden-xs">My Profile</span>
							</a>
						</li>
						<li class="col-md-2 pl-0 pr-0">
							<a id="loadDeposit" href="#loadDepositTab" data-toggle="tab">
								<span class=""><i class="fa fa-users"></i></span>
								<span class="hidden-xs">Deposit Status</span>
							</a>
						</li>
						<li class="col-md-2 pl-0 pr-0">
							<a id="loadLoan" href="#loadLoanTab" data-toggle="tab">
								<span class=""><i class="fa fa-users"></i></span>
								<span class="hidden-xs">Loan Status</span>
							</a>
						</li>
						<li class="col-md-2 pl-0 pr-0">
							<a id="loadAdvance" href="#loadAdvanceTab" data-toggle="tab">
								<span class=""><i class="fa fa-users"></i></span>
								<span class="hidden-xs">Advance Status</span>
							</a>
						</li>
						<li class="col-md-2 pl-0 pr-0">
							<a id="loadLeave" href="#loadLeaveTab" data-toggle="tab">
								<span class=""><i class="fa fa-users"></i></span>
								<span class="hidden-xs">Leave Status</span>
							</a>
						</li>
						<li class="col-md-2 pl-0">
							<a id="tab1a" href="#tab1" data-toggle="tab">
								{{-- <span class="visible-xs"><i class="fa-cog"></i></span> --}}
								<span class=""><i class="fa-cog"></i></span>
								<span class="hidden-xs">Daily Operations</span>
							</a>
						</li>
						
					</ul>	
				</div>
				<br>
        		<div class="row">	
					<div class="col-md-12">
						<div class="tab-content">
							<div class="tab-pane active" id="loadProfileTab">
								<div class="row" id="loadProfileContent"></div>
							</div>

							<div class="tab-pane" id="loadDepositTab">
								<div class="row" id="loadDepositContent"></div>
							</div>

							<div class="tab-pane" id="loadLoanTab">
								<div class="row" id="loadLoanContent"></div>
							</div>

							<div class="tab-pane" id="loadAdvanceTab">
								<div class="row" id="loadAdvanceContent"></div>
							</div>

							<div class="tab-pane" id="loadLeaveTab">
								<div class="row" id="loadLeaveContent"></div>
							</div>

							<div class="tab-pane" id="tab1">
								<div class="row" id="loadTab1"></div>
							</div>
						</div>		
						{{-- div class="tab-content" --}}
            		</div>
            	</div>
          	</div>		{{-- div panel-body --}}
        </div>	{{-- Div panel panel-default panel-border --}}
        <div class="footerTitle" style="border-top:1px solid white"></div>
	</div> {{-- Div col-md-10 fullbody --}}
	<div class="col-md-1"></div>
</div>
{{-- <div id="columnchart_material" style="width: 800px; height: 500px;"></div> --}}
<script type="text/javascript">
$(document).ready(function(){

	// var loadingDiv='<div style="text-align:center;font-size:30px;padding-top:20px"><i style="font-size:30px;" class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i><span style="font-size:22px;" >Loading...</span></div>';
	var loadingDiv='<div align="center"><img src="{{ asset('images/dashboards/loading.gif') }}"></div>';

	$("#loadProfileContent").html(loadingDiv);
	$("#loadProfileContent").load('{{URL::to("hrm/loadProfile")}}');

	$(document).on('click', '#loadDeposit', function(){
		$("#loadDepositContent").html(loadingDiv);
		$("#loadDepositContent").load('{{URL::to("hrm/loadDeposit")}}');
	});

	$(document).on('click', '#loadLoan', function(){
		$("#loadLoanContent").html(loadingDiv);
		$("#loadLoanContent").load('{{URL::to("hrm/loadLoan")}}');
	});

	$(document).on('click', '#loadAdvance', function(){
		$("#loadAdvanceContent").html(loadingDiv);
		$("#loadAdvanceContent").load('{{URL::to("hrm/loadAdvance")}}');
	});

	$(document).on('click', '#loadLeave', function(){
		$("#loadLeaveContent").html(loadingDiv);
		$("#loadLeaveContent").load('{{URL::to("hrm/loadLeave")}}');
	});

	$("#loadTab1").html(loadingDiv);
	$("#loadTab1").load('{{URL::to("hrm/loadHrmTab1")}}');


});

$(document).ready(function(){
    // set active tab after reload
    $('ul.nav-tabs li').click(function() {
        var activeTab = $(this).find('a').attr('href');
        $.cookie('selectedTab', activeTab, {expires: 7}); // Save active tab in cookie
    });

	@php
		// dd($path);
	@endphp
    var activeTab = $.cookie('selectedTab'); // Retrieve active tab
    // alert(activeTab);
    $('ul.nav-tabs a[href="' + activeTab + '"]').click();
});
</script>
@endsection
