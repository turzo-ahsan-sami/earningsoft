@extends('layouts/acc_layout')
@section('title', '| Register Report')
@section('content')


@php    
$projectSelected = isset($_GET['searchProject']) ? $_GET['searchProject'] : null;
$projectTypeSelected = isset($_GET['searchProjectType']) ? $_GET['searchProjectType'] : null;
$donorTypeSelected = isset($_GET['searchDonorType']) ? $_GET['searchDonorType'] : null;
$dateSelected = isset($_GET['searchDate']) ? $_GET['searchDate'] : null;
$firstRequest = isset($_GET['firstRequest']) ? '1' : '0';     
@endphp



<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">
          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options"> 
              <div class="panel-options">
               {{--  <a href="" class="btn btn-info pull-right addViewBtn" id="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}
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

          </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Loan Register Installment Report</h3>
        </div>
        {{-- <h5>kxghixd</h5> --}}
        
        <div class="panel-body panelBodyView"> 


          <!-- Filtering Start-->
          <div class="row" id="filtering-group">

            <div class="form-horizontal form-groups" style="padding-right: 0px;">

              {!! Form::open(['url' => 'loanRegisterInstallmentReport','method' => 'get']) !!}


              {!! Form::hidden('firstRequest',1) !!}

              <div class="col-md-2">
                <div class="form-group" style="font-size: 13px; color:black;">
                 <div style="text-align: center;" class="col-sm-12">
                  {!! Form::label('', 'Project:', ['class' => 'control-label pull-left']) !!}
                </div> 

                @php
                $acticeProjectforSearch = DB::table('acc_loan_register_account')->pluck('projectId_fk')->toArray();


                $searchProjects = DB::table('gnr_project')->whereIn('id',$acticeProjectforSearch)->select('id','name','projectCode')->get();
                @endphp

                <div class="col-sm-12">
                  <select name="searchProject" class="form-control input-sm" id="searchProject">
                    <option value="">All</option>                                         
                    @foreach($searchProjects as $searchProject)
                    <option value="{{$searchProject->id}}" @if($searchProject->id==$projectSelected){{"selected=selected"}}@endif>{{str_pad($searchProject->projectCode,3,"0",STR_PAD_LEFT).'-'.$searchProject->name}}</option>
                    @endforeach
                  </select>

                </div>
              </div>
            </div>



            <div class="col-md-2">
              <div class="form-group" style="font-size: 13px; color:black;">
               <div style="text-align: center;" class="col-sm-12">
                {!! Form::label('', 'Project Type:', ['class' => 'control-label pull-left']) !!}
              </div> 

              @php
              $acticeProjectTypesforSearch = DB::table('acc_loan_register_account')->distinct()->pluck('projectTypeId_fk')->toArray();


              $searchProjectTypes = DB::table('gnr_project_type')->whereIn('id',$acticeProjectTypesforSearch);

              if($projectSelected!=null){
                $searchProjectTypes = $searchProjectTypes->where('projectId',$projectSelected);
              }

              $searchProjectTypes = $searchProjectTypes->select('id','name','projectTypeCode')->get();
              @endphp

              <div class="col-sm-12">
                <select name="searchProjectType" class="form-control input-sm" id="searchProjectType">
                  <option value="">All</option>                                         
                  @foreach($searchProjectTypes as $searchProjectType)
                  <option value="{{$searchProjectType->id}}" @if($searchProjectType->id==$projectTypeSelected){{"selected=selected"}}@endif>{{str_pad($searchProjectType->projectTypeCode,3,"0",STR_PAD_LEFT).'-'.$searchProjectType->name}}</option>
                  @endforeach
                </select>

              </div>
            </div>
          </div>


          <div class="col-md-2">
            <div class="form-group" style="font-size: 13px; color:black;">
              <div style="text-align: center;" class="col-sm-12">
                {!! Form::label('', 'Donor Type:', ['class' => 'control-label pull-left']) !!}
              </div>

              <div class="col-sm-12">

                {!! Form::select('searchDonorType',[''=>'All','0'=>'Bank','1'=>'Donor'],$donorTypeSelected,['class'=>'form-control']) !!}
              </div>
            </div>
          </div>






          <div class="col-md-2" id="dateRangeDiv">
            <div class="form-group" style="font-size: 13px; color:black">
              <div style="text-align: center;" class="col-sm-12">
                {!! Form::label('', 'Month:', ['class' => 'control-label pull-left']) !!}
              </div>

              <div class="col-sm-12">
                <div class="form-group">
                  <div class="col-sm-12">
                    {!! Form::text('searchDateReplica',null,['id'=>'searchDateReplica','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
                    {!! Form::hidden('searchDate',null,['id'=>'searchDate']) !!}
                    <p id="searchDatee" style="color: red;display: none;">*Required</p>
                  </div>

                </div>

              </div>
            </div>
          </div>


          <div class="col-md-1">
            <div class="form-group" style="font-size: 13px; color:black">

              <div class="col-sm-12" style="padding-top: 25px;">

                {!! Form::submit('Search',['id'=>'search','class'=>'btn btn-primary btn-xs','style'=>'font-size:15px;']) !!}
              </div>
            </div>
            {!! Form::close() !!}
          </div>                                    

        </div>                            

      </div> {{-- End Filtering Group --}}
      <!-- filtering end-->



      @if($firstRequest)
      <div id="printDiv">



        <div id="printingContent">

          <div style="display: none;text-align: center;" id="hiddenTitle">
           <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
           <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
           <h4 style="text-align: center;padding: 0px;margin: 0px;">Loan Register Installment Report</h4>                          

           <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F, Y',strtotime($dateSelected))}}</h5>
         </div> 
         <div id="hiddenInfo" style="display: none;">                       

           <p style="padding: 0px;margin: 0px;font-size: 11px;">
            <span style='float: right;'>
              <span style="font-weight: bold;">Print Date :</span> {{date('F d,Y')}}
            </span>                               

          </p>             

        </div>


        <br>

        <table id="registerReportTable" class="table table-striped table-bordered" style="color: black;font-size:11px;border-collapse: collapse;margin-bottom:50px;" border= "1px solid black;" cellpadding="0" cellspacing="0" width="100%">
          <thead >
            <tr>
              <th rowspan="2">SL#</th>
              <th rowspan="2">Bank/Donar</th>
              <th rowspan="2">Account No / Phase/ Cycle</th>
              <th rowspan="2">Loan Amount</th>
              <th rowspan="2">Installment Size</th> 
              <th colspan="2" width="100">Current Month Loan Installment</th>            
              <th colspan="2" width="100">Current Month Loan Installment Re-Payment</th> 
              <th rowspan="2">Current Month Loan <br> Installment Due (Tk)</th>              

            </tr>
            <tr>
              <th>Date</th>
              <th>Amount (Tk)</th>
              <th>Date</th>
              <th>Amount (Tk)</th>

            </tr>


          </thead>
          <tbody>


           @php


           $gTloanAmount = 0;
           $gTinstallmentSize = 0;
           $gTloanAmount = 0;
           $gTcurrentMonthPayment = 0;
           $gTcurrentMonthDue = 0;
           @endphp

           @foreach($projectTypes as $projectType)
           <tr>
            <td colspan="10" class="name" style="font-weight: bold;font-size: 12px;">{{$projectType->name}}</td>
          </tr>




          @php
          $index = 0;

          $sTloanAmount = 0;
          $sTinstallmentSize = 0;
          $sTcurrentMonthPayment = 0;
          $sTcurrentMonthDue = 0;
          @endphp






          @foreach($accounts as $key => $account)

          @if($account->projectTypeId_fk==$projectType->id)

          @php

          $bankName = DB::table('gnr_bank')->where('id',$account->bankId_fk)->value('name');
          if ($account->phase>0) {
            $accNoPhaseCycleValue = "- / ".str_pad($account->phase,3,'0',STR_PAD_LEFT)." / ".str_pad($account->cycle,3,'0',STR_PAD_LEFT);
          }
          else{
            $accNoPhaseCycleValue = $account->accNo." / - / -";
          }

          $startDate = date("Y-m-d", strtotime($dateSelected));
          $endDate = date("Y-m-t", strtotime($startDate));


          $installmentSize = (float) DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->value('totalAmount');

          $installmentDate = DB::table('acc_loan_register_payment_schedule')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->value('paymentDate');

          if ($installmentDate==null) {
            $installmentDate = "-";
          }
          else{
            $installmentDate = date('d-m-Y',strtotime($installmentDate));
          }

          $paymentDateInThisMonth = DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->orderBy('id','desc')->value('paymentDate');

          if ($paymentDateInThisMonth==null) {
            $paymentDateInThisMonth = "-";
          }
          else{
            $paymentDateInThisMonth = date('d-m-Y',strtotime($paymentDateInThisMonth));
          }

          $paymentAmountInThisMonth = (float) DB::table('acc_loan_register_payments')->where('accId_fk',$account->id)->where('paymentDate','>=',$startDate)->where('paymentDate','<=',$endDate)->sum('totalAmount');

          $thisMonthDue = $installmentSize - $paymentAmountInThisMonth;

          @endphp


          <tr>

           <td>{{++$index}}</td> 


           @php
           $count = 1;               

           $isChanged = 0;

           if ($key>0) {
             if ($accounts[$key-1]->bankId_fk!=$account->bankId_fk) {
               $isChanged = 1;
             }
           }

           if ($key == 0 || $isChanged == 1) {
            $count = DB::table('acc_loan_register_account')->whereIn('bankId_fk',$bankIds)->whereIn('id',$accountListHavingInstallment);
            if ($projectSelected!=null) {
             $count = $count->where('projectId_fk',$projectSelected);
           }
           if ($projectTypeSelected!=null) {
             $count = $count->where('projectTypeId_fk',$projectTypeSelected);
           }
           $count = $count->where('bankId_fk',$account->bankId_fk)->count();

         }
         @endphp


         @if($isChanged==1 || $key==0)


         <td rowspan="{{$count}}" class="name">{{$bankName}}</td>


         @endif





         {{-- <td class="name bankName">{{$bankName}}</td>     --}}
         <td>{{$accNoPhaseCycleValue}}</td>
         <td class="amount">{{number_format($account->loanAmount,2,'.',',')}}</td>
         <td class="amount">{{number_format($installmentSize,2,'.',',')}}</td>
         <td>{{$installmentDate}}</td>
         <td class="amount">{{number_format($installmentSize,2,'.',',')}}</td>
         <td>{{$paymentDateInThisMonth}}</td>
         <td class="amount">{{number_format($paymentAmountInThisMonth,2,'.',',')}}</td>
         <td class="amount">{{number_format($thisMonthDue,2,'.',',')}}</td>


       </tr>

       @php
       $sTloanAmount = $sTloanAmount + $account->loanAmount;
       $sTinstallmentSize = $sTinstallmentSize + $installmentSize;
       $sTcurrentMonthPayment = $sTcurrentMonthPayment + $paymentAmountInThisMonth;
       $sTcurrentMonthDue = $sTcurrentMonthDue + $thisMonthDue;
       @endphp

       @endif
       @endforeach {{-- Account --}}

       <tr class="subTotal">
         <td colspan="3">Sub Total</td>
         <td class="amount">{{number_format($sTloanAmount,2,'.',',')}}</td>
         <td class="amount">{{number_format($sTinstallmentSize,2,'.',',')}}</td>
         <td></td>
         <td class="amount">{{number_format($sTinstallmentSize,2,'.',',')}}</td>
         <td></td>
         <td class="amount">{{number_format($sTcurrentMonthPayment,2,'.',',')}}</td>
         <td class="amount">{{number_format($sTcurrentMonthDue,2,'.',',')}}</td>
       </tr>


       @php
       $gTloanAmount = $gTloanAmount + $sTloanAmount;
       $gTinstallmentSize = $gTinstallmentSize + $sTinstallmentSize;
       $gTcurrentMonthPayment = $gTcurrentMonthPayment + $sTcurrentMonthPayment;
       $gTcurrentMonthDue = $gTcurrentMonthDue + $sTcurrentMonthDue;
       @endphp


       @endforeach {{-- Project Type --}}

     </tbody>
     <tfoot>
      <tr>
       <td colspan="3">Total</td>
       <td class="amount">{{number_format($gTloanAmount,2,'.',',')}}</td>
       <td class="amount">{{number_format($gTinstallmentSize,2,'.',',')}}</td>
       <td></td>
       <td class="amount">{{number_format($gTinstallmentSize,2,'.',',')}}</td>
       <td></td>
       <td class="amount">{{number_format($gTcurrentMonthPayment,2,'.',',')}}</td>
       <td class="amount">{{number_format($gTcurrentMonthDue,2,'.',',')}}</td>
     </tr>
   </tfoot>
 </table>
</div>
@endif
</div>
</div>
</div>
</div>
</div>
</div>
</div>


<style type="text/css">
#registerReportTable thead tr th{
  padding: 2px;
}
#registerReportTable tbody tr td.amount{
  text-align: right;
  padding-right: 5px;
}
#registerReportTable tbody tr td.name{
  text-align: left;
  padding-left: 5px;
}

#registerReportTable tfoot tr td.name{
  text-align: center;
  font-weight: bold;
}
#registerReportTable tfoot tr td.amount{
  text-align: right;
  padding-right: 5px;
  font-weight: bold;
}
#registerReportTable tfoot tr td{
  background-color: #8b8d91;
  line-height: 10px;
}
#registerReportTable tfoot tr td:nth-child(1){
  text-align: center;
  font-size: 13px;
}
#registerReportTable tbody tr.subTotal td{
  background-color: #9da5b2;
}
</style>



