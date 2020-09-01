@extends('layouts/acc_layout')
@section('title', '| Branch Wise Ledger Report')
@section('content')

<?php
    // echo $str = 'astring23.2';
    // echo $pre = strstr($str, '.', true);

    // $str = 23.987654321; 
    // $str_arr = explode('.',$str);
    // $beforePoint=(int)$str_arr[0];       // Before the Decimal point
    // $afterPoint=(int)$str_arr[1];       // After the Decimal point

    // var_dump($beforePoint);
    // echo "<br/>"; 
    // var_dump($afterPoint);

    // $afterPointArr=explode(' ',$str_arr[1]);
    // $afterPointArr = array_map('intval',str_split($afterPoint));


    // $numlength = strlen((string)$afterPoint);
    // echo $numlength;
                          

    // echo "<pre>";
    // print_r($afterPointArr);
    // echo "</pre>";
    // var_dump($afterPointArr);

    // echo "<pre>";
    // print_r($ledgerOptions123);
    // echo "</pre>";



?>

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
                <h3  style=" text-align: center; font-family: Antiqua; letter-spacing: 2px; color: white;">Branch Wise Ledger Report</h3>
            </div>

            <div class="panel-body panelBodyView" >

                <!-- Filtering Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            {{-- <div class="col-md-2"> --}}
                                {{-- <div class="row"> --}}

                                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'filteringFormId', 'method'=>'get')) !!}

                                    @if($userBranchId==1)
                                    <div class="col-md-1">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">                                                
                                                {!! Form::select('filProject', $projectsOption, null ,['id'=>'filProject','class'=>'form-control input-sm', 'autocomplete'=>'off', 'autofocus']) !!}
                                                <p id='filProjectE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($userBranchId==1)
                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Branch:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filBranch', $branchesOption, null ,['id'=>'filBranch','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filBranchE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($userBranchId==1)
                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Project Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filProjectType', $projectTypesOption, null ,['id'=>'filProjectType','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filProjectTypee' style="max-height:3px; color:red;"></p>                                                
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Ledger:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">

                                                {!! Form::select('filLedgerId', $ledgerOptions, null ,['id'=>'filLedgerId','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filLedgerIdE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group" style="font-size: 13px; color:#212F3C">
                                            {!! Form::label('', 'Voucher Type:', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::select('filVoucherType', $voucherTypesOption, null ,['id'=>'filVoucherType','class'=>'form-control input-sm','autocomplete'=>'off', 'autofocus', 'required']) !!}
                                                <p id='filVoucherTypeE' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

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

                                    {{-- <div class="col-md-1"></div> --}}

                                    <div class="col-md-1">
                                        <div class="form-group" style="">
                                            {!! Form::label('', '', ['class' => 'control-label col-sm-12']) !!}
                                            <div class="col-sm-12" style="padding-top: 7px;">
                                                {!! Form::submit('Search', ['id' => 'filteringFormSubmit', 'class' => 'btn btn-primary animated fadeInRight']); !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>

                                    {!! Form::close()  !!}
                                {{-- </div> --}}
                            {{-- </div>     --}}

                            {{-- end Div of ledgerSearch --}}

                            <div class="col-md-10"></div>
                        </div>
                    </div>

                </div>
                <!-- filtering end-->



                <div class="row">
                    <div class="col-md-12"  id="reportingDiv">
                        
                    </div>
                </div>




            </div>
        </div>
    </div>
</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    
    var errMsg="Please Select";
    var userBranchId="{{$userBranchId}}";
    var csrf = "{{csrf_token()}}";
    var loadingDiv='<div align="center"><img src="{{ asset('images/loading/loading.gif') }}"></div>';

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }
    
    $("#filProject").change(function () {

        // alert("pr");

        $('#projectIde').hide();
        var projectId = this.value;

        if(projectId==""){
            alert("empty");
            $('#projectIde').html("Please Select Project");
            return false;
        }
       
        $.ajax({
            type: 'post',
            url: './getBranchNProjectTypeByProject',
            data: {projectId: projectId , _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));


                var branchList=data['branchList'];
                var projectTypeList=data['projectTypeList'];

                $("#filBranch").empty();
                $("#filBranch").append('<option value="-1">All with HO</option>');
                $("#filBranch").append('<option value="-2">All with out HO</option>');
                $("#filBranch").append('<option value="1">000 - Head Office</option>');

                $.each(branchList, function( key,obj){
                    $('#filBranch').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                });

                $("#filProjectType").empty();
                $("#filProjectType").prepend('<option value="-1">All</option>');

                $.each(projectTypeList, function( key,obj){
                    $('#filProjectType').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                });

            },
            error: function(_response){
                alert("Error");
            }
        }); 
    });     //filProject


    $("#filBranch").change(function () {


        if(userBranchId!=1){
            return false;
        }
        // alert();
        var projectId = $("#filProject option:selected").val();
        var branchId = this.value;

        if(projectId==""){
            $('#filProjectE').html(errMsg);
            return false;
        }

        $.ajax({
            type: 'post',
            url: './getLedgersOptionByBranch',
            data: {projectId: projectId, branchId:branchId, _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                alert(data['ledgerIdArr'].length);

                // var projectTypeList=data['projectTypeList'];
                var ledgerOptions=data['ledgerOptions'];

                $("#filLedgerId").empty();
                $("#filLedgerId").prepend('<option selected="selected" value="">Select  Ledger</option>');
                
                // $.each(ledgerOptions, function(ledgerId, nameWithCode){
                //     $('#filLedgerId').append("<option value='"+ledgerId+"'>"+nameWithCode+"</option>");
                // });
                // $('#filLedgerId option[value=""]').insertBefore('#filLedgerId option[value="1"]').attr('selected', 'selected');

                $.each(ledgerOptions, function(key, obj){
                    $('#filLedgerId').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                });



            },
            error: function(_response){
                alert("Error");
            }
        });
    });



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


    $("#filteringFormId").submit(function( event ) {
        event.preventDefault();

        if(userBranchId==1){
            var branchValue=$('#filBranch').val();        
            if(branchValue){$('#filBranchE').hide();}else{$('#filBranchE').show();$('#filBranchE').html(errMsg);return false;}

            var projectValue=$('#filProject').val();        
            if(projectValue){$('#filProjectE').hide();}else{$('#filSamityE').show();$('#filProjectE').html(errMsg);;return false;}

        }        

        var ledgerValue=$('#filLedgerId').val();        
        if(ledgerValue){$('#filLedgerIdE').hide();}else{$('#filSamityE').show();$('#filLedgerIdE').html(errMsg);;return false;}
        

        var serializeValue=$(this).serialize();
        // alert(serializeValue);

        $("#reportingDiv").html(loadingDiv);
        $("#reportingDiv").load('{{URL::to("./branchWiseLedgerReport")}}'+'?'+serializeValue);
        // $('#loadingModal').show();
        // $('#loadingModal').hide();
        
    });

// ===============================================Ends Form Submit===============================================
// ==============================================================================================================




    $('select').on('change', function (e) {

        var projectValue = $("#filProject").val();
        if(projectValue){$('#filProjectE').hide();}else{$('#filProjectE').show();}

        var branchValue = $("#filBranch").val();
        if(branchValue){$('#filBranchE').hide();}else{$('#filBranchE').show();}

        var projectTypeValue = $("#filProjectType").val();
        if(projectTypeValue){$('#filProjectTypeE').hide();}else{$('#filProjectTypeE').show();}

        var ledgerValue = $("#filLedgerId").val();
        if(ledgerValue){$('#filLedgerIdE').hide();}else{$('#filLedgerIdE').show();}

        var voucherTypeValue = $("#filVoucherType").val();
        if(voucherTypeValue){$('#filVoucherTypeE').hide();}else{$('#filVoucherTypeE').show();}
    });

    $("input").keyup(function(){
        var startDateValue = $("#filStartDate").val();
        if(startDateValue){$('#filStartDateE').hide();}else{$('#filStartDateE').show();}

        var endDateValue = $("#filEndDate").val();
        if(endDateValue){$('#filEndDateE').hide();}else{$('#filEndDateE').show();}

    });




    $("#printIcon").click(function(event) {

        // $("#reportingTable").removeClass('table table-striped table-bordered');

        var mainContents = document.getElementById("printDiv").innerHTML;
        var headerContents = '';

        var printStyle = '<style>#reportingTable{float:left;height:auto;padding:0px;width:100% !important;font-size:11px;page-break-inside:auto;}  thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:11px} tr{ page-break-inside:avoid; page-break-after:auto } table tbody tr td.amount{text-align:right !important;}</style><style media="print">@page{size: A4 portrait;margin:10mm 10mm;}</style><style>@media print {.element-that-contains-table { overflow: visible !important; }}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';
        
        var printContents = '<div id="order-details-wrapper">' + headerContents + printStyle + mainContents +'</div>';

        var win = window.open('','printwindow');
        win.document.write(printContents);
        win.print();
        // $("#reportingTable").addClass('table table-striped table-bordered');
        win.close();

        
    });




});
</script>




@endsection
