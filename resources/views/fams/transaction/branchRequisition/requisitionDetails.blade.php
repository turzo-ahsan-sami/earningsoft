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
	
	.table-bordered > thead > tr > th, 
	.table-bordered > tbody > tr > th, 
	.table-bordered > tfoot > tr > th, 
	.table-bordered > thead > tr > td, 
	.table-bordered > tbody > tr > td, 
	.table-bordered > tfoot > tr > td {
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
	    width: 80px;
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

</style>

<div id="useDetailsModel" class="modal fade" style="margin-top:2%">
<div class="modal-dialog">
	<div class="modal-content">
		<div style="text-align: center;" class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="useDetailsHead">Requisition Details</h4>
		<div style="padding-top: 10px;">	
		<p>
			<span style="margin-top: 0px;margin-bottom: 0px">Requisition No : </span>
			<span style="margin-top: 0px;margin-bottom: 0px"><span id="empRequisitionHead"></span></span>
		</p>	
		<p>
			<span style="margin-top: 0px" id="">Requisition From : </h5>
			<span style="margin-top: 0px;margin-bottom: 0px"><span id="empReqBranchHead"></span></span>
		</p>
		<p>
			<span style="margin-top: 0px;margin-bottom: 0px"  id="">Requisition To : </span>
			<span style="margin-top: 0px;margin-bottom: 0px"><span id="reqEmployeeHead"></span></span>
		</p>
		<p>
			<span style="margin-top: 0px;margin-bottom: 0px"  id="">Requisition Date : </span>
			<span style="margin-top: 0px;margin-bottom: 0px"><span id="requiDateHead"></span></span>
		</p>	
		</div>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					
							<a class="list-group-item active">Requisition No: <span id="showRequisitionNo"></span></a>
							<table class="table table-bordered responsive order-details-table" border="0" cellpadding="0" cellspacing="0" width="100%" id="swhoAppendRows">
						    <thead>
						    	
							    <tr>
						        	
							        <th rowspan="2" width="70">SL No.</th>
							        <th rowspan="2" style="text-align: left;">Product Name</th>
							        <th colspan="5" width="80" style="border-bottom: none;">Use Information</th>
							    </tr>
							    <tr>
							    	<th width="200"><span style="background-color: white; padding: 4px 15px;">Qty</span></th>
							        <!-- <th width="80"><span style="background-color: white; padding:4px 3px">Unit Price</span></th>
							        <th width="80"><span style="background-color: white; padding: 4px 1px;">Total Price</span></th> -->
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
					                <!-- <td></td>
					                <td>
							        	<input class="totalOrderedProductQuantity" type="text" value="" readonly="readonly" id="totalAmountDetails"/>
							        </td> -->
					                
							    </tr>
							    </tfoot>
							</table>
					
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input id="modalDeliveryNo" type="hidden" value="" />
			<input id="modalOrderNo" type="hidden" value=""/>
			<input id="modalrequisitionNo" type="hidden" value="" />
			<button type="button" class="btn btn-primary" data-dismiss="modal" id="useDetailsDissmiss">Close</button>
		</div>
	</div>
</div>
</div>