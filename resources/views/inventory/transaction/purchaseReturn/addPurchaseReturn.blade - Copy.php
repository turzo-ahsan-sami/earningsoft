@extends('layouts/inventory_layout')
@section('title', '| New Group')
@section('content')

<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
 $gnrBranchId = Session::get('branchId');
 $logedUserName = $user->name;

//echo $grnBranchId;
?>
<div class="row add-data-form" style="padding-bottom:20px;">
    <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewInvPurchaseReturnList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Purchase Return List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Purchase Return</div>
                                </div>
                    <div class="panel-body">
                    {!! Form::open(array('url' => '', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}

                     <div class="row">
                     <div class="col-md-6">   

                        <div class="form-group">
                            {!! Form::label('purchaseReturnBillNo', 'Purchase Return No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                    $purchseMaxId = DB::table('inv_purchase_return')->max('id')+1;
                                    $valueForField = 'PRR'.sprintf('%04d', $gnrBranchId) . sprintf('%06d', $purchseMaxId);
                                ?>
                                {!! Form::text('purchaseReturnBillNo', $value = $valueForField, ['class' => 'form-control', 'id' => 'purchaseReturnBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                                 <p id='billNoe' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('purchaseBillNo1', 'Pruchase BillNo:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                    $pruchaseBillNo = array('' => 'Please Select')+ DB::table('inv_purchase')->pluck('billNo','id')->all();  
                                ?>      
                                {!! Form::select('purchaseBillNo1', ($pruchaseBillNo), null, array('class'=>'form-control', 'id' => 'purchaseBillNo1')) !!}
                                <p id='pruchaseBillNo1e' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('supplierId', 'Supplier Name:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                    $supplierId = array('' => 'Please Select'); 
                                ?>      
                                {!! Form::select('supplierId', ($supplierId), null, array('class'=>'form-control', 'id' => 'supplierId')) !!}
                                <p id='supplierIde' style="max-height:3px;"></p>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('purchaseDate', 'Purchase Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('purchaseDate', null, ['class' => 'form-control', 'id' => 'purchaseDate', 'readonly']) !!}
                                 <p id='purchaseDatee' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('purchaseReturnDate', 'Purchase Return Date:', ['class' => 'col-sm-4 control-label']) !!}
                                <?php $date = date("Y-m-d");?>
                            <div class="col-sm-8">
                                {!! Form::text('purchaseReturnDate',$date, ['class' => 'form-control', 'id' => 'purchaseReturnDate', 'readonly', 'readonly']) !!}
                                 <p id='purchaseReturnDatee' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('remark', 'Remark:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::textarea('remark', null, ['class' => 'form-control', 'rows'=>2, 'id' => 'remark']) !!}
                                 <p id='remarke' style="max-height:3px;"></p>
                            </div> 
                        </div>

                    </div>    
                    <div class="col-md-6">

                        <div class="form-group">
                            {!! Form::label('totalQuantity', 'Total Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalQuantity', null, ['class' => 'form-control numeric', 'id' => 'totalQuantity', 'readonly']) !!}
                                 <p id='totalQuantitye' style="max-height:3px; color:red;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalAmount', null, ['class' => 'form-control numeric', 'id' => 'totalAmount', 'readonly']) !!}
                                 <p id='totalAmounte' style="max-height:3px; color:red;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('discount', 'Discount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <div class="col-sm-5" style="padding-left: 0%">
                                    {!! Form::text('discountPercent', null, ['class' => 'form-control numeric col-sm-5', 'id' => 'discountPercent', 'placeholder'=>'%', 'readonly']) !!}
                                </div>
                                
                                <div class="col-sm-7" style="padding-right: 0%; padding-left: 0%">
                                    {!! Form::text('discount', null, ['class' => 'form-control col-sm-7 numeric', 'id' => 'discount', 'readonly']) !!}
                                </div>
                                 <p id='discounte' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('amountAfterDiscount', 'T/A After Discount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('amountAfterDiscount', null, ['class' => 'form-control', 'id' => 'amountAfterDiscount', 'readonly']) !!}
                                 <p id='amountAfterDiscounte' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('vat', 'VAT:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <div class="col-sm-5" style="padding-left: 0%">
                                    {!! Form::text('vatPercent', null, ['class' => 'form-control numeric col-sm-5', 'id' => 'vatPercent', 'placeholder'=>'%', 'readonly']) !!}
                                </div>
                               
                                <div class="col-sm-7" style="padding-right: 0%; padding-left: 0%">
                                    {!! Form::text('vat', 'VAT is not refundable', ['class' => 'form-control col-sm-7 numeric', 'id' => 'vat', 'readonly']) !!}
                                </div>
                                 <p id='vate' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group">
                            {!! Form::label('grossTotal', 'Gross Total:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('grossTotal', null, ['class' => 'form-control', 'id' => 'grossTotal', 'readonly']) !!}
                                 <p id='grossTotale' style="max-height:3px;"></p>
                            </div> 
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('branchId', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            <?php 
                                $branchNames = DB::table('gnr_branch')->where('id',$gnrBranchId)->get(); 
                            ?>
                                <select class ="form-control" id = "branchId" autocomplete="off" name="branchId" readonly>
                                    @foreach($branchNames as $branchName)
                                    <option value="{{$branchName->id}}">{{$branchName->name}}</option>
                                    @endforeach
                                </select>
                                <p id='branchIde' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('createdBy', 'Created By:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('createdBy', $logedUserName, ['class' => 'form-control', 'id' => 'createdBy']) !!}
                                <p id='branchIde' style="max-height:3px;"></p>

                            </div>
                        </div>
                        <p id='numericError' style="max-height:3px; color:red;"></p>

                        

                        <div class="form-group">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8 text-right" style="">
                                {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                <a href="{{url('viewInvPurchaseList/')}}" class="btn btn-danger closeBtn">Close</a>
                                
                            </div>
                        </div>
                </div>                  
                </div>       

                    <div class="row">
                        <div class="col-md-12">
                        <table id="addProductTable" class="table table-bordered responsive addProductTable">
                            <thead>
                                <tr class="">
                                    <th style="text-align:center;" class="col-sm-3">Item Name</th>
                                    <th style="text-align:center;" class="col-sm-2">Qty</th>
                                    <th style="text-align:center;" class="col-sm-2">Price</th>
                                    <th style="text-align:center;" class="col-sm-2">Total</th>
                                    <th style="text-align:center;" class="col-sm-1">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php 
                                        $productId = array('' => 'Please Select product'); 
                                    ?>
                                    {!! Form::select('productId', ($productId), null, array('class'=>'form-control input-sm', 'id' => 'productId')) !!}
                                </td>
                                <td class="">
                                    <input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='' placeholder='Insert Item' min="1"/>
                                </td>
                                <td class="">
                                    <input type='number' class="form-control input-sm text-center" id='productPriceAddPro' name='productPriceApn[]' value='' placeholder='Enter product price' min="1" readonly="readonly" />
                                </td>
                                <td class="">
                                    <input type='number' class="form-control input-sm text-center" id='totalAmountAddPro' name='totalAmountApn[]' value='' placeholder='' min="1" readonly />
                                </td>
                                <td><input style='text-align:center;border-radius:0;width:80px;' id='addProduct' class='btn btn-primary btn-xs'
                                    type='button' name='productAddButton' value='Add Product'/>
                                </td>
                            </tr>
                            <tr class="">
                                <td>
                                    <p class="hidden" id="productIdError" style="color: red;">Product Field Is Requrired</p>
                                    <p class="hidden" id="productIde" style="color: red;">Product Name and Quantity is required</p>
                                </td>
                                <td>
                                    <p class="hidden" id="qnttyError" style="color: red;">Product Qty Is Required</p>
                                    <p class="hidden" id="productQuantitye" style="color: red;"></p>
                                </td>
                                <td class="">
                                    <p class="hidden" id="priceError" style="color: red;">Price Required</p>
                                </td>
                                <td class=""></td>
                                <td class=""></td>

                            </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align:right;"><strong>Total Quantity</strong></td>
                                    <td style="text-align:center;" id='totalQuantityFooter'></td>
                                    <td style="text-align:center;" id='productPriceShow' class="hidden"><strong>Total Amount</strong></td>
                                    <td style="text-align:right;"><strong>Total Amount</strong></td>
                                    <td style="text-align:center;" id='proTotalAmountShowFooter' class=""></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                        <div class="col-md-0"></div>
                    </div>      
                    {!! Form::close()  !!}
                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                </div>
             </div>
             <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>

<script type="text/javascript">
  
$(document).ready(function(){
    
    $("#purchaseBillNo1").change(function(){ 
                var purchaseBillNo1 = $('#purchaseBillNo1').val(); //alert(pruchaseBillNo1);
                var csrf = "<?php echo csrf_token(); ?>";
              $.ajax({
                  type: 'post',
                  url: './dataFetchFromPurchaseTable',
                  data: { _token: csrf, billNo:purchaseBillNo1},
                  dataType: 'json',   
                  success: function( data ){
                    //alert(JSON.stringify(data));

                     $.each(data, function (key, value) {
        
                            if (key == "supplierName") { 
                                $("#supplierId").empty();
                                $.each(value, function (key1, value1) { 
                                    $('#supplierId').append("<option value='"+ value1.id+"'>"+value1.supplierCompanyName+"</option>"); 
                                })
                            } 

                            if (key == "purchaseTables") { 
                                $.each(value, function (key1, value1) { 
                                    $('#purchaseDate').val(value1.purchaseDate); //alert(value1.purchaseDate);
                                    $('#remark').val(value1.remark); //alert(value1.purchaseDate);
                                    $('#discountPercent').val(value1.discountPercent); //alert(value1.discountPercent);
                                    $('#vatPercent').val(value1.vatPercent);
                                })

                            }   

                            if (key == "productNames") { 
                                $("#productId").empty();
                                $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                                $.each(value, function (key1, value1) { 
                                    $('#productId').append("<option value='"+ value1.id+"'>"+value1.name+"</option>"); 
                                })
                            } 
       
                    });
                        

                },
                error: function(data){
                  alert("error");
                }

              });/*End Ajax*/
        }); 

    $("#productId").change(function(){ 
                var productId = $('#productId').val();
                var billNo = $('#purchaseBillNo1').find(":selected").text(); //alert(billNo); 
                var csrf = "<?php echo csrf_token(); ?>";
              $.ajax({
                  type: 'post',
                  url: './getTtlValFmPurDtlsTab',
                  data: { _token: csrf, productId:productId, billNo:billNo},
                  dataType: 'json',   
                  success: function( data ){
                    //alert(JSON.stringify(data));
                    //var totalAmount = parseFloat(data.totalPrice);
                     //alert(data.price);
                     $('#productPriceAddPro').val(data.price);
                },
                error: function(data){
                  alert("error");
                }

              });/*End Ajax*/       
});

var i=0;
$('#addProduct').click(function(){
    var testx = '';

    var productId       = $('#productId').val();
    var productName     = $('#productId option:selected').text();
    var productQntty    = parseFloat($('#productQntty').val()); //alert(productQntty);
    var priceEachNewApn = $('#productPriceAddPro').val();
    var csrf = "<?php echo csrf_token(); ?>";
        i++;
    if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
    else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;} 
    else if(isNaN(priceEachNewApn) || priceEachNewApn==''){$('#priceError').removeClass('hidden'); return false;} 
         
         //purchase billNo
            var purchaseBillNo1 = $('#purchaseBillNo1').find(":selected").text();
         //end purchase billNo
         var getProductId                   = $("#productId").val();
         var getProductQty                  = $("#productQntty").val();
         var productPriceForTotalPrice      = $('#productPriceAddPro').val();
         var toShowTotalPriceInApnTable     = $("#totalAmountAddPro").val();

            $('#addProductTable tr.forhide').each(function() {
                var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
                if(cellText==getProductId){
                    var getApnProductQty = $(this).closest('tr').find('.apnQnt').val();
                    var totalQtyforsamePro = parseFloat(getApnProductQty)+parseFloat(getProductQty); 
                    $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);

                    var getApnProductAmount = $(this).closest('tr').find('.productPriceApnTable').val();
                    var totalAmountforsamePro = parseFloat(getApnProductAmount)+parseFloat(toShowTotalPriceInApnTable); 
                    $(this).closest('tr').find('.productPriceApnTable').val(totalAmountforsamePro);
                    testx = 'yes';
                }
            });

        if(testx!=='yes'){
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td><input type="number" name="productTotalPriceApnTable[]" class="form-control name_list input-sm productTotalPriceApnTable" id="productTotalPriceApnTable" style="text-align:center; cursor:default" value="'+productPriceForTotalPrice+'" readonly/></td><td><input type="number" name="productPriceApnTable[]" class="form-control name_list input-sm productPriceApnTable" id="productPriceApnTable" style="text-align:center; cursor:default"  value="'+toShowTotalPriceInApnTable+'" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><input type="text" class="hidden" name="purchaseBillNo" value="'+ purchaseBillNo1 +'"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
        }
            $('#productQntty').val('');
            $('#productId').val('');
            $('#productPriceAddPro').val('');
            $('#totalAmountAddPro').val('');
            $('#productGroupId').val('');
            $('#productCategoryId').val(''); 
            $('#productSubCategoryId').val('');

// onclick add button total amount summation  
        var sumTotal = 0;
            $(".productPriceApnTable").each(function() {
                var value = $(this).text();
                /*if(!isNaN(value) && value.length != 0) {
                sum += parseFloat(value);
                }*/
                sumTotal += Number($(this).val());
                $('#totalAmount').val(Math.ceil(sumTotal));
                //$('#amountAfterDiscount').val(Math.ceil(sumTotal));
                $('#proTotalAmountShowFooter').text(Math.ceil(sumTotal));
            });
// onclick add button quantity summation          
        var sum = 0;
            $(".apnQnt").each(function() {
                var value = $(this).text();
                /*if(!isNaN(value) && value.length != 0) {
                sum += parseFloat(value);
                }*/
                sum += Number($(this).val());
                $('#totalQuantity').val(sum);
                $('#totalQuantityFooter').text(sum);
            });

            var discountRate = parseFloat($('#discountPercent').val()); //alert(discountRate);
            var totalAmoutforDisCal = parseFloat($('#totalAmount').val()); //alert(totalAmoutforDisCal);
            var discountAmount = parseFloat(totalAmoutforDisCal*discountRate) /100; //alert(discountAmount);
            var amountAfterVat = parseFloat(totalAmoutforDisCal-discountAmount); //alert(discountAmount);
            $('#discount').val(discountAmount);
            $('#amountAfterDiscount').val(amountAfterVat);
            $('#grossTotal').val(amountAfterVat);


});//end add click function

            $("#productQntty").on("input",function (e) {
            var productQntyForTotalPrice  = $(this).val();
            var productPriceForTotalPrice = $('#productPriceAddPro').val();
                if(productPriceForTotalPrice==''){productPriceForTotalPrice=0;}
            var toShowTotalPriceInApnTable = parseFloat(productQntyForTotalPrice*productPriceForTotalPrice).toFixed(5).replace(/\.0+$/,''); 
            $("#totalAmountAddPro").val(toShowTotalPriceInApnTable);
        });

    //to remove append row row 
      $(document).on('click', '.btn_remove', function(){ 
           var removeQntty          = parseFloat($(this).closest('tr').find('.apnQnt').val()); //alert(removeQntty);
           var removeAmount         = parseFloat($(this).closest('tr').find('.productPriceApnTable').val());  
           var totalQnttyFromInput  = parseFloat($('#totalQuantity').val()); //alert(toralQnttyFromInput);
           var totalAmountFromInput = parseFloat($('#totalAmount').val()); //alert(toralAmountFromInput);
           var grossTotal           = parseFloat($('#grossTotal').val());
           var discountPercent      = parseFloat($('#discountPercent').val());
           var discount             = parseFloat($('#discount').val());

           var removeDiscount       = parseFloat(removeAmount*discountPercent/100); //alert(removeDiscount);
           var discountAfterRemove  = parseFloat(discount-removeDiscount); //alert(discountAfterRemove);
           var amountAfterRemove    = parseFloat(removeAmount-removeDiscount); //alert(amountAfterRemove);
           var grossTotalAfterRem   = parseFloat(grossTotal-amountAfterRemove); //alert(grossTotalAfterRem);
           var totalAmountAfterRemove = parseFloat(totalAmountFromInput-removeAmount);
           var totalQntfterRemove     = parseFloat(totalQnttyFromInput-removeQntty);

                $('#totalQuantity').val(totalQntfterRemove);
                $('#discount').val(discountAfterRemove);
                $('#amountAfterDiscount').val(grossTotalAfterRem);    
                $('#totalAmount').val(totalAmountAfterRemove);
                $('#grossTotal').val(grossTotalAfterRem);

                $('#totalQuantityFooter').text(totalQntfterRemove);
                $('#proTotalAmountShowFooter').text(totalAmountAfterRemove);
            
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove(); 
 
 }); //end remove button click


 $('form').submit(function( event ) {
    //alert("you are submitting" + $(this).serialize());
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addInvPurchaseReturnItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
        //alert(JSON.stringify(_response));
            if(_response=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
            if (_response.errors) {
                if (_response.errors['pruchaseBillNo1']) {
                    $('#pruchaseBillNo1e').empty();
                    $('#pruchaseBillNo1e').append('<span class="errormsg" style="color:red;">'+_response.errors.pruchaseBillNo1+'</span>');
                }
            
        }
            window.location.href = "{{url('viewInvPurchaseReturnList/')}}";
            
        },
        error: function( _response ){
            // Handle error
            alert('_response.errors');
            
        }
    });
}); 

$("input").keyup(function(){
        var productQntty = $("#productQntty").val();
        if(productQntty){$('#qnttyError').hide(); $('#productQuantitye').hide();}else{$('#qnttyError').show(); $('#productQuantitye').show();}
         var productPriceAddPro = $("#productPriceAddPro").val();
        if(productPriceAddPro){$('#priceError').hide();}else{$('#priceError').show();}
    });

$('select').on('change', function (e) {
    var pruchaseBillNo1 = $("#pruchaseBillNo1").val();
    if(pruchaseBillNo1){$('#pruchaseBillNo1e').hide();}else{$('#pruchaseBillNo1e').show();}
    var productId = $("#productId").val();
    if(productId){$('#productIdError').hide(); $('#productIde').hide();}else{$('#productIdError').show(); $('#productIde').show();}
    });      

});
</script>      

@endsection
<style>
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type=number] {
    -moz-appearance:textfield;
}


</style>
 
 

 
