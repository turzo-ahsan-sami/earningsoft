@extends('layouts/pos_layout')
@section('title', '| Sales Return')
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
                <a href="{{url('pos/salesReturn/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Sales Return List</a>
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
                    <div class="panel-title">Add Sales Return</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <div class="col-md-6" style="padding-right: 0px; padding-left: 0px;">
                            <div class="col-md-9">
                                
                                <div class="form-group">
                                    {!! Form::label('purchaseId', 'Bill number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('billNo',$saleBillNo, array('class'=>'form-control', 'id' => 'billNoId','readonly')) !!}
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('', 'Customer:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select name="customerId" id="customerTypeId" class="form-control col-sm-12">
                                            <option value="">Select customer</option>
                                            @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id='productTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                 <div class="form-group">
                                    {!! Form::label('', 'Sale bill no:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select name="purchaseId" id="state" class="form-control col-sm-12">
                                           <option >select</option>
                                          
                                        </select>
                                        <p id='productTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    {!! Form::label('Project', 'Project:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select class="form-control" id="project" name="projectId">
                                            <option value="0">Select Project</option>
                                        </select>
                                        <p id='projectMsg' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                <input type="hidden" id="projectType" name="projectTypeId" value=""/>
                                <input type="hidden" id="branch" name="branchId" value="{{ $branch }}" />

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
                                    {!! Form::label('stock', 'Stock:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('stock',null, array('class'=>'form-control', 'id' => 'stock','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                    <input type="hidden" name="" id="productCode">
                                </div>

                                <div class="form-group">
                                    {!! Form::label('purchaseDate', 'Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9" style="padding-right: 0px;">
                                        {!! Form::text('purchaseDate', $value = null, ['class' => 'form-control', 'id' => 'purchaseDate', 'type' => 'text','placeholder' => 'Enter date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                        <p id='purchaseDatee' style="max-height:3px;color: red;display: none;">*Required</p>
                                    </div>
                                    <!-- <div class="col-sm-1" style="padding-right: 0px;" h>
                                        <i class="fa fa-calendar" aria-hidden="true" style="font-size: 2em;"></i>
                                    </div> -->
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
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                        <div class="form-group">
                        <div class="col-sm-6 text-right" style="padding: 20px 20px 0px 0px;">
                            {!! Form::button('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                            {!! Form::button('Submit & Print', ['id' => 'submitPrint', 'class' => 'btn btn-success']) !!}
                            <a href="{{url('pos/salesReturn/')}}" class="btn btn-danger closeBtn">Close</a>
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

    var projects = [];
    
    projects = <?php echo json_encode($projects) ?>;

$('#state').change(function() {

    var saleBillNo = $(this).val();

    $('#project').html('');
    $('#project').append('<option value="0">Select Project</option>');

    $.ajax({
        url:'./getProjectDetailsForSalesReturn',
        type: 'GET',
        data: {saleBillNo:saleBillNo},
        dataType: 'json',
        success: function(data) {

            $('#project').append('<option value='+data[0].projectId+' selected>'+data[0].project+'</option>');
            $('#projectType').val(data[0].projectTypeId);
        }
    })

});

// $('#project').change(function(){

//     var projectId = $(this).val();

//     $('#projectType').html('');
//     $('#projectType').append('<option value="0">Select Project Type</option>');

//     $('#branch').html('');
//     $('#branch').append('<option value="0">Select Branch</option>');

//     if(projectId != 0)
//     {
//         for(i=0; i<projectTypes.length; i++)
//         {   
//             if(projectId == projectTypes[i].projectId)
//                 $('#projectType').append('<option value='+projectTypes[i].id+'>'+projectTypes[i].name+'</option>');
//         }
//     }
// });

// $('#projectType').change(function(){

//     var projectTypeId = $(this).val();

//     $('#branch').html('');
//     $('#branch').append('<option value="0">Select Branch</option>');

//     if(projectTypeId != 0)
//     {
//         for(i=0; i<branches.length; i++)
//         {   
//             if(projectTypeId == branches[i].projectTypeId)
//                 $('#branch').append('<option value='+branches[i].id+'>'+branches[i].name+'</option>');
//         }
//     }
// });

$(document).ready(function() {

//onchange supplier 
$('#customerTypeId').on('change',function(){
    if($(this).val() != ''){
        var customerId = $(this).val();


         $.ajax({
            url:'./getBillNoOnChangeCustomerReturn',
            type: 'GET',
            data: {customerId:customerId},
            dataType: 'json',
            success: function(data) {

                $("#state").empty();
                $("#state").append('<option>Select</option>');
                $('#productInfoTable .calulationTotal').remove(); 
                calQuantityPrice();
                $.each(data,function(key,value){
                    $("#state").append('<option value="'+value.saleBillNo+'">'+value.saleBillNo+'</option>');
                });
            }
         })
    }else{
         $("#state").empty();
        $("#state").append('<option>Select</option>'); 
         $('#productInfoTable .calulationTotal').remove(); 
          calQuantityPrice();
    }
   
 
   //alert(supplierId);
    
})



$('#state').on('change',function(){
    if($(this).val() != ''){
        var id = $(this).val();
        var supplierId = $('#customerTypeId').val();
        //alert(id);
         $.ajax({
            url:'./getProductOnChangeCustomer',
            type: 'GET',
            data: {id:id,supplierId:supplierId},
            dataType: 'json',
            success: function(data) {

                $('#productInfoTable .calulationTotal').remove(); 
                calQuantityPrice();
                
                $.each(data,function(key,value){
                        
                    var productname = value.productName;
                    var qty = value.quantity;
                    var total = value.price;
                    var productId = value.productId;
                    var totalAmount = qty * total;
                    
                    
                    var productId = value.productId;
                    $('#productCode').val(productId);

                    var salesId = value.salesId;
                        
                    $('#productInfoTable').append('<tr class="calulationTotal"><td hidden>><input type="text" id="salesId" value="'+salesId+'"/></td><td hidden><span class="quantityMax">'+qty+'</span></td><td><span class="quantityCal">'+productname+'</span></td><td hidden><span class="productID">'+productId+'</span></td><td><input type="text" class="purchaseQuantity" value="'+qty+'" style="text-align:center;" /></td><td><span class="productPrice">'+total+'</span></td><td><span class="totalPrice">'+totalAmount+'</span></td><td><button class="remove_field"  style="float:right; color:red">X</button</td></tr>');
                
                    calQuantityPrice();
                });
            
            }
        });

    }

});

$("#productInfoTable")
  .on('input', '.purchaseQuantity', function() {
     var qty =  $(this).closest('tr').find('.purchaseQuantity').val();
    var maxQty =  $(this).closest('tr').find('.quantityMax').html();
    var totalPrice =  $(this).closest('tr').find('.totalPrice').html();
     var price =  $(this).closest('tr').find('.productPrice').text();

    var totalPrice =  $(this).closest('tr').find('.totalPrice').html();

     if(qty != ''){
            if(maxQty >= parseInt(qty)){
            //var price =  $(this).closest('tr').find('.productPrice').text();
            var total = price * qty ;
            $(this).closest('tr').find('.totalPrice').html(total);
            calQuantityPrice();
        }else{
           alert(' Maximum ' +' ' + maxQty + '');
             var total = price * maxQty ;
            $(this).closest('tr').find('.purchaseQuantity').val(maxQty);
            $(this).closest('tr').find('.productPrice').text(price);
            $(this).closest('tr').find('.totalPrice').html(total);
             calQuantityPrice();

          }  
        }
 });

//product on change
$('#productCode').change(function(){
     if($(this).val() != ''){
        var productCode = $('#state').val();
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
               $('.productPrice').val(productPrice);
               $('.productId').html(productId);
               $('.productCodeIde').html(productCode);
            }
        });
    }
})



//Add to chart button 
 var i=1; 


$("#productInfoTable")
  .on('click', '.remove_field', function() {
    $(this).closest("tr").remove();
    calQuantityPrice();
  });


function calQuantityPrice(){
        var totalQuantity = 0;
        var totalPrice = 0;
        $('#productInfoTable  .purchaseQuantity').each(function(){
            if($(this).val() != ''){
               var quantity = $(this).val(); 
            }else{
                var quantity = 0; 
            }
           
            totalQuantity += parseFloat(quantity);
        })
         $('#productInfoTable .totalPrice').each(function(){
            var price = $(this).html();
            totalPrice += parseFloat(price);
        })
       
        $('.totalQty').html(totalQuantity);  
        $('.totalAmount').html(totalPrice);

        $('#totalQuantity').val(totalQuantity);
        $('#totalAmount').val(totalPrice);
}



    var totalQuantity = $('#totalQuantity').val();
    var totalAmount = $('#totalAmount').val();

$('#productInfoTable').append('<td colspan="1" style="text-align:center"><strong>Total Quantity</strong></td><td style="text-align:center"><strong class="totalQty">'+totalQuantity+'</strong></td><td style="text-align:center; font-weight:bold;"><strong class=""/>Total Price</strong></td><td style="text-align:center; font-weight:bold;"><strong class="totalAmount"/>'+totalAmount+'</strong></td>');

//add pur
$('#submit, #submitPrint').click(function(event) {    
var billNo               = $("#billNoId").val();
var customerId           = $("#customerTypeId").val();
var purchaseDate         = $("#purchaseDate").val();
 var productId=          $('#productCode').val();
 var productID = [];
 var quantity = [];
 var price = [];
 var total = [];
var test = $('#productInfoTable .calulationTotal');

var salesId = $('#salesId').val();

test.each(function(){
    var inputProductId= $(this).find('.productID').html();
    var inputQuantity= $(this).find('.purchaseQuantity').val();
    var purchasePrice= $(this).find('.productPrice').html();
    var totalPrice= $(this).find('.totalPrice').html();
    productID.push(inputProductId);
    quantity.push(inputQuantity);
    price.push(purchasePrice);
    total.push(totalPrice);
    
})


//console.log(price);
//alert(productDetails);
// var productId            = $(".productCodeId").val();


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

var csrf                 = "<?php echo csrf_token(); ?>";


var formData = new FormData();

formData.append('billNo',billNo);
formData.append('customerId',customerId);

formData.append('projectId',projectId);
formData.append('projectTypeId',projectTypeId);
formData.append('branchId',branchId);

formData.append('purchaseDate',purchaseDate);
formData.append('qty',qty);
formData.append('totalAmount',totalAmount);

formData.append('salesId',salesId);

formData.append('productId',productId);
formData.append('quantity',JSON.stringify(quantity));
formData.append('price',JSON.stringify(price));
formData.append('total',JSON.stringify(total));
formData.append('productID',JSON.stringify(productID));
formData.append('_token',csrf);

    if(this.id == 'submit') option = 1;
    else if(this.id == 'submitPrint') option = 2;

    if (customerId == "") {
        alert('Customer required');
    }else if(purchaseDate == ""){
       alert('Date required'); 
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
            url: './posSaveSalesReturnItem',
            data: formData,
            dataType: 'json',
            success: function( data )
            {   
                if(option == 1) window.location.href = "{{ url('pos/salesReturn/')}}";
                else if(option == 2) window.location.href = "{{ url('pos/viewSalesReturnItem')}}/"+data['salesReturnId'];
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
