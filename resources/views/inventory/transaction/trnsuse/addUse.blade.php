@extends('layouts/inventory_layout')
@section('title', '| Use')
@section('content')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$branchCode = DB::table('gnr_branch')->where('id', $gnrBranchId)->value('branchCode');
?>
<div class="row add-data-form">
    <div class="col-md-12">
    		<div class="col-md-1"></div>
    			<div class="col-md-10 fullbody">
    				<div class="viewTitle" style="border-bottom: 1px solid white;">
            			<a href="{{url('viewUse/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
            			</i>Use List</a>
        			</div>
        		<div class="panel panel-default panel-border">
                				<div class="panel-heading">
                    				<div class="panel-title">Use</div>
                				</div>
                	<div class="panel-body">
                    {!! Form::open(array('url' => 'addProductUseItem', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
                     <div class="row">
                     <div class="col-md-12">
                     <div class="col-md-6">     
                         <div class="form-group">
                            {!! Form::label('useBillNo', 'Use No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                               <?php 
									$useMaxId = DB::table('inv_tra_use')->max('useNumber')+1;
									$valueForField = 'US.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $useMaxId);
								?>
                                {!! Form::text('useBillNo', $value = $valueForField, ['class' => 'form-control', 'id' => 'useBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('requisitionNo', 'Requisition No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('requisitionNo', $value = null, ['class' => 'form-control', 'id' => 'requisitionNo', 'type' => 'text','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('requisition', 'Requisition:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('requisition', $value = null, ['class' => 'form-control', 'id' => 'requisition', 'type' => 'text','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('date', 'Date:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('date', $value = null, ['class' => 'form-control', 'id' => 'date', 'type' => 'text','autocomplete'=>'off','readonly']) !!}
                            </div>
                        </div>

					</div>    
					<div class="col-md-6"> 
                        <div class="form-group">
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

                        <div class="form-group">
                            {!! Form::label('useType', 'Use place:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">

                                {!! Form::radio('useType', 'employee', false, array('class' => '')) !!}
                                {!! Form::label('useType', 'Employee', ['class' => 'control-label']) !!}

                              <span style="padding-left: 10px">    
                                {!! Form::radio('useType', 'room', false, array('class' => '')) !!}
                                {!! Form::label('room', 'Room / Department', ['class' => 'control-label']) !!}
                              </span>
                                <p id="errorEmptyUsePlace" style="color:red" hidden="hidden">This field is required</p>
                            </div>
                        </div>

                        <div class="form-group useTypeCls" id="hidSoWemployee">
							{!! Form::label('employeeId', 'Employee Name:', ['class' => 'col-sm-4 control-label']) !!}
							<div class="col-sm-8">
							<?php 
								//$employeeIds = DB::table('gnr_employee')->where('branchId',$gnrBranchId)->get();
                                $employeListOfthisBranch = DB::table('hr_emp_org_info')->where('branch_id_fk',$gnrBranchId)->pluck('emp_id_fk')->toArray();

                                $employeeIds = DB::table('hr_emp_general_info')->whereIn('id',$employeListOfthisBranch)->get();  
							?>
								<select class ="form-control" id = "employeeId" autocomplete="off" name="employeeId">
								<option value="">Select Employee Name</option>
									@foreach($employeeIds as $employeeId)
									<option value="{{$employeeId->id}}">{{$employeeId->emp_id.'-'.$employeeId->emp_name_english}}</option>
									@endforeach
								</select>
								<p id='employeeIde' style="max-height:3px;"></p>
							</div>
						</div>

                        <div class="form-group useTypeCls" id="hidSoWroom">
                            {!! Form::label('roomId', 'Room Name:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                            <?php 
                                $roomIds = array(''=>'Select Room')+DB::table('gnr_room')->pluck('name','id')->all(); 
                            ?>
                                {!! Form::select('roomId', ($roomIds), null, ['class' => 'form-control', 'id' => 'roomId']) !!}
                                <p id='employeeIde' style="max-height:3px;"></p>
                            </div>
                        </div>

                        <div class="form-group" id="departmentDiv">
                            {!! Form::label('departmentId', 'Department Name:', ['class' => 'col-sm-4 control-label']) !!}
                               <div class="col-sm-8">
                                                                                
                                        {{-- @foreach(App\gnr\GnrDepartment::all() as $key => $departmentId)
                                            <div class="col-sm-4" style="padding-left: 15px;padding-right: 0px;">
                                                <p style="font-size: 14px;">
                                                {!! Form::checkbox("departmentId[]", $departmentId->id, true, array('class' => '')) !!} &nbsp
                                                {!! Form::label('departmentId', $departmentId->name, ['class' => 'control-label']) !!}
                                                </p>
                                            </div>
                                        @endforeach    --}} 

                                        @php
                                            $departments = DB::table('gnr_department')->select('name','id')->get();
                                        @endphp

                                        <select id="departmentId" name="departmentId[]" class="form-control" multiple="multiple">
                                        @foreach ($departments as $department)
                                            <option value="{{$department->id}}" selected="selected">{{$department->name}}</option>
                                        @endforeach
                                            
                                        </select>
                                        
                                        
                                                                             
                                        
                                </div>
                        </div>

                        

                        <div class="form-group">
                            {!! Form::label('totlalUseQuantity', 'Total Use Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totlalUseQuantity', $value = null, ['class' => 'form-control', 'id' => 'totlalUseQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('totalUseAmount', 'Total Use Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalUseAmount', $value = null, ['class' => 'form-control', 'id' => 'totalUseAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('averagePriceInput', 'Average Price:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('averagePriceInput', $value = null, ['class' => 'form-control', 'id' => 'averagePriceInput', 'type' => 'text','autocomplete'=>'off', 'readonly']) !!}
                            </div>
                        </div>

                        <div class="form-group">
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
									<th style="text-align:center;" class="col-sm-3">Item Name</th>
									<th style="text-align:center;" class="col-sm-2">Qty</th>
									<th style="text-align:center;" class="col-sm-3 hidden">Price</th>
									<th style="text-align:center;" class="col-sm-3 hidden">Total</th>
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
								<td class="hidden">
									
								</td>
								<td class="hidden">
									
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
								<td class="hidden"></td>
								<td class="hidden"></td>
								<td class=""></td>
							</tr>
							</tbody>
							<tfoot>
								<tr>
									<td style="text-align:right;"><strong>Total Quantity</strong></td>
									<td style="text-align:center;" id='totalQuantityFooter'></td>
									<td style="text-align:center;" id='productPriceShow' class="hidden"><strong>Total Amount</strong></td>
									<td style="text-align:center;" id='proTotalPriceShow' class="hidden"></td>
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

<script>  
 $(document).ready(function(){  

    //Stock calculation======================================================
    var stockQuantity           = 0;
    var toralPriceAllTbl        = 0;
    var averagePrice            = 0;
    var changeProductQuantity   = 0;
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

            //alert(productId);
            $.ajax({
                type: 'post',
                url: './calculationInvAverageprice',
                data: {productId:productId,branchId:branchId,_token:csrf},
                dataType: 'json',
                success: function(data) {
                    //alert(JSON.stringify(data));
                   // alert(data);
                        toralPriceAllTbl  = data;
                    $("#currentStock").trigger('input');
                }
            });       
        });

        /*$('#currentStock').on('input',function(){
            //$('#currentStockFdiv').removeClass('hidden');
            $('#currentStock').css({background:'#ffd1b3'});
        });

        // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
            $('#productQntty').on('input', function(){
            var givenProductQuantity = parseFloat($('#productQntty').val()); //alert(givenProductQuantity);
            var perProStock = parseFloat(stockQuantity); //alert(perProStock);
            if(givenProductQuantity > perProStock){
                alert('Max quantity should not be more than current Stock\nYour current stock is = '+perProStock);
                $('#productQntty').val(perProStock);
            }
        });*/

        $('#currentStock , #averagePriceInput').on('input',function(){
            //alert(toralPriceAllTbl);
                averagePrice = parseFloat(Math.round(toralPriceAllTbl/stockQuantity));

            $("#averagePriceInput").val(averagePrice||0);
            $('#currentStock').css({background:'#ffd1b3'});
        });

        // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
            $('#productQntty').on('input', function(){
            var givenProductQuantity = parseFloat($('#productQntty').val()); //alert(givenProductQuantity);
            var perProStock = parseFloat(stockQuantity); //alert(perProStock);
            if(givenProductQuantity > perProStock){
                alert('Max quantity should not be more than current Stock\nYour current stock is = '+perProStock);
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
                var getApnProductQty = $(this).closest('tr').find('.apnQnt').val();
                var totalQtyforsamePro = parseFloat(getApnProductQty)+parseFloat(getProductQty); 
                $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);
                var perProPrice = $(this).closest('tr').find('.productPrice').val();
                var totalPrice = parseFloat(perProPrice)*parseFloat(totalQtyforsamePro);
                $(this).closest('tr').find('.totalAmount').val(totalPrice);
                //$(this).closest('tr').addClass('checked');

                    if(totalQtyforsamePro>stockQuantity){
                        totalPrice = stockQuantity*perProPrice; 
                            // For extra quantity and amount
                            //extrQuty   = totalQtyforsamePro-stockQuantity;
                            //extrAmount = parseFloat(extrQuty*perProPrice);

                        $(this).closest('tr').find('.apnQnt').val(stockQuantity);
                        $(this).closest('tr').find('.totalAmount').val(totalPrice);
                    }

                testx = 'yes';
                }
            });

        if(testx!=='yes'){
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td class="hidden"><input type="number" name="productPrice[]" class="form-control name_list  input-sm productPrice" id="productPrice'+i+'" style="text-align:center; cursor:default" value="'+averagePrice+'" readonly/></td><td class="hidden"><input type="number" name="proTotalPrice[]" class="form-control  name_list input-sm  totalAmount" id="totalCostPrice'+i+'" style="text-align:center; cursor:default"  value="'+totalPricePerPro+'" readonly/></td><td class=""><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
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
                $('#totalUseAmount').val(sumTotal);
                $('#proTotalPriceShow').text(sumTotal);
            });
// onclick add button quantity summation          
        var sum = 0;
            $(".apnQnt").each(function() {
                sum += Number($(this).val());
                $('#totlalUseQuantity').val(sum);
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
		  $('#totlalUseQuantity').val(qntAfterRemove);
		  var removeAmount = parseFloat($(this).closest('tr').find('.totalAmount').val());
		   var totalAmount    = parseFloat($("#proTotalPriceShow").text());
		   var totalAmountAfterRemove = totalAmount-removeAmount;
		  $('#proTotalPriceShow').text(totalAmountAfterRemove);
		  $('#totalUseAmount').val(totalAmountAfterRemove);
		  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      }); 
	 


$('form').submit(function( event ) {
    var employeeId = $("#employeeId").val(); 
    var roomId = $("#roomId").val();
    //var departmentId = $("#department").val();
    
    var departmentIds = $("#departmentId").val();
    
    
    if(employeeId=='' && (roomId=='' || departmentIds=='')){
       $("#errorEmptyUsePlace").show();
       return false;
    }else{
        $("#errorEmptyUsePlace").hide();
    }
    event.preventDefault();
    
    
    $.ajax({
         type: 'post',
         url: './addProductUseItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    	
			if(_response=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
    		if (_response.errors) {
            if (_response.errors['employeeId']) {
                $('#employeeIde').empty();
                $('#employeeIde').append('<span class="errormsg" style="color:red;">'+_response.errors.employeeId+'</span>');
                return false;
            }
    	}
        
			window.location.href = '{{url('viewUse/')}}';
        },
        error: function( _response ){
            
            alert('_response.errors');
            
        }
    });
});

	$("input").keyup(function(){
		var productQntty = $("#productQntty").val();
		if(productQntty){$('#qnttyError').hide(); $('#productQuantitye').hide();}else{$('#qnttyError').show(); $('#productQuantitye').show();}
	});
	$('select').on('change', function (e) {
	    var employeeId = $("#employeeId").val();
	    if(employeeId){$('#employeeIde').hide();}else{$('#employeeIde').show(); }
		
	    var productId = $("#productId").val();
	    if(productId){$('#productIdError').hide(); $('#productIde').hide();}else{$('#productIdError').show(); $('#productIde').show();}
		
		/*var productCategoryId = $('#productCategoryId').val();
		if(productCategoryId ==''){$("#productSubCategoryId").val(''); $("#productBrandId").val(''); $("#productSubCategoryId").prop("disabled", true); $("#productBrandId").prop("disabled", true);}*/
		
	});

    $("#employeeId").on('change', function(e){
        var employeeId = $('#employeeId').val();
        if(employeeId){ $("#roomId").val('');}
    });

    $("#roomId").on('change', function(e){
        var roomId = $('#roomId').val();
        if(roomId){ $("#employeeId").val('');}
    });

    $('.useTypeCls').hide();
    $('#departmentDiv').hide();

    $("input[name$='useType']").click(function() {
        var test = $(this).val();
        $("div.useTypeCls").hide();        
        $("#hidSoW"+test).show();

        if (test=='employee') {
            $("#departmentDiv").hide();
        }
        else{
            $("#departmentDiv").show();
        }
    });


    //Stock calculation======================================================
    var stockQuantity = '';
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
        });

        $('#currentStock').on('input',function(){
            //$('#currentStockFdiv').removeClass('hidden');
            $('#currentStock').css({background:'#ffd1b3'});
        });

        // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
            $('#productQntty').on('input', function(){
            var givenProductQuantity = parseFloat($('#productQntty').val()); //alert(givenProductQuantity);
            var perProStock = parseFloat(stockQuantity); //alert(perProStock);
            if(givenProductQuantity > perProStock){
                $('#productQntty').val(perProStock);
            }
        });

});
</script>
{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function(){

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


<script type="text/javascript">
    $(document).ready(function() {

        function toDate(dateStr) {
            var parts = dateStr.split("-");
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }

        $("#date").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "2015:c",
            minDate: new Date('2015-07-01'),
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {

            }
        });

        /*Filter Department On change Room*/
        $("#roomId").change(function() {
            var roomId = $(this).val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './invGetdepOnChangeRoom',
                type: 'POST',
                dataType: 'json',
                data: {roomId: roomId, _token: csrf},
            })
            .done(function(departmentList) {
                $("#departmentId").empty();
                
                $.each(departmentList, function(index, obj) {
                    $("#departmentId").append("<option value='"+obj.id+"' selected='selected'>"+obj.name+"</option>")
                });
                
            })
            .fail(function() {
                alert('Response Error');
            })
            .always(function() {
                console.log("complete");
            });
            
        });
        /*End Filter Department On change Room*/

        $("#departmentId").select2();
        $("#departmentId").next('span').css('width','100%');
    }); /*ready*/

    
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
 
 

 
