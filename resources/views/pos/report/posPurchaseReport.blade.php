@extends('layouts/pos_layout')
@section('title', '| Sales')
@section('content')
@include('successMsg')

<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;
if(isset($_GET['salesTypeId'])) {
  $salesTypeId =$_GET['salesTypeId'];   
} else {
 $salesTypeId =0; 
}

?>

<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
         
              <button id="print" class="btn btn-info pull-left print-icon" style="">
                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
              </button>

              <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
              </button>

              <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
              </button>

            </div>
            <div class="row">
              {!! Form::open(array('url' => 'pos/posPurchaseReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm','method' => 'get')) !!}
         
       
         <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('startDate', 'Start Date:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-md-12">
            {!! Form::text('startDate', $value = null, ['class' => 'form-control', 'id' => 'startDate', 'type' => 'text','placeholder' => 'Enter Start Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
    
            </div> 
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('endDate', 'End Date:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
          {!! Form::text('endDate', $value = null, ['class' => 'form-control', 'id' => 'endDate', 'type' => 'text','placeholder' => 'Enter End Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
            </div> 
          </div>
        </div>

          <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('branchId', 'Branch:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
              @php
                                $branches = DB::table('gnr_branch')->select('id','name','branchCode')->get();
                            @endphp
                            <select id="branchId" name="branchId" class="form-control">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{$branch->id}}">{{str_pad($branch->branchCode,3,'0',STR_PAD_LEFT).'-'.$branch->name}}</option>
                                @endforeach
                            </select> 
          </div> 
        </div>
      </div>

       <div class="col-md-1">
          <div class="form-group">
            {!! Form::label('billNo', 'Purchase:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
             <select name="billNo" style="width: 100px" id="billNo" class="form-control">
              <option value="" selected="selected">Select Purchase</option>
              @foreach($purchaseNo as $purchaseNos)
              <option value="{{$purchaseNos->id}}">{{$purchaseNos->billNo}}</option>
              @endforeach
            </select>
          </div> 
        </div>
      </div>

         <div class="col-md-1">
          <div class="form-group">
            {!! Form::label('productId', 'Product:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
             <select style="width: 100px; padding-top:6px; font-size: 8px;" class="productId  select2">
                                                <option value="select">Select product</option>
                                                @foreach($productNames as $productName)
                                                    <option value="{{$productName->id}}" class="productCodeId">{{str_pad($productName->code,3,"0",STR_PAD_LEFT )}} - {{$productName->name}}</option>
                                                @endforeach
                                          </select>
          </div> 
        </div>
      </div>

        <div class="col-md-1">
          <div class="form-group">
            {!! Form::label('supplierId', 'Supplier:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-sm-12">
             <select name="supplierId" style="width:100px" id="supplierTypeId" class="form-control col-sm-9">
              <option value="" selected="selected">Select supplier</option>

              @foreach($purchaseSuppliers as $purchaseSupplier)
              <option value="{{$purchaseSupplier->id}}">{{$purchaseSupplier->name}}</option>
              @endforeach
            </select>
          </div> 
        </div>
      </div>


        <div class="col-md-1">
          <div class="form-group">
            <label class="col-md-12 control-label"></label>
            <div class="col-md-12" style="margin-top: -30px;">
              <button id="btnSubmitSearch"  name="btnSubmitSearch" class="btn btn-primary animated fadeInDown">Search</button>
            </div>
          </div>
        </div>
        {!! Form::close()  !!}
      </div>
    
        <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">PURCHASE REPORT</font></h1>
      </div>
      <div id="printDiv">
        <div class="panel-body panelBodyView" id="printContents"> 
          <table class="table table-striped table-bordered" id="posSalesView" width="100%" style="color:black;font-size:14px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th width="80">SL No.</th>
                <th>Purchase Date</th>
                <th>Purchase No</th>
                <th>Branch Name</th>
                <th>Product Name</th>
                <th>Barcode</th>
                <th>Received Quantity</th>
                <th>Supplier Name</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>

               <?php $no=0; ?>
              @foreach($posPurchaseReports as $posPurchaseReports)
              <tr class="item{{$posPurchaseReports->id}}">
                <td class="text-center slNo">{{++$no}}</td>
                <td>{{date('d-m-Y', strtotime($posPurchaseReports->purchaseDate))}}</td>
                <td style="text-align: left; padding-left: 5px;">{{$posPurchaseReports->billNo}}</td>
                <td style="text-align: left; padding-left: 5px;">
              
                </td> 
                  <td style="text-align: left; padding-left: 5px;">
                   {{$posPurchaseReports->name}}
                </td> 
                 <td style="text-align: left; padding-left: 5px;">
                   {{$posPurchaseReports->code}}
                </td> 
                  <td style="text-align: left; padding-left: 5px;">
                   {{$posPurchaseReports->quantity}}
                </td>
                <td style="text-align: left; padding-left: 5px;">
                  <?php
                  $supplierName = DB::table('pos_supplier')->select('name')->where('id',$posPurchaseReports->supplierId)->first();
                  ?>
                  {{$supplierName->name}}</td>
                  <td style="text-align: center;">{{$posPurchaseReports->price}}</td>
                  <td style="text-align: right; padding-right: 5px;">{{number_format($posPurchaseReports->total,2)}}</td>
                </tr>
                @endforeach
        
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

@include('dataTableScript')
<script type="text/javascript">
$(document).ready(function() {

    $( "#startDate" ).datepicker({
      dateFormat: "yy-mm-dd",
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      yearRange: "-20:+0"
    });

      $( "#endDate" ).datepicker({
      dateFormat: "yy-mm-dd",
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      yearRange: "-20:+0"
    });
});

$('.select2').select2();

</script>
<script type="text/javascript">
  $(function() {

    $("#btnExportExcel").click(function(e) {
        //alert('sdsds');
        var today = new Date();
        var dd = today.getDate();

        var mm = today.getMonth()+1; 
        var yyyy = today.getFullYear();
        if(dd<10) 
        {
          dd='0'+dd;
        } 

        if(mm<10) 
        {
          mm='0'+mm;
        } 
        today = dd+'-'+mm+'-'+yyyy;
        //alert(today);
        let file = new Blob([$('#printDiv').html()], {type:"application/vnd.ms-excel"});
        let url = URL.createObjectURL(file);
        let a = $("<a />", {
          href: url,
          download: " Sales Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });


  

  $("#print").click(function(){
    var printContents = document.getElementById("printContents").innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML ="<p class='text-center'>Ambala Pos.</p><p class='text-center'>Sales Report</p>" + printContents;

    window.print();
    document.body.innerHTML = originalContents;

  });
</script> 
@endsection