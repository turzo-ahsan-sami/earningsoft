@extends('layouts/acc_layout')
@section('title', '| Add Budget')
@section('content')

    <style type="text/css">
    #budgetTable{
        font-family: arial !important;
    }
</style>
@php
    $branch = DB::table('gnr_branch')->where('id',Auth::user()->branchId)->select('id','branchCode')->first();
    $userBranchCode = $branch->branchCode;
@endphp

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
                        <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Add Revised Budget</h3>
                    </div>

                    <div class="panel-body panelBodyView" ><!--start of panel body-->

                        <div class="viewTitle">
                            <a href="{{url('/viewRevisedBudget')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                            </i>Revised Budget List</a>
                        </div>

                        <!-- Filtering Start-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    {!! Form::open(array('url' => './loadRevisedBudget', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                                   <div class="col-md-2" id="projectDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('projectId', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('projectId', $projects, null ,['id'=>'projectId','class'=>'form-control input-sm','autocomplete'=>'off']) !!} 
                                                <p id='projectIdPVe' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-2" id="branchDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            <div class="col-md-12">
                                                {!! Form::label('', 'Branch:', ['class' => 'control-label pull-left']) !!}
                                            </div>
                                            <select name="branchId" class="form-control input-sm" id="branchId">
                                                <option value="">All (with HO)</option>
                                            </select>
                                            <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>

                                    <div class="col-md-1 hidden" id="projectTypeDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-md-12">
                                                {!! Form::select('filProjectType', $projectTypes, null ,['id'=>'filProjectType','class'=>'form-control input-sm']) !!}
                                                <p id='filProjectTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>



                                    {{--  acc project div --}}
                                    <div class="col-md-2 id="accountTypeDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Account Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                <select class="form-control" name="filAccountType" id="filAccountType">
                                                    {{-- <option value="">All</option> --}}
                                                    @foreach ($accLedgerTypes as $key => $name)
                                                        <option value={{ $key }}  >{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                                <p id='filAccountTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-2" id="fiscalYearDiv">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('fiscalYearId', 'Fiscal Year', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('fiscalYearId',$fiscalYear, null, array('class'=>'form-control input-sm', 'id' => 'fiscalYearId','readonly'=>'readonly')) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; padding: 4px 12px;">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 13px;">
                                                {!! Form::submit('Proceed', ['id' => 'filteringFormSubmit', 'class' => 'btn btn-primary btn-s animated fadeInRight', 'style'=>'font-size:12px']); !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2"></div>

                                    {!! Form::close() !!}
                                    {{-- </div> --}}
                                    {{-- </div>     --}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12"  id="reportingDiv"></div>
                        </div>
                    </div><!--end of panel body-->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var userBranchId = "{{ Auth::user()->branchId }}";
        var userBranchCode = "{{ $branch->branchCode }}";
        var projectId = $('#projectId').val();
        var branchId = $('#branchId').val();

        if (userBranchCode == 0) {
            getProjectTypesNBranches(projectId);
        }

        getChildrenLedgers(projectId, branchId);
        $('#projectId').change(function (event){
            var projectId = $('#projectId').val();
            //alert(projectId);
            getProjectTypesNBranches(projectId);
            var branchId = $('#branchId').val();
            getChildrenLedgers(projectId, branchId);
        });

        function getProjectTypesNBranches(projectId) {

            var csrf = "{{ csrf_token() }}";
            // alert(projectId);

            if (projectId == 0) {
                $("#branchId").empty();
                $("#branchId").append('<option value="">Select Branch</option>');
              

                $("#filProjectType").empty();
                $('#filProjectType').append("<option value='0'>Select Project</option>");
            }
            else {
                $.ajax({
                    type: 'post',
                    url: "./getProjectTypesNBranches",
                    data: {projectId: projectId , _token: csrf},
                    dataType: 'json',
                    success: function (data){
                        $("#filProjectType").empty();
                        // $('#filProjectType').append("<option value='0'>All</option>");
                        $.each(data['projectTypes'], function( key,obj){
                            $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        });

                        $("#branchId").empty();
                        $("#branchId").append('<option value="">All(With HO)</option>');
                        // $("#branchId").append('<option value="0">All Branches</option>');
                        $("#branchId").append('<option value="{{ $userBranchData->id }}">{{ $userBranchData->nameWithCode }}</option>');

                        $.each(data['branches'], function(index,val){
                            $('#branchId').append("<option value='"+index+"'>"+val+"</option>");
                        });

                    },
                    error:  function (data){

                    }
                });
            }
        }
        
        function getChildrenLedgers(projectId, branchId) {
            var csrf = "{{ csrf_token() }}";
            // alert(projectId, branchId);

            $.ajax({
                type: 'post',
                url: "./getChildrenLedgers",
                data: {projectId: projectId, branchId: branchId, _token: csrf},
                dataType: 'json',
                success: function (data){

                    $("#ledgerId").empty();
                    $("#ledgerId").append('<option value="">Select Ledger</option>');
                    $.each(data, function(index, obj){
                        $('#ledgerId').append("<option value='"+obj.id+"'>"+obj.code+' - '+obj.name+"</option>");
                    });

                },
                error:  function (data){

                }
            });
        }
    });
</script>
<script type="text/javascript">

$(document).ready(function() {
    //project wise branch
    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }
    // $("#projectId").change(function () {
    //         $('#projectIde').hide();
    //         var projectId = this.value;
    //         var csrf = "<?php echo csrf_token(); ?>";
    //         $.ajax({
    //             type: 'post',
    //             url: './getBranchNProjectTypeByProject',
    //             data: {projectId: projectId , _token: csrf},
    //             dataType: 'json',
    //             success: function (data) {
    //                 //alert(JSON.stringify(data));
    //                 var branchList=data['branchList'];
    //                 var projectTypeList=data['projectTypeList'];

    //                 $("#branchId").empty();
    //                 $("#branchId").append('<option value="">All (With Head Office)</option>');
    //                 $("#branchId").append('<option value="0">All (WithOut Head Office)</option>');
    //                 $("#branchId").append('<option value="1">000 - Head Office</option>');
    //                 console.log(branchList);
    //                 $.each(branchList, function( key,obj){
    //                     $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
    //                 });

    //                 $("#projectTypeId").empty();
    //                 //$("#projectTypeId").prepend('<option value="">All</option>');

    //                 $.each(projectTypeList, function( key,obj){
    //                     $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
    //                 });

    //             },
    //             error: function(_response){
    //                 alert("Error");
    //             }
    //         });
    //     });
    $("#filteringFormId").submit(function( event ) {
        event.preventDefault();
        var serializeValue=$(this).serialize();
        $('#loadingModal').show();
        var projectId = $('#projectId').val();
        var fiscalYearId = $('#fiscalYearId').val();
        var accountType = $('#filAccountType').val();
        var branchId = $('#branchId').val();
        var csrf = "{{csrf_token()}}";

        formData = new FormData();
        formData.append('fiscalYearId', fiscalYearId);
        formData.append('projectId', projectId);
        formData.append('accountType', accountType);
        formData.append('branchId', branchId);
         formData.append('_token', csrf);

            if(projectId == ""){
                alert('Project  must be selected');
            }else if(branchId == ""){
                alert('Branch  must be selected');
            }else{
                    $.ajax({
                        processData: false,
                        contentType: false,
                        type: 'post',
                        url: './checkRevisedBudgetItem',
                        data: formData,
                        dataType: 'json',
                        success: function( _response ){
                        	 //alert('ok');
                             $("#reportingDiv").load('{{URL::to("./loadRevisedBudget")}}'+'?'+serializeValue, function(){

                           });
                        },
                        error: function( _response ){
                            alert('Budget not available!');
                        }
                    });
            }
        $('#loadingModal').hide();
    });

});

$(document).ready(function() {

    // print script
    $("#printIcon").click(function(event) {

        var mainContents = document.getElementById("reportingDiv").innerHTML;

        var headerContents = '';

        var printStyle = '<style>.amount{text-align: right;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px;} table, td,th {border:1px solid #222;} .text-bold{font-weight:bold} table{float:left;height:auto;padding:0px;border-collapse: collapse;width:100%;font-size:11px;page-break-inside:auto;font-family: arial!important;} tr:last-child { font-weight: bold;} thead tr th{ text-align:center;vertical-align: middle;padding:3px;font-size:11px;}  tr{ page-break-inside:avoid; page-break-after:auto }</style><style media="print">@page{margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}} .level-1 td, .level-2 td, .level-3 td, .level-4 td, .level-constant td{font-weight: bold !important;padding-left: 5px !important;text-transform: uppercase;}.level-1 td{font-size: 1.04em;}.level-2 td{font-size: 1.00em;/* padding-left: 15px !important; */}.level-3 td{font-size: .96em;/* padding-left: 25px !important; */}.level-4 td{font-size: .92em;/* padding-left: 35px !important; */}.level-constant td{font-size: .88em;/* padding-left: 45px !important; */}.level-transformed td{font-weight: normal !important;font-size: .88em;padding-left: 5px !important;text-transform: capitalize;}.level-final td{font-weight: normal !important;text-transform: none;}.total td{padding-left: 5px !important;font-size: 1.05em;}</style>';

        // var mainContents = document.getElementById("printDiv").innerHTML;

        var footerContents = "<div class='row' style='font-size:12px; padding-left:5px; padding-top:20px;'>Prepared By<span style='display:inline-block; width: 33%; padding-top:40px;'></span> Checked By<span style='display:inline-block; width: 33%; padding-right:15px; padding-top:40px;'></span>Approved By</div>";

        var printContents = '<div id="order-details-wrapper" style="padding: 10px;">' + printStyle + mainContents + footerContents +'</div>';

        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        win.close();
    });

    $("#loadingModal").hide();

}); /* Ready to print */
</script>

@endsection
