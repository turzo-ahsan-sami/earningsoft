@extends('layouts/inventory_layout')
@section('title', '| New Group')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;
?>
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addInvPurcReqReturnF/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Purchase Return</a>
          </div>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#invPurchaseRetView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="invPurchaseRetView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Pur. Return Date</th>
                <th>Pur. Return Bill No</th>
                <th>Purchase Bill No</th>
                <th>Supplier Name</th>
                <th>Totlal Quantity</th>
                <th>Totlal Amount</th>
                <th>Discount</th>
                <th>Gross Totlal</th>
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($purchaseRets as $purchaseRet)
                    <tr class="item{{$purchaseRet->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{date('d-m-Y', strtotime($purchaseRet->purchaseReturnDate))}}</td>
                      <td style="text-align: left; padding:5px">{{$purchaseRet->purchaseReturnBillNo}}</td>
                      <td style="text-align: left; padding:5px">{{$purchaseRet->purchaseBillNo}}</td>
                      <td style="text-align: left; padding:5px">
                        <?php
                            $supplierName = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$purchaseRet->supplierId)->first();
                          ?>
                        {{$supplierName->supplierCompanyName}}
                      </td>
                      <td style="text-align: right; padding:5px">{{$purchaseRet->totalQuantity}}</td>
                      <td style="text-align: right; padding:5px">{{$purchaseRet->totalAmount}}</td>
                      <td style="text-align: right; padding:5px">{{$purchaseRet->discount}}</td>
                      <td style="text-align: right; padding:5px">{{$purchaseRet->grossTotal}}</td>
                      <td class="text-center" width="80">
                        <a href="javascript:;" class="forPurchaseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$purchaseRet->id}}">
                            <span class="fa fa-eye"></span>
                        </a>&nbsp
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$purchaseRet->id}}" data-purchasereturnbillno="{{$purchaseRet->purchaseReturnBillNo}}" data-purchasebillno="{{$purchaseRet->purchaseBillNo}}" data-supplierid="{{$purchaseRet->supplierId}}" data-remark="{{$purchaseRet->remark}}" data-purchasedate="{{$purchaseRet->purchaseDate}}" data-purchasereturndate="{{$purchaseRet->purchaseReturnDate}}" data-totalquantity="{{$purchaseRet->totalQuantity}}" data-totalamount="{{$purchaseRet->totalAmount}}" data-discountpercent="{{$purchaseRet->discountPercent}}" data-discount="{{$purchaseRet->discount}}" data-amountafterdiscount="{{$purchaseRet->amountAfterDiscount}}" data-vatpercent="{{$purchaseRet->vatPercent}}" data-vat="{{$purchaseRet->vat}}" data-grosstotal="{{$purchaseRet->grossTotal}}" data-createddate="{{$purchaseRet->createdDate}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$purchaseRet->id}}">
                          <span class="glyphicon glyphicon-trash"></span>
                        </a>
                    </td>
                  </tr>
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
       {!! Form::open(array('url' => '', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}

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
                                <p id='purchaseBillNo1e' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('supplierId', 'Supplier Name:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                    $supplierId = array('' => 'Please Select') + DB::table('gnr_supplier')->pluck('supplierCompanyName','id')->all();; 
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
                            <div class="col-sm-8">
                                {!! Form::text('purchaseReturnDate', null, ['class' => 'form-control', 'id' => 'purchaseReturnDate', 'readonly', 'readonly']) !!}
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
                        <div class="form-group hidden">
                            {!! Form::label('createdDate', 'createdDate:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('createdDate', null, ['class' => 'form-control', 'id' => 'createdDate']) !!}
                                 <p id='createdDatee' style="max-height:3px;"></p>
                            </div> 
                        </div>
                        <p id='numericError' style="max-height:3px; color:red;"></p>
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
</div>


@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>

<script type="text/javascript">
$( document ).ready(function() {

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

//edit in append rows
$("#addProductTable").hover(function(){
    $(".apnQnt").on('input',function(){
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

var totalAmountField  = $("#totalAmount").val(); alert(totalAmountField);
var discountRate      = parseFloat($('#discountPercent').val()); //alert(discountRate);
var discountAmount    = parseFloat(totalAmountField*discountRate) /100; //alert(discountAmount);
                        $('#discount').val(discountAmount);


var amoutnAfterDiscount = parseFloat(totalAmountField-discountAmount); //alert(amoutnAfterDiscount);
                          $('#amountAfterDiscount').val(amoutnAfterDiscount);
                          $('#grossTotal').val(amoutnAfterDiscount);

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


$(document).on('click', '.edit-modal', function() {
  $('#addProductTable tbody .forEmpty').remove();

var dropDownText = $(this).data('purchasebillno');
var dropDownValue = $("#purchaseBillNo1 option").filter(function() {
  return $(this).text() === dropDownText;
}).first().attr("value");

    $('.errormsg').empty();
    $('#MSGE').empty();
    $('#MSGS').empty();
    $('#footer_action_button').text(" Update");
    $('#footer_action_button').addClass('glyphicon glyphicon-check');
    //$('#footer_action_button').removeClass('glyphicon-trash');
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
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#purchaseReturnBillNo').val($(this).data('purchasereturnbillno'));
    $('#purchaseBillNo1').val(dropDownValue);
    $('#supplierId').val($(this).data('supplierid'));
    $('#purchaseDate').val($(this).data('purchasedate'));
    $('#purchaseReturnDate').val($(this).data('purchasereturndate'));
    $('#remark').val($(this).data('remark'));
    $('#totalQuantity').val($(this).data('totalquantity'));
    $('#totalAmount').val($(this).data('totalamount'));
    $('#discountPercent').val($(this).data('discountpercent'));
    $('#discount').val($(this).data('discount'));
    $('#amountAfterDiscount').val($(this).data('amountafterdiscount'));
    $('#vatPercent').val($(this).data('vatpercent'));
    $('#grossTotal').val($(this).data('grosstotal'));
    $('#totalQuantityFooter').text($(this).data('totalquantity'));
    $('#proTotalAmountShowFooter').text($(this).data('totalamount'));
    $('#createdDate').val($(this).data('createddate'));
  
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');

var purIdForCountRow = $(this).data('id');
var csrf = "<?php echo csrf_token(); ?>"; 
$.ajax({
         type: 'post',
         url: './purReturnEditAppendRows',
         data: {
            '_token': csrf,
            'id': purIdForCountRow
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
$.each(data, function (key, value) {
        if (key == "purRetDetailsTables") {
          var i = 0;
        $.each(value, function (key1, value1) {
          //alert(value1.totalQuantity);
          var string="<tr id='row"+i+"' class='forEmpty forhide'>";
            string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"'><option>select Product</option></select></td>";
            string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt' style='text-align:center;' value='"+value1.quantity+"' autocomplete='off'/></td>";
            string+="<td><input type='number' name='productTotalPriceApnTable[]'' class='form-control name_list input-sm productTotalPriceApnTable' id='productTotalPriceApnTable' style='text-align:center;' value='"+value1.price+"' readonly/></td>";
            string+="<td><input type='number' name='productPriceApnTable[]' class='form-control name_list input-sm productPriceApnTable' id='productPriceApnTable' style='text-align:center;'  value='"+value1.totalPrice+"' readonly/></td>";
            string+="<td><input type='text' name='purchaseDetailsTablesId[]' class='form-control input-sm name_list hidden deleteIdSend' style='text-align:center; cursor:default' value='"+value1.id+"' /><a href='javascript:;' name='remove' id='"+i+"' class='btn_remove' style='width:80px'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></a></td></td>";
            string+="</tr>";
            $('#addProductTable').append(string);
            //$('#forOptionSelected'+increage).val(value1.productId);
            ++i;
          })
          }
    
      if (key == "productId") {
        $.each(value, function (key1, value1) {
          //alert(value1.id);
        $('.apendSelectOption').append("<option value='"+ value1.id+"'>"+value1.name+"</option>");

        })
      } 
  });

    var productDetailsRowLength = data.purRetDetailsTables.length;
      for(var i=0; i<productDetailsRowLength; i++){
        $('#productId5'+i).val(data['purRetDetailsTables'][i].productId);
      }
        },
        error: function( data ){
          alert();
        }
    });

  });


  // Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {
$.ajax({
         type: 'post',
         url: './editInvPurRetItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
        alert(JSON.stringify(data));
      if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
            
   
        },
        error: function( data ){
            // Handle error
            alert('data.errors');
            
        }
    });
});
    

       
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