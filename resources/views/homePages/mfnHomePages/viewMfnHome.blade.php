@extends('layouts/microfin_layout')
@section('content')
@section('title', '| Microfinance Home')
{{-- <link rel="stylesheet" href="{{ asset('../resources/views/homePages/mfnHomePages/mfnHome.css') }}"> --}}
<link rel="stylesheet" href="{{ asset('../resources/views/homePages/homeDashboards.css') }}">
{{-- @include('homePages.accHomePages.accHome') --}}
<style type="text/css">
	.blue li {background: url(../images/dashboards/liBlueCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
	.orange li {background: url(../images/dashboards/liOrangeCircle.png) no-repeat 0px 6px scroll; list-style-type: none;}
</style>
<script type="text/javascript" src="{{ asset('https://www.gstatic.com/charts/loader.js') }}">
</script>

<div class="row"  style="padding-bottom: 1%">
	<div class="col-md-1"></div>
	<div class="col-md-10 fullbody">
		<div class="viewTitle" >
			{{-- <span class="panel-title" style="color: white; font-size:40px;">MIS Dashboard</span> --}}
        	<img src="{{ asset('images/dashboards/mfnDashboard/mfnDashboard.png') }}">
    	</div>
    	<div class="panel panel-default panel-border">
        	<div class="panel-body">
        		<div class="row">

        			<div class="col-md-12">

        				<ul class="nav nav-tabs">
							<li class="active">
								<a id="tab1a" href="#tab1" data-toggle="tab">
									{{-- <span class="visible-xs"><i class="fa-cog"></i></span> --}}
									<span class=""><i class="fa-cog"></i></span>
									<span class="hidden-xs">Daily Operations</span>
								</a>
							</li>
							<li @if (Auth::user()->branchId != 1) style="display: none;" @endif>
								<a id="tab2a" href="#tab2" data-toggle="tab">
									<span class=""><i class="fa fa-users"></i></span>
									<span class="hidden-xs">Organization Status</span>
								</a>
							</li>
							<li>
								<a id="tab3a" href="#tab3" data-toggle="tab">
									<span class=""><i class="fa fa-calculator"></i></span>
									<span class="hidden-xs">Loan, Savings & Insurance</span>
								</a>
							</li>
							<li>
								<a id="tab4a" href="#tab4" data-toggle="tab">
									<span class=""><i class="fa fa-flag"></i></span>
									<span class="hidden-xs">Branch Status</span>
								</a>
							</li>
						</ul>		{{-- ul nav nav-tabs --}}

						<div class="tab-content">
							<div class="tab-pane active" id="tab1">

								<div class="row" id="loadTab1"></div>

							</div>
							<div class="tab-pane" id="tab2">

								<div class="row" id="loadTab2"></div>

							</div>
							<div class="tab-pane" id="tab3">

								<div class="row" id="loadTab3"></div>

							</div>

							<div class="tab-pane" id="tab4">

								<div class="row" id="loadTab4"></div>

							</div>


						</div>		{{-- div class="tab-content" --}}


            		</div>
            		{{-- <div class="col-md-2" style="border-left: 1px solid #D2d2d2;">Major Reports</div> --}}
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

	$("#loadTab1").html(loadingDiv);
	$("#loadTab1").load('{{URL::to("mfn/loadMfnTab1")}}');

    $("#tab2a").click(function(){
    	$("#loadTab2").html(loadingDiv);
    	$("#loadTab2").load('{{URL::to("mfn/loadMfnTab2")}}');
	});

    $("#tab3a").click(function(){
    	$("#loadTab3").html(loadingDiv);
    	$("#loadTab3").load('{{URL::to("mfn/loadMfnTab3")}}');
	});

    $("#tab4a").click(function(){
    	$("#loadTab4").html(loadingDiv);
    	$("#loadTab4").load('{{URL::to("mfn/loadMfnTab4")}}');
	});

});
</script>

<script type="text/javascript">

// Tab Pane continue moving
	var tabChange = function () {
		var tabs = $('.chart_title .nav-tabs > li');
		var active = tabs.filter('.active');
		var next = active.next('li').length ? active.next('li').find('a') : tabs.filter(':first-child').find('a');
		// Use the Bootsrap tab show method
		next.trigger('click');
	};

	// Tab Cycle function
	var tabCycle = setInterval(tabChange, 10000);

</script>
@endsection
