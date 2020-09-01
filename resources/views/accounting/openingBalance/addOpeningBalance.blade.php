@extends('layouts/acc_layout')
@section('title', '| Add Opening Balance')
@section('content')
@include('successMsg')
<?php
// $toDay =date("Y-m-d");
// echo $toDay."<br/>";

// $ficalId=DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', '2017-05-01')->where('fyEndDate', '>=', '2017-05-01')->value('id');
// echo $ficalId;

// echo "<br/>".date('d-m-Y H:i:s');
// echo "<br/>".Carbon\Carbon::parse(date('d-m-Y H:i:s'))->format('Y-m-d');
$toDay=Carbon\Carbon::now()->format('Y-m-d');
// echo "<br/>".$toDay;
$ficalinfo=DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $toDay)->where('fyEndDate', '>=', $toDay)->select('id', 'fyStartDate', 'fyEndDate')->first();
$fyStartDate=Carbon\Carbon::parse($ficalinfo->fyStartDate)->format('Y');
// echo "<br/>".$fyStartDate;



?>
    <div class="row add-data-form"  style="padding-bottom: 1%">
        <div class="col-md-12">
            <div class="col-md-1"></div>
            <div class="col-md-10 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('viewOpeningBalance/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Opening Balance List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Add Opening Balance</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="padding: 0px 20px 0px 20px">
                                {{-- <div class="col-md-8"> --}}
                                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups','autocomplete'=> 'off')) !!}

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('projectId', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                <select class ="form-control" id = "projectId" name="projectId">
                                                    <option value="">Select Project</option>
                                                    @foreach($projects as $project)
                                                        <option value="{{$project->id}}">{{$project->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='projectIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-2"></div> --}}
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('branchId', 'Branch:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                <select class ="form-control" id = "branchId" name="branchId" >
                                                    <option value="">Select Branch</option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{$branch->id}}">{{$branch->branchCode." - ".$branch->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='branchIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3" hidden>
                                        <div class="form-group">
                                            {!! Form::label('projectTypeId', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                <select class ="form-control" id = "projectTypeId" name="projectTypeId">
                                                    <option value="">Select Project Type</option>
                                                    @foreach($projectTypes as $projectType)
                                                        <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='projectTypeIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('openingDate', 'Opening Date:', ['class' => 'col-sm-12 control-label']) !!}
                                            <div class="col-sm-12">
                                                {!! Form::text('openingDate', null, ['class' => 'form-control','style'=>'cursor:pointer', 'id' => 'openingDate'])!!}
                                                <p id='openingDatee' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row" style="padding: 20px 0px 30px 0px">
                                    <div class="col-md-0"></div>
                                    <div class="col-md-12"  style="padding: 0px 15px 0px 15px; ">
                                        <table id="addOpeningBalanceTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    {{-- <th class="ledgerId" style="display:none;">Id</th> --}}
                                                    <th style="text-align:center; width: 120px;">Account Type</th>
                                                    {{-- <th style="text-align:center;">Parent</th> --}}
                                                    <th style="text-align:center;">Account Head</th>
                                                    <th style="text-align:center; width: 80px;">Code</th>
                                                    <th style="text-align:center; width: 150px;">Debit</th>
                                                    <th style="text-align:center; width: 150px;">Credit</th>
                                                    <th style="text-align:center; width: 150px;">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody id="eachRow">
                                                <tr>
                                                    {{-- <th class="ledgerId" style="display:none;"></th> --}}
                                                    <th></th>
                                                    {{-- <th></th> --}}
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <p id='addOpeningBalanceTableE' style="max-height:3px; color: red;"></p>
                                    </div>
                                    <div class="col-md-0"></div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9 text-right">
                                        {!! Form::submit('Submit', ['id' => 'addOpeningBalanceSubmit', 'class' => 'btn btn-info']) !!}
                                        {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                        <a href="{{url('addOpeningBalance/')}}" class="btn btn-danger closeBtn">Close</a>
                                        <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                                    </div>
                                </div>
                                {!! Form::close()  !!}
                                {{-- </div> --}}
                                {{-- <div class="col-md-4 emptySpace vert-offset-top-4"><img src="images/image15.png" width="90%" height="" style="float:right"></div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footerTitle" style="border-top:1px solid white"></div>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>

@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function(){

        // var openingDatefromDB= "{{$openingDatefromDB}}";
        // alert(openingDatefromDB);

        $("#openingDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2016:c",
            minDate: new Date(2017, 06 - 1, 30),
            // maxDate:  new Date(2017, 06 - 1, 30),
            maxDate: "dateToday",
            // minDate: new Date(openingDatefromDB),
            // maxDate: openingDatefromDB,
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#openingDatee').hide();
            }
        });

        $("#projectId").on('change', function () {
            $('#openingDate').val("");
            $("#addOpeningBalanceTable tbody").empty();
            var projectId = $("#projectId").val();
            if(projectId!=""){
                $('#projectIde').hide();
            }
        });

        // $(".ledgerId").hide();
        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }


        $("#projectId").change(function () {
            $('#openingDate').val("");
            $("#addOpeningBalanceTable tbody").empty();

            $('#projectIde').hide();
            var projectId = this.value;
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './getBranchNProjectTypeByProject',
                data: {projectId: projectId , _token: csrf},
                dataType: 'json',
                success: function (data) {
                    // alert(JSON.stringify(data));

                    var branchList=data['branchList'];
                    var projectTypeList=data['projectTypeList'];

                    $("#branchId").empty();
                    $("#branchId").prepend('<option selected="selected" value="">Select Branch</option>');
                    $("#branchId").append('<option value="1">000 - Head Office</option>');

                    $.each(branchList, function( key,obj){
                        // $('#branchId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        $('#branchId').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    });

                    $("#projectTypeId").empty();
                    //$("#projectTypeId").prepend('<option selected="selected" value="">Select Project Type</option>');

                    $.each(projectTypeList, function( key,obj){
                        $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.name+"</option>");
                        // $('#projectTypeId').append("<option value='"+obj.id+"'>"+obj.projectTypeCode+" - "+obj.name+"</option>");
                    });

                },
                error: function(_response){
                    alert("Error");
                }
            });

        });

        $("#branchId").change(function () {
            $('#openingDate').val("");
            $("#addOpeningBalanceTable tbody").empty();
            var projectId = $("#projectId").val();
            if (projectId=="") {
                $('#projectIde').html("Please Select Project First");
                return false;
            }
        });

        $("#projectTypeId").change(function () {
            $('#openingDate').val("");
            $("#addOpeningBalanceTable tbody").empty();
            var projectId = $("#projectId").val();
            if (projectId=="") {
                $('#projectIde').html("Please Select Project First");
                return false;
            }
            var branchId = $("#branchId").val();
            if (branchId=="") {
                $('#branchIde').html("Please Select Project First");
                return false;
            }
        });

        $("#openingDate").datepicker( "option", "onSelect", function () {
            var projectId = $("#projectId").val();
            if (projectId=="") {
                $('#projectIde').html("Please Select Project First");
                $('#openingDate').val("");
                return false;
            }
            var branchId = $("#branchId").val();
            if (branchId=="") {
                $('#branchIde').html("Please Select Branch First");
                $('#openingDate').val("");
                return false;
            }
            var projectTypeId = $("#projectTypeId").val();
            if (projectTypeId=="") {
                $('#projectTypeIde').html("Please Select Project Type First");
                $('#openingDate').val("");
                return false;
            }

            var openingDate = this.value;
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './checkPreOpeningBalance',
                data: { projectId: projectId, branchId: branchId, projectTypeId: projectTypeId, openingDate: openingDate , _token: csrf},
                dataType: 'json',
                success: function (data) {
                    // alert(JSON.stringify(data));
                    // alert(data.matchedOpeningBalanceId);

                    if(data.matchedOpeningBalanceId!=""){
                        $("#addOpeningBalanceTable tbody").empty();
                        alert("Data Already Existed!!!");
                        $('#openingDate').val("");
                    }else{
                        // alert("Loading!!!");

                        $.ajax({
                            type: 'post',
                            url: './getLedgerHeader',
                            data: {projectId: projectId, branchId:branchId, _token: csrf},
                            dataType: 'json',
                            success: function (data) {
                                // alert(JSON.stringify(data));

                                var ledgers=data['ledgers'];

                                $("#addOpeningBalanceTable tbody").empty();

                                $.each(ledgers, function( key,obj){
                                    var eachRow =
                                            "<tr class='valueRow'>" +
                                                // "<td style='text-align:left; padding-left:5px;' class='ledgerNameColumn'>"+
                                                "<td style='text-align:left; padding-left:5px;' class='accountTypeNameColumn'>"+obj.accountTypeName+"</td>" +
                                                // "<td style='text-align:left; padding-left:5px;' class='parentNameColumn'>"+obj.parentName+"</td>" +
                                                "<td style='text-align:left; padding-left:5px;' class='ledgerIdColumn'>"+
                                                    "<input type='hidden' name='ledgerName' class='ledgerIdInput' value='"+obj.id+"'>"+obj.ledgerName+
                                                "</td>" +
                                                "<td  class='codeColumn'>"+obj.code+"</td>" +
                                                // "<td style='text-align:left; padding-left:5px;' class='parentNameColumn'>"+obj.code+"</td>" +
                                                "<td style='text-align:center;' class='debitAmountColumn' >" +
                                                    "<input type='text' name='debitAmount' style='text-align:right' class='debitAmountInput' amount='0' value='0'>"+
                                                "</td>" +
                                                "<td style='text-align:center;' class='creditAmountColumn'>"+
                                                    "<input type='text' name='creditAmount' style='text-align:right' class='creditAmountInput' amount='0' value='0'>"+
                                                "</td>" +
                                                "<td style='text-align:center;' class='balanceAmountColumn'>"+
                                                    "<input type='text' name='balanceAmount' style='text-align:right; cursor:not-allowed;' class='balanceAmountInput' amount='0' disabled='disabled' value='0'>"+
                                                "</td>" +

                                            "</tr>";

                                    $("#eachRow").append(eachRow);

                                    // $('.balanceAmountInput').prop("disabled", true);
                                });

                                $("#eachRow").append("<tr class='valueRow' style='font-weight: bold;' >" +
                                                        "<td colspan='3' id='totalColumn'>"+"Total: "+"</td>" +
                                                        "<td style='text-align:right; padding-right:8px;' id='totalDebitAmount' amount='0' >"+"0.00"+"</td>" +
                                                        "<td style='text-align:right; padding-right:8px;' id='totalCreditAmount' amount='0'>"+"0.00"+"</td>" +
                                                        "<td style='text-align:right; padding-right:8px;' id='totalBalanceAmount' amount='0'>"+"0.00"+"</td>" +
                                                    "</tr>");

                            },
                            error: function(_response){
                                alert("Error");
                            }
                        });

                    }



                },
                error: function(_response){
                    alert("Error");
                }
            });
        });


        $(document).on('input', '.debitAmountInput, .creditAmountInput, .balanceAmountInput', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        $(document).on('input', '.debitAmountInput', function() {
            $('#addOpeningBalanceTableE').hide();

            if(this.value==""){
                var debitAmount=0;
            }else{
                var debitAmount = parseFloat(this.value).toFixed(2);
            }

            if($(this).closest('tr').find(".creditAmountInput").val()==""){
                var creditAmount=0;
            }else{
                var creditAmount = parseFloat($(this).closest('tr').find(".creditAmountInput").val()).toFixed(2);
            }

            var balanceAmount=debitAmount-creditAmount;
            $(this).closest('tr').find(".balanceAmountInput").attr('amount', balanceAmount.toFixed(2));
            $(this).closest('tr').find(".balanceAmountInput").val(balanceAmount.toFixed(2));

            var tableRows = $('#addOpeningBalanceTable tbody tr').length;
            var counter=1;
            // alert(tableRows);
            var totalDebitAmount = 0;
            var totalCreditAmount = 0;

            $("#addOpeningBalanceTable tbody tr").each(function(){
                totalDebitAmount = parseFloat(totalDebitAmount);
                totalCreditAmount = parseFloat(totalCreditAmount);

                if (counter==tableRows) {
                    return false;
                }
                counter++;

                if ($(this).closest('tr').find('.debitAmountInput').val()=="") {
                    var tempDebitAmount=0;
                }else{
                    var tempDebitAmount= parseFloat($(this).closest('tr').find('.debitAmountInput').val()).toFixed(2);
                }

                if ($(this).closest('tr').find('.creditAmountInput').val()=="") {
                    var tempCreditAmount=0;
                }else{
                    var tempCreditAmount= parseFloat($(this).closest('tr').find('.creditAmountInput').val()).toFixed(2);
                }
                totalDebitAmount= parseFloat(totalDebitAmount)+parseFloat(tempDebitAmount);
                totalCreditAmount= parseFloat(totalCreditAmount)+parseFloat(tempCreditAmount);
                // alert(typeof(totalDebitAmount));

                $("#totalDebitAmount").attr('amount', parseFloat(totalDebitAmount).toFixed(2));
                $("#totalCreditAmount").attr('amount', parseFloat(totalCreditAmount).toFixed(2));

                // $("#totalDebitAmount").html(totalDebitAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $("#totalDebitAmount").html( parseFloat(totalDebitAmount).toFixed(2));
                $("#totalCreditAmount").html( parseFloat(totalCreditAmount).toFixed(2));
            });
            var totalBalanceAmount=(parseFloat($("#totalDebitAmount").attr('amount')).toFixed(2))-(parseFloat($("#totalCreditAmount").attr('amount')).toFixed(2));
            $("#totalBalanceAmount").attr('amount', parseFloat(totalBalanceAmount).toFixed(2));
            $("#totalBalanceAmount").html(parseFloat(totalBalanceAmount).toFixed(2));

        });

        $(document).on('input', '.creditAmountInput', function() {
            $('#addOpeningBalanceTableE').hide();

            if(this.value==""){
                var creditAmount=0;
            }else{
                var creditAmount = parseFloat(this.value).toFixed(2);
            }

            if($(this).closest('tr').find(".debitAmountInput").val()==""){
                var debitAmount=0;
            }else{
                var debitAmount = parseFloat($(this).closest('tr').find(".debitAmountInput").val()).toFixed(2);
            }

            var balanceAmount=debitAmount-creditAmount;
            $(this).closest('tr').find(".balanceAmountInput").attr('amount', balanceAmount.toFixed(2));
            $(this).closest('tr').find(".balanceAmountInput").val(balanceAmount.toFixed(2));

            var tableRows = $('#addOpeningBalanceTable tbody tr').length;
            var counter=1;
            // alert(tableRows);
            var totalDebitAmount = 0;
            var totalCreditAmount = 0;

            $("#addOpeningBalanceTable tbody tr").each(function(){
                totalDebitAmount = parseFloat(totalDebitAmount);
                totalCreditAmount = parseFloat(totalCreditAmount);

                if (counter==tableRows) {
                    return false;
                }
                counter++;

                if ($(this).closest('tr').find('.debitAmountInput').val()=="") {
                    var tempDebitAmount=0;
                }else{
                    var tempDebitAmount= parseFloat($(this).closest('tr').find('.debitAmountInput').val()).toFixed(2);
                }

                if ($(this).closest('tr').find('.creditAmountInput').val()=="") {
                    var tempCreditAmount=0;
                }else{
                    var tempCreditAmount= parseFloat($(this).closest('tr').find('.creditAmountInput').val()).toFixed(2);
                }
                totalDebitAmount= parseFloat(totalDebitAmount)+parseFloat(tempDebitAmount);
                totalCreditAmount= parseFloat(totalCreditAmount)+parseFloat(tempCreditAmount);

                $("#totalDebitAmount").attr('amount',parseFloat(totalDebitAmount).toFixed(2));
                $("#totalCreditAmount").attr('amount',parseFloat(totalCreditAmount).toFixed(2));

                // $("#totalDebitAmount").html(totalDebitAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $("#totalDebitAmount").html(parseFloat(totalDebitAmount).toFixed(2));
                $("#totalCreditAmount").html(parseFloat(totalCreditAmount).toFixed(2));
            });
            var totalBalanceAmount=(parseFloat($("#totalDebitAmount").attr('amount')).toFixed(2))-(parseFloat($("#totalCreditAmount").attr('amount')).toFixed(2));
            $("#totalBalanceAmount").attr('amount', parseFloat(totalBalanceAmount).toFixed(2));
            $("#totalBalanceAmount").html(parseFloat(totalBalanceAmount).toFixed(2));
        });

// =================================================Submit Button=================================================
        $('form').submit(function( event ) {
            event.preventDefault();
            // $(":submit", this).prop("disabled", "disabled");
            $("#addOpeningBalanceSubmit").prop("disabled", true);

            var projectId = $("#projectId option:selected").val();
            var branchId = $("#branchId option:selected").val();
            var projectTypeId = $("#projectTypeId option:selected").val();
            var openingDate = $("#openingDate").val();
            var csrf = "<?php echo csrf_token(); ?>";

            var ledgerIdArray = new Array();
            var debitAmountArray = new Array();
            var creditAmountArray = new Array();
            var balanceAmountArray = new Array();

            $("#addOpeningBalanceTable tbody tr").each(function(){
                ledgerIdArray.push(JSON.stringify($(this).find('.ledgerIdInput').val()));
                debitAmountArray.push(JSON.stringify($(this).find('.debitAmountInput').val()));
                creditAmountArray.push(JSON.stringify($(this).find('.creditAmountInput').val()));
                balanceAmountArray.push(JSON.stringify($(this).find('.balanceAmountInput').attr('amount')));
            });

            // alert(tatalDebitAmount);
            // var noOfRow=balanceAmountArray.length;
            // alert(noOfRow);


            var totalDebitAmount = parseFloat($("#totalDebitAmount").attr('amount')).toFixed(2);
            var totalCreditAmount = parseFloat($("#totalCreditAmount").attr('amount')).toFixed(2);
            // var totalCreditAmount = 0;
            // alert(totalDebitAmount);
            // alert(totalCreditAmount);

            // alert("projectId: "+projectId+", branchId: "+branchId+", projectTypeId: "+projectTypeId+", openingDate: "+openingDate+", ledgerIdArray: "+ledgerIdArray+", debitAmountArray: "+debitAmountArray+", creditAmountArray: "+creditAmountArray+", balanceAmountArray: "+balanceAmountArray);
            // alert("branchId: "+branchId);
            // alert("projectTypeId: "+projectTypeId);
            // alert("openingDate: "+openingDate);
            // alert("ledgerIdArray: "+ledgerIdArray);
            // alert("debitAmountArray: "+debitAmountArray);
            // alert("creditAmountArray: "+creditAmountArray);
            // alert("balanceAmountArray: "+balanceAmountArray);

            if (totalDebitAmount!=totalCreditAmount) {
                alert("Debit Amount & Credit Amount Are Not EQUAL!!");
                $('#addOpeningBalanceTableE').html("Debit Amount & Credit Amount Are Not EQUAL!!");
                $('#addOpeningBalanceTableE').show();
                $("#addOpeningBalanceSubmit").prop("disabled", false);
                return false;
            }

            $.ajax({
                type: 'post',
                url: './addOpeningBalanceItem',
                data: { _token:csrf, projectId:projectId, branchId:branchId, projectTypeId:projectTypeId, openingDate:openingDate, ledgerIdArray:ledgerIdArray, debitAmountArray:debitAmountArray, creditAmountArray:creditAmountArray, balanceAmountArray:balanceAmountArray },
                dataType: 'json',
                success: function( _response ){
                    // alert(JSON.stringify(_response));
                    if (_response.errors) {
                        // $(":submit", this).prop("disabled", false);
                        $("#addOpeningBalanceSubmit").prop("disabled", false);

                        if (_response.errors['projectId']) {
                            $('#projectIde').empty();
                            $('#projectIde').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                            return false;
                        }
                        if (_response.errors['branchId']) {
                            $('#branchIde').empty();
                            $('#branchIde').append('<span style="color:red;">'+_response.errors.branchId+'</span>');
                            return false;
                        }
                        if (_response.errors['projectTypeId']) {
                            $('#projectTypeIde').empty();
                            $('#projectTypeIde').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                            return false;
                        }
                        if (_response.errors['openingDate']) {
                            $('#openingDatee').empty();
                            $('#openingDatee').append('<span style="color:red;">'+_response.errors.openingDate+'</span>');
                            return false;
                        }

                    } else {
                        window.location.href = '{{url('viewOpeningBalance/')}}';
                    }
                },
                error: function( _response ){
                    // Handle error
                    alert("Could Not Add");
                    // alert(_response.errors);
                }
            });
        });

        $("input").keyup(function(){
            var openingDate = $("#openingDate").val();
            if(openingDate){$('#openingDatee').hide();}else{$('#openingDatee').show();}
        });

        $('select').on('change', function (e) {

            var projectId = $("#projectId").val();
            if(projectId){$('#projectIde').hide();}else{$('#projectIde').show();}

            var branchId = $("#branchId").val();
            if(branchId){$('#branchIde').hide();}else{$('#branchIde').show();}

            var projectTypeId = $("#projectTypeId").val();
            if(projectTypeId){$('#projectTypeIde').hide();}else{$('#projectTypeIde').show();}

        });



    });
</script>

<style type="text/css">
    /*input[type=text]:disabled {
        background: #CCCCCC;
    }*/
</style>
