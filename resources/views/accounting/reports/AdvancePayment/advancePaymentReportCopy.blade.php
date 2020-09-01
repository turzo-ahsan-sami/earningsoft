@extends('layouts/acc_layout')
@section('title', '| VAT Register Report')

@section('content')
<script type="text/javascript">
    $("#loadingModal").hide();
</script>

<div class="row">
 <div class="col-md-12">
  <div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
              <button id="printIcon" class="btn btn-info pull-right print-icon" style="">
                 <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
              </button>
            </div>
          <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Advance Payment Report</h3>
        </div>

         <div class="panel-body panelBodyView" ><!--start of panel body-->
            <!-- Filtering Start-->
             <div class="row">
                 <div class="col-md-12">
                     <div class="row">
                       {{-- <div class="col-md-2"> --}}
                           {{-- <div class="row"> --}}
                               {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                               @if($userBranchId==1)   <!--start of Branch section-->
                               <div class="col-md-1" id="projectDiv" style="padding-left:20px;">
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'Project Name:', ['class' => 'control-label col-sm-12']) !!}
                                       <div class="col-sm-12">
                                           {!! Form::select('filProject', $projectList, null ,['id'=>'filProject','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required !important']) !!}
                                           <p id='filSupplierE' style="max-height:3px; color:red;"></p>
                                       </div>
                                   </div>
                               </div>
                               @endif   <!--end of Branch section-->

                               @if($userBranchId==1)   <!--start of Branch section-->
                               <div class="col-md-2" id="projectTypeDiv" style="padding-left:30px;">
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'Project Type :', ['class' => 'control-label col-sm-12']) !!}
                                       <div class="col-sm-12">
                                           {!! Form::select('filProjectType',$projectType,null,['id'=>'filProjectType','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required !important']) !!}
                                           <p id='filSupplierE' style="max-height:3px; color:red;"></p>
                                       </div>
                                   </div>
                               </div>
                               @endif   <!--end of Branch section-->
                               @if($userBranchId==1)   <!--start of Branch section-->
                               <div class="col-md-1" id="advanceTypeDiv" style="padding-left:20px;">
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'Advance Type:', ['class' => 'control-label col-sm-12']) !!}
                                       <div class="col-sm-12">
                                           {!! Form::select('filadvType', $advRegisterType, null ,['id'=>'filadvType','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required !important']) !!}
                                           <p id='filadvTypeE' style="max-height:3px; color:red;"></p>
                                       </div>
                                   </div>
                               </div>
                               @endif
                               @if($userBranchId==1)   <!--start of employee section-->
                               <div class="col-md-1" id="searchDiv" >
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'Search Type:', ['class' => 'control-label col-sm-12']) !!}
                                       <div class="col-sm-12">
                                         <select id="filSearchType" class="form-control input-sm" autocomplete="off" name="filSearchType">
                                             <option value="all">All</option>
                                             <option value="1">Employee</option>
                                             <option value="2">Supplier</option>
                                             <option value="3">House Owner</option>

                                         </select>
                                       </div>
                                   </div>
                               </div>
                               @endif

                               @if($userBranchId==1)   <!--start of employee section-->
                               <div class="col-md-1" id="employeeDiv" >
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'Employee:', ['class' => 'control-label col-sm-12','id'=>'filEmployeeL']) !!}
                                       <div class="col-sm-12">
                                           {!! Form::select('filEmployee', $employeeList, null ,['id'=>'filEmployee','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required !important']) !!}
                                           <p id='filEmployeeE' style="max-height:3px; color:red;"></p>
                                       </div>
                                   </div>
                               </div>
                               @endif
                               @if($userBranchId==1)   <!--start of Branch section-->
                               <div class="col-md-1" id="supplierDiv" >
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'Supplier:', ['class' => 'control-label col-sm-12','id'=>'filSupplierL']) !!}
                                       <div class="col-sm-12">
                                           {!! Form::select('filSupplier', $supplierList, null ,['id'=>'filSupplier','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required !important']) !!}
                                           <p id='filSupplierE' style="max-height:3px; color:red;"></p>
                                       </div>
                                   </div>
                               </div>
                               @endif   <!--end of supplier section-->
                               @if($userBranchId==1)   <!--start of Branch section-->
                               <div class="col-md-2" id="houseOwnerDiv" >
                                   <div class="form-group" style="font-size: 13px; color:#212F3C">
                                       {!! Form::label('', 'House Owner:', ['class' => 'control-label col-sm-12','id'=>'filHouseOwnerL']) !!}
                                       <div class="col-sm-12">
                                           {!! Form::select('filHouseOwner', $houseOwnerList, null ,['id'=>'filHouseOwner','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required !important']) !!}
                                           <p id='filHouseOwnerE' style="max-height:3px; color:red;"></p>
                                       </div>
                                   </div>
                               </div>
                               @endif   <!--end of supplier section-->

                               <?php $toDate= date("d-m-Y");?>

                               <div class="col-md-1" style="padding-left:30px;">
                                  <div class="form-group" style="font-size: 13px; color:#212F3C">
                                      {!! Form::label('from', 'From:', ['class' => 'col-sm-12 control-label']) !!}
                                      <div class="col-sm-12">
                                          {!! Form::text('filStartDate',$toDate,['class' => 'form-control input-sm','style'=>'cursor:pointer', 'id' => 'filStartDate', 'readonly','autocomplete'=>'off', 'autofocus', 'required'])!!}
                                          <p id='filStartDateE' style="max-height:3px; color:red;"></p>
                                      </div>
                                  </div>
                               </div>
                               <div class="col-md-1" style="padding-left:20px;">
                                  <div class="form-group" style="font-size: 13px; color:#212F3C">
                                      {!! Form::label('from', 'To:', ['class' => 'col-sm-12 control-label']) !!}
                                      <div class="col-sm-12">
                                          {!! Form::text('filEndDate',$toDate,  ['class' => 'form-control input-sm','style'=>'cursor:pointer', 'id' => 'filEndDate', 'readonly','autocomplete'=>'off', 'autofocus', 'required'])!!}
                                          <p id='filEndDateE' style="max-height:3px; color:red;"></p>
                                      </div>
                                  </div>
                               </div>
                               <div class="col-md-2" style="padding-left:20px;">
                                  <div class="form-group" style="font-size: 13px; padding: 4px 12px;">
                                      {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                      <div class="col-sm-12" style="padding-top: 13px;">
                                          {!! Form::submit('Search', ['id' => 'filteringFormSubmit', 'class' => 'btn btn-primary btn-s animated fadeInRight', 'style'=>'font-size:12px']); !!}
                                      </div>
                                  </div>
                               </div>
                               {!! Form::close()  !!}
                           {{-- </div> --}}
                       {{-- </div>     --}}
                     </div>
                 </div>
             </div>
             <div class="row">
              <div class="col-md-12"  id="reportingDiv">

              </div>
            </div>
       </div><!--end of panel body-->
     </div>
    </div>
   </div>
 </div>
</div>
<script type="text/javascript">
$(document).ready(function() {

  $("#employeeDiv").hide();
   $("#supplierDiv").hide();
    $("#houseOwnerDiv").hide();


  var errMsg="Please Select";
  var userBranchId="{{$userBranchId}}";
  var csrf = "{{csrf_token()}}";
  function toDate(dateStr) {
    var parts = dateStr.split("-");
    return new Date(parts[2], parts[1] - 1, parts[0]);
  }

 /* Date Range From */
  $("#filStartDate").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange : "2017:c",
    minDate: new Date(2017, 07 - 1, 01),
    maxDate: "dateToday",
    dateFormat: 'dd-mm-yy',
    onSelect: function () {
        $('#filStartDateE').hide();
        $("#filEndDate").datepicker("option","minDate",new Date(toDate($(this).val())));
        $( "#filEndDate" ).datepicker( "option", "disabled", false );
    }
  });
 /* Date Range From */


/* Date Range To */
 $("#filEndDate").datepicker({
    changeMonth: true,
    changeYear: true,
    yearRange : "2016:c",
    maxDate: "dateToday",
    dateFormat: 'dd-mm-yy',
    onSelect: function () {
        $('#filEndDateE').hide();
    }
  });

 $("#filProject").change(function(event){
    var projectAjax=$("#filProject").val();
   $.ajax({
    type: 'post',
    url: "./advancePaymentReportProjectTypeAjax",
    data: {projectAjax: projectAjax},
    success: function(data) {
       console.log(data);
       $("#filProjectType").empty();

       $("#filProjectType").append("<option value='all'>All</option>");
      $.each(data, function( key,obj){
        //alert(JSON.stringify(obj));
          $("#filProjectType").append("<option value='"+obj.id+"'>"+obj.name+"</option>");
      });

    },
    error: function(argument) {

    }

  });
 });

 $("#filSearchType").change(function(event){
     var filSearchTypeVal =  $("#filSearchType").val();
     if(filSearchTypeVal == "1")
     {
         $("#employeeDiv").show();
         $("#supplierDiv").hide();
         $('#filSupplier').val("all");
         $("#houseOwnerDiv").hide();
         $('#filHouseOwner').val("all");
     }
     else if(filSearchTypeVal == "2")
     {
         $("#employeeDiv").hide();
         $('#filEmployee').val("all");
         $("#supplierDiv").show();
         $("#houseOwnerDiv").hide();
         $('#filHouseOwner').val("all");
     }
     else if(filSearchTypeVal == "3")
     {
         $("#employeeDiv").hide();
         $('#filEmployee').val("all");
         $("#supplierDiv").hide();
         $('#filSupplier').val("all");
         $("#houseOwnerDiv").show();
     }
     else
     { $("#employeeDiv").hide();
     $('#filEmployee').val("all");
     $("#supplierDiv").hide();
     $('#filSupplier').val("all");
     $("#houseOwnerDiv").hide();
     $('#filHouseOwner').val("all");

 }

});





// ==============================================================================================================
// ==============================================Starts Form Submit==============================================

$("#filteringFormId").submit(function( event ) {
       event.preventDefault();
       var serializeValue=$(this).serialize();
       $('#loadingModal').show();
       $("#reportingDiv").load('{{URL::to("./advancePaymentReportLowerPart")}}'+'?'+serializeValue);
       $("#loadingModal").hide();

   });

   $("#printIcon").click(function(event) {

       // $("#reportingTable").removeClass('table table-striped table-bordered');

       // var printStyle = '<style>#reportingTable{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;page-break-inside:auto; font-family: arial!important;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px} tr{ page-break-inside:avoid; page-break-after:auto } table tbody tr td.amount{text-align:right !important;}</style><style media="print">@page{size: A4 portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

        // var printStyle =
        // '<style>#reportingTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child{ font-weight: bold;}thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:right;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto }tbody tr td:nth-child(2){text-align:left !important;}tbody tr td:nth-child(3){text-align:left !important;}tbody tr td:nth-child(1){text-align:center !important;}</style>   <style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

        var printStyle =
        '<style>#reportingTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:right;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto }tbody tr td:nth-child(2){text-align:left !important;}tbody tr td:nth-child(3){text-align:left !important;}tbody tr td:nth-child(1){text-align:left !important;}</style>   <style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

       var mainContentsUpper = document.getElementById("printDivUpper").innerHTML;
       var mainContents1 = document.getElementById("printDiv1").innerHTML;
       var mainContents2 = document.getElementById("printDiv2").innerHTML;
       var mainContents3 = document.getElementById("printDiv3").innerHTML;

       var headerContents = '';

       // var paddingDiv  = "<div class='row' style='padding-top:20px !important;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>";


       // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-right:5px; padding-top:40px ;'>Prepared By <span style='display:inline-block; width: 36%; padding-top:40px ;'></span> Checked By <span style='display:inline-block; width: 35%; padding-top:40px ;'></span> Approved By</div>";
//
       // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px;'></span> Approved By</div>";

       var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

       // var footerContents = "<div class='row' style='font-size:12px;'> Prepared By <span style='display:inline-block; width: 34%;'></span> Checked By <span style='display:inline-block; width: 34%;'></span> Approved By</div>";

       var printContents = '<div id="order-details-wrapper"  style="padding: 10px;">' + headerContents + mainContentsUpper+printStyle+'<div>Employee'+mainContents1+'</div> </br></br><div>Supplier'+mainContents2+'</div></br></br><div>House Owner'+mainContents3+'</div>'+footerContents +'</div>';


       var win = window.open('','printwindow');
       win.document.write(printContents);
       win.print();
       // $("#reportingTable").addClass('table table-striped table-bordered');
       win.close();


   });

})
</script>


@endsection
