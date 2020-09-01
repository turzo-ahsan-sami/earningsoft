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
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title">Edit Sales Return</div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                        <div class="col-md-6" style="padding-right: 0px; padding-left: 0px;">
                            <div class="col-md-9">
                                
                                <div class="form-group">
                                    {!! Form::label('purchaseId', 'Bill number:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('billNo',$sale->billNo, array('class'=>'form-control', 'id' => 'billNoId','readonly')) !!}
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('', 'Customer:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select name="supplierId" id="customerTypeId"class="form-control col-sm-9">
                                            <option value="">Select customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{$customer->id}}" {{ ( $customer->id == $sale->customerId) ? 'selected' : '' }}>{{$customer->name}}</option>
                                            @endforeach
                                        </select>
                                        <p id='productTypeIde' style="max-height:3px;"></p>
                                    </div>
                                </div>

                                 <div class="form-group">
                                    {!! Form::label('', 'Sale bill no:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        <select name="purchaseId" id="state" class="form-control col-sm-12">
                                           <option selected>{{$salesBill->saleBillNo}}</option>
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
                                <input type="hidden" id="branch" name="branchId" value="{{ $branch }}"/>

                            </div>
                            {{-- <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="60%" height="" style="float:right">
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-9">
                                <div class="form-group">
                                    {!! Form::label('totalQuantity', 'Total Quantity:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('qty',$sale->qty, array('class'=>'form-control', 'id' => 'totalQuantity','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalQuantityIde" style="max-height:3px;"></p>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalAmount',$sale->totalAmount, array('class'=>'form-control', 'id' => 'totalAmount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('totalAmount', 'Stock:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9">
                                        {!! Form::text('totalAmount',null, array('class'=>'form-control', 'id' => 'totalAmount','placeholder'=>0,'readonly')) !!}
                                    </div>
                                    <p id="totalAmountIde" style="max-height:3px;"></p>
                                    <input type="hidden" name="" id="productCode">
                                     <input type="hidden" name="purchaseID" id="purchaseID" value="{{$sale->id}}">
                                </div>

                                <div class="form-group">
                                    {!! Form::label('purchaseDate', 'Date:', ['class' => 'col-sm-3 control-label']) !!}
                                    <div class="col-sm-9" style="padding-right: 0px;">
                                        {!! Form::text('purchaseDate', $value = $sale->returnDate, ['class' => 'form-control', 'id' => 'purchaseDate', 'type' => 'text','placeholder' => 'Enter date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
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
                                     @foreach($sales as $saleDetail)
                                    <tr class="calulationTotal" >
                                       <td><span class="quantityCal">{{$saleDetail->name}}</span></td>
                                       <td hidden><input type="text" id="salesTableId" value="{{ $saleDetail->salesTableId }}"/></td>
                                       <td hidden><span class="quantityCal">{{$saleDetail->quantity}}</span></td>
                                       <td hidden><span class="productID">{{$saleDetail->productId}}</span></td>
                                       <td><input type="text" class="purchaseQuantity" value="{{$saleDetail->quantity}}" style="text-align:center;" /></td>
                                       <td><span class="productPrice">{{$saleDetail->price}}</span></td>
                                       <td><span class="totalPrice">{{$saleDetail->price * $saleDetail->quantity}}</span></td>
                                       <td hidden><span id="quantityMax">{{$saleDetail->quantity}}</span></td>
                                       <td><button class="remove_field"  style="float:right; color:red">X</button></td></tr>
                                   </tr>
                                    @endforeach 
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="saleId" id="saleId" value="{{$sale->id}}">
                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                        <div class="form-group">
                        <div class="col-sm-6 text-right" style="padding: 20px 20px 0px 0px;">
                            {!! Form::button('Update', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                            <a href="{{url('pos/salesReturn/')}}" class="btn btn-danger closeBtn">Close</a>
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
    sale = <?php echo json_encode($sale) ?>;

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

    // $('#projectType').html('');
    // $('#projectType').append('<option value="0">Select Project Type</option>');

    // $('#branch').html('');
    // $('#branch').append('<option value="0">Select Branch</option>');

    
    // for(i=0; i<projectTypes.length; i++)
    // {   
    //     if(sale.projectId == projectTypes[i].projectId)
    //     {
    //         if(sale.projectTypeId == projectTypes[i].id)
    //             $('#projectType').append('<option value='+projectTypes[i].id+' selected>'+projectTypes[i].name+'</option>');
    //         else $('#projectType').append('<option value='+projectTypes[i].id+'>'+projectTypes[i].name+'</option>');
    //     }
    // }

    // for(i=0; i<branches.length; i++)
    // {   
    //     if(sale.projectTypeId == branches[i].projectTypeId)
    //     {
    //         if(sale.branchId == branches[i].id)
    //             $('#branch').append('<option value='+branches[i].id+' selected>'+branches[i].name+'</option>');
    //         else $('#branch').append('<option value='+branches[i].id+'>'+branches[i].name+'</option>');
    //     }
    // }
//onchange product code
    
    saleBillNo = <?php echo json_encode($salesBill->saleBillNo) ?>;

    $('#project').html('');
    $('#project').append('<option value="0">Select Project</option>');

    $.ajax({
        url:'../getProjectDetailsForSalesReturn',
        type: 'GET',
        data: {saleBillNo:saleBillNo},
        dataType: 'json',
        success: function(data) {
            $('#project').append('<option value='+data[0].projectId+' selected>'+data[0].project+'</option>');
            $('#projectType').val(data[0].projectTypeId);
        }
    })


//quantity calculation on input



//onchange supplier 
// $('#customerTypeId').on('change',function(){
//     if($(this).val() != ''){
//         var customerId = $(this).val();

//          $.ajax({
//             url:'../getBillNoOnChangeCustomerReturn',
//             type: 'GET',
//             data: {customerId:customerId},
//             dataType: 'json',
//             success: function(data) {
//                $("#state").empty();
//                 $("#state").append('<option>Select</option>');
//                 $.each(data,function(key,value){
//                     $("#state").append('<option value="'+value.saleBillNo+'">'+value.saleBillNo+'</option>');
//                 });
//             }
//     });
// }
   
   
 
   //alert(supplierId);
    
// })

$('#state').on('change',function(){
    if($(this).val() != ''){
        var id = $(this).val();
        //alert(id);
         $.ajax({
            url:'../getProductOnChangeCustomer',
            type: 'GET',
            data: {id:id},
            dataType: 'json',
            success: function(data) {

                // console.log(data);

            $('#productInfoTable .calulationTotal').remove(); 
            calQuantityPrice();
            $.each(data,function(key,value){
                var productname = value.name;
                var qty = value.quantity;
                var total = value.total;
                var totalAmount = qty * total;
                var productId = value.productId;
                $('#productCode').val(productId);
                $('#totalAmount').val(totalAmount);
                 
                  $('#productInfoTable').append('<tr id="row'+i+'" class="calulationTotal"><td hidden><span class="quantityMax">'+qty+'</span></td><td><span class="quantityCal">'+productname+'</span></td><td hidden><span class="productID">'+productId+'</span></td><td><input type="text" class="purchaseQuantity" name="purchaseQuantity[]" value="'+qty+'"/></td><td><span class="productPrice">'+total+'</span></td><td><span class="totalPrice">'+totalAmount+'</span></td><td><button class="remove_field"  style="float:right; color:red">X</button</td></tr>'


                );

                  calQuantityPrice();
            });
            }
        });
    }
});



$("#productInfoTable")
  .on('input', '.purchaseQuantity', function() {
    var qty =  $(this).val();
    if($(this).val() != ''){
        var maxQty =  $('#quantityMax').html();
        var maxlength = maxQty.length;
        //alert(maxQty);
        var price =  $(this).closest('tr').find('.productPrice').text();
  if(qty != ''){
    if(maxQty >= parseInt(qty)){
        var total = price * qty ;
        $(this).closest('tr').find('.totalPrice').html(total);
         calQuantityPrice();
    }else{
        alert(' Maximum ' +' ' + maxQty + '');
        var total = price * maxQty ;
        $(this).closest('tr').find('.totalPrice').html(total);

        $(this).closest('tr').find('.purchaseQuantity').val(maxQty);
        $(this).closest('tr').find('.productPrice').text(price);
       $(this).closest('tr').find('.totalPrice').html(total);
         calQuantityPrice();
        
    }  
     }
     }
   
   
 });

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
            var quantity = $(this).val();
            totalQuantity += parseFloat(quantity);
        })
         $('#productInfoTable .totalPrice').each(function(){
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

    var totalQuantity = $('#totalQuantity').val();
    var totalAmount = $('#totalAmount').val();

    $('#productInfoTable').append('<td colspan="1" style="text-align:center"><strong>Total Quantity</strong></td><td style="text-align:center"><strong class="totalQty">'+totalQuantity+'</strong></td><td style="text-align:center; font-weight:bold;"><strong class=""/>Total Price</strong></td><td style="text-align:center; font-weight:bold;">'+totalAmount+'<strong class="totalAmount"/></strong></td>');


//add pur
$("#submit").click(function(event) {    
var billNo               = $("#billNoId").val();
var saleId               = $("#saleId").val();
var customerId           = $("#customerTypeId").val();
var purchaseDate         = $("#purchaseDate").val();
 var productId=          $('#productCode').val();
 var productID = [];
 var quantity = [];
 var price = [];
 var total = [];
var test = $('#productInfoTable .calulationTotal');

var salesTableId = $('#salesTableId').val();

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
formData.append('purchaseDate',purchaseDate);
formData.append('qty',qty);
formData.append('totalAmount',totalAmount);

formData.append('salesTableId',salesTableId);

formData.append('projectId',projectId);
formData.append('projectTypeId',projectTypeId);
formData.append('branchId',branchId);

formData.append('productId',productId);
formData.append('saleId',saleId);
formData.append('quantity',JSON.stringify(quantity));
formData.append('price',JSON.stringify(price));
formData.append('total',JSON.stringify(total));
formData.append('productID',JSON.stringify(productID));
formData.append('_token',csrf);

    if (customerId == "") {
        alert('customerId required');
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
            url: '../posUpdateSalesReturnItem',
            data: formData,
            dataType: 'json',
            success: function( data ){
                // console.log(data);
                window.location.href = "{{ url('pos/salesReturn/')}}";
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
