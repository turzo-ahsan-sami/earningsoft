@extends('layouts/pos_layout')
@section('title', '| Add Collection')
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
                <a href="{{url('pos/listCollection')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Collection List</a>
            </div>
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Collection</div>
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
                                    {!! Form::label('Customer', 'Customer:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="customer" name="customerId">
                                            <option value="0">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{$customer->id}}" >{{ $customer->name }}</option>      
                                            @endforeach
                                        </select>
                                        <p id='customerMsg' style="max-height:3px;"></p>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Sales Bill No', 'Sales Bill No:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="salesBillNo" name="salesBillNo">
                                            <option value="0">Select Sales Bill No</option>
                                        </select>
                                        <p id='salesBillNoMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Collection Date', 'Collection Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-8" style="padding-right: 0px;">
                                        {!! Form::text('collectionDate', '', ['class' => 'form-control', 'id' => 'collectionDate', 'type' => 'text','placeholder' => 'Enter date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                        <p id='collectionDateMsg' style="max-height:3px;"></p>
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
                                                <option value="{{$project->id}}" >{{ $project->name }}</option>      
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
                                                <option value="{{$payment->id}}">{{$payment->name}}</option>
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
                                        </select>
                                        <p id='cashBankLedgerMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Total Sales Amount', 'Total Sales Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalsalesAmount', 0, ['class' => 'form-control', 'id' => 'totalSalesAmount', 'type' => 'text', 'readonly' => 'true','autocomplite'=>'off']) !!}
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
                                        {!! Form::text('paidAmount', '', ['class' => 'form-control paidAmount', 'id' => 'paidAmount', 'type' => 'text']) !!}
                                        <p id='paidAmountMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div id="collectionReport"></div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4 text-center">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                                {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span id="success" style="color:green; font-size:20px;"></span>
                        </div>
                    </div>
                    {!! Form::close() !!}

                </div>
                @endif
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>
{{-- <script>
   $(document).ready(function() {
      $('.paidAmount').on('keyup',function() {
        getDueAmount();
      })
   })

   function getDueAmount() {
        var dueAmount= parseInt($("#dueAmount").val());
        var paidAmount= parseInt($("#paidAmount").val());
        
        if (Number.isNaN(Number(dueAmount))) dueAmount = dueAmount;
        var afterPaidDueAmount = dueAmount - paidAmount;
        $('#dueAmount').val(afterPaidDueAmount);
   }
</script> --}}
<script type="text/javascript">
    var projectTypes = [];
    projectTypes = <?php echo json_encode($projectTypes) ?>;

    checkTransaction = <?php echo json_encode($checkTransaction) ?>;
  
$("#collectionDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "1998:c",
        minDate: checkTransaction.startDate,
        maxDate: checkTransaction.endDate,
        dateFormat: 'dd-mm-yy',
});

$('#customer').change(function(){

    var customerId = $(this).val();
    $("#totalSalesAmount").val(0);
    $("#dueAmount").val(0);

    if(customerId != 0)
    {
        $.ajax({
            url:'./getSalesBillNo',
            type: 'GET',
            data: {customerId:customerId},
            dataType: 'json',
            success: function(data) {
                
                $('#salesBillNo').html('');
                $('#salesBillNo').append('<option value="0">Select Sales Bill No</option>');
                $("#collectionReport").html("");

                for(i=0; i<data.length; i++)
                {
                    $('#salesBillNo').append('<option value='+data[i].id+'>'+data[i].saleBillNo+'</option>');
                }
            }
        });
    }
    else
    {
        $('#salesBillNo').html('');
        $('#salesBillNo').append('<option value="0">Select Sales Bill No</option>');
        $("#collectionReport").html("");
    }
});

$('#salesBillNo').change(function(){

    var salesId = $(this).val();
    $("#totalSalesAmount").val(0);
    $("#dueAmount").val(0);

    if(salesId != 0)
    {
        $.ajax({
            url:'./getSalesInfo',
            type: 'GET',
            data: {salesId:salesId},
            dataType: 'json',
            success: function(data) {

                totalCollectionAmount = 0;
                for(i=0; i<data.collectionInfo.length; i++)
                {
                    totalCollectionAmount = totalCollectionAmount + data.collectionInfo[i].collectionAmount;
                }

                $("#totalSalesAmount").val(data.grossTotal);
                $("#dueAmount").val(data.grossTotal - (data.payAmount + totalCollectionAmount));

                if(data.collectionInfo.length != 0)
                {   
                    $("#collectionReport").html("");
                    
                    serial = 1;
                    totalAmount = 0;

                    content = '<table class="table table-striped table-bordered" style="color:black;"><tr style="background: #696969!important; color: white;"><th>SL#</th><th>Collection Date</th><th>Collection Amount</th></tr>';
                    for(i=0; i<data.collectionInfo.length; i++)
                    {   
                        content += '<tr><td style="text-align: center; padding-left: 5px;">'+serial+'</td><td>'+data.collectionInfo[i].collectionDate+'</td><td style="text-align: right; padding-left: 5px;">'+data.collectionInfo[i].collectionAmount.toFixed(2)+'</td></tr>';
                        serial = serial + 1;
                        totalAmount = totalAmount + data.collectionInfo[i].collectionAmount;
                    }
                    
                    content += '<tr><td colspan="2"><b>Total</b></td><td style="text-align: right; padding-left: 5px;"><b>'+totalAmount.toFixed(2)+'</b></td></tr></table>';
                    $("#collectionReport").append(content);

                }
                else
                {   
                    $("#collectionReport").html("");
                    content = '<table class="table table-striped table-bordered" style="color:black;"><tr style="background: #696969!important; color: white;"><th>SL#</th><th>Collection Date</th><th>Collection Amount</th></tr>';
                    content += '<tr><td colspan="3"><b>There is no collection yet</b></td></tr></table>';
                    $("#collectionReport").append(content);

                }
            }
        });
    }
    else 
    {
        $("#collectionReport").html("");
    }

});

$('#project').change(function(){

    var projectId = $(this).val();
    $('#projectType').val();

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
            url:'./getLedgerForCollection',
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
            url: './insertCollection',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){

                $('#customerMsg').empty();
                $('#customerMsg').show();
                $('#salesBillNoMsg').empty();
                $('#salesBillNoMsg').show();
                $('#collectionDateMsg').empty();
                $('#collectionDateMsg').show();
                $('#paidAmountMsg').empty();
                $('#paidAmountMsg').show();
                $('#projectMsg').empty();
                $('#projectMsg').show();
                $('#paymentTypeMsg').empty();
                $('#paymentTypeMsg').show();
                $('#cashBankLedgerMsg').empty();
                $('#cashBankLedgerMsg').show();
                        
                if (_response.errors) {
                    if (_response.errors['customerId']) {
                        $('#customerMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.customerId+'</span>');
                    }
                    if (_response.errors['salesBillNo']) {
                        $('#salesBillNoMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.salesBillNo+'</span>');
                    }
                    if (_response.errors['collectionDate']) {
                        $('#collectionDateMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.collectionDate+'</span>');
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
                        window.location.href = '{{url('pos/listCollection/')}}';
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
