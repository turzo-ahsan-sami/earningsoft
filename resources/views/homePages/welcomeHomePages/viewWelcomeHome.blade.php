@php
$userBranchId = Auth::user()->branchId;
$userBranch = Auth::user()->branchId;
$companyInfo = DB::table('gnr_company')->where('id',Auth::user()->company_id_fk)->first();
//dd(Auth::user()->company_id_fk);
$gnrModule = DB::table('gnr_module')->get();
@endphp
@php
$generalStatus = (int) $gnrModule->where('id',7)->max('status');
$invStatus = (int) $gnrModule->where('id',1)->max('status');
$famsStatus = (int) $gnrModule->where('id',2)->max('status');
$procurementStatus = (int) $gnrModule->where('id',3)->max('status');
$accStatus = (int) $gnrModule->where('id',4)->max('status');
$hrStatus = (int) $gnrModule->where('id',5)->max('status');
$mrfStatus = (int) $gnrModule->where('id',6)->max('status');
$posStatus = (int) $gnrModule->where('id',8)->max('status');
$attStatus = (int) $gnrModule->where('id',9)->max('status');

@endphp
@extends('welcome')
@section('title', '| Dashboard')
@section('content')
<style type="text/css">
/*html,body{
    overflow-x: hidden;
}*/


	/*body{
		overflow-x:hidden;
	}*/
	.module{
		font-size: 9px;
	}
	.page-body .jumbotron {
padding-bottom: 0px !important;
}
</style>
<style type="text/css">
		@media all and (max-width: 600px) {
		.carousel-inner>.item>a>img, .carousel-inner>.item>img, .img-responsive, .thumbnail a>img, .thumbnail>img {
		margin: 0 auto !important;
		font-size: 14px !important;
		}
		.comName{
			text-align: center;
		}
		.comAdd{
			text-align: center;
			font-size: 11px !important;
		}
	}
	
</style>
<style type="">
	.logoImg{
		box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	}

  @media only screen and (max-width: 991px) and (min-width: 320px){
 /* 	body{
		overflow-x:hidden;
	}*/
   .sales-tab{
   	margin-right: 15px;
   }

   @media only screen and (max-width: 768px){
	/*.img {
	    width: 100% !important;
	}*/

	.img-responsive {
	    width: 30% !important;
	}

    .billing-tab{
   	margin-right: 15px;
   }

   .customize {
    margin-bottom: 15px;
    margin-left: -16px;
    margin-right: -30px;
	}

	
}

	@media all and (min-width:992px) {
		.companyInfo{
			padding-right: 27px;
		}
	}

	.img-customize{
		width: 100% !important;
    	height: 50px !important;
	}
</style>

<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10 companyInfo" >
		<div class="panel panel-body panel-border logoImg">
			<div class="row">
				<div class="col-sm-4 col-md-2 col-lg-1">
					<div style="" class="listing_big_image blue_bg">
						<img src="{{ asset('/images/company/'.$companyInfo->image) }}" border="0"  class="img-responsive img">
					</div>
					
				</div>
				<div class="col-sm-8 col-md-5 col-lg-5">
					<h4 class="comName"><strong>{{$companyInfo->name}}</strong></h4>
					<span class="comAdd"><strong>{{$companyInfo->address}}</strong></span></br>
				</div>
				<div class="col-sm-5"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1 col-md-10 col-md-offset-1 col-lg-10" style="padding-right:0px;">
		<div class="row-one widgettable">
			<div class="col-md-6 col-lg-3" style="padding-left:0px;">
				@foreach ($modules as $module)
				@if ($module->slug == 'bil')
				<a id="tab1a" href="#tab1" data-toggle="tab">
					<div class="r3_counter_box">
						<i class="pull-left fa fa-dollar icon-rounded"></i>
						<div class="stats">
							<span>Billing</span>
						</div>
					</div>
				</a>
				{{-- @elseif ($module->slug == 'inv')
				<a id="tab2a" href="#tab2" data-toggle="tab">
					<div class="r3_counter_box">
						<i class="pull-left fa fa-shopping-cart user1 icon-rounded" ></i>
						<div class="stats">
							<span>Inventory</span>
						</div>
					</div>
				</a>
				@elseif ($module->slug == 'fams')
				<a id="tab3a" href="#tab3" data-toggle="tab">
					<div class="r3_counter_box">
						<i class="pull-left fa fa-tachometer user2 icon-rounded"></i>
						<div class="stats">
							<span>FAMS</span>
						</div>
					</div>
				</a> --}}
				@elseif ($module->slug == 'acc')
				<a id="tab4a" href="#tab4" data-toggle="tab">
					<div class="r3_counter_box">
						<i class="pull-left fa fa-pie-chart dollar1 icon-rounded"></i>
						<div class="stats">
							<span>Accounting</span>
						</div>
					</div>
				</a>
				@endif
				@endforeach
			</div>
			
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
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function(){
		var loadingDiv='<div align="center"><img src="{{ asset('software/images/dashboards/loading.gif') }}"></div>';
		$("#loadTab1").html(loadingDiv);
		$("#loadTab1").load('{{URL::to("welcome/loadwelcomeTab1")}}');
	$("#tab1a").click(function(){
		$("#loadTab1").html(loadingDiv);
		$("#loadTab1").load('{{URL::to("welcome/loadwelcomeTab1")}}');
		});
	$("#tab2a").click(function(){
		$("#loadTab2").html(loadingDiv);
		$("#loadTab2").load('{{URL::to("welcome/loadwelcomeTab2")}}');
		});
	$("#tab3a").click(function(){
		$("#loadTab3").html(loadingDiv);
		$("#loadTab3").load('{{URL::to("welcome/loadwelcomeTab2")}}');
		});
	$("#tab4a").click(function(){
		$("#loadTab4").html(loadingDiv);
		$("#loadTab4").load('{{URL::to("welcome/loadwelcomeTab4")}}');
		});
	});
</script>
<script type="text/javascript">
	var tabChange = function () {
		var tabs = $('.chart_title .nav-tabs > li');
		var active = tabs.filter('.active');
		var next = active.next('li').length ? active.next('li').find('a') : tabs.filter(':first-child').find('a');
		next.trigger('click');
	};
	// Tab Cycle function
	var tabCycle = setInterval(tabChange, 10000);
</script>
{{-- <script rel="stylesheet" src="{{ asset('software/dashboard/js/SimpleChart.js') }}"></script> --}}


<style type="">



	/*
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
*/
.stats{
		margin-top: 15px !important;
		
}
.stats span{
		font-size: 12px !important;
}
.Linegraph{
	width: 98%;
	height: 285px;
}
.main-content {
position: relative;
}
span.dashboard_text {
font-size: 12px;
text-transform: capitalize;
display: block;
letter-spacing: 1px;
padding-left: 35px;
}
/* ----STICKY HEADER----*/
.sticky-header{
position: fixed;
top: 0;
left:0px;
width: 100%;
z-index: 100;
}
.header-section {
background:#FFF;
box-shadow:  1px 1px 4px rgba(0, 0, 0, 0.21);
-webkit-box-shadow:  1px 1px 4px rgba(0, 0, 0, 0.21);
-moz-box-shadow:  1px 1px 4px rgba(0, 0, 0, 0.21);
-o-box-shadow:  1px 1px 4px rgba(0, 0, 0, 0.21);
}
.header-section::after {
clear: both;
display: block;
content: '';
}
.header-left {
float: left;
width: 50%;
margin-left: 15%;
	position: relative;
}
.header-right {
float: right;
}
/* ----menu-icon----*/
button#showLeftPush {
font-size: 1.1em;
text-align: center;
cursor: pointer;
float: left;
color: #fff;
-moz-transition: all 0.2s ease-out 0s;
-webkit-transition: all 0.2s ease-out 0s;
transition: all 0.2s ease-out 0s;
	border: none;
background-color: #F2B33F;
	outline:none;
border-radius: 50%;
height: 40px;
width: 40px;
margin-top: 11px;
}
button#showLeftPush:hover {
color: #eee;
}
/*--push-menu-css--*/
.cbp-spmenu {
	position: fixed;
}
.cbp-spmenu-vertical {
height: 100%;
z-index: 1000;
padding: 0em 0 2em;
}
.cbp-spmenu-left {
left: 0;
}
.cbp-spmenu-left.cbp-spmenu-open {
	left: -309px;
}
/* Push classes applied to the body */
.cbp-spmenu-push {
	overflow-x: hidden;
	position: relative;
}
.cbp-spmenu-push-toright {
	left: 0;
}
/* Transitions */
.cbp-spmenu,
.cbp-spmenu-push {
	-webkit-transition: all 0.3s ease;
	-moz-transition: all 0.3s ease;
	transition: all 0.3s ease;
}
.cbp-spmenu-push div#page-wrapper {
margin: 0 0 0 13.5em;
	transition:.5s all;
	-webkit-transition:.5s all;
	-moz-transition:.5s all;
}
.cbp-spmenu-push.cbp-spmenu-push-toright div#page-wrapper {
margin: 0;
}
/*--//push-menu-css--*/
/*--side-menu--*/
.sidebar ul li{
	margin-bottom: 1em;
}
.sidebar ul li a {
color: #FFFFFF;
font-size: 1em;
padding: 5px 15px;
	padding-left: 25px;
}
.collapse.in {
display: block;
background: #2d6696fc;
background: none;
color: #fff;
margin-top: 10px;
}
.nav > li > a:hover, .nav > li > a:focus {
text-decoration: none;
background-color: transparent;
color: #000;
}
.sidebar .arrow {
float: right;
}
h4.heading {
padding-left: 25px;
font-size: 17px;
	letter-spacing: 1px;
	color:#eee;
font-weight: 600;
text-transform: uppercase;
margin-bottom: 10px;
margin-top: 30px;
}
i.nav_icon {
background: #00000070;
	font-size: 16px;
line-height: 25px;
display: block;
float: left;
width: 32px;
height: 26px;
margin-right: 10px;
margin-left: 0px;
text-align: center;
opacity: 0.6;
border-radius: 3px;
}
.fa {
display: inline-block;
font-family: FontAwesome;
font-style: normal;
font-weight: normal;
line-height: 1;
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
}
span.nav-badge {
font-size: 12px;
color: #FFFFFF;
background: rgba(255, 255, 255, 0.32);
width: 25px;
height: 25px;
border-radius: 68%;
-webkit-border-radius: 68%;
-moz-border-radius: 68%;
-o-border-radius: 68%;
position: absolute;
top: 18%;
right: 15%;
line-height: 26px;
letter-spacing: 1px;
text-align: center;
}
span.nav-badge-btm {
font-size: 12px;
color: #FFF;
background: #F2B33F;
position: absolute;
top: 18%;
right: 15%;
line-height: 22px;
letter-spacing: 1px;
text-align: center;
padding: 0em 1em;
border-radius: 30px;
}
.dropdown-menu > li > a:hover, .dropdown-menu > li > a:focus {
background: none;
color: #337ab7;
}
.chart-nav span.nav-badge-btm {
	right: 5%;
top: 24%;
}
ul.dropdown-menu{
	/* animation: flipInX 1s ease;
	-moz-animation: flipInX 1s ease;
	-webkit-animation: flipInX 1s ease;
	-webkit-backface-visibility: visible !important;
	-ms-backface-visibility: visible !important;
	backface-visibility: visible !important;
	-moz-backface-visibility: visible !important;
	data-wow-delay:".1s";
	*/
	
-webkit-transition: all 0.5s ease-out;
-moz-transition: all 0.5s ease-out;
-ms-transition: all 0.5s ease-out;
-o-transition: all 0.5s ease-out;
transition: all 0.5s ease-out;
}
/*--//side-menu--*/
/* ----Logo----*/
/* ----//Logo----*/
/*start search*/
.search-box {
float: left;
width: 43%;
margin: .5em 0 0 0em;
	position: relative;
z-index: 1;
display: inline-block;
}
.sb-search-input {
outline: none;
background: #fff;
width: 100%;
margin: 0;
z-index: 10;
font-size: 1em;
color: #383838;
padding: 0.5em 1em;
border: 1px solid #8c8c8c;
background: url(../images/search-icon.png) no-repeat 160px 12px;
	-webkit-appearance: none; /* for box shadows to show on iOS */
}
.sb-search-input::-webkit-input-placeholder {
	color:#888;
}
.sb-search-input:-moz-placeholder {
	color: #888;
}
.sb-search-input::-moz-placeholder {
	color: #888;
}
.sb-search-input:-ms-input-placeholder {
	color: #888;
}
.input__label {
	-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	position: absolute;
	width: 100%;
	height: 100%;
cursor: text;
}
.graphic {
	fill: none;
	-webkit-transform: scale3d(1, -1, 1);
	transform: scale3d(1, -1, 1);
	-webkit-transition: stroke-dashoffset 0.5s;
	transition: stroke-dashoffset 0.5s;
	pointer-events: none;
	stroke: #D81B60;
stroke-width: 6px;
stroke-dasharray: 962;
stroke-dashoffset: 962;
}
/* Madoka */
.input__field--madoka {
	display: block;
	float: right;
}
.input__field--madoka:focus {
	outline: none;
}
.input__field--madoka:focus + .input__label,
.input--filled .input__label {
	cursor: default;
	pointer-events: none;
}
.input__field--madoka:focus + .input__label .graphic,
.input--filled .graphic {
	stroke-dashoffset: 0;
}
.input__field--madoka:focus + .input__label .input__label-content{
	-webkit-transform: scale3d(0.81, 0.81, 1) translate3d(0, 4em, 0);
	transform: scale3d(0.81, 0.81, 1) translate3d(0, 4em, 0);
}
/*--//search-ends --*/
/*--- Progress Bar ----*/
.meter {
	position: relative;
}
.meter > span {
	display: block;
	height: 100%;
	
	position: relative;
	overflow: hidden;
}
.meter > span:after, .animate > span > span {
	content: "";
	position: absolute;
	top: 0; left: 0; bottom: 0; right: 0;
	
	overflow: hidden;
}
.animate > span:after {
	display: none;
}
@-webkit-keyframes move {
0% {
background-position: 0 0;
}
100% {
background-position: 50px 50px;
}
}
@-moz-keyframes move {
0% {
background-position: 0 0;
}
100% {
background-position: 50px 50px;
}
}
.red > span {
	background-color: #65CEA7;
}
.nostripes > span > span, .nostripes > span:after {
	-webkit-animation: none;
	-moz-animation: none;
	background-image: none;
}
/*--- User Panel---*/
.profile_details_left {
float: left;
padding-left: 5px;
}
.dropdown-menu {
box-shadow: 2px 3px 4px rgba(0, 0, 0, .175);
	-webkit-box-shadow: 2px 3px 4px rgba(0, 0, 0, .175);
	-moz-box-shadow: 2px 3px 4px rgba(0, 0, 0, .175);
border-radius: 0;
}
li.dropdown.head-dpdn {
display: inline-block;
padding: 1.25em 0;
	float: left;
}
li.dropdown.head-dpdn:nth-child(3) {
border-right: 1px solid transparent;
}
li.dropdown.head-dpdn a.dropdown-toggle {
padding: 0.5em .7em;
background: #629aa9;
margin: 0 .5em;
border-radius: 50%;
-webkit-border-radius: 50%;
-moz-border-radius: 50%;
-ms-border-radius: 50%;
-o-border-radius: 50%;
}
ul.dropdown-menu li {
margin-left: 0;
width: 100%;
padding: 0;
	background: #fff;
}
.user-panel-top ul{
	padding-left:0;
}
.user-panel-top li{
	float:left;
	margin-left:15px;
	position:relative;
}
.user-panel-top li span.digit{
font-size:11px;
font-weight:bold;
	color:#FFF;
	background:#e64c65;
	line-height:20px;
	width:20px;
	height:20px;
	border-radius:2em;
	-webkit-border-radius:2em;
	-moz-border-radius:2em;
		-o-border-radius:2em;
	text-align:center;
	display: inline-block;
	position: absolute;
	top: -3px;
	right: -10px;
}
.user-panel-top li:first-child{
	margin-left:0;
}
.custom-nav > li.act > a, .custom-nav > li.act > a:hover, .custom-nav > li.act > a:focus {
background-color: #353f4f;
color:#8BC34A;
}
.user-panel-top li a{
	display: block;
	padding: 5px;
	text-decoration:none;
}
.header-left i.fa.fa-envelope{
	color:#fff;
font-size: 14px;
}
.header-left i.fa.fa-bell{
	color:#fff;
font-size: 14px;
}
.header-left  i.fa.fa-tasks{
	color:#fff;
font-size: 14px;
}
.user-panel-top li a:hover{
	border-color:rgba(101, 124, 153, 0.93);
}
.user-panel-top li a i{
	width:24px;
	height:24px;
	display: block;
	text-align:center;
	line-height:25px;
}
.user-panel-top li a i span{
	font-size:15px;
	color:#FFF;
}
.user-panel-top li a.user{
	background:#667686;
}
.user-panel-top li span.green{
	background:#a88add;
}
.user-panel-top li span.red{
	background:#b8c9f1;
}
.user-panel-top li span.yellow{
	background:#bdc3c7;
}
/***** Messages *************/
.notification_header{
	background-color:#FAFAFA;
	padding: 10px 15px;
	border-bottom:1px solid rgba(0, 0, 0, 0.05);
	margin-bottom: 8px;
}
	.notification_header h3{
	color:#6A6A6A;
	font-size:12px;
	font-weight:600;
	margin:0;
}
.notification_bottom {
background-color: rgba(200, 129, 230, 0.14);
padding: 4px 0;
text-align: center;
	margin-top: 5px;
}
.notification_bottom a {
color: #6F6F6F;
	font-size: 1em;
}
.notification_bottom a:hover {
color:#6164C1;
}
	.notification_bottom h3 a{
	color: #717171;
	font-size:12px;
	border-radius:0;
	border:none;
	padding:0;
	text-align:center;
}
	.notification_bottom h3 a:hover{
	color:#4A4A4A;
	text-decoration:underline;
	background:none;
}
.user_img{
	float:left;
	width:19%;
}
.user_img img{
	max-width:100%;
	display:block;
	border-radius:2em;
	-webkit-border-radius:2em;
	-moz-border-radius:2em;
	-o-border-radius:2em;
}
.notification_desc{
	float:left;
	width:70%;
	margin-left:5%;
}
.notification_desc p{
	color:#757575;
	font-size:13px;
	padding:2px 0;
}
.wrapper-dropdown-2 .dropdown li a:hover .notification_desc p{
	color:#424242;
}
.notification_desc p span{
	color:#979797 !important;
	font-size:11px;
}
.content-top-1 {
    padding: 0px !important;
    margin-bottom: 14px !important;
}
/*---bages---*/
.header-left span.badge {
font-size: 11px;
font-weight: bold;
color: #FFF;
background: #ff6c5f;
line-height: 15px;
width: 20px;
height: 20px;
border-radius: 2em;
-webkit-border-radius: 2em;
-moz-border-radius: 2em;
-o-border-radius: 2em;
text-align: center;
display: inline-block;
position: absolute;
top: 16%;
padding: 2px 0 0;
}
.header-left span.blue{
	background-color: #2dde98;
}
.header-left span.red{
	background-color:#ef553a;
}
.header-left span.blue1{
	background-color: #ffc168;
}
i.icon_1{
float: left;
color: #00aced;
line-height: 2em;
margin-right: 1em;
}
i.icon_2{
float: left;
color:#ef553a;
line-height: 2em;
margin-right: 1em;
font-size: 20px;
}
i.icon_3{
float: left;
color:#9358ac;
line-height: 2em;
margin-right: 1em;
font-size: 20px;
}
.avatar_left {
float: left;
}
i.icon_4{
width: 45px;
height: 45px;
background: #F44336;
float: left;
color: #fff;
text-align: center;
font-size: 1.5em;
line-height: 44px;
font-style: normal;
margin-right: 1em;
}
i.icon_5{
background-color: #3949ab;
}
i.icon_6{
background-color: #03a9f4;
}
.blue-text {
color: #2196F3 !important;
float:right;
}
/*---//bages---*/
/*--Progress bars--*/
.progress {
height: 10px;
margin: 7px 0;
overflow: hidden;
background: #e1e1e1;
z-index: 1;
cursor: pointer;
}
.task-info .percentage{
	float:right;
	height:inherit;
	line-height:inherit;
}
.task-desc{
	font-size:12px;
}
.wrapper-dropdown-3 .dropdown li a:hover span.task-desc {
	color:#65cea7;
}
.progress .bar {
		z-index: 2;
		height:15px;
		font-size: 12px;
		color: white;
		text-align: center;
		float:left;
		-webkit-box-sizing: content-box;
		-moz-box-sizing: content-box;
		-ms-box-sizing: content-box;
		box-sizing: content-box;
		-webkit-transition: width 0.6s ease;
		-moz-transition: width 0.6s ease;
		-o-transition: width 0.6s ease;
		transition: width 0.6s ease;
	}
