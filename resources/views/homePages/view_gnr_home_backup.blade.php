@extends('layouts/gnr_layout')
@section('content')
	<div class="row">
	<div class="col-md-1"></div>
	<div class="col-md-10 fullbody">
		<div class="viewTitle">
			<span class="panel-title" style="color: white; font-size:40px;">General Settings Dashboard</span>
        	{{-- <img src="{{ asset('software/images/dashboards/gnrDashboard/gnrDashboard.png') }}"> --}}
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
									<span class="hidden-xs">Tools</span>
								</a>
							</li>
							@php
								use Illuminate\Support\Facades\Auth;
								$userBranchId = Auth::user()->branchId;
							@endphp
							{{-- @if ($userBranchId == 1)
								<li>
									<a id="tab2a" href="#tab2" data-toggle="tab">
										<span class=""><i class="fa fa-users"></i></span>
										<span class="hidden-xs">Organization Status</span>
									</a>
								</li>
							@endif --}}

							<li>
								<a id="tab3a" href="#tab3" data-toggle="tab">
									<span class=""><i class="fa-suitcase"></i></span>
									<span class="hidden-xs">Employee</span>
								</a>
							</li>
							{{-- <li>
								<a id="tab4a" href="#tab4" data-toggle="tab">
									<span class=""><i class="fa fa-flag"></i></span>
									<span class="hidden-xs">Branch Status</span>
								</a>
							</li> --}}
						</ul>		{{-- ul nav nav-tabs --}}

						

            		</div>
            		{{-- <div class="col-md-2" style="border-left: 1px solid #D2d2d2;">Major Reports</div> --}}
            	</div>
          	</div>	</br>	{{-- div panel-body --}}
          	<div class="row">
        			<div class="col-md-12">
        				<ul class="nav nav-tabs">
							<li class="active">
								<a id="tab1a" href="#tab1" data-toggle="tab">
									<img src="http://120.50.0.141/earningsoft/public/software/images/dashboards/accDashboard/ledgerReport.png"/>
								</a>
							</li>
							<li>
								<img src="http://120.50.0.141/earningsoft/public/software/images/dashboards/accDashboard/ledgerReport.png"/>
							</li>
            		</div>
            		{{-- <div class="col-md-2" style="border-left: 1px solid #D2d2d2;">Major Reports</div> --}}
            	</div>
          	</div>		
        </div>	{{-- Div panel panel-default panel-border --}}
        <div class="footerTitle" style="border-top:1px solid white"></div>
	</div> {{-- Div col-md-10 fullbody --}}
	<div class="col-md-1"></div>
