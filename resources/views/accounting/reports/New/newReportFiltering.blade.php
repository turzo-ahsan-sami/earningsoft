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
*  Topic: New Statement Report                    *
***********************************************!-->

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
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">New Account Statement Report</h3>
            </div>






            <div class="panel-body panelBodyView" ><!--start of panel body-->

                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                          {{-- <div class="col-md-2"> --}}
                              {{-- <div class="row"> --}}
                                  {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                                 <!--start of Branch section-->
                                 <?php $hello=array("--Select--"); ?>
                                 <div class="col-md-1" id="branchDiv">
                                     <div class="form-group" style="font-size: 13px; color:#212F3C">
                                         {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                         <div class="col-sm-12">
                                             {!! Form::select('filBranch',$hello , null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                             <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                         </div>
                                     </div>
                                 </div>
                                 <!--end of Branch section-->



                                   <!--start of Account Number section-->

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

  var errMsg="Please Select";

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



// ==============================================================================================================
// ==============================================Starts Form Submit==============================================


})
</script>




@endsection
