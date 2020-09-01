@extends('layouts/pos_layout')
@section('title', '| Sales Return')
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
              {{-- <a href="" class="btn btn-info pull-right addViewBtn" id="print" media="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}
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
              Sales Return Report
            </h3>

      

    </div>


    <div class="row">
              {!! Form::open(array('url' => 'pos/posSalesReturnServiceReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm','method' => 'get')) !!}
              <div class="col-md-2">
               <div class="form-group">
                {!! Form::label('clientCompanyId', 'Customer Name:', ['class' => 'col-sm-12 control-label fontCustomize']) !!}
                <div class="col-md-12">
                 <select name="customerTypeId" id="customerTypeId" class="form-control">
                 <option value="all" selected>All</option>
                 @foreach($customerNames as $customerName)
                 <option value="{{$customerName->id}}" >{{$customerName->name}}</option>
                 @endforeach
               </select>
                </div>
              </div>
            </div>
            <div class="col-md-2">
             <div class="form-group">
              {!! Form::label('salesBillNo', 'BillNo:', ['class' => 'col-md-12 control-label fontCustomize']) !!}
              <div class="col-md-12">
               <select name="salesTypeId" id="salesTypeId" class="form-control">
                 <option value="all">All</option>
               </select>
             </div>
           </div>
         </div>
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
        <span class="companyNameData fontCustomize" style="color:#333; text-align: center" ><b> {{$companyName->name}} </b></span><br>
         <span class="fontCustomize" style="color:#333;"><b> {{$companyName->address}} </b></span><br>
        <span  style="color:#333;" class="fontCustomize"><b>Sales Return Report</b></span><br>
        @if($startDate || $endDate)
        <span  style="color:#333;" class="fontCustomize"><b> From </b> {{$startDate}}<b> To </b>{{$endDate}}</span></p>
        @endif
        <p class="text-right fontCustomize"  style="color:#333; text-align: right; font-size: 12px;">Print Date : {{date("d-m-Y")}}</p>
        <div class="panel-body panelBodyView" id="printContents"> 
          <table class="table table-striped table-bordered" id="posSalesView" width="100%" style="color:black;font-size:14px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0">          
            <thead>
              <tr>
                <th>SL#</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Customer Name</th>
                <th>Product Name</th>
                <th>Received Quantity</th>
                <th>Price</th>
                <th>Total Sales Amount</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            
            <tbody>
              <?php 
              $no=1; 
              $qty=0; 
              $price=0; 
              $total=0; 

              ?>
              @foreach($posSalesReports as $posSalesReport)
              <tr>
               <td>{{$no++}}</td> 

               {{-- <td>{{ date('d-m-Y', strtotime($posSalesReport->returnDate)) }}</td> 
               <td>{{$posSalesReport->billNo}}</td> 
               <td style="text-align: left; padding-right: 5px;">{{$posSalesReport->customerName}}</td>  --}}

               @if($posSalesReport->returnDate) <td style="text-align: center" rowspan="{{$posSalesReport->daterow}}">{{ date('d-m-Y', strtotime($posSalesReport->returnDate)) }}</td> @endif
               @if($posSalesReport->billNo) <td style="text-align: center" rowspan="{{$posSalesReport->billrow}}">{{ $posSalesReport->billNo }}</td> @endif
               @if($posSalesReport->customerName) <td style="text-align: left; padding-left: 5px" rowspan="{{$posSalesReport->customerrow}}">{{ $posSalesReport->customerName }}</td> @endif

               <td style="text-align: left; padding-right: 5px;">{{$posSalesReport->productName}}</td> 
               <td>{{$posSalesReport->qty}}</td> 
               <td style="text-align: right; padding-right: 5px;">{{ number_format($posSalesReport->price, 2) }}</td> 
               <td style="text-align: right; padding-right: 5px;">{{ number_format($posSalesReport->total, 2) }}</td> 
               @php
                  $qty += $posSalesReport->qty;
                  $price += $posSalesReport->price;
                  $total += $posSalesReport->total;
               @endphp
              </tr>
                @endforeach
              <tr>
                <td colspan="5"><b>Total</b></td>
                <td><b>{{$qty}}</b></td>
                <td style="text-align: right; padding-right: 5px;"><b>{{ number_format($price, 2) }}</b></td>
                <td style="text-align: right; padding-right: 5px;"><b>{{ number_format($total, 2) }}</b></td>
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
          download: " Sales Report_"+ today + ".xls"}).appendTo("body").get(0).click();
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

//onchange customerName
$('#customerTypeId').change(function(){
    var customerId = $(this).val();
    //alert(customerId);
    $.ajax({
        url:'./getSellsBillNoCustomer',
        type: 'GET',
        data: {customerId:customerId},
        dataType: 'json',

        success: function(data) {
           $("#salesTypeId").empty();
            $("#salesTypeId").append('<option value ="all">Select</option>');
            
            $.each(data,function(key,value){
              $("#salesTypeId").append('<option value="'+value.billNo+'">'+value.billNo+'</option>');
            });
        }
    });
})






});
  $("#print").click(function(){
    var printContents = document.getElementById("printDiv").innerHTML;
    // var originalContents = document.body.innerHTML;

    // document.body.innerHTML = printContents;

    // window.print();
    // document.body.innerHTML = originalContents;

    var win = window.open('','printwindow');
      win.document.write(printContents);
      win.print();
      win.close();

  });


</script> 
@endsection