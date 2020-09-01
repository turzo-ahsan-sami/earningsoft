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
                <a href="{{url('pos/sales/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Sales List</a>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Edit Sales</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <div class="col-md-6" style="padding-right: 0px; padding-left: 0px;">
                            <div class="col-md-9">
                                
                                <div class="form-group">
                                    {!! Form::label('purchaseId', 'Bill Number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('saleBillNo',$sale->saleBillNo, array('class'=>'form-control', 'id' => 'saleNoId','readonly')) !!}
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('customerTypeId', 'Customer:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <select name="customerId" id="customerTypeId" class="form-control col-sm-9">
                                                    <option value="">Select Customer</option>
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer->id}}" 
                                                            {{ ( $customer->id == $sale->customerId) ? 'selected' : '' }}>{{$customer->name}}</option>   
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                               <a href="{{url('pos/addCustomer/')}}" class="btn btn-primary" target="_blank">Add</a>
                                            </div>
                                        </div>
                                        <p id='productTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('personId', 'Contact Person:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('conPerson',$sale->conPerson, array('class'=>'form-control', 'id' => 'personId', 'placeholder'=>'Contact Person')) !!}
                                    </div>
                                </div>
                                
                               
                                <div class="form-group">
                                    {!! Form::label('remarks', 'Remarks:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('remark',$sale->remark, array('class'=>'form-control', 'id' => 'remarkId','placeholder'=>'Enter Remarks')) !!}
                                    </div>
                                    <p id="remarksIde" style="max-height:3px;"></p>
                                </div>
                               
                                <div class="form-group">
                                    {!! Form::label('purchaseDate', 'Sales Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-8" style="padding-right: 0px;">
                                        {!! Form::text('purchaseDate', $value = $sale->salesDate, ['class' => 'form-control', 'id' => 'purchaseDate', 'type' => 'text','placeholder' => 'Enter Sale Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                        <p id='purchaseDatee' style="max-height:3px;color: red;display: none;">*Required</p>
                                    </div>
                                    <div class="col-sm-1" style="padding-right: 0px;">
                                        <i class="fa fa-calendar" aria-hidden="true" style="font-size: 2em;"></i>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('stocks', 'Stock:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('stock',$sale->stock, array('class'=>'form-control', 'id' => 'stockId','placeholder'=>'Enter Stock')) !!}
                                    </div>
                                    <p id="remarksIde" style="max-height:3px;"></p>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Project', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="project" name="projectId">
                                            <option value="0">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}" {{ ($sale->projectId == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>      
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
                                            <option value="{{ $productType->type }}">{{ ($productType->type == 'product') ? 'Product' : 'Hour' }}</option>
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
                                    {!! Form::label('totalQuantity', 'Total Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('qty',$sale->quantity, array('class'=>'form-control', 'id' => 'totalQuantity','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalQuantityIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('totalAmount',$sale->totalAmount, array('class'=>'form-control saleTotalAmount', 'id' => 'totalAmount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                </div>
                                
                                <div class="form-group">
                                    {!! Form::label('discount', 'Discount(%):', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                {!! Form::text('discountAmount',null, array('class'=>'form-control', 'id' => 'discount','placeholder'=>0)) !!}
                                            </div>
                                            <div class="col-sm-2">
                                                :
                                            </div>
                                            <div class="col-sm-5">
                                                {!! Form::text('discountAmount',$sale->discountAmount, array('class'=>'form-control', 'id' => 'discountTotal','placeholder'=>0,)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <p id="vatIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('afterDiscount', 'T/A After Discount:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('totalAmaountAfterDis',$sale->totalAmountAfterDis, array('class'=>'form-control', 'id' => 'afterDiscount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('vat', 'Vat(%):', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                {!! Form::text('vatAmount',null, array('class'=>'form-control', 'id' => 'vatAmount','placeholder'=>0)) !!}
                                            </div>
                                            <div class="col-sm-2">
                                                :
                                            </div>
                                            <div class="col-sm-5">
                                                {!! Form::text('vatAmount',$sale->vatAmount, array('class'=>'form-control', 'id' => 'vatAmountTotal','placeholder'=>0,)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <p id="vatIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('grossTotal', 'Gross Total:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('grossTotal',$sale->grossTotal, array('class'=>'form-control', 'id' => 'grossTotal','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="grossTotalIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('payAmount', 'Pay Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('payAmount',$sale->payAmount, array('class'=>'form-control', 'id' => 'payAmount','placeholder'=>0)) !!}
                                    </div>
                                    <p id="payAmountIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('puchaseDue', 'Purchase Due:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('dueAmount',$sale->dueAmount, array('class'=>'form-control', 'id' => 'puchaseDue','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="puchaseDuede" style="max-height:3px;"></p>
                                </div>

                                <div class="form-group">
                                     {!! Form::label('paymentTypeId', 'Payment Type:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select name="paymentType" id="paymentTypeId" class="form-control">
                                            <option value="">Select Payment Type</option>
                                           @foreach($payments as $payment)
                                                 <option value="{{$payment->id}}" {{$sale->paymentType == $payment->id ? 'selected' : ''}}>{{$payment->name}}</option>
                                           @endforeach    
                                        </select>
                                    </div>
                                    <p id="puchaseDuede" style="max-height:3px;"></p>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Ledger', 'Ledger:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        <select class="form-control" id="cashBankLedger" name="cashBankLedger">
                                            <option value="0">Select Ledger</option>
                                        </select>
                                        <p id='cashBankLedgerMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                                
                                <input type="hidden" value="{{$sale->id}}" id="saleId">
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
                                       <!--  <th style="text-align:center;">Product Name</th> -->
                                        <th style="text-align:center;">Qty</th>
                                        <th style="text-align:center;">Price</th>
                                        <th style="text-align:center;">Total</th>
                                        <th style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="chartTable">
                                        <td id="normalProduct">
                                          <select style="width: 100%; padding-top:6px; font-size: 11px;" class="productCode select2">
                                                <option value="">Select product</option>
                                                
                                                @foreach($products as $product)
                                                  <option value="{{$product->id}}" class="productCodeId">{{str_pad($product->code,3,"0",STR_PAD_LEFT )}} - {{$product->name}}</option>
                                                @endforeach
                                          </select>
                                          
                                        </td>
                                        <td id="hourProduct">@if($hourBaseProduct){{ $hourBaseProduct->code }} - {{ $hourBaseProduct->name }}@endif</td>
                                       <!--  <td><span class="productName"></span></td> -->
                                        <td hidden><span class="productName"/></spsn></spsn></td>
                                        <td hidden><span class="productCodeIde"/></spsn></spsn></td>
                                        <td><input type="text" name="qty" class="quantityCal" style="text-align:center;" /></td>
                                        <td><input type="text" name="productPrice" class="productPrice" style="text-align:center;" /></td>
                                        <td><span ><span class="totalPrice"/></span></td>

                                        <td>{!! Form::button('Add to chart', ['id' => '', 'class' => 'btn btn-primary addToChartBtn']) !!}</td>
                                    </tr> 
                                    @foreach($salesDetails as $salesDetail)
                                    <tr class="calulationTotal">
                                        <td>{{$salesDetail->code}} - {{$salesDetail->name}}</td>
                                        <td class="productId" hidden>{{$salesDetail->productId}}</td>
                                        <td hidden>{{$salesDetail->name}}</td>
                                        <td class="purchaseQuantity">{{$salesDetail->quantity}}</td>
                                        <td class="price">{{$salesDetail->price}}</td>
                                        <td class="purchasePrice">{{$salesDetail->price * $salesDetail->quantity}}</td>
                                        <td><button class="remove_field"  style="float:right; color:red">X</button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                        <div class="form-group">
                        <div class="col-sm-6 text-right" style="padding: 20px 20px 0px 0px;">
                            {!! Form::button('Update', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                            <a href="{{url('pos/sales/')}}" class="btn btn-danger closeBtn">Close</a>
                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                        </div>
                        </div>
                    </div> 
                    </div>  
                {!! Form::close() !!} 
            </div>
            
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<script type="text/javascript">
    
    var projectTypes = [];
    
    projectTypes = <?php echo json_encode($projectTypes) ?>;
    sale = <?php echo json_encode($sale) ?>;
    productType = <?php echo json_encode($productType->type) ?>;

    if(productType == 'product')
    {
        $('#chartTable').show();
        $('#normalProduct').show();
        $('#hourProduct').hide();
    }
    else
    {
        $('#chartTable').show();
        $('#normalProduct').hide();
        $('#hourProduct').show();

        hourBaseProduct = <?php echo json_encode($hourBaseProduct) ?>;

        $.ajax({
            url:'../salesAddProductNameOnChangeProductCode',
            type: 'GET',
            data: {productCode:hourBaseProduct.id},
            dataType: 'json',
            success: function(data) {
                
                var productName =  data['name'];
                var productPrice = data['salesPrice'];
                var productId =    data['id'];
                var productCode =  data['code'];

                $('.productName').html(productName);
                $('.productPrice').val(productPrice);
                $('#productId').html(productId);
                $('.productCodeIde').html(productCode);
                $(".productCode").val(productId);
                $('.productId').html(productId);
            }
        });
    } 

$('#paymentTypeId').change(function(){

    var paymentTypeId = $(this).val();

    $('#cashBankLedger').html('');
    $('#cashBankLedger').append('<option value="0">Select Ledger</option>');

    if(paymentTypeId != 0)
    {
        $.ajax({
            url:'../getLedgerForSales',
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

$(document).ready(function() {

    for(i=0; i<projectTypes.length; i++)
    {   
        if(sale.projectId == projectTypes[i].projectId)
        {
            if(sale.projectTypeId == projectTypes[i].id) $('#projectType').val(projectTypes[i].id);
        }
    }

    var paymentTypeId = $( "#paymentTypeId option:selected" ).val();

    $('#cashBankLedger').html('');
    $('#cashBankLedger').append('<option value="0">Select Ledger</option>');

    $.ajax({
        url:'../getLedgerForSales',
        type: 'GET',
        data: {paymentTypeId:paymentTypeId},
        dataType: 'json',
        success: function(data) {
           
            for(i=0; i<data.length; i++)
            {   
                if(sale.cashBankLedgerId == data[i].id)
                    $('#cashBankLedger').append('<option value='+data[i].id+' selected>'+data[i].name+'</option>');
                else $('#cashBankLedger').append('<option value='+data[i].id+'>'+data[i].name+'</option>');
            }
        }
    });

//onchange product code

setProduct = 0;

$('.productCode').change(function(){
    var productCode = $(this).val();
    if(productCode != ''){
         $.ajax({
        url:'../salesAddProductNameOnChangeProductCode',
        type: 'GET',
        data: {productCode:productCode},
        dataType: 'json',
        success: function(data) {
            // console.log(data);
            var productName =  data['name'];
            var productPrice = data['salesPrice'];
            var productId =    data['id'];
            var productCode =  data['code'];

           $('.productName').html(productName);
           $('.productPrice').val(productPrice);
           $('#productId').html(productId);
           setProduct = productId;
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

// $('.quantityCal,.productPrice').on('input',function(){
   
//    getPricequantity();

// })

$('.quantityCal').on('input',function(){

    productId = setProduct;

    $.ajax({
        url:'../checkQty',
        type: 'GET',
        data: {productId:productId},
        dataType: 'json',
        success: function(data) {

            var qty = $('.quantityCal').val();
            if(parseInt(data) < parseInt(qty))
            {   
                $('.quantityCal').val('');
                alert('This product quantity limit is '+data);
                return false;
            
            }
            else getPricequantity();
        }     
    });
});

$('.productPrice').on('input',function(){

    getPricequantity();

});

function getPricequantity(){
    var qty = $('.quantityCal').val();
   var price =  $('.productPrice').val();;
   
   var total = price * qty ;
   $('.totalPrice').html(total);
}

//Add to chart button 
 var i=1; 
$('.addToChartBtn').on('click',function(){
    $('.totalPrice').html('');
    var productname =  $('.productName').html();
    var productCode = $('.productCodeIde').html();
    // var productId = $('.productId').html();
    var productId = $('.productCode :selected').val();

    // console.log(productId);

    
    var qty = $('.quantityCal').val();
    var price =  $('.productPrice').val();
    var total = price * qty ;

        if(productname=="") {
            alert('Please insert product');
        }else if(qty=="") {
            alert('Please insert quantity');
        }else{
            $('.productName').html('');
            $('.productPrice').val('');
            $('.totalPrice').html('');
            $('.quantityCal').val('');
            //add more product field
             i++;  
            $('#productInfoTable').append('<tr id="row'+i+'" class="calulationTotal"><td><span class="">'+productCode+' - '+productname+'</span></td><td hidden><span class="productId">'+productId+'</span></td><td><span class="purchaseQuantity">'+qty+'</span></td><td><span class="price">'+price+'</span></td><td><span class="purchasePrice">'+total+'</span></td><td><button class="remove_field"  style="float:right; color:red">X</button</td></tr>'

            );
            
        } 
        calQuantityPrice();             
   
})

$("#productInfoTable")
  //.on('input', 'input', calQuantityPrice)
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
        
        $('.totalQty').html(totalQuantity);  
        $('.totalAmount').html(totalPrice);

        $('#totalQuantity').val(totalQuantity);
        $('#totalAmount').val(totalPrice);
        $('#afterDiscount').val(totalPrice);
        $('#puchaseDue').val(totalPrice);
        $('#grossTotal').val(totalPrice); 
}

var totalQuantity = $('#totalQuantity').val();
var totalAmount = $('#totalAmount').val();

$('#productInfoTable').append('<td colspan="1" style="text-align:center"><strong>Total Quantity</strong></td><td style="text-align:center"><strong class="totalQty">'+totalQuantity+'</strong></td><td style="text-align:center; font-weight:bold;"><strong class=""/>Total Price</strong></td><td style="text-align:center; font-weight:bold;"><strong class="totalAmount">'+totalAmount+'</strong></td>');
 

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
$("#submit").click(function(event) {    
var saleBillNo               = $("#saleNoId").val();
var conPerson                = $("#personId").val();
var customerId               = $("#customerTypeId").val();
var salesDate                = $("#purchaseDate").val();
var paymentType              = $("#paymentTypeId").val();
var stock                    = $("#stockId").val();
var saleId                   = $("#saleId").val();
var productId                = $(this).find('.productId').text();
 
 var productID = [];
 var quantity = [];
 var price = [];
 var total = [];
 var test = $('#productInfoTable .calulationTotal');

test.each(function(){
    var inputProductId= $(this).find('.productId').text();
    var inputQuantity= $(this).find('.purchaseQuantity').text();
    var purchasePrice= $(this).find('.price').text();
    var totalPrice= $(this).find('.purchasePrice').text();

    productID.push(inputProductId);
    quantity.push(inputQuantity);
    price.push(purchasePrice);
    total.push(totalPrice);
    
})


//alert(productDetails);
var productId  = $(".productCode").val();

if(productId == null || !productId) productId = productID[0];

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
var csrf                 = "<?php echo csrf_token(); ?>";


var formData = new FormData();

formData.append('saleBillNo',saleBillNo);
formData.append('conPerson',conPerson);
formData.append('customerId',customerId);
formData.append('projectId',projectId);
formData.append('projectTypeId',projectTypeId);
formData.append('branchId',branchId);
formData.append('salesDate',salesDate);
formData.append('stock',stock);
formData.append('remark',remark);
formData.append('qty',qty);
formData.append('totalAmount',totalAmount);
formData.append('discountAmount',discountAmount);
formData.append('totalAmaountAfterDis',totalAmaountAfterDis);
formData.append('vatAmount',vatAmount);
formData.append('grossTotal',grossTotal);
formData.append('payAmount',payAmount);
formData.append('dueAmount',dueAmount);
formData.append('productId',productId);
formData.append('paymentType',paymentType);
formData.append('saleId',saleId);
formData.append('quantity',JSON.stringify(quantity));
formData.append('price',JSON.stringify(price));
formData.append('total',JSON.stringify(total));
formData.append('productID',JSON.stringify(productID));
formData.append('cashBankLedger',cashBankLedger);
formData.append('_token',csrf);

    if (customerId == "") {
        alert('Supplier required');
    }else if(remark == ""){
       alert('Remark required'); 
    }
    else if($('#purchaseDate').val() == "")
    {
        alert('Date Required');
    }
    else if(stock == ""){
       alert('Stock required'); 
    } 
    else if($("#project").val() == 0)
    {
        alert('Project Required');
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
            url: '../updateSalesItem',
            data: formData,
            dataType: 'json',
            success: function( data ){
                // console.log(data);
                window.location.href = "{{ url('pos/sales/')}}";
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