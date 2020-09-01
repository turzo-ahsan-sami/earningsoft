<style type="text/css">
	#order-details-content label {
		font-weight: normal;
	}
	.modal-dialog {
		width: 50%
	}
	table {
	    border-collapse: collapse;
	    border-spacing: 0;
	}
	
	
	#purchDetailsModel .table-bordered > thead > tr > th, 
	#purchDetailsModel .table-bordered > tbody > tr > th, 
	#purchDetailsModel .table-bordered > tfoot > tr > th, 
	#purchDetailsModel .table-bordered > thead > tr > td, 
	#purchDetailsModel .table-bordered > tbody > tr > td, 
	#purchDetailsModel .table-bordered > tfoot > tr > td {
	    line-height: 1.42857;
    	padding: 8px;
    	vertical-align: middle;
    	font-size: 12px;
    	text-align: center;
	}
	a.list-group-item.active, 
	a.list-group-item.active:hover, 
	a.list-group-item.active:focus {
	    background-color: #303641!important;
	    background-image: none;
	    border-color: #303641;
	    color: #ffffff;
	    z-index: 2;
	    border-top-left-radius: 3px;
    	border-top-right-radius: 3px;
	}
	input.purchasedProductQuantity,
	input.receivedProductQuantity,
	input.remainingProductQuantity,
	input.orderedProductQuantity,
	input.totalOrderedProductQuantity,
	input.totalReceivedProductQuantity,
	input.totalPurchasedProductQuantity,
	input.totalRemainingProductQuantity,
	input.productUnitPrice {
		border: none!important;
	    padding: 3px 10px;
	    text-align: center;
	    width: 60px;
	}
	input.productUnitPrice {
		width: 80px !important;
	}
	#swhoAppendRows thead tr th{
		background-color:  #FAF0E6 !important;
		color: #949494 !important;
	}
	#swhoAppendRows tr td{
		color: #191919 !important;
	}
	p { 
		margin:0 
	}

	/* .first tr td{
		width:420px;
	} */

</style>

