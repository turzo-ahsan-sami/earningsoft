<style type="text/css">
	.icon-cust{
	background-color:teal;
	color: #ffffff;
	border-radius: 50px;
	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	-o-border-radius: 50px;
	-ms-border-radius: 50px;
	font-size: 25px;
	margin-top: 12px !important;
}	
.icon-sup{
	background-color:pink;
	color: #FF5CCD;
	border-radius: 50px;
	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	-o-border-radius: 50px;
	-ms-border-radius: 50px;
	font-size: 25px;
	margin-top: 12px !important;
}
.sale{
	background-color:green;
	color: #ffffff;
	border-radius: 50px;
	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	-o-border-radius: 50px;
	-ms-border-radius: 50px;
	font-size: 25px;
}
.purchase{
	background-color:blue;
	color: #ffffff;
	border-radius: 50px;
	-webkit-border-radius: 50px;
	-moz-border-radius: 50px;
	-o-border-radius: 50px;
	-ms-border-radius: 50px;
	font-size: 25px;
}

.r4_counter_box {
    min-height: 78px !important;
    background: #ffffff !important;
    padding: 15px !important;
    box-shadow:  1px 1px 4px rgba(0, 0, 0, 0.21);
}
.customize{
	margin-bottom: 15px;	
}

.colCus{
	padding-left:0px !important;
}
</style>
<div class="sales-tab" style="height: 360px;">
<div class="col-md-6 content-top-1 card">
	<div class="agileinfo-cdr">
		<div class="card-header">
			<h3>Sales</h3>
		</div>
		<div id="Linegraph"  class="Linegraph">
		</div>
	</div>
</div>
<div class="col-md-3">
	<div class="customize">
		<div class="col-md-12 colCus">
			<div class="r4_counter_box">
				<i class="pull-left fa fa-users  icon-cust"></i>
				<div class="stats">
					@php
						$branchInfo = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->where('companyId',Auth::user()->company_id_fk)->first();
						if($branchInfo->branchCode == 0){
							$customers = DB::table('pos_customer')->where('companyId',Auth::user()->company_id_fk)->get();
						}else{
							$customers = DB::table('pos_customer')/*->where('branchId',Auth::user()->branchId)*/->where('companyId',Auth::user()->company_id_fk)->get();
						}
						
					@endphp
					Total Customers <span><strong>{{count($customers)}}</strong></span>
				</div>
			</div>
		</div>
		
		<div class="clearfix"> </div>
	</div>
	<div class="customize">
		<div class="col-md-12 colCus">
			<div class="r4_counter_box">
				<i class="pull-left fa fa-building-o icon-sup"></i>
				<div class="stats">
					@php
						if($branchInfo->branchCode == 0){
							$suppliers = DB::table('pos_supplier')->where('companyId',Auth::user()->company_id_fk)->get();
						}else{
							$suppliers = DB::table('pos_supplier')/*->where('branchId',Auth::user()->branchId)*/->where('companyId',Auth::user()->company_id_fk)->get();
						}
					@endphp
					Total Suppliers <span><strong>{{count($suppliers)}}</strong></span>
				</div>
			</div>
		</div>
		
		<div class="clearfix"> </div>
	</div>
	<div class="customize">
		<div class="col-md-12 colCus">
			<div class="r4_counter_box">
				<i class="pull-left fa fa-industry user2 sale"></i>
				<div class="stats">
					@php
						if($branchInfo->branchCode == 0){
							$sales = DB::table('pos_sales')->where('companyId',Auth::user()->company_id_fk)->get();
						}else{
							$sales = DB::table('pos_sales')->where('branchId',Auth::user()->branchId)->where('companyId',Auth::user()->company_id_fk)->get();
						}
						
					@endphp
					Total Sales <span><strong>{{count($sales)}}</strong></span>
				</div>
			</div>
		</div>
		
		<div class="clearfix"> </div>
	</div>
	<div class="customize">
		<div class="col-md-12 colCus">
			<div class="r4_counter_box">
				<i class="pull-left fa fa-money purchase"></i>
				<div class="stats">
					@php
						if($branchInfo->branchCode == 0){
							$purchases = DB::table('pos_purchase')->where('companyId',Auth::user()->company_id_fk)->get();
						}else{
							$purchases = DB::table('pos_purchase')->where('branchId',Auth::user()->branchId)->where('companyId',Auth::user()->company_id_fk)->get();
						}
						
					@endphp
					Total Purchase <span><strong>{{count($purchases)}}</strong></span>
				</div>
			</div>
		</div>
		
		<div class="clearfix"> </div>
	</div>
