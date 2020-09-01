@extends('layouts/pos_layout')
@section('title', '| Voucher Configuration')
@php
    $company = DB::table('gnr_company')->where('id',Auth::user()->company_id_fk)->first();
@endphp
@section('content')
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <div class="panel-options">
                    <a href="{!! url('/pos/voucherSettingList'); !!}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Voucher Setting List</a>
                </div>
               <h3 class="text-center" style="color: white">Voucher Configuration</h3> 
            </div>
            <div class="panel panel-default panel-border">
               
                <div class="panel-body">

                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                    <div class="row">
                        <div class="col-md-12">

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('customer', 'Customer:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('customer', $accountHeads, $setting->customer ?? null, ['class' => 'form-control', 'id' => 'customer', 'type' => 'text', 'placeholder' => 'Select customer head','autocomplite'=>'off']) !!}
                                        <p style="max-height:3px;">* Account head only</p>
                                        <p id='customerMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('supplier', 'Supplier:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('supplier', $accountHeads, null, ['class' => 'form-control', 'id' => 'supplier', 'type' => 'text', 'placeholder' => 'Select supplier head','autocomplete'=>'off']) !!}
                                        <p style="max-height:3px;">* Account head only</p>
                                        <p id='supplierMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('sales', 'Sales:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('sales', $transactionHeads, null, ['class' => 'form-control', 'id' => 'sales', 'type' => 'text', 'placeholder' => 'Select sales head','autocomplete'=>'off']) !!}
                                        <p style="max-height:3px;">* Transaction head only</p>
                                        <p id='salesMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('sales_return', 'Sales return:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('salesReturn', $transactionHeads, null, ['class' => 'form-control', 'id' => 'sales_return', 'type' => 'text', 'placeholder' => 'Select sales return head','autocomplete'=>'off']) !!}
                                        <p style="max-height:3px;">* Transaction head only</p>
                                        <p id='salesReturnMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('purchase', 'Purchase:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('purchase', $transactionHeads, null, ['class' => 'form-control', 'id' => 'purchase', 'type' => 'text', 'placeholder' => 'Select purchase head','autocomplete'=>'off']) !!}
                                        <p style="max-height:3px;">* Transaction head only</p>
                                        <p id='purchaseMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('purchase_return', 'Purchase return:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('purchaseReturn', $transactionHeads, null, ['class' => 'form-control', 'id' => 'purchase_return', 'type' => 'text', 'placeholder' => 'Select purchase return head','autocomplete'=>'off']) !!}
                                        <p style="max-height:3px;">* Transaction head only</p>
                                        <p id='purchaseReturnMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Vat', 'Vat:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::select('vat', $transactionHeads, null, ['class' => 'form-control', 'id' => 'vat', 'type' => 'text', 'placeholder' => 'Select vat head','autocomplete'=>'off']) !!}
                                        <p style="max-height:3px;">* Transaction head only</p>
                                        <p id='vatMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                @if($company->business_type != 'manufacture'){
                                    @if($stockExist[0] == 1)
                                    <div class="form-group">
                                        {!! Form::label('Inventory', 'Inventory:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('inventory', $transactionHeads, null, ['class' => 'form-control', 'id' => 'inventory', 'type' => 'text', 'placeholder' => 'Select inventory head','autocomplete'=>'off']) !!}
                                            <p style="max-height:3px;">* Transaction head only</p>
                                            <p id='inventoryMsg' style="max-height:3px;"></p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('Cost of good sold', 'Cost of good sold:', ['class' => 'col-sm-4 control-label']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('cost_of_good_sold', $transactionHeads, null, ['class' => 'form-control', 'id' => 'cost_of_good_sold', 'type' => 'text', 'placeholder' => 'Select cost of good sold head','autocomplete'=>'off']) !!}
                                            <p style="max-height:3px;">* Transaction head only</p>
                                            <p id='cost_of_good_sold_msg' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4 text-center">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-12">
                                {!! Form::submit('Save', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{!! url('/pos/voucherSettingList'); !!}" class="btn btn-danger closeBtn">Close</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span id="success" style="color:green; font-size:20px;"></span>
                        </div>
                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>


<script type="text/javascript">

/*Data Insert Start*/
$(document).ready(function(){
    
    $('form').submit(function( event ) {
        event.preventDefault();
    
        $.ajax({
            type: 'post',
            url: './saveVoucherSetting',
            data: $('form').serialize(),
            dataType: 'json',
            success: function( _response ){

                // console.log(_response);
                $('#customerMsg').empty();
                $('#customerMsg').show();
                $('#supplierMsg').empty();
                $('#supplierMsg').show();
                $('#salesMsg').empty();
                $('#salesMsg').show();
                $('#salesReturnMsg').empty();
                $('#salesReturnMsg').show();
                $('#purchaseMsg').empty();
                $('#purchaseMsg').show();
                $('#purchaseReturnMsg').empty();
                $('#purchaseReturnMsg').show();
                $('#vatMsg').empty();
                $('#vatMsg').show();
                
                if (_response.errors) {
                    if (_response.errors['customer']) {
                        $('#customerMsg').empty();
                        $('#customerMsg').show();
                        $('#customerMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.customer+'</span>');
                    }
                    if (_response.errors['supplier']) {
                        $('#supplierMsg').empty();
                        $('#supplierMsg').show();
                        $('#supplierMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.supplier+'</span>');
                    }
                    
                    if (_response.errors['sales']) {
                        $('#salesMsg').empty();
                        $('#salesMsg').show();
                        $('#salesMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.sales+'</span>');
                    }

                    if (_response.errors['salesReturn']) {
                        $('#salesReturnMsg').empty();
                        $('#salesReturnMsg').show();
                        $('#salesReturnMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.salesReturn+'</span>');
                    }

                    if (_response.errors['purchase']) {
                        $('#purchaseMsg').empty();
                        $('#purchaseMsg').show();
                        $('#purchaseMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.purchase+'</span>');
                    }

                    if (_response.errors['purchaseReturn']) {
                        $('#purchaseReturnMsg').empty();
                        $('#purchaseReturnMsg').show();
                        $('#purchaseReturnMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.purchaseReturn+'</span>');
                    }

                    if (_response.errors['vat']) {
                        $('#vatMsg').empty();
                        $('#vatMsg').show();
                        $('#vatMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.vat+'</span>');
                    }

                    if (_response.errors['inventory']) {
                        $('#inventoryMsg').empty();
                        $('#inventoryMsg').show();
                        $('#inventoryMsg').append('<span class="errormsg" style="color:red;">'+_response.errors.inventory+'</span>');
                    }

                    if (_response.errors['cost_of_good_sold']) {
                        $('#cost_of_good_sold_msg').empty();
                        $('#cost_of_good_sold_msg').show();
                        $('#cost_of_good_sold_msg').append('<span class="errormsg" style="color:red;">'+_response.errors.cost_of_good_sold+'</span>');
                    }

                }
                else {
                     $('#success').text(_response.responseText);
                     window.location.href = '{{url('pos/voucherSettingList/')}}';
                }
            },
            error: function( _response ){
                
            }

        });
    });
    /*Data Insert End */


});
/*Error Alert remove Ajax End*/
/* Category Change Start*/

</script>
@endsection