<div id="purchDetailsModel" class="modal fade" style="margin-top:2%">
  <div class="modal-dialog">
	<div class="modal-content">
		<div style="text-align: center;" class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="useDetailsHead">Purchase Details</h4>
		<div style="padding-top: 10px;">
		  <table style="width:100%; padding-top: 20px; padding-left:67px;padding-right:67px;">
		
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;">Bill No</td>
		            <td>:</td>
		            <td style="font-size:12px; width:350px; text-align: left"><span id="billNoHead"></span></td>
		            <td style="width:40px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;">Bill Date</td>
		             <td>:</td>
		            <td style="font-size:12px; width:100px; text-align:right;"><span id="billDateHead"></span></td> 
		        </tr>
			
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;">Order No</td>
		            <td>:</td>
		            <td style="font-size:12px; width:350px; text-align: left"><span id="orderNoHead"></span></td>
		            <td style="width:40px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;">Order Date</td>
		             <td>:</td>
		            <td style="font-size:12px; width:100px; text-align:right;"><span id="orderDateHead"></span></td> 
		        </tr>
			
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;">Project Name</td>
		            <td>:</td>
		            <td style="font-size:12px; width:350px; text-align: left"><span id="projectHead"></span></td>
		            <td style="width:40px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;"><span id="project">Project Type</span></td>
		             <td><span id="projectView">:</span></td>
		            <td style="font-size:12px; width:100px; text-align:right;"><span id="projectTypeHead"></span></td> 
		        </tr>
				<tr>

		           <td style="text-align:left; font-size:14px;">Address<span style="padding-left:58px;">:</span><span id="SupplierAddressHead" style="font-size:12px; padding-left:4px; text-align: left;"></span> </td>
		                           	
		        </tr>
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;">Chalan No</td>
		            <td>:</td>
		            <td style="font-size:12px; width:350px; text-align: left"><span id="chalanNoHead"></span></td>
		            <td style="width:40px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;">Chalan Date</td>
		             <td>:</td>
		            <td style="font-size:12px; width:100px; text-align:right;"><span id="chalanDateHead"></span></td> 
		        </tr>
		
		 </table>	
		</div>
	</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<a class="list-group-item active">Bill No: <span id="showPurbillNo"></span></a>
						<table class="table table-bordered responsive order-details-table" border="0" cellpadding="0" cellspacing="0" width="100%" id="swhoAppendRows">
						       <thead>
						    	
									    <tr>
								        	
									        <th rowspan="2" width="70">SL No.</th>
									        <th rowspan="2" style="text-align: left;">Product Name</th>
									        <th colspan="5" width="80" style="border-bottom: none;">Purchase Information
									        </th>
									    </tr>
									    <tr>
									    	<th>
									    		<span style="background-color: white; padding: 4px 15px;">Qty</span>
									    	</th>
									        <th>
									        	<span style="background-color: white; padding:4px 3px">Unit Price</span>
									        </th>
									        <th>
									        	<span style="background-color: white; padding: 4px 1px;">Total Price</span>
									        </th>
									       
									    </tr>
						       </thead>
							    <tbody id="useTabelTbody">
							     	
							     
							    </tbody>
							  	<tfoot>
									    <tr>
									    	<td colspan="2" style="text-align: right">Total</td>
									        
							                <td>
							                	<input class="totalReceivedProductQuantity" type="text" value="" readonly="readonly" id="totalQtyDetails"/>
							                </td>
							                <td></td>
							                <td>
									        	<input class="totalOrderedProductQuantity" type="text" value="" readonly="readonly" id="totalAmountDetails"/>
									        </td>
							                
									    </tr>
							    </tfoot>
							</table>
				</div>
			</div>
		</div>

	
      <div id="tableDiv" style=" overflow: visible !important;">
       <div class="panel-body panelBodyView">
                      <div id="printingContent">

                        <div style="display: none;text-align: center;" id="hiddenTitle">
                         @php
			                $logo = DB::table('gnr_company')->select('image')->first(); 
		                 @endphp	
                           <h3 style="text-align: center;padding: 0px;margin: 0px;"><img src="{{asset('images/'.$logo->image)}}" alt="Logo" style="height:100px; width: 150px;" > </h3>

                           <table style="width:100%; padding-top: 20px; padding-left:67px;padding-right:67px;" >

		                           	<tr style="padding-bottom:4px;">
		                           		<td style="text-align:center;padding-left: 2px; font-size:18px;">MATERIAL RECIVING REPORT (MRR)</td>
		                           		
		                           	</tr>
		                           	<tr style="display:inline-block; height: 8px;">
		                           		<td class="first" style="font-size:16px; text-align: left;width:100px;">Bill No</td>
		                           		<td>:</td>
		                           		<td style="font-size:14px; width:200px; text-align: left"><span id="showbillNo"></span></td>
		                           		<td style="width:5px;"></td>
		                           		 <td style="font-size:16px;text-align:left;width:90px;">Bill Date</td>
		                           		  <td>:</td>
		                           		 <td style="font-size:14px; width:100px; text-align:right;"><span id="showbilldate"></span></td>
		                           	</tr>


		                            <tr style="display:inline-block; height: 8px;">
		                           		<td style="font-size:16px; width:100px;text-align: left">Order No</td>
		                           		<td style="width:0px">:</td>
		                           		<td style="font-size:14px; width:200px; text-align: left;"><span id="showOrderNo"></span></td>
		                           		<td style="width:5px;"></td>
		                           		 <td style="font-size:16px;text-align:left;width:90px;">Order Date</td>
		                           		   <td>:</td>
		                           		<td style="font-size:14px; width:100px; text-align:right;"><span id="showOrderDate"></span></td> 
		                           	</tr>

		                           	<tr style="display:inline-block; height: 8px;">
		                           		<td style="font-size:16px; width:100px;text-align: left">Project Name</td>
		                           		<td>:</td>
		                           		<td style="font-size:14px; text-align:left; width:200px;"><span id="showProject"></span></td>
		                           		<td style="width: 5px;"></td>
		                           		
		                           		 <td style="font-size:16px;text-align:left; width:90px;">Location</td>
		                           		 <td>:</td>
		                           		<td style="font-size:14px; width:100px; text-align: right;"><span id="showLocation"></span></td>
		                           	</tr>

		                           	<tr style="display:inline-block; height: 8px;">
		                           		<td style="font-size:16px; width:100px;text-align: left">Supplier Name </td>
		                           		<td>:</td>
		                           		<td style="font-size:14px; width:200px; text-align: left;"><span id="showSupplier"></span></td>
		                           		<td style="width:5px;"></td>
		                           		 <td style="font-size:16px;text-align:left;width:90px;">Mobile</td>
		                           		 <td>:</td>
		                           		<td style="font-size:14px; width:100px; text-align:right;"><span id="showMobile"></span></td>
		                           	</tr>

		                           	<tr>

		                           	  <td style="text-align:left; height: 8px; font-size:16px;">Address<span style="padding-left:56px;">:</span><span id="showSupplierAddress" style="font-size:14px; padding-left:4px;"></span> </td>
		                           	
		                            </tr>

                           	 

		                            <tr style="display:inline-block; height: 8px;">
		                           		<td style="font-size:16px; width:100px; text-align: left">Chalan No</td>
		                           		<td >:</td>
		                           		<td style="font-size:14px; width:200px; text-align: left;"><span id="showChalanName"></span></td>
		                           		<td style="width:5px;"></td>
		                           		 <td style="font-size:16px;text-align:left;width:90px;">Chalan Date</td>
		                           		 <td>:</td>
		                           		<td style="font-size:14px; width:100px; text-align:right;"><span id="showChalanDate"></span></td> 
		                           	</tr>

                        
                              </table> 
				        <div style="padding-left: 70px;padding-right: 70px; ">
                          <table id="purchasePrint"  style="color:black; font-size:16px;border-collapse:collapse; padding-top:100px !important;" border= "1px solid black;"  cellpadding="0" cellspacing="0">


	                            <thead>
	                            		<th style="font-size: 16px;">SL#</th>
	                            		<th style="font-size: 16px;">Product Name</th>
	                            		<th style="font-size: 16px; width:30px;">Quantity</th>
	                            		<th style="font-size: 16px;">Unit Price</th>
	                            		<th style="font-size: 16px;">Total Price</th>
	                            </thead>

	                            <tbody>

								
							

	                            </tbody>

	                            <tfoot >
	                            		<tr>
	                            	       <td colspan="2" style="text-align:center; font-weight: bold;">Total</td>
	                            							        
	                            		   <td style="text-align:center;"><span id="totalQtyPrint" style=" font-weight: bold;"></span>
	                            	        </td>
	                            					                	
	                            	        <td></td>
	                            
	                                        <td style="font-weight: bold; text-align:right; padding-right:5px;"><span id="totalAmountPrint"></span>
	                                        </td>
	                                    </tr>
	                            </tfoot> 

                          </table>
				
            <!-- Start  Footer  -->
                        <div class='row' style='font-size:16px;text-align: left; margin-top:50px;'>Receive By <span style='width: 36%; padding-left:3px;'>:</span><span  id='createPerson' style="padding-left:4px;"></span> </div> <div class='row' style='font-size:16px;text-align: left;'>Designation<span style='width:40%; padding-left:4px;'>:</span><span id='createPersonDeg' style="padding-left:4px;"></span></div> <div class='row' style='font-size:16px; text-align:left;'>Emp.Id <span style='width:60%; padding-left:30px;'>:</span><span id='createPersonId' style="padding-left:4px;"></span><span style="padding-left: 183px;">HR & Adminitration</span> <span style='display:inline-block; width:36%;'></span></span></div>

            <!-- end  Footer  -->

               </div>
            </div>
       </div>
   </div>
</div> 

   <div id="hiddenInfo" style="display: none;"></div>
   <br>
                                           
          <div>
                <div class="modal-footer">
					<input id="modalDeliveryNo" type="hidden" value="" />
					<input id="modalOrderNo" type="hidden" value=""/>
					<input id="modalrequisitionNo" type="hidden" value="" />

				    <button type="button" class="btn btn-primary" data-dismiss="modal" id="print">Print</button>
				    <button type="button" class="btn btn-primary" data-dismiss="modal" id="useDetailsDissmiss">Close</button>
			    </div>
		  </div>
	 </div>
   </div>
</div>