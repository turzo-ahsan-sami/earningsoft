@extends('layouts/pos_layout')
@section('title', '| order')
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
                <a href="{{url('pos/order/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Order List</a>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Edit order</div>
                </div>
                <div class="panel-body">
                    <div class="row"> 
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <div class="col-md-6" style="padding-right: 0px; padding-left: 0px;">
                            <div class="col-md-9">
                                
                                <div class="form-group">
                                    {!! Form::label('orderId', 'Bill Number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('billNo',$order->billNo, array('class'=>'form-control', 'id' => 'billNoId','readonly')) !!}
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
                                                    <option value="{{$customer->id}}" {{ ( $customer->id == $order->customerId) ? 'selected' : '' }}>{{$customer->name}}</option>
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
                                    {!! Form::label('personId', 'Contact Person:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('conPerson',$order->conPerson, array('class'=>'form-control', 'id' => 'personId', 'placeholder'=>'Contact Person')) !!}
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    {!! Form::label('remarks', 'Remarks:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('remark',$order->remark, array('class'=>'form-control', 'id' => 'remarkId','placeholder'=>'Enter Remarks')) !!}
                                    </div>
                                    <p id="remarksIde" style="max-height:3px;"></p>
                                </div>
                            </div>
                            {{-- <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="60%" height="" style="float:right">
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-9">
                                <div class="form-group">
                                    {!! Form::label('orderDate', 'order Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-8" style="padding-right: 0px;">
                                        {!! Form::text('orderDate', $value = $order->orderDate, ['class' => 'form-control', 'id' => 'orderDate', 'type' => 'text','placeholder' => 'Enter Sale Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                        <p id='orderDatee' style="max-height:3px;color: red;display: none;">*Required</p>
                                    </div>
                                    <div class="col-sm-1" style="padding-right: 0px;">
                                        <i class="fa fa-calendar" aria-hidden="true" style="font-size: 2em;"></i>
                                    </div>
                                </div>
                                <input type="hidden" id="projectType" name="projectTypeId" value=""/>
                                <input type="hidden" id="branch" name="branchId" value="{{ $branch }}" />

                              
                                <div class="form-group">
                                    {!! Form::label('Project', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="project" name="projectId">
                                            <option value="0">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}" {{ ($order->projectId == $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>      
                                            @endforeach
                                        </select>
                                        <p id='projectMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    {!! Form::label('Product Type', 'Product Type:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="productType" name="productType">
                                            @if($productType->type == 'product')
                                                <option value="{{ $productType->type }}">Product</option>
                                            @elseif($productType->type == 'hour')
                                                <option value="{{ $productType->type }}">Hour</option>
                                            @else 
                                                <option value="{{ $productType->type }}">Raw material</option>
                                            @endif
                                        </select>
                                        <p id='productTypeMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalAmount',$order->totalAmount, array('class'=>'form-control', 'id' => 'totalAmount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
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
                                        <th style="text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="chartTable">
                                        <td id="normalProduct">
                                           <select style="width: 100%; padding-top:6px; font-size: 11px;" class="productCode select2">
                                                <option value="">Select product</option>
                                                <!--  <option hidden name="productId" id="productId" class="productId "></option> -->
                                                @foreach($productNames as $productName)
                                                    @if($productName->type == $productType->type)
                                                        <option value="{{$productName->id}}" class="productCodeId" >{{str_pad($productName->code,3,"0",STR_PAD_LEFT )}} - {{$productName->name}}</option>
                                                    @endif
                                                @endforeach
                                          </select>
                                        </td>
                                        <td id="hourProduct">@if($hourBaseProduct){{ $hourBaseProduct->code }} - {{ $hourBaseProduct->name }}@endif</td>
                                        <td hidden><span class="productId" id="productId"/></spsn></spsn></td>
                                        <td hidden><span class="productName"/></spsn></spsn></td>
                                        <td hidden><span class="productCodeIde"/></spsn></spsn></td>
                                        <td><input type="text" name="qty" class="quantityCal" style="text-align:center;" /></td>
                                        <td>{!! Form::button('Add to chart', ['id' => '', 'class' => 'btn btn-primary addToChartBtn']) !!}</td>
                                    </tr>
                                    @php
                                      $sum = 0;
                                    @endphp
                                    @foreach($orderDetails as $orderDetail)
                                    <tr class="calulationTotal">
                                       <td>{{$orderDetail->code}} - {{$orderDetail->name}}</td>
                                       <td class="productId" hidden>{{$orderDetail->productId}}</td>
                                       <td hidden>{{$orderDetail->name}}</td>
                                       <td class="orderQuantity">{{$orderDetail->quantity}}</td>
                                       <td><button class="remove_field"  style="float:right; color:red">X</button></td>
                                    </tr>
                                     <?php
                                        $sum+= $orderDetail->quantity;
                                     ?>
                                    @endforeach 
                                    <td class="totalQuantity" hidden>{{$sum}}</td>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                        <div class="form-group">
                        <div class="col-sm-6 text-right" style="padding: 20px 20px 0px 0px;">
                            {!! Form::button('Update', ['id' => 'update', 'class' => 'btn btn-info']) !!}
                            <a href="{{url('pos/order/')}}" class="btn btn-danger closeBtn">Close</a>
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
{{-- Filtering --}}
<script type="text/javascript">

    var projectTypes = [];
   
    projectTypes = <?php echo json_encode($projectTypes) ?>;
    productType = <?php echo json_encode($productType->type) ?>;
    var order = <?php echo json_encode($order) ?>;

  
    if(productType == 'product')
    {
        $('#chartTable').show();
        $('#normalProduct').show();
        $('#hourProduct').hide();
    }
    else if(productType == 'raw')
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
            url:'../OrderProductNameOnChangeProductCode',
            type: 'GET',
            data: {productCode:hourBaseProduct.id},
            dataType: 'json',
            success: function(data) {
                
                var productName =  data['name'];
                var productPrice = data['costPrice'];
                var productId =    data['id'];
                var productCode =  data['code'];
            
                $('.productName').html(productName);
                $('.productPrice').val('');
                $('#productId').html(productId);
                $('.productCodeIde').html(productCode);
                $(".productCodeId").val(productId);
            
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
            url:'../getLedgerFororder',
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

    $('#projectType').val();
    
    for(i=0; i<projectTypes.length; i++)
    {   
        if(order.projectId == projectTypes[i].projectId)
        {
            if(order.projectTypeId == projectTypes[i].id) $('#projectType').val(projectTypes[i].id);
        }
    }

    var paymentTypeId = $( "#paymentTypeId option:selected" ).val();

    $('#cashBankLedger').html('');
    $('#cashBankLedger').append('<option value="0">Select Ledger</option>');

    $.ajax({
        url:'../getLedgerFororder',
        type: 'GET',
        data: {paymentTypeId:paymentTypeId},
        dataType: 'json',
        success: function(data) {
           
            for(i=0; i<data.length; i++)
            {   
                if(order.cashBankLedgerId == data[i].id)
                    $('#cashBankLedger').append('<option value='+data[i].id+' selected>'+data[i].name+'</option>');
                else $('#cashBankLedger').append('<option value='+data[i].id+'>'+data[i].name+'</option>');
            }
        }
    });
//onchange product code

$('.productCode').change(function(){
     if($(this).val() != ''){
    var productCode = $(this).val();
    $.ajax({
        url:'../OrderProductNameOnChangeProductCode',
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

$('.productPrice').on('input',function(){
   
   var price = $(this).val();
   var qty =  $('.quantityCal').val();
   
   var total = price * qty ;
   $('.totalPrice').html(total);
   
})

//onchange supplier 
$('#customerTypeId').change(function(){
    var customerId = $(this).val();
    //var productId = $('#customerTypeId').val();
   // alert(productCode);
    $.ajax({
        url:'../getBillNoOnChangeSupplier',
        type: 'GET',
        data: {customerId:customerId},
        dataType: 'json',
        success: function(data) {
            console.log(data);
           $("#billNoId").val(data);
        }
    });
})

//Add to chart button 
 var i=1; 
$('.addToChartBtn').on('click',function(){
    var productname =  $('.productName').html();
    var productCode = $('.productCodeIde').html();
    var productId = $('.productId').html();
    
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
            $('#productInfoTable').append('<tr id="row'+i+'" class="calulationTotal"><td><span class="">'+productCode+' - '+productname+'</span></td><td hidden><span class="productId">'+productId+'</span></td><td><span class="orderQuantity">'+qty+'</span></td><td><button class="remove_field"  style="float:right; color:red">X</button</td></tr>'

            );
            
            addToChartFunction();
        } 
        calQuantityPrice();             
   
})

$("#productInfoTable")
  .on('click', '.remove_field', function() {
    $(this).closest("tr").remove();
    calQuantityPrice();
    addToChartFunction();
  });

function addToChartFunction(){
       var productId = [];
        var quantity = [];
        var item = [];
        var orderInfo = $('#productInfoTable .calulationTotal');

        orderInfo.each(function(){
            productId.push($(this).find('.productId').text());
            quantity.push($(this).find('.orderQuantity').text());
           
           // item.push('productId: '+ productId+ "," + 'quantity'+quantity);
           
        })

        $.ajax({
            url:'../addToChartOrderItem',
            type: 'POST',
            data: {productId:productId,quantity:quantity},
            dataType: 'json',
            success: function(data) {
                $('#totalAmount').val(data);
                //console.log(data);
            }   
        });
}
function calQuantityPrice(){
    var totalQuantity = 0;
    var totalAmount = 0;
    $('#productInfoTable .calulationTotal .orderQuantity').each(function(){
        var quantity = $(this).html();
         totalQuantity += parseFloat(quantity);
         totalAmount +=  0; 
    })
  
    $('.totalQty').html(totalQuantity);  
    $('#totalAmount').val(totalAmount) 
     $('#totalQuantity').val(totalQuantity); 
     
}

var totalQuantity = $('.totalQuantity').text();
var totalAmount = $('#totalAmount').val();

$('#productInfoTable').append('<td colspan="1" style="text-align:center"><strong>Total Quantity</strong></td><td style="text-align:center"><strong class="totalQty">'+totalQuantity+'</strong></td>');

//add pur
$("#update").click(function(event) {    
var billNo               = $("#billNoId").val();
var conPerson            = $("#personId").val();
var customerId           = $("#customerTypeId").val();
var orderDate         = $("#orderDate").val();
 
 var productID = [];
 var quantity = [];
 
var test = $('#productInfoTable .calulationTotal');

test.each(function(){
    var inputProductId= $(this).find('.productId').text();
    var inputQuantity= $(this).find('.orderQuantity').text();
    var orderPrice= $(this).find('.price').text();
    var totalPrice= $(this).find('.orderPrice').text();
    productID.push(inputProductId);
    quantity.push(inputQuantity);
    
    
})

//alert(productDetails);
var productId            = $(".productCode").val();

var remark               = $("#remarkId").val();
var totalAmount          = $("#totalAmount").val();
var orderId               = "{{$order->id}}";

var projectId            = $("#project").val();
var projectTypeId        = $("#projectType").val();
var branchId             = $("#branch").val();
var csrf                 = "<?php echo csrf_token(); ?>";


var formData = new FormData();

formData.append('billNo',billNo);
formData.append('conPerson',conPerson);
formData.append('customerId',customerId);
formData.append('orderDate',orderDate);
//formData.append('productId',productId);
formData.append('projectId',projectId);
formData.append('projectTypeId',projectTypeId);
formData.append('branchId',branchId);
formData.append('remark',remark);
formData.append('totalAmount',totalAmount);
formData.append('orderId',orderId);
formData.append('quantity',JSON.stringify(quantity));
formData.append('productID',JSON.stringify(productID));
formData.append('_token',csrf);

    if (customerId == "") {
        alert('Supplier required');
    }else if(remark == ""){
       alert('Remark required'); 
    }
    else if($("#orderDate").val() == 0)
    {
        alert('Date Required');
    }
    else if($("#project").val() == 0)
    {
        alert('Project Required');
    }
    
    
    else{
        $.ajax({
            processData: false,
            contentType: false,
            type: 'post',
            url: '../posUpdateorderItem',
            data: formData,
            dataType: 'json',
            success: function( data ){
                window.location.href = "{{ url('pos/order/')}}";
            }
        });
    }
});


});
</script>



<script type="text/javascript">
     
    dateRange = <?php echo json_encode($dateRange) ?>;

    $(document).ready(function() {

        $("#orderDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1998:c",
            minDate: dateRange.startDate,
            maxDate: dateRange.endDate,
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#orderDatee').hide();
            }
        });
    });

$('.select2').select2();
</script>

@endsection