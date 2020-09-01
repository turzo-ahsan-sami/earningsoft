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

	input.totalOrderedProductQuantity,
	input.totalReceivedProductQuantity,
    input.totalPurchasedProductQuantity, 
	input.totalRemainingProductQuantity,
	input.productUnitPrice {
		/* border: none!important;
			    padding: 3px 10px;
			    text-align: center;
			    width:60px; */
	}
	input.productUnitPrice {
		/* width: 80px !important; */
	}
	#swhoAppendRows thead tr th{
		background-color:  #FAF0E6 !important;
		color: #949494 !important;
	}
	#swhoAppendRows tr td{
		color: #191919 !important;
	}

	 td #abcd{

		width:200px;
		text-align:left;
	  }

	p { 
		margin:0 
	}

	/* .first tr td{
		width:420px;
	} */

</style>

<div id="issueDetailsModel" class="modal fade" style="margin-top:2%">
  <div class="modal-dialog">
	<div class="modal-content">
		<div style="text-align: center;" class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="useDetailsHead">Issue Details</h4>
		<div style="padding-top: 10px;">

		<table style="width:100%; padding-top: 20px; padding-left:67px;padding-right:67px;">
		
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;"><span id="issuebillHeadHidee">Issue No</span></td>
		            <td><span id="issuebillHeadHide">:</span></td>
		            <td style="font-size:12px; width:300px; text-align: left"><span id="issueNoHead"></span></td>
		            <td style="width:2px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;">Issue date</td>
		             <td>:</td>
		            <td style="font-size:12px; width:188px; text-align:right;"><span id="issueDateHead"></span></td> 
		        </tr>
				
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;"><span id="issueOrderNoHide">Issue Order No</span></td>
		            <td id="coloneHideview">:</td>
		            <td style="font-size:12px; width:300px; text-align: left"><span id="issueOrderNoHead"></span></td>
		            <td style="width:2px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;"><span id="orderNoHide">Order No</span></td>
		             <td><span id="coloneHide">:</span></td>
		            <td style="font-size:12px; width:188px; text-align:right;"><span id="orderNoHead"></span></td> 
		        </tr>
				
				<tr style="display:inline-block;">
		            <td class="first" style="font-size:14px; text-align: left;width:110px;"><span id="projectNameViewHide">Project Name</span></td>
		            <td><span id="coloneViewHide">:</span></td>
		            <td style="font-size:12px; width:300px; text-align: left"><span id="projectHead"></span></td>
		            <td style="width:2px;"></td>
		            <td style="font-size:14px;text-align:left;width:90px;"><span>Branch Name</span></td>
		             <td>:</td>
		            <td style="font-size:12px; width:188px; text-align:right;"><span id="branchNameHead"></span></td> 
		        </tr>

		        <tr style="display:inline-block;">
		           
		            <td style="font-size:14px;text-align:left;width:108px;"><span id="projectType1"> Project Type</span></td>
		             <td><span id="projectView">:</span></td>
		            <td style="font-size:12px;  text-align:right;"><span id="projectTypeHead"></span></td> 
		        </tr>
		
		</table>	
	</div>