</div>
	{{--  <div class="row">
		<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.2s">
			<div class="xe-widget xe-counter-block" data-count=".num" data-from="0" data-to="99.9" data-suffix="%" data-duration="2">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-cloud"></i>
					</div>
					<div class="xe-label">
						<strong class="num">0.0%</strong>
						<span>Server uptime</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Result</span>
					<strong>78% Increase</strong>
				</div>
			</div>
		</div>
		<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.4s">
			<div class="xe-widget xe-counter-block xe-counter-block-purple" data-count=".num" data-from="0" data-to="512" data-duration="3">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-camera"></i>
					</div>
					<div class="xe-label">
						<strong class="num">0</strong>
						<span>Photos Taken</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Increase</span>
					<strong>512 more photos</strong>
				</div>
			</div>
		</div>
		<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.6s">
		<div class="xe-widget xe-counter-block xe-counter-block-blue" data-suffix="k" data-count=".num" data-from="0" data-to="310" data-duration="4" data-easing="false">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-user"></i>
					</div>
					<div class="xe-label">
						<strong class="num">0k</strong>
						<span>Daily Visits</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Bounce Rate</span>
					<strong>51.55%</strong>
				</div>
			</div>
		</div>
		<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.8s">
			<div class="xe-widget xe-counter-block xe-counter-block-orange">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="fa-life-ring"></i>
					</div>
					<div class="xe-label">
						<strong class="num">24/7</strong>
						<span>Live Support</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Tickets Opened</span>
					<strong data-count="this" data-from="0" data-to="14215" data-duration="2">0</strong>
				</div>
			</div>
		</div>
	</div> --}}
	{{-- <div class="row">
		<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.2s">
			<div class="xe-widget xe-progress-counter xe-progress-counter-pink" data-count=".num" data-from="0" data-to="12425" data-duration="2">
				<div class="xe-background">
					<i class="linecons-heart"></i>
				</div>
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-heart"></i>
					</div>
					<div class="xe-label">
						<span>users</span>
						<strong class="num">0</strong>
					</div>
				</div>
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="0" data-fill-to="56" data-fill-unit="%" data-fill-property="width" data-fill-duration="2" data-fill-easing="true"></span>
				</div>
				<div class="xe-lower">
					<span>Users p/ Month</span>
					<strong>41% more users</strong>
				</div>
			</div>
		</div> --}}
		{{-- <div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.4s">
			<div class="xe-widget xe-progress-counter xe-progress-counter-turquoise" data-count=".num" data-from="0" data-to="520" data-suffix="k" data-duration="3">
				<div class="xe-background">
					<i class="linecons-paper-plane"></i>
				</div>
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-paper-plane"></i>
					</div>
					<div class="xe-label">
						<span>customers</span>
						<strong class="num">0</strong>
					</div>
				</div>
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="0" data-fill-to="82" data-fill-unit="%" data-fill-property="width" data-fill-duration="3" data-fill-easing="true"></span>
				</div>
				<div class="xe-lower">
					<span>Customers p/ Month</span>
					<strong>82% more communication</strong>
				</div>
			</div>
		</div> --}}
	{{-- 	<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.6s">
			<div class="xe-widget xe-progress-counter xe-progress-counter-info" data-count=".num" data-from="0" data-to="289" data-suffix="k" data-duration="4">
				<div class="xe-background">
					<i class="linecons-music"></i>
				</div>
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-music"></i>
					</div>
					<div class="xe-label">
						<span>products</span>
						<strong class="num">0</strong>
					</div>
				</div>
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="0" data-fill-to="40" data-fill-unit="%" data-fill-property="width" data-fill-duration="4" data-fill-easing="true"></span>
				</div>
				<div class="xe-lower">
					<span>Products p/ Month</span>
					<strong>40% more products</strong>
				</div>
			</div>
		</div> --}}
		{{-- <div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.8s">
			<div class="xe-widget xe-progress-counter xe-progress-counter-red" data-count=".num" data-from="46" data-to="27" data-duration="3">
				<div class="xe-background">
					<i class="linecons-calendar"></i>
				</div>
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-calendar"></i>
					</div>
					<div class="xe-label">
						<span>appointments</span>
						<strong class="num">0</strong>
					</div>
				</div>
				<div class="xe-progress">
					<span class="xe-progress-fill" data-fill-from="89" data-fill-to="40" data-fill-unit="%" data-fill-property="width" data-fill-duration="3" data-fill-easing="true"></span>
				</div>
				<div class="xe-lower">
					<span>Appointments p/ Month</span>
					<strong>-32% less this week</strong>
				</div>
			</div>
		</div>
	</div> --}}
	{{-- <div class="row">
		<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.2s">
			<div class="xe-widget xe-counter-block" data-count=".num" data-from="0" data-to="99.9" data-suffix="%" data-duration="2">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-cloud"></i>
					</div>
					<div class="xe-label">
						<strong class="num">0.0%</strong>
						<span>Server uptime</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Result</span>
					<strong>78% Increase</strong>
				</div>
			</div>
		</div> --}}
		{{-- <div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.4s">
			<div class="xe-widget xe-counter-block xe-counter-block-purple" data-count=".num" data-from="0" data-to="512" data-duration="3">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-camera"></i>
					</div>
					<div class="xe-label">
						<strong class="num">0</strong>
						<span>Photos Taken</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Increase</span>
					<strong>512 more photos</strong>
				</div>
			</div>
		</div> --}}
	{{-- 	<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.6s">
			<div class="xe-widget xe-counter-block xe-counter-block-blue" data-suffix="k" data-count=".num" data-from="0" data-to="310" data-duration="4" data-easing="false">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="linecons-user"></i>
					</div>
					<div class="xe-label">
						<strong class="num">0k</strong>
						<span>Daily Visits</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Bounce Rate</span>
					<strong>51.55%</strong>
				</div>
			</div>
		</div> --}}
	{{-- 	<div class="col-sm-3 animated fadeInLeft" style="animation-delay: 0.8s">
			<div class="xe-widget xe-counter-block xe-counter-block-orange">
				<div class="xe-upper">
					<div class="xe-icon">
						<i class="fa-life-ring"></i>
					</div>
					<div class="xe-label">
						<strong class="num">24/7</strong>
						<span>Live Support</span>
					</div>
				</div>
				<div class="xe-lower">
					<div class="border"></div>
					<span>Tickets Opened</span>
					<strong data-count="this" data-from="0" data-to="14215" data-duration="2">0</strong>
				</div>
			</div>
		</div>
	</div> --}}
@endsection