<script type="text/javascript">
  $(document).ready(function() {

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
          download: "Loan Register Installment Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });

    function toDate(dateStr) {
      var parts = dateStr.split("-");
      return new Date(parts[2], parts[1] - 1, parts[0]);
    }


    var searchDate = "{{$dateSelected}}";


    if(searchDate!=''){

      $("#searchDateReplica").val($.datepicker.formatDate('MM yy', toDate(searchDate)));
          //$("#searchDate").val($.datepicker.formatDate('dd-mm-yy', new Date(searchDate)));
          $("#searchDate").val(searchDate);
        }


    ////////////////
    $("#searchDateReplica").datepicker({
      dateFormat: 'MM yy',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      onClose: function() {
        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
        $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
        $("#searchDate").val($.datepicker.formatDate('dd-mm-yy', new Date(year, month, 1)));
        $("#searchDatee").hide();
      }


    });

    $("#searchDateReplica").focus(function () {
      $(".ui-datepicker-calendar").hide();
      $("#ui-datepicker-div").position({
        my: "center top",
        at: "center bottom",
        of: $(this)
      });
    });
    /////////////

    

    var firstRequest = "{{$firstRequest}}";
    if (firstRequest==1) {

      $("#dateTo").datepicker().datepicker("setDate", new Date());
    }


    /*Validation*/
    $("#search").click(function(event) {
      var searchInterestPayment = $("#searchInterestPayment").val();
      if ($("#searchDate").val()=="") {
        event.preventDefault();
        $("#searchDatee").show();
      }
      if ($("#dateTo").val()=="" && searchInterestPayment=="") {
        event.preventDefault();
        $("#dateToe").show();
      }
    });
    /*End Validation*/


    /*Merge the Bnak Names*/
    /*$(".bankName").each(function(index, el) {
      
      if (index>0) {
        if ($(".bankName").eq(index-1).html()==$(this).html()) {
          $(".bankName").eq(index-1).remove();
          $(this).attr('rowspan', 2);
        }
      }
    });*/
    /*End Merge the Bnak Names*/





  });
