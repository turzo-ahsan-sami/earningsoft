@extends('layouts/acc_layout')
@section('title', '| Advance register Report')
@section('content')
@include('successMsg')


@php
$projectSelected = isset($_GET['searchproject']) ? $_GET['searchproject'] : null;
$projectTypeSelected = isset($_GET['searchProjectType']) ? $_GET['searchProjectType'] : null;


   // var_dump($searchProjectType);

@endphp

<div class="row">
 <div class="col-md-12">
  <div class="" style="">
   <div class="">
    <div class="panel panel-default" style="background-color:#708090;">
     <div class="panel-heading" style="padding-bottom:0px">
      <div class="panel-options">
       {{--    <a href="" class="btn btn-info pull-right addViewBtn" id="print" media="print"><i class="fa fa-print" aria-hidden="true"></i> Print</a> --}}

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


    <div class="row" id="filtering-group">
      <div class="form-horizontal form-groups" style="padding-right: 0px;">
       {!! Form::open(['url' => 'advanceRegister','method' => 'get']) !!}

       <div class="col-md-1">
         <div class="form-group" style="font-size: 13px; color:black;">

          <div style="text-align: center;" class="col-sm-12">
            {!! Form::label('', 'Project:', ['class' => 'control-label pull-left']) !!}
          </div>

          <div class="col-sm-12">

            <select name="searchproject" class="form-control input-sm" id="searchProject">

             <option value="">All</option>
             @foreach($searchProject as $searchProject)
             <option value="{{$searchProject->id}}"@if($searchProject->id==$projectSelected){{"selected=selected"}}@endif>{{$searchProject->name}}</option>
             @endforeach

           </select>



         </div>
       </div>
     </div>
     <div class="col-md-1">
       <div class="form-group" style="font-size: 13px; color:black;">

        <div style="text-align: center;" class="col-sm-12">
          {!! Form::label('', 'Project Type:', ['class' => 'control-label pull-left']) !!}
        </div>

        <div class="col-sm-12">

          <select name="searchProjectType" class="form-control input-sm" id="searchProjectType">
            <option value="">All</option>
            @foreach($searchProjectType as $searchProjectType)
            <option value="{{$searchProjectType->id}}"@if($searchProjectType->id==$projectTypeSelected){{"selected=selected"}}@endif>{{$searchProjectType->name}}</option>
            @endforeach

          </select>



        </div>
      </div>
    </div>




    <div class="col-md-1">
     <div class="form-group" style="font-size: 13px; color:black;">

      <div style="text-align: center;" class="col-sm-12">
        {!! Form::label('', 'Advance Type:', ['class' => 'control-label pull-left']) !!}
      </div>

      <div class="col-sm-12">

        <select name="searchRegisterType" class="form-control input-sm" id="searchRegisterType">

         <option value="">All</option>
         @foreach($searchRegisterType as $serchRegisterType)
         <option value="{{$serchRegisterType->id}}" @if($serchRegisterType->id==$regTypeSelected){{"selected=selected"}}@endif>{{$serchRegisterType->name}}</option>
         @endforeach

       </select>



     </div>
   </div>
 </div>


 <div class="col-md-1">
  <div class="form-group" style="font-size: 13px; color:black;">
   <div style="text-align: center;" class="col-sm-12">
    {!! Form::label('', 'Category:', ['class' => 'control-label pull-left']) !!}
  </div>

  @php
  $searchcategoryList = array(''=>'All','1'=>'House Owner','2'=>'Supplier','3'=>'Employeer');
  @endphp

  <div class="col-sm-12">
   {!! Form::select('searchcategory',$searchcategoryList,$categorySelected,['id'=>'searchcategory','class'=>'form-control input-sm']) !!}

 </div>
</div>
</div>



<div class="col-md-1">
  <div class="form-group" style="font-size: 13px; color:black;">
   <div style="text-align: center;" class="col-sm-12">
    {!! Form::label('', 'Search By:', ['class' => 'control-label pull-left']) !!}
  </div>
  <div class="col-sm-12">
    {!! Form::select('searchMethod',[''=>'Please Select','1'=>'Fiscal Year','2'=>'Current Year','3'=>'Date Range'],$searchMethodSelected,['id'=>'searchMethod','class'=>'form-control input-sm']) !!}

  </div>
</div>
</div>


<div class="col-md-2" style="display: none;" id="fiscalYearDiv">
 <div class="form-group" style="font-size: 13px; color:black">
  {!! Form::label('', ' ', ['class' => 'control-label col-sm-12']) !!}
  <div class="col-sm-12" style="padding-top: 18px;">
   {!! Form::select('fiscalYear', $fiscalYears, $fiscalYearSelected, array('class'=>'form-control input-sm', 'id' => 'fiscalYear')) !!}
 </div>
