@extends('layouts/acc_layout')
@section('title', '| Edit Fund transferred Voucher')
@section('content')

<?php
$user = Auth::user();
Session::put('branchId', $user->branchId);
$branchId = Session::get('branchId');

Session::put('id', $user->id);
$userId = Session::get('id');
$branch = DB::table('gnr_branch')->where('id', $voucherInfo->branchId)->select('name','companyId','branchCode')->first();

$ftIdValue=DB::table('acc_voucher')->where('id', $voucherInfo->id)->value('ftId');
$debitAmount = DB::table('acc_voucher_details')->where('voucherId', $voucherInfo->id)->value('debitAcc');
$creditAmount = DB::table('acc_voucher_details')->where('voucherId', $voucherInfo->id)->value('creditAcc');
// echo $voucherInfo->id;
// $voucherIdsOfTarget=DB::table('acc_voucher')->where('ftId', $ftIdValue)->where('id',"!=", $voucherInfo->id)->pluck('id')->toArray();
// echo "</br>voucherIdsOfTarget: "; var_dump($voucherIdsOfTarget);
// $targetBranchRows=DB::table('acc_voucher_details')->whereIn('voucherId', $voucherIdsOfTarget)->select('debitAcc','creditAcc','ftFrom','ftTo')->get();

// echo "</br>targetBranchRows: "; var_dump($targetBranchRows);
// echo "</br>targetBranchRows: "; var_dump($targetBranchRows[0]->debitAcc);

$oldTargetBranchArray=DB::table('acc_voucher')->where('ftId', $ftIdValue)->where('id',"!=", $voucherInfo->id)->pluck('branchId')->toArray();
// echo "</br>oldTargetBranchArray: "; var_dump($oldTargetBranchArray);
array_values($oldTargetBranchArray);

// $newTargetBranchArray = array_map('intval', explode(',', $oldTargetBranchArray));
// echo "</br>newTargetBranchArray: "; var_dump($newTargetBranchArray);

// $tableRows=DB::table('acc_voucher_details')->where('voucherId', $voucherInfo->id)->select('debitAcc','creditAcc')->get();

?>

