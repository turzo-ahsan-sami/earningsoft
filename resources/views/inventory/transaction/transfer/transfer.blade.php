@extends('layouts/inventory_layout')
@section('title', '| Transfer')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$grnBranchId = Session::get('branchId');
$gnrBranchId = $grnBranchId;
$grnBranchId;
?>
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addTransfer/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Transfer</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">TRANSFER LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#invTrnsTransferView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="invTrnsTransferView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Date</th>
                <th>Trns. BillNo</th>
                <th>Branch Out</th>
                <th>Branch In</th>
                <th>Total Quantity</th>
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($transfers as $transfer)
                    <tr class="item{{$transfer->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{$transfer->transferDate}}</td>
                      <td>{{$transfer->transferBillNo}}</td>
                      <td style="text-align: left; padding-left: 5px;">
                          <?php
                            $branchNameForm = DB::table('gnr_branch')->where('id', $transfer->brancIdFrom)->value('name') ;
                          ?>
                          {{$branchNameForm}}
                      </td>
                      <td style="text-align: left; padding-left: 5px;">
                          <?php
                            $branchNameTo = DB::table('gnr_branch')->where('id', $transfer->branchIdTo)->value('name') ;
                          ?>
                          {{$branchNameTo}}
                      </td>
                      <td style="text-align: right; padding-right: 5px;">{{$transfer->totalTransferQuantity}}</td>
                      <td class="text-center" width="80">
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$transfer->id}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$transfer->id}}">
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
       {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'useForm')) !!}
                     <div class="row">
                     <div class="col-md-12">
                     <div class="col-md-6"> 
                        <div class="form-group hidden">
                              {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                              {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text']) !!}
                              {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
                          </div>
                        </div>    
                         
                         <div class="form-group">
                            {!! Form::label('transferBillNo', 'Transfer No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('transferBillNo', null, ['class' => 'form-control', 'id' => 'transferBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('orderNo', 'Order No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('orderNo', $value = null, ['class' => 'form-control', 'id' => 'orderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                                <span id="orderNoError" style="display: none;"><font color="red">*Please Fill</font></span>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('transferOrderNo', 'Transfer Order No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('transferOrderNo', $value = null, ['class' => 'form-control', 'id' => 'transferOrderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                                <span id="transferOrderNoError" style="display: none;"><font color="red">*Please Fill</font></span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('brancIdFrom', 'Branch From:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                    $branchNames = DB::table('gnr_branch')->where('id', $gnrBranchId)->pluck('name','id')->all(); 
                                ?>
                                {!! Form::select('brancIdFrom', ($branchNames), null, ['class' => 'form-control', 'id' => 'brancIdFrom']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('branchIdTo', 'Branch To:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                                    $branchNames = array(''=>'Select Branch')+DB::table('gnr_branch')->orderBy('name', 'ASC')->pluck('name','id')->all(); 
                                ?>
                                {!! Form::select('branchIdTo', ($branchNames), null, ['class' => 'form-control', 'id' => 'branchIdTo']) !!}
                                <p id='branchIdToe' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('totalTransferQuantity', 'Transfer Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalTransferQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalTransferQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('totalTransferAmount', 'Transfer Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalTransferAmount', $value = null, ['class' => 'form-control', 'id' => 'totalTransferAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('averagePriceInput', 'Average Price:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('averagePriceInput', $value = null, ['class' => 'form-control', 'id' => 'averagePriceInput', 'type' => 'text','autocomplete'=>'off', 'readonly']) !!}
                            </div>
                        </div> 

        </div>                  
      </div>

      </div>
      <!--Stock report-->
        <div class="row" id="currentStockFdiv">
            <div class="col-sm-12">
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('currentStock', 'Current Stock:', ['class' => 'control-label col-sm-6']) !!}
                    <div class="col-sm-6">
                        {!! Form::text('currentStock', $value = null, ['class' => 'form-control text-center', 'id' => 'currentStock', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                    </div>
                </div>
            </div>
            </div>
        </div> 
               
          <!-- filtering -->
                    <div class="row" style="margin-top: 3%">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('productGroupId', 'Group:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                    <?php 
                                        $groupIds =  DB::table('inv_product')->select('groupId')->get();
                                        $groupArSize = count($groupIds);
                                        if($groupArSize>0){
                                            foreach($groupIds as $groupId){
                                              $groupName [] =  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->first();   
                                            }
                                            $groupNames = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
                                        }
                                    ?>   
                                        <select name="productGroupId" class="form-control input-sm" id="productGroupId">
                                            <option value="">Please select</option>
                                                <?php if($groupArSize>0): ?>
                                                    @foreach($groupNames as $groupName)
                                                           <option value="{{$groupName->id}}">{{$groupName->name}}</option>
                                                    @endforeach
                                                <?php endif ; ?>
                                        </select>
                                    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                    <?php 
                                        $categoryIds =  DB::table('inv_product')->select('categoryId')->get();
                                        $catArSize = count($categoryIds);
                                        if($catArSize>0){
                                            foreach($categoryIds as $categoryId){
                                                $categoryName [] =  DB::table('inv_product_category')->select('name','id')->where('id',$categoryId->categoryId)->first();   
                                            }
                                            $categoryNames = array_map("unserialize", array_unique(array_map("serialize", $categoryName)));
                                        }
                                    ?>
                                    <select name="productCategoryId" class="form-control input-sm" id="productCategoryId">
                                        <option value="">Please select</option>
                                            <?php if($catArSize>0): ?>
                                                @foreach($categoryNames as $categoryName)
                                                       <option value="{{$categoryName->id}}">{{$categoryName->name}}</option>
                                                @endforeach
                                            <?php endif ; ?>
                                    </select>
                                    
                                    </div>
                                </div>
                            </div>
            
                          <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('productSubCategoryId', 'Subcatagory:', ['class' => 'control-label col-sm-12']) !!}
                                    <div class="col-sm-12">
                                    <?php 
                                        $subCategoryIds =  DB::table('inv_product')->select('subCategoryId')->get();
                                        $subCatArSize = count($subCategoryIds);
                                        if($subCatArSize>0){
                                            foreach($subCategoryIds as $subCategoryId){
                                                $subCategoryName [] =  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCategoryId->subCategoryId)->first();   
                                            }
                                            $subCategoryNames = array_map("unserialize", array_unique(array_map("serialize", $subCategoryName)));
                                        }
                                    ?>
                                    <select name="productSubCategoryId" class="form-control input-sm" id="productSubCategoryId">
                                        <option value="">Please select</option>
                                            <?php if($subCatArSize>0): ?>
                                                @foreach($subCategoryNames as $subCategoryName)
                                                       <option value="{{$subCategoryName->id}}">{{$subCategoryName->name}}</option>
                                                @endforeach
                                            <?php endif ; ?>
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
                        <div class="col-md-0"></div>
                        <div class="col-md-12">
                        <table id="addProductTable" class="table table-bordered responsive addProductTable">
                            <thead>
                                <tr class="">
                                    <th style="text-align:center;" class="col-sm-4">Item Name</th>
                                    <th style="text-align:center;" class="col-sm-3">Qty</th>
                                    <th style="text-align:center;" class="col-sm-2 ">Price</th>
                                    <th style="text-align:center;" class="col-sm-2 ">Total</th>
                                    <th style="text-align:center;" class="col-sm-1">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php 
                                        $productId = array('' => 'Please Select product') + DB::table('inv_product')->pluck('name','id')->all(); 
                                    ?>
                                    {!! Form::select('productId', ($productId), null, array('class'=>'form-control input-sm', 'id' => 'productId')) !!}
                                    
                                </td>
                                <td class="">
                                    <input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='' placeholder='Insert Item' min="1"/>
                                    
                                </td>
                                <td class="">
                                    
                                </td>
                                <td class="">
                                    
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
                                <td class=""></td>
                                <td class=""></td>
                                <td class=""></td>
                            </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="text-align:right;"><strong>Total Quantity</strong></td>
                                    <td style="text-align:center;" id='totalQuantityFooter'></td>
                                    <td style="text-align:center;" id='productPriceShow' class=""><strong>Total Amount</strong></td>
                                    <td style="text-align:center;" id='proTotalPriceShow' class=""></td>
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
      </div>
    </div>
  </div>
</div>
@include('inventory/transaction/trnsuse/useDetails')
@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){ 

//Stock calculation======================================================
    var stockQuantity       = 0;;
    var toralPriceAllTbl    = 0;
    var averagePrice = 0;
    var changeProductQuantity = 0;
    $("#productId").change(function(){ 
                var productId = $('#productId').val();
                var billNo = $('#purchaseBillNo1').find(":selected").text(); //alert(billNo); 
                var branchId  = <?php echo $gnrBranchId; ?>;
                $("#productQntty").val('');
                var csrf = "<?php echo csrf_token(); ?>";  
            $.ajax({
                type: 'post',
                url: './calculationStockForBrnNhedo',
                data: {productId:productId,branchId:branchId,_token:csrf},
                dataType: 'json',
                success: function(data) {
                    //alert(JSON.stringify(data));
                    $("#currentStock").val(data);
                        stockQuantity = data;
                    $("#currentStock").trigger('input');
                }
            });
            $.ajax({
                type: 'post',
                url: './calculationInvAverageprice',
                data: {productId:productId,branchId:branchId,_token:csrf},
                dataType: 'json',
                success: function(data) {
                    //alert(JSON.stringify(data));
                        toralPriceAllTbl  = data;
                    $("#currentStock").trigger('input');
                }
            });       
        });

        $('#currentStock , #averagePriceInput').on('input',function(){
                averagePrice = parseFloat(Math.round(toralPriceAllTbl/stockQuantity));
            $("#averagePriceInput").val(averagePrice||0);
            $('#currentStock').css({background:'#ffd1b3'});
        });

        // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
            $('#productQntty').on('input', function(){
            var givenProductQuantity = parseFloat($('#productQntty').val()); //alert(givenProductQuantity);
            var perProStock = parseFloat(stockQuantity); //alert(perProStock);
            if(givenProductQuantity > perProStock){
              alert("Product quanity should not be more than \n = "+perProStock );
                $('#productQntty').val(perProStock);
            }
        });
    //end stock calcualtion=============================================

    var i=0;
    $('#addProduct').click(function(){
        var testx = '';

    var productId       = $('#productId').val();
    var productName     = $('#productId option:selected').text();
    var productQntty    = parseFloat($('#productQntty').val());
    var csrf            = "<?php echo csrf_token(); ?>";
        i++;
    if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
    else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;} 
    
        var productQuantity = $("#productQntty").val();
        var totalPricePerPro = parseFloat(productQuantity*averagePrice);

        var getProductId    = $("#productId").val();
        var getProductQty   = parseInt($("#productQntty").val());
        //var lallaa = $("#addProductTable tr").length;
            
            $('#addProductTable tr.forhide').each(function() {
                var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
                if(cellText==getProductId){
                var getApnProductQty    = parseFloat($(this).closest('tr').find('.apnQnt').val());
                var totalQtyforsamePro  = parseFloat(getApnProductQty)+parseFloat(getProductQty);

                if(totalQtyforsamePro>stockQuantity){
                        totalQtyforsamePro = stockQuantity;
                    }
                $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);
                var perProPrice = $(this).closest('tr').find('.productPrice').val();
                var totalPrice = parseFloat(perProPrice)*parseFloat(totalQtyforsamePro);
                $(this).closest('tr').find('.totalTransferAmount').val(totalPrice);
                //$(this).closest('tr').addClass('checked');

                testx = 'yes';
                }
            });

        if(testx!=='yes'){
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td class=""><input type="number" name="productPrice[]" class="form-control name_list  input-sm productPrice" id="productPrice'+i+'" style="text-align:center; cursor:default" value="'+averagePrice+'" readonly/></td><td class=""><input type="number" name="proTotalPrice[]" class="form-control  name_list input-sm  totalAmount" id="totalCostPrice'+i+'" style="text-align:center; cursor:default"  value="'+totalPricePerPro+'" readonly/></td><td class=""><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
        }
            $('#productQntty').val('');
            $('#productId').val('');
            $('#productGroupId').val('');
            $('#productCategoryId').val(''); 
            $('#productSubCategoryId').val('');
            $('#productBrandId').val('');

        // onclick add button total amount summation  
        var sumTotal = 0;
            $(".totalAmount").each(function() {
                sumTotal += Number($(this).val());
                $('#totalTransferAmount').val(sumTotal);
                $('#proTotalPriceShow').text(sumTotal);
            });
// onclick add button quantity summation          
        var sum = 0;
            $(".apnQnt").each(function() {
                sum += Number($(this).val());
                $('#totalTransferQuantity').val(sum);
                $('#totalQuantityFooter').text(sum);
            });
            $("#currentStock").val('');
            $("#averagePriceInput").val('');   
      });  

      $(document).on('click', '.btn_remove', function(){
           var removeQntty = parseFloat($(this).closest('tr').find('.apnQnt').val());
           var totalQnt    = parseFloat($("#totalQuantityFooter").text());
           var qntAfterRemove = totalQnt-removeQntty;
          $('#totalQuantityFooter').text(qntAfterRemove);
          $('#totalTransferQuantity').val(qntAfterRemove);
          var removeAmount = parseFloat($(this).closest('tr').find('.totalAmount').val());
          var totalAmount    = parseFloat($("#totalTransferAmount").val());
          var totalAmountAfterRemove = totalAmount-removeAmount;
          $('#totalTransferAmount').val(totalAmountAfterRemove);
          $('#proTotalPriceShow').text(totalAmountAfterRemove);

           var button_id = $(this).attr("id");
           // $('#row'+button_id+'').remove();
           $(this).closest('tr').remove();   
      }); 
      
});//end document.readyfunction
  
  
$( document ).ready(function() {
    
$(document).on('click', '.edit-modal', function() {
  $('#addProductTable tbody .forEmpty').remove();
    
    //Send ajax request for values
    var id = $(this).data('id');
    var csrf = "<?php echo csrf_token(); ?>";  

    $.ajax({
            type: 'post',
            url: './deitedInvTransferDataShow',
            data: {id:id, _token:csrf},
            dataType: 'json',
            success: function(data) {
                //alert(JSON.stringify(data));
                $('#transferBillNo').val(data[0].transferBillNo);
                $('#orderNo').val(data[0].orderNo);
                $('#transferOrderNo').val(data[0].transferOrderNo);
                $('#brancIdFrom').val(data[0].brancIdFrom);
                $('#branchIdTo').val(data[0].branchIdTo);
                $('#totalTransferQuantity').val(data[0].totalTransferQuantity);
                $('#totalTransferAmount').val(data[0].totalTransferAmount);
                $('#totalQuantityFooter').text(data[0].totalTransferQuantity);
                $('#proTotalPriceShow').text(data[0].totalTransferAmount);
            }
        }); 

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
  
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
  
var transferIdForCountRow = $(this).data('id');
var csrf = "<?php echo csrf_token(); ?>";
$.ajax({
         type: 'post',
         url: './trnasferEditAppendRows',
         data: {
            '_token': csrf,
            'id': transferIdForCountRow
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));transferDetailsTables
$.each(data, function (key, value) { 
        if (key == "transferDetailsTables") {
          var i = 0; 
        $.each(value, function (key1, value1) {
          //alert(value1.productId); 
          var string="<tr id='row"+i+"' class='forEmpty forhide'>";
            string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"' disabled><option>select Product</option></select></td>";
            string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt"+i+"' style='text-align:center;'  value='"+value1.transferQuantity+"' autocomplete='off'/></td>";
            string+="<td class=''><input type='number' name='productPrice[]' class='form-control name_list input-sm perProPriceEdtRow productPrice' id='productPrice'"+i+"' style='text-align:center; cursor:default' value='"+value1.price+"' readonly/></td>";
            string+="<td class=''><input type='number' name='proTotalPrice[]'' class='form-control name_list input-sm totalAmount' id='totalCostPrice"+i+"' style='text-align:center; cursor:default'  value='"+value1.totalPrice+"' readonly/></td>";
            string+="<td><input type='text' name='useDetailsTablesId[]' class='form-control input-sm name_list hidden deleteIdSend' style='text-align:center; cursor:default' value='"+value1.id+"' /><a href='javascript:;' name='remove' id='"+i+"' class='btn_remove' style='width:80px'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></a></td></td>";
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

    var productDetailsRowLength = data.transferDetailsTables.length;
      //alert(thakur);
      for(var i=0; i<productDetailsRowLength; i++){
        $('#productId5'+i).val(data['transferDetailsTables'][i].transferProductId);
      }
        },
        error: function( data ){
        
        }
    }); 
});

//each row quantity form
$(document).on('click', '.edit-modal', function() { 
// On hover get the tr length
    $("#useForm").one('mouseover',function(){ 
        var rowsCurStock = $('#addProductTable tbody tr.forhide').length;
        var getPresentValue = 0;

        if(rowsCurStock > 0){
          $(".apnQnt").on('focus',function(){
              getPresentValue = parseInt($(this).closest('tr').find('.apnQnt').val());
        });
        //Current stock per edit row
        //ajax
        $(".apnQnt").on('input',function(){
          var productId         = $(this).closest('tr').find('.apendSelectOption').val();  
          var branchId          = <?php echo $grnBranchId; ?>; 
          var csrf              = "<?php echo csrf_token(); ?>"; 
          var id                = $(this).attr('id'); //alert(id);
          var totalAmountId     = $(this).closest('tr').find('.totalAmount').attr('id');
          var givenalue         = $(this).closest('tr').find('.apnQnt').val();
          var perProAverPrice   = $(this).closest('tr').find('.perProPriceEdtRow').val();
          var checkWhichIdBig   = 0;
          $.ajax({
                  type: 'post',
                  url: './calculationStockForBrnNhedo',
                  data: { productId:productId, branchId:branchId, _token:csrf },
                  dataType: 'json',
                  success: function(data) {

                            var stockQtyTisPro = data;
                            var totalStockQtyTisPro = parseInt(data+getPresentValue); 
                      
                            $("#"+totalAmountId).val(parseFloat(perProAverPrice*givenalue));
                            $(".totalAmount").trigger('input');
                          if(parseFloat(givenalue)>parseInt(totalStockQtyTisPro)){
                            alert("Product quanity should not be more than \n = "+stockQtyTisPro );
                              // alert('Use quantity \nshould not be more than current stock');
                              $("#"+id).val(getPresentValue);
                              $("#currentStock").val(stockQtyTisPro);
                              $("#currentStock").trigger('input');
                              $(".apnQnt").trigger('input');
                          }
                  }
              });  
        });
        //ajax end
        }
    });
  });
//end each stock quantity


$("#addProductTable").hover(function(){
  $(".totalAmount").on('input',function(){
    var sumTotal1 = 0;
        $(".totalAmount").each(function() {
            sumTotal1 += Number($(this).val());
            $('#totalTransferAmount').val(sumTotal1);
            $('#proTotalPriceShow').text(sumTotal1);
        });
      });

    $(".apnQnt").on('input',function(){
    var sum1 = 0;
      $('.apnQnt').each(function() {
        sum1 += Number($(this).val()); 
        $('#totalTransferQuantity').val(sum1);
        $('#totalQuantityFooter').text(sum1);
      });
    });

});


// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {
    $('.apendSelectOption').removeAttr('disabled');
$.ajax({
         type: 'post',
         url: './editInvTransfer',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
          alert(JSON.stringify(data));
    if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
        if (data.errors) {
            if (data.errors) {
            if (data.errors['branchIdTo']) {
                $('#branchIdToe').empty();
                $('#branchIdToe').append('<span class="errormsg" style="color:red;">'+data.errors.branchIdTo+'</span>');
                return false;
            }
          }
        }
    else{ 
    $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        $('.item' + data["updateDatas"][0].id).replaceWith(
                                    "<tr class='item" + data["updateDatas"][0].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["updateDatas"][0].id + 
                                                                    "</td><td>" + data.dateFromarte + 
                                                                    "</td><td>" + data["updateDatas"][0].transferBillNo + 
                                                                    "</td><td style='text-align: left; padding-left: 10px;'>" + data["brnchFromName"].name + 
                                                                    "</td><td style='text-align: left; padding-left: 10px;'>" + data["brnchToName"].name + 
                                                                    "</td><td style='text-align: right; padding-right: 10px;'>" + data["updateDatas"][0].totalTransferQuantity + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
          }
        },
        error: function( data ){
            // Handle error
            alert('data.errors');
            
        }
    });
});

$('select').on('change', function (e) {
        var branchIdTo = $("#branchIdTo").val();
        if(branchIdTo){$('#branchIdToe').hide();}else{$('#branchIdToe').show(); }
    }); 
  
$(document).on('click', '.delete-modal', function() {
  $('#MSGE').empty();
  $('#MSGS').empty();
  $('#footer_action_button2').text(" Yes");
  $('#footer_action_button2').removeClass('glyphicon glyphicon-check');
  //$('#footer_action_button').addClass('glyphicon-trash');
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
    url: './deleteInvTransferItem',
    data: {
      '_token': $('input[name=_token]').val(),
      'id': $('.id').text()
    },
    success: function(data) {
    //alert(JSON.stringify(data));
      $('.item' + $('.id').text()).remove();
    },
        error: function( data ){
        //alert('hi');
        }
  });
});

  $("input").keyup(function(){
    var productQntty = $("#productQntty").val();
    if(productQntty){$('#qnttyError').hide(); $('#productQuantitye').hide();}else{$('#qnttyError').show(); $('#productQuantitye').show();}
  });
  $('select').on('change', function (e) {
      var branchId = $("#branchId").val();
      if(branchId){$('#branchIde').hide();}else{$('#branchIde').show(); }
    
  }); 


});//ready function end

</script>
{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function(){

        $("#supplierId").change(function(){ 
             var supplierId = $('#supplierId').val(); //alert(supplierId);
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './invPurOnCngSupl',
                  data: { _token: csrf, supplierId:supplierId},
                  dataType: 'json',   
                  success: function( _response ){
                    //alert(JSON.stringify(_response));
                    
                        $("#productId").empty();
                        $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName") {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            }  

                        if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        }

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "contactPerson") {
                            $('#contactPerson').val('');
                            $.each(value, function (key1, value1) {
                                //alert(value1);
                                $('#contactPerson').val(value1);
                            })    
                        }
       
                    });

                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
            
        }); 

        $("#productGroupId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var supplierId     = $('#supplierId').val();
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './invPurchaseOnCngGrp',
                  data: {
                    productGroupId:productGroupId,
                    _token: csrf,
                    supplierId:supplierId
                },
                  dataType: 'json',   
                  success: function( _response ){
                    //alert(JSON.stringify(_response));
                    $("#productId").empty();
                        $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }
       
                    });

                },
                error: function(_response){
                  alert("error");
                }

              });//End Ajax
            
        }); //End Change Product Group

        //Change Category
        $("#productCategoryId").change(function(){ 
             var productGroupId     = $('#productGroupId').val();
             var productCategoryId  = $('#productCategoryId').val(); //alert(productCategoryId);
             var supplierId         = $('#supplierId').val();
             var csrf               = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './invPurchaseOnCngCtg',
                  data: {
                    productGroupId:productGroupId, 
                    productCategoryId:productCategoryId,
                    supplierId:supplierId,
                    _token: csrf
                },
                  dataType: 'json',   
                  success: function( _response ){
                    //alert(JSON.stringify(_response));
                    $("#productId").empty();
                        $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }
       
                    });
                        
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
              
        }); /*End Change Category*/

        //Change sub Category
        $("#productSubCategoryId").change(function(){ 
             var productGroupId             = $('#productGroupId').val();
             var productCategoryId          = $('#productCategoryId').val(); 
             var supplierId                 = $('#supplierId').val();
             var productSubCategoryId       = $('#productSubCategoryId').val();
             var csrf                       = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './invPurchaseOnCngSubCtg',
                  data: {
                    productGroupId: productGroupId, 
                    productCategoryId: productCategoryId,
                    supplierId: supplierId,
                    productSubCategoryId: productSubCategoryId,
                    _token: csrf
                },
                  dataType: 'json',   
                  success: function( _response ){
                    //alert(JSON.stringify(_response));
                    $("#productId").empty();
                        $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                        
                    $.each(_response, function (key, value) {

                            if (key == "productName" && supplierId!=='') {
                                $.each(value, function (key1, value1) {
                                    $('#productId').append("<option value='"+ key1+"'>"+value1+"</option>");
                                })
                            } 

                            if (key == "groupName") {
                        $("#productGroupId").empty();
                        $("#productGroupId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productGroupId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                           })         
                        } 

                        if (key == "catagoryName") {
                        $("#productCategoryId").empty();
                        $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })    
                            })
                        }

                        if (key == "SubcatagoryName") {
                        $("#productSubCategoryId").empty();
                        $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productSubCategoryId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }

                        if (key == "brandName") {
                        $("#productBrandId").empty();
                        $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                            $.each(value, function (key1, value1) {
                                $.each(value1, function (key2, value2) {
                                    $('#productBrandId').append("<option value='"+ value2.id+"'>"+value2.name+"</option>");
                                })
                            })    
                        }
       
                    });
                        
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
              
        }); /*End Change Category*/
       
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
div.hr hr {
  height: 1px;
  background-color: #e5e5e5;
}
</style>
 



