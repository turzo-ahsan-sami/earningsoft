@php
// echo '<pre>';
// print_r($tab2ValuesArr);
// echo '</pre>';
@endphp
<div class="col-xs-12">
	<div id="at_a_glance">
		<div class="row glance_container">
			{{-- <div class="col-md-12 border_contain"> --}}
			<div class="col-md-12">
				<div style="" class="listing_big_image blue_bg animated fadeInLeft">
	            	<img src="{{ asset('software/images/dashboards/accDashboard/surplus.png') }}" border="0" width="50px" height="50px">
	            	<span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Surplus</span>
	            </div>
	    		<div style="width: 85% !important; text-align: left;" class="listing_container animated fadeInDown">
	    			<div class="row">
		    			<div class="col-xs-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month :</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year :</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>
		    				</ul>
		    			</div>
		    			<div class="col-xs-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Current Month :</span>
			            			<span class="listing_result">3,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span class="listing_result">267,655,286</span>
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
				<div style="" class="listing_big_image blue_bg animated fadeInDown">
	            	<img src="{{asset('software/images/dashboards/accDashboard/openingBalance.png')}}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-weight: bold; font-size: 11px;">Cash & Bank</span>
	            </div>
	    		<div style="width: 85% !important; text-align: left;" class="listing_container animated fadeInUp">
	    			<div class="row">
		    			<div class="col-xs-6">
		    				<ul class="orange">
		    					<li>
		            				<span class="listing_head">Cash in Hand :</span>
		            				<span class="listing_result">4,333,774</span>
		            			</li>
			            		<li>
		            				<span class="listing_head">Total Balance :</span>
		            				<span class="listing_result">136,572,822</span>
		            			</li>
		    				</ul>
		    			</div>
		    			<div class="col-xs-6">
		    				<ul class="orange">
		    					<li>
		            				<span class="listing_head">Cash at Bank :</span>
		            				<span class="listing_result">140,906,597</span>
		            			</li>
		    				</ul>
		    			</div>
	    			</div>
    			</div>
			</div>
		</div>

	</div>		{{-- at_a_glance Div --}}
</div>
