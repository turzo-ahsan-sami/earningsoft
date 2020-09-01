@extends('layouts/pos_layout')
@section('title', '| Purchase')
@section('content')

<style>
    td, th {
        padding: 14px;
    }
</style>

<div class="alert alert-info alert-dismissable" id="soldOutMessage" style="display: none;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Info!</strong> This product already Sold Out!!
</div>
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
        <div class="col-md-12 fullbody">
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('pos/purchase/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Purchase List</a>
            </div>
            @if(!$setting)
            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <h3 align="center" style="font-family: Antiqua;letter-spacing: 2px">Set Voucher Configuration First</h3>
                </div>
            </div>
            @else
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Add Purchase</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <div class="col-md-6" style="padding-right: 0px; padding-left: 0px;">
                            <div class="col-md-9">
                                
                                <div class="form-group">
                                    {!! Form::label('purchaseId', 'Bill number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('billNo',null, array('class'=>'form-control', 'id' => 'billNoId','readonly')) !!}
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('', 'Supplier:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <select name="supplierId" id="supplierTypeId" class="form-control col-sm-9">
                                                    <option value="">Select supplier</option>
                                                    @foreach($purchaseSuppliers as $purchaseSupplier)
                                                    <option value="{{$purchaseSupplier->id}}">{{$purchaseSupplier->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                 <a href="{{url('pos/addSupplier/')}}" class="btn btn-primary" target="_blank">Add</a>
                                            </div>
                                        </div>
                                        <p id='productTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('personId', 'Contact person:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('conPerson',null, array('class'=>'form-control', 'id' => 'personId', 'placeholder'=>'Contact Person')) !!}
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    {!! Form::label('remarks', 'Remarks:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('remark',null, array('class'=>'form-control', 'id' => 'remarkId','placeholder'=>'Enter remarks')) !!}
                                    </div>
                                    <p id="remarksIde" style="max-height:3px;"></p>
                                </div>
                               
                                <div class="form-group">
                                    {!! Form::label('purchaseDate', 'Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-8" style="padding-right: 0px;">
                                        {!! Form::text('purchaseDate', $value = null, ['class' => 'form-control', 'id' => 'purchaseDate', 'type' => 'text','placeholder' => 'Enter date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                        <p id='purchaseDatee' style="max-height:3px;color: red;display: none;">*Required</p>
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
                                    {!! Form::label('Product Type', 'Product Type:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="productType" name="productType">
                                            <option value="0">Select Product Type</option>
                                            <option value="product">Product</option>
                                            <option value="hour">Hour</option>
                                            <option value="raw">Raw material</option>
                                        </select>
                                        <p id='productTypeMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                            </div>
                            {{-- <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="60%" height="" style="float:right">
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-9">
                                <div class="form-group">
                                    {!! Form::label('totalQuantity', 'Total Quantity:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('qty',null, array('class'=>'form-control', 'id' => 'totalQuantity','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalQuantityIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalAmount',null, array('class'=>'form-control', 'id' => 'totalAmount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('discount', 'Discount(%):', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                {!! Form::text('discountAmount',null, array('class'=>'form-control', 'id' => 'discount','placeholder'=>0)) !!}
                                            </div>
                                            <div class="col-sm-2">
                                                :
                                            </div>
                                            <div class="col-sm-5">
                                                {!! Form::text('discountAmount',null, array('class'=>'form-control', 'id' => 'discountTotal','placeholder'=>0,)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <p id="vatIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('afterDiscount', 'T/A After Discount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalAmaountAfterDis',null, array('class'=>'form-control', 'id' => 'afterDiscount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('vat', 'Vat(%):', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                {!! Form::text('vatAmount',null, array('class'=>'form-control', 'id' => 'vatAmount','placeholder'=>0)) !!}
                                            </div>
                                            <div class="col-sm-2">
                                                :
                                            </div>
                                            <div class="col-sm-5">
                                                {!! Form::text('vatAmount',null, array('class'=>'form-control', 'id' => 'vatAmountTotal','placeholder'=>0,)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <p id="vatIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('grossTotal', 'Gross Total:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('grossTotal',null, array('class'=>'form-control', 'id' => 'grossTotal','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="grossTotalIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('payAmount', 'Pay Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('payAmount',null, array('class'=>'form-control', 'id' => 'payAmount','placeholder'=>0)) !!}
                                    </div>
                                    <p id="payAmountIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('puchaseDue', 'Due:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('dueAmount',null, array('class'=>'form-control', 'id' => 'puchaseDue','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="puchaseDuede" style="max-height:3px;"></p>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('paymentTypeId', 'Payment Type:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select name="paymentType" id="paymentTypeId" class="form-control">
                                            <option value="0">Select Payment</option>
                                           @foreach($payments as $payment)
                                                <option value="{{$payment->id}}">{{$payment->name}}</option>
                                           @endforeach
                                        </select>
                                    </div>
                                    <p id="puchaseDuede" style="max-height:3px;"></p>
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

                            </div>
                        </div>
                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                            <br>
                            <p id="salePricee" style="max-height:3px;"></p>
                            <br>
                            <table id="productInfoTable" class="table table-bordered responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align:center;">Product</th>
                                        <th style="text-align:center;">Qty</th>
                                        <th style="text-align:center;">Price</th>
                                        <th style="text-align:center;">Total</th>
                                        <th style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="chartTable">
                                    <tr>
                                        <td id="normalProduct">
                                          <select style="width: 80%; padding-top:6px; font-size: 11px;" class="productCode  select2">
                                                <option value="">Select Product</option>
                                                <!--   <option hidden name="productId" id="productId" class="productId "></option> -->
                                                @foreach($productNames as $productName)
                                                    @if($productName->type == 'product')
                                                        <option value="{{$productName->id}}" class="productCodeId">{{str_pad($productName->code,3,"0",STR_PAD_LEFT )}} - {{$productName->name}}</option>
                                                    @endif
                                                @endforeach
                                          </select>
                                          <a href="{{url('pos/addProduct/')}}" class="btn btn-primary" target="_blank">Add</a>
                                        </td>
                                        <td id="rawProduct">
                                          <select style="width: 80%; padding-top:6px; font-size: 11px;" class="productCode  select2">
                                                <option value="">Select Product</option>
                                                <!--   <option hidden name="productId" id="productId" class="productId "></option> -->
                                                @foreach($productNames as $productName)
                                                    @if($productName->type == 'raw')
                                                        <option value="{{$productName->id}}" class="productCodeId">{{str_pad($productName->code,3,"0",STR_PAD_LEFT )}} - {{$productName->name}}</option>
                                                    @endif
                                                @endforeach
                                          </select>
                                          <a href="{{url('pos/addProduct/')}}" class="btn btn-primary" target="_blank">Add</a>
                                        </td>
                                        <td id="hourProduct">@if($hourBaseProduct){{ $hourBaseProduct->code }} - {{ $hourBaseProduct->name }}@endif</td>
                                        <td hidden><span class="productId" id="productId"/></spsn></spsn></td>
                                        <td hidden><span class="productName"/></spsn></spsn></td>
                                        <td hidden><span class="productCodeIde"/></spsn></spsn></td>
                                        <td><input type="text" name="qty" class="quantityCal" style="text-align:center;" /></td>
                                        <td><input type="text" name="qty" class="productPrice" style="text-align:right;" /></td>
                                        <!-- <td><span class="productPrice" /></spsn></td> -->
                                      
                                        <td><span ><span class="totalPrice"/></span></td>
                                        <td>{!! Form::button('Add to chart', ['id' => '', 'class' => 'btn btn-primary addToChartBtn']) !!}</td>
                                    </tr> 
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                        <div class="form-group">
                        <div class="col-sm-6 text-right" style="padding: 20px 20px 0px 0px;">
                            {!! Form::button('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                            {!! Form::button('Submit & Print', ['id' => 'submitPrint', 'class' => 'btn btn-success']) !!}
                            <a href="{{url('pos/purchase/')}}" class="btn btn-danger closeBtn">Close</a>
                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                        </div>
                        </div>
                    </div> 
                    </div>  
                {!! Form::close() !!} 
            </div>
            @endif
            
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
{{-- Filtering --}}
<script type="text/javascript">
    
    var projectTypes = [];
    
    projectTypes = <?php echo json_encode($projectTypes) ?>;

    $('#chartTable').hide();

$('#paymentTypeId').change(function(){

    var paymentTypeId = $(this).val();

    $('#cashBankLedger').html('');
    $('#cashBankLedger').append('<option value="0">Select Ledger</option>');

    if(paymentTypeId != 0)
    {
        $.ajax({
            url:'./getLedgerForPurchase',
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

$('#productType').change(function(){

    var productType = $(this).val();
    hourBaseProduct = <?php echo json_encode($hourBaseProduct) ?>;

    $('#chartTable').hide();
    $('.calulationTotal').html('');
    $('.productName').html('');
    $('.productPrice').val('');
    $('.quantityCal').val('');
    $('#productId').html('');
    $('.productCodeIde').html('');
    $('.totalPrice').html('');

    $('.totalQty').html('0');  
    $('.totalAmount').html('0');

    $('#totalQuantity').val(0);
    $('#totalAmount').val(0);
    $('#afterDiscount').val(0);
    $('#puchaseDue').val(0);
    $('#grossTotal').val(0); 
    
    if(productType == 0) $('#chartTable').hide();
    else 
    {      
        if(productType == 'hour')
        {   
            if(hourBaseProduct != null)
            {   
                $('#chartTable').show();
                $('#normalProduct').hide();
                $('#hourProduct').show();
                $('#rawProduct').hide();
                
                var productName =  hourBaseProduct.name;
                var productPrice = hourBaseProduct.costPrice;
                var productId =    hourBaseProduct.id;
                var productCode =  hourBaseProduct.code;
            
                $('.productName').html(productName);
                $('.productPrice').val('');
                $('#productId').html(productId);
                $('.productCodeIde').html(productCode);
  
            }
        }
        else if(productType == 'raw')
        {
            $('#chartTable').show();
            $('#normalProduct').hide();
            $('#rawProduct').show();
            $('#hourProduct').hide();
        }
        else 
        {   
            $('#chartTable').show();
            $('#rawProduct').hide();
            $('#normalProduct').show();
            $('#hourProduct').hide();
        }
    }
});


$(document).ready(function() {
//onchange product code

$('.productCode').change(function(){
    

    if($(this).val() != ''){
        var productCode = $(this).val();

        // alert(productCode);
        
        $.ajax({
            url:'./purchaseAddProductNameOnChangeProductCode',
            type: 'GET',
            data: {productCode:productCode},
            dataType: 'json',
            success: function(data) {
                var productName =  data['name'];
                var productPrice = data['costPrice'];
                var productId =    data['id'];
                var productCode =    data['code'];
                //var product = productCode - productName;
                
               $('.productName').html(productName);
               $('.productPrice').val('');
               $('#productId').html(productId);
               $('.productCodeIde').html(productCode);
            }
        });
    }else{
       $('.totalPrice').html(0);
       $('.productPrice').val('');
       $('#productId').html(0);
       $('.quantityCal').val('');
    }
})
//quantity calculation on input

$('.quantityCal').on('input',function(){
    
   var qty = $(this).val();
   var price =  $('.productPrice').val();
   
   var total = price * qty ;
   $('.totalPrice').html(total);
   
})

$('.productPrice').on('input',function(){
   
   var price = $(this).val();
   var qty =  $('.quantityCal').val();
   
   var total = price * qty ;
   $('.totalPrice').html(total);
   
})

//onchange supplier 
$('#supplierTypeId').on('change',function(){

    if($(this).val() != ''){
        var supplierId = $(this).val();

        $.ajax({
            url:'./getBillNoOnChangeSupplierId',
            type: 'GET',
            data: {supplierId:supplierId},
            dataType: 'json',
            success: function(data) {
                console.log(data);
               $("#billNoId").val(data);
            }   
        });
    }else{
        $("#billNoId").val('');

    }
    //alert(supplierId);
    
})

//Add to chart button 
 var i=1; 
$('.addToChartBtn').on('click',function(){
    var productname =  $('.productName').html();
    var productCode = $('.productCodeIde').html();
    var productId = $('.productId').html();
    var productPrice =  $('.productPrice').val();
    
    var qty = $('.quantityCal').val();
    var price =  $('.productPrice').val();
    var total = price * qty ;

        if(productname=="") {
            alert('Please insert product');
        }else if(qty=="") {
            alert('Please insert quantity');
        }else if(productPrice == 0 || productPrice == '')
        {
            alert('Please insert price');
        }
        else{
            $('.productName').html('');
            $('.productPrice').val();
            $('.totalPrice').html('');
            $('.quantityCal').val('');
            //add more product field
             i++;  
            $('#productInfoTable').append('<tr id="row'+i+'" class="calulationTotal"><td><span class="productId">'+productname+' - '+productCode+'</span></td><td hidden><span class="productIdD">'+productId+'</span></td><td><span class="purchaseQuantity">'+qty+'</span></td><td><span class="price">'+price+'</span></td><td><span class="purchasePrice">'+total+'</span></td><td><button class="remove_field"  style="float:right; color:red">X</button</td></tr>'

            );
            
        } 
        calQuantityPrice();             
   
})

$("#productInfoTable")
  .on('input', 'input', calQuantityPrice)
  .on('click', '.remove_field', function() {
    $(this).closest("tr").remove();
    calQuantityPrice();
  });


function calQuantityPrice(){
    var totalQuantity = 0;
        var totalPrice = 0;
        $('#productInfoTable .calulationTotal .purchaseQuantity').each(function(){
            var quantity = $(this).html();
            totalQuantity += parseFloat(quantity);
        })
         $('#productInfoTable .calulationTotal .purchasePrice').each(function(){
            var price = $(this).html();
            totalPrice += parseFloat(price);
        })
        //console.log(totalPrice);
        $('.totalQty').html(totalQuantity);  
        $('.totalAmount').html(totalPrice);

        $('#totalQuantity').val(totalQuantity);
        $('#totalAmount').val(totalPrice);
        $('#afterDiscount').val(totalPrice);
        $('#puchaseDue').val(totalPrice);
        $('#grossTotal').val(totalPrice); 
}

$('#productInfoTable').append('<td colspan="1" style="text-align:center"><strong>Total Quantity</strong></td><td style="text-align:center"><strong class="totalQty">0</strong></td><td style="text-align:center; font-weight:bold;"><strong class=""/>Total Price</strong></td><td style="text-align:center; font-weight:bold;"><strong class="totalAmount"/></strong></td>');

//discount balance
$('#discount, #vatAmount, #payAmount').on('input',function(){
    discountAmount();
})

function discountAmount(){
   var discountPercentage= parseFloat($("#discount").val());
   if (Number.isNaN(Number(discountPercentage))) discountPercentage = 0;
   var totalAmountData = $('#totalAmount').val();
   
   var discountAmount = totalAmountData * (discountPercentage / 100);
   $('#discountTotal').val(discountAmount);
   var totalAfterDiscount = totalAmountData - discountAmount ;
   $('#afterDiscount').val(totalAfterDiscount);
   $('#puchaseDue').val(totalAfterDiscount);
   $('#grossTotal').val(totalAfterDiscount); 

   //vat amount calculation

   var vatAmountPercentage= parseFloat($('#vatAmount').val());
   if (Number.isNaN(Number(vatAmountPercentage))) vatAmountPercentage = 0;
   var afterDiscount = $('#afterDiscount').val();

   var vatAmount = afterDiscount * (vatAmountPercentage / 100);
    $('#vatAmountTotal').val(vatAmount);
    
   var totalAfterVat = parseFloat(afterDiscount) + parseFloat(vatAmount) ;
   $('#grossTotal').val(totalAfterVat);
   $('#puchaseDue').val(totalAfterVat);
   $('#grossTotal').val(totalAfterVat);

   //pay amount calculation

    var grossTotalAmount =  $('#grossTotal').val();
    var payAmount =  $('#payAmount').val();
    var purchaseDueAmount = grossTotalAmount - payAmount ;
    $('#puchaseDue').val(purchaseDueAmount);
}

function inputAmount(){
   var discountAmount= parseFloat($("#discountTotal").val());
   if (Number.isNaN(Number(discountAmount))) discountAmount = 0;
   var totalAmountData = $('#totalAmount').val();
   
   
   var totalAfterDiscount = parseFloat(totalAmountData) - parseFloat(discountAmount);
   $('#afterDiscount').val(totalAfterDiscount);
   $('#puchaseDue').val(totalAfterDiscount);
   $('#grossTotal').val(totalAfterDiscount); 

   //vat amount calculation

   var vatAmount= parseFloat($('#vatAmountTotal').val());
   if (Number.isNaN(Number(vatAmountTotal))) vatAmountTotal = 0;
   var afterDiscount = $('#afterDiscount').val();
    
   var totalAfterVat = parseFloat(afterDiscount) + parseFloat(vatAmount) ;
   $('#grossTotal').val(totalAfterVat);
   $('#puchaseDue').val(totalAfterVat);
   $('#grossTotal').val(totalAfterVat);

   //pay amount calculation

    var grossTotalAmount =  $('#grossTotal').val();
    var payAmount =  $('#payAmount').val();
    var purchaseDueAmount = grossTotalAmount - payAmount ;
    $('#puchaseDue').val(purchaseDueAmount);
}

$('#discountTotal, #vatAmountTotal').on('input',function(){
   inputAmount();
})

//add pur
$('#submit, #submitPrint').click(function(event) {    
var billNo               = $("#billNoId").val();
var conPerson            = $("#personId").val();
var supplierId           = $("#supplierTypeId").val();
var purchaseDate         = $("#purchaseDate").val();
var productId            =  $('#productId').text();
var productType          =  $('#productType').val();


 var productID = [];
 var quantity = [];
 var price = [];
 var total = [];
var test = $('#productInfoTable .calulationTotal');

test.each(function(){
    var inputProductId= $(this).find('.productIdD').text();
    var inputQuantity= $(this).find('.purchaseQuantity').text();
    var purchasePrice= $(this).find('.price').text();
    var totalPrice= $(this).find('.purchasePrice').text();

    productID = productID.filter((item) => item != "");
    quantity = quantity.filter((item) => item != "");
    price = price.filter((item) => item != "");
    total = total.filter((item) => item != "");

    productID.push(inputProductId);
    quantity.push(inputQuantity);
    price.push(purchasePrice);
    total.push(totalPrice);
    
})

// console.log(quantity);
//alert(productDetails);
// var productId            = $(".productCodeId").val();

var remark               = $("#remarkId").val();
var qty                  = $("#totalQuantity").val();
var totalAmount          = $("#totalAmount").val();
var discountAmount       = $("#discountTotal").val();
var totalAmaountAfterDis = $("#afterDiscount").val();
var vatAmount            = $("#vatAmountTotal").val();
var grossTotal           = $("#grossTotal").val();
var payAmount            = $("#payAmount").val();
var dueAmount            = $("#puchaseDue").val();

var projectId            = $("#project").val();
var projectTypeId        = $("#projectType").val();
var branchId             = $("#branch").val();
var cashBankLedger       = $('#cashBankLedger').val();
var paymentType          = $('#paymentTypeId').val();
var csrf                 = "<?php echo csrf_token(); ?>";


var formData = new FormData();

formData.append('billNo',billNo);
formData.append('conPerson',conPerson);
formData.append('supplierId',supplierId);
formData.append('purchaseDate',purchaseDate);
//formData.append('productId',productId);
formData.append('remark',remark);
formData.append('productType',productType);
formData.append('qty',qty);
formData.append('totalAmount',totalAmount);
formData.append('discountAmount',discountAmount);
formData.append('totalAmaountAfterDis',totalAmaountAfterDis);
formData.append('vatAmount',vatAmount);
formData.append('grossTotal',grossTotal);
formData.append('payAmount',payAmount);
formData.append('dueAmount',dueAmount);
formData.append('productId',productId);
formData.append('quantity',JSON.stringify(quantity));
formData.append('price',JSON.stringify(price));
formData.append('total',JSON.stringify(total));
formData.append('productID',JSON.stringify(productID));
formData.append('projectId',projectId);
formData.append('projectTypeId',projectTypeId);
formData.append('branchId',branchId);
formData.append('cashBankLedger',cashBankLedger);
formData.append('paymentType',paymentType);
formData.append('_token',csrf);

    if(this.id == 'submit') option = 1;
    else if(this.id == 'submitPrint') option = 2;

    if (supplierId == "") {
        alert('Supplier required');
    }else if(remark == ""){
       alert('Remark required'); 
    }
    else if($("#purchaseDate").val() == 0)
    {
        alert('Date Required');
    }
    else if($("#project").val() == 0)
    {
        alert('Project Required');
    }
    else if($("#projectType").val() == 0)
    {
        alert('Project Type Required');
    }
    else if($("#branch").val() == 0)
    {
        alert('Branch Required');
    }
    else if($('#productType').val() == 0)
    {
        alert('Product Type Required');
    }
    else if($('#paymentTypeId').val() == 0)
    {
        alert('Payment Type Required');
    }
    else if($('#cashBankLedger').val() == 0)
    {
        alert('Ledger Required');
    }
    else{
        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: './posSavePurchaseItem',
            data: formData,
            dataType: 'json',
            success: function( data ){
                
                if(option == 1) window.location.href = "{{ url('pos/purchase/')}}";
                else if(option == 2) window.location.href = "{{ url('pos/viewPurchaseItem')}}/"+data['purchaseId'];
            }
        });
    }
});


});
</script>
{{-- End Filtering --}}
{{-- Get Product Information --}}

<script type="text/javascript">

    dateRange = <?php echo json_encode($dateRange) ?>;

    $(document).ready(function() {

        $("#purchaseDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1998:c",
            minDate: dateRange.startDate,
            maxDate: dateRange.endDate,
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#purchaseDatee').hide();
            }
        });
    });

$('.select2').select2();

</script>

@endsection
