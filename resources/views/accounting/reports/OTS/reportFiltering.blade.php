@extends('layouts/acc_layout')
@section('title', '| OTS Account Statement Report')

@section('content')

<style type="text/css">
#trialBalanceReportTable{
  font-family: arial !important;
}
</style>

<!--***********************************************
* Programmer: Himel Dey                           *
*  Ambala IT                                      *
*  Topic: OTS Statement Report                    *
***********************************************!-->

<div class="row">
  <div class="col-md-12">
    <div class="" style="">
      <div class="">
        <div class="panel panel-default" style="background-color:#708090;">

          <div class="panel-heading" style="padding-bottom:0px">
            <div class="panel-options">
             {{--  <button id="printIcon" class="btn btn-info pull-right print-icon" style="">
                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
              </button> --}}

              <button id="printIcon" class="btn btn-info pull-left print-icon" style="">
                <i class="fa fa-print fa-lg" aria-hidden="true"> Print</i>
              </button>

              <button id="btnExportExcel" class="btn btn-info pull-center print-icon"  target="_blank" style="">
                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Excel</i>
              </button>

              <button  id="btnExportPdf" class="btn btn-info pull-right print-icon"  target="_blank" style="">
                <i class="fa-file-excel-o fa-lg" aria-hidden="true"> Pdf</i>
              </button>
            </div>
            <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">OTS Account Statement Report</h3>
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
                      <div class="col-md-1" id="branchDiv">
                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                          {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                          <div class="col-sm-12">
                            {!! Form::select('filBranch', $branchList, null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                            <p id='filBranchE' style="max-height:3px; color:red;"></p>
                          </div>
                        </div>
                      </div>
                      @endif   <!--end of Branch section-->



                      <!--start of Account Number section-->
                      <div class="col-md-2" id="branchDiv">
                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                          {!! Form::label('', 'Acc No:', ['class' => 'control-label col-sm-12']) !!}
                          <div class="col-sm-12">
                            {!! Form::select('filAccountNumber', $otsAccount, null ,['id'=>'filAccountNumber','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                            <p id='filAccountNumberE' style="max-height:3px; color:red;"></p>
                          </div>
                        </div>
                      </div>
                      <!--end of Account Number section-->



                      <?php $toDate= date("d-m-Y");?>

                      <div class="col-md-1">
                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                          {!! Form::label('from', 'From:', ['class' => 'col-sm-12 control-label']) !!}
                          <div class="col-sm-12">
                            {!! Form::text('filStartDate', $toDate, ['class' => 'form-control input-sm','style'=>'cursor:pointer', 'id' => 'filStartDate', 'readonly','autocomplete'=>'off', 'autofocus', 'required'])!!}
                            <p id='filStartDateE' style="max-height:3px; color:red;"></p>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-1">
                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                          {!! Form::label('from', 'To:', ['class' => 'col-sm-12 control-label']) !!}
                          <div class="col-sm-12">
                            {!! Form::text('filEndDate', $toDate, ['class' => 'form-control input-sm','style'=>'cursor:pointer', 'id' => 'filEndDate', 'readonly','autocomplete'=>'off', 'autofocus', 'required'])!!}
                            <p id='filEndDateE' style="max-height:3px; color:red;"></p>
                          </div>
                        </div>
                      </div>

                      {{-- <div class="col-md-2"></div> --}}

                      <div class="col-md-2">
                        <div class="form-group" style="font-size: 13px; padding: 4px 12px;">
                          {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                          <div class="col-sm-12" style="padding-top: 13px;">
                            {!! Form::submit('Search', ['id' => 'filteringFormSubmit', 'class' => 'btn btn-primary btn-s animated fadeInRight', 'style'=>'font-size:12px']); !!}
                          </div>
                        </div>
                      </div>

                      <div class="col-md-2"></div>






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
          download: "OTS Account Statement Report_"+ today + ".xls"}).appendTo("body").get(0).click();
        e.preventDefault();
      });

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
      yearRange : "2010:c",
      minDate: new Date(2010, 07 - 1, 01),
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
      yearRange : "2010:c",
      minDate: new Date(2010, 07 - 1, 01),
      maxDate: "dateToday",
      dateFormat: 'dd-mm-yy',
      onSelect: function () {
        $('#filEndDateE').hide();
        $("#filStartDate").datepicker("option","maxDate",new Date(toDate($(this).val())));
      }
    });



// ==============================================================================================================
// ==============================================Starts Form Submit==============================================

$("#filteringFormId").submit(function( event ) {
 event.preventDefault();

 if(userBranchId==1){
  var branchValue=$('#filBranch').val();
  if(branchValue!=0)
    {$('#filBranchE').hide();}
  else{$('#filBranchE').show();$('#filBranchE').html(errMsg);return false;}


}




var AccountNumber=$('#filAccountNumber').val();
console.log(AccountNumber);

if(AccountNumber!=0){$('#filAccountNumberE').hide();}
else{$('#filAccountNumberE').show();$('#filAccountNumberE').html(errMsg);return false;}


var serializeValue=$(this).serialize();
$("#reportingDiv").load('{{URL::to("./otsAccountStatementReportLowerPart")}}'+'?'+serializeValue);

});

$("#printIcon").click(function(event) {

       // $("#reportingTable").removeClass('table table-striped table-bordered');

       // var printStyle = '<style>#reportingTable{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;page-break-inside:auto; font-family: arial!important;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px} tr{ page-break-inside:avoid; page-break-after:auto } table tbody tr td.amount{text-align:right !important;}</style><style media="print">@page{size: A4 portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

       var printStyle =
       '<style>#reportingTable{float:left;height:auto;padding:0px;width:100%;font-size:11px;border:1pt solid ash;page-break-inside:auto;font-family: arial!important;} tr:last-child{ font-weight: bold;}thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;} tbody tr td { text-align:right;vertical-align: middle;padding:3px ;font-size:10px;} tr{ page-break-inside:avoid; page-break-after:auto }tbody tr td:nth-child(2){text-align:left !important;}tbody tr td:nth-child(3){text-align:left !important;}tbody tr td:nth-child(1){text-align:center !important;}</style><style>#information tbody tr td:nth-child(6){width:100px !important;} #information tbody tr td:nth-child(1){padding-left:0px;width:80px; text-align:left !important;} #information tbody tr td:nth-child(4){text-align:left !important;} </style> <style media="print">@page{size:portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

       var mainContents = document.getElementById("printDiv").innerHTML;
       var headerContents = '';

       // var paddingDiv  = "<div class='row' style='padding-top:20px !important;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>";


       // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-right:5px; padding-top:40px ;'>Prepared By <span style='display:inline-block; width: 36%; padding-top:40px ;'></span> Checked By <span style='display:inline-block; width: 35%; padding-top:40px ;'></span> Approved By</div>";
//
       // var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px;'></span> Approved By</div>";

       var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By <span style='display:inline-block; width: 35%; padding-top:40px;'></span> Checked By <span style='display:inline-block; width: 34%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

       // var footerContents = "<div class='row' style='font-size:12px;'> Prepared By <span style='display:inline-block; width: 34%;'></span> Checked By <span style='display:inline-block; width: 34%;'></span> Approved By</div>";

       var printContents = '<div id="order-details-wrapper"  style="padding: 10px;">' + headerContents + printStyle + mainContents /*+ paddingDiv*/ + footerContents +'</div>';


       var win = window.open('','printwindow');
       win.document.write(printContents);
       win.print();
       // $("#reportingTable").addClass('table table-striped table-bordered');
       win.close();


     });



$("#filBranch").click(function(event){
  var branchValue=$('#filBranch').val();




  $.ajax({
    type: 'post',
    url: "./otsAccountStatementReportAccountingList",
    data: {branchValue:branchValue},
    success: function (data){

      console.log(data);

      $("#filAccountNumber").empty();
      $.each(data, function( key,obj){

        $('#filAccountNumber').append("<option value='"+obj.id+"'>"+obj.accNo+"-"+obj.name+"</option>");
      });



    },
    error:  function (data){

    }

  });
});






})
</script>


@endsection
