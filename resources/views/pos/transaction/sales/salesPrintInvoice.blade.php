@extends('layouts/pos_layout')
@section('title', '| Sales')
@section('content')
<style type="text/css">
	#invoice{
		width: 200px;
		border: 1px solid black;
		text-align: center;
	}
</style>
@include('successMsg')
@include('convert_word')
<?php 
  /*echo "<pre>";
         print_r($receiveAllDataBySalesId);
  echo "</pre>";*/
  //echo $productIdFirst;
?>

<div class="row">
    <div style="float: right; padding-right: 30px; font-size:18px; color: #64363F"> 
        <button id="printList" style="background-color:transparent;border:none;">
            <i class="fa fa-print animated fadeInRight" aria-hidden="true"></i>
        </button>
    </div> 
</div>

<div id="printSalesInvoice">
<div id="headerDiv">
	
</div>
<div class="row" style="margin-top: 50px;">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-4" style="color:black !important;" id="addressDiv">
					<span>Ref : {{$ownerCompanyName}}/{{$productName}}/{{$clientInformationReceive->companyShortName}}/{{$salesYear}}/{{$salesMonth}}-{{$salesBillNo}}</span><br>
					<span>Date : <?php echo date('d/m/Y'); ?></span><br>
					<span>Invoice No : SB000{{$salesBillNo}}</span><br>
			</div>
			<div class="col-md-4" id="invoiceDiv">
				<div id="invoice">
					<h3>Invoice</h3>
				</div>
			</div>
			<div class="col-md-2"></div>
		</div>
		<div class="row" style="margin-top: 30px;">
			<div class="col-md-2"></div>
			<div class="col-md-9">
				<div>
				<span>To,</span><br>
				<span>{{$clientInformationReceive->clientContactPerson}}</span><br>
				<span>{{$clientInformationReceive->contactPersonDesigntion}}</span><br>
				<span>{{$clientInformationReceive->clientCompanyName}}</span><br>
				<span>{{$clientInformationReceive->address}}</span>
				</div>
				<div style="margin-top: 50px;">
					<b>Subject : <u>Invoice Against {{$productName}} Software</u></b>
				</div>
				<div style="margin-top: 20px;">
				  <table class="table table-bordered" id="printTable" border="1pt solid ash" style="color:black; border-collapse: collapse; margin-top:10px;">
					<thead>
					 <tr>
					 	<th>SL</th>
					 	<th>Particular</th>
					 	<th>Price</th>
					 	<th>Qty</th>
					 	<th>Amount</th>
					 </tr>
					</thead>
					<tbody>
						 <?php $no=0; $totalQuantity=0; $totalAmountOfSales=0;?>
						@foreach($receiveAllDataBySalesIds as $receiveAllDataBySalesId)
						<tr>
							<td>{{++$no}}</td>
							<?php if($receiveAllDataBySalesId->branchId==1){ ?>
							<td>{{$productName}} Software Bill for the Head Office</td>
							<?php } else if($receiveAllDataBySalesId->branchId==2){ ?>
							<td>{{$productName}} Software Bill for the Branch</td>
							<?php } ?>
							<td class="amount">{{$receiveAllDataBySalesId->unitPrice}}</td>
							<td class="quantity">{{$receiveAllDataBySalesId->salesProductQuantity}}</td>
							<td class="amount">{{$receiveAllDataBySalesId->totalAmount}}</td>
							<?php
							$totalQuantity += $receiveAllDataBySalesId->salesProductQuantity;
							$totalAmountOfSales += $receiveAllDataBySalesId->totalAmount;
							?>
						</tr>
						@endforeach
						<tr>
							<td colspan="3" class="text-center" id="totalAmount"><b>Total</b></td>
							<td class="quantity">{{$totalQuantity}}</td>
							<td class="amount">{{$totalAmountOfSales}}</td>
						</tr>
					</tbody>
				  </table>
				  <b>In word : BDT <?php echo convert_number_to_words($totalAmountOfSales);?></b>
				</div>
				<div style="margin-top: 50px; ">
					<p style="color:black!important;">Demand Draft/Online Cheque is to be issued in favor of &nbsp;"{{$ownerCompanyName}}".</p>
				</div>
				<div style="margin-top: 50px;">
					<p style="color:black!important;">Thanks you very much for your kind continuous support.</p>
				</div>
				<div style="margin-top: 20px;">
					<p id="thankingId" style="color:black!important;">Thanking You.</p>
					<span><b>{{$employeeName}}</b></span><br>
					<span>{{$employeePositionName}}</span><br>
					<span><b>{{$ownerCompanyName}}</b></span><br>
				</div>
			</div>
			<div class="col-md-1"></div>
		</div>
	</div>
  </div>
</div>
<script type="text/javascript">
	$("#printList").click(function(event) {
       var mainContents = document.getElementById("printSalesInvoice").innerHTML;
       var headerContents = '<style>#headerDiv{height:150px;}</style>';

       var printStyle = '<style>#printTable{float:left;height:auto;padding:0px;width:100% !important;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;} tr{ page-break-inside:avoid; page-break-after:auto } table tbody tr td.amount{text-align:right !important;}#invoice{width: 150px;border: 1px solid black;text-align: center;margin-left:300px;margin-top:-30px;}#thankingId{margin-bottom:50px !important;}#totalAmount{text-align:center;}#printTable tbody tr td{padding:7px;}#printTable tbody tr .amount{text-align:right;}#printTable tbody tr .quantity{text-align:center;}</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

       
       var printContents = '<div id="order-details-wrapper">' + headerContents + printStyle + mainContents +'</div>';

       var win = window.open('','printwindow');
       win.document.write(printContents);
       win.print();
       win.close();
   });
</script>

@endsection