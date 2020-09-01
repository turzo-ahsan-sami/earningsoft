@extends('hr_main')
@section('title', '| HRM Home')
@section('content')

<link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
<link rel="stylesheet" href="{{ asset('../resources/views/homePages/homeDashboards.css') }}">
<link rel="stylesheet" href="{{ asset('../resources/views/homePages/hrmHomePages/hrmDashboards.css') }}">

<style type="text/css">
	.blue li {background: url(../images/dashboards/liBlueCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
	.orange li {background: url(../images/dashboards/liOrangeCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
</style>

<script type="text/javascript" src="{{ asset('https://www.gstatic.com/charts/loader.js') }}"></script>

<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10 fullbody">
		<div class="viewTitle">
        	<img src="{{ asset('images/dashboards/hrmDashboard/hrmDashboard.png') }}">
    	</div>
    	<div class="panel panel-default panel-border">
        	<div class="panel-body">
        		<div class="row">
        			<div class="col-md-12">
        				<ul class="nav nav-tabs">
							<li class="active">
								<a id="loadProfile" href="#loadProfileTab" data-toggle="tab">
									<span class=""><i class="fa-cog"></i></span>
									<span class="hidden-xs">My Profile</span>
								</a>
							</li>
							<li>
								<a id="loadDeposit" href="#loadDepositTab" data-toggle="tab">
									<span class=""><i class="fa fa-users"></i></span>
									<span class="hidden-xs">Deposit Status</span>
								</a>
							</li>
							<li>
								<a id="loadLoan" href="#loadLoanTab" data-toggle="tab">
									<span class=""><i class="fa fa-users"></i></span>
									<span class="hidden-xs">Loan Status</span>
								</a>
							</li>
							<li>
								<a id="loadAdvance" href="#loadAdvanceTab" data-toggle="tab">
									<span class=""><i class="fa fa-users"></i></span>
									<span class="hidden-xs">Advance Status</span>
								</a>
							</li>
							<li>
								<a id="loadLeave" href="#loadLeaveTab" data-toggle="tab">
									<span class=""><i class="fa fa-users"></i></span>
									<span class="hidden-xs">Leave Status</span>
								</a>
							</li>
							<li>
								<a id="tab1a" href="#tab1" data-toggle="tab">
									{{-- <span class="visible-xs"><i class="fa-cog"></i></span> --}}
									<span class=""><i class="fa-cog"></i></span>
									<span class="hidden-xs">Daily Operations</span>
								</a>
							</li>
						</ul>

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
            		</div>
            	</div>
          	</div>		{{-- div panel-body --}}
        </div>	{{-- Div panel panel-default panel-border --}}
        <div class="footerTitle" style="border-top:1px solid white"></div>
	</div> {{-- Div col-md-10 fullbody --}}
	<div class="col-md-1"></div>
</div>

<script type="text/javascript">
$(document).ready(function(){

	var loadingDiv='<div align="center"><img src="{{ asset('images/dashboards/loading.gif') }}"></div>';

	$("#loadProfileContent").html(loadingDiv);
	$("#loadProfileContent").load('{{URL::to("hrm/loadProfile")}}');

	$(document).on('click', '#loadProfile', function(){
		$("#loadProfileContent").html(loadingDiv);
		$("#loadProfileContent").load('{{URL::to("hrm/loadProfile")}}');
	});

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

	$("#tab1a").click(function(){
    	$("#loadTab1").html(loadingDiv);
    	$("#loadTab1").load('{{URL::to("hrm/loadHrmTab1")}}');
	});


});
</script>
@endsection