.progress-striped .yellow{
	background:#f0ad4e;
}
.progress-striped .green{
	background:#5cb85c;
}
.progress-striped .light-blue{
	background:#4F52BA;
}
.progress-striped .red{
	background:#d9534f;
}
.progress-striped .blue{
	background:#428bca;
}
.progress-striped .orange {
	background:#e94e02;
}
.progress-striped .bar {
background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
-webkit-background-size: 40px 40px;
-moz-background-size: 40px 40px;
-o-background-size: 40px 40px;
background-size: 40px 40px;
}
.progress.active .bar {
-webkit-animation: progress-bar-stripes 2s linear infinite;
-moz-animation: progress-bar-stripes 2s linear infinite;
-ms-animation: progress-bar-stripes 2s linear infinite;
-o-animation: progress-bar-stripes 2s linear infinite;
animation: progress-bar-stripes 2s linear infinite;
}
@-webkit-keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
@-moz-keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
@-ms-keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
@-o-keyframes progress-bar-stripes {
from {
background-position: 0 0;
}
to {
background-position: 40px 0;
}
}
@keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
/*--Progress bars--*/
/********* profile details **********/
ul.dropdown-menu.drp-mnu i.fa {
margin-right: 0.5em;
color: #629aa9;
}
ul.dropdown-menu {
padding: 1em;
min-width: 280px;
top:101%;
}
.dropdown-menu > li > a {
padding: 3px 15px;
	font-size: 1em;
letter-spacing: .5px;
}
.profile_details {
float: right;
}
.profile_details_drop .fa.fa-angle-up{
	display:none;
}
.profile_details_drop.open .fa.fa-angle-up{
display:block;
}
.profile_details_drop.open .fa.fa-angle-down{
	display:none;
}
.profile_details_drop a.dropdown-toggle {
display:block;
	padding: 0.2em 3em 0 0em;
}
.profile_img span.prfil-img{
	float:left;
}
.user-name{
	float:left;
	margin-top:8px;
	margin-left:11px;
	height:35px;
}
.profile_details ul li{
	list-style-type:none;
	position:relative;
margin-right: 30px;
}
.profile_details li a i.fa.lnr {
position: absolute;
top: 34%;
right: 8%;
color: #333;
font-size: 1.6em;
}
span.prfil-img img,.activity-img img,.media>.pull-left img,.inbox-page img {
border-radius: 50%;
-webkit-border-radius: 50%;
-moz-border-radius: 50%;
-ms-border-radius: 50%;
-o-border-radius: 50%;
}
.profile_details ul li ul.dropdown-menu.drp-mnu {
padding: 1em;
min-width: 190px;
top: 122%;
left:0%;
}
ul.dropdown-menu.drp-mnu li {
list-style-type: none;
padding: 3px 0;
}
.user-name p{
	font-size:1em;
	color: #F2B33F;
	line-height:1em;
	font-weight:700;
}
.user-name span {
font-size: .75em;
color: #424f63;
font-weight: normal;
margin-top: .3em;
}
/*---footer---*/
.footer {
background: #fff;
padding: 1em;
width: 100%;
text-align:center;
	box-shadow: 0px -1px 4px rgba(0, 0, 0, 0.21);
	-webkit-box-shadow: 0px -1px 4px rgba(0, 0, 0, 0.21);
	-moz-box-shadow: 0px -1px 4px rgba(0, 0, 0, 0.21);
	-ms-box-shadow: 0px -1px 4px rgba(0, 0, 0, 0.21);
	-o-box-shadow: 0px -1px 4px rgba(0, 0, 0, 0.21);
}
.footer  p {
	color: #7A7676;
font-size: 1em;
	line-height: 1.6em;
}
.footer  p a{
color: #337ab7;
font-weight: 600;
}
.footer  p a:hover{
	text-decoration:underline;
}
/*---//footer---*/
/*---main-content-start---*/
.widget {
width: 32%;
border: 1px solid #F5F1F1;
padding: 0px;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.widget button {
font-size: 13px;
}
.charts-grids.widget {
width: 39%;
padding: 20px;
}
.widget.states-mdl {
margin: 0 0% 0% 1%;
width: 60%;
}
.stat {
padding-right: 0;
}
.stats-left {
float: left;
width: 70%;
background-color: #4F52BA;
	text-align: center;
	padding: 1em;
}
.states-mdl .stats-left {
background-color: #585858;
}
.states-mdl .stats-right {
background-color: rgba(88, 88, 88, 0.88);
}
.states-last .stats-left {
background-color: #e94e02;
}
.states-last .stats-right {
background-color: rgba(233, 78, 2, 0.84);
}
.stats-right {
float: right;
width: 30%;
text-align: center;
	padding: 1.54em 1em;
	background-color: rgba(79, 82, 186, 0.88);
}
.stats-left h5 {
color: #fff;
font-size: 1em;
}
.stats-left h4 {
font-size: 2em;
color: #fff;
margin-top: 10px;
}
.stats-right label {
font-size: 2em;
color: #fff;
}
/*--charts--*/
.charts,.row {
margin: 0 0 1em 0;
}
.charts-grids {
background-color: #fff;
padding:1em;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.w3ls-high {
margin-right: .5em;
width: 49%;
}
.agileits-high {
margin-left: .5em;
margin-right: 0;
width: 49%;
}
.charts-grids canvas#bar {
width: 100% !important;
}
.charts canvas#line {
width: 100% !important;
}
h4.title {
font-size: 1.1em;
color: #444;
margin: 0.5em 0 1em;
text-transform: uppercase;
}
/*--//charts--*/
.widget-shadow {
background-color: #fff;
box-shadow: 0 -1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
	-webkit-box-shadow: 0 -1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
	-moz-box-shadow: 0 -1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
}
/*--statistics--*/
.stats-info.widget {
padding: 1em;
background-color: #fff;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.stats-info ul li {
margin-bottom: 1em;
border-bottom: 1px solid #EFE9E9;
padding-bottom: 10px;
font-size: 0.9em;
color: #555;
}
.progress.progress-right {
width: 25%;
float: right;
height: 8px;
margin-bottom: 0;
}
.stats-info ul li.last {
border-bottom: none;
padding-bottom: 0;
margin-bottom: 0.5em;
}
.stats-info span.pull-right {
font-size: 0.7em;
margin-left: 11px;
line-height: 2em;
}
.stats-info.stats-last {
padding: 1.15em 1em;
width: 66%;
margin-left: 2%;
}
.table.stats-table {
margin-bottom: 0;
}
.stats-table span.label{
font-weight: 500;
}
.stats-table h5 {
color: #4F52BA;
font-size: 0.9em;
}
.stats-table h5.down {
color: #D81B60;
}
.stats-table h5 i.fa {
font-size: 1.2em;
font-weight: 800;
margin-left: 3px;
}
.stats-table thead tr th {
color: #555;
}
.stats-table td {
font-size: 0.9em;
color: #555;
padding: 11px !important;
}
/*--//statistics--*/
/*--map--*/
.map {
padding: 1em;
}
/*--//map--*/
/*--social-media--*/
.social-media {
padding: 0;
margin-left: 2%;
width: 31.3%;
}
.wid-social {
display: inline-block;
width: 33.33%;
padding: 15px;
float: left;
	text-align: center;
}
.top-content a i.fa{
color: #fff;
font-size: 35px;
}
ul.info {
background: #fff;
padding: .55em 1em;
text-align: center;
}
ul.info p {
color: #b3b3b3;
}
ul.info li {
list-style-type: none;
}
.content-top {
margin-bottom: 1em;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.facebook {
background-color:#3b5998 !important;
color: #ffffff !important;
padding: 1em;
text-align: center;
border-top-left-radius: 2px;
border-top-right-radius: 2px;
}
.icon-xlg {
font-size: 30px;
}
.wid-social .social-info h3 {
	color: rgba(255, 255, 255, 0.91);
font-weight: 800;
font-size: 1.5em;
margin: 0.3em 0;
}
.wid-social .social-info h4 {
margin: 0;
font-size: 0.8em;
color: #fff;
letter-spacing: 1px;
}
.twitter {
background-color:#55acee !important;
color: #ffffff !important;
padding: 1em;
text-align: center;
border-top-left-radius: 2px;
border-top-right-radius: 2px;
}
.google-plus {
background-color: #dc4e41 !important;
color: #ffffff !important;
padding: 1em;
text-align: center;
border-top-left-radius: 2px;
border-top-right-radius: 2px;
}
.dribbble {
background-color:#ea4c89 !important;
color: #ffffff !important;
}
.xing {
background-color: #cfdc00 !important;
color: #ffffff !important;
}
.vimeo {
background-color: #162221 !important;
color: #ffffff !important;
}
.yahoo {
background-color: #410093 !important;
color: #ffffff !important;
}
.flickr {
background-color: #a4c639 !important;
color: #ffffff !important;
}
.rss {
background-color: #f26522 !important;
color: #ffffff !important;
}
.wid-social.youtube {
width: 100%;
	background-color: #cd201f !important;
	color: #ffffff !important;
}
.wid-social.youtube .icon-xlg {
font-size: 38px;
}
.youtube .social-icon {
display: inline-block;
margin-right: 6em;
vertical-align: super;
}
.youtube .social-info {
display: inline-block;
}
.wid-social:hover .social-icon {
transform: rotatey(360deg);
transition: .5s all;
}
/*--//social-media--*/
/*--calender --*/
.calender {
padding: 1em 1.5em 1.5em;
}
/*--//calender --*/
/*---//main-content---*/
/*-- media --*/
h2.title1,h3.title1 {
font-size: 1.7em;
color: #629aa9;
margin-bottom: 0.8em;
}
.bs-example5{
background:#fff;
padding:2em;
}
.media-heading {
color: #000;
}
.sidebard-panel .feed-element, .media-body, .sidebard-panel p {
font-size:0.85em;
color:#999;
}
.example_6{
	margin:2em 0 0 0;
}
.demolayout {
background:#F2B33F;
width: 60px;
overflow: hidden;
}
.padding-5 {
padding: 5px;
}
.demobox {
background:#f0f0f0;
color: #333;
font-size: 13px;
text-align: center;
line-height:30px;
display: block;
}
.padding-l-5 {
padding-left: 5px;
}
.padding-r-5 {
padding-right: 5px;
}
.padding-t-5 {
padding-top: 5px;
}
.padding-b-5 {
padding-bottom: 5px;
}
code {
background:rgb(246, 255, 252);
padding: 2px 2px;
color: #000;
}
.media_1-left {
padding: 0;
background-color: #fff;
width: 49%;
}
.media_1-right {
float: left;
margin-left: 2%;
width: 49%;
padding: 0;
}
.media_1{
	margin:2em 0 0 0;
	padding-bottom: 1px;
}
.media_box{
	margin-bottom:2em;
}
.media_box1{
	margin-top:2em;
}
.media {
margin-top:45px !important;
}
.media:first-child {
margin-top: 0 !important;
padding: 0 1px;
}
.panel_2{
	padding:2em 2em 0;
	background:#fff;
}
.panel_2 p{
	color:#555;
	font-size:0.85em;
	margin-bottom:1em;
}
td.head {
color: #000 !important;
font-size: 1.2em !important;
}
/*--Typography--*/
h3.hdg {
font-size: 2em;
}
.show-grid [class^=col-] {
background: #fff;
text-align: center;
margin-bottom: 10px;
line-height: 2em;
border: 10px solid #f0f0f0;
}
.show-grid [class*="col-"]:hover {
background: #e0e0e0;
}
.xs h3, h3.m_1{
	color:#000;
	font-size:1.7em;
	font-weight:300;
	margin-bottom: 1em;
}
.grid_3 p{
color: #999;
font-size: 0.85em;
margin-bottom: 1em;
font-weight: 300;
}
.label {
font-weight: 300 !important;
border-radius:4px;
}
.grid_3 {
padding: 1.5em 1em;
}
.grid_5{
	margin-top: 2em;
}
.grid_5 h3, .grid_5 h2, .grid_5 h1, .grid_5 h4, .grid_5 h5, h3.hdg {
	margin-bottom:0.6em;
	color: #555;
}
.pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus {
z-index: 0;
}
.badge-primary {
background-color: #03a9f4;
}
.badge-success {
background-color: #8bc34a;
}
.badge-warning {
background-color: #ffc107;
}
.badge-danger {
background-color: #e51c23;
}
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
border-top: none;
}
.grid_3 p{
line-height: 2em;
color: #888;
font-size: 0.9em;
margin-bottom: 1em;
font-weight: 300;
}
.bs-docs-example {
margin: 1em 0;
}
section#tables  p {
margin-top: 1em;
}
@media (max-width:768px){
.grid_3 {
	margin-bottom: 0em;
}
}
@media (max-width:640px){
	h1, .h1, h2, .h2, h3, .h3 {
		margin-top: 0px;
		margin-bottom: 0px;
	}
	.grid_5 h3, .grid_5 h2, .grid_5 h1, .grid_5 h4, .grid_5 h5, h3.hdg, h3.bars {
		margin-bottom: .5em;
	}
	.progress {
		height: 10px;
		margin-bottom: 10px;
	}
	ol.breadcrumb li,.grid_3 p,ul.list-group li,li.list-group-item1 {
		font-size: 14px;
	}
	.breadcrumb {
		margin-bottom: 10px;
	}
	.well {
		font-size: 14px;
		margin-bottom: 10px;
	}
	h2.typoh2 {
		font-size: 1.5em;
	}
.grid_4 {
margin-top: 30px;
}
}
@media (max-width:480px){
	.table h1 {
		font-size: 26px;
	}
	.table h2 {
		font-size: 23px;
	}
	.table h3 {
		font-size: 20px;
	}
	.alert,p {
		font-size: 14px;
	}
	.pagination {
		margin: 20px 0 0px;
	}
}
@media (max-width: 320px){
	.grid_4 {
		margin-top: 18px;
	}
	h3.title {
		font-size: 1.6em;
	}
	.alert, p,ol.breadcrumb li, .grid_3 p,.well, ul.list-group li, li.list-group-item1,a.list-group-item {
		font-size: 13px;
	}
	.alert {
		padding: 10px;
		margin-bottom: 10px;
	}
	ul.pagination li a {
		font-size: 14px;
		padding: 5px 11px !important;
	}
	.list-group {
		margin-bottom: 10px;
	}
}
/*--//Typography--*/
/*--table--*/
.tables h4 {
font-size: 1.4em;
margin-bottom: 1em;
color: #777777;
}
.tables .panel-body ,.tables .bs-example{
padding: 2em 2em 0.5em;
}
.tables .table > thead > tr > th, .tables .table > tbody > tr > th, .tables .table > tfoot > tr > th, .tables .table > thead > tr > td, .tables .table > tbody > tr > td, .tables .table > tfoot > tr > td {
padding: 13px;
	border-top: 1px solid #E0E0E0;
}
.bs-example {
margin-top: 2em;
}
.table-hover > tbody > tr:hover {
background-color: #E8E6E6;
}
/*--//table--*/
/*--forms--*/
.forms h4 {
font-size: 1.3em;
color: #6F6F6F;
}
.form-title {
padding: 1em 2em;
background-color: #f5f5f5;
border-bottom: 1px solid #ddd;
}
.form-body {
padding: 1.5em 2em;
}
.inline-form .form-group,.inline-form .checkbox, .form-two .form-group{
margin-right: 1em;
}
.forms label {
font-weight: 400;
}
.form-control {
border-radius: inherit;
}
.help-block {
margin-top: 10px;
}
.forms button.btn.btn-default {
background-color: #F2B33F;
color: #fff;
padding: .5em 1.5em;
	border: none;
	outline:none;
	border-radius: inherit;
}
.inline-form.widget-shadow {
margin-top: 2em;
}
.form-two {
margin-top: 2em;
}
.form-three{
margin-top:2em;
padding: 2em;
}
/* --  general forms  -- */
.form-control1, .form-control_2.input-sm{
border: 1px solid #ccc;
padding: 5px 8px;
color: #616161;
background: #fff;
box-shadow: none !important;
width: 100%;
font-size: 0.85em;
font-weight: 300;
height: 40px;
border-radius: 0;
-webkit-appearance: none;
resize: none;
}
.general .tab-content {
padding: 1.5em 0.5em 0;
}
.control3{
	margin:0 0 1em 0;
}
.tag_01{
margin-right:5px;
}
.tag_02{
margin-right:3px;
}
.control2{
height:200px;
}
.bs-example4 {
background: #fff;
padding: 2em;
}
button.note-color-btn {
width: 20px !important;
height: 20px !important;
border: none !important;
}
.show-grid [class^=col-] {
background: #fff;
text-align: center;
margin-bottom: 10px;
line-height: 2em;
border: 10px solid #f0f0f0;
}
.show-grid [class*="col-"]:hover {
background: #e0e0e0;
}
.xs h3, .widget_head{
	color:#000;
	font-size:1.7em;
	font-weight:300;
	margin-bottom: 1em;
}
.grid_3 p{
color: #999;
font-size: 0.85em;
margin-bottom: 1em;
font-weight: 300;
}
.input-icon.right > i, .input-icon.right .icon {
right:12px;
float: right;
}
.input-icon > i, .input-icon .icon {
position: absolute;
display: block;
margin: 10px 8px;
line-height: 14px;
color: #999;
}
.form-group input#disabledinput {
	cursor: not-allowed;
}
/*--//forms--*/
/*--validation--*/
.validation-grids {
padding: 0;
width: 49%;
}
.validation-grids.validation-grids-right {
margin-left: 2%;
}
.validation-grids .radio{
display: inline-block;
margin: 0.5em 2em 0 0;
}
.help-block {
font-size: 0.8em;
color: #6F6F6F;
margin-left: .5em;
}
.mid-content-top {
margin-right: .2em;
}
.validation-grids .btn-primary{
background: #337ab7 !important;
color: #FFF;
border: none;
font-size: 1em;
font-weight: 400;
padding: .5em 1.2em;
width: 100%;
margin-top: 1.5em;
outline: none;
display:block;
transition: 0.5s all;
-webkit-transition: 0.5s all;
-moz-transition: 0.5s all;
-o-transition: 0.5s all;
-ms-transition: 0.5s all;
	border-radius: inherit;
opacity: 1;
}
.validation-grids .btn-primary:hover{
	background: #F2B33F !important;
}
.bottom .btn-primary {
margin: 0;
}
.bottom .form-group {
margin-bottom: 0;
}
/*--//validation--*/
/*--grids--*/
.grids {
padding: 2em 1em;
	margin-bottom: 2em;
}
.grids .form-group {
margin: 0;
}
.grid-bottom{
padding: 2em;
}
.grid-bottom .table{
	margin:0;
}
/*--//grids--*/
/*--.blank-page--*/
.blank-page{
padding: 2em;
}
.blank-page p {
font-size: 1em;
color: #555;
line-height: 1.8em;
}
/*--//blank-page--*/
/*-- 404 page --*/
.error-page h2 {
font-size: 100px;
color: #629aa9;
}
.error-page {
background: #fff;
	padding:2em 0;
text-align: center;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
form.search-form {
width: 30%;
margin: 0 auto;
}
.error-page h3 {
color: #888;
margin-bottom: 1em;
}
.error-page input.form-control {
height: 36px;
}
.error-page h3 i.fa {
color: #F2B33F;
}
.error-page p {
letter-spacing: 1px;
	width:40%;
	margin: 30px auto 0;
line-height: 28px;
color: #444;
}
/*--//404 page--*/
.blank-page {
background: #fff;
}
/*--login-page--*/
.login-page {
width: 40%;
margin: 1em auto 0;
}
.signup-page {
width: 40%;
margin: 1em auto 0;
}
.login-page h2.title1,.signup-page h2.title1{
text-align: center;
padding: 1em;
background: #629aa9;
border-bottom: 10px solid #4b7884;
margin: 0;
	color: #fff;
font-size: 1.6em;
}
.login-top {
padding: 1.5em;
border-bottom: 1px solid #DED9D9;
text-align: center;
}
.login-body {
	padding: 4em 2em;
}
.login-top  h4 {
font-size: 1.1em;
color: #555;
line-height: 1.8em;
}
.login-top  h4  a {
color: #e94e02;
}
.login-top  h4  a:hover{
color: #555;
}
.login-page input[type="email"], .login-page input[type="password"] {
font-size: 1em;
padding: 14px 15px 14px 37px;
width: 100%;
color: #A8A8A8;
outline: none;
border: 1px solid #D3D3D3;
background: #FFFFFF;
margin: 0em 0em 1.5em 0em;
}
.login-page input.user {
background: url(../images/user.png)no-repeat 8px 16px #fff;
}
.login-page input.lock {
background: url(../images/lock.png)no-repeat 8px 16px #fff;
}
.login-page label.checkbox {
margin: 0 0 0 1.3em;
font-size: 1em;
color: #555;
font-weight: 400;
display: inline-block;
float: left;
}
.login-page label.checkbox {
margin-left: 1.3em;
}
.login-page label.checkbox {
margin-left: 1.3em;
font-size: 1em;
color: #555;
font-weight: 400;
display: inline-block;
	cursor: pointer;
}
.forgot-grid {
margin-bottom: 1.2em;
}
.forgot {
float: right;
}
.forgot a {
font-size: 1em;
color: #555;
display:block;
}
.forgot a:hover{
color: #f2b33f;
}
.login-page input[type="submit"] {
border: none;
outline: none;
cursor: pointer;
color: #fff;
background: #F2B33F;
width: 40%;
padding: .6em 1em;
font-size: 1em;
margin: 0.5em auto 0;
display: block;
	transition: 0.5s all;
	-webkit-transition: 0.5s all;
	-moz-transition: 0.5s all;
	-ms-transition: 0.5s all;
	-o-transition: 0.5s all;
}
.login-page input[type="submit"]:hover{
background: #337ab7;
}
.login-page-bottom {
text-align: center;
}
.social-btn {
display: inline-block;
background: #3B5998;
transition: all 0.5s ease-out;
-webkit-transition: all 0.5s ease-out;
-moz-transition: all 0.5s ease-out;
-ms-transition: all 0.5s ease-out;
-o-transition: all 0.5s ease-out;
}
.registration {
text-align: center;
margin-top: 1em;
font-size: 16px;
}
.registration a {
color: #337ab7;
}
.registration a:hover{
color: #f2b33f;
}
.social-btn i {
color: #fff;
padding: .8em 1.3em;
font-size: 0.9em;
	vertical-align: middle;
}
.social-btn i.fa {
background-color: #354F88;
padding: .6em 1em;
font-size: 1.1em;
	transition:.5s all;
	-webkit-transition:.5s all;
	-moz-transition:.5s all;
}
.social-btn:hover {
	background:#354F88;
}
.social-btn.sb-two {
background-color: #45B0E3;
	margin-left: 2em;
}
.social-btn i.fa.fa-twitter {
background-color: #40A2D1;
}
.social-btn.sb-two:hover{
background-color: #40A2D1;
}
.login-page-bottom h5 {
font-size: 1.5em;
color: #524C4F;
font-weight: 800;
margin: 1em 0;
}
.social-btn:hover i.fa {
transform: rotateY(360deg);
	-moz-transform: rotateY(360deg);
	-webkit-transform: rotateY(360deg);
	-o-transform: rotateY(360deg);
	-ms-transform: rotateY(360deg);
}
/*--//login-page--*/
/*-- icons --*/
.codes a {
color: #999;
}
.icon-box {
padding: 8px 15px;
background:rgba(149, 149, 149, 0.25);
margin: 1em 0 1em 0;
border: 5px solid #ffffff;
text-align: left;
-moz-box-sizing: border-box;
-webkit-box-sizing: border-box;
box-sizing: border-box;
transition: 0.5s all;
-webkit-transition: 0.5s all;
-o-transition: 0.5s all;
-ms-transition: 0.5s all;
-moz-transition: 0.5s all;
cursor: pointer;
}
.icon-box:hover {
background: #629aa9;
	transition:0.5s all;
	-webkit-transition:0.5s all;
	-o-transition:0.5s all;
	-ms-transition:0.5s all;
	-moz-transition:0.5s all;
}
.icon-box:hover i.fa {
	color:#fff !important;
}
.icon-box:hover a.agile-icon {
	color:#fff !important;
}
.codes .bs-glyphicons li {
float: left;
width: 12.5%;
height: 115px;
padding: 10px;
line-height: 1.4;
text-align: center;
font-size: 12px;
	list-style-type: none;
}
.codes .bs-glyphicons .glyphicon {
margin-top: 5px;
margin-bottom: 10px;
font-size: 24px;
}
.codes .glyphicon {
position: relative;
top: 1px;
display: inline-block;
font-family: 'Glyphicons Halflings';
font-style: normal;
font-weight: 400;
line-height: 1;
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
	color: #777;
}
.codes .bs-glyphicons .glyphicon-class {
display: block;
text-align: center;
word-wrap: break-word;
}
h3.icon-subheading {
	font-size: 28px;
color:#555 !important;
margin: 15px 0 15px;
}
h2.agileits-icons-title,h3.agileits-icons-title {
text-align: center;
font-size: 33px;
color: #F2B33F;
}
.icons a {
color: #777;
font-size: 14px;
}
.icon-box i {
margin-right: 10px !important;
font-size: 16px !important;
color: #333 !important;
}
.bs-glyphicons li {
float: left;
width: 18%;
height: 115px;
padding: 10px;
line-height: 1.4;
text-align: center;
font-size: 12px;
list-style-type: none;
background:rgba(149, 149, 149, 0.18);
margin: 1%;
}
.bs-glyphicons .glyphicon {
margin-top: 5px;
margin-bottom: 10px;
font-size: 24px;
	color: #282a2b;
}
.glyphicon {
position: relative;
top: 1px;
display: inline-block;
font-family: 'Glyphicons Halflings';
font-style: normal;
font-weight: 400;
line-height: 1;
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
	color: #777;
}
.bs-glyphicons .glyphicon-class {
display: block;
text-align: center;
word-wrap: break-word;
}
@media (max-width:991px){
	h2.agileits-icons-title,h3.agileits-icons-title {
		font-size: 28px;
	}
	h3.icon-subheading {
		font-size: 22px;
	}
}
@media (max-width:768px){
	h2.agileits-icons-title,h3.agileits-icons-title {
		font-size: 28px;
	}
	h3.icon-subheading {
		font-size: 25px;
	}
	/*.row {
		margin-right: 0;
		margin-left: 0;
	}*/
	.icon-box {
		margin: 0;
	}
}
@media (max-width: 640px){
	.icon-box {
		float: left;
		width: 50%;
	}
}
@media (max-width: 480px){
	.bs-glyphicons li {
		width: 31%;
	}
}
@media (max-width: 414px){
	h2.agileits-icons-title,h3.agileits-icons-title {
		font-size: 23px;
	}
	h3.icon-subheading {
		font-size: 18px;
	}
	.bs-glyphicons li {
		width: 31.33%;
	}
}
@media (max-width: 384px){
	.icon-box {
		float: none;
		width: 100%;
	}
}
.grid_3.grid_4 {
background: #fff ! important;
padding: 2em ! important;
box-shadow: 0 1px 1px rgba(0,0,0,.05);
-o-box-shadow: 0 1px 1px rgba(0,0,0,.05);
-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
-moz-box-shadow: 0 1px 1px rgba(0,0,0,.05);
}
/*-- //icons --*/
/*-- sign-up --*/
.signup-page h3.title1 {
	text-align: center;
}
p.creating {
text-align: center;
}
.sign-up-row {
padding: 2em;
}
.sign-up1{
	float:left;
width: 25%;
}
.sign-up1 h4 {
color: #555;
margin: 1em 0 1em;
font-size: 1em;
}
.sign-up2 {
width: 70%;
float: left;
margin-top: 1em;
}
.signup-page input[type="text"],.signup-page input[type="email"],.signup-page input[type="password"]{
	outline:none;
	border: 1px solid #D0D0D0;
background: none;
font-size: 15px;
padding: 14px 15px 14px 37px;
	width:100%;
margin: 0em 0 1.5em 0;
}
.signup-page input[type="email"]{
background: url(../images/mail.png)no-repeat 8px 16px #fff;
	background-size: 20px;
}
.signup-page input[type="text"] {
background: url(../images/user.png)no-repeat 8px 16px #fff;
}
.signup-page input[type="password"] {
background: url(../images/lock.png)no-repeat 8px 16px #fff;
}
.signup-page input[type="text"]:focus ,.signup-page input[type="email"]:focus,.signup-page input[type="password"]:focus,.login-page input[type="email"]:focus,.login-page input[type="password"]:focus{
border-color: #337ab7;
}
.signup-page h5,.signup-page h6{
	margin: 0 0 1em;
color: #629aa9;
font-size: 1.1em;
}
.signup-page h6{
	margin:1em 0 !important;
}
.sub-login-left{
	float:left;
	width:30%;
}
.sub-login-right{
	float:right;
}
.sub-login{
	margin:5em 0 0;
}
.signup-page p{
font-size: 1em;
color: #555;
}
.sub-login-right p a {
color: #a88add;
padding-left: 8px;
}
.sub-login-right p a:hover{
	color:#fff;
}
.sub_home  input[type="submit"] {
border: none;
outline: none;
cursor: pointer;
color: #fff;
background: #f2b33f;
width: 25%;
padding: .6em 1em;
font-size: 1em;
margin: 0.5em auto 0em;
display: block;
	transition: 0.5s all;
	-webkit-transition: 0.5s all;
	-moz-transition: 0.5s all;
	-ms-transition: 0.5s all;
	-o-transition: 0.5s all;
	-ms-transition: 0.5s all;
}
.sub_home input[type="submit"]:hover {
background-color: #337ab7;
}
.signup-page .radios {
margin-top: 1.5em;
}
.signup-page label.label_radio {
margin-right: 2em;
color: #7D7878;
font-size: 1em;
}
/*-- //sign-up --*/
/*-- charts-page--*/
.chrt-page-grids {
width: 49%;
border: 1px solid #F5F1F1;
padding: 2em;
box-shadow: 0 -1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
	-webkit-box-shadow: 0 -1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
	-moz-box-shadow: 0 -1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);
background-color: #fff;
}
.chrt-page-grids.chrt-right {
margin-left: 2%;
}
.doughnut-grid ,.polar-area,.pie-grid{
width: 74%;
margin: 2.2em auto;
}
.polar-area {
margin: 2.9em auto 1em;
}
.pie-grid{
margin: 2.8em auto 1em;
}
.radar-grid {
width: 85%;
margin: 0 auto;
}
.chrt-page-grids  canvas#bar {
width: 100% !important;
}
/*--//charts-page--*/
/*--general elements--*/
.panel-info.widget-shadow {
padding: 2em 1em;
}
.panel {
border-radius: inherit;
}
.general h4.title2{
font-size: 1.4em;
margin: 0 0 1em 1em;
color: #777777;
}
.modals{
margin-top: 2em;
padding: 2em 1em;
}
.modal .row {
margin: 1em 0 0;
}
h4.modal-title {
margin: 0;
}
.modal-grids button.btn.btn-primary {
background-color: #F2B33F;
font-size: 1em;
color: #fff;
	border-color: #F2B33F;
	outline:none;
	border-radius: 3px;
}
.general-grids {
padding: 2em 1.5em;
width: 49%;
}
.general-grids.grids-right {
margin-left: 2%;
}
/*--ScrollSpy --*/
.scrollspy-example {
position: relative;
height: 200px;
margin-top: 10px;
overflow: auto;
padding-right: 20px;
}
.scrollspy-example h4 {
font-size: 1.2em;
color: #629aa9;
margin-bottom: .5em;
}
.scrollspy-example p {
font-size: 0.9em;
color: #555;
line-height: 1.8em;
margin-bottom: 2em;
}
.general-grids ul.dropdown-menu{
padding: 0.5em;
}
.general-grids h4.title2 ,.tool-tips h4.title2{
margin: 0 0 1em;
}
/*--tabs --*/
.nav-tabs {
border-bottom: 1px solid #797979;
}
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
border: 1px solid #797979;
	border-bottom-color: transparent;
}
.tab-content p {
font-size: 0.9em;
color: #555;
line-height: 1.8em;
}
/*--tool-tips--*/
.tool-tips.widget-shadow {
padding: 2em;
margin-top: 2em;
}
.bs-example-tooltips {
margin-bottom: 1em;
	text-align: center;
}
.bs-example-tooltips .btn-default {
margin-right: 1em;
}
.collps-grids{
width: 49%;
}
/*--//general elements--*/
/*-- col_3 --*/
.widget{
	padding:0;
}
.col-md-3.widget {
width: 18.8%;
-webkit-transition: 0.5s all;
-moz-transition:  0.5s all;
-o-transition:  0.5s all;
-ms-transition:  0.5 sall;
transition:  0.5s all;
}
.widget:hover i.fa {
-webkit-transform: scale(1.1);
-moz-transform: scale(1.3);
-o-transform: scale(1.3);
-ms-transform: scale(1.3);
transform: scale(1.1);
-webkit-transition: 0.5s all;
-moz-transition:  0.5s all;
-o-transition:  0.5s all;
-ms-transition:  0.5 sall;
transition:  0.5s all;
}
/*.r3_counter_box {
min-height: 100px;
background: #ffffff;
padding: 15px;
}*/

