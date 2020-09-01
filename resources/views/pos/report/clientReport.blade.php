@extends('layouts/pos_layout')
@section('title', '| Collection Report')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;

if(isset($_GET['salesBillNo'])){
  $salesBillNo = $_GET['salesBillNo'];
} else {
  $salesBillNo = 0;
}
if(isset($_GET['salesTypeId'])) {
  $salesTypeId =$_GET['salesTypeId'];   
} else {
 $salesTypeId =0; 
}
if(isset($_GET['clientCompanyId'])) {
  $clientCompanyId =$_GET['clientCompanyId'];   
} else {
 $clientCompanyId =0; 
}

?>
<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading"  style="padding-bottom:0px">
            <div class="panel-options">
             {{--  <a href="" class="btn btn-info pull-right addViewBtn" id="print" media="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}
             <button id="print" class="btn btn-info pull-left print-icon" style="">
              <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
            </button>

            <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
              <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
            </button>

            <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
              <i class="fa-file-pdf-o" aria-hidden="true"> Pdf</i>
            </button>
          </div>
          <div class="row">
            {!! Form::open(array('url' => 'pos/posCollectionClientReport', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm','method' => 'get')) !!}
            <div class="col-md-2">
             <div class="form-group">
              {!! Form::label('clientCompanyId', 'Company Name:', ['class' => 'col-sm-12 control-label']) !!}
              <div class="col-md-12">
                <?php 
                $posClients = array('' => 'Select Company') + DB::table('pos_client')->pluck('clientCompanyName','id')->all(); 
                ?>      
                {!! Form::select('clientCompanyId', ($posClients), null, array('class'=>'form-control', 'id' => 'clientCompanyId')) !!}
              </div>
            </div>
          </div>
          <div class="col-md-2">
           <div class="form-group">
            {!! Form::label('salesBillNo', 'Type:', ['class' => 'col-md-12 control-label']) !!}
            <div class="col-md-12">
             <select name="salesTypeId" id="salesTypeId" class="form-control">
               <option value="0">Select Type</option>
               <option value="1" <?php if($salesTypeId==1) echo 'Selected'; ?>>Sales</option>
               <option value="2" <?php if($salesTypeId==2) echo 'Selected'; ?>>Service Charge</option>
             </select>
           </div>
         </div>
       </div>
       <div class="col-md-2">
         <div class="form-group">
          {!! Form::label('salesBillNo', 'Bill No:', ['class' => 'col-md-12 control-label']) !!}
          <div class="col-md-12">
            <?php
            if((isset($clientCompanyId)) && (isset($salesTypeId))) {
             $salesBillNoArr = DB::table('pos_sales')->select('salesBillNo')->where('companyId',$clientCompanyId)->where('salesType',$salesTypeId)->get();
           } else if(isset($clientCompanyId)) {
            $salesBillNoArr = DB::table('pos_sales')->select('salesBillNo')->where('companyId',$clientCompanyId)->get();                               
          } else {
            $salesBillNoArr = DB::table('pos_sales')->select('salesBillNo')->get();
          }
          ?>
          <select id="salesBillNo" name="salesBillNo" class="form-control">
           <option value="0">Select Bill</option>    
           @foreach ($salesBillNoArr as $salesBillNo)
           <option value="{{$salesBillNo->salesBillNo}}" @if ($selectedBillNo==$salesBillNo->salesBillNo) {{"selected=selected"}}@endif >SB000{{$salesBillNo->salesBillNo}}</option>
           @endforeach
         </select>
       </div>
     </div>
   </div>
   <div class="col-md-2">
    <div class="form-group">
      {!! Form::label('startDate', 'Start Date:', ['class' => 'col-md-12 control-label']) !!}
      <div class="col-sm-12">
        {!! Form::text('startDate', null, ['class' => 'form-control ', 'id' => 'startDate']) !!}
      </div> 
    </div>
  </div>
  <div class="col-md-2">
    <div class="form-group">
      {!! Form::label('endDate', 'End Date:', ['class' => 'col-md-12 control-label']) !!}
      <div class="col-sm-12">
        {!! Form::text('endDate', null, ['class' => 'form-control ', 'id' => 'endDate']) !!}
      </div> 
    </div>
  </div>
  <div class="col-md-1">
    <div class="form-group">
      <label class="col-md-12 control-label"></label>
      <div class="col-md-12" style="margin-top: 20px;">
        <button id="btnSubmitSearch"  name="btnSubmitSearch" class="btn btn-primary animated fadeInDown">Search</button>
      </div>
    </div>
  </div>
  {!! Form::close()  !!}
</div>
<h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">CLIENT REPORT</font></h1>
</div>
<div id="printDiv">
  <div class="panel-body panelBodyView" id="printContents"> 
    <div class="row" style="margin-left: 5px;">
      <table>
        <tr>
         <th>Date </th><td> : <?php echo date('d/m/Y'); ?></td>
       </tr>
       <tr>
         <th>Company Name </th><td> :  <?php
         $companyName = DB::table('pos_client')->where('id',$clientCompanyId)->value('clientCompanyName');
         ?>
         {{$companyName}}
       </td>
     </tr>
     <tr>
       <th>Company Address </th><td> :<?php
       $address = DB::table('pos_client')->where('id',$clientCompanyId)->value('address');
       ?>
     {{$address}} </td>
   </tr>
 </table>
</div>
<table class="table table-striped table-bordered" width="100%" style="color:black;font-size:14px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0" id="posCollectionView">
  <thead>
    <tr>
      <th>Collection Date</th>
      <th>Bill No</th>
      <th>Collection Bill No</th>
      <th>Type</th>
      <th>Sales Amount</th>
      <th>Ins.No</th>
      <th>Paid Amount</th>
      <th>Due Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php $no=0; 

    if(isset($posCollectionReport)):
      $salesBillNoCount=0;
      $totalSubSalesAmount=0;
      $totalSubSalesPayAmount=0; 
      $totalSalesAmountValue=0;
      $totalSubSalesPayAmountValue=0;
      $totalDue=0;
      ?>

      @foreach($posCollectionReport as $key => $posCollection)
      <?php 

      $salesBillNo = $posCollection->salesBillNo; 
      ?>

      <?php 

      $isChangedSalesBillNo = 0;
      if ($key>0) {

       if ($posCollectionReport[$key-1]->salesBillNo!=$posCollection->salesBillNo) {
         $isChangedSalesBillNo = 1;
       }
     }

     if ($key == 0 || $isChangedSalesBillNo == 1) {
       $countSalesBillNo = $posCollectionReport->where('salesBillNo',$posCollection->salesBillNo)->count('salesBillNo');
       $salesBillNoCount = $salesBillNoCount + $countSalesBillNo;
                                       /*$totalSubSalesAmount += $posCollection->totalSalesAmount;
                                       $totalSubSalesPayAmount += $posCollection->salesPayAmount;*/
                                       $totalSubSalesAmount = $posCollectionReport->where('salesBillNo',$posCollection->salesBillNo)->sum('totalSalesAmount');
                                       $totalSubSalesPayAmount = $posCollectionReport->where('salesBillNo',$posCollection->salesBillNo)->sum('salesPayAmount');
                                     }
                                     ?>

                                     <tr class="item{{$posCollection->id}}">
                                       <td>{{date('d-m-Y', strtotime($posCollection->collectionDate))}}</td>
                                       @if ($key == 0 || $isChangedSalesBillNo == 1)
                                       <td style="text-align: left; padding-left: 5px;" rowspan="{{$countSalesBillNo}}">{{'SB000'.$posCollection->salesBillNo}}</td>
                                       @endif
                                       <td style="text-align: left; padding-left: 5px;">{{'CB000'.$posCollection->collectionBillNo}}</td>
                                       @if ($key == 0 || $isChangedSalesBillNo == 1)
                                       <td style="text-align: left; padding-left: 5px;" rowspan="{{$countSalesBillNo}}">
                                        @if($posCollection->salesType==1)
                                        {{'Sales'}}
                                        @elseif($posCollection->salesType==2)
                                        {{'Service Charge'}}
                                        @endif
                                      </td>
                                      @endif 
                                      <td style="text-align: center;">
                                        @if($posCollection->totalSalesAmount>0)
                                        {{$posCollection->totalSalesAmount}}
                                        @else
                                        {{'-'}}
                                        @endif
                                      </td>

                                      <td style="text-align: center;">{{$posCollection->installmentNo}}</td>

                                      <td style="text-align: center;">{{$posCollection->salesPayAmount}}</td>
                                      <td></td>

                                    </tr>
                                    
                                    @if($salesBillNoCount == $key+1)
                                    <tr style="font-size: 14px;">
                                      <td colspan="4"><b>Sub Total:</b></td>
                                      <td><b>{{$totalSubSalesAmount}}</b></td>
                                      <?php $totalSalesAmountValue += $totalSubSalesAmount; ?> 
                                      <td></td>
                                      <td><b>{{$totalSubSalesPayAmount}}</b></td>
                                      <td><b>{{$totalSubSalesAmount-$totalSubSalesPayAmount}}</b></td>
                                      <?php $totalSubSalesPayAmountValue += $totalSubSalesPayAmount; ?>
                                      <?php $totalDue += $totalSubSalesAmount-$totalSubSalesPayAmount; ?>
                                    </tr>
                                    @endif
                                    @endforeach
                                    <tr style="font-size: 18px;">
                                      <td colspan="4"><b> Total </b></td>
                                      <td><b>{{$totalSalesAmountValue}}</b></td>
                                      <td></td>
                                      <td><b>{{$totalSubSalesPayAmountValue}}</b></td>
                                      <td><b>{{$totalDue}}</b></td>
                                    </tr>
                                    <?php endif; ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
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
          download: "Client Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });
                      $( "#startDate" ).datepicker({
                        dateFormat: "yy-mm-dd",
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-3:+0"
                      });

                      $( "#endDate" ).datepicker({
                        dateFormat: "yy-mm-dd",
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-3:+0"
                      });


                      $("#clientCompanyId,#salesTypeId").change(function(event) {
                       var clientCompanyId = $("#clientCompanyId").val();
                       var salesTypeId = $("#salesTypeId").val();
       //alert(clientCompanyId);
       //alert(salesTypeId);
       //alert(clientCompanyId);
       $("#salesBillNo").empty();
       $("#salesBillNo").prepend('<option value="0">Select Bill</option>');
       $.ajax({
         url: './salesBillNoFillter',
         type: 'POST',
         dataType: 'json',
         data: {clientCompanyId: clientCompanyId,salesTypeId:salesTypeId},
       })
       .done(function(data) {
        //alert(JSON.stringify(data));
        if(data) {
          $.each(data,function(index,value){
            $('#salesBillNo').append("<option value='"+value.salesBillNo+"'>SB000"+value.salesBillNo+"</option>");
          });
        }
        console.log("success");
      })
       .fail(function() {
         console.log("error");
       })
       .always(function() {
         console.log("complete");
       });
       

     });


                    });
                    $("#print").click(function(){
                      var printContents = document.getElementById("printContents").innerHTML;
                      var originalContents = document.body.innerHTML;
                      document.body.innerHTML ="<p class='text-center'>Ambala Pos.</p><p class='text-center'>Collection Report</p>" + printContents;
                      window.print();
                      document.body.innerHTML = originalContents;
                      var win = window.open('','printwindow');
                      win.document.write("<p class='text-center'>Ambala Pos.</p><p class='text-center'>Collection Report</p>" + printContents);
                      win.print();
                      win.close();
                    });


                  </script>
                  @include('dataTableScript')
                  @endsection