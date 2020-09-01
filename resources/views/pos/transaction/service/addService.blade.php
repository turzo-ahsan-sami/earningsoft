@extends('layouts/pos_layout')
@section('title', '| Service')
@section('content')
<style type="text/css">
    .select2-results__option[aria-selected=true] {
    display: none;
}
</style>
    <div class="row add-data-form" style="padding-bottom:20px;">
        <div class="col-md-12">
            <div class="col-md-1"></div>
                <div class="col-md-10 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('pos/posViewList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Service List</a>
                    </div>
                <div class="panel panel-default panel-border">

                    <div class="panel-heading">
                        <div class="panel-title">Service</div>
                    </div>

                    <div class="panel-body">
                    {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'salesForm')) !!}

                    <div class="row">
                        <div class="col-md-6">   

                            <div class="form-group">
                                {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    
                                    {!! Form::text('billNo', $value =$maxServiceNumber, ['class' => 'form-control', 'id' => 'billNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                                     <p id='billNoe' style="max-height:3px;"></p>
                                </div> 
                            </div>

                            <div class="form-group">
                                {!! Form::label('clientCompanyId', 'Company Name:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    <?php 
                                    $posClients = array('' => 'Please Select Client Company') + DB::table('pos_client')->pluck('clientCompanyName','id')->all(); 
                                    ?>      
                                    {!! Form::select('clientCompanyId', ($posClients), null, array('class'=>'form-control', 'id' => 'clientCompanyId')) !!}
                                    <p id='clientCompanyIde' style="max-height:3px;"></p>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('serviceEmployeeId', 'Service Person:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    <?php 
                                    $salesEmployeeIds =DB::table('hr_emp_general_info')->orderBy('emp_id')
                                    ->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"),'id')->get(); 
                                    ?>  
                                    <select id="serviceEmployeeId" name="servicePerson[]" class="form-control" multiple="multiple">    
                                        @foreach ($salesEmployeeIds as $salesEmployeeId)
                                           <option  value="{{$salesEmployeeId->id}}" >{{$salesEmployeeId->name}}</option>
                                       @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('serviceDates', 'Service Date:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('serviceDates', null, ['class' => 'form-control', 'id' => 'serviceDates','readonly']) !!}
                                     <p id='salesDateese' style="max-height:3px;"></p>
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
                                        {!! Form::text('discountPercent', null, ['class' => 'form-control numeric col-sm-5', 'id' => 'discountPercent', 'placeholder'=>'%']) !!}
                                    </div>
                                    
                                    <div class="col-sm-7" style="padding-right: 0%; padding-left: 0%">
                                        {!! Form::text('discount', null, ['class' => 'form-control col-sm-7 numeric', 'id' => 'discount']) !!}
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
                                        {!! Form::text('vatPercent', null, ['class' => 'form-control numeric col-sm-5', 'id' => 'vatPercent', 'placeholder'=>'%']) !!}
                                    </div>
                                   
                                    <div class="col-sm-7" style="padding-right: 0%; padding-left: 0%">
                                        {!! Form::text('vat', null, ['class' => 'form-control col-sm-7 numeric', 'id' => 'vat', 'readonly']) !!}
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

                            <div class="form-group">
                                {!! Form::label('payAmount', 'Pay Amount:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('payAmount', null, ['class' => 'form-control numeric', 'id' => 'payAmount']) !!}
                                     <p id='payAmounte' style="max-height:3px;"></p>
                                </div> 
                            </div>

                            <div class="form-group">
                                {!! Form::label('due', 'Service Due:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('due', null, ['class' => 'form-control numeric', 'id' => 'due']) !!}
                                     <p id='duee' style="max-height:3px;"></p>
                                </div> 
                            </div>

                            <div class="form-group">
                                {!! Form::label('paymentType', 'Payment Type:', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8">
                                     <label><input type="radio" class="paymentType" id="cash" name="paymentType" value="1"> Cash </label>
                                    <label><input type="radio" class="paymentType" id="bank" name="paymentType" value="2"> Bank </label>
                                    <p id='paymentTypee' style="max-height:3px;"></p>
                                </div> 
                            </div>
                            <div id="bankStatment">
                                <div class="form-group">
                                    {!! Form::label('bankName', 'Bank Name:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('bankName', null, ['class' => 'form-control', 'id' => 'bankName']) !!}
                                         <p id='bankNamee' style="max-height:3px;"></p>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    {!! Form::label('checkNo', 'Check No:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('checkNo', null, ['class' => 'form-control', 'id' => 'checkNo']) !!}
                                         <p id='checkNoe' style="max-height:3px;"></p>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    {!! Form::label('bankDate', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text('bankDate', null, ['class' => 'form-control ', 'id' => 'bankDate']) !!}
                                         <p id='bankDatee' style="max-height:3px;"></p>
                                    </div> 
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                                <div class="col-sm-8 text-right" style="">
                                    {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                    <a href="{{url('pos/posViewList/')}}" class="btn btn-danger closeBtn">Close</a>
                                
                                </div>
                            </div>
                    </div>                  
                </div>       
                  <!-- filtering -->
                        <div class="row">
                      
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                    <?php 
                                         $groupIds =DB::table('pos_product_group')->select('name','id')->get();

                                       
                                    ?>   
                                        <select name="productGroupId" class="form-control input-sm" id="productGroupId">
                                            <option value="">Please select</option>
                                                
                                                    @foreach($groupIds as $groupId)
                                                           <option value="{{$groupId->id}}">{{$groupId->name}}</option>
                                                  
                                                    @endforeach 
                                        </select>
                                    </div>
                                </div>
                            </div> 
                            
                           <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <?php 
                                            $categoryIds =DB::table('pos_product_category')->select('name','id')->get();
                                           
                                        ?>
                                        <select name="productCategoryId" class="form-control input-sm" id="productCategoryId">
                                            <option value="">Please select</option>
                                               
                                                    @foreach($categoryIds as $categoryId)
                                                           <option value="{{$categoryId->id}}">{{$categoryId->name}}</option>
                                                    @endforeach
                                               
                                        </select>
                                    </div>
                                </div>
                            </div>
            
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('productSubCategoryId', 'Subcatagory:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <?php 
                                            $subCategoryIds =DB::table('pos_product_sub_category')->select('name','id')->get();
                                           
                                        ?>
                                        <select name="productSubCategoryId" class="form-control input-sm" id="productSubCategoryId">
                                            <option value="">Please select</option>
                                                    @foreach($subCategoryIds as $subCategoryId)
                                                           <option value="{{$subCategoryId->id}}">{{$subCategoryId->name}}</option>
                                                    @endforeach
                                        </select>                                    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 hidden">
                                <div class="form-group">
                                    {!! Form::label('productBrandId', 'Brand:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                        <?php 
                                        $brandIds =  DB::table('pos_product')->select('brandId')->get();
                                        $bndArSize = count($brandIds);
                                        if($bndArSize){
                                            foreach($brandIds as $brandId){
                                                $brandName [] =  DB::table('pos_product_brand')->select('name','id')->where('id',$brandId->brandId)->first();   
                                            }
                                            $brandNames = array_map("unserialize", array_unique(array_map("serialize", $brandName)));
                                        }
                                    ?>
                                        <select name="productBrandId" class="form-control input-sm" id="productBrandId">
                                            <option value="">Please select</option>
                                                <?php if($bndArSize>0): ?>
                                                    @foreach($brandNames as $brandName)
                                                    <option value="{{$brandName->id}}">{{$brandName->name}}</option>
                                                    @endforeach
                                                <?php endif ; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>    
                <!-- filtering end-->

                    <div class="row">
                        <div class="col-md-12">
                            <table id="addProductTable" class="table table-bordered responsive addProductTable">
                                <thead>
                                    <tr class="">
                                        <th style="text-align:center;" class="col-sm-3">Item Name</th>
                                        <th style="text-align:center;" class="col-sm-2">B.Name</th>
                                        <th style="text-align:center;" class="col-sm-2">S.Month</th>
                                        <th style="text-align:center;" class="col-sm-1">Qty</th>
                                        <th style="text-align:center;" class="col-sm-2">Price</th>
                                        <th style="text-align:center;" class="col-sm-2">Total</th>
                                        <th style="text-align:center;" class="col-sm-1">Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                         
                                        <td>
                                            <?php 
                                                $productId = array('' => 'Please Select product') + DB::table('pos_product')->pluck('name','id')->all(); 
                                            ?>
                                            {!! Form::select('productId', ($productId), null, array('class'=>'form-control input-sm', 'id' => 'productId')) !!}
                                        </td>
                                        <td>
                                            <select name="optBranchName" id="optBranchName" class="form-control input-sm text-center">
                                                <option value="">Select Branch</option>
                                                <option value="1">Head Office</option>
                                                <option value="2">Branch</option>
                                            </select>
                                        </td>

                                        <td>
                                                   {!! Form::text('serviceDate', null, ['class' => 'form-control', 'id' => 'serviceDate','readonly']) !!}
                                                   <p id='serviceDatee' style="max-height:3px;"></p>
                                              
                                        </td>
                                       
                                        <td class="">
                                            <input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='' placeholder='Insert Item' min="1"/>
                                        </td>
                                        <td class="">
                                            <input type='number' class="form-control input-sm text-center" id='productPriceAddPro' name='productPriceApn[]' value='' placeholder='Enter product price' min="1"/>
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
                                            <p class="hidden" id="branchError" style="color: red;">Product Qty Is Required</p>
                                            <p class="hidden" id="BranchName" style="color: red;"></p>
                                        </td>
                                        <td></td>
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
                                        <td style="text-align:right;"></td>
                                        <td style="text-align:right;"></td>
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
$("#bankStatment").hide();
$(".paymentType").click(function(event) {
   var bankStatmentValue = $('input[name=paymentType]:checked').val();
   if(bankStatmentValue==2){
    $("#bankStatment").show();
   } else {
    $("#bankStatment").hide();
   }
});
 $(document).ready(function(){ 

$('#serviceEmployeeId').select2(); 
$("#serviceEmployeeId").on("select2:select", function (evt) {
    var element = evt.params.data.element;
    var $element = $(element);
    $element.detach();
    $(this).append($element);
    $(this).trigger("change");
});



   $("#clientCompanyId").change(function(){
            var clientCompanyId = $(this).val();
            
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './posClientsProductsAssignChange',
                data: {clientCompanyId:clientCompanyId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function(data){
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">All</option>');
                   // alert(JSON.stringify(data['salesPersonArr']));
                    var servicePersonId = data['servicePersonArr'];// Data Array
                    $('#serviceEmployeeId').val(servicePersonId);

                    var newOption = new Option(servicePersonId.text, servicePersonId.id, true, true);

                    $('#serviceEmployeeId').append(newOption).trigger('change');
                    $("#serviceEmployeeId option[selected]").remove();

                    $.each(data['productName'], function (key, productObj) {  
                                      
                      $('#productId').append("<option value='"+ productObj.id+"'>"+productObj.name+"</option>");
                        
                    });
                
                },
                error: function(_response){
                    alert("error");
                }

        });/*End Ajax*/

    });/*End Change Product*/

$(".numeric").keypress(function (e) {
            
     $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                $("#numericError").html("Digits Only").show().fadeOut("slow");
                event.preventDefault(); 
            }
        });

$( "#serviceDates" ).datepicker({
      dateFormat: "yy-mm-dd",  
      changeMonth: true,
      changeYear: true,
      maxDate: "0"
    });
$("#serviceDates").datepicker().datepicker("setDate", new Date());
$("#serviceDate").datepicker({
       dateFormat: 'MM yy',
       changeMonth: true,
       changeYear: true,
       showButtonPanel: true,

       onClose: function(dateText, inst) {
           var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
           var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
           $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
       }
   });
 $("#serviceDate").focus(function () {
       $(".ui-datepicker-calendar").hide();
       //$(".ui-datepicker-current").hide();
       $("#ui-datepicker-div").position({
           my: "center top",
           at: "center bottom",
           of: $(this)
       });
   });

$( "#chalanDate,#bankDate" ).datepicker({
      dateFormat: "yy-mm-dd",  
      changeMonth: true,
      changeYear: true,
      maxDate: "0"
    });
$("#chalanDate").datepicker().datepicker("setDate", new Date());


var i=0;
$('#addProduct').click(function(){
    var testx = '';

    var productId        = $('#productId').val();
    var productName      = $('#productId option:selected').text();
    //var optServiceDate   = $('#serviceDate option:selected').text();
    var optServiceDate = $('#serviceDate').val();
    var optBranchName    = $('#optBranchName option:selected').text();
    var optBranchId      = $('#optBranchName').val();
    //alert(optBranchId);
    var productQntty    = parseFloat($('#productQntty').val()); //alert(productQntty);
    var priceEachNewApn = $('#productPriceAddPro').val();
    var csrf = "<?php echo csrf_token(); ?>";
        i++;
    if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
    else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;} 
    else if(isNaN(priceEachNewApn) || priceEachNewApn==''){$('#priceError').removeClass('hidden'); return false;} 
         
         var getProductId                   = $("#productId").val();
         var getServiceDate                 = $("#serviceDate").val();
         var getBranchId                    = $("#optBranchName").val();
         var getProductQty                  = $("#productQntty").val();
         var productPriceForTotalPrice      = $('#productPriceAddPro').val();
         var toShowTotalPriceInApnTable     = $("#totalAmountAddPro").val();

            $('#addProductTable tr.forhide').each(function() {
                var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
                var branchText = $(this).closest('tr').find('.optBranchIdclass').val();
                var serviceDateText = $(this).closest('tr').find('.optServiceIdclass').val();
                if(cellText==getProductId && branchText==getBranchId && serviceDateText==getServiceDate){
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
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="text" name="optBranchName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+optBranchName+'" readonly/></td><td><input type="text" name="optServiceDate[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+optServiceDate+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td><input type="number" name="productTotalPriceApnTable[]" class="form-control name_list input-sm productTotalPriceApnTable" id="productTotalPriceApnTable" style="text-align:center; cursor:default" value="'+productPriceForTotalPrice+'" readonly/></td><td><input type="number" name="productPriceApnTable[]" class="form-control name_list input-sm productPriceApnTable" id="productPriceApnTable" style="text-align:center; cursor:default"  value="'+toShowTotalPriceInApnTable+'" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><input type="text" name="productId6[]" class="form-control input-sm name_list hidden optBranchIdclass" style="text-align:center; cursor:default" value="'+optBranchId+'" id="productId6"/><input type="text" name="productId7[]" class="form-control input-sm name_list hidden optServiceDateIdclass" style="text-align:center; cursor:default" value="'+optServiceDate+'" id="productId7"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
        }
            $('#productQntty').val('');
            $('#productId').val('');
            $('#optBranchName').val('');
            $('#serviceDate').val('');
            $('#productPriceAddPro').val('');
            $('#totalAmountAddPro').val('');
            $('#productGroupId').val('');
            $('#productCategoryId').val(''); 
            $('#productSubCategoryId').val('');

// onclick add button total amount summation  
        var sumTotal = 0;
            $(".productPriceApnTable").each(function() {
                var value = $(this).text();
                sumTotal += Number($(this).val());
                $('#totalAmount').val(Math.ceil(sumTotal));
                $('#amountAfterDiscount').val(Math.ceil(sumTotal));
                $('#proTotalAmountShowFooter').text(Math.ceil(sumTotal));
            });
// onclick add button quantity summation          
        var sum = 0;
            $(".apnQnt").each(function() {
                var value = $(this).text();
                sum += Number($(this).val());
                $('#totalQuantity').val(sum);
                $('#totalQuantityFooter').text(sum);
            });
//dinamicaly set value to get alert or something            
$("#totalAmount").trigger('input');
$("#amountAfterDiscount").trigger('input');

});
$('#serviceDate option:selected').text() 

// calculation of tatal price per row
        $("#productQntty").on("input",function (e) {
            var productQntyForTotalPrice  = $(this).val();
            var productPriceForTotalPrice = $('#productPriceAddPro').val();
                if(productPriceForTotalPrice==''){productPriceForTotalPrice=0;}
            var toShowTotalPriceInApnTable = parseFloat(productQntyForTotalPrice*productPriceForTotalPrice).toFixed(5).replace(/\.0+$/,''); 
            $("#totalAmountAddPro").val(toShowTotalPriceInApnTable);
        });
// calculation of tatal price per row
        $("#productPriceAddPro").on("input",function (e) {
            var productQntyForTotalPrice  = $('#productQntty').val();
            var productPriceForTotalPrice = $('#productPriceAddPro').val();
                if(productQntyForTotalPrice==''){productQntyForTotalPrice=0;}
            var toShowTotalPriceInApnTable = parseFloat(productQntyForTotalPrice*productPriceForTotalPrice).toFixed(5).replace(/\.0+$/,''); 
            $("#totalAmountAddPro").val(toShowTotalPriceInApnTable);
        });
//to remove append row row 
      $(document).on('click', '.btn_remove', function(){ 
           var removeQntty          = parseFloat($(this).closest('tr').find('.apnQnt').val()); //alert(removeQntty);
           var removeAmount         = parseFloat($(this).closest('tr').find('.productPriceApnTable').val());  
           var toralQnttyFromInput  = parseFloat($('#totalQuantity').val()); //alert(toralQnttyFromInput);
           var toralAmountFromInput = parseFloat($('#totalAmount').val()); //alert(toralAmountFromInput);
           var qntAfterRemove       = parseFloat(toralQnttyFromInput-removeQntty); //alert(qntAfterRemove);
           var amountAfterRemove    = parseFloat(toralAmountFromInput-removeAmount); //alert(amountAfterRemove);

           var totalVat                     = parseFloat($('#vatPercent').val()); //alert(totalVat);
           var amountAfterDiscountForGross  = parseFloat($("#amountAfterDiscount").val()); //alert(amountAfterDiscountForGross);
           var grossTotalForRemove          = parseFloat($("#grossTotal").val()); //alert(grossTotalForRemove);

           $('#totalQuantity').val(qntAfterRemove);
           $('#totalQuantityFooter').text(qntAfterRemove);
           if(amountAfterRemove<0){
              $('#totalAmount').val(0);
              $('#proTotalAmountShowFooter').text('');
           }else{$('#totalAmount').val(Math.ceil(amountAfterRemove));
                   $('#proTotalAmountShowFooter').text(Math.ceil(amountAfterRemove));
                }

           // Discount Calcualtion after remove row
                var percent = parseFloat($('#discountPercent').val()); //alert(percent);
                var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
                var result = parseFloat(Math.ceil(tAmount*percent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(result);
                var totalAfterDiscount = parseFloat(Math.round(tAmount-result)).toFixed(5).replace(/\.0+$/,''); //alert(result);
                //var result = parseFloat(parseInt($("#totalAmount").val(), 10) * percent)/ 100;
                if(isNaN(result)){ $('#discount').val(''); $('#amountAfterDiscount').val(tAmount);}
                else{$('#discount').val(result||''); $('#amountAfterDiscount').val(totalAfterDiscount||'');}

            //VAT calculation after remove row
            var vatPercent            = parseFloat($('#vatPercent').val()); //alert(vatPercent);
            var amountForVat          = parseFloat($("#totalAmount").val()); //alert(amountForVat);
            var afterDiscountForVat   = parseFloat($("#amountAfterDiscount").val()); //alert(afterDiscountForVat);
                    if(afterDiscountForVat==0){
                        var varBeforeDisc = parseFloat(Math.round(amountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                            if(isNaN(varBeforeDisc)){$('#vat').val('');}
                            else{$('#vat').val(varBeforeDisc||'');}   
                    }
                    else if(afterDiscountForVat==0 && amountForVat){
                        var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                            if(isNaN(varBeforeDisc)){$('#vat').val('');}
                            else{$('#vat').val(varBeforeDisc||'');} 
                    }

                    else if(afterDiscountForVat && amountForVat){
                        var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                            if(isNaN(varBeforeDisc)){$('#vat').val('');}
                            else{$('#vat').val(varBeforeDisc||'');} 
                    } 
                    else if(amountForVat==0){$('#vat').val('');} 

                    if(isNaN(totalVat) && isNaN(amountAfterDiscountForGross)){
                        var grossTotalAfterRemove = parseFloat(toralAmountFromInput-removeAmount);
                        $('#grossTotal').val(grossTotalAfterRemove||'');
                        $('#payAmount').val(grossTotalAfterRemove||'');
                    }
                    else if(amountAfterDiscountForGross>0 && isNaN(totalVat)){
                        var grossTotalAfterRemove = parseFloat(Math.round(removeAmount*percent) / 100); //alert(toralQnttyFromInput);
                        var totalRemoveAmount     = parseFloat(removeAmount-grossTotalAfterRemove); //alert(totalRemoveAmount);
                        var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-totalRemoveAmount));
                        if(grossTotalAfterRemoveShow==''){$('#grossTotal').val(0); $('#payAmount').val(0);}
                        else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); $('#payAmount').val(grossTotalAfterRemoveShow||'');}
                        /*$('#grossTotal').val(grossTotalAfterRemoveShow||'');*/
                    }
                    else if(isNaN(amountAfterDiscountForGross) && totalVat>0){
                        var vatAfterRemove    = parseFloat(Math.round(removeAmount*vatPercent) / 100); //alert(grossTotalAftervat);
                        var totalAmountWithVat = parseFloat(Math.ceil(removeAmount+vatAfterRemove)); //alert(totalAmountWithVat);
                        var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-totalAmountWithVat)); 
                        if(grossTotalAfterRemoveShow <0){$('#grossTotal').val(0); $('#payAmount').val(0);}
                        else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); $('#payAmount').val(grossTotalAfterRemoveShow||'');}
                        /*$('#grossTotal').val(grossTotalAfterRemoveShow||'');*/
                    }
                    else if(amountAfterDiscountForGross && totalVat){
                        var grossTotalAfterRemove = parseFloat(Math.round(removeAmount*percent) / 100); 
                            if(isNaN(grossTotalAfterRemove)){grossTotalAfterRemove=0}
                        var totalRemoveAmount     = parseFloat(removeAmount-grossTotalAfterRemove); //alert(totalRemoveAmount);
                        var vatAfterRemove        = parseFloat(Math.round(totalRemoveAmount*vatPercent) / 100); //alert(grossTotalAftervat);
                        var amountToRemove        = parseFloat(Math.ceil(totalRemoveAmount+vatAfterRemove)); 
                        var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-amountToRemove)); //alert(grossTotalAfterRemoveShow);
                        if(grossTotalAfterRemoveShow=='' || grossTotalAfterRemoveShow<0){$('#grossTotal').val(0); $('#payAmount').val(0);}
                        else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); $('#payAmount').val(grossTotalAfterRemoveShow||''); }
                    }
            $('#due').val(0);
            if(tAmount==0){$('#discountPercent').val(0); $('#vatPercent').val(0);}
            
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove(); 
 
 }); //end remove button click
     
// Discount Calcualtion
$("#discountPercent, #totalAmount").on("input",function(e) { // input on change
    var percent = parseFloat($('#discountPercent').val()); //alert(percent);
    var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
    var result = parseFloat(Math.ceil(tAmount*percent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(result);
    var totalAfterDiscount = parseFloat(Math.round(tAmount-result)).toFixed(5).replace(/\.0+$/,''); //alert(totalAfterDiscount);
    if(isNaN(result)){ $('#discount').val(''); $('#amountAfterDiscount').val(tAmount||'');}
    else{$('#discount').val(result||''); $('#amountAfterDiscount').val(totalAfterDiscount||''); }
    $('#due').val(0);
  })

//Discount percentage calculation
$("#discount").on("input",function(e) {
    var discountPer = parseFloat($("#discountPercent").val()); //alert(discountPer);
    var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
    var discountAmount = parseFloat($("#discount").val()); //alert(discountAmount);
    var discountRate = parseFloat(Math.round(100*discountAmount ) /tAmount);
        
    var totalAfterDiscount = parseFloat(Math.round(tAmount-discountAmount)).toFixed(5).replace(/\.0+$/,''); //alert(totalAfterDiscount);
    if(isNaN(totalAfterDiscount) || totalAfterDiscount==''){$('#amountAfterDiscount').val(tAmount||''); $('#discountPercent').val('');}
        else{ 
        $('#discountPercent').val(discountRate);
        $('#amountAfterDiscount').val(totalAfterDiscount); 
        } 
})

// Calculation VAT Rate
$("#vatPercent, #totalAmount, #amountAfterDiscount, #discountPercent, #discount").on("input",function(e) { // input on change
    var vatPercent            = parseFloat($('#vatPercent').val()); //alert(vatPercent);
    var amountForVat          = parseFloat($("#totalAmount").val()); //alert(amountForVat);
    var afterDiscountForVat   = parseFloat($("#amountAfterDiscount").val()); //alert(afterDiscountForVat);
            if(isNaN(afterDiscountForVat)){
                var varBeforeDisc = parseFloat(Math.round(amountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                    if(isNaN(varBeforeDisc)){$('#vat').val('');}
                    else{$('#vat').val(varBeforeDisc||'');}   
            }
            else if(isNaN(afterDiscountForVat) && amountForVat){
                var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                    if(isNaN(varBeforeDisc)){$('#vat').val('');}
                    else{$('#vat').val(varBeforeDisc||'');} 
            }

            else if(afterDiscountForVat && amountForVat){
                var varBeforeDisc = parseFloat(Math.round(afterDiscountForVat*vatPercent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(varBeforeDisc);
                    if(isNaN(varBeforeDisc)){$('#vat').val('');}
                    else{$('#vat').val(varBeforeDisc||'');} 
            }

        $('#due').val(0);

  })

//Gross total calculation
$("#vatPercent, #totalAmount, #amountAfterDiscount, #discountPercent, #discount").on("input",function(e) { // input on change
    var totalVat                        = parseFloat($('#vat').val()); //alert(totalVat);
    var amountForGrosstotal             = parseFloat($("#totalAmount").val()); //alert(amountForGrosstotal);
    var amountAfterDiscountForGross     = parseFloat($("#amountAfterDiscount").val()); //alert(amountAfterDiscountForGross);
        if(isNaN(totalVat) && isNaN(amountAfterDiscountForGross)){
            $('#grossTotal').val(amountForGrosstotal||'');
            $('#due').val(amountForGrosstotal||'');
        }
        else if(amountAfterDiscountForGross && isNaN(totalVat)){
            $('#grossTotal').val(amountAfterDiscountForGross||'');
            $('#due').val(amountAfterDiscountForGross||'');
        }
        else if(isNaN(amountAfterDiscountForGross) && totalVat){
            var grossTotalAmount = parseFloat(Math.ceil(amountForGrosstotal+totalVat));
            $('#grossTotal').val(grossTotalAmount||'');
            $('#due').val(grossTotalAmount||'');
        }
        else if(amountAfterDiscountForGross && totalVat){
            var grossTotalAmount = parseFloat(Math.ceil(amountAfterDiscountForGross+totalVat));
            //Math.ceil(grossTotalAmount);
            $('#grossTotal').val(grossTotalAmount);
            $('#due').val(grossTotalAmount||'');
        }
        else if(amountAfterDiscountForGross<1 && isNaN(totalVat)){
           $('#grossTotal').val(amountAfterDiscountForGross||'');
           $('#due').val(amountAfterDiscountForGross||'');
        }
        else if(amountAfterDiscountForGross<1 && totalVat){
           $('#grossTotal').val(amountAfterDiscountForGross||'');
           $('#due').val(amountAfterDiscountForGross||'');
           $('#vat').val('');
        }
        $('#payAmount').val(0);    
  })

//Pay amount/due amount calculation
    $("#payAmount, #due").on("input",function(e) { 

        var grossTotalForDue = parseFloat($("#grossTotal").val());
        var payAmount = parseFloat($("#payAmount").val());
        if(isNaN(payAmount)){$('#due').val(grossTotalForDue);}
       else if(isNaN(grossTotalForDue)){
            grossTotalForDue = 0;
        }else{
            var dueAmount = grossTotalForDue-payAmount;
            if(payAmount>grossTotalForDue){
                    //$('#payAmounte').text('Pay Amount Should not be more than gross total');
                    $("#payAmounte").html("Pay Amount Should not be more than gross total").show().fadeOut("slow");
                    $("#payAmounte").css("color", "red");
                    event.preventDefault();
            }
            else if(isNaN(dueAmount)){$('#due').val(0);}
            else{$('#due').val(dueAmount);}
        }
    })  
        
$('form').submit(function( event ) {
    var form = $('form').serialize();
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addPosServiceItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
             if(_response=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}

            if (_response.errors) {
                 //alert(JSON.stringify(_response.errors));
                if (_response.errors['paymentType']) {
                    $('#paymentTypee').empty();
                    $('#paymentTypee').append('<span class="errormsg" style="color:red;">'+_response.errors.paymentType+'</span>');
                }
                
        }
        var salesId = _response.salesId;
        if(!_response.errors){window.location.href = "{{url('pos/serviceInvoicePrint/').'/'}}"+salesId;}
            
        },
        error: function( _response ){
            
            alert('_response.errors');
            
        }
    });
});

$("#salesForm").mouseover(function(){
        var rows= $('#addProductTable tbody tr.forhide').length;
        if(rows>0){
            $("#supplierId").prop('disabled', true);
        }else{$("#supplierId").prop('disabled', false);}
    });  

        
    $("input").keyup(function(){
        var productQntty = $("#productQntty").val();
        if(productQntty){$('#qnttyError').hide(); $('#productQuantitye').hide();}else{$('#qnttyError').show(); $('#productQuantitye').show();}
         var productPriceAddPro = $("#productPriceAddPro").val();
        if(productPriceAddPro){$('#priceError').hide();}else{$('#priceError').show();}
    });

    $('select').on('change', function (e) {
    var supplierId = $("#supplierId").val();
    if(supplierId){$('#supplierIde').hide();}else{$('#supplierIde').show();}

    var productId = $("#productId").val();
    if(productId){$('#productIdError').hide(); $('#productIde').hide();}else{$('#productIdError').show(); $('#productIde').show();}
    
    });
});

</script>


<script type="text/javascript">
    $(document).ready(function(){ 
        $("#optBranchName").change(function(){

            var branchId = $(this).val();
            var productId    = $("#productId").val();
            var csrf = "<?php echo csrf_token(); ?>";

               $.ajax({
                    type: 'post',
                    url: './posProductChange',
                    data: {branchId:branchId,productId:productId,_token: csrf},
                    dataType: 'json',
                    success: function( data ) {
                        $("#productPriceAddPro").val(data['productPrice']);
                       
                     },
                     error: function(_response) {
                        alert("error");
                    }

               });/*End Ajax*/

            });/*End Change Project*/
    });
</script>

<script type="text/javascript">
    
    /* Group Category*/
     jQuery(document).ready(function($) {
         $("#productGroupId").change(function(){
            var productGroupId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './posServiceGroupChange',
                data: {productGroupId:productGroupId,_token: csrf},
                dataType: 'json',
                success: function( data ) {

                    $("#productCategoryId").empty();
                    $("#productCategoryId").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['categories'], function (key, productObj) {                       
                    $('#productCategoryId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });


                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['subCategories'], function (key, productObj) {                       
                    $('#productSubCategoryId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });

                   $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">All</option>');
                   

                    $.each(data['product'], function (key, productObj) {                       
                            
                                
                            $('#productId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/
     });

</script>

 <!--  Change Category -->
      <script type="text/javascript">
      $(document).ready(function(){
        $("#productCategoryId").change(function(){
            var productCategoryId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './posServiceCategoryChange',
                data: {productCategoryId:productCategoryId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){
                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['subCategories'], function (key, productObj) {                       
                    $('#productSubCategoryId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });

                  $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['product'], function (key, productObj) {                       
                    $('#productId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });

                  
                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/

        });
     </script> 

     <!--  Change Sub Category -->
      <script type="text/javascript">
      $(document).ready(function(){
        $("#productSubCategoryId").change(function(){
            var productSubCategoryId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './posServiceSubCategoryChange',
                data: {productSubCategoryId:productSubCategoryId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function( data ){
                   

                  $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">All</option>');

                 $.each(data['product'], function (key, productObj) {                       
                    $('#productId').append("<option value='"+ productObj.id+"'>"+(productObj.name)+"</option>");
                        
                    });

                  
                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Category*/

        });
     </script>



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
@endsection 
 

 