</script>






<script type="text/javascript">
  $(document).ready(function() {


   function pad (str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
  }

  $

  /* Change Project*/
  $("#searchProject").change(function(){

    var projectId = $(this).val();

    var csrf = "<?php echo csrf_token(); ?>";

    $.ajax({
      type: 'post',
      url: './famsAddProductOnChangeProject',
      data: {projectId:projectId,_token: csrf},
      dataType: 'json',
      success: function( data ){


        $("#searchProjectType").empty();
        $("#searchProjectType").prepend('<option selected="selected" value="">All</option>');


        var activeProjectTypes = jQuery.parseJSON("{{json_encode($acticeProjectTypesforSearch)}}");


        $.each(data['projectTypeList'], function (key, projectObj) {

          if ($.inArray(projectObj.id, activeProjectTypes)!=-1) {
            $('#searchProjectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");
          }


        });



      },
      error: function(_response){
        alert("error");
      }

    });/*End Ajax*/

  });/*End Change Project*/



  {{-- Print Page --}}

  $("#print").click(function(event) {

    $("#hiddenTitle").show();
    $("#hiddenInfo").show();
    $("#registerReportTable").removeClass('table table-striped table-bordered');

    var mainContents = document.getElementById("printingContent").innerHTML;

    var printStyle = '<style>#registerReportTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tr{ page-break-inside:avoid; page-break-after:auto } #registerReportTable tfooy tr td:nth-child(1){text-align:center;} </style><style media="print">@page{size:landscape;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style><style>#registerReportTable tbody tr td{text-align: center;}#registerReportTable tbody tr td.amount{text-align: right;padding-right: 2px;}#registerReportTable tbody tr td.name{text-align: left;padding-left: 2px;} tfoot tr td:nth-child(1){text-align:center} tfoot tr td{text-align: right;padding-right: 2px;} #registerReportTable tfoot tr td,#registerReportTable tbody tr.subTotal td{font-weight:bold;}</style>';


    var footerContents = "<div class='row' style='font-size:12px;padding-left:5px;'>Prepared By <span style='display:inline-block; width: 40%;'></span> Checked By <span style='display:inline-block; width: 40%;'></span> Approved By</div>";
    printContents = '<div id="order-details-wrapper">' + printStyle + mainContents + footerContents +'</div>';




    /*tr:nth-of-type(10n){page-break-after: always;}*/
       // @media print {.element-that-contains-table { overflow: visible !important; }}


  /*document.body.innerHTML = printStyle + printContents;
  window.print();*/

  var win = window.open('','printwindow');
  win.document.write(printContents);
  win.print();
  win.close();
});

  {{-- End Print Page --}} 

});

</script>




@endsection