</div>
</div>

<div class="col-md-2" style="display: none;" id="dateRangeDiv">
 <div class="form-group" style="font-size: 13px; color:black">
  <div style="text-align: center;" class="col-sm-12">
    {!! Form::label('', ' ', ['class' => 'control-label']) !!}
  </div>

  <div class="col-sm-12" style="padding-top: 7px;">
    <div class="form-group">
     <div class="col-sm-6">
       {!! Form::text('dateFrom',$dateFromSelected,['id'=>'dateFrom','placeholder'=>'From','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
       <p id="dateFrome" style="color: red;display: none;">*Required</p>
     </div>
     <div class="col-sm-6" id="dateToDiv">
       {!! Form::text('dateTo',$dateToSelected,['id'=>'dateTo','placeholder'=>'To','class'=>'form-control input-sm','readonly','style'=>'cursor:pointer']) !!}
       <p id="dateToe" style="color: red;display: none;">*Required</p>
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

</div> {{--End Filtering Group --}}




<h2 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;margin-top: 0px;">Advance Register  Report</h2>

</div>
<div class="panel-body panelBodyView">
  <div>

  </div>


  @if(!$firstRequest )
  <div id="printDiv">
    <div id="printingContent">

      <div style="display: none;text-align: center;" id="hiddenTitle">
       <h3 style="text-align: center;padding: 0px;margin: 0px;">Ambala Foundation</h3>
       <p style="text-align: center;padding: 0px;margin: 0px;font-size: 10px;">House # 62, Block # Ka, Piciculture Housing Society, Shyamoli, Dhaka-1207</p>
       <h4 style="text-align: center;padding: 0px;margin: 0px;">Advance Register Report</h4>
       {{-- <h5 style="text-align: center;">{{$selectedBranchName}}</h5>  --}}
       <h5 style="text-align: center;font-size: 14;padding: 0px;margin: 0px;">{{date('F d, Y',strtotime($endDate))}}</h5>
     </div>
     <div id="hiddenInfo" style="display: none;">

      <!-- project selected-->

      <p style="padding: 0px;margin: 0px;font-size: 11px;">Project:


        @if($projectSelected==null)
        {{'All'}}

        @else
        @php
        $project = DB::table('gnr_project')->where('id',$projectSelected)->value('name');
        @endphp


        {{$project}};

        @endif {{--project selected end--}}
      </p>

      <p style="padding: 0px;margin: 0px;font-size: 11px;">Project Type :


        @if($projectTypeSelected==null)
        {{'All'}}

        @else
        @php
        $projectTypeName = DB::table('gnr_project_type')->where('id',$projectTypeSelected)->value('name');
        @endphp


        {{$projectTypeName}};

        @endif {{--Project Type end php--}}

      </p>

      {{--Register Type start php--}}
      <p style="padding: 0px;margin: 0px;font-size: 11px;">Register Type :


        @if($regTypeSelected==null)
        {{'All'}}

        @else
        @php
        $reTypeName = DB::table('acc_adv_register_type')->where('id',$regTypeSelected)->value('name');
        @endphp


        {{$reTypeName}};

        @endif {{--Register Type end php--}}
        <span style='float: right;'>
         Reporting Peroid : {{date('d-m-Y',strtotime($startDate))." to ".date('d-m-Y',strtotime($endDate))}}
       </span>
     </p>
     {{--Register Category start php--}}
     <p style="padding: 0px;margin: 0px;font-size: 11px;">Register Category:
      @if($categorySelected==null)
      {{'All'}}

      @elseif($categorySelected==1)
      {{'House Owner'}}

      @elseif($categorySelected==2)
      {{'Supplier'}}

      @elseif($categorySelected==3)
      {{'Employee'}}

      @endif
      {{--Register Category end php--}}

      <span style='float: right;'>
        Print Date : {{date('F d,Y')}}
      </span>

    </p>


  </div>

  <br>
  <div id="tableDiv" style="overflow: visible !important;">

    <table id="advanceRegisterReport" width="100%" class="table table-striped table-bordered" style="color:black;font-size:11px;margin-bottom:50px;border-collapse: collapse;" border= "1px solid black;" cellpadding="0" cellspacing="0">


      <thead valign="center">

        <tr style="height:30px;">
          <th width="80px">SL#</th>
          <th>Date</th>
          <th>Name</th>
          <th>Project Name</th>
          <th>Amount (Tk)</th>
        </tr>

      </thead>

      <tbody>



        @php
        $totalAmount= 0;
        @endphp


        @foreach ($registerType as $reg)
        <tr>

         <td style="text-align:left; padding-left:7px; font-weight: bold;" colspan="5">{{$reg->name}}:</td>
       </tr>

       @php
       $subtotal= 0;
       $index=1;
       @endphp


       @foreach($accAdvRegister as $accAdvReg)

       @php

       $project= DB::table('gnr_project')->where('id',$accAdvReg->projectId)->value('name');
       $projectType= DB::table('gnr_project_type')->where('id',$accAdvReg->projectTypeId)->value('name');


       $result='';

       if($accAdvReg->houseOwnerId){
        $houseOwner= DB::table('gnr_house_Owner')->where('id',$accAdvReg->houseOwnerId)->value('houseOwnerName');
        $result=$houseOwner;

      }

      elseif($accAdvReg->supplierId){
        $supplir=DB::table('gnr_supplier')->where('id',$accAdvReg->supplierId)->value('name');
        $result =$supplir;

      }

      elseif($accAdvReg->employeeId){
        $employee = DB::table('hr_emp_general_info')->where('id',$accAdvReg->employeeId)->value('emp_name_english');
        $result =$employee;
      }

      @endphp


      @if($reg->id==$accAdvReg->advRegType)



      <tr>
       <td>{{$index++}} </td>

       <td style="text-align: center;">{{date('d-m-Y',strtotime($accAdvReg->advPaymentDate))}}</td>
       <td style="text-align: left;padding-left: 5px;">{{$result}} </td>

       <td style="text-align: left;padding-left: 5px;">{{$project}} </td>

       <td style="text-align: right;padding-right: 5px;">{{number_format($accAdvReg->amount,2)}}</td>

     </tr>


     @php $subtotal=$subtotal+$accAdvReg->amount; @endphp



     @endif

     @endforeach


     <tr class="totalRow" style="background-color: #808080 !important;font-size: 12px;">
      <td colspan="4" style="text-align: center;font-weight: bold;">Sub Total</td>                                         <td style="text-align: right;padding-right: 5px;font-weight: bold;">{{number_format($subtotal,2)}}</td>
    </tr>


    @php $totalAmount=$totalAmount+$subtotal; @endphp

    @endforeach

    <tr class="totalRow" style="background-color: #8d9096 !important;font-size: 12px;">

     <td colspan="4" style="text-align:center; padding:10px;font-weight:bold; font-size:15px;">Total</td>                                   <td style="text-align:right;padding-right: 5px;font-weight: bold;">{{number_format($totalAmount,2)}}</td>
   </tr>



 </tbody>
</table>
</div>
<div>
  @endif
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<script type="text/javascript">
  $(document).ready(function(){

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
          download: "Advance Register Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });


    function pad (str, max) {
      str = str.toString();
      return str.length < max ? pad("0" + str, max) : str;
    }

    $("#searchProject").change(function(){

     var searchProject = $(this).val();
     var csrf = "<?php echo csrf_token(); ?>";

     $.ajax({
      type: 'post',
      url: './advRegChangeProjectType',
      data: {projectId:searchProject,_token: csrf},
      dataType: 'json',
      success: function( data ){


       $("#searchProjectType").empty();
       $("#searchProjectType").prepend('<option selected="selected" value="">All</option>');




       $.each(data['projectTypeList'], function (key, projectObj) {

         $('#searchProjectType').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");
       });




     },
     error: function(_response){
      alert("error");
    }

  });/*End Ajax*/

   });/*End Change Project*/
  });
</script>


<script type="text/javascript">
  $(document).ready(function() {

    function toDate(dateStr) {
     var parts = dateStr.split("-");
     return new Date(parts[2], parts[1] - 1, parts[0]);
   }

   /* Date Range From */
   $("#dateFrom").datepicker({
     changeMonth: true,
     changeYear: true,
     yearRange : "2010:c",
     minDate: new Date(2010, 07 - 1, 01),
     maxDate: "dateToday",
     dateFormat: 'dd-mm-yy',
     onSelect: function () {
       $('#dateFrome').hide();
       $("#dateTo").datepicker("option","minDate",new Date(toDate($(this).val())));
     }
   });
   /* Date Range From */

// triggering minimum date in date to
// $('.ui-datepicker-current-day').click();

/* Date Range To */
$("#dateTo").datepicker({
 changeMonth: true,
 changeYear: true,
 yearRange : "2010:c",
 minDate: new Date(2010, 07 - 1, 01),
 maxDate: "dateToday",
 dateFormat: 'dd-mm-yy',
 onSelect: function () {
   $('#dateToe').hide();
   $("#dateFrom").datepicker("option","maxDate",new Date(toDate($(this).val())));
 }
});

/* End Date Range To */


var dateFromData = $("#dateFrom").val();

if (dateFromData!="") {
  $("#dateTo").datepicker("option","minDate",new Date(toDate(dateFromData)));

  $("input:radio[name='searchRadio']").click(function() {
   var value = $(this).val();
   if (value==1) {

   }
   if (value==2) {

   }
 });

}


});/*End Doc Ready*/

</script>

{{-- Filtering Mehod --}}
<script type="text/javascript">
  $(document).ready(function() {

    $("#searchMethod").change(function(event) {

      var searchMethod = $(this).val();
      if (searchMethod=="") {
        $("#fiscalYearDiv").hide();
        $("#dateRangeDiv").hide();
      }
              //Fiscal Year
              else if(searchMethod==1){
                $("#fiscalYearDiv").show();
                $("#dateRangeDiv").hide();
              }

              //Current Year
              else if(searchMethod==2){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                var d = new Date();
                var year = d.getFullYear();
                var month = d.getMonth();
                if (month<=5) {
                  year--;
                  month = 6;
                }
                else{
                  month = 6;
                }
                d.setFullYear(year, month, 1);

                $("#dateFrom").datepicker("option","minDate",new Date(d));
                $("#dateTo").datepicker("option","minDate",new Date(d));
                $("#dateFrom").datepicker("setDate", new Date(d));
                $("#dateFrom").hide();
                $("#dateFrome").hide();
                $("#dateToDiv").attr("class", "col-sm-12");
              }

              //Date Range
              else if(searchMethod==3){
                $("#fiscalYearDiv").hide();
                $("#dateRangeDiv").show();
                $("#dateToDiv").attr("class", "col-sm-6");
                $("#dateFrom").show();
                //$("#dateFrom").val("");
                $("#dateFrom").datepicker("option","minDate",new Date(Date.parse("1998-01-01")));
              }
            });
    $("#searchMethod").trigger('change');
  });
</script>
{{-- End Filtering Mehod --}}

<script type="text/javascript">
  $(document).ready(function() {
        //$('body').width( "3000" );
      });
    </script>






    {{-- Print Page --}}
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $("#print").click(function(event) {

          $("#hiddenTitle").show();
          $("#hiddenInfo").show();
          $("#advanceRegisterReport").removeClass('table table-striped table-bordered');

          var mainContents = document.getElementById("printingContent").innerHTML;
          var headerContents = '';

          var footerContents = "<div class='row' style='font-size:12px;'>Prepared By <span style='display:inline-block; width: 36%;'></span> Checked By <span style='display:inline-block; width: 36%;'></span> Approved By</div>";



          var printStyle = '<style>#advanceRegisterReport{} #advanceRegisterReport tr:last-child { font-weight: bold;font-size:13px;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:center;vertical-align: middle;padding:3px;font-size:10px} tr:last-child { font-weight: bold;font-size:13px;} #advanceRegisterReport tbody tr{ page-break-inside:avoid; page-break-after:auto } </style><style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print { #tableDiv { -moz-overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

          printContents = '<div id="order-details-wrapper" style="-moz-overflow: visible !important;"> ' + printStyle + mainContents + footerContents +'</div>';


          var win = window.open('','printwindow');
          win.document.write(printContents);
          win.print();
          win.close();
        });
      });
    </script>
    {{-- EndPrint Page --}}

    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $("#search").click(function(event) {


          if ($("#searchMethod").val()==2 || $("#searchMethod").val()==3) {

            if ($("#dateFrom").val()=="") {
              event.preventDefault();
              $("#dateFrome").show();
            }
            if ($("#dateTo").val()=="") {
              event.preventDefault();
              $("#dateToe").show();
            }

          }


        });
      });
    </script>

    <style type="text/css">

    #filtering-group input{
      height: auto;

      border-radius: 5px;
    }

    #filtering-group select{height:auto; border-radius: 5px;}

    .row-name{text-align: left;padding-left: 15px;}
    .row-amount{text-align: right;padding-right: 15px;}

    #famsAssetRegisterReportTable tr.totalRow td{text-align: right;padding-right: 15px;font-weight: bold;}
  </style>

  <style type="text/css">
  @media print {
   thead {display: table-header-group;}
 }
</style>


<style type="text/css">


/*#stockViewTable tbody tr td.fotBgColor{
    background-color:  #cceeff !important;
    }*/



    #filtering-group input{
      height:25px;
      border-radius: 0px;
    }

    #filtering-group select{height:25px; border-radius: 0px;}

    .dataTables_filter, .dataTables_info { display: none; }
    /*.stockViewTable_length, .dataTables_paginate { display: none; }  */
  </style>

  <style type="text/css" media="print">
  @media print {
   thead {display: table-header-group;}
 }
</style>

<style type="text/css">
.table thead tr th {
  border: 1px solid white;
  border-bottom: 1px solid red;
  border-collapse: separate;
  _background-color: transparent!important;
  border-bottom: 0 !important;
  position: static !important;
}
</style>
<style type="text/css">
#advanceRegisterReport thead tr th{padding-top: 7px;}
</style>




@endsection
