@extends('layouts/pos_layout')
@section('title', '| Sales Wise Profit Report')
@section('content')
@include('successMsg')
<style type="text/css">
  .fontCustomize{
    font-size: 13px;
  }
</style>
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

            <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;padding-left: 220px">
              Sales Wise Profit Report
            </h3>
            

      
    </div>

    <div class="row">
              {!! Form::open(array('url' => 'pos/posSalesWiseProfitReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm','method' => 'get')) !!}
              
            
         <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('startDate', 'Start Date:', ['class' => 'col-md-12 control-label fontCustomize']) !!}
            <div class="col-sm-12">
              {!! Form::text('startDate', null, ['class' => 'form-control ', 'id' => 'startDate', 'required' => 'required']) !!}
            </div> 
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group">
            {!! Form::label('endDate', 'End Date:', ['class' => 'col-md-12 control-label fontCustomize']) !!}
            <div class="col-sm-12">
              {!! Form::text('endDate', null, ['class' => 'form-control ', 'id' => 'endDate', 'required' => 'required']) !!}
            </div> 
          </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
              {{-- {!! Form::label('', '', ['class' => 'control-label col-md-12', 'style' => 'color:#708090; padding-top: 25px;']) !!} --}}
              <div class="col-md-12">
                <label for="year" class="control-label">Action</label><br>
                <input class="btn btn-primary" type="submit" name="Show" value="Search">
              </div>
            </div>
        </div>
        {!! Form::close()  !!}
      </div>

    @if(count($posSalesReports) != 0)
      <div id="printDiv">
        <p class="reportHeading" style="text-align: center;">
        <span class="companyNameData fontCustomize" style="color:#333;"><b> {{$companyName->name}} </b></span><br>
        <span class="fontCustomize" style="color:#333;"><b> {{$companyName->address}} </b></span><br>
        <span  style="color:#333;" class="fontCustomize"><b>Sales Wise Profit Report</b></span><br>
        @if($startDate || $endDate)
        <span  style="color:#333;" class="fontCustomize"><b> From </b> {{$startDate}}<b> To </b>{{$endDate}}</span></p>
        @endif
        <p class="text-right fontCustomize"  style="color:#333; font-size: 12px; text-align: right;">Print Date : {{date("d-m-Y")}}</p>
        <div class="panel-body panelBodyView" id="printContents"> 
          <table class="table table-striped table-bordered" id="posSalesView" width="100%" style="color:black;font-size:14px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0">
           
            <thead>
              <tr>
                <th width="80">SL#</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Sale Amount</th>
                <th>Cost Price</th>
                <th>Profit</th>
              </tr>
              
            </thead>
            
            <tbody>
              @php 

                $no=1; 
                $totalSaleAmount = $totalCostAmount = $totalProfitAmount = $totalQuantity = 0;

              @endphp

              @foreach($posSalesReports as $posSalesReport)
              <tr>
               <td>{{$no++}}</td>
               
               {{-- <td>{{ date('d-m-Y', strtotime($posSalesReport->salesDate)) }}</td>
               <td>{{$posSalesReport->saleBillNo}}</td>  --}}

              @if($posSalesReport->salesDate) <td style="text-align: center" rowspan="{{$posSalesReport->daterow}}">{{ date('d-m-Y', strtotime($posSalesReport->salesDate)) }}</td> @endif
              @if($posSalesReport->saleBillNo) <td style="text-align: center" rowspan="{{$posSalesReport->billrow}}">{{ $posSalesReport->saleBillNo }}</td> @endif
               
               <td style="text-align: left; padding-right: 5px;">{{$posSalesReport->productName}}</td>

               <td>{{ $posSalesReport->quantity }}</td> 
               <td style="text-align: right; padding-right: 5px;">{{ number_format($posSalesReport->total - $posSalesReport->discountAmount, 2)}}</td>
               <td style="text-align: right; padding-right: 5px;">{{ number_format($posSalesReport->productCostPrice, 2) }}</td>
               <td style="text-align: right; padding-right: 5px;">{{ number_format(($posSalesReport->total - $posSalesReport->discountAmount) - $posSalesReport->productCostPrice, 2)}}</td>
               @php
                  $totalQuantity += $posSalesReport->quantity;
                  $totalSaleAmount += $posSalesReport->total - $posSalesReport->discountAmount;
                  $totalCostAmount += $posSalesReport->productCostPrice;
                  $totalProfitAmount += ($posSalesReport->total - $posSalesReport->discountAmount) - $posSalesReport->productCostPrice;
               @endphp
               </tr>
                @endforeach
              <tr>
                <td colspan="4"><b>Total</b></td>
                <td><b>{{ $totalQuantity }}</b></td>
                <td style="text-align: right; padding-right: 5px;"><b>{{ number_format($totalSaleAmount, 2) }}</b></td>
                <td style="text-align: right; padding-right: 5px;"><b>{{ number_format($totalCostAmount, 2) }}</b></td>
                <td style="text-align: right; padding-right: 5px;"><b>{{ number_format($totalProfitAmount, 2) }}</b></td>
              </tr>
            </tbody>
           
            </table>
          </div>
        </div>
      </div>
      @endif
  </div>
</div>
</div>

@include('dataTableScript')
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
          download: " Sales Wise Profit Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });
    $( "#startDate" ).datepicker({
      dateFormat: "dd-mm-yy",
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      yearRange: "-20:+0"
    });

    $( "#endDate" ).datepicker({
      dateFormat: "dd-mm-yy",
      showOtherMonths: true,
      selectOtherMonths: true,
      changeMonth: true,
      changeYear: true,
      yearRange: "-20:+0"
    });


});
  $("#print").click(function(){
    var printContents = document.getElementById("printDiv").innerHTML;
    var win = window.open('','printwindow');
    win.document.write(printContents);
    win.print();
    win.close();
  });

</script> 
@endsection