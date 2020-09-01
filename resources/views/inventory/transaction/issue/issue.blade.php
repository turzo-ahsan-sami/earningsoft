@extends('layouts/inventory_layout')
@section('title', '| Issue')
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
              <a href="{{url('addIssue/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Issue</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">ISSUE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
           /* $("#invTrnsIssueView").dataTable().yadcf([
               "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
            ]);*/

           

             $("#invTrnsIssueView").dataTable({
                   "oLanguage": {
                      "sEmptyTable": "No Records Available",
                      "sLengthMenu": "Show _MENU_ ",                  
                    },
                  "lengthMenu": [ [25, 50, -1], [25, 50, "All"] ]
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="invTrnsIssueView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Order No</th>
                <th>Issue Order No</th>
                <th>Branch Name</th>
                <th>Total Quantity</th>
                <th>Total Amount</th>
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($issues as $issue)
                  @php
                    $branchName = DB::table('gnr_branch')->where('id',$issue->branchId)->value('name');
                  @endphp

                    <tr class="item{{$issue->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{$issue->issueDate}}</td>
                      <td>{{$issue->issueBillNo}}</td>
                      <td>{{$issue->orderNo}}</td>
                      <td>{{$issue->issueOrderNo}}</td>
                      <td style="text-align: left; padding-left: 5px;">{{$branchName}}</td>
                      <td >{{$issue->totalQuantity}}</td>
                      <td style="text-align: right; padding-right: 5px;">{{$issue->totalAmount}}</td>
                      <td class="text-center" width="80">
                          <a href="javascript:;" class="forPurchaseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$issue->id}}">
                            <span class="fa fa-eye"></span>
                        </a>&nbsp
                       <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$issue->id}}" data-issuebillno="{{$issue->issueBillNo}}" data-orderno="    {{$issue->orderNo}}" data-issueorderdate="{{$issue->issueOrderNo}}"  data-projectid="{{$issue->projectId}}" data-projecttypeid="{{$issue->projectTypeId}}" data-branchid="{{$issue->branchId}}" data-issuedate="     {{$issue->issueDate}}" data-createby="{{$issue->createdBy}}" data-purchasedate="{{$issue->purchaseDate}}" data-totalquantity="{{$issue->totalQuantity}}" data-totalamount="{{$issue->totalAmount}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$issue->id}}">
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
                            {!! Form::label('issueBillNo', 'Issue No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('issueBillNo', null, ['class' => 'form-control', 'id' => 'issueBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('orderNo', 'Order No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('orderNo', $value = null, ['class' => 'form-control', 'id' => 'orderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('issueOrderNo', 'Issue Order No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('issueOrderNo', $value = null, ['class' => 'form-control', 'id' => 'issueOrderNo', 'type' => 'text','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                         <div class="form-group">
                            {!! Form::label('projectId', 'Project:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                               
                                    $projectNmaes  = DB::table('gnr_project')->select('id','name','projectCode')->orderBy('projectCode')->get();
                                   
                                ?> 
                                <select name="projectId" id="projectId" class="form-control">
                                    <option value="">Select Project</option>
                                      @foreach($projectNmaes as $projectNmae) 
                                        <option value="{{$projectNmae->id}}">{{$projectNmae->projectCode.'-'.$projectNmae->name}}</option>
                                      @endforeach 
                                </select>  
                                <p id='projectIde' style="max-height:3px;"></p>
                            </div>
                               
                        </div>

                         <div class="form-group">
                            {!! Form::label('projectTypeId', 'Project Type:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                <?php 
                               
                                    $projectTypeNmaes  = DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
                                   
                                ?> 
                                <select name="projectTypeId" id="projectTypeId" class="form-control">
                                    <option value="">Select Project Type</option>
                                      @foreach($projectTypeNmaes as $projectTypeNmae) 
                                        <option value="{{$projectTypeNmae->id}}">{{$projectTypeNmae->projectTypeCode.'-'.$projectTypeNmae->name}}</option>
                                      @endforeach 
                                </select>  
                                <p id='projectTypeIde' style="max-height:3px;"></p>
                            </div>
                               
                        </div>
                        
                   </div>    
               <div class="col-md-6"> 

                  <div class="form-group">
                    {!! Form::label('branchId', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                    <?php 
                        $branchNames = array(''=>'Select Branch')+DB::table('gnr_branch')->orderBy('name', 'ASC')->pluck('name','id')->all(); 
                    ?>
                        {!! Form::select('branchId', ($branchNames), null, ['class' => 'form-control', 'id' => 'branchId']) !!}
                        <p id='branchIde' style="max-height:3px;"></p>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('totalQuantity', 'Issue Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::text('totalQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalQuantity', 'type' => 'text','autocomplete'=>'off', 'readonly']) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::text('totalAmount', $value = null, ['class' => 'form-control', 'id' => 'totalAmount', 'type' => 'text','autocomplete'=>'off', 'readonly']) !!}
                    </div>
                </div>



                <div class="form-group hidden">
                    {!! Form::label('averagePriceInput', 'Average Price:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::text('averagePriceInput', $value = null, ['class' => 'form-control', 'id' => 'averagePriceInput', 'type' => 'text','autocomplete'=>'off', 'readonly']) !!}
                    </div>
                </div> 

              <div class="form-group hidden">
                 {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
                 <div class="col-sm-8 text-right" style="">
                  {!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                  <a href="{{url('viewUse/')}}" class="btn btn-danger closeBtn">Close</a>
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
                                        <td>
                                            <input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='' placeholder='Insert Item' min="1"/>
                                            
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td><input style='text-align:center;border-radius:0;width:80px;' id='addProduct' class='btn btn-primary btn-xs'
                                            type='button' name='productAddButton' value='Add Product'/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="hidden" id="productIdError" style="color: red;">Product Field Is Requrired</p>
                                            <p class="hidden" id="productIde" style="color: red;">Product Name and Quantity is required</p>
                                        </td>
                                        <td>
                                            <p class="hidden" id="qnttyError" style="color: red;">Product Qty Is Required</p>
                                            <p class="hidden" id="productQuantitye" style="color: red;"></p>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
@include('inventory/transaction/issue/issueDetails')
@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){ 

   function num(argument){
           return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

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
            $("#averagePriceInput").val(averagePrice);
            $('#currentStock').css({background:'#ffd1b3'});
        });

       

        // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
            $('#productQntty').on('input', function(){
            var givenProductQuantity = parseFloat($('#productQntty').val()); //alert(givenProductQuantity);
            var perProStock = parseFloat(stockQuantity); //alert(perProStock);
            if(givenProductQuantity > perProStock){
              alert("Product Quantity should not be more than\n = "+perProStock);
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
    var csrf = "<?php echo csrf_token(); ?>";
        i++;
    if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
    else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;} 
    
        var productQuantity = $("#productQntty").val();
        var totalPricePerPro = parseFloat(productQuantity*averagePrice);

        var getProductId    = $("#productId").val();
        var getProductQty   = $("#productQntty").val();
        //var lallaa = $("#addProductTable tr").length;
            
            $('#addProductTable tr.forhide').each(function() {
                var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
                if(cellText==getProductId){
                var getApnProductQty = parseFloat($(this).closest('tr').find('.apnQnt').val());
                var totalQtyforsamePro = parseFloat(getApnProductQty)+parseFloat(getProductQty);

                if(totalQtyforsamePro>getApnProductQty){
                        totalQtyforsamePro = stockQuantity; 
                    }
                $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);
                var perProPrice = $(this).closest('tr').find('.productPrice').val();
                var totalPrice = parseFloat(perProPrice)*parseFloat(totalQtyforsamePro);
                $(this).closest('tr').find('.totalAmount').val(totalPrice);
               

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
                $('#totalAmount').val(sumTotal);
                $('#proTotalPriceShow').text(sumTotal);
            });
// onclick add button quantity summation          
        var sum = 0;
            $(".apnQnt").each(function() {
                sum += Number($(this).val());
                $('#totalQuantity').val(sum);
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
          $('#totalQuantity').val(qntAfterRemove);
          var removeAmount = parseFloat($(this).closest('tr').find('.totalAmount').val());
          var totalAmount    = parseFloat($("#totalAmount").val());
          var totalAmountAfterRemove = totalAmount-removeAmount;
          $('#totalAmount').val(totalAmountAfterRemove);
          $('#proTotalPriceShow').text(totalAmountAfterRemove);

           var button_id = $(this).attr("id");
          $(this).closest('tr').remove();   
      }); 
      
});//end document.readyfunction
  
  
$( document ).ready(function() {

  function num(argument){
           return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          }
    
$(document).on('click', '.edit-modal', function() {
  if(hasAccess('editInvIssue')){
  $('#addProductTable tbody .forEmpty').remove();
    
    //Send ajax request for values
    var id = $(this).data('id');
    var csrf = "<?php echo csrf_token(); ?>";  

   
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

    $('#issueBillNo').val($(this).data('issuebillno'));
    $('#orderNo').val($(this).data('orderno'));
    $('#issueOrderNo').val($(this).data('issueorderdate'));
    $('#projectId').val($(this).data('projectid'));
    $('#projectTypeId').val($(this).data('projecttypeid'));
    $('#branchId').val($(this).data('branchid'));

    $('#totalQuantity').val($(this).data('totalquantity'));
    $('#totalAmount').val($(this).data('totalamount'));

    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
  
var issueIdForCountRow = $(this).data('id');
var csrf = "<?php echo csrf_token(); ?>";
$.ajax({
         type: 'post',
         url: './issueEditAppendRows',
         data: {
            '_token': csrf,
            'id': issueIdForCountRow
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
$.each(data, function (key, value) { 
        if (key == "useDetailsTables") {
          var i = 0; 
        $.each(value, function (key1, value1) {
          //alert(value1.productId); 
          var string="<tr id='row"+i+"' class='forEmpty forhide'>";
            string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"' disabled><option>select Product</option></select></td>";
            string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt"+i+"' style='text-align:center;'  value='"+value1.issueQuantity+"' autocomplete='off'/></td>";
            string+="<td class=''><input type='number' name='productPrice[]' class='form-control name_list input-sm perProPriceEdtRow productPrice' id='productPrice'"+i+"' style='text-align:center; cursor:default' value='"+value1.price+"' readonly/></td>";
            string+="<td class=''><input type='number' name='proTotalPrice[]'' class='form-control name_list input-sm totalAmount' id='totalCostPrice"+i+"' style='text-align:center; cursor:default'  value='"+value1.totalPrice+"' readonly/></td>";
            string+="<td><input type='text' name='useDetailsTablesId[]' class='form-control input-sm name_list hidden deleteIdSend' style='text-align:center; cursor:default' value='"+value1.id+"' /><a href='javascript:;' name='remove' id='"+i+"' class='btn_remove' style='width:80px'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></a></td></td>";
            string+="</tr>";
            $('#addProductTable').append(string);
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

    var productDetailsRowLength = data.useDetailsTables.length;
      //alert(thakur);
        for(var i=0; i<productDetailsRowLength; i++){
        $('#productId5'+i).val(data['useDetailsTables'][i].issueProductId);
      }
        },
        error: function( data ){
        
        }
    }); 
}
});

//each row quantity form
$(document).on('click', '.edit-modal', function() { 
// On hover get the tr length
    $("#useForm").one('mouseover',function(){ 
        var rowsCurStock = $('#addProductTable tbody tr.forhide').length;
        var getPresentValue = 0;

        if(rowsCurStock > 0){
          $(".apnQnt").on('focus',function(){
              getPresentValue = $(this).closest('tr').find('.apnQnt').val();
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
                  data: {productId:productId,branchId:branchId,_token:csrf},
                  dataType: 'json',
                  success: function(data) {

                            var stockQtyTisPro = data;
                            var totalStockQtyTisPro = parseFloat(data+getPresentValue);
                      
                            $("#"+totalAmountId).val(parseFloat(perProAverPrice*givenalue));
                            $(".totalAmount").trigger('input');
                          if(parseFloat(givenalue)>parseFloat(stockQtyTisPro)){
                              alert('Use quantity should not be more than\n = '+stockQtyTisPro);
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
            $('#totalAmount').val(sumTotal1);
            $('#proTotalPriceShow').text(sumTotal1);
        });
      });

    $(".apnQnt").on('input',function(){
    var sum1 = 0;
      $('.apnQnt').each(function() {
        sum1 += Number($(this).val()); 
        $('#totalQuantity').val(sum1);
        $('#totalQuantityFooter').text(sum1);
      });
    });

});


// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {
    $('.apendSelectOption').removeAttr('disabled');
$.ajax({
         type: 'post',
         url: './editInvIssue',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
        
    if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
        if (data.errors) {
            if (data.errors['branchId']) {
                $('#branchIde').empty();
                $('#branchIde').append('<span class="errormsg" style="color:red;">'+data.errors.branchId+'</span>');
                return false;
            }
        }
    else{ 
    $('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        $('.item' + data["updateDatas"][0].id).replaceWith(
          "<tr class='item" + data["updateDatas"][0].id + "'><td  class='text-center slNo'>" + data.slno +
          "</td><td class='hidden'>" + data["updateDatas"][0].id +  "</td><td>" + data.dateFromarte +"</td><td>" + data["updateDatas"][0].issueBillNo +"</td><td>" + data["updateDatas"][0].orderNo + 
          "</td><td>" + data["updateDatas"][0].issueOrderNo +"</td><td>" + data["updateDatas"][0].projectId +"</td><td>" + data["projectTypeId"]+ "</td><td style='text-align: left; padding-left: 5px;'>" + data["brnchName"].name +
          "</td><td style='text-align: right; padding-right: 5px;'>" + data["updateDatas"][0].totalQuantity +
          "</td><td style='text-align: right; padding-right: 5px;'>" + data["updateDatas"][0].totalAmount + 
          "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
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
  



$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deleteIssueItem')){
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
}
});

$('.modal-footer').on('click', '.delete', function() {
  $.ajax({
    type: 'post',
    url: './deleteIssueItem',
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

//purchase detaisl
       function num(argument){
           return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
          }
      $(document).on('click', '.forPurchaseDetailsModel', function() {
        if(hasAccess('viewIssueData')){
         $('.modal-header').css({"background-color":"white"});
         $('#swhoAppendRows tbody').empty();
         $('#issueDetailsModel').modal('show');
         $('.modal-dialog').css('width','50%');


             var id = ($(this).data('id'));
              var crsf = ($(this).data('token'));
              //alert(id);alert(crsf);
              $.ajax({
                   type: 'post',
                   url: './viewIssueData',
                   data: {
                      '_token': $('input[name=_token]').val(),
                      'id': id
                   },
                   dataType: 'json',
          success: function( data ){

                $('#issueNoHead').text(data['issueData'].issueBillNo);   
                $('#projectHead').text(data['projectId']);
                $('#projectTypeHead').text(data['projectTypeId']);
                $('#orderNoHead').text(data['issueData'].orderNo);
                $('#issueOrderNoHead').text(data['issueData'].issueOrderNo);
                $('#branchNameHead').text(data['branchName']);
                $('#issueDateHead').text(data.issueDate);

                if(data['projectTypeId']==null){
                  $('#projectType1').hide();
                  $('#projectView').hide();
                  $('#projectTypeHead').hide();
                }else{
                  $('#projectType1').show();
                  $('#projectView').show();
                  $('#projectTypeHead').show();

                  }

              /*  if(data['issueData'].orderNo<=0){
                  $('#orderNoHide').hide();
                  $('#coloneHide').hide();
                  $('#orderNoHead').hide();
                }else{
                  $('#orderNoHide').show();
                  $('#coloneHide').show();
                  $('#orderNoHead').show();

                }


                if(data['issueData'].issueOrderNo<=0){
                  $('#issueOrderNoHide').hide();
                  $('#coloneHideview').hide();
                  $('#issueOrderNoHead').hide();
                }else{
                  $('#issueOrderNoHide').show();
                  $('#coloneHideview').show();
                  $('#issueOrderNoHead').show();

                }

                if(data['projectId']==null){
                  $('#projectNameViewHide').hide();
                  $('#coloneViewHide').hide();
                  $('#projectHead').hide();
                }else{
                  $('#projectNameViewHide').show();
                  $('#coloneViewHide').show();
                  $('#projectHead').show();

                }*/

        $(document).on('click', '#print', function(){ 
            $("#issuePrint").show();
        
        });
            /* Start Print */
              
            $('#projectName').text(data['projectId']);
            $('#projectType').text(data['projectTypeId']);
            $('#issueOrderName').text(data['issueData'].issueOrderNo);
            $('#branch').text(data['branchName']);
            $('#createPerson').text(data['employeeName'].emp_name_english);
            $('#createPersonId').text(data['employeeName'].emp_id);
            $('#createPersonDeg').text(data['employeeDege']);
            $('#issueDate').text(data.issueDate);
            $('#issueNo').text(data['issueData'].issueBillNo);
            $('#issueOrderNoerew').text(data['issueData'].orderNo);

         /* Start Print */

       $.each(data, function (key, value) {
         if (key == "issueDetailsTables") {
           var i = 1;
         $.each(value, function (key1, value1) {
            var string="<tr>";
                string+="<td style='text-align:center' class='slNoUse'>"+i+"</td>";
                string+="<td class='productName' style='text-align:left'id='rowAppendView"+i+"'>"+data.productName[key1]+"</td>";
                string+="<td class='productPricePerPc'>"+value1.issueQuantity+"</td>";
                string+="<td class='productPricePerPc'>"+num(value1.price)+"</td>";
                string+="<td class='productPricePerPc'>"+num(value1.totalPrice)+"</td>";
                string+="</tr>";
                $('#swhoAppendRows').append(string);

              i++;
            })
        }

   
    if (key == "issueDetailsTables") {
          var j = 1;
          $.each(value, function (key2, value2) {

               var strin="<tr>";
                  strin+="<td style='text-align:center' class='slNoUse'>"+j+"</td>";
                  strin+="<td class='productName' style='text-align:left; font-size:14px;'>"+data.productName[key2]+"</td>";
                  strin+="<td class='productPricePerPc'style='font-size:14px;'>"+value2.issueQuantity+"</td>";
                  strin+="<td class='productPricePerPc'style='font-size:14px; text-align:right; padding-right:5px;'>"+num(value2.price)+"</td>";
                  strin+="<td class='productPricePerPc'style='font-size:14px; text-align:right; padding-right:5px;'>"+num(value2.totalPrice)+"</td>";
                  strin+="</tr>";
                  $('#issuePrint').append(strin);
                  
                j++;

             })
          } 
  });

    $('#totalQtyDetails').html(data['issueData'].totalQuantity);
    $('#totalAmountDetails').html(num(data['issueData'].totalAmount));
    
    $('#totalQtyPrint').html(data['issueData'].totalQuantity);
    $("#totalAmountPrint").html(num(data['issueData'].totalAmount));

        },
     error: function( data ){
        
        }
      });
}
   });
 });

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

{{-- Print Page --}}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#print").click(function(event) {

            $("#hiddenTitle").show();
            $("#hiddenInfo").show();
            $("#issuePrint").removeClass('table table-striped table-bordered');

        var mainContents = document.getElementById("printingContent").innerHTML;
  var headerContents = '';

  var printStyle = '<style>#issuePrint{float:none !important;height:auto;padding:0px; margin-top:10px; margin-bottom:10px;width:100%;font-size:11px;border:1pt ash;page-break-inside:auto;} thead tr th{text-align:center;vertical-align: middle;padding:3px;font-size:10px} tbody tr td {text-align:center;vertical-align: middle;padding:3px;font-size:10px}  tbody tr{ page-break-inside:avoid; margin-bottom:10px; page-break-after:auto} </style><style media="print">@page{size:portrait;}</style><style>@media print { #tableDiv { -moz-overflow: visible !important;}}</style><style>@page:first{tr:nth-of-type(4n){page-break-after: always;}}</style>';

        printContents = '<div id="order-details-wrapper" style="-moz-overflow: visible !important;"> ' + printStyle + mainContents+'</div>';


var win = window.open('','printwindow');
win.document.write(printContents);
win.print();
location.reload();
win.close();

});
    });

</script>
{{-- EndPrint Page --}}


<script type="text/javascript">
$(document).ready(function(){ 

   
function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

  $("#projectId").change(function(){
            
           var project = $(this).val();
           var csrf = "<?php echo csrf_token(); ?>";

           $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProject',
                data: {projectId:project,_token: csrf},
                dataType: 'json',
                success: function( data ) {

                    $("#projectTypeId").empty();
                    $("#projectTypeId").prepend('<option selected="selected" value="">Select Project Type</option>');

                 $.each(data['projectTypeList'], function (key, projectObj) {
                                
                           $('#projectTypeId').append("<option value='"+ projectObj.id+"'>"+pad(projectObj.projectTypeCode,3)+"-"+projectObj.name+"</option>");                      
                    });
                 },
                error: function(_response) {
                    alert("error");
                }

           });/*End Ajax*/

        });/*End Change Project*/
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
 