.r3_counter_box {
min-height: 100px;
background: #ffffff;
padding: 15px;
margin-bottom:14px;
}
.stats {
overflow: hidden;
}
.r3_counter_box .fa {
margin-right: 0px;
font-size: 25px;
width: 66px;
height: 66px;
text-align: center;
line-height: 65px;
-webkit-transition: 0.5s all;
-moz-transition: 0.5s all;
-o-transition: 0.5s all;
-ms-transition: 0.5 sall;
transition: 0.5s all;
}
.stats span{
	color:#777;
	font-size:16px;
}
.fa.pull-left {
margin-right: 5% !important;
}
.icon-rounded{
background-color:#7460ee;
color: #ffffff;
border-radius: 50px;
-webkit-border-radius: 50px;
-moz-border-radius: 50px;
-o-border-radius: 50px;
-ms-border-radius: 50px;
font-size: 25px;
}
.r3_counter_box.stats {
padding-left: 85px;
}
.r3_counter_box h5 {
margin: 10px 0 5px 0;
color:#000;
font-weight:600;
font-size: 20px;
}
i.user1{
	background: #fc4b6c;
}
i.user2{
	background: #1e88e5;
}
i.dollar1{
	background: #ffb22b;
}
i.dollar2{
	background: #00ad45;
}
.widget1 {
margin-right: 1.5%;
}
.widget{}
.world-map {
width: 64%;
float: left;
background: #4597a8;
position: relative;
padding: 2em 2em 0 2em;
}
.world-map h3 {
float: left;
font-size: 1.9em;
color: #fff;
font-weight: 600;
padding: 0em 0 0.5em 0;
}
.world-map p {
float: right;
font-size: 1.3em;
color: #fff;
font-weight: 300;
padding: 0.5em 0 0.5em 0;
}
.row-one{
	margin-top:20px;
}
/*-- col_3 --*/
/*--inbox--*/
.inbox-page {
width: 95%;
margin: 20px auto;
}
.inbox-row {
	padding: 0.5em 1em;
}
.inbox-page h4 {
font-size: 1.1em;
color: #629aa9;
margin-bottom: 1em;
}
.mail {
float: left;
margin-right: 1em;
}
.mail.mail-name {
width: 15%;
}
.mail-right {
float: right;
margin-left: 1.5em;
}
li.head i.fa {
color: #629aa9;
margin-right: 10px;
}
.inbox-page h6 {
font-size: 1em;
color: #555;
}
.inbox-page input.checkbox {
margin: 13px 0 0;
}
.inbox-page img {
width: 100%;
vertical-align: bottom;
}
.inbox-page p {
font-size: 1em;
color: #000;
line-height: 1.8em;
}
.inbox-page h6 {
font-size: 1em;
color: #555;
line-height: 2em;
}
.inbox-page ul.dropdown-menu {
padding: 5px 0;
min-width: 105px;
top: 0;
left: -110px;
}
.inbox-page .dropdown-menu > li > a {
padding: 4px 15px;
font-size: 0.9em;
}
.inbox-page .dropdown-menu > li > a:hover, .inbox-page .dropdown-menu > li > a:focus {
color: #337ab7;
}
.mail-icon {
margin-right: 7px;
}
.inbox-page.row {
margin-top: 2em;
}
.inbox-page .checkbox {
position: relative;
top: -3px;
margin: 0 1rem 0 0;
cursor: pointer;
}
.inbox-page .checkbox:before {
-webkit-transition: all 0.3s ease-in-out;
-moz-transition: all 0.3s ease-in-out;
transition: all 0.3s ease-in-out;
content: "";
position: absolute;
left: 0;
z-index: 1;
width: 15px;
height: 15px;
border: 1px solid #A0A0A0;
}
.inbox-page .checkbox:after {
content: "";
position: absolute;
top: -0.125rem;
left: 0;
width: 1.1rem;
height: 1.1rem;
background: #fff;
cursor: pointer;
}
.inbox-page .checkbox:checked:before {
-webkit-transform: rotate(-45deg);
-moz-transform: rotate(-45deg);
-ms-transform: rotate(-45deg);
-o-transform: rotate(-45deg);
transform: rotate(-45deg);
height: .4rem;
width: .8rem;
border-color: #4F52BA;
border-top-style: none;
border-right-style: none;
border-width: 2px;
}
.mail-body {
padding: 1em 2em;
border: 1px solid #D4D4D4;
margin: 10px 0;
transition: .5s all;
}
.mail-body p{
font-size: 0.9em;
line-height: 1.8em;
}
.mail-body input[type="text"]{
width: 100%;
border: none;
color: #000;
border-bottom: 1px solid #F5F5F5;
padding: 1em 0;
	outline:none;
	transition:.5s all;
	-webkit-transition:.5s all;
	-moz-transition:.5s all;
	font-size:1em;
}
.mail-body input[type="text"]:focus{
	padding: 2em 0;
	border-bottom: 1px solid #C7C5C5;
}
.mail-body input[type="submit"] {
border: none;
background: none;
font-size: 1em;
margin-top: 0.5em;
color: #4F52BA;
	outline:none;
	font-weight: 600;
}
/*--//inbox--*/
/*--compose mail--*/
.compose-left{
width: 28%;
	padding: 0;
}
.compose-right{
width: 70%;
margin-left: 2%;
	padding: 0;
}
.compose-left a i.fa {
margin-right: 0.7em;
}
.compose-left ul li{
	display:block;
}
.compose-left ul li.head {
padding: 0.5em 1.5em;
border-bottom: 1px solid #DCDCDC;
color: #000;
font-size: 1.2em;
background-color: #F5F5F5;
}
.compose-left ul li a {
display: block;
font-size: 1em;
color: #555;
border-bottom: 1px solid #DCDCDC;
padding: 0.7em 1.5em;
}
.compose-left ul li a:hover {
background-color: rgb(241, 241, 241);
}
.compose-left span {
float: right;
background-color: #F2B33F;
padding: 3px 10px;
font-size: .7em;
border-radius: 4px;
color: #fff;
}
.chat-left {
position: relative;
float: left;
width: 25%;
}
.chat-right {
float: left;
}
.small-badge {
position: absolute;
left: 27px;
top: 1px;
overflow: hidden;
width: 12px;
height: 12px;
padding: 0;
border: 2px solid #fff!important;
border-radius: 20px;
background-color: red;
}
.small-badge.bg-green {
background-color: green;
}
.chat-grid.widget-shadow {
margin-top: 2em;
}
.chat-right p {
font-size: 1em;
color: #000;
	line-height: 1.2em;
}
.chat-right h6 {
font-size: 0.8em;
color: #999;
line-height: 1.4em;
}
.compose-right .panel-heading {
padding: 0.8em 2em;
}
.compose-right .panel-body {
padding: 2em;
}
.compose-right .alert.alert-info {
padding: 10px 20px;
font-size: 0.9em;
color: #255a87;
background-color: #255a8736;
border-color: #255a8736;
	border-radius: inherit;
}
.compose-right .form-group {
margin: .5em 0;
}
.compose-right .btn.btn-file {
position: relative;
overflow: hidden;
	border-radius: inherit;
background: #f2b33f;
color: #fff;
}
.compose-right .btn.btn-file>input[type='file'] {
position: absolute;
top: 0;
right: 0;
opacity: 0;
filter: alpha(opacity=0);
outline: none;
background: white;
cursor: inherit;
display: inline-flex;
width: 100%;
padding: 0.4em;
}
.compose-right p.help-block {
display: inline-block;
margin-left: 0.5em;
font-size: 0.9em;
color: #6F6F6F;
}
.compose-right input[type="submit"] {
font-size: 0.9em;
background-color: #255a87;
border: 1px solid #255a87;
color: #fff;
padding: 0.4em 1em;
margin-top: 1em;
}
/*--//compose mail--*/
/*--widgets-page--*/
/*---photoday-section-----*/
.widgettable .card {
background: #fff;
margin-bottom: 20px;
	-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
*, :active, :focus, :hover {
outline: 0!important;
-webkit-tap-highlight-color: transparent!important;
}
.widgettable .wthree-crd {
padding-left: 0;
}
.widgettable .w3-agile-crd {
padding-right: 0;
}
.wthree-crd.widgettable {
padding-left: 0;
}
.w3-agile-crd.widgettable {
padding-right: 0;
}
.w3-agileits-crd.widgettable {
padding: 0px;
}
.p-r-20 {
padding-right: 20px!important;
}
.p-l-20 {
padding-left: 20px!important;
}
.m-0 {
margin: 0!important;
}
.widgettable .widget-report-table h3 {
line-height: 36px;
font-size: 19px;
color: #000;
}
.widgettable  .c-teal {
color: #fdbd10!important
}
.widget table tr,.widget table th,.widget table td {
border: none;
border-bottom: 1px solid #e8e8e8;
border-top: 1px solid #e8e8e8;
}
.widget p {
margin: 0 0 6px;
}
.f-300 {
font-weight: 500!important;
	font-size:22px;
}
.m-t-20 {
margin-top: 10px!important;
}
.text-right {
text-align: right;
}
.p-15 {
padding: 15px!important;
}
.widgettable .widget-report-table .table-bordered, .widget-status-table .table-bordered {
border-top: 0;
}
.widgettable .table-bordered,.widgettable  .table-bordered>tbody>tr>td:last-child,.widgettable  .table-bordered>tbody>tr>th:last-child,.widgettable  .table-bordered>thead>tr>th:last-child {
border-right: 0;
}
.widgettable .table-bordered,.widgettable  .table-bordered>tbody>tr>td,.widgettable  .table-bordered>tbody>tr>th {
border-bottom: 0;
border-left: 0;
}
.card-header h3 {
font-size: 19px;
	margin:0;
color: #000;
}
header.widget-header h4 {
font-size: 19px;
	color:#000;
}
.widgettable .table {
margin-bottom: 0;
}
.widgettable .table-bordered,.widgettable  .table-bordered>tbody>tr>td,.widgettable  .table-bordered>tbody>tr>th,.widgettable  .table-bordered>tfoot>tr>td,.widgettable  .table-bordered>tfoot>tr>th,.widgettable  .table-bordered>thead>tr>td,.widgettable  .table-bordered>thead>tr>th {
border: 1px solid #F5F5F5;
}
.widgettable .table {
width: 100%;
max-width: 100%;
margin-bottom: 16px;
}
.widgettable pre code,.widgettable  table {
background-color: transparent;
}
.widgettable table {
border-collapse: collapse;
border-spacing: 0;
}
.widgettable .table>caption+thead>tr:first-child>td,.widgettable  .table>caption+thead>tr:first-child>th,.widgettable  .table>colgroup+thead>tr:first-child>td,.widgettable  .table>colgroup+thead>tr:first-child>th,.widgettable  .table>thead:first-child>tr:first-child>td,.widgettable  .table>thead:first-child>tr:first-child>th {
border-top: 0;
font-weight: 600;
color: #000;
}
.widgettable .table-bordered>thead>tr>th {
border-left: 0;
}
.widgettable .table>tbody>tr>td,.widgettable  .table>tbody>tr>th,.widgettable  .table>tfoot>tr>td,.widgettable  .table>tfoot>tr>th,.widgettable  .table>thead>tr>td,.widgettable  .table>thead>tr>th {
padding: 12px;
}
.widgettable .table>thead>tr>th {
background-color: #fff;
vertical-align: middle;
font-weight: 500;
color: #333;
border-width: 1px;
}
.btn, .m-sidebar header h2, .p-menu>li>a, .popover-title, .table>thead>tr>th {
text-transform: capitalize;
}
.widgettable .table-bordered>thead>tr>td,.widgettable  .table-bordered>thead>tr>th {
border-bottom-width: 2px;
}
.widgettable .table-bordered,.widgettable  .table-bordered>tbody>tr>td,.widgettable  .table-bordered>tbody>tr>th,.widgettable  .table-bordered>tfoot>tr>td,.widgettable  .table-bordered>tfoot>tr>th,.widgettable  .table-bordered>thead>tr>td,.widgettable  .table-bordered>thead>tr>th {
border: 1px solid #F5F5F5;
}
.widgettable .table>thead>tr>th {
vertical-align: bottom;
border-bottom: 2px solid #F5F5F5;
}
.widgettable .table>tbody>tr>td,.widgettable  .table>tbody>tr>th,.widgettable  .table>tfoot>tr>td,.widgettable  .table>tfoot>tr>th,.widgettable  .table>thead>tr>td,.widgettable  .table>thead>tr>th {
padding: 15px;
line-height: 1.42857143;
vertical-align: top;
border-top: 1px solid #F5F5F5;
}
*, :active, :focus, :hover {
outline: 0!important;
-webkit-tap-highlight-color: transparent!important;
}
caption, th {
text-align: left;
}
td, th {
padding: 0;
}
*, a, button, i, input {
-webkit-font-smoothing: antialiased;
}
.list-group .list-group-item {
border: 0;
margin: 0;
padding: 0px 14px;
}
.list-group-item:first-child {
border-top-right-radius: 2px;
border-top-left-radius: 2px;
}
a.list-group-item, button.list-group-item {
color: #555;
}
.list-group-item {
position: relative;
display: block;
padding: 10px 15px;
margin-bottom: -1px;
background-color: transparent;
border: 1px solid #E9E9E9;
}
.pull-right {
float: right!important;
	margin:0;
}
form.pull-right.mail-src-position {
margin-top: -6px;
}
.agileinfo-cdr {
padding: 25px 27px;
}
.widget.widget-report-table {
padding: 10px 0;
}
.media>.pull-left {
padding-right: 15px;
}
.pull-left {
float: left!important;
}
.widget h4 {
line-height: 100%;
font-size: 18px;
font-weight: 400;
}
hr {
margin-top: 18px;
margin-bottom: 18px;
border-top: 1px solid #eee;
}
.streamline .sl-primary {
border-left-color: #188ae2;
}
.streamline .sl-item {
position: relative;
padding-bottom: 16px;
border-left: 1px solid #ccc;
}
.streamline .sl-item .text-muted {
color: inherit;
opacity: .6;
	font-size:14px;
}
.streamline .sl-item p {
margin-bottom: 10px;
	font-size:16px;
}
.agileinfo-cdr .list-group {
margin-bottom: 0px;
}
.agileinfo-cdr .lg-item-heading {
font-weight: 600;
font-size: 15px;
color: #5a5a5a;
}
.agileinfo-cdr small.lg-item-text {
font-size: 13px;
}
.streamline .sl-primary{
border-left-color: #ff4a43;
}
.streamline .sl-danger {
border-left-color: #22beef;
}
.streamline .sl-success {
border-left-color: #a2d200;
}
.streamline .sl-warning {
border-left-color: #8e44ad;
}
.streamline .sl-item:before {
content: '';
position: absolute;
left: -6px;
top: 0;
background-color: #ccc;
width: 12px;
height: 12px;
border-radius: 100%;
}
.streamline .sl-primary:before, .streamline .sl-primary:last-child:after {
background-color: #ff4a43;
}
.streamline .sl-danger:before, .streamline .sl-danger:last-child:after {
background-color: #22beef;
}
.streamline .sl-success:before, .streamline .sl-success:last-child:after {
background-color: #a2d200;
}
.streamline .sl-warning:before, .streamline .sl-warning:last-child:after {
background-color: #8e44ad;
}
.card .card-body.card-padding {
padding: 17px 27px;
}
.streamline .sl-item .sl-content {
margin-left: 24px;
}
.panel-body .list-group {
margin-bottom: 0;
}
/*---//photoday-section-----*/
/*-- buttons --*/
.agile_info_shadow {
padding: 1em;
background: #fff;
box-shadow: 0 0px 24px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);
-webkit-box-shadow: 0 0px 24px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);
-moz-box-shadow: 0 0px 24px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);
-o-box-shadow: 0 0px 24px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);
border: 1px solid #ddd;
}
.button_set_one {
margin-bottom: 1em;
}
/*-- color-variations --*/
.variations-panel {
padding: 1em;
border-radius: 0;
background: #fff;
margin-bottom: 2em;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.panel-body .col-adjust-8 > .row > div {
width: 11.1% !important;
padding-left: 7px;
padding-right: 7px;
}
.panel-title h3,.hover-buttons h2{
color: #222d32;
font-size: 1.4em;
margin: 0 0 .5em 0;
}
h3.w3_inner_tittle.two {
color: #222d32;
font-size: 1.4em;
margin: 0 0 1em 0;
}
.bg-dark,.bg-primary,.bg-success,.bg-info,.bg-warning,.bg-danger,.bg-alert,.bg-system {
margin-bottom: 1em;
}
.w3l-table-info h2,.agile-tables h3 {
font-size: 28px;
color: #222d32;
}
.mb10 {
margin-bottom: 10px !important;
}
.fw600 {
font-weight: 600 !important;
}
.pv20 {
padding-top: 14px !important;
padding-bottom: 14px !important;
}
.br-b {
border-bottom: 1px solid #eeeff1 !important;
}
.br-lighter {
border-color: #EEE !important;
}
.bg-light {
background-color: #fafafa;
color: #666;
}
.bg-light.light {
background-color: #FEFEFE;
}
.bg-dark {
background-color: #2a3342 !important;
color: #8697b2;
}
.text-white {
color: #fff !important;
}
.bg-dark.light {
background-color: #364155 !important;
}
.bg-dark.dark {
background-color: #1e252f !important;
}
.bg-primary {
background-color: #11a8bb !important;
color: #a2edf6;
}
.bg-primary.light {
background-color: #14c1d7 !important;
}
.bg-primary.dark {
background-color: #0e8f9f !important;
}
.bg-success {
background-color: #47D178 !important;
color: #eafaf0;
}
.bg-success.light {
background-color: #5fd78a !important;
}
.bg-success.dark {
background-color: #32c867 !important;
}
.bg-info {
background-color: #47d1af !important;
color: #eafaf6;
}
.bg-info.light {
background-color: #5fd7ba !important;
}
.bg-info.dark {
background-color: #32c8a3 !important;
}
.bg-warning {
background-color: #ff7444 !important;
color: #ffffff;
}
.bg-warning.light {
background-color: #ff8b63 !important;
}
.bg-warning.dark {
background-color: #ff5d25 !important;
}
.bg-danger {
background-color: #ee5744 !important;
color: #ffffff;
}
.bg-danger.light {
background-color: #f17060 !important;
}
.bg-danger.dark {
background-color: #eb3e28 !important;
}
.bg-alert {
background-color: #fdba4b !important;
color: #ffffff;
}
.bg-alert.light {
background-color: #fdc669 !important;
}
.bg-alert.dark {
background-color: #fdae2d !important;
}
.bg-system {
background-color: #6852b2 !important;
color: #e3dff1;
}
.bg-system.light {
background-color: #7a67bb !important;
}
.bg-system.dark {
background-color: #5b479f !important;
}
/*-- color-variations --*/
.button-states-top-grid{
position: relative;
margin-bottom: 0;
background-color: #fff;
padding: 28px 32px;
border-radius: 0;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.button-sizes{
position: relative;
margin-bottom: 0;
background-color: #fff;
padding: 28px 32px;
border-radius: 0;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
#content .panel {
-webkit-box-shadow: 0 2px 0 #e5eaee;
box-shadow: 0 2px 0 #e5eaee;
padding: 28px 32px;
border-radius: 5px;
}
.mtn {
margin-top: 0 !important;
}
.panel-heading + .panel-body {
border-top: 0;
}
#content .panel .panel-body {
border: 0;
margin-top: 30px;
}
#content .panel .panel-heading + .panel-body {
margin-top: 0px;
}
.mb15 {
margin-bottom: 15px !important;
}
.mb20 {
margin-bottom: 20px !important;
}
.bs-component {
position: relative;
}
.btn {
display: inline-block;
margin-bottom: 0;
text-align: center;
vertical-align: middle;
text-transform: uppercase;
cursor: pointer;
background-image: none;
border: 0;
border-color: rgba(0, 0, 0, 0.07) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.18);
white-space: nowrap;
padding: 8px 15px;
border-radius: 3px;
letter-spacing: 0.02em;
-webkit-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
}
.btn-block {
display: block;
width: 100%;
}
.btn-dark {
color: #fff;
background-color: #2a3342;
}
.btn.disabled, .btn[disabled], fieldset[disabled] .btn {
cursor: not-allowed;
pointer-events: none;
opacity: 0.65;
filter: alpha(opacity=65);
-webkit-box-shadow: none;
box-shadow: none;
}
.btn-dark.disabled, .btn-dark[disabled], fieldset[disabled] .btn-dark, .btn-dark.disabled:hover, .btn-dark[disabled]:hover, fieldset[disabled] .btn-dark:hover, .btn-dark.disabled:focus, .btn-dark[disabled]:focus, fieldset[disabled] .btn-dark:focus, .btn-dark.disabled:active, .btn-dark[disabled]:active, fieldset[disabled] .btn-dark:active, .btn-dark.disabled.active, .btn-dark[disabled].active, fieldset[disabled] .btn-dark.active {
background-color: #2a3342;
border-color: #2a3342;
}
.btn-system {
color: #fff;
background-color: #6852b2;
}
.btn-system:hover, .btn-system:focus, .btn-system:active, .btn-system.active, .open > .dropdown-toggle.btn-system {
color: #fff;
background-color: #2a3342;
border-color: rgba(0, 0, 0, 0.05);
}
#source-button {
position: absolute;
top: 0;
right: 0;
z-index: 100;
font-weight: 600;
}
.btn-dark.btn-dark:hover, .btn-dark.btn-dark:focus, .btn-dark.btn-dark:active, .btn-dark.btn-dark.active {
background-color: #629aa9;
}
.btn-dark:hover, .btn-dark:focus, .btn-dark:active, .btn-dark.active, .open > .dropdown-toggle.btn-dark {
color: #fff;
background-color: #2a3342;
border-color: rgba(0, 0, 0, 0.05);
}
button.btn.btn-default {
background: #629aa9;
color: #fff;
outline: none;
border: none;
}
.button_set_one.three.one .btn {
border-radius: 20px;
-webkit-border-radius: 20px;
-o-border-radius: 20px;
-ms-border-radius: 20px;
}
.btn-pri {
position: relative;
padding-left: 40px;
}
.button_set_one.three .btn {
margin: 0 2px;
color: #fff;
padding: 9px 20px 9px 35px;
}
.btn-pri i {
background: rgba(0, 0, 0, 0.1);
left: -1px;
margin-right: 0px;
padding: 13px 10px;
position: absolute;
top: -1px;
}
.button_set_one .btn {
display: inline-block;
padding: 9px 9px;
margin-bottom: 0;
font-size: 14px;
font-weight: normal;
line-height: 1.42857143;
text-align: center;
white-space: nowrap;
vertical-align: middle;
-ms-touch-action: manipulation;
touch-action: manipulation;
cursor: pointer;
-webkit-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
background-image: none;
border: 1px solid transparent;
border-radius: 0px;
}
.button_set_one.two .btn {
margin: 0 0.5em;
border-radius: 4px;
-webkit-border-radius: 4px;
-o-border-radius: 4px;
-ms-border-radius: 4px;
}
.btn-danger:hover, .btn-danger:focus, .btn-danger.focus, .btn-danger:active, .btn-danger.active, .open > .dropdown-toggle.btn-danger {
color: #fff;
background-color: #2d2d2d;
border-color: #2d2d2d;
}
.btn-xs, .btn-group-xs > .btn {
padding: 3px 8px;
font-size: 11px;
line-height: 1.5;
border-radius: 3px;
}
.btn-sm, .btn-group-sm > .btn {
padding: 5px 14px;
font-size: 11px;
line-height: 1.5;
border-radius: 3px;
}
.btn-group-lg > .btn, .btn-lg, .btn-group-lg > .btn {
font-size: 1em;
}
.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary {
color: #fff;
background-color: #2a3342;
border-color: rgba(0, 0, 0, 0.05);
}
.w3layouts-map {
margin-bottom: 1em;
}
.agileits-map{
margin-bottom: 1em;
}
/*-- icon-hover-effects --*/
a.button,a.button2{
/*display: inline-block;*/
/*vertical-align: middle;*/
padding: 1em;
cursor: pointer;
background:none;
text-decoration: none;
font-size: 1.2em;
color: #666;
/* Prevent highlight colour when element is tapped */
-webkit-tap-highlight-color: rgba(0,0,0,0);
}
.hover-buttons {
margin-bottom: 0;
background-color: #fff;
padding: 28px 32px;
border-radius: 0;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.aligncenter {
text-align: center;
}
.agile-buttons-grids {
margin-bottom: 2em;
}
ul.bt-list li {
display: inline-block;
list-style: none;
width: 32%;
margin: 2% 0;
}
ul.bt-list li a {
padding: .6em 2em;
color: #fff;
width: 90%;
}
.col-1 {
background-color: #399834;
}
.col-2 {
background-color: #c65186;
}
.col-3 {
background-color: #2f72c3;
}
.col-4 {
background-color: #768b82;
}
.col-5 {
background-color: #e84c3d;
}
.col-6 {
background-color: #b147cb;
}
.col-7 {
background-color: #1bbc9b;
}
.col-24 {
background-color: #4c4c4c;
}
.col-8 {
background-color: #739b9d;
}
.col-9 {
background-color: #3598db;
}
.col-10 {
background-color: #27ae61;
}
.col-11 {
background-color: #f98b02;
}
.col-12 {
background-color: #a1a8ae;
}
.col-13 {
background-color: #eca900;
}
.col-14 {
background-color: #9b58b5;
}
.col-15 {
background-color: #44ccf6;
}
.col-16 {
background-color: #7f7f7f;
}
.col-17 {
background-color: #2ecd71;
}
.col-18 {
background-color: #e63f51;
}
.col-19 {
background-color: #b9bf15;
}
.col-20 {
background-color: #399834;
}
.col-21 {
background-color: #c65186;
}
.col-22{
background-color: #2f72c3;
}
.col-23{
background-color: #758f84;
}
/* ICONS */
/* Icon Back */
.hvr-icon-back {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-left: 2.2em;
-webkit-transition-duration: 0.1s;
transition-duration: 0.1s;
}
.hvr-icon-back:before {
content: "\f137";
position: absolute;
left: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.1s;
transition-duration: 0.1s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-back:hover:before, .hvr-icon-back:focus:before, .hvr-icon-back:active:before {
-webkit-transform: translateX(-4px);
transform: translateX(-4px);
}
/* Icon Forward */
.hvr-icon-forward {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.1s;
transition-duration: 0.1s;
}
.hvr-icon-forward:before {
content: "\f138";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.1s;
transition-duration: 0.1s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-forward:hover:before, .hvr-icon-forward:focus:before, .hvr-icon-forward:active:before {
-webkit-transform: translateX(4px);
transform: translateX(4px);
}
/* Icon Down */
@-webkit-keyframes hvr-icon-down {
0%,
50%,
100% {
-webkit-transform: translateY(0);
transform: translateY(0);
}
25%,
75% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
}
@keyframes hvr-icon-down {
0%,
50%,
100% {
-webkit-transform: translateY(0);
transform: translateY(0);
}
25%,
75% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
}
/* Icon Down */
.hvr-icon-down {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-down:before {
content: "\f01a";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-down:hover:before, .hvr-icon-down:focus:before, .hvr-icon-down:active:before {
-webkit-animation-name: hvr-icon-down;
animation-name: hvr-icon-down;
-webkit-animation-duration: 0.75s;
animation-duration: 0.75s;
-webkit-animation-timing-function: ease-out;
animation-timing-function: ease-out;
}
/* Icon Up */
@-webkit-keyframes hvr-icon-up {
0%,
50%,
100% {
-webkit-transform: translateY(0);
transform: translateY(0);
}
25%,
75% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
}
@keyframes hvr-icon-up {
0%,
50%,
100% {
-webkit-transform: translateY(0);
transform: translateY(0);
}
25%,
75% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
}
/* Icon Up */
.hvr-icon-up {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-up:before {
content: "\f01b";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-up:hover:before, .hvr-icon-up:focus:before, .hvr-icon-up:active:before {
-webkit-animation-name: hvr-icon-up;
animation-name: hvr-icon-up;
-webkit-animation-duration: 0.75s;
animation-duration: 0.75s;
-webkit-animation-timing-function: ease-out;
animation-timing-function: ease-out;
}
/* Icon Spin */
.hvr-icon-spin {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-spin:before {
content: "\f021";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transition-duration: 1s;
transition-duration: 1s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-in-out;
transition-timing-function: ease-in-out;
}
.hvr-icon-spin:hover:before, .hvr-icon-spin:focus:before, .hvr-icon-spin:active:before {
-webkit-transform: rotate(360deg);
transform: rotate(360deg);
}
/* Icon Drop */
@-webkit-keyframes hvr-icon-drop {
0% {
opacity: 0;
}
50% {
opacity: 0;
-webkit-transform: translateY(-100%);
transform: translateY(-100%);
}
51%,
100% {
opacity: 1;
}
}
@keyframes hvr-icon-drop {
0% {
opacity: 0;
}
50% {
opacity: 0;
-webkit-transform: translateY(-100%);
transform: translateY(-100%);
}
51%,
100% {
opacity: 1;
}
}
/* Icon Drop */
.hvr-icon-drop {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-drop:before {
content: "\f041";
position: absolute;
right: 1em;
opacity: 1;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-drop:hover:before, .hvr-icon-drop:focus:before, .hvr-icon-drop:active:before {
opacity: 0;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-animation-name: hvr-icon-drop;
animation-name: hvr-icon-drop;
-webkit-animation-duration: 0.5s;
animation-duration: 0.5s;
-webkit-animation-delay: 0.3s;
animation-delay: 0.3s;
-webkit-animation-fill-mode: forwards;
animation-fill-mode: forwards;
-webkit-animation-timing-function: ease-in-out;
animation-timing-function: ease-in-out;
-webkit-animation-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);
animation-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);
}
/* Icon Fade */
.hvr-icon-fade {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-fade:before {
content: "\f00c";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.5s;
transition-duration: 0.5s;
-webkit-transition-property: color;
transition-property: color;
}
.hvr-icon-fade:hover:before, .hvr-icon-fade:focus:before, .hvr-icon-fade:active:before {
color: #0F9E5E;
}
/* Icon Float Away */
@-webkit-keyframes hvr-icon-float-away {
0% {
opacity: 1;
}
100% {
opacity: 0;
-webkit-transform: translateY(-1em);
transform: translateY(-1em);
}
}
@keyframes hvr-icon-float-away {
0% {
opacity: 1;
}
100% {
opacity: 0;
-webkit-transform: translateY(-1em);
transform: translateY(-1em);
}
}
/* Icon Float Away */
.hvr-icon-float-away {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-float-away:before, .hvr-icon-float-away:after {
content: "\f055";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
}
.hvr-icon-float-away:after {
opacity: 0;
-webkit-animation-duration: 0.5s;
animation-duration: 0.5s;
-webkit-animation-fill-mode: forwards;
animation-fill-mode: forwards;
}
.hvr-icon-float-away:hover:after, .hvr-icon-float-away:focus:after, .hvr-icon-float-away:active:after {
-webkit-animation-name: hvr-icon-float-away;
animation-name: hvr-icon-float-away;
-webkit-animation-timing-function: ease-out;
animation-timing-function: ease-out;
}
/* Icon Sink Away */
@-webkit-keyframes hvr-icon-sink-away {
0% {
opacity: 1;
}
100% {
opacity: 0;
-webkit-transform: translateY(1em);
transform: translateY(1em);
}
}
@keyframes hvr-icon-sink-away {
0% {
opacity: 1;
}
100% {
opacity: 0;
-webkit-transform: translateY(1em);
transform: translateY(1em);
}
}
/* Icon Sink Away */
.hvr-icon-sink-away {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-sink-away:before, .hvr-icon-sink-away:after {
content: "\f056";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-sink-away:after {
opacity: 0;
-webkit-animation-duration: 0.5s;
animation-duration: 0.5s;
-webkit-animation-fill-mode: forwards;
animation-fill-mode: forwards;
}
.hvr-icon-sink-away:hover:after, .hvr-icon-sink-away:focus:after, .hvr-icon-sink-away:active:after {
-webkit-animation-name: hvr-icon-sink-away;
animation-name: hvr-icon-sink-away;
-webkit-animation-timing-function: ease-out;
animation-timing-function: ease-out;
}
/* Icon Grow */
.hvr-icon-grow {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-grow:before {
content: "\f118";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-grow:hover:before, .hvr-icon-grow:focus:before, .hvr-icon-grow:active:before {
-webkit-transform: scale(1.3) translateZ(0);
transform: scale(1.3) translateZ(0);
}
/* Icon Shrink */
.hvr-icon-shrink {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-shrink:before {
content: "\f119";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-shrink:hover:before, .hvr-icon-shrink:focus:before, .hvr-icon-shrink:active:before {
-webkit-transform: scale(0.8);
transform: scale(0.8);
}
/* Icon Pulse */
@-webkit-keyframes hvr-icon-pulse {
25% {
-webkit-transform: scale(1.3);
transform: scale(1.3);
}
75% {
-webkit-transform: scale(0.8);
transform: scale(0.8);
}
}
@keyframes hvr-icon-pulse {
25% {
-webkit-transform: scale(1.3);
transform: scale(1.3);
}
75% {
-webkit-transform: scale(0.8);
transform: scale(0.8);
}
}
.hvr-icon-pulse {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-pulse:before {
content: "\f015";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-pulse:hover:before, .hvr-icon-pulse:focus:before, .hvr-icon-pulse:active:before {
-webkit-animation-name: hvr-icon-pulse;
animation-name: hvr-icon-pulse;
-webkit-animation-duration: 1s;
animation-duration: 1s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: infinite;
animation-iteration-count: infinite;
}
/* Icon Pulse Grow */
@-webkit-keyframes hvr-icon-pulse-grow {
to {
-webkit-transform: scale(1.3);
transform: scale(1.3);
}
}
@keyframes hvr-icon-pulse-grow {
to {
-webkit-transform: scale(1.3);
transform: scale(1.3);
}
}
.hvr-icon-pulse-grow {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-pulse-grow:before {
content: "\f015";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-pulse-grow:hover:before, .hvr-icon-pulse-grow:focus:before, .hvr-icon-pulse-grow:active:before {
-webkit-animation-name: hvr-icon-pulse-grow;
animation-name: hvr-icon-pulse-grow;
-webkit-animation-duration: 0.3s;
animation-duration: 0.3s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: infinite;
animation-iteration-count: infinite;
-webkit-animation-direction: alternate;
animation-direction: alternate;
}
/* Icon Pulse Shrink */
@-webkit-keyframes hvr-icon-pulse-shrink {
to {
-webkit-transform: scale(0.8);
transform: scale(0.8);
}
}
@keyframes hvr-icon-pulse-shrink {
to {
-webkit-transform: scale(0.8);
transform: scale(0.8);
}
}
.hvr-icon-pulse-shrink {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
}
.hvr-icon-pulse-shrink:before {
content: "\f015";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-pulse-shrink:hover:before, .hvr-icon-pulse-shrink:focus:before, .hvr-icon-pulse-shrink:active:before {
-webkit-animation-name: hvr-icon-pulse-shrink;
animation-name: hvr-icon-pulse-shrink;
-webkit-animation-duration: 0.3s;
animation-duration: 0.3s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: infinite;
animation-iteration-count: infinite;
-webkit-animation-direction: alternate;
animation-direction: alternate;
}
/* Icon Push */
@-webkit-keyframes hvr-icon-push {
50% {
-webkit-transform: scale(0.5);
transform: scale(0.5);
}
}
@keyframes hvr-icon-push {
50% {
-webkit-transform: scale(0.5);
transform: scale(0.5);
}
}
.hvr-icon-push {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-push:before {
content: "\f006";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-push:hover:before, .hvr-icon-push:focus:before, .hvr-icon-push:active:before {
-webkit-animation-name: hvr-icon-push;
animation-name: hvr-icon-push;
-webkit-animation-duration: 0.3s;
animation-duration: 0.3s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: 1;
animation-iteration-count: 1;
}
/* Icon Pop */
@-webkit-keyframes hvr-icon-pop {
50% {
-webkit-transform: scale(1.5);
transform: scale(1.5);
}
}
@keyframes hvr-icon-pop {
50% {
-webkit-transform: scale(1.5);
transform: scale(1.5);
}
}
.hvr-icon-pop {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-pop:before {
content: "\f005";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-pop:hover:before, .hvr-icon-pop:focus:before, .hvr-icon-pop:active:before {
-webkit-animation-name: hvr-icon-pop;
animation-name: hvr-icon-pop;
-webkit-animation-duration: 0.3s;
animation-duration: 0.3s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: 1;
animation-iteration-count: 1;
}
/* Icon Bounce */
.hvr-icon-bounce {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-bounce:before {
content: "\f087";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-bounce:hover:before, .hvr-icon-bounce:focus:before, .hvr-icon-bounce:active:before {
-webkit-transform: scale(1.5);
transform: scale(1.5);
-webkit-transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
transition-timing-function: cubic-bezier(0.47, 2.02, 0.31, -0.36);
}
/* Icon Rotate */
.hvr-icon-rotate {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-rotate:before {
content: "\f0c6";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-rotate:hover:before, .hvr-icon-rotate:focus:before, .hvr-icon-rotate:active:before {
-webkit-transform: rotate(20deg);
transform: rotate(20deg);
}
/* Icon Grow Rotate */
.hvr-icon-grow-rotate {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-grow-rotate:before {
content: "\f095";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-grow-rotate:hover:before, .hvr-icon-grow-rotate:focus:before, .hvr-icon-grow-rotate:active:before {
-webkit-transform: scale(1.5) rotate(12deg);
transform: scale(1.5) rotate(12deg);
}
/* Icon Float */
.hvr-icon-float {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-float:before {
content: "\f01b";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-float:hover:before, .hvr-icon-float:focus:before, .hvr-icon-float:active:before {
-webkit-transform: translateY(-4px);
transform: translateY(-4px);
}
/* Icon Sink */
.hvr-icon-sink {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-sink:before {
content: "\f01a";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
-webkit-transition-property: transform;
transition-property: transform;
-webkit-transition-timing-function: ease-out;
transition-timing-function: ease-out;
}
.hvr-icon-sink:hover:before, .hvr-icon-sink:focus:before, .hvr-icon-sink:active:before {
-webkit-transform: translateY(4px);
transform: translateY(4px);
}
/* Icon Bob */
@-webkit-keyframes hvr-icon-bob {
0% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
50% {
-webkit-transform: translateY(-2px);
transform: translateY(-2px);
}
100% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
}
@keyframes hvr-icon-bob {
0% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
50% {
-webkit-transform: translateY(-2px);
transform: translateY(-2px);
}
100% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
}
@-webkit-keyframes hvr-icon-bob-float {
100% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
}
@keyframes hvr-icon-bob-float {
100% {
-webkit-transform: translateY(-6px);
transform: translateY(-6px);
}
}
.hvr-icon-bob {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-bob:before {
content: "\f077";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-bob:hover:before, .hvr-icon-bob:focus:before, .hvr-icon-bob:active:before {
-webkit-animation-name: hvr-icon-bob-float, hvr-icon-bob;
animation-name: hvr-icon-bob-float, hvr-icon-bob;
-webkit-animation-duration: .3s, 1.5s;
animation-duration: .3s, 1.5s;
-webkit-animation-delay: 0s, .3s;
animation-delay: 0s, .3s;
-webkit-animation-timing-function: ease-out, ease-in-out;
animation-timing-function: ease-out, ease-in-out;
-webkit-animation-iteration-count: 1, infinite;
animation-iteration-count: 1, infinite;
-webkit-animation-fill-mode: forwards;
animation-fill-mode: forwards;
-webkit-animation-direction: normal, alternate;
animation-direction: normal, alternate;
}
/* Icon Hang */
@-webkit-keyframes hvr-icon-hang {
0% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
50% {
-webkit-transform: translateY(2px);
transform: translateY(2px);
}
100% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
}
@keyframes hvr-icon-hang {
0% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
50% {
-webkit-transform: translateY(2px);
transform: translateY(2px);
}
100% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
}
@-webkit-keyframes hvr-icon-hang-sink {
100% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
}
@keyframes hvr-icon-hang-sink {
100% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
}
.hvr-icon-hang {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-hang:before {
content: "\f078";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-hang:hover:before, .hvr-icon-hang:focus:before, .hvr-icon-hang:active:before {
-webkit-animation-name: hvr-icon-hang-sink, hvr-icon-hang;
animation-name: hvr-icon-hang-sink, hvr-icon-hang;
-webkit-animation-duration: .3s, 1.5s;
animation-duration: .3s, 1.5s;
-webkit-animation-delay: 0s, .3s;
animation-delay: 0s, .3s;
-webkit-animation-timing-function: ease-out, ease-in-out;
animation-timing-function: ease-out, ease-in-out;
-webkit-animation-iteration-count: 1, infinite;
animation-iteration-count: 1, infinite;
-webkit-animation-fill-mode: forwards;
animation-fill-mode: forwards;
-webkit-animation-direction: normal, alternate;
animation-direction: normal, alternate;
}
/* Icon Wobble Horizontal */
@-webkit-keyframes hvr-icon-wobble-horizontal {
16.65% {
-webkit-transform: translateX(6px);
transform: translateX(6px);
}
33.3% {
-webkit-transform: translateX(-5px);
transform: translateX(-5px);
}
49.95% {
-webkit-transform: translateX(4px);
transform: translateX(4px);
}
66.6% {
-webkit-transform: translateX(-2px);
transform: translateX(-2px);
}
83.25% {
-webkit-transform: translateX(1px);
transform: translateX(1px);
}
100% {
-webkit-transform: translateX(0);
transform: translateX(0);
}
}
@keyframes hvr-icon-wobble-horizontal {
16.65% {
-webkit-transform: translateX(6px);
transform: translateX(6px);
}
33.3% {
-webkit-transform: translateX(-5px);
transform: translateX(-5px);
}
49.95% {
-webkit-transform: translateX(4px);
transform: translateX(4px);
}
66.6% {
-webkit-transform: translateX(-2px);
transform: translateX(-2px);
}
83.25% {
-webkit-transform: translateX(1px);
transform: translateX(1px);
}
100% {
-webkit-transform: translateX(0);
transform: translateX(0);
}
}
.hvr-icon-wobble-horizontal {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-wobble-horizontal:before {
content: "\f061";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-wobble-horizontal:hover:before, .hvr-icon-wobble-horizontal:focus:before, .hvr-icon-wobble-horizontal:active:before {
-webkit-animation-name: hvr-icon-wobble-horizontal;
animation-name: hvr-icon-wobble-horizontal;
-webkit-animation-duration: 1s;
animation-duration: 1s;
-webkit-animation-timing-function: ease-in-out;
animation-timing-function: ease-in-out;
-webkit-animation-iteration-count: 1;
animation-iteration-count: 1;
}
/* Icon Wobble Vertical */
@-webkit-keyframes hvr-icon-wobble-vertical {
16.65% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
33.3% {
-webkit-transform: translateY(-5px);
transform: translateY(-5px);
}
49.95% {
-webkit-transform: translateY(4px);
transform: translateY(4px);
}
66.6% {
-webkit-transform: translateY(-2px);
transform: translateY(-2px);
}
83.25% {
-webkit-transform: translateY(1px);
transform: translateY(1px);
}
100% {
-webkit-transform: translateY(0);
transform: translateY(0);
}
}
@keyframes hvr-icon-wobble-vertical {
16.65% {
-webkit-transform: translateY(6px);
transform: translateY(6px);
}
33.3% {
-webkit-transform: translateY(-5px);
transform: translateY(-5px);
}
49.95% {
-webkit-transform: translateY(4px);
transform: translateY(4px);
}
66.6% {
-webkit-transform: translateY(-2px);
transform: translateY(-2px);
}
83.25% {
-webkit-transform: translateY(1px);
transform: translateY(1px);
}
100% {
-webkit-transform: translateY(0);
transform: translateY(0);
}
}
.hvr-icon-wobble-vertical {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-wobble-vertical:before {
content: "\f062";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-wobble-vertical:hover:before, .hvr-icon-wobble-vertical:focus:before, .hvr-icon-wobble-vertical:active:before {
-webkit-animation-name: hvr-icon-wobble-vertical;
animation-name: hvr-icon-wobble-vertical;
-webkit-animation-duration: 1s;
animation-duration: 1s;
-webkit-animation-timing-function: ease-in-out;
animation-timing-function: ease-in-out;
-webkit-animation-iteration-count: 1;
animation-iteration-count: 1;
}
/* Icon Buzz */
@-webkit-keyframes hvr-icon-buzz {
50% {
-webkit-transform: translateX(3px) rotate(2deg);
transform: translateX(3px) rotate(2deg);
}
100% {
-webkit-transform: translateX(-3px) rotate(-2deg);
transform: translateX(-3px) rotate(-2deg);
}
}
@keyframes hvr-icon-buzz {
50% {
-webkit-transform: translateX(3px) rotate(2deg);
transform: translateX(3px) rotate(2deg);
}
100% {
-webkit-transform: translateX(-3px) rotate(-2deg);
transform: translateX(-3px) rotate(-2deg);
}
}
.hvr-icon-buzz {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-buzz:before {
content: "\f017";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-buzz:hover:before, .hvr-icon-buzz:focus:before, .hvr-icon-buzz:active:before {
-webkit-animation-name: hvr-icon-buzz;
animation-name: hvr-icon-buzz;
-webkit-animation-duration: 0.15s;
animation-duration: 0.15s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: infinite;
animation-iteration-count: infinite;
}
/* Icon Buzz Out */
@-webkit-keyframes hvr-icon-buzz-out {
10% {
-webkit-transform: translateX(3px) rotate(2deg);
transform: translateX(3px) rotate(2deg);
}
20% {
-webkit-transform: translateX(-3px) rotate(-2deg);
transform: translateX(-3px) rotate(-2deg);
}
30% {
-webkit-transform: translateX(3px) rotate(2deg);
transform: translateX(3px) rotate(2deg);
}
40% {
-webkit-transform: translateX(-3px) rotate(-2deg);
transform: translateX(-3px) rotate(-2deg);
}
50% {
-webkit-transform: translateX(2px) rotate(1deg);
transform: translateX(2px) rotate(1deg);
}
60% {
-webkit-transform: translateX(-2px) rotate(-1deg);
transform: translateX(-2px) rotate(-1deg);
}
70% {
-webkit-transform: translateX(2px) rotate(1deg);
transform: translateX(2px) rotate(1deg);
}
80% {
-webkit-transform: translateX(-2px) rotate(-1deg);
transform: translateX(-2px) rotate(-1deg);
}
90% {
-webkit-transform: translateX(1px) rotate(0);
transform: translateX(1px) rotate(0);
}
100% {
-webkit-transform: translateX(-1px) rotate(0);
transform: translateX(-1px) rotate(0);
}
}
@keyframes hvr-icon-buzz-out {
10% {
-webkit-transform: translateX(3px) rotate(2deg);
transform: translateX(3px) rotate(2deg);
}
20% {
-webkit-transform: translateX(-3px) rotate(-2deg);
transform: translateX(-3px) rotate(-2deg);
}
30% {
-webkit-transform: translateX(3px) rotate(2deg);
transform: translateX(3px) rotate(2deg);
}
40% {
-webkit-transform: translateX(-3px) rotate(-2deg);
transform: translateX(-3px) rotate(-2deg);
}
50% {
-webkit-transform: translateX(2px) rotate(1deg);
transform: translateX(2px) rotate(1deg);
}
60% {
-webkit-transform: translateX(-2px) rotate(-1deg);
transform: translateX(-2px) rotate(-1deg);
}
70% {
-webkit-transform: translateX(2px) rotate(1deg);
transform: translateX(2px) rotate(1deg);
}
80% {
-webkit-transform: translateX(-2px) rotate(-1deg);
transform: translateX(-2px) rotate(-1deg);
}
90% {
-webkit-transform: translateX(1px) rotate(0);
transform: translateX(1px) rotate(0);
}
100% {
-webkit-transform: translateX(-1px) rotate(0);
transform: translateX(-1px) rotate(0);
}
}
.hvr-icon-buzz-out {
display: inline-block;
vertical-align: middle;
-webkit-transform: translateZ(0);
transform: translateZ(0);
box-shadow: 0 0 1px rgba(0, 0, 0, 0);
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
-moz-osx-font-smoothing: grayscale;
position: relative;
padding-right: 2.2em;
-webkit-transition-duration: 0.3s;
transition-duration: 0.3s;
}
.hvr-icon-buzz-out:before {
content: "\f023";
position: absolute;
right: 1em;
padding: 0 1px;
font-family: FontAwesome;
-webkit-transform: translateZ(0);
transform: translateZ(0);
}
.hvr-icon-buzz-out:hover:before, .hvr-icon-buzz-out:focus:before, .hvr-icon-buzz-out:active:before {
-webkit-animation-name: hvr-icon-buzz-out;
animation-name: hvr-icon-buzz-out;
-webkit-animation-duration: 0.75s;
animation-duration: 0.75s;
-webkit-animation-timing-function: linear;
animation-timing-function: linear;
-webkit-animation-iteration-count: 1;
animation-iteration-count: 1;
}
/*-- //icon-hover-effects --*/
/*-- //buttons --*/
/*--- index page ---*/
.content-top-1 {
background-color: #fff;
padding: 1em;
margin-bottom: 1em;
border: 1px solid #ebeff6;
border-radius: 0px;
-webkit-border-radius: 0px;
-o-border-radius: 0px;
-moz-border-radius: 0px;
-ms-border-radius: 0px;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.top-content h5 {
font-size: 1.1em;
margin-top: 22px;
color: #777;
}
.top-content label {
font-size: 1.7em;
color: #333;
}
.pie-title-center {
display: inline-block;
position: relative;
text-align: center;
}
.pie-value {
display: block;
position: absolute;
font-size: 14px;
height: 40px;
top: 50%;
left: 0;
right: 0;
margin-top: -20px;
line-height: 40px;
}
/*--- //index page ---*/
/*--- mainpage-chit ---*/
.chit-chat-heading {
font-size: 19px;
color: #000;
text-transform: capitalize;
	margin-bottom:10px;
}
h3#geoChartTitle {
font-size: 19px;
color: #000;
}
.work-progres {
padding: 2.1em 1em;
background: #fff;
	-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.geo-chart {
padding: 1.98em 1em;
background: #fff;
	-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
	box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
div#geoChart {
width: 100% !important;
height: 355px!important;
border: 4px solid #fff;
}
.chit-chat-layer1-left {
padding-left: 0;
}
.chit-chat-layer1-rit {
padding-right: 0;
}
/*--- //mainpage-chit ---*/
/*-- /weather --*/
.weather_w3_inner_info ul li {
display: inline-block;
text-align: center;
width: 13%;
margin-top: 2em;
}
.weather_w3_inner_info ul li:nth-child(1) figure.icons {
float: none;
width: 100%;
}
.weather_w3_inner_info h3 {
font-size: 22px;
color: #fff;
}
figure.icons,.weather-text {
float: left;
width: 50%;
}
.weather_w3_inner_info h4 {
font-size: 1.2em;
color: #fff;
font-weight: 600;
letter-spacing: 1px;
}
.weather_w3_inner_info h5 {
font-size:1em;
color: #fff;
margin-top: 0.5em;
	letter-spacing: 1px;
}
.weather_w3_inner_info{
background: url(../images/weather.jpg)no-repeat center;
background-size: cover;
-webkit-background-size: cover;
-moz-background-size: cover;
-o-background-size: cover;
-ms-background-size: cover;
min-height: 300px;
}
.social_media_w3ls {
margin-top: 2em;
}
.weather_w3_inner_info ul {
margin: 0 auto;
text-align: center;
}
.over_lay_agile{
padding: 3em 3em 7.5em 3em;
}
.chit-chat-layer1 {
margin-bottom: 20px;
}
/*-- //weather --*/
/*-- Calender --*/
.agil-info-calendar {
margin:20px 0px;
}
.agile-calendar {
padding-left: 0px;
}
.calendar-widget {
padding: 15px 10px;
background: #fff;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.chit-chat-heading i {
color: #00c4ff;
margin-right: 10px;
margin-left: 5px;
}
/*-- //Calender --*/
/*--statistics--*/
.stats-info ul li {
margin-bottom: 1em;
border-bottom:1px solid #e1ab91;
font-size: 1em;
color: #555;
}
.table>thead>tr>th {
border-bottom: 1px solid #e1ab91 ! important;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
border-bottom: 1px solid #e9e9e9 ! important;
}
.progress.progress-right {
width: 25%;
float: right;
height: 10px;
margin-bottom: 0;
}
.stats-info ul li.last {
border-bottom: none;
padding-bottom: 0;
margin-bottom: 0.5em;
}
.stats-info span.pull-right {
font-size: 0.9em;
margin-left: 11px;
line-height: 2em;
	margin-top:0;
}
.table.stats-table {
margin-bottom: 0;
}
.stats-table span.label{
font-weight: 500;
}
.stats-table h5 {
color: #4F52BA;
font-size: 0.9em;
}
.stats-table h5.down {
color: #D81B60;
}
.stats-table h5 i.fa {
font-size: 1.2em;
font-weight: 800;
margin-left: 3px;
}
.stats-table thead tr th {
color: #8b5c7e;
}
.stats-table td {
font-size: 0.9em;
color: #555;
padding: 11px !important;
}
/*--//statistics--*/
/* simple chart */
.SimpleChart {
position: relative;
}
.SimpleChart #tip {
/*background-color: #f0f0f0;
border: 1px solid #d0d0d0;
position: absolute;
left: -200px;
top: 30px;*/
}
.down-triangle {
/*width: 0;
height: 0;
border-top: 10px solid #d0d0d0;
border-left: 6px solid transparent;
border-right: 6px solid transparent;
position: absolute;
left: -200px;*/
}
.SimpleChart #highlighter {
position: absolute;
left: -200px;
}
.-simple-chart-holder {
float: left;
position: relative;
width: 100%;
background-color: #fff;
border: 1px solid #cecece;
/*padding: 6px;*/
}
.SimpleChart .legendsli {
list-style: none;
}
.SimpleChart .legendsli span {
float: left;
vertical-align: middle;
}
.SimpleChart .legendsli span.legendindicator {
position: relative;
top: 5px;
}
.SimpleChart .legendsli span.legendindicator .line {
width: 30px;
height: 3px;
}
.SimpleChart .legendsli span.legendindicator .circle {
width: 12px;
height: 12px;
border-radius: 20px;
position: relative;
top: -5px;
right: 20px;
}
/******Starts::Horizontal Alignment of Legends******/
.simple-chart-legends {
background: #E7E7E7;
border: 1px solid #d6d7dd;
padding: 5px;
margin: 2px 0px;
}
.simple-chart-legends ul {
}
.simple-chart-legends ul li {
display: inline;
border-right: 1px solid #d6d7dd;
float: left;
padding: 10px;
}
.simple-chart-legends ul li:last-child {
border-right: 0px;
}
.simple-chart-legends.vertical {
margin: 0px 10px;
}
.simple-chart-legends.vertical ul li {
display: block;
border: 0px;
border-bottom: 1px solid #d6d7dd;
}
.simple-chart-legends.vertical ul li:last-child {
border-bottom: 0px;
}
.simple-chart-legends .legendvalue {
padding-left: 2px;
background: #fff;
}
/******Starts::Horizontal Alignment of Legends******/
.simple-chart-Header {
position: absolute;
font-size: 16px;
}
/* //simple chart */
/*--Progress bars--*/
.progress {
height: 10px;
margin: 7px 0;
overflow: hidden;
background: #e1e1e1;
z-index: 1;
cursor: pointer;
}
.task-info .percentage{
	float:right;
	height:inherit;
	line-height:inherit;
}
.task-desc{
	font-size:12px;
}
.wrapper-dropdown-3 .dropdown li a:hover span.task-desc {
	color:#65cea7;
}
.progress .bar {
		z-index: 2;
		height:15px;
		font-size: 12px;
		color: white;
		text-align: center;
		float:left;
		-webkit-box-sizing: content-box;
		-moz-box-sizing: content-box;
		-ms-box-sizing: content-box;
		box-sizing: content-box;
		-webkit-transition: width 0.6s ease;
		-moz-transition: width 0.6s ease;
		-o-transition: width 0.6s ease;
		transition: width 0.6s ease;
	}
.progress-striped .yellow{
	background:#f0ad4e;
}
.progress-striped .green{
	background:#5cb85c;
}
.progress-striped .light-blue{
	background:#4F52BA;
}
.progress-striped .red{
	background:#d9534f;
}
.progress-striped .blue{
	background:#428bca;
}
.progress-striped .orange {
	background:#e94e02;
}
.progress-striped .bar {
background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
-webkit-background-size: 40px 40px;
-moz-background-size: 40px 40px;
-o-background-size: 40px 40px;
background-size: 40px 40px;
}
.progress.active .bar {
-webkit-animation: progress-bar-stripes 2s linear infinite;
-moz-animation: progress-bar-stripes 2s linear infinite;
-ms-animation: progress-bar-stripes 2s linear infinite;
-o-animation: progress-bar-stripes 2s linear infinite;
animation: progress-bar-stripes 2s linear infinite;
}
@-webkit-keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
@-moz-keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
@-ms-keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
@-o-keyframes progress-bar-stripes {
from {
background-position: 0 0;
}
to {
background-position: 40px 0;
}
}
@keyframes progress-bar-stripes {
from {
background-position: 40px 0;
}
to {
background-position: 0 0;
}
}
.stats-info.widget {
background: #fff;
padding: 15px 30px;
}
.stats-body {
margin-top: 20px;
margin-bottom: 25px;
}
/*--Progress bars--*/
/*-- User profile --*/
.malorum-top {
background: url(../images/weather.jpg)no-repeat;
	background-size:cover;
min-height: 258px;
}
.malorm-bottom {
padding: 1.5em 2em;
position: relative;
background: #fff;
-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
span.malorum-pro {
background: url(../images/img1.png)no-repeat;
width: 100px;
height: 100px;
display: inline-block;
position: absolute;
top: -60px;
left: 38%;
border: 4px solid #fff;
border-radius: 63px;
-webkit-border-radius: 63px;
-moz-border-radius: 63px;
-o-border-radius: 63px;
}
.chart-layer1-right {
padding-right: 0px;
}
.malorm-bottom ul {
list-style: none;
padding: 0px;
text-align: center;
margin-top: 1em;
}
.malorm-bottom ul li {
display: inline-block;
margin-right: 10px;
}
.malorm-bottom i.fa.fa-facebook {
	font-size: 1em;
color: #fff;
background: #3c579e;
width: 30px;
height: 30px;
line-height: 30px;
transition: 0.5s all;
}
.malorm-bottom i.fa.fa-facebook:hover{
	border-radius: 35px;
	-webkit-border-radius:35px;
	-moz-border-radius:35px;
	-ms-border-radius:35px;
	-o-border-radius:35px;
	transition: 0.5s all;
-webkit-transition: 0.5s all;
-moz-transition: 0.5s all;
-o-transition: 0.5s all;
}
.malorm-bottom i.fa.fa-twitter{
	font-size: 1em;
color: #fff;
background:#0f98ce;
width: 30px;
height: 30px;
line-height: 30px;
transition: 0.5s all;
}
.malorm-bottom h2 {
font-size: 1.2em;
color: #337ab7;
font-weight: 600;
text-align: center;
margin-bottom: 0.5em;
	margin-top: 1.6em;
}
.malorm-bottom p {
font-size: 1em;
color: #000;
line-height: 1.8em;
text-align: center;
width: 100%;
margin: 0 auto;
}
.malorm-bottom i.fa.fa-twitter:hover{
	border-radius: 35px;
	-webkit-border-radius:35px;
	-moz-border-radius:35px;
	-ms-border-radius:35px;
	-o-border-radius:35px;
	transition: 0.5s all;
-webkit-transition: 0.5s all;
-moz-transition: 0.5s all;
-o-transition: 0.5s all;
}
.malorm-bottom i.fa.fa-google-plus{
	font-size: 1em;
color: #fff;
background: #ca2429;
width: 30px;
height: 30px;
line-height: 30px;
transition: 0.5s all;
}
.malorm-bottom i.fa.fa-google-plus:hover{
	border-radius: 35px;
	-webkit-border-radius:35px;
	-moz-border-radius:35px;
	-ms-border-radius:35px;
	-o-border-radius:35px;
	transition: 0.5s all;
-webkit-transition: 0.5s all;
-moz-transition: 0.5s all;
-o-transition: 0.5s all;
}
.malorum-icons li a{
	position: relative;
display: -webkit-box;
display: -webkit-flex;
display: -ms-flexbox;
display: flex;
-webkit-box-align: center;
-webkit-align-items: center;
-ms-flex-align: center;
align-items: center;
-webkit-box-pack: center;
-webkit-justify-content: center;
-ms-flex-pack: center;
justify-content: center;
text-decoration: none;
-webkit-transition: all .15s ease;
transition: all .15s ease;
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
}
.malorum-icons a:hover .tooltip {
display: block;
visibility: visible;
opacity: 1;
-webkit-transform: translate(0, -10px);
transform: translate(0, -10px);
}
.malorum-icons a:active {
box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.5) inset;
}
.malorum-icons .tooltip {
opacity: 0;
position: absolute;
top: -20px;
left: 50%;
-webkit-transition: all .15s ease;
transition: all .15s ease;
-webkit-backface-visibility: hidden;
backface-visibility: hidden;
}
.malorum-icons .tooltip span {
position: relative;
left: -50%;
padding: 6px 8px 5px 8px;
border-radius: 3px;
-webkit-border-radius: 3px;
-ms-border-radius: 3px;
-o-border-radius: 3px;
-moz-border-radius: 3px;
color: #fff;
font-size: 0.7em;
line-height: 1;
background: #565656;
color: #fff;
letter-spacing: 0.5px;
}
.malorum-icons .tooltip span:after {
position: absolute;
content: " ";
width: 0;
height: 0;
top: 100%;
left: 50%;
margin-left: -8px;
border: 8px solid transparent;
border-top-color: #565656;
}
.malorum-icons i {
position: relative;
top: 1px;
font-size: 1.5rem;
}
/*-- User profile --*/
/*--profile--*/
.profile{
padding: 0;
	width: 32%;
}
.profile-top {
background-color: #2c3b41;
text-align: center;
	padding: 1.5em;
}
.profile-top img {
vertical-align: middle;
border: 4px solid #F2B33F;
border-radius: 63%;
}
.profile-top h4 {
font-size: 1.1em;
color: #fff;
margin: .5em 0;
}
.profile-top h5 {
font-size: 0.9em;
color: rgba(255, 255, 255, 0.59);
}
h4.title3 {
padding: 1em;
background-color: #1e282c;
color: #fff;
}
.profile-text {
padding: 1.5em 3em;
}
.profile-row.row-middle {
margin: 1em 0;
}
.profile-left {
float: left;
width: 15%;
}
.profile-right {
float: left;
width: 85%;
}
.row-middle .profile-right {
border-top: 1px dotted #6164C1;
border-bottom: 1px dotted #6164C1;
padding: 1em 0;
}
.profile-row .profile-icon {
font-size: 1.4em;
	margin-top: 0.6em;
	color: #6F6F6F;
}
i.fa.fa-mobile.profile-icon {
font-size: 2em;
}
i.fa.fa-facebook.profile-icon {
font-size: 1.6em;
margin-top: .3em;
}
.profile-right h4 {
font-size: 1em;
color: #504E4E;
font-weight: 500;
}
.profile-right p {
font-size: .9em;
color: #999;
margin-top: .4em;
}
.profile-btm ul {
background-color: #e4e4e4;
}
.profile-btm ul li {
display: inline-block;
width: 32.5%;
text-align: center;
padding: 1.35em 0;
}
.profile-btm ul li:nth-child(2) {
border-left: 1px solid #CACACA;
border-right: 1px solid #CACACA;
}
.profile-btm ul li h4 {
font-size: 1.3em;
color: #6164C1;
font-weight: 900;
}
.profile-btm ul li h5 {
font-size: 0.9em;
color: #6F6F6F;
margin-top: 0.3em;
}
/*--//profile--*/
/*--chat--*/
.chat-mdl-grid {
margin: 0 2%;
}
.activity-img1 {
width: 64%;
padding: 0;
}
.scrollbar {
height: 462px;
background: #fff;
overflow-y: scroll;
padding:2em 1em 0;
}
.activity-row {
margin-bottom: 1em;
padding-bottom: 1.02em;
}
.activity-desc-sub, .activity-desc-sub1 {
padding: .7em;
background: #E7E7E7;
position: relative;
}
.activity-desc-sub1:after {
right: -6%;
top: 20%;
border: solid transparent;
content: " ";
height: 0;
width: 0;
position: absolute;
pointer-events: none;
border-color: rgba(213, 242, 239, 0);
border-left-color: #E7E7E7;
border-width: 8px;
margin-top: -5px;
}
.activity-desc-sub:before {
left: -8%;
top: 20%;
border: solid transparent;
content: " ";
height: 0;
width: 0;
position: absolute;
pointer-events: none;
border-color: rgba(213, 242, 239, 0);
border-right-color: #E7E7E7;
border-width: 9px;
margin-top: -5px;
}
.activity-row p {
font-size: 0.9em;
color: #555;
margin-bottom: .3em;
}
.activity-row span {
font-size: .7em;
color: #ADADAD;
}
.activity-row span.right {
text-align: right;
display: block;
}
.chat-bottom {
padding: 1em;
}
.chat-bottom input[type="text"] {
width: 100%;
border: none;
border-bottom: 1px solid #D4CFCF;
padding: 0.6em 1em;
outline: none;
transition: .5s all;
-webkit-transition: .5s all;
-moz-transition: .5s all;
box-shadow: 0px -1px 2px #CECECE;
	-webkit-box-shadow: 0px -1px 2px #CECECE;
	-moz-box-shadow: 0px -1px 2px #CECECE;
}
/*--//chat--*/
/*--todo--*/
.single-bottom ul li {
list-style: none;
padding: 0px 10px 18px;
}
.single-bottom ul li input[type="checkbox"] {
display: none;
}
.single-bottom ul li input[type="checkbox"]+label {
position: relative;
padding-left: 2em;
border: none;
outline: none;
font-size: 0.9em;
color: #999;
font-weight: 400;
	cursor: pointer;
}
.single-bottom ul li input[type="checkbox"]+label span:first-child {
width: 17px;
height: 17px;
display: inline-block;
border: 2px solid #C8C8C8;
position: absolute;
left: 0;
	top: 2px;
}
.single-bottom ul li input[type="checkbox"]:checked+label span:first-child:before {
content: "";
background: url(../images/tick.png)no-repeat;
position: absolute;
left: 1px;
top: 2px;
font-size: 10px;
width: 10px;
height: 10px;
}
/*--//todo--*/
/*--weather--*/
.weather-grids {
padding: 0;
width: 49%;
}
.header-top {
border-bottom: 3px solid #fff;
padding: 1em 1.5em;
background-color: #1e282c;
}
.header-top h2 {
float: left;
margin: .1em 0 0 .5em;
color: #FFFFFF;
font-size: 1.3em;
}
.header-top ul {
float: right;
border: 1px solid #FFFFFF;
border-radius: 5px;
}
.header-top li{
	display: inline-block;
	float: left;
}
.header-top li p{
	color:#fff;
	font-size: 1em;
	padding: 4px 6px;
}
.header-top li p.cen {
background: #FFFFFF;
color: #6164C1;
border-radius: 0 3px 3px 0;
}
.whe {
vertical-align: bottom;
margin-right: 0.5em;
}
/*----*/
.weather-grids canvas {
display: block;
margin: 0 auto;
}
.weather-grids canvas#clear-day {
width: 30px;
float: left;
}
.header-bottom1 {
float: left;
width: 25%;
}
.header-head{
	padding: 2em;
	text-align:center;
}
.header-bottom2 {
background: #f1f1f1;
}
.header-bottom1:nth-child(3) {
border-right: none;
}
.header-head h4 {
color: #337ab7;
font-size: 1.1em;
margin-bottom: 1em;
}
.header-head h6 {
color: #000;
font-size: 1.5em;
font-weight: bold;
margin: 0.5em 0;
}
.bottom-head p{
	color:#8C8B8B;
	font-size: 1em;
	line-height: 1.4em;
}
/*--//weather--*/
/*--circle-charts--*/
.weather-grids.weather-right {
margin-left: 2%;
text-align: center;
}
.weather-grids.weather-right h3 {
font-size: 1.2em;
color: #fff;
text-align: left;
}
.circle-charts {
padding: 3em 2em;
}
.weather-right ul li {
display: inline-block;
}
.weather-right ul li:nth-child(2){
margin:0 2em;
}
.weather-right ul li  p {
font-size: 1em;
color: #555;
margin-top: 1em;
}
/*--//circle-charts--*/
.widget_1_box {
width: 32%;
padding: 0;
}
.widget_1_box.widget-mdl-grid{
margin: 0 2%;
}
.widget_1_box.widget-mdl-grid2{
margin-right: 2%;
}
.tile-progress{
	padding: 2em 3em;
	text-align:center;
}
.widget_1_box .bg-info {
background-color: #6164C1;
}
.widget_1_box .bg-success {
background-color: #F2B33F;
}
.widget_1_box  .bg-danger {
background-color: rgba(233, 78, 2, 0.88);
}
.tile-progress h4 {
color: #fff;
font-size: 1.2em;
}
.tile-progress span {
color: rgba(255, 255, 255, 0.67);
font-size: 1em;
}
.widget_1_box .progress {
background: rgba(50, 50, 58, 0.5);
margin: 1em 0;
}
.widget_1_box .progress-striped .blue {
background: rgba(242, 179, 63, 0.78);
}
.widget_1_box .progress-striped .yellow {
background: #EB621F;
}
.widget_1_box .progress-striped .orange {
background: #6164C1;
}
.widget_1_box .progress-striped .bar {
background-image: -webkit-gradient(linear, 0 100%, 100% 0, color-stop(0.25, rgba(255, 255, 255, 0.15)), color-stop(0.25, transparent), color-stop(0.5, transparent), color-stop(0.5, rgba(255, 255, 255, 0.15)), color-stop(0.75, rgba(255, 255, 255, 0.15)), color-stop(0.75, transparent), to(transparent));
background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: -moz-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
-webkit-background-size: 40px 40px;
-moz-background-size: 40px 40px;
-o-background-size: 40px 40px;
background-size: 40px 40px;
}
/*--//widgets-page--*/
/*-- todo inbox chat section in index page --*/
.panel-default > .panel-heading + .panel-collapse > .panel-body {
border-top-color: #ddd;
color: #444;
}
.label-info {
background-color: #2dde98;
}
.label-primary {
background-color: #fd5c63;
}
.label-info1 {
background-color: #0099cc;
}
.label-primary1 {
background-color: #ff9933;
}
.span_7 {
padding-left: 0;
}
.span_8{
	text-align:center;
	padding: 0;
	width: 32.33%;
margin-right: 1.5%;
	-webkit-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
-moz-box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
box-shadow: 0px 0px 5px -2px rgba(0,0,0,0.75);
}
.span_8:nth-child(3){
	margin-right:0%;
}
.activity_box h2,.activity_box h3 {
text-align: left;
padding: 1em;
margin: 0;
	background: #2c3a40;
	background: #ff4f81;
color: #fff;
	text-transform:uppercase;
	font-size:1em;
}
.activity-desc1{
	padding:0;
}
.activity-row{
	border-bottom:1px solid #999;
}
.activity_box1 h3 {
background: #2c3a40;
background: #0091cd;
}
.activity_box2 h3 {
background: #2c3a40;
background: #00a78e;
}
.col_2 {
background-color: #fff;
padding: 1em;
margin-bottom: 1em;
}
.grid-1, .grid-2, .grid-3, .grid-4{
display: inline-block;
}
.grid-1 {
margin-bottom: 2em;
}
.grid-1, .grid-3{
	margin-right:10%;
	width: 27%;
}
.activity-row, .activity-row1{
text-align: left;
}
i.text-info{
float: left;
line-height: 60px;
font-size: 1.2em;
}
i.icon_13{
color:#a88add;
}
.text-info {
color: #b8c9f1;
}
.activity-img{
text-align:center;
}
.activity-img img{
display:inline-block;
}
.activity-desc h5{
	color:#00BCD4;
font-size: 1em;
font-weight: 400;
	margin-bottom: 5px;
}
.activity-desc h5 a{
	color: #629aa9;
}
.activity-desc h5 a:hover{
	color:#000;
	text-decoration:none;
}
.activity-desc p{
	color:#333;
	font-size:0.85em;
	line-height:1.5em;
}
.activity-img span{
	display: block;
font-size: 12px;
margin: .5em 0 0;
color: #999;
}
.activity-desc-sub,.activity-desc-sub1{
	padding:.7em;
	background:#eee;
	position:relative;
}
.activity-desc-sub1{
	text-align: right;
}
.activity-img1{
	padding:0 !important;
	width: 50%;
}
.activity-img2{
	padding:0 !important;
}
.activity-desc-sub:before{
	left: -8%;
top: 20%;
border: solid transparent;
content: " ";
height: 0;
width: 0;
position: absolute;
pointer-events: none;
border-color: rgba(213, 242, 239, 0);
border-right-color:#E7E7E7;
border-width: 8px;
margin-top: -5px;
}
.activity-row1{
	border-bottom:none !important;
}
.activity_box form{
	background: #eee;
padding: 1em 0em;
}
.activity_box input[type="text"]{
	outline: none;
border: 1px solid #888888;
padding: 10px;
color: #333;
font-size: 13px;
width: 70%;
background: #fff;
}
.activity_box input[type="submit"]{
	outline: none;
width: 20%;
font-size: 1em;
color: #fff;
background: #629aa9;
border: none;
padding: 8px 0;
margin-left: 0.5em;
	transition: 0.5s all;
	-webkit-transition: 0.5s all;
	-moz-transition: 0.5s all;
	-ms-transition: 0.5s all;
	-o-transition: 0.5s all;
}
.activity_box input[type="submit"]:hover{
	background: #F2B33F;
}
.activity-desc-sub1:after{
	right:-6%;
top: 20%;
border: solid transparent;
content: " ";
height: 0;
width: 0;
position: absolute;
pointer-events: none;
border-color: rgba(213, 242, 239, 0);
border-left-color:#E7E7E7;
border-width: 8px;
margin-top: -5px;
}
.activity-desc-sub h5,.activity-desc-sub1 h5{
	font-size:14px;
	color:#000;
	margin:0 0 .5em;
}
.activity-desc-sub p,.activity-desc-sub1 p{
	font-size:13px;
	color:#999;
	margin:0;
	line-height:1.8em;
}
.activity-desc1 h6{
color:#aaa;
font-size:13px;
margin: 1em 0 0 0;
}
.activity-row{
	border-bottom: 1px solid #EEE;
}
.scrollbar{
	height:363px;
	background:#fff;
	overflow-y: scroll;
padding:2em 1em 0;
}
.scrollbar1 {
height: 436px;
}
.single-bottom ul {
padding: 0;
}
.single-bottom ul li{
	list-style:none;
	padding:0px 20px 18px;
}
.single-bottom ul li input[type="checkbox"] {
display: none;
}
.single-bottom ul li input[type="checkbox"]+label {
position: relative;
padding-left: 31px;
border: none;
outline: none;
font-size:14px;
color: #444;
	cursor: pointer;
}
.single-bottom ul li input[type="checkbox"]+label span:first-child {
	width: 17px;
height: 17px;
display: inline-block;
border:2px solid #CCC;
position: absolute;
left: 0;
bottom: 4px;
}
.single-bottom ul li input[type="checkbox"]:checked+label span:first-child:before {
	content: "";
background: url(../images/tick.png)no-repeat;
position: absolute;
left: 1px;
top: 2px;
font-size: 10px;
width: 10px;
height: 10px;
}
.activity_box{
background: #fff;
min-height: 485px;
}
.icon_11{
color: #27cce4;
}
.icon_12{
color:#bdc3c7;
}
#style-1::-webkit-scrollbar-track
{
	
	background-color:#f0f0f0;
}
#style-1::-webkit-scrollbar
{
	width:3px;
	background-color: #f5f5f5;
}
#style-1::-webkit-scrollbar-thumb
{
	
	background-color: #ff4f81;
}
#style-2::-webkit-scrollbar-track
{
	
	background-color:#f0f0f0;
}
#style-2::-webkit-scrollbar
{
	width:3px;
	background-color: #f5f5f5;
}
#style-2::-webkit-scrollbar-thumb
{
	
	background-color:#00a78e;
}
#style-3::-webkit-scrollbar-track
{
	
	background-color:#f0f0f0;
}
#style-3::-webkit-scrollbar
{
	width:4px;
	background-color: #f5f5f5;
}
#style-3::-webkit-scrollbar-thumb
{
	
	background-color:#0091cd;
}
/*-- spark --*/
.spark-left {
padding-left: 0;
}
.spark-right {
padding: 0;
}
text.highcharts-yaxis-title {
font-size: 1.5em;
font-weight: 600;
color: #000 ! important;
}
.spar-bottom {
margin: 1em 0;
}
.spar-left {
padding-left: 0;
}
.spar-right {
padding: 0;
}
/*-- spark --*/
/*-- General stats --*/
.stats-info{
background-color: #fff;
}
.panel .panel-heading {
padding: 20px;
overflow: hidden;
border-top-left-radius: 0;
border-top-right-radius: 0;
border: 0!important;
height: 55px;
font-size: 14px;
font-weight: 600;
}
.panel .panel-heading .panel-title {
font-size: 14px;
float: left;
margin: 0;
padding: 0;
font-weight: 600;
}
h4.asd label {
font-size: 1em;
padding-left: 1em;
color: #000;
	cursor: pointer;
}
.stats-info ul {
margin: 0;
}
.list-unstyled {
padding-left: 0;
list-style: none;
}
.stats-info ul li {
border-bottom: 1px solid #eee;
padding:14.35px 0;
font-size: 0.85em;
color: #999;
}
.stats-info ul li.last{
	border-bottom:0;
	padding-bottom:4px;
}
.text-success {
color:#00ACED;
}
.text-danger {
color: #f25656;
}
/*-- col_3 --*/
/*-- //todo inbox chat section in index page --*/
/*---- responsive-design -----*/
@media(max-width:1440px){
	.login-page {
		width: 43%;
	}
	.general .tab-content {
		padding: 1em 0.5em 0;
	}
	.activity-desc-sub:before {
		left: -9%;
	}
	.activity-desc-sub1:after {
		right: -8.5%;
	}
	.doughnut-grid {
		width: 81%;
		margin: 2.5em auto 1.6em;
	}
	.chrt-page-grids canvas#line {
		height: 310px !important;
	}
	.chrt-page-grids canvas#bar {
		height: 292px !important;
	}
	.navbar-collapse.bs-example-js-navbar-scrollspy {
		padding: 0;
	}
	.navbar-collapse.bs-example-js-navbar-scrollspy .nav > li > a {
		padding: 15px 13px;
	}
	.general-grids .tab-content {
		overflow-y: scroll;
		height: 228px;
	}
	.header-left {
		margin-left: 17%;
	}
	.button_set_one.three .btn {
		padding: 9px 13px 9px 35px;
	}
	.widgettable .table>tbody>tr>td, .widgettable .table>tbody>tr>th, .widgettable .table>tfoot>tr>td, .widgettable .table>tfoot>tr>th, .widgettable .table>thead>tr>td, .widgettable .table>thead>tr>th {
		padding: 15px 0;
	}
	.mail.mail-name {
		width: 10%;
	}
}
@media(max-width:1366px){
	.navbar-collapse.bs-example-js-navbar-scrollspy .nav > li > a {
		padding: 15px 11px;
	}
	.weather-right ul li:nth-child(2) {
		margin: 0 1em;
	}
	.activity-desc-sub1:after {
		right: -9%;
	}
	.activity-desc-sub:before {
		left: -10%;
	}
	.login-page {
		width: 46%;
	}
	.doughnut-grid {
		margin: 3.6em auto 1.6em;
	}
	.pie-grid {
		margin: 4.1em auto 1em;
	}
	.polar-area {
		margin: 2.8em auto 0.6em;
	}
	#navbar-example2 .navbar-brand {
		font-size: 16px;
	}
	.navbar-collapse.bs-example-js-navbar-scrollspy .nav > li > a {
		padding: 15px 8px;
		font-size: 0.9em;
	}
	.header-left {
		margin-left: 18%;
	}
	.r3_counter_box .fa {
		font-size: 23px;
		width: 60px;
		height: 60px;
		line-height: 60px;
	}
	.r3_counter_box {
		padding: 15px 10px;
	}
	ul.bt-list li a {
		padding: .6em 1em;
	}
	.stats-body {
		margin-top: 0px;
		margin-bottom: 0px;
	}
	.malorm-bottom p {
		font-size: .9em;
		line-height: 1.7em;
	}
	.stats-info ul li {
		padding: 11.8px 0;
	}
	.malorum-top {
		min-height: 220px;
	}
	.inbox-page p {
		font-size: .9em;
	}
	.signup-page {
		width: 50%;
	}
}
@media(max-width:1280px){
	.profile-text {
		padding: 1.5em 2em;
	}
	.profile-btm ul li {
		width: 32.4%;
	}
	.activity-img1 {
		width: 75%;
	}
	.activity-img2 {
		padding: 0;
	}
	.activity-right .activity-img {
		padding-left: 0;
	}
	.activity-left .activity-img {
		padding-right: 0;
	}
	.activity-desc-sub:before {
		left: -9.4%;
	}
	.activity-desc-sub1:after {
		right: -8.5%;
	}
	.weather-right ul li:nth-child(2) {
		margin: 0 0.3em;
	}
	.sign-up2 {
		width: 75%;
	}
	.tile-progress {
		padding: 2em 2em;
	}
	.login-page {
		width: 52%;
	}
	.chrt-page-grids canvas#line {
		height: 282px !important;
	}
	.chrt-page-grids canvas#bar {
		height: 265px !important;
	}
	.header-left {
		margin-left: 19%;
		width: 40%;
	}
	.col-md-3.widget {
		width: 19.2%;
	}
	.widget1 {
		margin-right: 1%;
	}
	.stats span {
		font-size: 15px;
	}
	ul.info li.col-md-6 {
		padding: 0;
	}
	.activity-desc {
		padding: 0;
	}
	.activity-desc1 h6 {
		font-size: 11px;
	}
	.icon-box i {
		margin-right: 5px !important;
	}
	.icon-box {
		padding: 8px 10px;
	}
	.list-group .list-group-item {
		padding: 0px 0px;
	}
	.streamline .sl-item {
		padding-bottom: 11px;
	}
	.work-progres {
	padding: 2em 1em 0em;
	}
	.malorm-bottom {
		padding: 1.5em 1em;
	}
	.mail {
		margin-right: .5em;
	}
	.inbox-row {
		padding: 0.5em;
	}
	.error-page p {
		width: 50%;
	}
}
@media(max-width:1080px){
	.logo a {
		padding: 0.9em 2.35em .7em;
	}
	.header-left {
		width: 52%;
	}
	.cbp-spmenu-push div#page-wrapper {
		margin:0 0 0 14.5em;
		padding: 5em 1em 2em;
	}
	.sidebar ul li {
		margin-bottom: 0.8em;
	}
	.wid-social .social-info h3 {
		font-size: 1.1em;
	}
	.stats-info ul li {
		padding-bottom: 9px;
	}
	.youtube .social-icon {
		margin-right: 4em;
	}
	h3.title1 {
		font-size: 1.6em;
	}
	.general-grids {
		width: 100%;
	}
	.general-grids.grids-right {
		margin: 2em 0 0;
	}
	#navbar-example2 .navbar-brand {
		font-size: 18px;
	}
	.navbar-collapse.bs-example-js-navbar-scrollspy .nav > li > a {
		padding: 15px 20px;
		font-size: 1em;
	}
	.header-head {
		padding: 2em 1em;
	}
	.profile-text {
		padding: 1.5em 1.2em;
	}
	div#vmap {
		height: 311px !important;
	}
	.profile-btm ul li {
		width: 32.2%;
	}
	.wid-social {
		width: 33.33%;
		padding: 16px 8px;
	}
	.wid-social .social-info h4 {
		font-size: 0.7em;
		letter-spacing: 0.5px;
	}
	.inbox-page {
		width: 96%;
	}
	.weather-grids {
		width: 100%;
	}
	.weather-grids.weather-right {
		margin: 2em 0 0;
	}
	.weather-right ul li:nth-child(2) {
		margin: 0 3em;
	}
	.scrollbar {
		padding: 1.5em 1em 0;
	}
	.activity-row p {
		font-size: 0.8em;
	}
	.activity-row {
		margin-bottom: 0;
		padding-bottom: 1em;
	}
	.activity-desc-sub:before {
		left: -12%;
		top: 17%;
	}
	.activity-desc-sub1:after {
		right: -10.5%;
	}
	.sign-up2 {
		width: 72%;
	}
	.single-bottom ul li {
		padding: 0px 0px 18px;
	}
	.inbox-page {
		width: 100%;
	}
	.compose-left ul li a {
		padding: 0.7em 1em;
	}
	.login-page,.signup-page {
		width: 60%;
	}
	.chrt-page-grids canvas#line {
		height: 237px !important;
	}
	.polar-area {
		margin: 2em auto 0em;
		width: 80%;
	}
	.chrt-page-grids canvas#bar {
		height: 223px !important;
	}
	.header-left {
		width: 30%;
		margin-left: 22%;
	}
	.col-md-3.widget {
		width: 32%;
		margin-right: 2%;
		margin-bottom: 2%;
	}
	.col-md-3.widget:nth-child(3){
		margin-right: 0%;
	}
	.col-md-3.widget:nth-child(4),.col-md-3.widget:nth-child(5){
		margin-bottom: 0%;
	}
	.row-one .content-top-2.card {
		width: 100%;
	}
	.stat {
		width: 49%;
	}
	.charts-grids.widget {
		width: 49%;
		padding: 20px;
	}
	.stat {
		width: 50%;
		padding-left: 0;
		padding-right: 15px;
	}
	.col-md-2.stat {
		padding-left: 0;
		padding-right: 0px;
	}
	.content-top-1:nth-child(3), .content-top:nth-child(3) {
		margin-bottom: 0;
	}
	.charts-grids.widget {
		margin-top: 1em;
	}
	.span_8 {
		width: 49%;
		margin-right: 1%;
		margin-top: 1em;
	}
	.span_8:nth-child(2) {
		margin-right: 0%;
	}
	.icon-box {
		width: 33.33%;
		margin: .5em 0;
	}
	.button_set_one {
		width: 100%;
	}
	.button-states-top-grid,.button-size-grids{
		width: 50%;
	}
	.hover-buttons{
		width: 100%;
	}
	.elements .widgettable {
		width: 100%;
		padding-right: 0;
		padding-left: 0;
	}
	.chit-chat-layer1-left {
		padding-left: 0px;
		padding-right: 0px;
		width: 100%;
	}
	.chit-chat-layer1-rit {
		padding-right: 0px;
		padding-left: 0;
		padding-top: 15px;
		width: 100%;
	}
	.agile-calendar {
		width: 100%;
		padding-right: 0;
		padding-bottom: 15px;
	}
	.stats-info.widget {
		width: 100%;
	}
	.chart-layer1-right {
		padding-right: 0px;
		padding-left: 0;
		width: 100%;
		padding-top: 15px;
	}
	.inbox-page {
		width: 95%;
	}
	.mail.mail-name {
		width: 15%;
	}
	.error-page p {
		width: 60%;
	}
	span.malorum-pro {
		left: 44%;
	}
}
@media(max-width:1024px){
	.panel_2 {
		padding: 1.5em 1em 0;
	}
	.header-left {
		margin-left: 24%;
	}
	.charts{
		margin: 1em 0 0 0;
	}
	.w3ls-high {
		margin-right: .5em;
		width: 100%;
	}
	.agileits-high {
		margin-top: 1em;
		margin-left: 0;
		width: 100%;
	}
	.graph-container {
		height: 350px !important;
	}
	.bs-example-tooltips .btn-default {
		margin-right: .5em;
	}
	.icon-box {
		padding: 8px 8px;
	}
	.compose-left ul li.head {
		padding: 0.5em 1em;
		font-size: 1.1em;
	}
	.error-page p {
		width: 70%;
	}
	
	.compose-left,.compose-right {
		width: 100%;
		float: left;
		margin-left: 0%;
		margin-top: 2%;
	}
	.button_set_one.two .btn {
		margin: 0 0em;
	}
}
@media(max-width:991px){
	.search-box {
		margin: 1.2em 0 0 2em;
	}
	.stats-info.stats-last {
		width: 62%;
		float: left;
	}
	.map {
		float: left;
		width: 60%;
	}
	.social-media {
		width: 38%;
		float: left;
	}
	.grid_box1 {
		margin-bottom: 1em;
	}
	.example_6, .media_1 {
		margin: 1.5em 0 0 0;
	}
	.media_1-left {
		width: 100%;
	}
	.media_1-right {
		float: none;
		margin: 0;
		width: 100%;
	}
	.panel_2 {
		padding: 1.5em 1em;
	}
	.panel_2.panel_3 {
		margin-top: 1.5em;
	}
	.panel_2 .table {
		margin-bottom: 0;
	}
	.modal-grids {
		float: left;
		width: 33%;
		text-align: center;
	}
	.modal-grids:nth-child(4) {
		margin-top: 0.2em;
	}
	.bs-example-tooltips {
		text-align: left;
	}
	.modals {
		margin-top: 1.5em;
	}
	.tool-tips.widget-shadow {
		margin-top: 1.5em;
	}
	h3.title1 {
		font-size: 1.6em;
	}
	.profile {
		width: 100%;
	}
	.chat-mdl-grid {
		margin: 1.5em 0;
	}
	.profile-text {
		padding: 1.5em 5em;
	}
	.scrollbar {
		padding: 1.5em 3em 0;
	}
	.activity-desc-sub:before {
		left: -3.8%;
		top: 29%;
	}
	.activity-desc-sub, .activity-desc-sub1 {
		padding: .7em 1em;
	}
	.activity-desc-sub1:after {
		right: -3.5%;
		top: 36%;
	}
	.activity-row .col-xs-3 {
		width: 15%;
	}
	.activity-img1,.activity-img2 {
		width: 85%;
	}
	.activity-left .activity-img img {
		margin: 0 0 0 auto;
	}
	.mail.mail-name {
		width: 20%;
	}
	.mail-right {
		margin-left: 0.8em;
	}
	.inbox-page h6 {
		font-size: 0.9em;
	}
	.inbox-page p {
		font-size: 0.9em;
	}
	.inline-form .form-group, .inline-form .checkbox, .form-two .form-group {
		margin-right: 0.5em;
	}
	.form-three {
		margin-top: 1em;
	}
	.validation-grids {
		width: 100%;
	}
	.validation-grids.validation-grids-right {
		margin: 1.5em 0 0;
	}
	.inline-form.widget-shadow {
		margin-top: 1.5em;
	}
	.login-page {
		width: 70%;
	}
	.sign-up2 {
		width: 71%;
	}
	.chrt-page-grids {
		width: 100%;
		padding: 1.5em 2em;
	}
	.chrt-page-grids.chrt-right {
		margin: 1.5em 0 0;
	}
	.doughnut-grid {
		width: 65%;
		margin: 2em auto 0.5em;
	}
	.radar-grid {
		width: 70%;
	}
	.polar-area, .pie-grid {
		width: 60%;
		margin: 1.5em auto 0;
	}
	.header-left {
		margin-left: 25%;
		width: 27%;
	}
	.search-box {
		margin: .7em 0 0 1em;
		width: 41%;
	}
	.stat {
		float: left;
	}
	ul.info li.col-md-6 {
		padding: 0;
		width: 50%;
		float: left;
	}
	.col-md-6.top-content {
		float: left;
	}
	.col-md-6.top-content {
		float: left;
		width:50%;
	}
	.span_8 {
		width: 100%;
		margin-right: 1%;
		margin-top: 1em;
	}
	.icons a {
		font-size: 13px;
	}
	.col-md-3.widget {
		float: left;
	}
	.error-page h2 {
		font-size: 80px;
	}
	form.search-form {
		width: 45%;
	}
}
@media(max-width:900px){
	.logo a {
		padding: 0.95em 1.95em .7em;
	}
	.logo a span {
		letter-spacing: 5px;
	}
	.search-box {
		width: 39%;
	}
	.profile_details_drop a.dropdown-toggle {
		padding: 0.8em 2em 0 1em;
	}
	.profile_details ul li ul.dropdown-menu.drp-mnu {
		padding: 0.5em;
		min-width: 163px;
	}
	.cbp-spmenu-vertical {
		padding: 1.5em 0;
		width: 231px;
	}
	.sidebar ul li a {
		font-size: 0.9em;
	}
	.sidebar .nav-second-level li a {
		font-size: .8em !important;
	}
	.cbp-spmenu-push div#page-wrapper {
		margin: 0 0 0 14.5em;
	}
	.bs-example-tooltips .btn-default {
		margin-right: 0.2em;
	}
	.grid_3.grid_5 .label {
		font-size: 60%;
	}
	.well {
		font-size: 0.9em;
		line-height: 1.8em;
		padding: 11px 15px;
	}
	.compose-right .panel-body {
		padding: 1.5em;
	}
	.tables .panel-body, .tables .bs-example {
		padding: 1.5em 1.5em 0em;
	}
	.tables h4 {
		margin-bottom: 0.8em;
	}
	.tables .table > thead > tr > th, .tables .table > tbody > tr > th, .tables .table > tfoot > tr > th, .tables .table > thead > tr > td, .tables .table > tbody > tr > td, .tables .table > tfoot > tr > td {
	font-size: 0.9em;
	}
	.form-body {
		padding: 1.5em;
	}
	.forms button.btn.btn-default {
		padding: .5em .9em;
	}
	.login-page,.signup-page {
		width: 75%;
	}
	.login-top h4 {
		font-size: 1em;
	}
	.sign-up2 input[type="text"], .sign-up2 input[type="password"] {
		padding: 8px 10px;
		margin: 0.5em 0;
	}
	.sign-up1 h4 {
		margin: 1em 0 0;
	}
	.blank-page p {
		font-size: 0.9em;
	}
	.doughnut-grid {
		width: 55%;
	}
	.polar-area, .pie-grid {
		width: 55%;
	}
	.header-left {
		margin-left: 27%;
		width: 23%;
	}
	.sb-search-input {
		width: 80%;
		background: url(../images/search-icon.png) no-repeat 130px 12px;
	}
	.search-box {
		width: 46%;
		margin: .7em 0 0 0em;
	}
	.charts-grids.widget {
		width: 100%;
		padding: 20px;
	}
	.charts-grids.widget:nth-child(2) {
		margin-top: 1em;
		margin-left: 0;
	}
	.icon-box {
		width: 50%;
	}
	.input__label {
		width: 80%;
		left: 20%;
	}
	.mail.mail-name {
		width: 9%;
	}
	.mail-right {
		margin-left: 0.5em;
	}
	.button_set_one.two .btn {
		margin: 0.4em 0em 0;
	}
	.button_set_one .btn {
		margin-top: .4em;
	}
}
@media(max-width:800px){
	.header-left {
		margin-left: 27%;
		width: 26%;
	}
	.profile_details ul li {
		margin-right: 10px;
	}
	.col-md-3.widget {
		width: 48%;
		margin-right: 2%;
		margin-bottom: 2%;
	}
	.col-md-3.widget:nth-child(3) {
		margin-right: 2%;
	}
	.col-md-3.widget:nth-child(4) {
		margin-bottom: 2%;
	}
	.sidebar-left {
		width: 210px !important;
	}
	.cbp-spmenu-push div#page-wrapper {
		margin: 0 0 0 13em;
	}
	.header-right {
		float: right;
		width: 47%;
	}
	.search-box {
		width: 44%;
	}
	.sb-search-input {
		width: 80%;
		background: url(../images/search-icon.png) no-repeat 100px 12px;
	}
	.button_set_one.three .btn {
		margin: 5px 2px 0;
	}
}
@media(max-width:768px){
	.logo a {
		padding: 1.1em 1.3em .7em;
	}
	.logo a h1 {
		font-size: 1.2em;
		line-height:1em;
	}
	.logo a span {
		font-size: .6em;
	}
	.search-box {
		width: 45%;
		margin: 1em 0 0 1em;
	}
	.profile_details_drop a.dropdown-toggle {
		padding: 0.6em 2em 0 1em;
	}
	.profile_details li a i.fa.lnr {
		top: 28%;
	}
	.cbp-spmenu-vertical {
		padding: 1em 0;
		width: 201px;
		top: 63px;
	}
	.cbp-spmenu-push div#page-wrapper {
		margin: 0;
	}
	.cbp-spmenu-left.cbp-spmenu-open {
		left:0
	}
	.cbp-spmenu-vertical {
		
		left: -309px;
	}
	.activity-desc-sub:before {
		left: -3.4%;
	}
	.activity-desc-sub1:after {
		right: -3.1%;
	}
	.header-left {
		margin-left: 2%;
		width: 30%;
	}
	.header-right {
		float: right;
		width: 55%;
	}
	.sb-search-input {
		width: 80%;
		background: url(../images/search-icon.png) no-repeat 120px 12px;
	}
	.login-page, .signup-page {
		width: 60%;
	}
}
@media(max-width:767px){
	.sidebar .navbar-collapse.collapse {
		display: block;
	}
	.cbp-spmenu-vertical {
		padding: 1em 0;
		width: 201px;
		top: 47px;
	}
	.sidebar-left {
		width: 250px !important;
	}
	.navbar-inverse .navbar-toggle {
		border-color: transparent;
		background: #629aa9;
	}
	.navbar-toggle {
		border-radius: 30px;
		height: 40px;
		width: 40px;
		text-align: center;
	}
	.navbar-toggle .icon-bar {
		width: 20px;
	}
	.navbar-inverse .navbar-toggle:hover, .navbar-inverse .navbar-toggle:focus {
		background-color: #F2B33F;
	}
	.search-box {
		width: 45%;
		margin: .7em 0 0 1em;
	}
	.icon-box {
		width: 50%;
		float: left;
	}
}
@media(max-width:736px){
	.header-right {
		float: right;
		width: 57%;
	}
	.login-page h3.title1, .signup-page h3.title1 {
		padding: .9em 1em;
		border-bottom: 8px solid #4b7884;
		font-size: 1.4em;
	}
	.login-page input[type="email"], .login-page input[type="password"],.signup-page input[type="text"], .signup-page input[type="email"], .signup-page input[type="password"]{
		font-size: .9em;
		padding: 12px 15px 12px 37px;
	}
	.login-body {
		padding: 3em 2em;
	}
	.login-page input[type="submit"],.sub_home input[type="submit"] {
		padding: .5em 1em;
	}
	.compose-left,.compose-right {
		width: 100%;
		float: left;
		margin-left: 0%;
		margin-top: 2%;
	}
	.mail.mail-name {
		width: 16%;
	}
	.folder, .chat-grid {
		width: 49%;
		float: left;
		margin-right: 2%;
	}
	.chat-grid.widget-shadow {
		margin-top: 0em;
		margin-right: 0%;
	}
	.button-states-top-grid, .button-size-grids {
		width: 100%;
		float: none;
		margin: 0;
		margin-bottom: 1em;
	}
	button.btn.btn-default:nth-child(4) {
		margin-top: .5em;
	}
}
@media(max-width:667px){
	.header-left {
		width: 36%;
	}
	.header-right {
		float: right;
		width: 62%;
	}
	.mail.mail-name {
		width: 11%;
	}
	.mail-right {
		margin-left: 0.4em;
	}
	/*.row {
		margin: 1.3em 1em 0;
	}*/
	.modal-grids:nth-child(4) {
		margin-top: 0em;
	}
	.modal-grids button.btn.btn-primary {
		font-size: .9em;
	}
	.modal-grids:nth-child(2) {
		width: 25%;
	}
	.modal-grids:nth-child(3) {
		width: 38%;
	}
}
@media(max-width:640px){
	.sidebar .navbar-collapse.collapse {
		display: block;
	}
	.profile_details_drop a.dropdown-toggle {
		padding: 0 3em 0 0;
	}
	.header-right span.badge {
		font-size: 9px;
		line-height: 13px;
		width: 18px;
		height: 17px;
	}
	.header-right i.fa{
		font-size: .9em;
		margin-right: 0.2em;
	}
	.progress {
		height: 7px;
		margin-bottom: 5px;
	}
	ul.dropdown-menu.drp-mnu li {
		padding: 6px 0;
	}
	.profile_details .dropdown-menu > li > a {
		font-size: 1em;
	}
	.profile_details li a i.fa.lnr {
		top: 14%;
	}
	h4.title {
		font-size: 1em;
	}
	.stats-left h4 {
		font-size: 1.7em;
	}
	.stats-right {
		padding: 1.35em 0em;
	}
	.wid-social {
		padding: 15px 8px;
	}
	.activity-desc-sub:before {
		left: -4.3%;
	}
	.activity-desc-sub1:after {
		right: -3.8%;
	}
/*	.charts, .row {
		margin: 1.3em 0 0;
	}*/
	.charts-grids canvas#pie {
		width: 100% !important;
		height: auto !important;
		margin: 0.9em 0;
	}
	.stats-info ul li {
		font-size: 0.8em;
	}
	.progress.progress-right {
		width: 33%;
		height: 5px;
	}
	.stats-table td {
		padding: 9px 13px !important;
	}
	.grids {
		padding: 1.5em 0.5em;
	}
	.grid-bottom {
		padding: 1.5em;
	}
	.grid-bottom  th {
		font-size: 0.8em;
	}
	.panel-info.widget-shadow {
		padding: 1.5em 0.5em 0.5em;
	}
	.panel-body {
		font-size: 0.9em;
	}
	.navbar-nav {
		margin: 0;
	}
	.grid_5 {
		margin-top: 1.5em;
	}
	.grid_4 {
		margin-top: 20px;
	}
	.tab-content > .active {
		padding: 0.5em 0 0;
	}
	.bs-example {
		margin-top: 1em;
	}
	.widget_1_box {
		width: 100%;
	}
	.widget_1_box.widget-mdl-grid {
		margin: 3% 0;
	}
	.tile-progress {
		padding: 1.5em 2em;
	}
	.inbox-page.row {
		margin-top: 1.5em;
	}
	.compose-left ul li a {
		padding: 0.58em 1em;
	}
	.form-grids-right label {
		float: left;
		text-align: right;
		width: 20%;
	}
	.form-grids .checkbox label {
		width: 100%;
		text-align: left;
	}
	.form-grids .col-sm-offset-2 {
		margin-left: 7em;
	}
	.form-grids .col-sm-9 {
		float: right;
		width: 80%;
	}
	.forms button.btn.btn-default {
		padding: .5em 2.5em;
	}
	.signup-page p {
		font-size: 0.9em;
		margin: 0 5em;
		line-height: 1.8em;
	}
	.footer p {
		font-size: 0.9em;
	}
	.profile_details li a i.fa.lnr {
		top: 34%;
		right: 0%;
	}
	.profile_details_drop a.dropdown-toggle {
		padding: 0.4em 2em 0 0;
	}
	h3.hdg {
		font-size: 1.5em;
	}
	.error-page p {
		width: 80%;
	}
}
@media(max-width:600px){
	.search-box {
		width: 47%;
		margin: .7em 0 0 0em;
	}
	.registration {
		font-size: 15px;
	}
	.error-page p {
		width: 85%;
	}
	.button-states-top-grid, .button-size-grids {
		width: 100%;
	}
	span.malorum-pro {
		left: 42%;
	}
	.mail-right.dots_drop{
		width: 2%;
	}
	.mail-right {
		margin-left: 0.4em;
		width: 13%;
	}
	.mail {
		margin-right: 1.5em;
	}
}
@media(max-width:568px){
	.search-box {
		width: 44%;
		margin: .7em 0 0 0em;
	}
	.sb-search-input {
		width: 90%;
		background: url(../images/search-icon.png) no-repeat 110px 12px;
	}
	.login-page, .signup-page {
		width: 70%;
	}
	.mail.mail-name {
		width: 70%;
	}
	.modal-grids:nth-child(3) {
		width: 40%;
		}
	button.btn.btn-default:nth-child(3) {
		margin-top: .5em;
	}
	.mail {
		margin-right: 1em;
	}
}
@media(max-width:480px){
	.search-box {
		margin: 1em 0 0 3em;
	}
	.logo a {
		padding: 1em 1.38em .6em;
	}
	.logo a span {
		font-size: .55em;
	}
	.cbp-spmenu-vertical {
		top: 67px;
	}
	.stats-left {
		width: 68%;
		padding: 1em .6em;
	}
	.stats-right {
		padding: 1.52em 0;
		width: 32%;
	}
	.sidebar .nav-second-level li a {
		padding-left: 40px !important;
	}
	.stats-left h4 {
		font-size: 1.4em;
	}
	.stats-right label {
		font-size: 1.5em;
	}
	/*.charts, .row {
		margin: 1.1em 0 0;
	}*/
	.charts .charts-grids {
		width: 100%;
		padding: 1em 2em;
	}
	.charts-grids canvas#bar,.charts canvas#line {
		height: 215px !important;
	}
	.charts-grids.states-mdl {
		margin: 4% 0;
	}
	.charts-grids canvas#pie {
		width: 73% !important;
		margin: 0 auto;
	}
	.stats-info.widget {
		width: 100%;
		float: none;
	}
	.stats-info.stats-last {
		width: 100%;
		float: none;
		margin: 4% 0 0;
	}
	.map {
		float: none;
		width: 100%;
	}
	div#vmap {
		height: 250px !important;
	}
	.social-media {
		width: 100%;
		float: none;
		margin: 3% 0 0;
	}
	.bs-example-tooltips .btn-default {
		margin: 0 auto 1em;
		display: block;
	}
	.modals {
		padding: 1.5em 1em;
	}
	.modals h4.title2 {
		margin: 0 0 1em 0;
	}
	.modal-grids {
		float: left;
		width: inherit;
		text-align: center;
		padding: 0;
	}
	.modal-grids:nth-child(3) {
		margin: 0 1em;
	}
	.modal-grids button.btn.btn-primary {
		font-size: 0.9em;
	}
	.scrollspy-example p {
		margin-bottom: 0.8em;
	}
	.tool-tips.widget-shadow {
		padding: 1.5em;
	}
	.general-grids.grids-right {
		margin: 1.5em 0 0;
	}
	.popover {
		max-width: 140px;
	}
	.grid_3.grid_5 .label {
		font-size: 41%;
	}
	h3.hdg {
		font-size: 1.5em;
	}
	.table {
		margin-bottom:0;
	}
	.header-top {
		padding: 1em 1.5em;
	}
	.weather-grids.weather-right {
		margin: 1.5em 0 0;
	}
	.weather-right ul li:nth-child(2) {
		margin: 0 0.4em;
	}
	.inbox-page p {
		font-size: 0.8em;
	}
	.inbox-page h4 {
		font-size: 1em;
		margin-bottom: 0.8em;
	}
	.mail {
		margin-right: 0.7em;
	}
	.mail.mail-name {
		width: 10%;
	}
	.inbox-page h6 {
		font-size: 0.8em;
	}
	.inbox-row a .mail {
		margin: 0;
	}
	h3.title1 {
		margin-bottom: 0.6em;
	}
	.mail-body {
		padding: 0.5em 1em;
	}
	.mail-body input[type="text"] {
		padding: 0.5em 0;
		font-size: 0.9em;
	}
	.mail-body input[type="text"]:focus {
		padding: 1em 0;
	}
	.mail-body input[type="submit"] {
		font-size: 0.9em;
		margin-top: 0.2em;
	}
	.inbox-page.row {
		margin-top: 1em;
	}
	.footer p {
		font-size: 0.9em;
	}
	.compose-left {
		width: 100%;
		float: none;
	}
	.chat-grid.widget-shadow {
		margin-top: 1.3em;
	}
	.compose-right {
		width: 100%;
		float: none;
		margin: 4% 0 0;
	}
	.compose-right .alert.alert-info {
		padding: 6px 20px;
	}
	.compose-right .alert {
		margin-bottom: 14px;
	}
	.tables h4 {
		margin-bottom: 0.5em;
	}
	.tables .panel-body, .tables .bs-example {
		padding: 1.5em 1.5em;
	}
	.tables .table > thead > tr > th, .tables .table > tbody > tr > th, .tables .table > tfoot > tr > th, .tables .table > thead > tr > td, .tables .table > tbody > tr > td, .tables .table > tfoot > tr > td {
		font-size: 0.8em;
	}
	h3.title1 {
		font-size: 1.3em;
	}
	.form-three {
		padding: 1em 1.5em;
	}
	.form-title {
		padding: 0.8em 1.5em;
	}
	.forms h4 {
		font-size: 1.1em;
	}
	.login-page {
		width: 90%;
		margin: 0 auto;
	}
	.login-top {
		padding: 1em;
	}
	.login-top h4 {
		font-size: 0.9em;
	}
	.login-body {
		padding: 1.5em;
	}
	.login-page input[type="text"], .login-page input[type="password"] {
		margin: 0 0 1em 0;
	}
	.login-page label.checkbox {
		font-size: 0.9em;
	}
	.login-page-bottom h5 {
		font-size: 1.3em;
		margin: 1em 0;
	}
	.social-btn i {
		padding: .8em 1em;
		font-size: 0.8em;
	}
	.social-btn i.fa {
		padding: .5em 0.8em;
		font-size: 1em;
	}
	.sign-up1 h4 {
		margin: 1.1em 0 0;
		font-size: 0.9em;
	}
	.sign-up2 label {
		margin: 0em 2em 0 0;
		font-size: .9em;
	}
	.sub_home input[type="submit"] {
		margin: 0.5em 0 0 6.6em;
		width: 28%;
		font-size: 0.9em;
	}
	.blank-page {
		padding: 1.5em;
	}
	.chrt-page-grids {
		width: 100%;
		padding: 1em 1.5em;
	}
	ul.nav.nav-second-level li a {
		padding: 8px 15px;
	}
	span.nav-badge-btm {
		font-size: 11px;
		padding: 0 0.7em;
	}
	span.nav-badge {
		font-size: 10px;
		width: 23px;
		height: 23px;
		line-height: 23px;
	}
	.sidebar ul li {
		margin-bottom: 0.6em;
	}
	h3.title1 {
		font-size: 1.4em;
	}
	.header-left {
		width: 100%;
		float:none;
	}
	.header-right {
		float:none;
		width: 100%;
	}
	.search-box {
		margin: 1em 0 1em 0em;
	}
	.profile_details {
		float: right;
		margin-right: 2em;
	}
	.cbp-spmenu-push div#page-wrapper {
		padding: 9em 1em 2em;
	}
	.input__label {
		width: 90%;
		left: 10%;
	}
	.sb-search-input {
		width: 90%;
		background: url(../images/search-icon.png) no-repeat 160px 12px;
	}
	.stat {
		width: 100%;
		padding-left: 0;
		padding-right: 0px;
	}
	.content-top-1:nth-child(3) {
		margin-bottom: 1em;
	}
	.col-md-2.stat {
		padding-left: 0;
		padding-right: 15px;
	}
	.cbp-spmenu-vertical {
		top: 55px;
	}
	.content-top-2.charts-grids.w3ls-high.card.agileits-high {
		padding: 1em 0em;
	}
	.login-page, .signup-page {
		width: 95%;
		margin: 2em auto 0;
	}
	.col-md-2.stat {
		padding-left: 0;
		padding-right: 0px;
	}
	.navbar-collapse {
		padding-right: 0px;
		padding-left: 0px;
	}
	ul.bt-list li {
		width: 49%;
		margin: 2% 0;
	}
	.folder, .chat-grid {
		width: 100%;
		float: none;
		margin-right: 0%;
	}
	.modal-grids,.modal-grids:nth-child(2),.modal-grids:nth-child(3) {
		width: 100%;
		float: none;
	}
	.modal-grids:nth-child(3) {
		margin:.5em 0;
	}
	.grid-bottom {
		width: 100%;
		overflow-x: scroll;
	}
}
@media(max-width:414px){
	.sb-search-input {
		width: 90%;
		background: url(../images/search-icon.png) no-repeat 135px 12px;
	}
	.r3_counter_box .fa {
		font-size: 20px;
		width: 55px;
		height: 55px;
		line-height: 55px;
	}
	.r3_counter_box {
		padding: 15px 8px;
	}
	.google-plus,.twitter,.facebook {
		padding: .5em 1em;
	}
	.top-content a i.fa {
		font-size: 25px;
	}
	.scrollbar {
		padding: 1.5em 1em 0;
	}
	.icon-box {
		width: 100%;
		float: none;
	}
	.signup-page input[type="text"] {
		background: url(../images/user.png)no-repeat 8px 10px #fff;
	}
	.login-page h2.title1, .signup-page h2.title1 {
		padding: .8em 1em;
		font-size: 1.4em;
	}
	.mail-right {
		width: 20%;
	}
}
@media(max-width:384px){
	.profile_details {
		float: right;
		margin-right: 0em;
	}
	.sb-search-input {
		width: 90%;
		background: url(../images/search-icon.png) no-repeat 130px 12px;
	}
	ul.bt-list li a {
		width: 100%;
	}
}
@media(max-width:375px){
	.sb-search-input {
		width: 90%;
		background: url(../images/search-icon.png) no-repeat 125px 12px;
	}
}
@media(max-width:320px){
	.r3_counter_box {
		padding: 15px 15px;
	}
	ul.bt-list li {
		width: 100%;
	}
	.registration {
		font-size: 14px;
	}
	.sb-search-input {
		width: 90%;
		background: url(../images/search-icon.png) no-repeat 105px 7px;
	}
	.grid_3.grid_4 {
		padding: 2em 1em ! important;
	}
	.variations-panel {
		padding: 0em;
	}
	.malorum-top {
		min-height: 150px;
	}
	.logo a {
		padding: 0.9em 1em .3em;
	}
	.logo a h1 {
		font-size: 1em;
		line-height: 0.7em;
	}
	.logo a span {
		letter-spacing: 4px;
		font-size: .5em;
	}
	.search-box {
		margin: 0.2em 0 0 0em;
	}
	.sb-search-input {
		font-size: 0.75em;
		padding: 0.5em .8em;
	}
	.header-right {
		margin-top: 0.5em;
	}
	.header-right span.badge {
		font-size: 8px;
		line-height: 11px;
		width: 16px;
		height: 15px;
	}
	.header-right i.fa {
		margin-right: 0;
	}
	.profile_details_drop a.dropdown-toggle {
		padding: 0 1.5em 0 0;
	}
	.user-name p {
		font-size: 0.9em;
	}
	.profile_img span.prfil-img {
		width: 32%;
	}
	.profile_img span.prfil-img img {
		width: 100%;
	}
	.user-name {
		margin-top: 5px;
		margin-left: 8px;
	}
	.profile_details li a i.fa.lnr {
		top: 8%;
	}
	.cbp-spmenu-vertical {
		padding: 0.5em 0;
		width: 161px;
		top: 52px;
	}
	i.nav_icon {
		margin-right: 0.7em;
		font-size: 1em;
	}
	.sidebar ul li a {
		font-size: 0.85em;
		padding: 5px 10px;
	}
	span.nav-badge {
		font-size: 8px;
		width: 21px;
		height: 20px;
		line-height: 21px;
	}
	span.nav-badge-btm {
		font-size: 9px;
		padding: 0 0.8em;
		line-height: 18px;
		top: 22%;
	}
	ul.nav.nav-second-level li a {
		padding: 5px 32px;
	}
	.cbp-spmenu-push div#page-wrapper {
		padding: 8.2em 1em 1.5em;
	}
	.bs-example5 {
		padding: 1em;
	}
	.panel-info.widget-shadow {
		padding: 1.5em 0em 0.5em;
	}
	.panel-body {
		padding: 10px;
	}
	.tool-tips.widget-shadow {
		padding: 1em;
	}
	.widget {
		float: none;
		width: 100%;
	}
	.widget.states-mdl {
		margin: 3% 0;
	}
	.stats-right {
		padding: 1.35em 0;
	}
	.charts .charts-grids {
		padding: 0.5em 1em;
	}
	h4.title {
		margin: 0.5em 0 0.8em;
	}
	.stats-info.stats-last {
		padding: 0.8em;
	}
	.stats-left {
		padding: 0.82em .6em;
	}
	.stats-table th {
		font-size: 0.7em;
	}
	.stats-table td {
		padding: 9px 8px !important;
		font-size: 0.7em;
	}
	.map {
		padding: 0.5em .8em;
	}
	.map h4.title {
		margin-bottom: 0;
	}
	div#vmap {
		height: 180px !important;
	}
	.social-media .icon-xlg {
		font-size: 20px;
	}
	.wid-social .social-info h3 {
		font-size: 1em;
	}
	.wid-social .social-info h4 {
		font-size: 0.75em;
		letter-spacing: 0px;
	}
	.charts, .row {
		margin: 1em 0 0;
	}
	.calender {
		padding: 0.5em 1em 1em;
	}
	h3.title1 {
		font-size: 1.3em;
	}
	.grids {
		padding: 1em 0em;
		margin-bottom: 1.5em;
	}
	.grid-bottom {
		padding: .5em;
	}
	.grid-bottom th {
		font-size: 0.6em;
	}
	.grid-bottom td {
		font-size: .75em;
	}
	.sidebard-panel .feed-element, .media-body, .sidebard-panel p {
		font-size: 0.8em;
	}
	.media {
		margin-top: 16px !important;
	}
	.media_1 td.head {
		font-size: 1em !important;
	}
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
		font-size: 0.7em;
		padding: 5px;
	}
	.padding-5{
		padding:2px;
	}
	.padding-l-5 {
		padding-left: 2px;
	}
	.padding-r-5 {
		padding-right: 2px;
	}
	.padding-t-5 {
		padding-top: 2px;
	}
	.padding-b-5 {
		padding-bottom: 2px;
	}
	.notification_header h3 {
		font-size: 11px;
	}
	.notification_desc p {
		font-size: 12px;
	}
	.notification_desc p span {
		font-size: 10px;
	}
	.dropdown-menu > li > a {
		padding: 3px 8px;
	}
	ul.dropdown-menu {
		min-width: 210px;
	}
	.notification_header {
		margin-bottom: 4px;
	}
	.notification_bottom a {
		font-size: 0.9em;
	}
	.progress {
		height: 6px;
		margin: 4px 0;
	}
	.profile_details ul li ul.dropdown-menu.drp-mnu {
		min-width: 140px;
	}
	ul.dropdown-menu.drp-mnu li {
		padding: 4px 0;
	}
	.modal-grids {
		float: none;
	}
	.modal-grids:nth-child(3) {
		margin: 1em 0;
	}
	.general-grids {
		padding: 1.5em 1em;
	}
	#navbar-example2 .navbar-brand {
		font-size: 16px;
	}
	.navbar {
		margin-bottom: 12px;
	}
	.scrollspy-example p {
		font-size: 0.8em;
	}
	.grids-right .nav > li > a {
		padding: 10px 10px;
	}
	.grids-right ul.dropdown-menu {
		min-width: 105px;
	}
	.tool-tips.widget-shadow {
		margin-top: 1.2em;
	}
	.general h4.title2 {
		font-size: 1.2em;
	}
	.header-head {
		padding: 1.5em 0;
	}
	.header-head h4 {
		font-size: 0.9em;
	}
	.weather-grids canvas {
		width:45px;
	}
	.bottom-head p {
		font-size: 0.8em;
	}
	.header-head h6 {
		font-size: 1.1em;
	}
	.header-top {
		padding: 1em 1em;
	}
	.header-top li p {
		font-size: 0.8em;
		padding: 2px 6px;
	}
	.weather-grids canvas#clear-day {
		width: 25px;
	}
	.header-top h2 {
		margin: .2em 0 0 .5em;
		font-size: 1.2em;
	}
	.weather-grids.weather-right {
		margin: 1.2em 0 0;
	}
	.weather-right ul li:nth-child(2) {
		margin: 2em 0;
	}
	.profile-text {
		padding: 1.5em 1.5em;
	}
	.scrollbar {
		padding: 1.5em 0.5em 0;
	}
	.activity-row .col-xs-3 {
		width: 21%;
	}
	.activity-img1, .activity-img2 {
		width: 78%;
	}
	.activity-desc-sub:before {
		left: -9%;
		top: 20%;
	}
	.activity-desc-sub1:after {
		right: -8.5%;
		top: 28%;
	}
	.activity-row p {
		font-size: 0.8em;
	}
	.activity-desc-sub, .activity-desc-sub1 {
		padding: .5em 0.8em;
	}
	h4.title3 {
		padding: 0.6em 1em;
	}
	.single-bottom ul li input[type="checkbox"]+label {
		font-size: 0.8em;
	}
	.single-bottom ul li input[type="checkbox"]+label span:first-child {
		width: 15px;
		height: 15px;
		top: 1px;
	}
	.compose-left ul li.head {
		font-size: 1em;
	}
	.compose-left ul li a {
		font-size: 0.9em;
	}
	.compose-right .panel-body {
		padding: 1em;
	}
	.form-control1,.form-control_2.input-sm {
		height: 35px;
	}
	.validation .form-control {
		height: 29px;
	}
	.validation-grids .btn-primary {
		font-size: 0.8em;
	}
	.control2 {
		height: 150px;
	}
	.compose-right input[type="submit"] {
		margin-top: 0.5em;
	}
	.tables .panel-body, .tables .bs-example {
		padding: 1.5em 1em;
	}
	.form-body {
		padding: 1em;
	}
	.form-grids-right label {
		font-size: 0.9em;
	}
	.form-grids .col-sm-9 {
		width: 77%;
	}
	.form-grids .col-sm-offset-2 {
		margin-left: 4em;
	}
	.form-three {
		padding: 1em;
	}
	.forms label {
		font-size: 0.9em;
	}
	.login-page {
		width: 100%;
	}
	.login-top h4 {
		font-size: 0.8em;
	}
	.login-body {
		padding: 1em;
	}
	.login-page input.user, .login-page input.lock{
		background-position: 11px 11px;
		background-size: 6%;
		padding: 8px 15px 8px 37px;
	}
	.signup-page input[type="text"], .signup-page input[type="email"], .signup-page input[type="password"] {
		font-size: .9em;
		padding: 10px 15px 10px 37px;
	}
	.signup-page input[type="email"] {
		background: url(../images/mail.png)no-repeat 8px 10px #fff;
		background-size: 20px;
	}
	.signup-page input[type="password"] {
		background: url(../images/lock.png)no-repeat 8px 10px #fff;
	}
	.forgot a {
		font-size: 0.9em;
	}
	.login-page input[type="submit"] {
		font-size: 0.9em;
	}
	.login-page-bottom h5 {
		font-size: 1.1em;
	}
	.social-btn.sb-two {
		margin: 1em 0 0;
	}
	.signup-page p {
		font-size: 0.8em;
		margin: 0;
	}
	.sign-up-row {
		width: 100%;
		padding: 1em;
	}
	.sign-up1,.sign-up2 {
		float: none;
		width: 100%;
	}
	.sign-up2 input[type="text"], .sign-up2 input[type="password"] {
		padding: 7px 10px;
		font-size: 12px;
	}
	.sign-up1 h4 {
		margin: .5em 0 0;
	}
	.blank-page {
		padding: 1em;
	}
	.sub_home input[type="submit"] {
		margin: 0.5em 0 0 0;
		width: 34%;
		font-size: 0.8em;
	}
	.chrt-page-grids {
		padding: 0.5em 1em;
	}
	.polar-area, .pie-grid {
		width: 80%;
		margin: 1em auto 0;
	}
	.profile_details {
		float: right;
		margin-right: 0em;
	}
	.col-md-3.widget {
		width: 100%;
		margin-right: 0%;
		margin-bottom: 2%;
	}
	.agileinfo-cdr {
		padding: 10px;
	}
	.agileinfo-cdr .lg-item-heading {
		font-size: 13px;
	}
	.profile_details li a i.fa.lnr {
		right: 2%;
	}
	.r3_counter_box {
		padding: 10px 10px 0px;
		min-height: 80px;
	}
	.top-content a i.fa {
		font-size: 25px;
	}
	.google-plus,.twitter,.facebook {
		padding: .5em 1em;
	}
	.top-content label {
		font-size: 1.4em;
	}
	.sidebar-left .navbar-brand {
		font-size: 20px;
		padding: 10px 0px 10px 15px;
	}
	span.dashboard_text {
		font-size: 11px;
		padding-left: 30px;
	}
	.sidebar-left {
		width: 215px !important;
	}
	button#showLeftPush {
		font-size: 1em;
	}
}
/*--//responsive-design---*/
</style>
<script>
	//Author: Monie Corleone
//Purpose: To draw line chart in canvas element
//The MIT License (MIT)
//Copyright (c) <2015> <Monie Corleone>
; (function ($, window, document, undefined) {
var pluginName = "SimpleChart";
var defaults = {
ChartType: "Line", //Area, Scattered, Bar, Hybrid, Pie, Stacked, Stacked Hybrid
xPadding: 60,
yPadding: 50,
topmargin: 25,
rightmargin: 20,
data: null,
toolwidth: 300,
toolheight: 300,
axiscolor: "#333",
font: "italic 10pt sans-serif",
headerfontsize: "14px",
axisfontsize: "12px",
piefontsize: "13px",
pielabelcolor: "#fff",
pielabelpercentcolor: "#000",
textAlign: "center",
textcolor: "#E6E6E6",
showlegends: true,
showpielables: false,
legendposition: 'bottom',
legendsize: '100',
xaxislabel: null,
yaxislabel: null,
title: null,
LegendTitle: "Legend",
pieborderColor: "#fff",
pieborderWidth: 2
};
function Plugin(element, options) {
this.element = element;
this.options = $.extend({}, defaults, options);
this.init();
}
Plugin.prototype = {
init: function () {
var that = this,
config = that.options;
var graph = $(that.element).addClass("SimpleChart").addClass(config.ChartType).append("<canvas class='SimpleChartcanvas'></canvas>").find('canvas').css({
float: (config.legendposition == 'right' || config.legendposition == 'left') ? 'left' : '',
'margin-top': config.topmargin,
'margin-right': config.rightmargin
});
var ctx = graph[0].getContext("2d");
graph[0].width = $(that.element).width() - (config.showlegends ? ((config.legendposition == 'right' || config.legendposition == 'left') ? parseInt(config.legendsize) + parseInt(config.xPadding) : 0) : 0) - config.rightmargin;
graph[0].height = $(that.element).height() - (config.showlegends ? ((config.legendposition == 'bottom' || config.legendposition == 'top') ? config.legendsize : 0) : 0) - config.topmargin;
var c = graph[0].getContext('2d');
switch (config.ChartType) {
case "Line":
that.drawAxis(c, graph);
that.drawLineAreaScatteredHybridCharts(c, graph);
break;
case "Area":
that.drawAxis(c, graph);
that.drawLineAreaScatteredHybridCharts(c, graph);
break;
case "Scattered":
that.drawAxis(c, graph);
that.drawLineAreaScatteredHybridCharts(c, graph);
break;
case "Hybrid":
that.drawAxis(c, graph);
that.drawLineAreaScatteredHybridCharts(c, graph);
that.drawBar(c, graph);
that.drawHybrid(c, graph);
break;
case "Bar":
that.drawAxis(c, graph);
that.drawBar(c, graph);
break;
case "Pie":
that.drawPie(c, graph);
break;
case "Stacked":
that.drawAxis(c, graph);
that.drawStacked(c, graph);
break;
case "StackedHybrid":
that.drawAxis(c, graph);
that.drawStacked(c, graph);
that.drawLineAreaScatteredHybridCharts(c, graph);
break;
}
//show legend
if (config.showlegends) {
that.drawLegends(graph);
}
},
reload: function () {
$(this.element).empty();
this.init();
},
destroy: function () {
$(this.element).empty();
},
FindYMax: function () {
config = this.options;
var max = 0;
for (var i = 0; i < config.data.length; i++) {
for (var j = 0; j < config.data[i].values.length; j++) {
if (config.data[i].values[j].Y > max) {
max = config.data[i].values[j].Y;
}
}
}
max += 10 - max % 10;
return max;
},
pixelX: function (val, i) {
config = this.options;
var graph = $(this.element).find('.SimpleChartcanvas');
return ((graph.width() - config.xPadding) / config.data[i].values.length) * val + (config.xPadding * 1.5);
},
pixelY: function (val) {
config = this.options;
var graph = $(this.element).find('.SimpleChartcanvas');
return graph.height() - (((graph.height() - config.yPadding) / this.FindYMax()) * val) - config.yPadding;
},
getRandomColor: function () {
var letters = '0123456789ABCDEF'.split('');
var color = '#';
for (var i = 0; i < 6; i++) {
color += letters[Math.floor(Math.random() * 16)];
}
return color;
},
drawAxis: function (c, graph) {
var that = this, xelementarray = new Array(),
config = this.options;
c.lineWidth = 2;
c.strokeStyle = config.axiscolor;
c.font = config.font;
c.textAlign = config.textAlign;
c.beginPath();
c.moveTo(config.xPadding, 0);
c.lineTo(config.xPadding, graph.height() - config.yPadding);
c.lineTo(graph.width(), graph.height() - config.yPadding);
c.stroke();
c.fillStyle = config.textcolor;
for (var i = 0; i < config.data.length; i++) {
for (var j = 0; j < config.data[i].values.length; j++) {
if (xelementarray.indexOf(config.data[i].values[j].X) < 0) {
xelementarray.push(config.data[i].values[j].X);
c.fillText(config.data[i].values[j].X, that.pixelX(j, i), graph.height() - config.yPadding + 20);
}
}
}
c.save();
var fontArgs = c.font.split(' ');
c.font = config.axisfontsize + ' ' + fontArgs[fontArgs.length - 1];
if (config.xaxislabel) {
c.fillText(config.xaxislabel, graph.width() / 2, graph.height());
}
if (config.yaxislabel) {
c.save();
c.translate(0, graph.height() / 2);
c.rotate(-Math.PI / 2);
c.fillText(config.yaxislabel, 0, 15);
c.restore();
}
if (config.title) {
$("<div class='simple-chart-Header' />").appendTo($(that.element)).html(config.title).css({
left: graph.width() / 2 - ($(that.element).find('.simple-chart-Header').width() / 2),
top: 5
});
}
c.restore();
c.textAlign = "right"
c.textBaseline = "middle";
var maxY = that.FindYMax();
var incrementvalue = "";
for (var i = 0 ; i < Math.ceil(maxY).toString().length - 1; i++) {
incrementvalue += "0";
}
incrementvalue = "1" + incrementvalue;
incrementvalue = Math.ceil(maxY / parseInt(incrementvalue)) * Math.pow(10, (Math.ceil(maxY / 10).toString().length - 1));
for (var i = 0; i < that.FindYMax() ; i += parseInt(incrementvalue)) {
c.fillStyle = config.textcolor;
c.fillText(i, config.xPadding - 10, that.pixelY(i));
c.fillStyle = config.axiscolor;
c.beginPath();
c.arc(config.xPadding, that.pixelY(i), 6, 0, Math.PI * 2, true);
c.fill();
}
},
drawPie: function (c, graph) {
var that = this,
config = this.options;
c.clearRect(0, 0, graph.width(), graph.height());
var totalVal = 0, lastend = 0;
for (var j = 0; j < config.data[0].values.length; j++) {
totalVal += (typeof config.data[0].values[j].Y == 'number') ? config.data[0].values[j].Y : 0;
}
for (var i = 0; i < config.data[0].values.length; i++) {
c.fillStyle = config.data[0].linecolor == "Random" ? config.data[0].values[i].color = randomcolor = that.getRandomColor() : config.data[0].linecolor;
c.beginPath();
var centerx = graph.width() / 2.2;
var centery = graph.height() / 2.2;
c.moveTo(centerx, centery);
c.arc(centerx, centery, (config.legendposition == 'right' || config.legendposition == 'left') ? centerx : centery, lastend, lastend +
(Math.PI * 2 * (config.data[0].values[i].Y / totalVal)), false);
c.lineTo(centerx, centery);
c.fill();
c.fillStyle = config.pielabelcolor;
c.lineWidth = config.pieborderWidth;
c.strokeStyle = config.pieborderColor;
c.stroke();
if (config.showpielables) {
c.save();
c.translate(centerx, centery);
c.rotate(lastend - 0.20 +
(Math.PI * 2 * (config.data[0].values[i].Y / totalVal)));
var dx = Math.floor(centerx * 0.5) + 40;
var dy = Math.floor(centery * 0.05);
c.textAlign = "right";
var fontArgs = c.font.split(' ');
c.font = config.piefontsize + ' ' + fontArgs[fontArgs.length - 1];
c.fillText(config.data[0].values[i].X, dx, dy);
c.restore();
c.save();
c.fillStyle = config.pielabelpercentcolor;
c.translate(centerx, centery);
c.rotate(lastend - 0.15 +
(Math.PI * 2 * (config.data[0].values[i].Y / totalVal)));
var dx = Math.floor(centerx * 0.5) + 90;
var dy = Math.floor(centery * 0.05);
c.textAlign = "right";
var fontArgs = c.font.split(' ');
c.font = config.piefontsize + ' ' + fontArgs[fontArgs.length - 1];
c.fillText(Math.round((config.data[0].values[i].Y / totalVal) * 100) + "%", dx, dy);
c.restore();
}
lastend += Math.PI * 2 * (config.data[0].values[i].Y / totalVal);
}
var canvasOffset = $(graph).offset();
var offsetX = canvasOffset.left;
var offsetY = canvasOffset.top;
},
drawBar: function (c, graph) {
var that = this,
config = this.options;
for (var i = 0; i < config.data[0].values.length; i++) {
var randomcolor;
c.strokeStyle = config.data[0].linecolor == "Random" ? config.data[0].values[i].color = randomcolor = that.getRandomColor() : config.data[0].linecolor;
c.fillStyle = config.data[0].linecolor == "Random" ? randomcolor : config.data[0].linecolor;
c.beginPath();
c.rect(that.pixelX(i, 0) - config.yPadding / 4, that.pixelY(config.data[0].values[i].Y), config.yPadding / 2, graph.height() - that.pixelY(config.data[0].values[i].Y) - config.xPadding + 8);
c.closePath();
c.stroke();
c.fill();
c.textAlign = "left";
c.fillStyle = "#000";
c.fillText(config.data[0].values[i].Y, that.pixelX(i, 0) - config.yPadding / 4, that.pixelY(config.data[0].values[i].Y) + 7, 200);
}
},
drawStacked: function (c, graph) {
var that = this,
config = this.options;
for (var i = 0; i < config.data.length; i++) {
for (var j = 0; j < config.data[i].values.length; j++) {
var randomcolor;
c.strokeStyle = config.data[i].linecolor == "Random" ? config.data[i].values[j].color = randomcolor = that.getRandomColor() : config.data[i].linecolor;
c.fillStyle = config.data[i].linecolor == "Random" ? randomcolor : config.data[i].linecolor;
c.beginPath();
c.rect(that.pixelX(j, 0) - config.yPadding / 4, that.pixelY(config.data[i].values[j].Y), config.yPadding / 2, graph.height() - that.pixelY(config.data[i].values[j].Y) - config.xPadding + 8);
c.closePath();
c.stroke();
c.fill();
c.textAlign = "left";
c.fillStyle = "#000";
c.fillText(config.data[i].values[j].Y, that.pixelX(j, 0) - config.yPadding / 4, that.pixelY(config.data[i].values[j].Y) + 7, 200);
}
}
},
drawHybrid: function (c, graph) {
var that = this,
config = this.options;
var randomcolor;
c.strokeStyle = config.data[0].linecolor == "Random" ? randomcolor = that.getRandomColor() : config.data[0].linecolor;
c.beginPath();
c.moveTo(that.pixelX(0, 0), that.pixelY(config.data[0].values[0].Y));
for (var j = 1; j < config.data[0].values.length; j++) {
c.lineTo(that.pixelX(j, 0), that.pixelY(config.data[0].values[j].Y));
}
c.stroke();
c.fillStyle = config.data[0].linecolor == "Random" ? randomcolor : config.data[0].linecolor;
for (var j = 0; j < config.data[0].values.length; j++) {
c.beginPath();
c.arc(that.pixelX(j, 0), that.pixelY(config.data[0].values[j].Y), 4, 0, Math.PI * 2, true);
c.fill();
}
},
drawLineAreaScatteredHybridCharts: function (c, graph) {
var that = this,
config = this.options;
var tipCanvas = $(that.element).append("<canvas id='tip'></canvas><div class='down-triangle'></div>").find("#tip").attr('width', config.toolwidth).attr('height', config.toolheight);
var tipCtx = tipCanvas[0].getContext("2d");
var highlighter = $(that.element).append("<canvas id='highlighter'></canvas>").find('#highlighter').attr('width', "0").attr('height', "0");
var higlightctx = highlighter[0].getContext("2d");
var tipbaloontip = $(that.element).find('.down-triangle');
var canvasOffset = $(graph).offset();
var offsetX = canvasOffset.left;
var offsetY = canvasOffset.top;
$(graph[0]).on("mousemove", function (e) {
drawToolTiponHover(e);
});
for (var i = 0; i < config.data.length; i++) {
c.strokeStyle = config.data[i].linecolor == "Random" ? config.data[i].Randomlinecolor = that.getRandomColor() : config.data[i].linecolor;
c.beginPath();
c.moveTo(that.pixelX(0, i), that.pixelY(config.data[i].values[0].Y));
if (config.ChartType !== "Scattered" && config.ChartType !== "Hybrid") {
for (var j = 1; j < config.data[i].values.length; j++) {
c.lineTo(that.pixelX(j, i), that.pixelY(config.data[i].values[j].Y));
}
c.stroke();
}
c.fillStyle = config.data[i].linecolor == "Random" ? config.data[i].Randomlinecolor : config.data[i].linecolor;
if (config.ChartType == "Area") {
c.lineTo(that.pixelX(config.data[i].values.length - 1, i), that.pixelY(0));
c.lineTo(that.pixelX(0, 0), that.pixelY(0));
c.stroke();
c.fill();
}
if (config.ChartType == "Line" || config.ChartType == "Scattered" || config.ChartType == "StackedHybrid") {
for (var j = 0; j < config.data[i].values.length; j++) {
c.beginPath();
c.arc(that.pixelX(j, i), that.pixelY(config.data[i].values[j].Y), 4, 0, Math.PI * 2, true);
c.fill();
}
}
}
var linepoints = [];
for (var i = 0; i < config.data.length; i++) {
for (var j = 0; j < config.data[i].values.length; j++) {
linepoints.push({
x: that.pixelX(j, i),
y: that.pixelY(config.data[i].values[j].Y),
r: 4,
rXr: 16,
tip: config.data[i].values[j].Y,
color: config.data[i].linecolor == "Random" ? config.data[i].Randomlinecolor : config.data[i].linecolor
});
}
}
function drawToolTiponHover(e) {
mouseX = parseInt(e.pageX - offsetX);
mouseY = parseInt(e.pageY - offsetY);
var hit = false;
for (var i = 0; i < linepoints.length; i++) {
var dot = linepoints[i];
var dx = mouseX - dot.x;
var dy = mouseY - dot.y;
if (dx * dx + dy * dy < dot.rXr) {
tipCanvas[0].style.left = (dot.x - (tipCanvas[0].width / 2)) - 3 + "px";
tipCanvas[0].style.top = (dot.y - 21 - tipCanvas[0].height) + config.topmargin + "px";
tipCtx.clearRect(0, 0, tipCanvas[0].width, tipCanvas[0].height);
tipCtx.fillText(dot.tip, 5, 15);
tipbaloontip[0].style.left = (dot.x) - 7 + "px";
tipbaloontip[0].style.top = (dot.y + config.topmargin) - 19 + "px";
if (config.ChartType == "Line" || config.ChartType == "Scattered" || config.ChartType == "Hybrid" || config.ChartType == "StackedHybrid") {
highlighter[0].style.left = (dot.x) - 9 + "px";
highlighter[0].style.top = (dot.y + config.topmargin) - 9 + "px";
}
higlightctx.clearRect(0, 0, highlighter.width(), highlighter.height());
higlightctx.strokeStyle = dot.color;
higlightctx.beginPath();
higlightctx.arc(9, 9, 7, 0, 2 * Math.PI);
higlightctx.lineWidth = 2;
higlightctx.stroke();
hit = true;
}
}
if (!hit) {
tipCanvas[0].style.left = "-400px";
highlighter[0].style.left = "-400px";
tipbaloontip[0].style.left = "-400px";
}
}
},
drawLegends: function (graph) {
var that = this,
config = this.options;
if (config.ChartType == "Line" || config.ChartType == "Area" || config.ChartType == "Scattered" || config.ChartType == "Stacked" || config.ChartType == "StackedHybrid") {
var _legends = $("<div class='simple-chart-legends' />", { id: "legendsdiv" }).css({
width: (config.legendposition == 'right' || config.legendposition == 'left') ? (config.legendsize - 5) : graph.width(),
height: (config.legendposition == 'top' || config.legendposition == 'bottom') ? (config.legendsize - 5) : graph.height(),
float: (config.legendposition == 'right' || config.legendposition == 'left') ? 'left' : ''
}).appendTo($(that.element));
var _ul = $(_legends).append("<span>" + config.LegendTitle + "</span>").append("<ul />").find("ul")
for (var i = 0; i < config.data.length; i++) {
$("<li />", { class: "legendsli" }).append("<span />").find('span').addClass("legendindicator").append('<span class="line" style="background: ' + (config.data[i].linecolor == "Random" ? config.data[i].Randomlinecolor : config.data[i].linecolor) + '"></span><span class="circle" style="background: ' + (config.data[i].linecolor == "Random" ? config.data[i].Randomlinecolor : config.data[i].linecolor) + '"></span>').parent().append("<span>" + config.data[i].title + "</span>").appendTo(_ul);
}
if (config.legendposition == 'top' || config.legendposition == 'left') {
$(_legends).insertBefore($(that.element).find('.SimpleChartcanvas'));
}
if (config.legendposition == 'right' || config.legendposition == 'left') {
$(_legends).addClass('vertical')
}
else {
$(_legends).addClass('horizontal');
}
}
if (config.ChartType == "Bar" || config.ChartType == "Hybrid" || config.ChartType == "Pie") {
var _legends = $("<div class='simple-chart-legends' />", { id: "legendsdiv" }).css({
width: (config.legendposition == 'right' || config.legendposition == 'left') ? (config.legendsize - 5) : graph.width(),
height: (config.legendposition == 'top' || config.legendposition == 'bottom') ? (config.legendsize - 5) : graph.height(),
float: (config.legendposition == 'right' || config.legendposition == 'left') ? 'left' : ''
}).appendTo($(that.element));
var _ul = $(_legends).append("<span>" + config.LegendTitle + "</span>").append("<ul />").find("ul")
for (var i = 0; i < config.data[0].values.length; i++) {
$("<li />", { class: "legendsli" }).append("<span />").find('span').addClass("legendindicator").append('<span class="line" style="background: ' + (config.data[0].linecolor == "Random" ? config.data[0].values[i].color : config.data[0].linecolor) + '"></span><span class="circle" style="background: ' + (config.data[0].linecolor == "Random" ? config.data[0].values[i].color : config.data[0].linecolor) + '"></span>').parent().append("<span>" + config.data[0].values[i].X + "</span><span class='legendvalue'>" + (config.ChartType == 'Pie' ? config.data[0].values[i].Y : '') + "</span>").appendTo(_ul);
}
if (config.legendposition == 'top' || config.legendposition == 'left') {
$(_legends).insertBefore($(that.element).find('.SimpleChartcanvas'));
}
if (config.legendposition == 'right' || config.legendposition == 'left') {
$(_legends).addClass('vertical')
}
else {
$(_legends).addClass('horizontal');
}
}
}
}
$.fn[pluginName] = function (options) {
if (typeof options === "string") {
var args = Array.prototype.slice.call(arguments, 1);
this.each(function () {
var plugin = $.data(this, 'plugin_' + pluginName);
if (plugin[options]) {
plugin[options].apply(plugin, args);
} else {
plugin['options'][options] = args[0];
}
});
} else {
return this.each(function () {
if (!$.data(this, 'plugin_' + pluginName)) {
$.data(this, 'plugin_' + pluginName, new Plugin(this, options));
}
});
}
}
})(jQuery, window, document, undefined);
</script>
@endsection