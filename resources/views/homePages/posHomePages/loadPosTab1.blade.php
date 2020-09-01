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

<style type="text/css">
	@media all and (min-width:992px) {	
	.listing_container{
		text-align: left;
		width: 90% !important;
	}
}
</style>
@php
// echo '<pre>';
// print_r($tab2ValuesArr);
// echo '</pre>';
@endphp
<div class="visible-md-block visible-lg-block">
<div class="col-xs-12">
	<div id="at_a_glance">
		<div class="row glance_container">
			{{-- <div class="col-md-12 border_contain"> --}}
			<div class="col-md-12">
				<div style="" class="listing_big_image blue_bg animated fadeInLeft">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/sale.png') }}" border="0" width="50px" height="50px">
	            	<span style="color:#4BB2DE;font-size: 9px;">Sales</span>
	            </div>
	    		<div style="" class="listing_container animated fadeInDown">
	    			<div class="row">
		    			<div class="col-md-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Sales:</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Sales:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>
		    				</ul>
		    			</div>
		    			<div class="col-md-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Current Month Sales:</span>
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
	            	<img src="{{ asset('software/images/dashboards/invDashboard/slesReturn.png') }}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-size: 9px;">Sales Return</span>
	            </div>
	    		<div class="listing_container animated fadeInUp">
	    			<div class="row">
		    			<div class="col-md-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Sales Return:</span>
			            			<span class="listing_result">40,500,320</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Sales Return:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>
		    				</ul>
		    			</div>
		    			<div class="col-md-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Current Month Sales Return:</span>
			            			<span class="listing_result">2,057,417</span>
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

		

	</div>		{{-- at_a_glance Div --}}
</div>

</div>
<!-- Mobile -->
    <div class="visible-xs-block visible-sm-block">

    	<div class="col-sm-12">
	<div id="at_a_glance">
		<div class="row glance_container">
			<div class="col-xs-3 col-sm-3">
				<div style="" class="listing_big_image blue_bg animated fadeInLeft">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/sale.png') }}" border="0" width="50px" height="50px">
	            	<span style="color:#4BB2DE;font-size: 9px;">Sales</span>
	            </div>
			</div>

			<div class="col-xs-9 col-sm-9">
					<div class="listing_container animated fadeInDown">
	    			
		    			
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Sales:</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Sales:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>

			            		<li>
			            			<span class="listing_head">Current Month Sales:</span>
			            			<span class="listing_result">3,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span>
		    				</ul>
		    			
		    	

		            
    			</div>
			</div>

			
		</div>





		<div class="row glance_container">
			<div class="col-xs-3 col-sm-3">
		<div style="" class="listing_big_image blue_bg animated fadeInDown">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/slesReturn.png') }}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-size: 9px;">Sales Return</span>
	            </div>
	        </div>

			<div class="col-xs-9 col-sm-9">
					<div class="listing_container animated fadeInDown">
	    			
		    			
		    			<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Sales Return:</span>
			            			<span class="listing_result">40,500,320</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Sales Return:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>

			            		<li>
			            			<span class="listing_head">Current Month Sales Return:</span>
			            			<span class="listing_result">2,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span class="listing_result">267,655,286</span>
			            		</li>
		    				</ul>
		    			
		    	

		            
    			</div>
			</div>

			
		</div>

		

	</div>		
</div>
    </div>
</br>
<div class="visible-md-block visible-lg-block">
<div class="col-md-12 ">
	<div id="at_a_glance">
		<div class="row glance_container">
			{{-- <div class="col-md-12 border_contain"> --}}
			<div class="col-md-12">
				<div style="" class="listing_big_image blue_bg animated fadeInDown">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/purchase.png') }}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-size: 9px;">Purchase</span>
	            </div>
	    		<div class="listing_container animated fadeInUp">
	    			<div class="row">
		    			<div class="col-md-6 col-sm-12">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Purchase:</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Purchase:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>
		    				</ul>
		    			</div>
		    			<div class="col-md-6 col-sm-12">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Current Month Purchase:</span>
			            			<span class="listing_result">3,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span class="listing_result">267,655,286</span>
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
				<div style="" class="listing_big_image blue_bg animated fadeInDown">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/use.png') }}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-size: 9px;">Purchase Return</span>
	            </div>
	    		<div  class="listing_container animated fadeInUp">
	    			<div class="row">
		    			<div class="col-md-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Purchase Return:</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Purchase Return:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>
		    				</ul>
		    			</div>
		    			<div class="col-md-6">
		    				<ul class="blue">
		    					<li>
			            			<span class="listing_head">Current Month Purchase Return:</span>
			            			<span class="listing_result">3,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span class="listing_result">267,655,286</span>
			            		</li>
		    				</ul>
		    			</div>
	    			</div>
    			</div>
    			</div>
			</div>

	</div>		{{-- at_a_glance Div --}}
</div>

</div>




<!-- Mobile -->
    <div class="visible-xs-block visible-sm-block">

    	<div class="col-sm-12">
	<div id="at_a_glance">
		<div class="row glance_container">
			<div class="col-xs-3 col-sm-3">
				<div style="" class="listing_big_image blue_bg animated fadeInDown">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/purchase.png') }}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-size: 9px;">Purchase</span>
	            </div>
			</div>

			<div class="col-xs-9 col-sm-9">
					<div class="listing_container animated fadeInDown">
	    			
		    			
		    			<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Purchase:</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Purchase:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Month Purchase:</span>
			            			<span class="listing_result">3,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span class="listing_result">267,655,286</span>
			            		</li>
		    				</ul>
		    	

		            
    			</div>
			</div>

			
		</div>





		<div class="row glance_container">
			<div class="col-xs-3 col-sm-3">
		<div style="" class="listing_big_image blue_bg animated fadeInDown">
	            	<img src="{{ asset('software/images/dashboards/invDashboard/use.png') }}" border="0" width="50px" height="50px">
					<span style="color:#4BB2DE;font-size: 9px;">Purchase Return</span>
	            </div>
	        </div>

			<div class="col-xs-9 col-sm-9">
					<div class="listing_container animated fadeInDown" style="height:110px;">
	    			
		    			
		    		<ul class="blue">
		    					<li>
			            			<span class="listing_head">Last Month Purchase Return:</span>
			            			<span class="listing_result">30,662,218</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Current Year Purchase Return:</span>
			            			<span class="listing_result">63,524,963</span>
			            		</li>

			            			<li>
			            			<span class="listing_head">Current Month Purchase Return:</span>
			            			<span class="listing_result">3,057,417</span>
			            		</li>
			            		<li>
			            			<span class="listing_head">Cumulative :</span>
			            			<span class="listing_result">267,655,286</span>
			            		</li>
		    				</ul>
		    			
		    	

		            
    			</div>
			</div>

			
		</div>

		

	</div>		
</div>
    </div>