<div class="row add-data-form">
<div class="col-md-12">
<div class="col-md-1"></div>
    <div class="col-md-10 fullbody">
        <div class="viewTitle" style="border-bottom: 1px solid white;">
            <a href="{{url('/viewVoucher')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Voucher List</a>
        </div>
        <div class="panel panel-default panel-border">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">

                        {{-- <div class="tab-pane" id="VoucherTab"> --}}
                        <div class="panel-heading">
                            <div class="panel-title col-md-4">Edit FT Voucher</div>
                            <div class="col-md-1"></div>
                            <div class="col-md-4"><h4 style="color: black;"><?php echo "Total Amount: ";?><span id="totalAmountColumnFT">0.0</span><?php echo " Tk";?></h4></div>
                            {{--<div class="col-md-1"></div>--}}
                            <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong >{{$branch->name}}</strong></h4></div>
                        </div>

                        {{-- <div class="row"> --}}
                        {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                        {!! Form::text('id', $value = $voucherInfo->id, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}

                        <div class="col-md-12">
                            <div class="row">
                                {{--<div class="col-md-12">--}}
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('projectIdFT', 'Project:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            <select class="form-control" id="projectIdFT" name="projectIdFT">
                                                <option value="">Select Project</option>
                                                @foreach($projects as $project)
                                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                                @endforeach
                                            </select>
                                            <p id='projectIdFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">        {{--project type--}}
                                        {!! Form::label('projectTypeIdFT', 'Project Type:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            <select class="form-control" id="projectTypeIdFT" name="projectTypeIdFT">
                                                <option value="">Select Project Type</option>
                                                @foreach($projectTypes as $projectType)
                                                    <option value="{{$projectType->id}}">{{$projectType->name}}</option>
                                                @endforeach
                                            </select>
                                            <p id='projectTypeIdFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('voucherDateFT', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            {!! Form::text('voucherDateFT',  date('d-m-Y',strtotime($voucherInfo->voucherDate)), ['class' => 'form-control', 'id' => 'voucherDateFT','readonly','style'=>'cursor:pointer'])!!}
                                            <p id='voucherDateFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('voucherCodeFT', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            {!! Form::text('voucherCodeFT', $voucherInfo->voucherCode, ['class' => 'form-control', 'id' => 'voucherCodeFT', 'type' => 'text', 'disabled' => 'disabled']) !!}
                                            <p id='voucherCodeFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('targetBranchFT', 'Target Branch:', ['class' => 'col-sm-12 control-label']) !!}
                                        <?php
                                        // $branchesInfo = DB::table('gnr_branch')->select('id', 'name', 'branchCode')->get();
                                        ?>
                                        <div class="col-sm-12">

                                            <select class="form-control" id="targetBranchFT" name="targetBranchFT">
                                                <option value="">Please Select Project First</option>
                                            </select>
                                            <p id='targetBranchFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('targetBranchHeadFT', 'Target Branch Cash/Bank:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            <select class="form-control" id="targetBranchHeadFT" name="targetBranchHeadFT">
                                                <option value="">Please Select Project First</option>
                                            </select>
                                            <p id='targetBranchHeadFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('narrationFT', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            {!! Form::text('narrationFT', $value = null, ['class' => 'form-control', 'id' => 'narrationFT', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                            <p id='narrationFTe' style="max-height:3px; color: red;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--</div> col-12--}}

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('debitAccFT', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            <select class="form-control" id="debitAccFT" name="debitAccFT">
                                                <option value="">Please Select Project First</option>
                                                {{-- <option value="">Select Debit Account</option>
                                                @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                    <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                @endforeach --}}
                                            </select>
                                            <p id='debitAccFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('creditAccFT', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            <select class="form-control" id="creditAccFT" name="creditAccFT">
                                                <option value="">Please Select Project First</option>
                                                {{-- <option value="">Select Credit Account</option>
                                                @foreach($ledgersOfCashAndBank as $ledgerOfCashAndBank)
                                                    <option value="{{$ledgerOfCashAndBank->id}}">{{$ledgerOfCashAndBank->name}}</option>
                                                @endforeach --}}
                                            </select>
                                            <p id='creditAccFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('amountFT', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12">
                                            {!!  Form::text('amountFT', $value = null, ['class' => 'form-control', 'id' => 'amountFT', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                            <p id='amountFTe' style="max-height:3px; color:red;"></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('', '', ['class' => 'col-sm-12 control-label']) !!}
                                        <div class="col-sm-12" style="padding-top: 20px;">
                                            <button class="btn btn-info" id="addFT" style="float: right; " type="button">Add</button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        {{-- {{$voucherInfo->id}} --}}
                        {{-- {{$voucherInfo->createdDate}} --}}
                        <div class="row" style="padding-bottom: 20px">
                            <div class="col-md-0"></div>
                            <div class="col-md-12" style="padding: 0px 28px; ">
                                <table id="addFTTable" class="table table-striped table-bordered" style="color: black;">
                                    <thead>
                                    <tr id="headerRowFT">
                                        <th style="padding: 10px 5px; width: 12%; text-align:center;">Target Branch</th>
                                        <th style="padding: 10px 5px; width: 15%; text-align:center;">Target Cash/ Bank</th>
                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Debit Account</th>
                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Credit Account</th>
                                        <th style="padding: 10px 5px; width: 10%; text-align:center;">Amount</th>
                                        <th style="padding: 10px 5px; width: 15%; text-align:center;">Narration</th>
                                        <th style="padding: 10px 5px; width: 8%; text-align:center;">Actions</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $tableRows=DB::table('acc_voucher_details')->where('voucherId', $voucherInfo->id)->select('debitAcc','creditAcc','amount','localNarration','ftFrom','ftTo','ftTargetAcc')->get();
                                        $x=0;
                                        $singleAmount=0;
                                        $totalOfSingleAmount=0;
                                        ?>
                                        @foreach($tableRows as $key1=> $tableRow)
                                        <?php $x++; ?>

                                        <?php
                                            $debitAcc=DB::table('acc_account_ledger')->where('id', $tableRow->debitAcc)->select('id','name', 'code')->first();
                                            $creditAcc=DB::table('acc_account_ledger')->where('id', $tableRow->creditAcc)->select('id','name', 'code')->first();
                                            if ($tableRow->ftTargetAcc!=0) {
                                                $ftTargetAcc=DB::table('acc_account_ledger')->where('id', $tableRow->ftTargetAcc)->select('id','name', 'code')->first();
                                                $statusOfTargetHead="cashNBank";
                                            }elseif ($tableRow->ftTargetAcc==0) {
                                                $statusOfTargetHead="nonCash";
                                            }
                                            $targetBranchInfo=DB::table('gnr_branch')->where('id', $tableRow->ftTo)->select('name', 'branchCode')->first();

                                            $singleAmount=$tableRow->amount;
                                            $totalOfSingleAmount= $singleAmount + $totalOfSingleAmount;

                                        ?>
                                        <tr class='valueRowFT'>
                                            {{-- <td class='debitAcc' > {{$tableRow->debitAcc}} </td> --}}


                                            <td style='padding: 8px 5px; text-align:left;' class='targetBranchFT'>
                                                <input type='hidden' class='targetBranchInputFT' value="{{$tableRow->ftTo}}">{{str_pad($targetBranchInfo->branchCode,3,"0",STR_PAD_LEFT)." - ".$targetBranchInfo->name}}
                                            </td>

                                            <td style='padding: 8px 5px; text-align:left;' class='targetBranchHeadFT'>
                                             @if ($tableRow->ftTargetAcc==0)
                                                <input type='hidden' class='targetBranchHeadInputFT' value="0">{{"N/A"}}
                                            @else
                                                <input type='hidden' class='targetBranchHeadInputFT' value="{{$tableRow->ftTargetAcc}}">{{$ftTargetAcc->code." - ".$ftTargetAcc->name}}
                                            @endif
                                            </td>

                                            <td style='padding: 8px 5px; text-align:left;' class='debitAcc'>
                                                <input type='hidden' class='debitAccInputFT' value="{{$tableRow->debitAcc}}">{{$debitAcc->code." - ".$debitAcc->name}}
                                            </td>

                                            <td style='padding: 8px 5px; text-align:left;'  class='creditAccFT'>
                                                <input type='hidden' class='creditAccInputFT' value="{{$tableRow->creditAcc}}">{{$creditAcc->code." - ".$creditAcc->name}}
                                            </td>

                                            {{-- <td class='creditAcc' > {{$tableRow->creditAcc}} </td> --}}
                                            <td style='padding: 8px 5px; text-align:right;' class='amountColumnFT' > {{$tableRow->amount}} </td>
                                            <td style='padding: 8px 5px; text-align:left;'  class='narrationFT' > {{$tableRow->localNarration}} </td>
                                            <td> <a href='javascript:;' class='removeButton glyphicon glyphicon-trash' style='color:red; font-size:14px'></a> </td>
                                            {{-- <td> <button href='javascript:;' class='removeButton glyphicon glyphicon-trash'> </button> </td>                                                        --}}
                                            {{-- <td> <button>Delete</button></td>                                                        --}}

{{-- <td class='creditAcc'><input type='hidden' class='' value={{$tableRow->debitAcc}}>creditAccName</td> --}}

                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <p id='tableFTe' style="max-height:3px; color: red;"></p>
                            </div>
                            <div class="col-md-0"></div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('globalNarrationFT', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}
                                <div class="col-sm-12">
                                    {!! Form::text('globalNarrationFT', $voucherInfo->globalNarration, ['class' => 'form-control', 'id' => 'globalNarrationFT', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                    <p id='globalNarrationFTe' style="max-height:3px; color:red;"></p>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-12" style="padding: 0px 30px 10px 30px; " >
                                <div class="form-group">
                                   {!! Form::label('imageFT', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                    <div class="col-sm-6">
                                        <div id="addMoreFTId" class="addMoreFTClass fetchFTData">
                                            {{-- <input type="file" name="imagePV[]"   class="editImagePV imageUpdatePV">       --}}  
                                                @if($voucherInfo->image)  
                                                @foreach(json_decode($voucherInfo->image, true) as $images)
                                                {{-- <input type="hidden" name="id" value="$voucherInfo->id"> --}}
                                                    <div class="col-sm-3" class ="previousImages" style="margin-top:10px;">
                                                        <img src="{{ asset('/images/vouchers/'.$images) }}"  height="70" width="70">
                                                        <button class="removeUploadedFTImage"  style="float:right; color:red; font-size:8px;">X</button>
                                                    </div>
                                                @endforeach
                                                @endif
                                        </div>
                                        <p id='imageFTe' style="max-height:3px;"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="padding: 0px 20px 15px 30px; ">
                                <p><input type="button" style="" name="addMoreFT" id="addMoreFT" class="btn btn-info" value="Add More"></p>
                            </div>  
                        <div class="form-group" >
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                            <div class="col-sm-9 text-right" style="padding-right: 45px;">
                                {{-- <input type="button" name="" id="submit" class="btn btn-info" value="Submit"> --}}
                                {!! Form::submit('Update', ['id' => 'updateSubmit', 'class' => 'btn btn-info']) !!}
                                {{--{{ Form::reset('Reset', ['class' => 'btn btn-warning']) }}--}}
                                <a href="{{url('/viewVoucher')}}" class="btn btn-danger closeBtn">Close</a>
                                <span id="success" style="color:green; font-size:16px;" class="pull-right"></span>
                            </div>
                        </div>
                        {!! Form::close() !!}

                        {{-- </div> --}}
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="footerTitle" style="border-top:1px solid white"></div> --}}
    </div>
<div class="col-md-1"></div>
</div>
</div>

{{-- <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js')}}"></script> --}}
{{-- <script src="{{ asset('js/select2/select2.min.js')}}"></script> --}}
<script type="text/javascript">
$(document).ready(function(){

    $("#debitAccFT, #creditAccFT").select2();
    $("#debitAccFT, #creditAccFT").next("span").css("width","100%");


    $("#voucherDateFT").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "2016:c",
        minDate: new Date(2016, 07 - 1, 01),
        maxDate: "dateToday",
        dateFormat: 'dd-mm-yy',
        onSelect: function () {
            $('#voucherDateFTe').hide();
        }
    });

// =================================================== Load Previous Value from DB===================================================

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    var projectIdValue = '<?php echo $voucherInfo->projectId; ?>';
    $('#projectIdFT').val(projectIdValue);

    $('#projectIdFT, #projectTypeIdFT, #voucherDateFT').prop("disabled", true);

    var projectTypeIdValue = '<?php echo $voucherInfo->projectTypeId; ?>';
    $('#projectTypeIdFT').val(projectTypeIdValue);


    $('#amount').on('input', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });
    var voucherTypeId="{{$voucherInfo->voucherTypeId}}";

    var totalOfSingleAmount="{{$totalOfSingleAmount}}";
    $('#totalAmountColumnFT').html(parseFloat(totalOfSingleAmount).toFixed(2));


    $("#projectIdFT").change(function () {
        var projectId = {{$voucherInfo->projectId}};
        var userBranchId = "{{$voucherInfo->branchId}}";
        // alert(projectId);
        // alert(userBranchId);
        var csrf = "{{csrf_token()}}";

        $.ajax({
            type: 'post',
            url: './getBranchNProjectTypeByProject',
            data: { projectId: projectId, _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                var branchList=data['branchList'];
                $("#targetBranchFT").empty();
                $("#targetBranchFT").prepend('<option  value="">Select Branch</option>');
                if(userBranchId!=1){
                    $("#targetBranchFT").append('<option  value="1">000 - Head Office</option>');
                }
                $.each(branchList, function(key, obj){
                    if(userBranchId!=obj.id){
                        $('#targetBranchFT').append("<option value='"+obj.id+"'>"+pad(obj.branchCode,3)+" - "+obj.name+"</option>");
                    }
                });
                $("#creditAccFT, #debitAccFT, #targetBranchHeadFT").empty();
                $("#creditAccFT, #debitAccFT, #targetBranchHeadFT").prepend('<option value="">Please Select Branch First</option>');

            },
            error: function(_response){
                alert("Error");
            }
        });
    });

    function changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check){
        var csrf = "{{csrf_token()}}";
        $.ajax({
            type: 'post',
            url: './getLedgersByBranches',
            data: { projectId: projectId, branchIdArray: branchIdArray, _token: csrf},
            dataType: 'json',
            success: function (data) {
                // alert(JSON.stringify(data));
                if(check=="targetBranchHeadFT"){
                    // alert(check);
                    var tBHeadFTVal=$("#targetBranchHeadFT").val();
                    // $("#targetBranchHeadFT").empty();
                    // $("#targetBranchHeadFT").prepend('<option selected="selected" value="">Select  Account</option>');

                    if(statusOfTargetHead=="firstLoad"){
                        // alert(statusOfTargetHead);
                        // var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        // $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                        // $.each(ledgersOfCashAndBank, function(key, obj){
                        //     $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        // });
                    }else if(statusOfTargetHead=="nonCash"){
                        // alert(statusOfTargetHead);
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                    }else if(statusOfTargetHead=="cashNBank"){
                        // alert(statusOfTargetHead);
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }

                    $("#targetBranchHeadFT").val(tBHeadFTVal);

                    $("#creditAccFT, #debitAccFT").empty();
                    $("#creditAccFT, #debitAccFT").prepend('<option selected="selected" value="">Select  Account</option>');
                    if(targetBranchHead==0){
                        var ledgersWithOutCashNBank=data['ledgersWithOutCashNBank'];
                        $.each(ledgersWithOutCashNBank, function(key, obj){
                            $('#creditAccFT, #debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else{
                        // var ledgersWithOutCashNBank=data['ledgersWithOutCashNBank'];
                        // $.each(ledgersWithOutCashNBank, function(key, obj){
                        //     $('#debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        // });
                        var ledgersOfCashAndBank = data['ledgersOfCashAndBank'];
                        var ledgersOfFTDebitCashAndBank = data['ledgersOfFTDebitCashAndBank'];

                        $.each(ledgersOfCashAndBank, function(key, obj){
                            // alert(obj);
                            $('#creditAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");

                        });
                        $.each(ledgersOfFTDebitCashAndBank, function(key, obj){
                            // alert(obj);
                            $('#debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");

                        });
                    }
                }else if(check=="fromAddButton"){
                    var tBHeadFTVal=$("#targetBranchHeadFT").val();
                    $("#targetBranchHeadFT").empty();
                    $("#targetBranchHeadFT").prepend('<option selected="selected" value="">Select  Account</option>');

                    if(statusOfTargetHead=="nonCash"){
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                    }else if(statusOfTargetHead=="cashNBank"){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }
                    $("#targetBranchHeadFT").val(tBHeadFTVal);

                    $("#creditAccFT, #debitAccFT").empty();
                    $("#creditAccFT, #debitAccFT").prepend('<option selected="selected" value="">Select  Account</option>');
                    if(targetBranchHead==0){
                        var ledgersWithOutCashNBank=data['ledgersWithOutCashNBank'];
                        $.each(ledgersWithOutCashNBank, function(key, obj){
                            $('#creditAccFT, #debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else{
                        var ledgersOfFTDebitCashAndBank = data['ledgersOfFTDebitCashAndBank'];
                        $.each(ledgersOfFTDebitCashAndBank, function(key, obj){
                            $('#debitAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#creditAccFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }
                }else if(check=="targetBranchFT"){
                    // alert(check);
                    $("#targetBranchHeadFT").empty();
                    $("#targetBranchHeadFT").prepend('<option selected="selected" value="">Select  Account</option>');

                    if(statusOfTargetHead=="firstLoad"){
                        // alert(statusOfTargetHead);
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else if(statusOfTargetHead=="nonCash"){
                        // alert(statusOfTargetHead);
                        $("#targetBranchHeadFT").append('<option  value="0">Non Cash</option>');
                    }else if(statusOfTargetHead=="cashNBank"){
                        // alert(statusOfTargetHead);
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#targetBranchHeadFT').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }

                    $("#creditAccFT, #debitAccFT").empty();
                    $("#creditAccFT, #debitAccFT").prepend('<option value="">Please Select Target Branch Cash/Bank First</option>');
                }

            },
            error: function(_response){
                alert("Error");
            }
        });
    }
    $("#targetBranchFT").change(function () {
        var projectId = $("#projectIdFT").val();
        var branchId = this.value;
        var targetBranchHead = 0;
        // alert(projectId);
        // alert(branchId);

        var branchIdArray = new Array();
        branchIdArray.push(branchId);

        var idVal="FT";
        var check="targetBranchFT";

        changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check);
    });

    // $(document).on('change', '#targetBranchHeadFT', function() {
    $("#targetBranchHeadFT").change(function () {
        var projectId = $("#projectIdFT").val();
        var branchId = $("#targetBranchFT").val();
        var userBranchId="{{$branchId}}";
        var targetBranchHead = this.value;

        var branchIdArray = new Array();
        branchIdArray.push(userBranchId);
        // branchIdArray.push(branchId);
        var check="targetBranchHeadFT";
        changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check);
    });

    $("#projectIdFT").trigger('change');

    var statusOfTargetHead = "firstLoad";
    // var statusOfTargetHead = "{{$statusOfTargetHead}}";

    function tbodyAppend(idVal, ideVal){

    }

    $("#addFT").click(function(){

        var idVal="FT";
        var ideVal="FTe";

        var msg="The Field is Required";
        var projectId = $("#projectId"+idVal+" option:selected").val();
        if(projectId){ $('#projectId'+ideVal).hide(); }else{ $('#projectId'+ideVal).show(); $('#projectId'+ideVal).html(msg); return false;}

        var projectTypeId = $("#projectTypeId"+idVal+" option:selected").val();
        if(projectTypeId){ $('#projectTypeId'+ideVal).hide(); }else{ $('#projectTypeId'+ideVal).show(); $('#projectTypeId'+ideVal).html(msg); return false;}

        var voucherDate = $("#voucherDate"+idVal).val();
        if(voucherDate){ $('#voucherDate'+ideVal).hide(); }else{ $('#voucherDate'+ideVal).show(); $('#voucherDate'+ideVal).html(msg); return false;}

        var voucherCode = $("#voucherCode"+idVal).val();
        if(voucherCode){ $('#voucherCode'+ideVal).hide(); }else{ $('#voucherCode'+ideVal).show(); $('#voucherCode'+ideVal).html(msg); return false;}

        var debitAcc = $("#debitAcc"+idVal).val();
        if(debitAcc){ $('#debitAcc'+ideVal).hide(); }else{ $('#debitAcc'+ideVal).show(); $('#debitAcc'+ideVal).html(msg); return false;}

        var creditAcc = $("#creditAcc"+idVal).val();
        if(creditAcc){ $('#creditAcc'+ideVal).hide(); }else{ $('#creditAcc'+ideVal).show(); $('#creditAcc'+ideVal).html(msg); return false;}

        var amount = $("#amount"+idVal).val();
        if(amount){ $('#amount'+ideVal).hide(); }else{ $('#amount'+ideVal).show(); $('#amount'+ideVal).html(msg); return false;}

        var narration = $("#narration"+idVal).val();
        if(narration){$('#narration'+ideVal).hide();}else{$('#narration'+ideVal).show(); $('#narration'+ideVal).html(msg); return false;}

        if(debitAcc==creditAcc){
            $('#debitAcc'+ideVal).show();
            $('#debitAcc'+ideVal).html("Debit and Credit Are Same!!! Please Select Again");
            $('#debitAcc'+idVal).val("");
            $('#creditAcc'+ideVal).show();
            $('#creditAcc'+ideVal).html("Debit and Credit Are Same!!! Please Select Again");
            $('#creditAcc'+idVal).val("");
            return false;
        }else{
            $('#debitAcc'+ideVal).hide();
            $('#creditAcc'+ideVal).hide();
        }

        var debitAcc = $("#debitAcc"+idVal+" option:selected").val();
        var creditAcc = $("#creditAcc"+idVal+" option:selected").val();
        var debitAccName = $("#debitAcc"+idVal+" option:selected").html();
        var creditAccName = $("#creditAcc"+idVal+" option:selected").html();

        var amount = parseFloat($("#amount"+idVal).val());
        var totalAmount = parseFloat($("#totalAmountColumn"+idVal).text());
        var narration = $("#narration"+idVal).val();

        totalAmount =amount+totalAmount;
        $("#totalAmountColumn"+idVal).text(totalAmount.toFixed(2));


            var targetBranch = $("#targetBranch"+idVal+" option:selected").val();
            var targetBranchHead = $("#targetBranchHead"+idVal+" option:selected").val();
            var targetBranchName = $("#targetBranch"+idVal+" option:selected").html();
            // alert(targetBranchHead);
            if(targetBranchHead==0){
                var targetBranchHeadName = "N/A";
                statusOfTargetHead = "nonCash";
            }else{
                var targetBranchHeadName = $("#targetBranchHead"+idVal+" option:selected").html();
                statusOfTargetHead = "cashNBank";
            }

            var markup =
                "<tr class='valueRow"+idVal+"'>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='targetBranch"+idVal+"'>" +
                    "<input type='hidden' class='targetBranchInput"+idVal+"' value='"+targetBranch+"'>"+targetBranchName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='targetBranchHead"+idVal+"'>" +
                    "<input type='hidden' class='targetBranchHeadInput"+idVal+"' value='"+targetBranchHead+"'>"+targetBranchHeadName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='debitAcc"+idVal+"'>" +
                    "<input type='hidden' class='debitAccInput"+idVal+"' value='"+debitAcc+"'>"+debitAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:left;' class='creditAcc"+idVal+"'>"+
                    "<input type='hidden' class='creditAccInput"+idVal+"' value='"+creditAcc+"'>"+creditAccName+"" +
                    "</td>" +
                    "<td style='padding: 8px 5px; text-align:right;' class='amountColumn"+idVal+"' >" +amount+ "</td>" +
                    "<td style='padding: 8px 5px; text-align:Left;' class='narration"+idVal+"'>"+narration+"</td>" +
                    // "<td style='padding: 8px 5px; text-align:center;' ><button class='removeButton"+idVal+"'>Delete</button></td>" +
                    "<td style='padding: 8px 5px; text-align: center;'><a href='javascript:;' class='removeButton"+idVal+"'><i class=' glyphicon glyphicon-trash' style='color:red; font-size:14px'><i></a></td>" +
                "</tr>";


        $("#headerRow"+idVal).after(markup);

        // $('#debitAcc'+idVal).val("");
        // $('#creditAcc'+idVal).val("");
        $('#amount'+idVal).val("");
        $('#narration'+idVal).val("");

        $('#projectId'+idVal).prop("disabled", true);
        $('#projectTypeId'+idVal).prop("disabled", true);
        $('#voucherDate'+idVal).prop("disabled", true);
        $('#voucherDate'+idVal).css("cursor","not-allowed");

        var projectId=$('#projectId'+idVal).val();
        var check="fromAddButton";

            var projectId = $("#projectIdFT").val();
            var branchId = $("#targetBranchFT").val();
            var targetBranchHead = $("#targetBranchHeadFT").val();
            // alert(targetBranchHead);
            var userBranchId="{{$branchId}}";

            var branchIdArray = new Array();
            branchIdArray.push(userBranchId);
            branchIdArray.push(branchId);
            var check="fromAddButton";
            changeLedgersByTargetBranch(projectId, branchIdArray, targetBranchHead, check);

    });


// ===================================================End Load Previous Value from DB===================================================

// ===============================================insert value into the table (headerRow)===============================================

    $(document).on('click', '.removeButton', function () {
        var tdAmount = parseFloat($(this).closest('tr').find('.amountColumnFT').text());
        $("#totalAmountColumnFT").text((parseFloat($("#totalAmountColumnFT").html())-tdAmount).toFixed(2));
        $(this).closest('tr').remove();
        return false;
    });

//===========================================END of insert value into the table (headerRow)===========================================

// ====================================================Send & Save into DB->Table====================================================
var removed_images = new Array();
    var total_file = 0;
    var max_fields = 10;
    var i = 1;

    var tweetParentsArray = [];

    $('#addMoreFT').on('click',function(){   
            if(i < max_fields){
                $('#addMoreFTId').append('<div class="col-sm-12 extraImgInput"  style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imageFT[]"   class="imageFTChange imageUpdateFT test"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
            } else{
                alert('Maximum '+max_fields+' images can be uploaded.');
            }

            $(".imageFTChange").on('change',function(){

                total_file=document.getElementsByClassName("imageUpdateFT").length;

                console.log('total_file : ', total_file);
                console.log('total_file : ', document.getElementsByClassName("imageUpdateFT"));

                for(var i=0; i<total_file; i++){
                    $('.fetchFTData').append('<div class="col-sm-3" id ="imageFTRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removeFTImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
                    $('.extraImgInput').hide();
                }
            });
        });

        $('#addMoreFTId').on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('div').parent('div').remove();
              i--;
        });

        //remove image div
       
        $('.fetchFTData').on("click",".removeUploadedFTImage",function(){
            var images =   $(this).parent('div').find('img').attr('src'); 
            var a =  images.split("/");
            removed_images.push(a[a.length-1]);
            console.log(removed_images);
            $(this).parent('div').remove();
        });

        $('#addMoreFTId').on("click",".removeFTImage",function(){
            tweetParentsArray.push($(this).attr("data-id"));
            $(this).parent('div').remove();
        });

    // $("#submit").click(function(){
//     $('form').submit(function( event ) {
//         event.preventDefault();
//         $("#updateSubmit").prop("disabled", true);

//         //Get all the vlaues
//         var ftId = "{{DB::table('acc_voucher')->where('id', $voucherInfo->id)->value('ftId')}}";
//         var prepBy = "{{$userId}}";
//         var branchIdFrom = "{{$branchId}}";
//         var companyId = "{{$branch->companyId}}";
//         var voucherIdFrom = "{{$voucherInfo->id}}";
//         var projectId = $("#projectIdFT option:selected").val();
//         var projectTypeId = $("#projectTypeIdFT option:selected").val();
//         var voucherDate = $("#voucherDateFT").val();
//         var voucherCode = $("#voucherCodeFT").val();
//         var voucherTypeId = "{{$voucherInfo->voucherTypeId}}";
//         var createdDate = "{{$voucherInfo->createdDate}}";
//         var globalNarration = $("#globalNarrationFT").val();

//         // var oldTargetBranchArray = <?php echo json_encode($oldTargetBranchArray); ?>;

//         var oldTargetBranchArray = "{{json_encode($oldTargetBranchArray)}}";


//         var csrf = "<?php echo csrf_token(); ?>";

//         //Get Table Data for Journal Details Table
//         var tableTargetBranch = new Array();
//         var tableTargetBranchHead = new Array();
//         var tableDebitAcc = new Array();
//         var tableCreditAcc = new Array();
//         var tableAmount = new Array();
//         var tableNarration = new Array();

//         $("#addFTTable tr.valueRowFT").each(function(){
//             tableTargetBranch.push(JSON.stringify($(this).find('.targetBranchInputFT').val()));
//             tableTargetBranchHead.push(JSON.stringify($(this).find('.targetBranchHeadInputFT').val()));
//             tableDebitAcc.push(JSON.stringify($(this).find('.debitAccInputFT').val()));
//             tableCreditAcc.push(JSON.stringify($(this).find('.creditAccInputFT').val()));
//             tableAmount.push(JSON.stringify($(this).find('.amountColumnFT').html()));
//             tableNarration.push(JSON.stringify($(this).find('.narrationFT').html()));
//         });

//         // alert(ftId);
//         // alert(voucherIdFrom);
//         // alert(oldTargetBranchArray);
//         // alert(tableTargetBranch);
//         // alert(tableTargetBranchHead);
//         // alert(tableDebitAcc);
//         // alert(tableCreditAcc);
//         // alert(tableAmount);
//         // alert(tableNarration);

//         var amountColumn = $(".amountColumnFT").html(); //alert(amountColumn);
//         $.ajax({
//             type: 'post',
//             url: './updateFTVoucherItem',
// //                data: $('form').serialize(),
//             data: { prepBy: prepBy, ftId: ftId, voucherIdFrom: voucherIdFrom, createdDate:createdDate, voucherTypeId:voucherTypeId, amountColumn:amountColumn, branchIdFrom: branchIdFrom, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, oldTargetBranchArray: oldTargetBranchArray, tableTargetBranch: tableTargetBranch, tableTargetBranchHead: tableTargetBranchHead, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },
//             dataType: 'json',

//             success: function( _response ){
//                 // alert(JSON.stringify(_response));
//                 if (_response.errors) {
//                     $("#updateSubmit").prop("disabled", false);
//                     if (_response.errors['projectId']) {
//                         $('#projectIdFTe').empty();
//                         $('#projectIdFTe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
//                         return false;
//                     }
//                     if (_response.errors['projectTypeId']) {
//                         $('#projectTypeIdFTe').empty();
//                         $('#projectTypeIdFTe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
//                         return false;
//                     }
//                     if (_response.errors['voucherDate']) {
//                         $('#voucherDateFTe').empty();
//                         $('#voucherDateFTe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
//                         return false;
//                     }
//                     if (_response.errors['voucherCode']) {
//                         $('#voucherCodeFTe').empty();
//                         $('#voucherCodeFTe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
//                         return false;
//                     }
//                     if (_response.errors['amountColumn']) {
//                         $('#tableFTe').empty();
//                         $('#tableFTe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
//                         return false;
//                     }
//                     if (_response.errors['globalNarration']) {
//                         $('#globalNarrationFTe').empty();
//                         $('#globalNarrationFTe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
//                         return false;
//                     }

//                 } else {
//                     // document.getElementById("msg").innerHTML =responseText ;
//                     alert(JSON.stringify(_response.responseText));
//                     //$('.item' + $('.id').text()).remove();
//                     // location.reload();
//                     window.location.href = '{{url('/viewVoucher/') }}';
//                 }
//             },
//             error: function( _response ){
//                 // Handle error
//                 alert('Could Not Update!!!');
//             }
//         });


//     });

$('form').submit(function( event ) {
        event.preventDefault();
        $("#updateSubmit").prop("disabled", true);

        //Get all the vlaues
        var ftId = "{{DB::table('acc_voucher')->where('id', $voucherInfo->id)->value('ftId')}}";
        var prepBy = "{{$userId}}";
        var branchIdFrom = "{{$branchId}}";
        var companyId = "{{$branch->companyId}}";
        var voucherId = "<?php echo $voucherInfo->id; ?>";
        var voucherIdFrom = "{{$voucherInfo->id}}";
        var projectId = $("#projectIdFT option:selected").val();
        var projectTypeId = $("#projectTypeIdFT option:selected").val();
        var voucherDate = $("#voucherDateFT").val();
        var voucherCode = $("#voucherCodeFT").val();
        var voucherTypeId = "{{$voucherInfo->voucherTypeId}}";
        var createdDate = "{{$voucherInfo->createdDate}}";
        var globalNarration = $("#globalNarrationFT").val();

        // var oldTargetBranchArray = <?php echo json_encode($oldTargetBranchArray); ?>;

        var oldTargetBranchArray = "{{json_encode($oldTargetBranchArray)}}";


        var csrf = "<?php echo csrf_token(); ?>";

        //Get Table Data for Journal Details Table
        var tableTargetBranch = new Array();
        var tableTargetBranchHead = new Array();
        var tableDebitAcc = new Array();
        var tableCreditAcc = new Array();
        var tableAmount = new Array();
        var tableNarration = new Array();

        $("#addFTTable tr.valueRowFT").each(function(){
            tableTargetBranch.push($(this).find('.targetBranchInputFT').val());
            tableTargetBranchHead.push($(this).find('.targetBranchHeadInputFT').val());
            tableDebitAcc.push($(this).find('.debitAccInputFT').val());
            tableCreditAcc.push($(this).find('.creditAccInputFT').val());
            tableAmount.push($(this).find('.amountColumnFT').html());
            tableNarration.push($(this).find('.narrationFT').html());
        });

        // alert(ftId);
        // alert(voucherIdFrom);
        // alert(oldTargetBranchArray);
        // alert(tableTargetBranch);
        // alert(tableTargetBranchHead);
        // alert(tableDebitAcc);
        // alert(tableCreditAcc);
        // alert(tableAmount);
        // alert(tableNarration);

        var amountColumn = $(".amountColumnFT").html(); //alert(amountColumn);
        formData = new FormData();
        var totalFiles = document.getElementsByClassName("imageUpdateFT").length; 
            for(var index = 0; index < totalFiles; index++){
                var check = 0;
                for(var i = 0; i < tweetParentsArray.length; i++) {
                    if (tweetParentsArray[i] == index) {
                        ++check;
                    }
                }
                if (check == 0) {
                    formData.append('image[]',document.getElementsByClassName("imageUpdateFT")[index].files[0]);
                }
              // formData.append('image[]',document.getElementsByClassName("imageUpdatePV")[index].files[0]); 
            };
        formData.append('voucherId', voucherId);
        formData.append('prepBy', prepBy);
        formData.append('ftId', ftId);
        formData.append('voucherIdFrom', voucherIdFrom);
        formData.append('createdDate', createdDate);
        formData.append('voucherTypeId', voucherTypeId);
        formData.append('amountColumn', amountColumn);
        formData.append('branchIdFrom', branchIdFrom);
        formData.append('companyId', companyId);
        formData.append('projectId', projectId);
        formData.append('projectTypeId', projectTypeId);
        formData.append('voucherDate', voucherDate);
        formData.append('voucherCode', voucherCode);
        formData.append('oldTargetBranchArray', oldTargetBranchArray);
        formData.append('tableTargetBranch', JSON.stringify(tableTargetBranch));
        formData.append('tableTargetBranchHead', JSON.stringify(tableTargetBranchHead));
        formData.append('tableAmount', JSON.stringify(tableAmount));
        formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
        formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
         formData.append('previousImages', JSON.stringify(removed_images));
        formData.append('tableNarration', JSON.stringify(tableNarration));
        formData.append('globalNarration', globalNarration);
        formData.append('_token', csrf);
           
        $.ajax({
             processData: false,
            contentType:false,
            type: 'post',
            url: './updateFTVoucherItem',
            data: formData,
            // { prepBy: prepBy, ftId: ftId, voucherIdFrom: voucherIdFrom, createdDate:createdDate, voucherTypeId:voucherTypeId, amountColumn:amountColumn, branchIdFrom: branchIdFrom, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, oldTargetBranchArray: oldTargetBranchArray, tableTargetBranch: tableTargetBranch, tableTargetBranchHead: tableTargetBranchHead, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },
            dataType: 'json',

            success: function( _response ){
                // alert(JSON.stringify(_response));
                if (_response.errors) {
                    $("#updateSubmit").prop("disabled", false);
                    if (_response.errors['projectId']) {
                        $('#projectIdFTe').empty();
                        $('#projectIdFTe').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                        return false;
                    }
                    if (_response.errors['projectTypeId']) {
                        $('#projectTypeIdFTe').empty();
                        $('#projectTypeIdFTe').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherDate']) {
                        $('#voucherDateFTe').empty();
                        $('#voucherDateFTe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                        return false;
                    }
                    if (_response.errors['voucherCode']) {
                        $('#voucherCodeFTe').empty();
                        $('#voucherCodeFTe').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                        return false;
                    }
                    if (_response.errors['amountColumn']) {
                        $('#tableFTe').empty();
                        $('#tableFTe').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
                        return false;
                    }
                    if (_response.errors['globalNarration']) {
                        $('#globalNarrationFTe').empty();
                        $('#globalNarrationFTe').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
                        return false;
                    }

                } else {
                    // document.getElementById("msg").innerHTML =responseText ;
                    alert(JSON.stringify(_response.responseText));
                    //$('.item' + $('.id').text()).remove();
                    // location.reload();
                    window.location.href = '{{url('/viewVoucher/') }}';
                }
            },
            error: function( _response ){
                // Handle error
                alert('Could Not Update!!!');
            }
        });


    });
// ==================================================End JavaScript for Voucher==================================================


});     //END of document.ready function
</script>


@endsection