</div>
</div>
<script>
	var graphdata1 = {
	linecolor: "#CCA300",
	title: "Monday",
	values: [
	{ X: "6:00", Y: 10.00 },
	{ X: "7:00", Y: 20.00 },
	{ X: "8:00", Y: 40.00 },
	{ X: "9:00", Y: 34.00 },
	{ X: "10:00", Y: 40.25 },
	{ X: "11:00", Y: 28.56 },
	{ X: "12:00", Y: 18.57 },
	{ X: "13:00", Y: 34.00 },
	{ X: "14:00", Y: 40.89 },
	{ X: "15:00", Y: 12.57 },
	{ X: "16:00", Y: 28.24 },
	{ X: "17:00", Y: 18.00 },
	{ X: "18:00", Y: 34.24 },
	{ X: "19:00", Y: 40.58 },
	{ X: "20:00", Y: 12.54 },
	{ X: "21:00", Y: 28.00 },
	{ X: "22:00", Y: 18.00 },
	{ X: "23:00", Y: 34.89 },
	{ X: "0:00", Y: 40.26 },
	{ X: "1:00", Y: 28.89 },
	{ X: "2:00", Y: 18.87 },
	{ X: "3:00", Y: 34.00 },
	{ X: "4:00", Y: 40.00 }
	]
	};
	var graphdata2 = {
	linecolor: "#00CC66",
	title: "Tuesday",
	values: [
	{ X: "6:00", Y: 100.00 },
	{ X: "7:00", Y: 120.00 },
	{ X: "8:00", Y: 140.00 },
	{ X: "9:00", Y: 134.00 },
	{ X: "10:00", Y: 140.25 },
	{ X: "11:00", Y: 128.56 },
	{ X: "12:00", Y: 118.57 },
	{ X: "13:00", Y: 134.00 },
	{ X: "14:00", Y: 140.89 },
	{ X: "15:00", Y: 112.57 },
	{ X: "16:00", Y: 128.24 },
	{ X: "17:00", Y: 118.00 },
	{ X: "18:00", Y: 134.24 },
	{ X: "19:00", Y: 140.58 },
	{ X: "20:00", Y: 112.54 },
	{ X: "21:00", Y: 128.00 },
	{ X: "22:00", Y: 118.00 },
	{ X: "23:00", Y: 134.89 },
	{ X: "0:00", Y: 140.26 },
	{ X: "1:00", Y: 128.89 },
	{ X: "2:00", Y: 118.87 },
	{ X: "3:00", Y: 134.00 },
	{ X: "4:00", Y: 180.00 }
	]
	};
	var graphdata3 = {
	linecolor: "#FF99CC",
	title: "Wednesday",
	values: [
	{ X: "6:00", Y: 230.00 },
	{ X: "7:00", Y: 210.00 },
	{ X: "8:00", Y: 214.00 },
	{ X: "9:00", Y: 234.00 },
	{ X: "10:00", Y: 247.25 },
	{ X: "11:00", Y: 218.56 },
	{ X: "12:00", Y: 268.57 },
	{ X: "13:00", Y: 274.00 },
	{ X: "14:00", Y: 280.89 },
	{ X: "15:00", Y: 242.57 },
	{ X: "16:00", Y: 298.24 },
	{ X: "17:00", Y: 208.00 },
	{ X: "18:00", Y: 214.24 },
	{ X: "19:00", Y: 214.58 },
	{ X: "20:00", Y: 211.54 },
	{ X: "21:00", Y: 248.00 },
	{ X: "22:00", Y: 258.00 },
	{ X: "23:00", Y: 234.89 },
	{ X: "0:00", Y: 210.26 },
	{ X: "1:00", Y: 248.89 },
	{ X: "2:00", Y: 238.87 },
	{ X: "3:00", Y: 264.00 },
	{ X: "4:00", Y: 270.00 }
	]
	};
	var graphdata4 = {
	linecolor: "Random",
	title: "Thursday",
	values: [
	{ X: "6:00", Y: 300.00 },
	{ X: "7:00", Y: 410.98 },
	{ X: "8:00", Y: 310.00 },
	{ X: "9:00", Y: 314.00 },
	{ X: "10:00", Y: 310.25 },
	{ X: "11:00", Y: 318.56 },
	{ X: "12:00", Y: 318.57 },
	{ X: "13:00", Y: 314.00 },
	{ X: "14:00", Y: 310.89 },
	{ X: "15:00", Y: 512.57 },
	{ X: "16:00", Y: 318.24 },
	{ X: "17:00", Y: 318.00 },
	{ X: "18:00", Y: 314.24 },
	{ X: "19:00", Y: 310.58 },
	{ X: "20:00", Y: 312.54 },
	{ X: "21:00", Y: 318.00 },
	{ X: "22:00", Y: 318.00 },
	{ X: "23:00", Y: 314.89 },
	{ X: "0:00", Y: 310.26 },
	{ X: "1:00", Y: 318.89 },
	{ X: "2:00", Y: 518.87 },
	{ X: "3:00", Y: 314.00 },
	{ X: "4:00", Y: 310.00 }
	]
	};
	var Piedata = {
	linecolor: "Random",
	title: "Profit",
	values: [
	{ X: "Monday", Y: 50.00 },
	{ X: "Tuesday", Y: 110.98 },
	{ X: "Wednesday", Y: 70.00 },
	{ X: "Thursday", Y: 204.00 },
	{ X: "Friday", Y: 80.25 },
	{ X: "Saturday", Y: 38.56 },
	{ X: "Sunday", Y: 98.57 }
	]
	};
	$(function () {
	$("#Bargraph").SimpleChart({
	ChartType: "Bar",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	data: [graphdata4, graphdata3, graphdata2, graphdata1],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	$("#sltchartype").on('change', function () {
	$("#Bargraph").SimpleChart('ChartType', $(this).val());
	$("#Bargraph").SimpleChart('reload', 'true');
	});
	$("#Hybridgraph").SimpleChart({
	ChartType: "Hybrid",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	data: [graphdata4],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	$(".Linegraph").SimpleChart({
	ChartType: "Line",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: false,
	data: [graphdata4, graphdata3, graphdata2, graphdata1],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Sales Profit',
	yaxislabel: 'Profit in $'
	});
	$("#Areagraph").SimpleChart({
	ChartType: "Area",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	data: [graphdata4, graphdata3, graphdata2, graphdata1],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	$("#Scatterredgraph").SimpleChart({
	ChartType: "Scattered",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	data: [graphdata4, graphdata3, graphdata2, graphdata1],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	$("#Piegraph").SimpleChart({
	ChartType: "Pie",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	showpielables: true,
	data: [Piedata],
	legendsize: "250",
	legendposition: 'right',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	$("#Stackedbargraph").SimpleChart({
	ChartType: "Stacked",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	data: [graphdata3, graphdata2, graphdata1],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	$("#StackedHybridbargraph").SimpleChart({
	ChartType: "StackedHybrid",
	toolwidth: "50",
	toolheight: "25",
	axiscolor: "#E6E6E6",
	textcolor: "#6E6E6E",
	showlegends: true,
	data: [graphdata3, graphdata2, graphdata1],
	legendsize: "140",
	legendposition: 'bottom',
	xaxislabel: 'Hours',
	title: 'Weekly Profit',
	yaxislabel: 'Profit in $'
	});
	});
</script>
<style type="text/css">
	.content-top-1 {
    padding: -10em;
    margin-bottom: 14px !important;
}
</style>