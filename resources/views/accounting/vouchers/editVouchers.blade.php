@extends('layouts/acc_layout')
@section('title')
@if ($voucherInfo->voucherTypeId==1)
    {{" | Edit Debit Voucher"}}
@elseif ($voucherInfo->voucherTypeId==2)
    {{" | Edit Credit Voucher"}}
@elseif ($voucherInfo->voucherTypeId==3)
    {{" | Edit Journal Voucher"}}
@elseif ($voucherInfo->voucherTypeId==4)
    {{" | Edit Contra Voucher"}}
@endif
@endsection

@section('content')

<?php
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $branchId = Session::get('branchId');

    Session::put('id', $user->id);
    $userId = Session::get('id');
    $emp_id_fk = DB::table('users')->where('id',$userId)->value('emp_id_fk');
    $branch = DB::table('gnr_branch')->where('id', $voucherInfo->branchId)->select('name','companyId','branchCode')->first();
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
                                <div class="panel-title col-md-4">
                                {{"Edit "}}
                                @if ($voucherInfo->voucherTypeId==1)
                                    {{"Debit"}}
                                @elseif ($voucherInfo->voucherTypeId==2)
                                    {{"Credit"}}
                                @elseif ($voucherInfo->voucherTypeId==3)
                                    {{"Journal"}}
                                @elseif ($voucherInfo->voucherTypeId==4)
                                    {{"Contra"}}
                                @endif
                                 {{" Voucher"}}
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-4"><h4 style="color: black;"><?php echo "Total Amount: ";?><span id="totalAmountColumn">0.0</span><?php echo " Tk";?></h4></div>
                                {{--<div class="col-md-1"></div>--}}
                                <div class=" col-md-3"><h4 style="color: black;"><?php echo "Branch: ";?><strong >{{$branch->name}}</strong></h4></div>
                            </div>

                            <div class="row">
                                {!! Form::open(array('url' => '', 'role' => 'form','enctype' => 'multipart/form-data', 'class'=>'form-horizontal form-groups')) !!}

                                {!! Form::text('id', $value = $voucherInfo->id, ['class' => 'form-control hidden', 'id' => 'id', 'type' => 'text']) !!}
                                {{-- {{ Form::hidden('journalTab', 3, array('id' => 'journalTabValue')) }} --}}

                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div>
                                                @if (Session::has('responseText'))
                                                    <strong>Note!</strong> {!! Session::get('responseText') !!}
                                                @endif
                                            </div>
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

                                            <div class="col-md-3" hidden>
                                                <div class="form-group">        {{--project type--}}
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
                                                    {!! Form::label('voucherDate', 'Voucher Date:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        {!! Form::text('voucherDate', date('d-m-Y',strtotime($voucherInfo->voucherDate)), ['class' => 'form-control', 'id' => 'voucherDate','readonly' => 'true'])!!}
                                                        <p id='voucherDatee' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('voucherCode', 'Voucher Code:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        {!! Form::text('voucherCode', $voucherInfo->voucherCode, ['class' => 'form-control', 'id' => 'voucherCode', 'type' => 'text', 'readonly' => 'true']) !!}
                                                        <p id='voucherCodee' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                        @if ($voucherInfo->voucherTypeId==1)
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('creditAcc', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        <select class ="form-control" id = "creditAcc" name="creditAcc">
                                                            <option value="">Please Select Project First</option>
                                                            {{-- <option value="">Select Credit Account</option>
                                                            @foreach($ledgerAccounts as $ledgerAccount)
                                                                <option value="{{$ledgerAccount->id}}">{{$ledgerAccount->name}}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        <p id='creditAcce' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('debitAcc', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        <select class ="form-control" id = "debitAcc" name="debitAcc">
                                                            <option value="">Please Select Project First</option>
                                                            {{-- <option value="">Select Debit Account</option>
                                                            @foreach($ledgerAccounts as $ledgerAccount)
                                                                <option value="{{$ledgerAccount->id}}">{{$ledgerAccount->name}}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        <p id='debitAcce' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>

                                        @else
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('debitAcc', 'Debit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        <select class ="form-control" id = "debitAcc" name="debitAcc">
                                                            <option value="">Please Select Project First</option>
                                                            {{-- <option value="">Select Debit Account</option>
                                                            @foreach($ledgerAccounts as $ledgerAccount)
                                                                <option value="{{$ledgerAccount->id}}">{{$ledgerAccount->name}}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        <p id='debitAcce' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('creditAcc', 'Credit Account:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        <select class ="form-control" id = "creditAcc" name="creditAcc">
                                                            <option value="">Please Select Project First</option>
                                                            {{-- <option value="">Select Credit Account</option>
                                                            @foreach($ledgerAccounts as $ledgerAccount)
                                                                <option value="{{$ledgerAccount->id}}">{{$ledgerAccount->name}}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        <p id='creditAcce' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif


                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('amount', 'Amount:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        {!!  Form::text('amount', $value = null, ['class' => 'form-control', 'id' => 'amount', 'type' => 'text','min'=>'1', 'placeholder' => 'Enter Amount']) !!}
                                                        <p id='amounte' style="max-height:3px; color:red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('narration', 'Narration/ Cheque Details:', ['class' => 'col-sm-12 control-label']) !!}
                                                    <div class="col-sm-12">
                                                        {!! Form::text('narration', $value = null, ['class' => 'form-control', 'id' => 'narration', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                        <p id='narratione' style="max-height:3px; color: red;"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {{--                                    {!! Form::label('submit', ' ', ['class' => 'col-sm-12 control-label']) !!}--}}

                                        <div style="padding-right: 30px;">
                                            <button class="btn btn-info" id="add" style="float: right; " type="button">Add</button>

                                        </div>
                                    </div>

                                </div>
{{-- {{$voucherInfo->id}} --}}
{{-- {{$voucherInfo->createdDate}} --}}
                                <div class="row" style="padding-bottom: 20px">
                                    <div class="col-md-0"></div>
                                        <div class="col-md-12"  style="padding: 0px 45px 0px 45px; ">
                                            <table id="addTable" class="table table-striped table-bordered" style="color: black;">
                                                <thead>
                                                    <tr id="headerRow">
                                                    @if ($voucherInfo->voucherTypeId==1)
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;" >Credit Account</th>
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;">Debit Account</th>
                                                    @else
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;">Debit Account</th>
                                                        <th style="padding: 10px 5px; width: 30%; text-align:center;" >Credit Account</th>
                                                    @endif

                                                        <th style="padding: 10px 5px; width: 12%; text-align:center;">Amount</th>
                                                        <th style="padding: 10px 5px; width: 20%; text-align:center;">Narration / Cheque Details</th>
                                                        <th style="padding: 10px 5px; width: 8%; text-align:center;">Actions</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    $tableRows=DB::table('acc_voucher_details')->where('voucherId', $voucherInfo->id)->select('debitAcc','creditAcc','amount','localNarration')->get();
                                                    $x=0;
                                                    $singleAmount=0;
                                                    $totalOfSingleAmount=0;
                                                    ?>
                                                    @foreach($tableRows as $tableRow)
                                                    <?php $x++; ?>

                                                    <?php
                                                        $debitAcc=DB::table('acc_account_ledger')->where('id', $tableRow->debitAcc)->select('id','name', 'code')->first();
                                                        $creditAcc=DB::table('acc_account_ledger')->where('id', $tableRow->creditAcc)->select('id','name', 'code')->first();

                                                        $singleAmount=$tableRow->amount;
                                                        $totalOfSingleAmount= $singleAmount + $totalOfSingleAmount;

                                                    ?>
                                                    <tr class='valueRow'>
                                                        {{-- <td class='debitAcc' > {{$tableRow->debitAcc}} </td> --}}

                                                        @if ($voucherInfo->voucherTypeId==1)
                                                            <td style='padding: 8px 5px; text-align:left;'  class='creditAcc'>
                                                                <input type='hidden' class='creditAccInput' value={{$tableRow->creditAcc}}>{{$creditAcc->code." - ".$creditAcc->name}}
                                                            </td>
                                                            <td style='padding: 8px 5px; text-align:left;' class='debitAcc'>
                                                                <input type='hidden' class='debitAccInput' value={{$tableRow->debitAcc}}>{{$debitAcc->code." - ".$debitAcc->name}}
                                                            </td>
                                                        @else
                                                            <td style='padding: 8px 5px; text-align:left;' class='debitAcc'>
                                                                <input type='hidden' class='debitAccInput' value={{$tableRow->debitAcc}}>{{$debitAcc->code." - ".$debitAcc->name}}
                                                            </td>
                                                            <td style='padding: 8px 5px; text-align:left;'  class='creditAcc'>
                                                                <input type='hidden' class='creditAccInput' value={{$tableRow->creditAcc}}>{{$creditAcc->code." - ".$creditAcc->name}}
                                                            </td>
                                                        @endif

                                                        {{-- <td class='creditAcc' > {{$tableRow->creditAcc}} </td> --}}
                                                        <td style='padding: 8px 5px; text-align:right;' class='amountColumn' > {{$tableRow->amount}} </td>
                                                        <td style='padding: 8px 5px; text-align:left;'  class='narration' > {{$tableRow->localNarration}} </td>
                                                        <td> <a href='javascript:;' class='removeButton glyphicon glyphicon-trash' style='color:red; font-size:14px'></a> </td>
                                                        {{-- <td> <button href='javascript:;' class='removeButton glyphicon glyphicon-trash'> </button> </td>                                                        --}}
                                                        {{-- <td> <button>Delete</button></td>                                                        --}}

        {{-- <td class='creditAcc'><input type='hidden' class='' value={{$tableRow->debitAcc}}>creditAccName</td> --}}

                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                            <p id='tablee' style="max-height:3px; color: red;"></p>
                                        </div>
                                        <div class="col-md-0"></div>
                                    </div>
                                    <div class="col-md-12"></div>

                                    <div class="col-md-12" style="padding: 0px 30px 20px 30px; ">
                                        <div class="form-group">
                                            {!! Form::label('globalNarration', 'Global Narration Details:', ['class' => 'col-sm-12 control-label']) !!}


                                            <div class="col-sm-12">
                                                {!! Form::text('globalNarration',  $voucherInfo->globalNarration, ['class' => 'form-control', 'id' => 'globalNarration', 'type' => 'text', 'placeholder' => 'Enter Details']) !!}
                                                <p id='globalNarratione' style="max-height:3px;"></p>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="col-md-12" style="padding: 0px 30px 10px 30px; " >
                                            <div class="form-group">
                                               {!! Form::label('imagePV', 'Voucher Image', ['class' => 'col-sm-12 control-label']) !!}
                                                <div class="col-sm-6">
                                                    <div id="addMorePVId" class="addMorePVClass fetchPVData">
                                                        {{-- <input type="file" name="imagePV[]"   class="editImagePV imageUpdatePV">       --}}
                                                            @if($voucherInfo->image)  
                                                                 @foreach(json_decode($voucherInfo->image, true) as $images)
                                                            {{-- <input type="hidden" name="id" value="$voucherInfo->id"> --}}
                                                                <div class="col-sm-3" class ="previousImages" style="margin-top:10px;">
                                                                    <img src="{{ asset('/images/vouchers/'.$images) }}"  height="70" width="70">
                                                                    <button class="removeUploadedPVImage"  style="float:right; color:red; font-size:8px;">X</button>
                                                                </div>
                                                            @endforeach
                                                            @endif
                                                           
                                                    </div>
                                                    <p id='imagePVe' style="max-height:3px;"></p>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-12" style="padding: 0px 20px 15px 30px; ">
                                            <p><input type="button" style="" name="addMorePV" id="addMorePV" class="btn btn-info" value="Add More"></p>
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

                            </div>
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
<script type="text/javascript">
    // $("#debitAcc, #creditAcc").select2({

    // });
    // $("#debitAcc, #creditAcc").next("span").css("width","100%");
</script>

{{-- <script src="{{asset('js/jquery-1.11.1.min.js')}}"></script> --}}

<script>
    $(document).ready(function(){

        $("#debitAcc, #creditAcc").select2();
        $("#debitAcc, #creditAcc").next("span").css("width","100%");

        $("#voucherDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2016:c",
            minDate: new Date(2016, 07 - 1, 01),
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#voucherDatee').hide();
            }
        });

// =================================================== Load Previous Value from DB===================================================

        var projectIdValue = '<?php echo $voucherInfo->projectId; ?>';
        $('#projectId').val(projectIdValue);

        $('#projectId, #projectTypeId, #voucherDate').prop("disabled", true);

        // var projectTypeIdValue = '<?php echo $voucherInfo->projectTypeId; ?>';
        // $('#projectTypeId').val(projectTypeIdValue);

        var totalOfSingleAmount="{{$totalOfSingleAmount}}";

        $('#totalAmountColumn').html(parseFloat(totalOfSingleAmount).toFixed(2));

        $('#amount').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        var voucherTypeId="{{$voucherInfo->voucherTypeId}}";

// ===================================================End Load Previous Value from DB===================================================

// ===============================================insert value into the table (headerRow)===============================================
        var totalAmount=0.0;
        $("#add").click(function(){

            var msg="The Field is Required";
            var projectId = $("#projectId option:selected").val();
            if(projectId){ $('#projectIde').hide(); }else{ $('#projectIde').html(msg); return false;}

            var projectTypeId = $("#projectTypeId option:selected").val();
            if(projectTypeId){ $('#projectTypeIde').hide(); }else{ $('#projectTypeIde').html(msg); return false;}

            var voucherDate = $("#voucherDate").val();
            if(voucherDate){ $('#voucherDatee').hide(); }else{ $('#voucherDatee').html(msg); return false;}

            var voucherCode = $("#voucherCode").val();
            if(voucherCode){ $('#voucherCodee').hide(); }else{ $('#voucherCodee').html(msg); return false;}

            var debitAcc = $("#debitAcc").val();
            if(debitAcc){ $('#debitAcce').hide(); }else{ $('#debitAcce').show(); $('#debitAcce').html(msg); return false;}

            var creditAcc = $("#creditAcc").val();
            if(creditAcc){ $('#creditAcce').hide(); }else{ $('#creditAcce').show(); $('#creditAcce').html(msg); return false;}

            var amount = $("#amount").val();
            if(amount){ $('#amounte').hide(); }else{ $('#amounte').show();  $('#amounte').html(msg); return false;}

            var narration = $("#narration").val();
            if(narration){$('#narratione').hide();}else{$('#narratione').show(); $('#narratione').html(msg); return false;}

            if(debitAcc==creditAcc){
                $('#debitAcce').html("Debit and Credit Are Same!!! Please Select Again");
                $('#debitAcc').val("");
                $('#creditAcce').html("Debit and Credit Are Same!!! Please Select Again");
                $('#creditAcc').val("");
                return false;
            }else{
                $('#debitAcce').hide();
                $('#creditAcce').hide();
            }

            var projectId = $("#projectId option:selected").val();
            var projectTypeId = $("#projectTypeId option:selected").val();
            var voucherDate = $("#voucherDate").val();
            var voucherCode = $("#voucherCode").val();
            var debitAcc = $("#debitAcc option:selected").val();
            var creditAcc = $("#creditAcc option:selected").val();
            var debitAccName = $("#debitAcc option:selected").html();
            var creditAccName = $("#creditAcc option:selected").html();
            var amount = $("#amount").val();
            var narration = $("#narration").val();

            var amount = parseFloat($("#amount").val());
            var totalAmount = parseFloat($("#totalAmountColumn").text());

            totalAmount =amount+totalAmount;
            $("#totalAmountColumn").text(totalAmount.toFixed(2));
            if(voucherTypeId==1){
                var markup =
                    "<tr class='valueRow'>" +
                        "<td style='padding: 8px 5px; text-align:left;' class='creditAcc'>"+
                        "<input type='hidden' class='creditAccInput' value='"+creditAcc+"'>"+creditAccName+"" +
                        "</td>" +
                        "<td style='padding: 8px 5px; text-align:left;' class='debitAcc'>" +
    //                            "<input type='hidden' class='no' value='"+no+"'>" +
                        "<input type='hidden' class='debitAccInput' value='"+debitAcc+"'>"+debitAccName+"" +
                        "</td>" +
                        "<td style='padding: 8px 5px; text-align:right;' class='amountColumn' >" +amount+ "</td>" +
                        "<td style='padding: 8px 5px; text-align:left;' class='narration'>"+narration+"</td>" +
                        "<td style='padding: 8px 5px; text-align: center;'><a href='javascript:;' class='removeButton'><i class=' glyphicon glyphicon-trash' style='color:red; font-size:14px'><i></a></td>" +
                    // "<td><button class='removeButton'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></button></td>" +
                    "</tr>";
            }else{
                var markup =
                    "<tr class='valueRow'>" +
                        "<td style='padding: 8px 5px; text-align:left;' class='debitAcc'>" +
    //                            "<input type='hidden' class='no' value='"+no+"'>" +
                        "<input type='hidden' class='debitAccInput' value='"+debitAcc+"'>"+debitAccName+"" +
                        "</td>" +
                        "<td style='padding: 8px 5px; text-align:left;' class='creditAcc'>"+
                        "<input type='hidden' class='creditAccInput' value='"+creditAcc+"'>"+creditAccName+"" +
                        "</td>" +
                        "<td style='padding: 8px 5px; text-align:right;' class='amountColumn' >" +amount+ "</td>" +
                        "<td style='padding: 8px 5px; text-align:left;' class='narration'>"+narration+"</td>" +
                        "<td style='padding: 8px 5px; text-align: center;'><a href='javascript:;' class='removeButton'><i class=' glyphicon glyphicon-trash' style='color:red; font-size:14px'><i></a></td>" +
                    // "<td><button class='removeButton'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></button></td>" +
                    "</tr>";
            }

            $("#headerRow").after(markup);

            // $('#debitAcc').val("");
            // $('#creditAcc').val("");
            $('#amount').val("");
            $('#narration').val("");

            $('#projectId').prop("disabled", true);
            $('#projectTypeId').prop("disabled", true);
            $("#projectId").trigger('change');

        });

        $(document).on('click', '.removeButton', function () {
            var tdAmount = parseFloat($(this).closest('tr').find('.amountColumn').text());
//            document.getElementById("demo15").innerHTML =tdAmount ;
            $("#totalAmountColumn").text((parseFloat($("#totalAmountColumn").html())-tdAmount).toFixed(2));
            $(this).closest('tr').remove();
            return false;
        });

//===========================================END of insert value into the table (headerRow)===========================================


        var projectId="<?php echo $voucherInfo->projectId; ?>";
        var branchId="<?php echo $voucherInfo->branchId; ?>";
        // alert(voucherTypeId);
            // alert(projectId+" "+branchId);

        $("#projectId").change(function () {
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './getProjectTypeNLedgersInfo',
                data: {projectId: projectId, branchId:branchId, _token: csrf},
                dataType: 'json',
                success: function (data) {
                    // alert(JSON.stringify(data));
                    var projectTypeList=data['projectTypeList'];

                    $("#projectTypeId").empty();
                    $("#projectTypeId").prepend('<option selected="selected" value="">Select Project Type</option>');

                    $.each(projectTypeList, function( value ,index){
                        $('#projectTypeId').append("<option value='"+index+"'>"+value+"</option>");
                    });
                    var projectTypeIdValue = "{{$voucherInfo->projectTypeId}}";
                    $('#projectTypeId').val(projectTypeIdValue);

                    $("#creditAcc").empty();
                    $("#creditAcc").prepend('<option selected="selected" value="">Select  Account</option>');
                    $("#debitAcc").empty();
                    $("#debitAcc").prepend('<option selected="selected" value="">Select  Account</option>');

                    if(voucherTypeId==1){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        var ledgersOfAssetNLiabilityNCapitalFundNExpense=data['ledgersOfAssetNLiabilityNCapitalFundNExpense'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#creditAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                        $.each(ledgersOfAssetNLiabilityNCapitalFundNExpense, function(key, obj){
                            $('#debitAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else if(voucherTypeId==2){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        var ledgersOfAssetNLiabilityNCapitalFundNIncome=data['ledgersOfAssetNLiabilityNCapitalFundNIncome'];
                        $.each(ledgersOfAssetNLiabilityNCapitalFundNIncome, function(key, obj){
                            $('#creditAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#debitAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else if(voucherTypeId==3){
                        // var ledgersOfAllAccountType=data['ledgersOfAllAccountType'];
                        var ledgersOfNonCashNIncome=data['ledgersOfNonCashNIncome'];
                        var ledgersOfNonCashNExpense=data['ledgersOfNonCashNExpense'];
                        $.each(ledgersOfNonCashNIncome, function(key, obj){
                            // $('#creditAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                            $('#debitAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                        $.each(ledgersOfNonCashNExpense, function(key, obj){
                            $('#creditAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                            // $('#debitAcc'+idVal).append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }else if(voucherTypeId==4){
                        var ledgersOfCashAndBank=data['ledgersOfCashAndBank'];
                        $.each(ledgersOfCashAndBank, function(key, obj){
                            $('#creditAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                            $('#debitAcc').append("<option value='"+obj.id+"'>"+obj.code+" - "+obj.name+"</option>");
                        });
                    }

                },
                error: function(_response){
                    alert("Error");
                }
            });
        });
        $("#projectId").trigger('change');

// ====================================================Send & Save into DB->Table====================================================
//        var p=0;
    // $("#submit").click(function(){
        var removed_images = new Array();
        var total_file = 0;
        var max_fields = 10;
        var i = 1;

        var tweetParentsArray = [];

        $('#addMorePV').on('click',function(){
            //console.log('ok 1');    
            if(i < max_fields){
                $('#addMorePVId').append('<div class="col-sm-12 extraImgInput"  style="padding-left:0px; margin-top:10px;"><div class="col-sm-4" style="padding-left:0px;"><input type="file" name="imagePV[]"   class="imagePVChange imageUpdatePV"></div><div class="col-sm-3"><button class="remove_field" style="float:right; color:red">X</button></br></div></div>');
            } else{
                alert('Maximum '+max_fields+' images can be uploaded.');
            }

            $(".imagePVChange").on('change',function(){
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
            
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only Images are allowed");
                    $('.imageUpdatePV').val('');
                }else{
                    total_file=document.getElementsByClassName("imageUpdatePV").length;
                    console.log('total_file : ', total_file);
                    console.log('total_file : ', document.getElementsByClassName("imageUpdatePV"));
                    for(var i=0; i<total_file; i++){
                        $('.fetchPVData').append('<div class="col-sm-3" id ="imagePvRemove"  style="margin-top:10px;"><img src="'+URL.createObjectURL(event.target.files[i])+'" height="70" width="70"><button class="removePvImage" data-id="'+(total_file-1)+'" style="float:right; color:red; font-size:8px;">X</button></div>');
                        $('.extraImgInput').hide();
                    }
                }
            });
        });

        $('#addMorePVId').on("click",".remove_field", function(e){
            console.log('ok 2');
            e.preventDefault();
            $(this).parent('div').parent('div').remove();
              i--;
        });

        //remove image div
       
        $('.fetchPVData').on("click",".removeUploadedPVImage",function(){
            var images =   $(this).parent('div').find('img').attr('src'); 
            var a =  images.split("/");
            removed_images.push(a[a.length-1]);
            console.log(removed_images);
            $(this).parent('div').remove();
        });

        $('#addMorePVId').on("click",".removePvImage",function(){
            tweetParentsArray.push($(this).attr("data-id"));
            $(this).parent('div').remove();
        });
       $('form').submit(function( event ) {
        event.preventDefault();
        $("#updateSubmit").prop("disabled", true);
            // alert("submit bottom is pressed" + $(this));

            //Get all the vlaues
            var prepBy = "<?php echo $emp_id_fk; ?>";
            var branchId = "<?php echo $branchId; ?>";
            var companyId = "<?php echo $branch->companyId; ?>";

            var voucherId = "<?php echo $voucherInfo->id; ?>";
            var voucherTypeId = "<?php echo $voucherInfo->voucherTypeId; ?>";
            var projectId = $("#projectId option:selected").val();
            var projectTypeId = $("#projectTypeId option:selected").val();
            var voucherDate = $("#voucherDate").val();
            var voucherCode = $("#voucherCode").val();
            var createdDate = '<?php echo $voucherInfo->createdDate; ?>';

//            var amountColumn = $(".amountColumn").html(); alert(amountColumn);
            var globalNarration = $("#globalNarration").val();

            var csrf = "<?php echo csrf_token(); ?>";

            //Get Table Data for Voucher Details Table
            var tableDebitAcc = new Array();
            var tableCreditAcc = new Array();
            var tableAmount = new Array();
            var tableNarration = new Array();
            
            // alert("submit bottom is pressed" + $(this));

            $("#addTable tr.valueRow").each(function(){
                tableDebitAcc.push($(this).find('.debitAccInput').val());
                tableCreditAcc.push($(this).find('.creditAccInput').val());
                tableAmount.push($(this).find('.amountColumn').html());
                tableNarration.push($(this).find('.narration').html());
            });
            // alert(rowCount);

            // alert(branchId);
            // alert(companyId);
            // alert(voucherId);

            // alert(projectId);
            // alert(projectTypeId);
            // alert(voucherDate);
            // alert(voucherCode);
            // alert(globalNarration);
//
            // alert(tableDebitAcc);
            // alert(tableCreditAcc);
            // alert(tableAmount);
            // alert(tableNarration);

            var amountColumn = $(".amountColumn").html();


            formData = new FormData();
            var totalFiles = document.getElementsByClassName("imageUpdatePV").length; 
            
            for(var index = 0; index < totalFiles; index++){
                var check = 0;
                for(var i = 0; i < tweetParentsArray.length; i++) {
                    if (tweetParentsArray[i] == index) {
                        ++check;
                    }
                }
                if (check == 0) {
                    formData.append('image[]',document.getElementsByClassName("imageUpdatePV")[index].files[0]);
                }
              // formData.append('image[]',document.getElementsByClassName("imageUpdatePV")[index].files[0]); 
            };
           

            
            formData.append('prepBy', prepBy);
            formData.append('prepBy', prepBy);
            formData.append('voucherTypeId', voucherTypeId);
            formData.append('amountColumn', amountColumn);
            formData.append('voucherId', voucherId);
            formData.append('branchId', branchId);
            formData.append('companyId', companyId);
            formData.append('projectId', projectId);
            formData.append('voucherDate', voucherDate);
            formData.append('voucherCode', voucherCode);
            formData.append('projectId', projectId);
            formData.append('projectTypeId', projectTypeId);
            formData.append('tableAmount', JSON.stringify(tableAmount));
            formData.append('tableDebitAcc', JSON.stringify(tableDebitAcc));
            formData.append('tableCreditAcc', JSON.stringify(tableCreditAcc));
            formData.append('tableNarration', JSON.stringify(tableNarration));
            formData.append('previousImages', JSON.stringify(removed_images));
            formData.append('globalNarration', globalNarration);
            formData.append('_token', csrf);
            $.ajax({
                processData: false,
                contentType:false,
                type: 'post',
                url: './updateVoucherItem',
//                data: $('form').serialize(),
                data:formData,
                // data: { prepBy:prepBy, createdDate:createdDate, voucherTypeId:voucherTypeId, amountColumn:amountColumn,  voucherId:voucherId,  branchId: branchId, companyId: companyId, projectId: projectId, projectTypeId: projectTypeId, voucherDate: voucherDate, voucherCode: voucherCode, tableDebitAcc: tableDebitAcc, tableCreditAcc: tableCreditAcc, tableAmount: tableAmount, tableNarration: tableNarration, globalNarration:globalNarration, _token: csrf },
                dataType: 'json',

                success: function(_response){
                    // alert(JSON.stringify(_response));
                    if (_response.errors) {
                        $("#updateSubmit").prop("disabled", false);
                        if (_response.errors['projectId']) {
                            $('#projectIde').empty();
                            $('#projectIde').append('<span style="color:red;">'+_response.errors.projectId+'</span>');
                            return false;
                        }
                        if (_response.errors['projectTypeId']) {
                            $('#projectTypeIde').empty();
                            $('#projectTypeIde').append('<span style="color:red;">'+_response.errors.projectTypeId+'</span>');
                            return false;
                        }
                        if (_response.errors['voucherCode']) {
                            $('#voucherCodee').empty();
                            $('#voucherCodee').append('<span style="color:red;">'+_response.errors.voucherCode+'</span>');
                            return false;
                        }
                        if (_response.errors['amountColumn']) {
                            $('#tablee').empty();
                            $('#tablee').append('<span style="color:red;">'+_response.errors.amountColumn+'</span>');
                            return false;
                        }
                        if (_response.errors['globalNarration']) {
                            $('#globalNarratione').empty();
                            $('#globalNarratione').append('<span style="color:red;">'+_response.errors.globalNarration+'</span>');
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
                    // alert('_response.errors');
                    alert('Could Not Update!!!');
                }

            });
        });
// ==================================================End JavaScript for Voucher==================================================


    });     //END of document.ready function
</script>


@endsection