</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					
							<a class="list-group-item active" style="text-align: center;">Issue Information<span id="showPurbillNo"></span></a>
							<table class="table table-bordered responsive order-details-table" border="0" cellpadding="0" cellspacing="0" width="100%" id="swhoAppendRows">
						         <thead>
						    	
									    <tr>
								        	
									        <th rowspan="2" width="70">SL No.</th>
									        <th rowspan="2" style="text-align: left;">Product Name</th>
									        <th colspan="5" width="80" style="border-bottom: none;"></th>
									    </tr>
									    <tr>
									    	<th width="80">
									    		<span style="padding: 4px 15px;">Qty</span>
									    	</th>
									        <th width="80">
									        	<span style="padding:4px 3px">Unit Price</span>
									        </th>
									        <th width="80">
									        	<span style="padding: 4px 1px;">Total Price</span></th>
									       
									    </tr>
						         </thead>
							     <tbody>
							     	<!-- <td class='productName' style='text-align:left'id='rowAppendView'></td> -->
							     </tbody>
							  	 <tfoot>
								    <tr>
								    	<td colspan="2" style="text-align: right; font-weight:bold;">Total</td>
								        <td style="text-align:center; font-weight:bold;">
						                	<span id="totalQtyDetails"></span>
                                        </td>
						                <td></td>
						                <td style="text-align:center; font-weight:bold;">
								            <span id="totalAmountDetails"></span>
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
                           <h3 style="text-align: center;padding-top: 0px;margin: 0px;"><img src="{{asset('images/'.$logo->image)}}" alt="Logo" style="height:100px; width: 150px;" > </h3>

                          
                           <table style="width:100%;  padding-left:67px;padding-right:67px;" id='rowAppend' class="table table-bordered responsive order-details-table">

	                              	<tr style="padding-bottom:4px;">
	                           		      <td style="text-align:center;padding-left: 2px; font-size:18px;">MATERIAL ISSUE REPORT</td>
	                           	    </tr>
									
									<tr style="display:inline-block; height: 8px;">
							            <td style="width:110; font-size:16px; text-align:left;">Issue No</td>
							            <td>:</td>

							            <td style="font-size:14px; width:200px; text-align: left"><span id="issueNo"></span></td>
							            
							             <td style="width:15px;"></td>
							             
							             <td style="font-size:16px;text-align:left; width:80px;">Issue date</td>
							             
							              <td >:</td>
							             
							             <td style="font-size:14px; width:90px;text-align:right;"><span id="issueDate"></span></td>  
							        </tr>
									
									<tr style="display:inline-block;  height: 8px;">
							            <td style="width:110; font-size:16px; text-align:left;"><span id="issueOrderNoHidePrint">Issue Order No</span></td>

							            <td><span id="coloneHidePrint">:</span></td>

							            <td style="font-size:14px; width:200px; text-align: left;"><span id="issueOrderName"></span></td>
							            
							              <td style="width:15px;"></td>
							            
							            <td style="font-size:16px;text-align:left;width:80px;">Order No</td>
							            
							            <td>:</td>
							            <td style="font-size:14px;width:90px; text-align:right;"><span id="issueOrderNoerew"></span></td> 
							        </tr>
									
									<tr style="display:inline-block; height: 8px;">
							            <td style="width:110; font-size:16px;  text-align:left;">Project Name</td>

							            <td>:</td>

							             <td style="font-size:14px; width:200px; text-align: left;"><span id="projectName"></span></td>
							            
							             <td style="width:15px;"></td>
							             
							           <td style="font-size:16px; text-align:left;width:80px;"><span>Branch</span></td>
							             
							              <td>:</td>
							             
							              <td style="font-size:14px; width:90px; text-align:right;"><span id="branch"></span></td>  

							        </tr>

							        <tr style="display:inline-block; height: 8px;">
							           
							            <td style="font-size:16px;text-align:left;width:110px;"> Project Type</td>
							             <td>:</td>
							            <td style="font-size:14px;  text-align:left;"><span id="projectType"></span></td> 
							        </tr>
		
		                       </table>

				<div style="padding-left: 70px;padding-right: 70px; ">
                    <table id="issuePrint"  style="color:black; font-size:16px;border-collapse:collapse; padding-top:100px !important;" border= "1px solid black;"  cellpadding="0" cellspacing="0">


                            <thead style="">
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
				
<!--  Footer  -->
                          <div class='row' style='font-size:16px;text-align: left; margin-top:50px;'>Receive By <span style='width: 36%; padding-left:3px;'>:</span><span  id='createPerson' style="padding-left:4px;"></span> </div> <div class='row' style='font-size:16px;text-align: left;'>Designation<span style='width:40%; padding-left:4px;'>:</span><span id='createPersonDeg' style="padding-left:4px;"></span></div> <div class='row' style='font-size:16px; text-align:left;'>Emp.Id <span style='width:60%; padding-left:30px;'>:</span><span id='createPersonId' style="padding-left:4px;"></span><span style="padding-left: 183px;">HR & Adminitration</span> <span style='display:inline-block; width:36%;'></span></span></div>
               </div>
            </div>
       </div>
   </div>
</div> 

   <div id="hiddenInfo" style="display: none;"></div>
   <br>
                                           
      <!--end test -->
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