@extends('layouts/acc_layout')
@section('title', '| Edit Opening Balance')
@section('content')
@include('successMsg')
<?php
// $toDay =date("Y-m-d");
// echo $toDay."<br/>";

// $ficalId=DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', '2017-05-01')->where('fyEndDate', '>=', '2017-05-01')->value('id');
// echo $obId;

// echo "<br/>".date('d-m-Y H:i:s');
// echo "<br/>".Carbon\Carbon::parse(date('d-m-Y H:i:s'))->format('Y-m-d');
$toDay=Carbon\Carbon::now()->format('Y-m-d');
// echo "<br/>".$toDay;
$ficalinfo=DB::table('gnr_fiscal_year')->where('fyStartDate', '<=', $toDay)->where('fyEndDate', '>=', $toDay)->select('id', 'fyStartDate', 'fyEndDate')->first();
$fyStartDate=Carbon\Carbon::parse($ficalinfo->fyStartDate)->format('Y');

$openingBalance=DB::table('acc_opening_balance')->where('id',$openingBalanceId)->select('projectId','branchId','projectTypeId','openingDate')->first();
// dd($openingBalance);

// $debitAmount=DB::table('acc_opening_balance')->where('id',11)->value('debitAmount');
// echo "debitAmount: ". $debitAmount;
// echo "<br/>";
// echo "Mod: ".($debitAmount%1);
// echo "<br/>";
// echo "Mod: ".fmod($debitAmount,1);



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
                        <div class="panel-title">Edit Opening Balance</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="padding: 0px 20px 0px 20px">
                                {{-- <div class="col-md-8"> --}}
                                {!! Form::open(array('url' => '', 'id' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

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
                                                <select class ="form-control" id = "branchId" name="branchId">
                                                    <option value="">Select Branch</option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{$branch->id}}">{{$branch->branchCode." - ".$branch->name}}</option>
                                                    @endforeach
                                                </select>
                                                <p id='branchIde' style="max-height:3px; color:red;"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
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
                                        <table id="editOpeningBalanceTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    {{-- <th class="ledgerId" style="display:none;">Id</th> --}}
                                                    <th style="width: 120px;">Account Type</th>
                                                    {{-- <th style="text-align:center;">Parent</th> --}}
                                                    <th>Account Head</th>
                                                    <th style="width: 80px;">Code</th>
                                                    <th style="width: 150px;">Debit</th>
                                                    <th style="width: 150px;">Credit</th>
                                                    <th style="width: 150px;">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody id="eachRow">
                                            @php
                                                $debitArray=array();
                                                $crebitArray=array();
                                                $i=0;
                                            @endphp
                                                @foreach ($openingBalanceInfos as $openingBalanceInfo)
                                                @php
                                                    array_push($debitArray, round($openingBalanceInfo->debitAmount,2));
                                                    array_push($crebitArray, round($openingBalanceInfo->creditAmount,2));
                                                @endphp
                                                    <tr>
                                                        <td tdno={{++$i}} class="accountTypeNameColumn" style="text-align: left; padding-left: 5px;">
                                                            @php
                                                                $accountTypeId=DB::table('acc_account_ledger')->where('id', $openingBalanceInfo->ledgerId)->value('accountTypeId');
                                                                echo DB::table('acc_account_type')->where('id',$accountTypeId)->value('name');
                                                            @endphp
                                                        </td>
                                                        <td class="hidden openingBalanceIdColumn">
                                                            <input type='hidden' name='openingBalanceId' class='openingBalanceIdInput' value={{$openingBalanceInfo->id}}>
                                                        </td>

                                                        <td class="ledgerIdColumn" style="text-align: left; padding-left: 5px;">
                                                            @php
                                                                $ledgerId=DB::table('acc_account_ledger')->where('id', $openingBalanceInfo->ledgerId)->select('name','code')->first();
                                                            @endphp
                                                            <input type='hidden' name='ledgerName' class='ledgerIdInput' value={{$openingBalanceInfo->ledgerId}}>
                                                            {{$ledgerId->name}}
                                                        </td>

                                                        <td class="codeColumn">{{$ledgerId->code}}</td>

                                                        <td class="debitAmountColumn">
                                                            <input type='text' name='debitAmount' style='text-align:right' class='debitAmountInput' value={{round($openingBalanceInfo->debitAmount,2)}}>
                                                        </td>

                                                        <td class="creditAmountColumn">
                                                            <input type='text' name='creditAmount' style='text-align:right' class='creditAmountInput' value={{round($openingBalanceInfo->creditAmount,2)}}>
                                                        </td>

                                                        <td class="balanceAmountColumn">
                                                            <input type='text' name='balanceAmount' style='text-align:right; cursor:not-allowed;' class='balanceAmountInput' amount={{round($openingBalanceInfo->balanceAmount,2)}}  disabled='disabled' value={{number_format($openingBalanceInfo->balanceAmount, 2, '.', '')}}>
                                                        </td>
                                                    </tr>

                                                @endforeach
                                                <tr class="valueRow" style="font-weight: bold;">
                                                    <td colspan="3" id="totalColumn"> Total:</td>
                                                    <td style="text-align:right; padding-right: 8px;" id="totalDebitAmount" amount={{array_sum($debitArray)}} > {{array_sum($debitArray)}} </td>
                                                    <td style="text-align:right; padding-right: 8px;" id="totalCreditAmount" amount={{array_sum($crebitArray)}} > {{array_sum($crebitArray)}} </td>
                                                    <td style="text-align:right; padding-right: 8px;" id="totalBalanceAmount" amount={{array_sum($debitArray)-array_sum($crebitArray)}} >{{array_sum($debitArray)-array_sum($crebitArray)}}</td>
                                                </tr>

                                                @php
                                                    // $x=array_sum($debitArray);
                                                    // $y=array_sum($crebitArray);
                                                    // $z=$x-$y;
                                                    // echo $z;
                                                @endphp

                                            </tbody>
                                        </table>
                                        <p id='editOpeningBalanceTableE' style="max-height:3px; color: red;"></p>
                                    </div>
                                    <div class="col-md-0"></div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9 text-right">
                                        @php
                                            $modify = 0;
                                        @endphp
                                        @if ($modify != 0)
                                            {!! Form::submit('Update', ['id' => 'editOpeningBalanceSubmit', 'class' => 'btn btn-info']) !!}
                                            {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                        @endif
                                        @if ($openingBalance->branchId == 95 || $openingBalance->branchId == 96)
                                            {!! Form::submit('Update', ['id' => 'editOpeningBalanceSubmit', 'class' => 'btn btn-info']) !!}
                                            {{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}
                                        @endif
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
    $(document).ready(function() {
        var x = $("#totalDebitAmount").attr('amount');
        var y = $("#totalCreditAmount").attr('amount');
        var z=x-y;
        $("#totalBalanceAmount").html(z);
        $("#totalBalanceAmount").attr('amount',z);
        @php
            // dd($openingBalance);
        @endphp

        var projectId="{{$openingBalance->projectId}}";
        $("#projectId").val(projectId);
        var branchId="{{$openingBalance->branchId}}";
        $("#branchId").val(branchId);
        var projectTypeId="{{$openingBalance->projectTypeId}}";
        $("#projectTypeId").val(projectTypeId);
        var openingDate="{{date('d-m-Y',strtotime($openingBalance->openingDate))}}";
        $("#openingDate").val(openingDate);
        $('#projectId, #branchId, #projectTypeId, #openingDate').prop("disabled", true);

        $(document).on('input', '.debitAmountInput, .creditAmountInput, .balanceAmountInput', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        $(document).on('input', '.debitAmountInput', function() {
            $('#editOpeningBalanceTableE').hide();

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

            var tableRows = $('#editOpeningBalanceTable tbody tr').length;
            var counter=1;
            // alert(tableRows);
            var totalDebitAmount = 0;
            var totalCreditAmount = 0;

            $("#editOpeningBalanceTable tbody tr").each(function(){
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
            $('#editOpeningBalanceTableE').hide();

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

            var tableRows = $('#editOpeningBalanceTable tbody tr').length;
            var counter=1;
            // alert(tableRows);
            var totalDebitAmount = 0;
            var totalCreditAmount = 0;

            $("#editOpeningBalanceTable tbody tr").each(function(){
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

//======================Save======================
        $('form').submit(function( event ) {
            event.preventDefault();
            $("#editOpeningBalanceSubmit").prop("disabled", true);

            var projectId = $("#projectId option:selected").val();
            var branchId = $("#branchId option:selected").val();
            var projectTypeId = $("#projectTypeId option:selected").val();
            var openingDate = $("#openingDate").val();
            var obId = "{{$obId}}";
            var csrf = "<?php echo csrf_token(); ?>";

            var openingBalanceIdArray = new Array();
            var ledgerIdArray = new Array();
            var debitAmountArray = new Array();
            var creditAmountArray = new Array();
            var balanceAmountArray = new Array();
            $("#editOpeningBalanceTable tbody tr").each(function(){
                openingBalanceIdArray.push(JSON.stringify($(this).find('.openingBalanceIdInput').val()));
                ledgerIdArray.push(JSON.stringify($(this).find('.ledgerIdInput').val()));
                debitAmountArray.push(JSON.stringify($(this).find('.debitAmountInput').val()));
                creditAmountArray.push(JSON.stringify($(this).find('.creditAmountInput').val()));
                balanceAmountArray.push(JSON.stringify($(this).find('.balanceAmountInput').attr('amount')));
            });


            var totalDebitAmount = $("#totalDebitAmount").attr('amount');
            var totalCreditAmount = $("#totalCreditAmount").attr('amount');

            if (totalDebitAmount!=totalCreditAmount) {
                alert("Debit Amount & Credit Amount Are Not EQUAL!!");
                $('#editOpeningBalanceTableE').html("Debit Amount & Credit Amount Are Not EQUAL!!");
                $('#editOpeningBalanceTableE').show();
                $("#editOpeningBalanceSubmit").prop("disabled", false);
                return false;
            }

            // var noOfRow=balanceAmountArray.length;
            // alert(noOfRow);


            // alert(csrf);

            // alert("projectId: "+projectId+", branchId: "+branchId+", projectTypeId: "+projectTypeId+", openingDate: "+openingDate+", ledgerIdArray: "+ledgerIdArray+", debitAmountArray: "+debitAmountArray+", creditAmountArray: "+creditAmountArray+", balanceAmountArray: "+balanceAmountArray);
            // alert("branchId: "+branchId);
            // alert("projectTypeId: "+projectTypeId);
            // alert("openingDate: "+openingDate);
            // alert("openingBalanceIdArray: "+openingBalanceIdArray);
            // alert("ledgerIdArray: "+ledgerIdArray);
            // alert("debitAmountArray: "+debitAmountArray);
            // alert("creditAmountArray: "+creditAmountArray);
            // alert("balanceAmountArray: "+balanceAmountArray);

            $.ajax({
                type: 'post',
                url: './updateOpeningBalanceItem',
                data: { _token:csrf, obId:obId, projectId:projectId, branchId:branchId, projectTypeId:projectTypeId, openingDate:openingDate, openingBalanceIdArray:openingBalanceIdArray, ledgerIdArray:ledgerIdArray, debitAmountArray:debitAmountArray, creditAmountArray:creditAmountArray, balanceAmountArray:balanceAmountArray },
                dataType: 'json',
                success: function( _response ){
                    // alert(JSON.stringify(_response));
                    if (_response.errors) {
                        $("#editOpeningBalanceSubmit").prop("disabled", false);
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
                    alert("Could Not Update");
                    // alert(_response.errors);
                }
            });         //ajax function


        });



    });
</script>
