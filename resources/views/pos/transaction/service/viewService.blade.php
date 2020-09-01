@extends('layouts/pos_layout')
@section('title', '| Service')
@section('content')
@include('successMsg')

<!-- <?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;
?> -->
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('pos/addPosServiceRequiF/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Service</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">SERVICE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
         
        </div>
          <table class="table table-striped table-bordered" id="posSalesView" style="color:black;">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Company Name</th>
                <th>Totlal Quantity</th>
                <th>Gross Totlal</th>
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody>
               <?php $no=0; ?>
                  @foreach($posSales as $posSales)
                    @if($posSales->salesType==2)
                    <tr class="item{{$posSales->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{date('d-m-Y', strtotime($posSales->salesDate))}}</td>
                      <td style="text-align: left; padding-left: 5px;">{{'SB000'.$posSales->salesBillNo}}</td>
                      <td style="text-align: left; padding-left: 5px;">
                      <?php
                            $companyName = DB::table('pos_client')->select('clientCompanyName')->where('id',$posSales->companyId)->first();


                          ?>
                      {{$companyName->clientCompanyName}}</td>
                     
                  
                      <td style="text-align: center;">{{$posSales->totalSalesQuantity}}</td>
                      <td style="text-align: right; padding-right: 5px;">{{number_format($posSales->totalSaleGrossAmount,2)}}</td>
                  
                      <td class="text-center" width="80">
                        <a href="{{url('pos/serviceInvoicePrint/').'/'.$posSales->id}}" class="princtSalesInvoiceId" data-token="{{csrf_token()}}" data-id="{{$posSales->id}}">
                              <span class="fa fa-envelope"></span>
                          </a>&nbsp
                        <a href="javascript:;" class="forPurchaseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$posSales->id}}">
                            <span class="fa fa-eye"></span>
                        </a>&nbsp

                       
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$posSales->id}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp

                       
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$posSales->id}}">
                          <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </td>
                  </tr>
                  @endif
                  @endforeach
            </tbody>
           
          </table>
        </div>
      </div>
  </div>
  </div>
</div>
</div>

