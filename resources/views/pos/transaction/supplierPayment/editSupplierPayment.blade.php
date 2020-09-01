@extends('layouts/pos_layout')
@section('title', '| Add Payment')
@section('content')
    <?php
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;

    ?>
    <style type="text/css">
    .select2-results__option[aria-selected=true] {
        display: none;
    }
</style>
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('pos/viewSupplierPayment')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Payment List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Edit Payment</div>
                </div>
                @if(!$setting)
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <h3 align="center" style="font-family: Antiqua;letter-spacing: 2px">Set voucher configuration first</h3>
                    </div>
                </div>
                @elseif($checkTransaction['status'] == false)
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <h3 align="center" style="font-family: Antiqua;letter-spacing: 2px">You have to year end first before you proceed</h3>
                    </div>
                </div>
                @else
                <div class="panel-body">

                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                    <div class="row">
                        <div class="col-md-12">

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('Supplier', 'Supplier:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="supplier" name="supplierId">
                                            <option value="0">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{$supplier->id}}" {{($posSupPayment->supplierId == $supplier->id) ? 'selected' : ''}}>{{ $supplier->name }}</option>      
                                            @endforeach
                                        </select>
                                        <p id='supplierMsg' style="max-height:3px;"></p>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Purchase Bill No', 'Purchase Bill No:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="purchaseBillNo" name="purchaseBillNo">
                                         {{--  @foreach($supplierInfo as $supplier)
                                                <option value="{{$supplier->purchaseId}}" {{($posSupPayment->purchaseId == $supplier->purchaseId) ? 'selected' : ''}}>{{ $supplier->purchaseBillNo }}</option>      
                                            @endforeach  --}}
                                             <option value="{{$posSupPayment->purchaseId}}">{{ $posSupPayment->purchaseBillNo }}</option>      
                                           
                                        </select>
                                        <p id='purchaseBillNoMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Payment Date', 'Payment Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-8" style="padding-right: 0px;">
                                        {!! Form::text('paymentDate', $posSupPayment->paymentDate, ['class' => 'form-control', 'id' => 'paymentDate', 'type' => 'text','placeholder' => 'Enter date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                        <p id='paymentDateMsg' style="max-height:3px;"></p>
                                    </div>
                                    <div class="col-sm-1" style="padding-right: 0px;">
                                        <i class="fa fa-calendar" aria-hidden="true" style="font-size: 2em;"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Project', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="project" name="projectId">
                                            <option value="0">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}" {{($posSupPayment->projectId == $project->id) ? 'selected' : ''}}>{{ $project->name }}</option>      
                                            @endforeach
                                        </select>
                                        <p id='projectMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <input type="hidden" id="projectType" name="projectTypeId" value=""/>
                                <input type="hidden" id="branch" name="branchId" value="{{ $branch }}" />

                                <div class="form-group">
                                    {!! Form::label('paymentTypeId', 'Payment Type:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select name="paymentType" id="paymentTypeId" class="form-control">
                                            <option value="0">Select Payment</option>
                                            @foreach($payments as $payment)
                                                <option value="{{$payment->id}}" {{($posSupPayment->paymentType == $payment->id) ? 'selected' : ''}}>{{$payment->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id="paymentTypeMsg" style="max-height:3px;"></p>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('Ledger', 'Ledger:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="cashBankLedger" name="cashBankLedger">
                                            <option value="0">Select Ledger</option>
                                             @foreach($ledgerData as $ledger)
                                                <option value="{{$ledger->id}}" {{($posSupPayment->cashBankLedgerId == $ledger->id) ? 'selected' : ''}}>{{$ledger->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id='cashBankLedgerMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Total Purchase Amount', 'Total Purchase Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalpurchaseAmount', $posSupPayment['purchase']['grossTotal'], ['class' => 'form-control', 'id' => 'totalpurchaseAmount', 'type' => 'text', 'readonly' => 'true','autocomplite'=>'off']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Due Amount', 'Due Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('dueAmount', 0, ['class' => 'form-control', 'id' => 'dueAmount', 'type' => 'text', 'readonly' => 'true','autocomplite'=>'off']) !!}
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('Pay Amount', 'Pay Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('paidAmount', $posSupPayment->paidAmount, ['class' => 'form-control', 'id' => 'paidAmount', 'type' => 'text']) !!}
                                        <p id='paidAmountMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div id="purchaseReport"></div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4 text-center">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                                {!! Form::submit('Update', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span id="success" style="color:green; font-size:20px;"></span>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{$posSupPayment->id}}">
                    {!! Form::close() !!}

                </div>
                @endif
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>


<script type="text/javascript">

    var projectTypes = [];
    projectTypes = <?php echo json_encode($projectTypes) ?>;

    checkTransaction = <?php echo json_encode($checkTransaction) ?>;
    
$("#paymentDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "1998:c",
        minDate: checkTransaction.startDate,
        maxDate: checkTransaction.endDate,
        dateFormat: 'dd-mm-yy',
});

$('#supplier').change(function(){

    var supplierId = $(this).val();
    $("#totalpurchaseAmount").val(0);
    $("#dueAmount").val(0);

    if(supplierId != 0)
    {
        $.ajax({
            url:'../getPurchaseBillNo',
            type: 'GET',
            data: {supplierId:supplierId},
            dataType: 'json',
            success: function(data) {
                
                $('#purchaseBillNo').html('');
                $('#purchaseBillNo').append('<option value="0">Select Purchase Bill No</option>');
                $("#purchaseReport").html("");

                for(i=0; i<data.length; i++)
                {
                    $('#purchaseBillNo').append('<option value='+data[i].id+'>'+data[i].billNo+'</option>');
                }
            }
        });
    }
    else
    {
        $('#purchaseBillNo').html('');
        $('#purchaseBillNo').append('<option value="0">Select Sales Bill No</option>');
        $("#purchaseReport").html("");

    }
    
});

$('#purchaseBillNo').change(function(){

    var purchaseId = $(this).val();
    $("#totalpurchaseAmount").val(0);
    $("#dueAmount").val(0);
    

    if(purchaseId != 0)
    {
        $.ajax({
            url:'../getPurchaseInfo',
            type: 'GET',
            data: {purchaseId:purchaseId},
            dataType: 'json',
            success: function(data) {

                totalPaymentAmount = 0;
                for(i=0; i<data.paymentInfo.length; i++)
                {
                    totalPaymentAmount = totalPaymentAmount + data.paymentInfo[i].paidAmount;
                }

                $("#totalpurchaseAmount").val(data.grossTotal);
                $("#dueAmount").val(data.grossTotal - (data.payAmount + totalPaymentAmount));

                if(data.paymentInfo.length != 0)
                {   
                    $("#purchaseReport").html("");
                    
                    serial = 1;
                    totalAmount = 0;

                    content = '<table class="table table-striped table-bordered" style="color:black;"><tr style="background: #696969!important; color: white;"><th>SL#</th><th>Payment Date</th><th>Paid Amount</th></tr>';
                    for(i=0; i<data.paymentInfo.length; i++)
                    {   
                        content += '<tr><td style="text-align: center; padding-left: 5px;">'+serial+'</td><td>'+data.paymentInfo[i].paymentDate+'</td><td style="text-align: right; padding-left: 5px;">'+data.paymentInfo[i].paidAmount.toFixed(2)+'</td></tr>';
                        serial = serial + 1;
                        totalAmount = totalAmount + data.paymentInfo[i].paidAmount;
                    }
                    
                    content += '<tr><td colspan="2"><b>Total</b></td><td style="text-align: right; padding-left: 5px;"><b>'+totalAmount.toFixed(2)+'</b></td></tr></table>';
                    $("#purchaseReport").append(content);

                }
                else
                {   

                    $("#purchaseReport").html("");
                    content = '<table class="table table-striped table-bordered" style="color:black;"><tr style="background: #696969!important; color: white;"><th>SL#</th><th>Payment Date</th><th>Paid Amount</th></tr>';
                    content += '<tr><td colspan="3"><b>There is no payment yet</b></td></tr></table>';
                    $("#purchaseReport").append(content);

                }
            }
        });
    }
    else 
    {
        $("#purchaseReport").html("");
    }

});

$('#project').change(function(){

    var projectId = $(this).val();

    $('#projectType').val('');
  
    if(projectId != 0)
    {
        for(i=0; i<projectTypes.length; i++)
        {   
            if(projectId == projectTypes[i].projectId) $('#projectType').val(projectTypes[i].id);
        }
    }
});

$('#paymentTypeId').change(function(){

    var paymentTypeId = $(this).val();

    $('#cashBankLedger').html('');
    $('#cashBankLedger').append('<option value="0">Select Ledger</option>');

    if(paymentTypeId != 0)
    {
        $.ajax({
            url:'../getLedgerForSupplierPayment',
            type: 'GET',
            data: {paymentTypeId:paymentTypeId},
            dataType: 'json',
            success: function(data) {
                
                for(i=0; i<data.length; i++)
                {   
                    $('#cashBankLedger').append('<option value='+data[i].id+'>'+data[i].name+'</option>');
                }
            }
            
        });
    }

});

$(document).ready(function(){
   
    $('form').submit(function( event ) {
        
        event.preventDefault();
        var formData = new FormData($(this)[0]);

        $.ajax({
            type: 'post',
            url: '../updateSupplierPayment',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){

                $('#supplierMsg').empty();
                $('#supplierMsg').show();
                $('#purchaseBillNoMsg').empty();
                $('#purchaseBillNoMsg').show();
                $('#paymentDateMsg').empty();
                $('#paymentDateMsg').show();
                $('#paidAmountMsg').empty();
                $('#paidAmountMsg').show();
                $('#projectMsg').empty();
                $('#projectMsg').show();
                $('#paymentTypeMsg').empty();
                $('#paymentTypeMsg').show();
                $('#cashBankLedgerMsg').empty();
                $('#cashBankLedgerMsg').show();
                        
                if (_response.errors) {
                    if (_response.errors['supplierId']) {
                        $('#supplierMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.supplierId+'</span>');
                    }
                    if (_response.errors['purchaseBillNo']) {
                        $('#purchaseBillNoMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.purchaseBillNo+'</span>');
                    }
                    if (_response.errors['paymentDate']) {
                        $('#paymentDateMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.paymentDate+'</span>');
                    }
                    if (_response.errors['paidAmount']) {
                        $('#paidAmountMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.paidAmount+'</span>');
                    }
                    if (_response.errors['projectId']) {
                        $('#projectMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.projectId+'</span>');
                    }
                    if (_response.errors['paymentType']) {
                        $('#paymentTypeMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.paymentType+'</span>');
                    }
                    if (_response.errors['cashBankLedger']) {
                        $('#cashBankLedgerMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.cashBankLedger+'</span>');
                    }
                }
                else {
                    if(_response.status == true)
                    {
                        $('#success').text(_response.msg);
                        window.location.href = '{{url('pos/viewSupplierPayment/')}}';
                    }
                }
            },
            error: function( _response ){
                // Handle error
                //alert(_response.errors);
                //alert("Select Image please");
            }

        });
    });
});

</script>
@endsection
