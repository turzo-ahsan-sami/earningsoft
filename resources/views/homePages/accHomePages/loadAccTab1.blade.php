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
    height: 66px !important;

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

<style type="text/css">
	@media all and (max-width: 768px) {	
		.listing_big_image{
			margin-bottom: 17px !important;
		    width: 88px;
		    height: 75px;
		    float: middle !important;
		    padding: 5px;
		    margin-top: 15px;
		
		}

		.listing_container ul > li {
   
           padding:0px;
   
         }

       .blue{
	     padding-left: 10px
       }

	

		.listing_container {
			text-align: left;
    		height: 100px;
             width: 98% !important;
		}
	    
}



</style>

<!-- <style type="text/css">
	@media all and (min-width:992px) {	
	.listing_container{
		text-align: left;
		width: 90% !important;
	}
}
</style> -->
<div class="visible-md-block visible-lg-block">
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

</div>
</div>




<!-- Mobile -->
    <div class="visible-xs-block visible-sm-block">
    	<div class="org_timeline">
	<p class="text-right">Last Update: {{ $lastUpdateFormatedTime }}</p>
</div>

    	<div class="col-sm-12">
	<div id="at_a_glance">
		<div class="row glance_container">
			<div class="col-xs-3 col-sm-3">
				<div style="" class="first_content_column listing_big_image blue_bg animated fadeInLeft">
					<img class="content_logo" src="{{ asset('software/images/dashboards/accDashboard/surplus.png') }}" border="0" width="50px" height="50px">
				
				</div>
			</div>

			<div class="col-xs-9 col-sm-9">
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
								<li class="li-left">
									<span class="listing_head">Last Month Surplus</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('previousMonthSurplus'), 2) }}</span>
								</li>
								<li class="li-left">
									<span class="listing_head">Cumulative Surplus Amount</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('cumulativeSurplus'), 2) }}</span>
								</li>
							</ul>
						</div>
						
					</div>
				</div>
			</div>

			
		</div>





		<div class="row glance_container">
			<div class="col-xs-3 col-sm-3">
		<div style="" class="second_content_column listing_big_image blue_bg animated fadeInDown">
					<img class="content_logo" src="{{asset('software/images/dashboards/accDashboard/openingBalance.png')}}" border="0" width="50px" height="50px">
				
				</div>
	        </div>

			<div class="col-xs-9 col-sm-9">
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
								<li class="li-left">
									<span class="listing_head">Current Bank Amount</span> <span class="text-bold">:&nbsp;&nbsp;</span>
									<span class="listing_result">{{ number_format($branchStatusInfos->sum('currentCashAndBank'), 2) }}</span>
								</li>
							</ul>
						</div>
						
					</div>
				</div>
			</div>

			
		</div>

		

	</div>		
</div>
    </div>

	