<div id="myModal" class="modal fade" style="margin-top:3%">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title" style="clear:both"></h4>
      </div>
      <div class="modal-body">
       {!! Form::open(array('url' => '', 'role' => 'form',  'class'=>'form-horizontal form-groups', 'id' => 'purchaseForm')) !!}

                     <div class="row">
                     <div class="col-md-6">   
                        <div class="form-group hidden">
                              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text']) !!}
                              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
                          </div>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('billNo', 'Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                            @php
                                
                                 $maxServiceNumber = 'SS'.str_pad(6,'0',STR_PAD_LEFT);

                            @endphp
                            <div class="col-sm-8">
                                {!! Form::text('billNo',null, ['class' => 'form-control', 'id' => 'billNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                                 <p id='billNoe' style="max-height:3px;"></p>
                            </div> 
                        </div>

                         <div class="form-group">
                            {!! Form::label('CompanyName', 'Company Name:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                               
                                    $companyName  =  array('' => 'Please Select') +DB::table('pos_client')->pluck('clientCompanyName','id')->all();
                                   
                                ?> 
                                {!! Form::select('CompanyName', ($companyName), null, array('class'=>'form-control', 'id' => 'CompanyName')) !!} 
                                <p id='CompanyNamee' style="max-height:3px;"></p>
                            </div>
                               
                        </div>
                        <div class="form-group">
                            {!! Form::label('salesEmployeeId', 'Sales Person:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                $salesEmployeeIds =DB::table('hr_emp_general_info')->orderBy('emp_id')
                                ->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"),'id')->get(); 
                                ?>  
                                <select id="salesEmployeeId" name="salesPerson[]" class="form-control" multiple="multiple">    
                                    @foreach ($salesEmployeeIds as $salesEmployeeId)
                                       <option  value="{{$salesEmployeeId->id}}" >{{$salesEmployeeId->name}}</option>
                                   @endforeach
                               </select>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('salesDate', 'Service Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('salesDate', null, ['class' => 'form-control', 'id' => 'salesDate','readonly']) !!}
                                 <p id='salesDatee' style="max-height:3px;"></p>
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
                            </div> 
                        </div>
                        <div id="bankStatment" style="display:none;" >
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
                                    {!! Form::text('bankDate', null, ['class' => 'form-control ', 'id' => 'bankDate','readonly','autocomplete'=>'off']) !!}
                                     <p id='bankDatee' style="max-height:3px;"></p>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group hidden">
                            {!! Form::label('createdDate', 'createdDate:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('createdDate', null, ['class' => 'form-control', 'id' => 'createdDate']) !!}
                                 <p id='createdDatee' style="max-height:3px;"></p>
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
                                        $categoryId =DB::table('pos_product_category')->select('name','id')->get();
                                    ?>
                                    <select name="productCategoryId" class="form-control input-sm" id="productCategoryId">
                                        <option value="">Please select</option>
                                                @foreach($categoryId as $categoryId)
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
                                       $subCategoryIds =DB::table('pos_product_category')->select('name','id')->get();
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
                                        $brandIds =  DB::table('inv_product')->select('brandId')->get();
                                        $bndArSize = count($brandIds);
                                        if($bndArSize){
                                            foreach($brandIds as $brandId){
                                                $brandName [] =  DB::table('inv_product_brand')->select('name','id')->where('id',$brandId->brandId)->first();   
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
                            <table id="addProductTable" class="table table-bordered responsive addProductTable" style="color:black;">
                                <thead>
                                    <tr class="">
                                        <th style="text-align:center;" class="col-sm-3">Item Name</th>
                                        <th style="text-align:center;" class="col-sm-3">Branch Name</th>
                                        <th style="text-align:center;" class="col-sm-3">S.Month</th>
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
                                               
                                               $productIds =DB::table('pos_product')->select('name','id')->get();
                                               
                                            ?>
                                             <select name="productId" class="form-control input-sm" id="productId">
                                                <option value="">Please select product</option>
                                                        @foreach($productIds as $productId)
                                                               <option value="{{$productId->id}}">{{$productId->name}}</option>
                                                        @endforeach
                                                   
                                            </select>
                                           
                                        </td>

                                        <td>
                                             <select name="branchId" class="form-control input-sm" id="branchId1">
                                                <option value="">Please select Branch</option>
                                                <option value="1">Head Office</option>
                                                <option value="2">Branch</option>                                           
                                             </select>
                                           
                                        </td>
                                        <td >
                                              {!! Form::text('serviceDate', null, ['class' => 'form-control', 'id' => 'serviceDate','readonly' ]) !!}
                                                   <p id='serviceDatee' style="max-height:3px;"></p>
                                           
                                        </td>
                                        <td class="">
                                            <input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='' placeholder='Insert Item' min="1"/>
                                        </td>
                                       
                                        <td class="">
                                            <input type='number' class="form-control input-sm text-center" id='productPriceAddPro' name='productPriceApn[]' value='' placeholder='Enter product price' min="1"/>
                                        </td>
                                        <td class="">
                                            <input type='number' class="form-control input-sm text-center" id='totalAmountAddPro' name='totalAmountApn[]' value='' placeholder='' min="1" readonly="readonly" />
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
                                          
                                           

                                        </td>
                                        <td>
                                            <p class="hidden" id="qnttyError" style="color: red;">Product Qty Is Required</p>
                                            <p class="hidden" id="productQuantitye" style="color: red;"></p>
                                        </td>
                                        <td class=""></td>
                                        <td class=""></td>
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
                    <div class="deleteContent" style="padding-bottom:20px;">
                        <h4>You are about to delete this item this procedure is irreversible !</h4>
                        <h4>Do you want to proceed ?</h4> 
                        <span class="hidden id"></span>
                    </div>
                    <div class="modal-footer">
                        <p id="MSGE" class="pull-left" style="color:red"></p>
                        <p id="MSGS" class="pull-left" style="color:green"></p>
                        {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'id' => 'footer_action_button'] ) !!}
                        {!! Form::button('<span id=""></span>', ['class' => 'btn actionBtn', 'data-dismiss' => 'modal', 'id' => 'footer_action_button2'] ) !!}

                        {!! Form::button('<span id=""> Close</span>',['class' => 'btn btn-warning', 'data-dismiss' => 'modal' , 'id' => 'footer_action_button_dismis'] ) !!}
                    </div>
                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                </div>
               </div>
             
            </div>
        <div class="col-md-1"></div>
    </div>



@include('pos/transaction/service/serviceDetails')
@include('dataTableScript')


<script type="text/javascript">

    $(".paymentType").click(function(event) {
        var bankStatmentValue = $('input[name=paymentType]:checked').val();
        if(bankStatmentValue==2){
            $("#bankStatment").show();
        } else {
            $("#bankStatment").hide();
        }
    });
 
    $( document ).ready(function() {
        $('#salesEmployeeId').select2();
        $('#salesEmployeeId').next('span').css('width', '100%');;
        $("#salesEmployeeId").on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    });
  
    $(".numeric").keypress(function (e) {
        $(this).val($(this).val().replace(/[^0-9\.]/g,''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            $("#numericError").html("Digits Only").show().fadeOut("slow");
            event.preventDefault(); 
        }
    });

    $( "#salesDate" ).datepicker({
        dateFormat: "yy-mm-dd",  
        changeMonth: true,
        changeYear: true,
        maxDate: "0"
    });

    $( "#bankDate" ).datepicker({
        dateFormat: "yy-mm-dd",  
        changeMonth: true,
        changeYear: true,
        maxDate: "0"
    });

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
        $("#ui-datepicker-div").position({
           my: "center top",
           at: "center bottom",
           of: $(this)
       });
   });


    function num(argument){

      return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

     
   $("input").keyup(function(){
        var productQntty = $("#productQntty").val();
        if(productQntty){$('#qnttyError').hide(); $('#productQuantitye').hide();}else{$('#qnttyError').show(); $('#productQuantitye').show();}
         
    });

    $('select').on('change', function (e) {
      var productId = $("#productId").val();
      if(productId){$('#productIdError').hide(); $('#productIde').hide();}else{$('#productIdError').show(); $('#productIde').show();}
    
    });

    var i=0;
    $('#addProduct').click(function(){
        var testx = '';

        var productId       = $('#productId').val();
        var serviceDate     = $('#serviceDate').val();
        var productName     = $('#productId option:selected').text();
        var branchId        = $('#branchId1 option:selected').val();
        var optBranchName   = $('#branchId1 option:selected').text();
       
        var productQntty    = parseFloat($('#productQntty').val()); //alert(productQntty);
        var csrf = "<?php echo csrf_token(); ?>";
            i++;
        if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
        
        else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;} 
         
        var getProductId                   = $("#productId").val();
        var getbranchId                    = $("#branchId1").val();
        var getServiceDate                 = $("#serviceDate").val();
        var getProductQty                  = $("#productQntty").val();
        var productPriceForTotalPrice      = $('#productPriceAddPro').val();
        var toShowTotalPriceInApnTable     = $("#totalAmountAddPro").val();

        $('#addProductTable tr.forhide').each(function() {
            var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
            var branchText = $(this).closest('tr').find('.branchId').val();
            if(cellText==getProductId && branchText==getbranchId){
                var getApnProductQty = $(this).closest('tr').find('.apnQnt').val();
                var totalQtyforsamePro = parseFloat(getApnProductQty)+parseFloat(getProductQty);
                alert(totalQtyforsamePro); 
                $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);

                var getApnProductAmount = $(this).closest('tr').find('.productPriceApnTable').val();
                var totalAmountforsamePro = parseFloat(getApnProductAmount)+parseFloat(toShowTotalPriceInApnTable); 
                $(this).closest('tr').find('.productPriceApnTable').val(totalAmountforsamePro);
                testx ='yes';
            }
        });

        if(testx!=='yes'){
         
            $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="text" name="optBranchName[]" class="form-control name_list input-sm"  style="text-align:left; cursor:default" value="'+optBranchName+'" readonly/></td><td><input type="text" name="serviceDate[]" class="form-control name_list input-sm"  style="text-align:left; cursor:default" value="'+serviceDate+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td><input type="number" name="productTotalPriceApnTable[]" class="form-control name_list input-sm productTotalPriceApnTable" id="productTotalPriceApnTable" style="text-align:center; cursor:default" value="'+productPriceForTotalPrice+'" readonly/></td><td><input type="number" name="productPriceApnTable[]" class="form-control name_list input-sm productPriceApnTable" id="productPriceApnTable" style="text-align:center; cursor:default"  value="'+toShowTotalPriceInApnTable+'" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><input type="text" name="productId6[]" class="form-control input-sm name_list hidden branchId" style="text-align:center; cursor:default" value="'+branchId+'" id="optBranchName"/><input type="text" name="productId7[]" class="form-control input-sm name_list hidden serviceDate" style="text-align:center; cursor:default" value="'+serviceDate+'" id="serviceDate"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
        }
       
        $('#productQntty').val('');
        $('#productId').val('');
        $('#serviceDate').val('');
        $('#branchId1').val('');
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

// calculation of total price per row
        $("#productQntty, #productPriceAddPro").on("input",function (e) {
            var productQntyForTotalPrice  = $('#productQntty').val(); //$(this).val();
            var productPriceForTotalPrice = $('#productPriceAddPro').val(); 
                if(productPriceForTotalPrice==''){productPriceForTotalPrice=0;}
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
            var amountAfterRemove    = parseFloat(toralAmountFromInput-removeAmount); //   alert(amountAfterRemove);
            var totalVat                 = parseFloat($('#vatPercent').val()); //alert(totalVat);
            var amountAfterDiscountForGross  = parseFloat($("#amountAfterDiscount").val()); //alert(amountAfterDiscountForGross);
            var grossTotalForRemove      = parseFloat($("#grossTotal").val()); 

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

            //Gross total calculation after remove
            if(isNaN(totalVat) && amountAfterDiscountForGross==0){ alert('1');
                var grossTotalAfterRemove = parseFloat(toralAmountFromInput-removeAmount);
                $('#grossTotal').val(grossTotalAfterRemove||'');
              
            }
            else if(amountAfterDiscountForGross>0 && percent && isNaN(totalVat)){  //alert('2');
                var grossTotalAfterRemove = parseFloat(Math.round(removeAmount*percent) / 100); //alert(toralQnttyFromInput);
                var totalRemoveAmount     = parseFloat(removeAmount-grossTotalAfterRemove); //alert(totalRemoveAmount);
                var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-totalRemoveAmount));

                var payAmountFieldValue = $('#payAmount').val();
                var dueAmount = parseFloat(Math.round(grossTotalAfterRemoveShow-payAmountFieldValue)); 
                if(grossTotalAfterRemoveShow==''){$('#grossTotal').val(0); $('#due').val(dueAmount);}
                else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); $('#due').val(dueAmount||'');}
              
            }
            else if(isNaN(percent) && isNaN(totalVat)){ //alert('3');
             
                var payAmountFieldValue = $('#payAmount').val(); //alert(payAmountFieldValue);
                var removeFromGrosstoral = parseFloat(Math.round(grossTotalForRemove-removeAmount));  
                var dueAmount = parseFloat(Math.round(removeFromGrosstoral-payAmountFieldValue)); 
                if(grossTotalAfterRemoveShow <0){$('#grossTotal').val(0); $('#due').val(dueAmount);}
                else{$('#grossTotal').val(removeFromGrosstoral||'');  $('#due').val(dueAmount);}
              
            }
            else if(amountAfterDiscountForGross && totalVat){
                var grossTotalAfterRemove = parseFloat(Math.round(removeAmount*percent) / 100); 
                  if(isNaN(grossTotalAfterRemove)){grossTotalAfterRemove=0}
                var totalRemoveAmount     = parseFloat(removeAmount-grossTotalAfterRemove); //alert(totalRemoveAmount);
                var vatAfterRemove        = parseFloat(Math.round(totalRemoveAmount*vatPercent) / 100); //alert(grossTotalAftervat);
                var amountToRemove      = parseFloat(Math.ceil(totalRemoveAmount+vatAfterRemove)); //alert(amountToRemove);
                var grossTotalAfterRemoveShow = parseFloat(Math.round(grossTotalForRemove-amountToRemove));

                var payAmountFieldValue = $('#payAmount').val();
                var dueAmount = parseFloat(Math.round(grossTotalAfterRemoveShow-payAmountFieldValue)); 
                if(grossTotalAfterRemoveShow=='' || grossTotalAfterRemoveShow<0){$('#grossTotal').val(0);/* $('#payAmount').val(0);*/ $('#due').val(dueAmount); }
                else{$('#grossTotal').val(grossTotalAfterRemoveShow||''); /*$('#payAmount').val(grossTotalAfterRemoveShow);*/ $('#due').val(dueAmount);}
            }
      
            //$('#due').val(0);

            var button_id = $(this).attr("id");   
            $('#row'+button_id+'').remove();  
       }); //end remove button click
     
// Discount Calcualtion
    $("#discountPercent, #totalAmount").on("input",function(e) { // input on change
        var percent = parseFloat($('#discountPercent').val()); //alert(percent);
        var tAmount = parseFloat($("#totalAmount").val()); //alert(tAmount);
        
        var result = parseFloat(Math.ceil(tAmount*percent) / 100).toFixed(5).replace(/\.0+$/,''); //alert(result);
        var totalAfterDiscount = parseFloat(Math.round(tAmount-result)).toFixed(5).replace(/\.0+$/,''); //alert(result);
      
        if(isNaN(result)){ $('#discount').val(''); $('#amountAfterDiscount').val(tAmount||'');}
        else{$('#discount').val(result||''); $('#amountAfterDiscount').val(totalAfterDiscount||''); }
        //$('#due').val(0); 
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
        if(isNaN(afterDiscountForVat) || afterDiscountForVat==0){
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
        var totalVat                    = parseFloat($('#vat').val()); //alert(totalVat);
        var amountForGrosstotal         = parseFloat($("#totalAmount").val()); //alert(amountForGrosstotal);
        var amountAfterDiscountForGross = parseFloat($("#amountAfterDiscount").val()); //alert(amountAfterDiscountForGross);

        var valueOfPayAmountField       = parseFloat($('#payAmount').val()); //alert(valueOfPayAmountField);
        var grossTotalForDue            = parseFloat($('#grossTotal').val()); //alert(grossTotalForDue);

        if(isNaN(totalVat) && isNaN(amountAfterDiscountForGross)){  //alert('1');
            $('#grossTotal').val(amountForGrosstotal||'');
            $('#due').val(dueAfterPayAmount||'');
        }
          
        else if(amountAfterDiscountForGross && isNaN(totalVat)){ //alert('3');
            var dueAfterPayAmount = parseFloat(amountAfterDiscountForGross-valueOfPayAmountField);
            $('#grossTotal').val(amountAfterDiscountForGross||'');
            $('#due').val(dueAfterPayAmount);  
        }
        else if(isNaN(amountAfterDiscountForGross) && totalVat){ //alert('4');
            var dueAfterPayAmount = parseFloat(grossTotalAmount-valueOfPayAmountField);
            $('#grossTotal').val(grossTotalAmount||''); 
            $('#due').val(dueAfterPayAmount);
        }
        else if(amountAfterDiscountForGross && totalVat){ //alert('5');
            var grossTotalAmount = parseFloat(Math.ceil(amountAfterDiscountForGross+totalVat));
            var dueAfterPayAmount = parseFloat(grossTotalAmount-valueOfPayAmountField);
            
            $('#grossTotal').val(grossTotalAmount); //alert(dueAfterPayAmount);
            
            $('#due').val(dueAfterPayAmount||'');
        } 
        else if(amountAfterDiscountForGross<1 && isNaN(totalVat)){

            $('#grossTotal').val(amountAfterDiscountForGross||'');
        }
        else if(amountAfterDiscountForGross<1 && totalVat){
            $('#grossTotal').val(amountAfterDiscountForGross||'');
            $('#payAmount').val(amountAfterDiscountForGross||'');
            $('#vat').val('');
        }
          //$('#due').val(0);    
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
                $("#payAmounte").html("Pay Amount Should not be more than gross total").show().fadeOut("slow");
                $("#payAmounte").css("color", "red");
                      event.preventDefault();
            }
            else if(isNaN(dueAmount)){$('#due').val(0);}
            else{$('#due').val(dueAmount);}
        }
    })    
      

    $(document).on('click', '.edit-modal', function() {
      //if(hasAccess('editInvPurchaseItem')){
        $('#addProductTable tbody .forEmpty').remove();
        $('.errormsg').empty();
        $('#MSGE').empty();
        $('#MSGS').empty();
        $('#footer_action_button').text(" Update");
        $('#footer_action_button').addClass('glyphicon glyphicon-check');
        $('#footer_action_button_dismis').text(" Close");
        $('#footer_action_button_dismis').addClass('glyphicon glyphicon-remove');
        $('.actionBtn').addClass('btn-success');
        $('.actionBtn').removeClass('btn-danger');
        $('.actionBtn').addClass('edit');
        $('.modal-title').text('Update Data');
        $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
        $('.modal-dialog').css('width','80%');
        $('.deleteContent').hide();
        $('.form-horizontal').show();

        var salesId = $(this).data('id');
        var csrf = "<?php echo csrf_token(); ?>";

        $.ajax({
            type: 'post',
            url: './salesEditData',
            data:{id:salesId,_token: csrf},
            dataType: 'json',
            success: function(data){
                $('#billNo').val(data['salesTables'].salesBillNo);
                $('#CompanyName').val(data['salesTables'].companyId);
                $('#salesDate').val(data['salesTables'].salesDate);
                $('#totalQuantity').val(data['salesTables'].totalSalesQuantity);
                $('#totalAmount').val(data['salesTables'].totalSalesAmount);
                $('#discountPercent').val(data['salesTables'].discountRate);
                $('#discount').val(data['salesTables'].salesDiscount);
                $('#amountAfterDiscount').val(data['salesTables'].tcAfterDiscount);
                $('#vatPercent').val(data['salesTables'].vatRate);
                $('#vat').val(data['salesTables'].salesVat);
                $('#grossTotal').val(data['salesTables'].totalSaleGrossAmount);
                $('#payAmount').val(data['salesTables'].salesPayAmount);
                $('#due').val(data['salesTables'].salesDue);
                $('#bankName').val(data['salesTables'].bankName);
                $('#checkNo').val(data['salesTables'].checkNo);
                $('#bankDate').val(data['salesTables'].bankDate);
                var salesPersonId =data['servicePersonArr'];
                $('#salesEmployeeId').val(salesPersonId);
                var newOption = new Option(salesPersonId.text, salesPersonId.id, true, true);
                $('#salesEmployeeId').append(newOption).trigger('change');
                $("#salesEmployeeId option[selected]").remove();  
                $('#proTotalAmountShowFooter').html(num(data['salesTables'].totalSalesAmount));
                $('#totalQuantityFooter').html(data['salesTables'].totalSalesQuantity);
               
                if(data['salesTables'].paymentType==1){
                   $('input[name=paymentType][value=1]').attr('checked','checked');
                   $("#bankStatment").hide();
                }else if(data['salesTables'].paymentType==2){

                   $('input[name=paymentType][value=2]').attr('checked','checked');
                   $("#bankStatment").show();
                }
                $("#id").val(data['salesTables'].id);
                $('#footer_action_button2').hide();
                $('#footer_action_button').show();
                $('.actionBtn').removeClass('delete');
                $('#myModal').modal('show');
            }
      });
        

        if($(this).data('discount')){
            var discountAmount      = $(this).data('discount');
            var totalAmount         = $(this).data('totalamount');
            var vatAmount           = $(this).data('vat');
            var tAmountafterDisc    = $(this).data('amountafterdiscount');
            var calculationDisPer   = parseFloat(Math.round(100*discountAmount) / totalAmount);
            $('#discountPercent').val(calculationDisPer);
            if(vatAmount){
                var calculationVatPer   = parseFloat(Math.round(100*vatAmount) / tAmountafterDisc);
                $('#vatPercent').val(calculationVatPer);
            }
        }
        if($(this).data('discount') == '' && $(this).data('vat')){
            var totalAmount     = $(this).data('totalamount');
            var vatAmount       = $(this).data('vat');
            var calculationVatPer   = parseFloat(Math.round(100*vatAmount) / totalAmount);
            $('#vatPercent').val(calculationVatPer); //alert(calculationVatPer);
         }
      
        var purIdForCountRow = $(this).data('id');
        var csrf = "<?php echo csrf_token(); ?>";
        $.ajax({
            type: 'post',
            url: './salesEditAppendRows',
            data: {
                '_token': csrf,
                'id': purIdForCountRow
            },
            dataType: 'json',
            success: function(data){
             
                $.each(data, function (key, value) {
                    if (key == "salesDetailsTables") {
                        var i = 0;
              
                        $.each(value, function (key1, value1) {
              
                            var string="<tr id='row"+i+"' class='forEmpty forhide'>";
                            string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"'><option>select Product</option></select></td>";
                            if(value1.branchId==1){
                                string+="<td class='' style='text-align: left;'><select name='productId6[]' class='apendSelectOption1 form-control input-sm'  value='"+value1.branchId+"' ><option value='1'>Head Office</option></select></td>";
                            }
                            if(value1.branchId==2){
                                string+="<td class='' style='text-align: left;'><select name='productId6[]' class='apendSelectOption1 form-control input-sm' value='"+value1.branchId+"' ><option value='2'>Branch</option></select></td>";
                            }
                            if(value1.salesType==2){
                                string+="<td><input class='dateService form-control input-sm' style='text-align:left;' name='productId7[]' id='productId7' value='"+value1.serviceDate+"' autocomplete='off' readonly /></td>";
                            }
                            string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt' style='text-align:center;' value='"+value1.salesProductQuantity+"' autocomplete='off'/></td>";
                            string+="<td><input type='number' name='productTotalPriceApnTable[]'' class='form-control name_list input-sm productTotalPriceApnTable' id='productTotalPriceApnTable' style='text-align:center;' value='"+value1.unitPrice+"'/></td>";
                            string+="<td><input type='number' name='productPriceApnTable[]' class='form-control name_list input-sm productPriceApnTable' id='productPriceApnTable' style='text-align:center;'  value='"+value1.totalAmount+"' readonly/></td>";
                            string+="<td><input type='text' name='salesDetailsTablesId[]' class='form-control input-sm name_list hidden deleteIdSend' style='text-align:center; cursor:default' value='"+value1.id+"' /><a href='javascript:;' name='remove' id='"+i+"' class='btn_remove' style='width:80px'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></a></td></td>";
                            string+="</tr>";
                            $('#addProductTable').append(string);
               
                             ++i;
                
                        })
                    }
        
                    if (key == "productId") {
                       $.each(value, function (key1, value1) {
              //alert(value1.id);
                            $('.apendSelectOption').append("<option value='"+ value1.id+"'>"+value1.name+"</option>");

                        });
            
                    }

         });

        var productDetailsRowLength = data.salesDetailsTables.length;
            for(var i=0; i<productDetailsRowLength; i++){
                $('#productId5'+i).val(data['salesDetailsTables'][i].productId);
                $('#apnQnt'+i).val(data['salesDetailsTables'][i].quantityId);
            }
        },
            error: function( data ){
              alert('error');
            }
        }); 
    //}
    });

        $("#addProductTable").hover(function(){
            $(".apnQnt,.productTotalPriceApnTable").on('input',function(){
                var getApnProductQty  = $(this).closest('tr').find('.apnQnt').val();
                var getApnProPerpri   = $(this).closest('tr').find('.productTotalPriceApnTable').val();
                var totalAmountForApnR  = parseFloat(Math.ceil(getApnProductQty*getApnProPerpri));
                $(this).closest('tr').find('.productPriceApnTable').val(totalAmountForApnR);

                var sum = 0;
                var total = 0;
                $('.apnQnt').each(function() {
                    sum += Number($(this).val());
                    $('#totalQuantity').val(sum);
                    $('#totalQuantityFooter').text(sum);
                });

               $('.productPriceApnTable').each(function() {
                    total += Number($(this).val());
                    $('#totalAmount').val(total);
                    $('#proTotalAmountShowFooter').text(total);
                });

                $("#totalAmount").trigger('input');
                 $("#grossTotal").trigger('input');

            });
        });


// Edit Data (Modal and function edit data)
        $('.modal-footer').on('click', '.edit', function() {
  //$('#supplierId').removeAttr('disabled');

  
            $.ajax({
                type: 'post',
                url: './editPosServiceProductItem',
                data: $('form').serialize(),
                dataType: 'json',
                success: function( data ){
                 
                    if (data.errors) {
                        if (data.errors['supplierId']) {
                            $('#supplierIde').empty();
                            $('#supplierIde').append('<span class="errormsg" style="color:red;">'+data.errors.supplierId+'</span>');
                        }
                    }
                    else{ 
                        $('#MSGE').addClass("hidden"); 
                        $('#MSGS').text('Data successfully inserted!');
                        $('#myModal').modal('hide');
                        $('.succsMsg').removeClass('hidden');
                        $('.succsMsg').show();
                        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
                        location.reload();
                    
                    }
                },
                error: function( data ){
                    // Handle error
                   alert('data.errors');
                }
            });
        });

//delete function
        $(document).on('click', '.delete-modal', function() {
            $('#MSGE').empty();
            $('#MSGS').empty();
            $('#footer_action_button2').text(" Yes");
            $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
            $('#footer_action_button_dismis').text(" No");
            $('#footer_action_button_dismis').removeClass('glyphicon glyphicon-remove');
            $('.actionBtn').removeClass('edit');
            $('.actionBtn').removeClass('btn-success');
            $('.actionBtn').addClass('btn-danger');
            $('.actionBtn').addClass('delete');
            $('.modal-title').text('Delete');
            $('.modal-header').css({"background-color":"black", "color":"white", "padding":"10px"});
            $('.modal-dialog').css('width','50%');
            $('.id').text($(this).data('id'));
            $('.deleteContent').show();
            $('.form-horizontal').hide();
            $('#footer_action_button2').show();
            $('#footer_action_button').hide();
            $('#myModal').modal('show');

        });

        $('.modal-footer').on('click', '.delete', function() {

            $.ajax({
                type: 'post',
                url: './deletPosSalesItem',
                data: {
                  '_token': $('input[name=_token]').val(),
                  'id': $('.id').text()
                },
                success: function(data) {
                    $('.item' + $('.id').text()).remove();
                },
                    error: function( data ){
                     alert('hi');
                    }
            });
        });
        function num(argument){

          return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
//sales detaisl
        $(document).on('click', '.forPurchaseDetailsModel', function() {

            $('.modal-header').css({"background-color":"white"});
            $('#swhoAppendRows tbody').empty();
            $('#purchDetailsModel').modal('show');
            $('.modal-dialog').css('width','50%');
            var id = ($(this).data('id'));
            var crsf = ($(this).data('token'));
            $.ajax({
                type: 'post',
                url: './posSalesDetails',
                data: {
                    '_token': $('input[name=_token]').val(),
                    'id': id
                 },
                dataType: 'json',
                success: function( data ){
                    $('#purDateHead').text(data.createdDate);    
                    $('#billNoHead').text(data['salesDetails'][0].salesBillNo);
                    $('#salesDateHead').text(data.dateFromarte);
                    $('#companyHead').text(data['companyName'].clientCompanyName);
                    $('#SalesPerHead').text(data['personName']);
                    $('#totalQtyHead').text(data['salesDetails'][0].totalSalesQuantity);
                    $('#totalAmountHead').text(data['salesDetails'][0].totalSalesAmount);
                    $('#payAmountHead').text(data['salesDetails'][0].salesPayAmount);
                    $('#dueAmountHead').text(data['salesDetails'][0].salesDue);
               
                    $(document).on('click', '#print', function(){    
                       
                       $("#salesPrint").show();
                    });
                // Start Print 
                
                    $('#printsalDateHead').text(data.dateFromartecreate);    
                    $('#printBillNoHead').text(data['salesDetails'][0].salesBillNo);
                    $('#printCompanyHead').text(data['companyName'].clientCompanyName);
                    $('#printSalesPerHead').text(data['personName']);
                    $('#printTotalQtyHead').text(data['salesDetails'][0].totalSalesQuantity);
                    $('#printTotalAmountHead').text(data['salesDetails'][0].totalSalesAmount);
                    $('#printPayAmountHead').text(data['salesDetails'][0].salesPayAmount);
                    $('#printDueAmountHead').text(data['salesDetails'][0].salesDue);
                  //$('#createPerson').text(data['employeeName'][0].emp_name_english);
                  //$('#createPersonId').text(data['employeeName'].emp_id);
                  //$('#createPersonDeg').text(data['employeeDege']);
                
               
               
                $.each(data, function (key, value) {
                if (key == "salesDetailsTable") {
                  
                  var i = 1;
                  var headOffice = 'Head office';
                  var branch = 'Branch';
                $.each(value, function (key1,value1) {
                 
                    var string="<tr>";
                        string+="<td style='text-align:center' class='slNoUse'>"+i+"</td>";
                        string+="<td class='productName' style='text-align:left;' id='productRowName"+i+"'>"+data.productName[key1]+"</td>";
                        if(value1.branchId==1){
                           string+="<td class='branchName' style='text-align:left;' id='branchId"+i+"'>"+headOffice+"</td>";
                        }
                        if(value1.branchId==2){
                           string+="<td class='branchName' style='text-align:left;' id='branchId"+i+"'>"+branch+"</td>";
                        }
                         if(value1.salesType==2){
                           string+="<td class='serviceDate' style='text-align:left;' id='serviceDate"+i+"'>"+value1.serviceDate+"</td>";
                        }
                        string+="<td class='productPricePerPc'>"+value1.salesProductQuantity+"</td>";
                        string+="<td class='productPricePerPc'>"+num(value1.unitPrice)+"</td>";
                        string+="<td class='productPricePerPc'>"+num(value1.totalAmount)+"</td>";
                        string+="</tr>";
                        $('#swhoAppendRows').append(string);

                        i++;

                    })
                }
                if (key == "salesDetailsTable") {
                    var j = 1;
                    var headOffice = 'Head office';
                    var branch = 'Branch';
                    $.each(value, function (key2, value2) {
                        var strin="<tr>";
                        strin+="<td style='text-align:center' class='slNoUse'>"+j+"</td>";
                        strin+="<td class='productName' style='text-align:left; font-size:14px;'  id='product"+j+"'>"+data.productName[key2]+"</td>";
                        if(value2.branchId==1){
                            strin+="<td class='branchName' style='text-align:left;' id='branchId"+j+"'>"+headOffice+"</td>";
                        }
                        if(value2.branchId==2){
                            strin+="<td class='branchName' style='text-align:left;' id='branchId"+j+"'>"+branch+"</td>";
                        }

                        strin+="<td class='productPricePerPc'style='font-size:14px;'>"+value2.salesProductQuantity+"</td>";
                        strin+="<td class='productPricePerPc'style='font-size:14px; text-align:right; padding-right:5px;'>"+num(value2.unitPrice)+"</td>";
                        strin+="<td class='productPricePerPc'style='font-size:14px; text-align:right; padding-right:5px;'>"+num(value2.totalAmount)+"</td>";
                        strin+="</tr>";
                        $('#salesPrint').append(strin);
                          
                        j++;

                    })
                  } 
              });

                    $('#totalQtyDetails').val(data['salesDetails'][0].totalSalesQuantity);
                    $('#totalAmountDetails').val((data['salesDetails'][0].totalSalesAmount));
                    $('#PrintTotalQtyDetails').html(data['salesDetails'][0].totalSalesQuantity);
                    $('#printTotalAmountDetails').html(num(data['salesDetails'][0].totalSalesAmount));
                 

                },
                  error: function( data ){
                        alert('error');
                }
            });

        });


        $("#purchaseForm").mouseover(function(){
            var rows= $('#addProductTable tbody tr.forhide').length;
            if(rows>0){
                 $("#supplierId").prop('disabled', true);
                }else{$("#supplierId").prop('disabled', false);}
            }); 
        });
</script>
{{-- Filtering --}}


{{-- Print Page --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#print").click(function(event) {

            $("#hiddenTitle").show();
            $("#hiddenInfo").show();
            $("#purchasePrint").removeClass('table table-striped table-bordered');

        var mainContents = document.getElementById("printingContent").innerHTML;
  var headerContents = '';

  var printStyle = '<style>#purchasePrint{float:none !important;height:auto;padding:0px; margin-top:10px; margin-bottom:10px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tbody tr{ page-break-inside:avoid; margin-bottom:10px; page-break-after:auto} </style><style media="print">@page{size:portrait;}</style><style>@media print { #tableDiv { -moz-overflow: visible !important;}}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

        printContents = '<div id="order-details-wrapper" style="-moz-overflow: visible !important;"> ' + printStyle + mainContents+'</div>';


var win = window.open('','printwindow');
win.document.write(printContents);
win.print();
location.reload();
win.close();

});
    });

// Sales Person Change
        $("#CompanyName").change(function(){
            var clientCompanyId = $(this).val();
            
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './posClientsProductsAssign',
                data: {clientCompanyId:clientCompanyId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function(data){
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">All</option>');
                    var salesPersonId = data['salesPersonArr'];// Data Array
                    $('#salesEmployeeId').val(salesPersonId);

                    var newOption = new Option(salesPersonId.text, salesPersonId.id, true, true);

                    $('#salesEmployeeId').append(newOption).trigger('change');
                    $("#salesEmployeeId option[selected]").remove();

                    $.each(data['productName'], function (key, productObj) {  
                                      
                        $('#productId').append("<option value='"+ productObj.id+"'>"+productObj.name+"</option>");
                        
                    });
                
                },
                error: function(_response){
                    alert("error");
                }

        });/*End Ajax*/

    });/*End Sales Person*/

</script>
{{-- EndPrint Page --}}

<script type="text/javascript">
    $(document).ready(function(){ 

        $("#branchId1").change(function(){

            var branchId = $(this).val();
            var productId    = $("#productId").val();
           //alert(productId);
            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './posBranchChange',
                data: {branchId:branchId,productId:productId,_token: csrf},
                dataType: 'json',
                success: function( data ) {
                    //alert(JSON.stringify(data['productPrice']));
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
                    //alert(JSON.stringify(data['productPrice']));
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