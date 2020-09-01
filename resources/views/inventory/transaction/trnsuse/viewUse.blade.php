@extends('layouts/inventory_layout')
@section('title', '| Use')
@section('content')
@include('successMsg')

@php
  $foreignUseIds = DB::table('inv_tra_use_return')->distinct()->pluck('useId')->toArray();
@endphp

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
              <a href="{{url('addUse/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Use</a>
          </div>
          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px"><font color="white">USE LIST</font></h1>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#invTrnsView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="invTrnsView" style="color: black;">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Date</th>
                <th>Use Bill No</th>
                <th>Requisition No</th>
                <th>Branch Name</th>
                <th>Employee Name</th>
                <th>Room / Department</th>
                <th>Total Quantity</th>
              <!-- <?php 
                if($grnBranchId==1): ?>
                <th>Total Amount</th>
              <?php endif; ?> -->
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($productUses as $productUse)
                    <tr class="item{{$productUse->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{$productUse->useDate}}</td>
                      <td>{{$productUse->useBillNo}}</td>
                      <td>{{$productUse->requisitionNo}}</td>
                      <td style="padding-left: 5px; text-align: left;">
                      	<?php
                            $brnchName = DB::table('gnr_branch')->select('name')->where('id',$productUse->branchId)->first();
                          ?>
                      	{{$brnchName->name}}
                      </td>
                      <td style="padding-left: 5px; text-align: left;">
                        @php
                          $employee = array();
                          //$employeeId = DB::table('hr_emp_org_info')->where('id',$productUse->employeeId)->value('emp_id_fk');
                          
                        //   if ($productUse->employeeId!=null) {
                          if ($productUse) {
                            $employee = DB::table('hr_emp_general_info')->where('id',$productUse->employeeId)->select('emp_id','emp_name_english')->first();
                          }      
                        //   dd($productUse->employeeId, sizeof($employee));                    
                          //$employeehName = DB::table('gnr_employee')->where('id',$productUse->employeeId)->value('name');

                        @endphp
                    
                       @php
                        // dd($productUse->employeeId, $employee->emp_id);
                         if ($productUse->employeeId!=null) {
                            // echo $employee->emp_id.'-'.$employee->emp_name_english;
                         } 
                       @endphp 
                      	
                      </td>
                      <td style="padding-left: 5px; text-align: left;">
                      @php
                        $departmentRoom = DB::table('gnr_room')->where('id',$productUse->roomId)->value('name');
                      @endphp
                      {{$departmentRoom.str_repeat('&nbsp;', (4*2))}}
                        <?php

                          if ($productUse->roomId!=null) {
                            $room = DB::table('gnr_room')->where('id',$productUse->roomId)->first();
                            $splitArray=str_replace(array('[', ']', '"', ''), '',  $productUse->departmentId); 
                            $targetArray = explode(",",$splitArray);
                            $arraySize = count($targetArray);
                            $j = 0;
                            //var_dump($targetArray);
                          foreach($targetArray as $departmentId){
                            echo App\gnr\GnrDepartment::where('id',$departmentId)->value('name');
                             $j++; 
                              if($j<$arraySize){
                                echo '&nbsp/&nbsp';
                              }
                          }
                            
                          
                          }

                        ?>
                        
                      </td>
                      <td style="padding-right: 5px; text-align: right;">{{$productUse->totlalUseQuantity}}</td>
                    <!-- <?php 
                      if($grnBranchId==1): ?>
                      <td style="padding-right: 5px; text-align: right;" class="">{{$productUse->totalUseAmount}}</td>
                    <?php endif; ?> -->
                      <td class="text-center" width="80">
                        <a href="javascript:;" class="forUseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$productUse->id}}">
                            <span class="fa fa-eye"></span>
                        </a>&nbsp;
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$productUse->id}}" data-employeeid="{{$productUse->employeeId}}" data-roomid="{{$productUse->roomId}}" departmentId="{{str_replace(['"','[',']'],'',$productUse->departmentId)}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp;

                        @php
                        if (in_array($productUse->id, $foreignUseIds)) {
                          $canDelete = 0;
                        }
                        else{
                          $canDelete = 1;
                        }   
                      @endphp


                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$productUse->id}}" @php if($canDelete==0){ echo "style=\"pointer-events: none;cursor: not-allowed;\"";}@endphp>
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
       {!! Form::open(array('url' => 'addProductUseItem', 'role' => 'form', 'class'=>'form-horizontal form-groups', 'id' => 'useForm')) !!}
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
                            {!! Form::label('useBillNo', 'Use No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                               
                                {!! Form::text('useBillNo', $value = null, ['class' => 'form-control', 'id' => 'useBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
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
					         </div>    
					     <div class="col-md-6"> 
                  <div class="form-group">
      							{!! Form::label('branchId', 'Branch Name:', ['class' => 'col-sm-4 control-label']) !!}
      							<div class="col-sm-8">
      							<?php 
      								$branchNames = DB::table('gnr_branch')->where('id',$grnBranchId)->get(); 
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

                        {!! Form::radio('useType', 'employee', false, array('class' => '', 'id' => 'radionOne')) !!}
                        {!! Form::label('useType', 'Employee', ['class' => 'control-label']) !!}

                      <span style="padding-left: 10px">    
                        {!! Form::radio('useType', 'room', false, array('class' => '', 'id' => 'radionTwo')) !!}
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
                        //$roomIds = array(''=>'Select Room / Dept')+DB::table('gnr_room')->where('branchId', $gnrBranchId)->pluck('name','id')->all();
                         
                      ?>
                          {!! Form::select('roomId', ($roomIds), null, ['class' => 'form-control', 'id' => 'roomId']) !!}
                          <p id='roomIde' style="max-height:3px;"></p>
                      </div>
                  </div>

                  <div class="form-group" id="departmentDiv">
                            {!! Form::label('departmentId', 'Department Name:', ['class' => 'col-sm-4 control-label']) !!}
                               <div class="col-sm-8">
                                        
                                                                        
                                        @php
                                            $departments = DB::table('gnr_department')->select('name','id')->get();
                                        @endphp

                                        <select id="departmentId" name="departmentId[]" class="form-control" style="width: 100%;" multiple="multiple">
                                        @foreach ($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
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
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('currentStock', 'Current Stock:', ['class' => 'control-label col-sm-4']) !!}
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
									<th style="text-align:center;" class="col-sm-1">Item Name</th>
									<th style="text-align:center;" class="col-sm-1">Qty</th>
									<td style="text-align:center;" class="col-sm-3 hidden">Price</td>
									<td style="text-align:center;" class="col-sm-3 hidden">Total</td>
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



<script type="text/javascript">
$(document).ready(function(){ 

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
                    // alert(JSON.stringify("Qty"+data));
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
                    // alert(JSON.stringify("price"+data));
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
        $("#productIdError").hide();

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
      $('#totalUseAmount').val(totalAmountAfterRemove); //alert(totalAmountAfterRemove);
      
           var button_id = $(this).attr("id");   
           // $('#row'+button_id+'').remove();  
           $(this).closest('tr').remove(); 
      }); 

    $('.useTypeCls').hide();
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

    $("#employeeId").on('change', function(e){
        var employeeId = $('#employeeId').val();
        if(employeeId){ $("#roomId").val('');}
    });

    $("#roomId").on('change', function(e){
        var roomId = $('#roomId').val();
        if(roomId){ $("#employeeId").val('');}
    });
      
});//end document.readyfunction
	
	
$( document ).ready(function() {
		
$(document).on('click', '.edit-modal', function() {
  if(hasAccess('editProductUseItem')){
	$('#addProductTable tbody .forEmpty').remove();
	

    //Send ajax request for values
    var id = $(this).data('id'); //alert($(this).data('id'));
    var csrf = "<?php echo csrf_token(); ?>";  

    $.ajax({
            type: 'post',
            url: './deitedDataUseShow',
            data: {id:id, _token:csrf},
            dataType: 'json',
            success: function(data) {
                // alert(JSON.stringify(data)); 
                // alert(data[0].useBillNo);
                $('#useBillNo').val(data[0].useBillNo);
                $('#requisitionNo').val(data[0].requisitionNo);
                $('#requisition').val(data[0].requisition);
                $('#branchId').val(data[0].branchId);
                
                $('#totlalUseQuantity').val(data[0].totlalUseQuantity);
                $('#totalUseAmount').val(data[0].totalUseAmount);
                $('#totalQuantityFooter').text(data[0].totlalUseQuantity);
                $('#proTotalPriceShow').text(data[0].totalUseAmount);
                
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

    $('#employeeId').val($(this).data('employeeid'));
    $('#roomId').val($(this).data('roomid'));
    $("#roomId").trigger('change');

    /*Checked the existing Departments*/
    var tempDepIds = $(this).attr('departmentId');
    var departmentIds = tempDepIds.split(',');
    
    
    if (tempDepIds.length>0) {
      $.each(departmentIds, function(index, departmentId) {        
           $("#departmentId option[value="+departmentId+"]").prop('selected', true);
      });
    }
    

    /*End checked the existing Departments*/
	
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');

	//for checked roomId radion
  if($(this).data('roomid')){
    $('#hidSoWroom').show();
    $('#departmentDiv').show();
    $('#radionTwo').prop('checked', 'checked');
    $('#radionOne').prop('checked',false);
  }else{
    $('#hidSoWroom').hide();
    $('#departmentDiv').hide();
  }

  //for checked employee radion
  if($(this).data('employeeid')){
    $('#hidSoWemployee').show();
    $('#radionOne').prop('checked', 'checked');
    $('#radionTwo').prop('checked',false);
  }else{
    $('#hidSoWemployee').hide();
  }
	
var useIdForCountRow = $(this).data('id');
var csrf = "<?php echo csrf_token(); ?>";	
$.ajax({
         type: 'post',
         url: './useEditAppendRows',
         data: {
            '_token': csrf,
            'id': useIdForCountRow
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
						string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt"+i+"' style='text-align:center;'  value='"+value1.productQuantity+"' autocomplete='off'/></td>";
						string+="<td class='hidden'><input type='number' name='productPrice[]' class='form-control name_list input-sm productPrice' id='productPrice'"+i+"' style='text-align:center; cursor:default' value='"+value1.costPrice+"' readonly/></td>";
						string+="<td class='hidden'><input type='number' name='proTotalPrice[]'' class='form-control name_list input-sm totalAmount' id='totalCostPrice'"+i+"' style='text-align:center; cursor:default'  value='"+value1.totalCostPrice+"' readonly/></td>";
						string+="<td><input type='text' name='productName[]' class='form-control input-sm name_list hidden' style='text-align:center; cursor:default' value='"+value1.productName+"' /><input type='text' name='useDetailsTablesId[]' class='form-control input-sm name_list hidden deleteIdSend' style='text-align:center; cursor:default' value='"+value1.id+"' /><a href='javascript:;' name='remove' id='"+i+"' class='btn_remove' style='width:80px'><i class='glyphicon glyphicon-trash' style='color:red; font-size:16px'></i></a></td></td>";
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

		var productDetailsRowLength = data.useDetailsTables.length;
			//alert(thakur);
			for(var i=0; i<productDetailsRowLength; i++){
				$('#productId5'+i).val(data['useDetailsTables'][i].productId);
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

          $(document).on('focus','.apnQnt',function(){
              getPresentValue = $(this).closest('tr').find('.apnQnt').val();
        });
        //Current stock per edit row
        //ajax
        
        $(document).on('input','.apnQnt',function(){
          var productId   = $(this).closest('tr').find('.apendSelectOption').val();  
          var branchId    = <?php echo $grnBranchId; ?>; 
          var csrf        = "<?php echo csrf_token(); ?>"; 
          var id          = $(this).attr('id'); //alert(id);
          var givenalue   = $(this).closest('tr').find('.apnQnt').val(); 
          var checkWhichIdBig = 0;
          $.ajax({
                  type: 'post',
                  url: './calculationStockForBrnNhedo',
                  data: { productId:productId, branchId:branchId, _token:csrf },
                  dataType: 'json',
                  success: function(data) {
                       //alert(JSON.stringify(data));
                       //var purchaseQty        = $("#"+id).attr("max"); //alert(purchaseQty);
                       var newPerProPrice = $("#"+id).closest('tr').find('.productPrice').val();
                       var thisProNewTotal = parseInt( givenalue*newPerProPrice );
                       $("#"+id).closest('tr').find('.totalAmount').val(thisProNewTotal); 

                       $(".totalAmount").trigger('input');

                       var stockQtyTisPro = data;
                          if(parseFloat(givenalue)>parseFloat(stockQtyTisPro)){
                              alert('Use quantity \n should not be more than current stock your current stock is = '+stockQtyTisPro);
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



    $(document).on('input','.totalAmount',function(){
    var sumTotal1 = 0;
        $(".totalAmount").each(function() {
            sumTotal1 += Number($(this).val());
            $('#totalUseAmount').val(sumTotal1);
            $('#proTotalPriceShow').text(sumTotal1);
        });
      });

    $(document).on('input','.apnQnt',function(){
    var sum1 = 0;
      $('.apnQnt').each(function() {
        sum1 += Number($(this).val()); 
        $('#totlalUseQuantity').val(sum1);
        $('#totalQuantityFooter').text(sum1);
      });
    });
    
});

// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {

    var employeeId = $("#employeeId").val(); 
    var roomOrDept = $("#roomId").val();
    var departmentIds = $("#departmentId").val();
    if(employeeId=='' && (roomOrDept=='' || departmentIds=='')){
       $("#errorEmptyUsePlace").show();
       return false;
    }else{
        $("#errorEmptyUsePlace").hide();
    }

    
    if ($("input[name='productName[]'").length<=0) {
        alert('Please select atleast one product.');
        $("#productIdError").show();
        return false;
    }
    else{
        $("#productIdError").hide();
    }
        
$('.apendSelectOption').removeAttr('disabled');

$.ajax({
         type: 'post',
         url: './editProductUseItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
            location.reload();
    	//alert(JSON.stringify(data));
			//alert(data["updateDatas"][0].id);
		/*if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
    		if (data.errors) {
            if (data.errors['employeeId']) {
                $('#employeeIde').empty();
                $('#employeeIde').append('<span class="errormsg" style="color:red;">'+data.errors.employeeId+'</span>');
                return false;
            }
    	}
		else{	
		$('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(data.employeehName);
        $('.item' + data["updateDatas"][0].id).replaceWith(
                                    "<tr class='item" + data["updateDatas"][0].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["updateDatas"][0].id + 
                                                                    "</td><td>" + data.dateFromarte + 
                                                                    "</td><td>" + data["updateDatas"][0].useBillNo + 
                                                                    "</td><td>" + data["updateDatas"][0].requisitionNo + 
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data["brnchName"].name +
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data.employeehName +
                                                                    "</td><td style='text-align: left; padding-left: 5px;'>" + data.roomName +
                                                                    "</td><td style='text-align: right; padding-right: 5px;'>" + data["updateDatas"][0].totlalUseQuantity + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='forUseDetailsModel' data-id='" + data["updateDatas"][0].id + "'><span class='fa fa-eye'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-employeeid='" + data["updateDatas"][0].employeeId + "' data-roomid='" + data["updateDatas"][0].roomId + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
        $('.succsMsg').removeClass('hidden');
        $('.succsMsg').show();
        setTimeout(function(){ $('.succsMsg').hide(); }, 5000);
        	}*/
        },
        error: function( data ){
            // Handle error
            alert('errors');
            
        }
    });
});	
	
//use detaisl
$(document).on('click', '.forUseDetailsModel', function() {
  if(hasAccess('productUseDetails')){
   $('.modal-header').css({"background-color":"white"});
   $('#swhoAppendRows tbody').empty();
   $('#useDetailsModel').modal('show');
   $('.modal-dialog').css('width','50%');
    var id = ($(this).data('id'));
    var crsf = ($(this).data('token'));
    //alert(id);alert(crsf);
    $.ajax({
         type: 'post',
         url: './productUseDetails',
         data: {
            '_token': $('input[name=_token]').val(),
            'id': id
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
        $('#useBillnoHead').text(data['useDetails'].useBillNo);
        $('#useRequisitionHead').text(data['useDetails'].requisitionNo);
        $('#useBranchHead').text(data['brnchName'].name);
          if(data.employeeName==null){
            $('#useEmployeeHeadLabel').text('Room Name :');
            $('#useEmployeeHead').text(data.roomName);
            $("#useDepartmentHeadP").show();
            $('#useDepartmentHeadLabel').text('Department Name :');
            $('#useDepartmentHead').text(data.departmentName);
          }else{
            $("#useDepartmentHeadP").hide();
            $('#useEmployeeHeadLabel').text('Employee Name :');
            $('#useEmployeeHead').text(data.employeeName);
          }
        //$('#useEmployeeHead').text(data.employeeName);
        $('#showUsebillNo').text(data['useDetails'].useBillNo);
        $('#useDateHead').text(data.dateUse);
			
        $.each(data, function (key, value) {
			{
				if (key == "useDetailsTables") {
					var i = 1;
				$.each(value, function (key1, value1) {
					var string="<tr>";
						string+="<td style='text-align:center' class='slNoUse'>"+i+"</td>";
						string+="<td class='productName' style='text-align: left;'>"+value1.productName+"</td>";
						string+="<td class='productPricePerPc'>"+value1.productQuantity+"</td>";
						string+="<td class='productPricePerPc'>"+value1.costPrice+"</td>";
						string+="<td class='productPricePerPc'>"+value1.totalCostPrice+"</td>";
						string+="</tr>";
						$('#swhoAppendRows').append(string);
					i++;

				})
		    }
		}
	});
        $('#totalQtyDetails').val(data['useDetails'].totlalUseQuantity);
		    $('#totalAmountDetails').val(data['useDetails'].totalUseAmount);
 
        },
        error: function( data ){
        
        }
    });
  }
});


$(document).on('click', '.delete-modal', function() {
  if(hasAccess('deleteUseItem')){
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
    url: './deleteUseItem',
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

	$(document).on('keyup','input',function(){
		var productQntty = $("#productQntty").val();
		if(productQntty){$('#qnttyError').hide(); $('#productQuantitye').hide();}else{$('#qnttyError').show(); $('#productQuantitye').show();}
	});
	$(document).on('change','select',function (e) {
	    var employeeId = $("#employeeId").val();
	    if(employeeId){$('#employeeIde').hide();}else{$('#employeeIde').show(); }
		
	    var productId = $("#productId").val();
	    if(productId){$('#productIdError').hide(); $('#productIde').hide();}else{$('#productIdError').show(); $('#productIde').show();}
	});	

  // //Stock calculation======================================================
  //   var stockQuantity = '';
  //   var changeProductQuantity = 0;
  //   $("#productId").change(function(){ 
  //               var productId = $('#productId').val();
  //               var billNo = $('#purchaseBillNo1').find(":selected").text(); //alert(billNo); 
  //               var branchId  = <?php echo $gnrBranchId; ?>;
  //               var csrf = "<?php echo csrf_token(); ?>";  
  //           $.ajax({
  //               type: 'post',
  //               url: './calculationStockForBrnNhedo',
  //               data: {productId:productId,branchId:branchId,_token:csrf},
  //               dataType: 'json',
  //               success: function(data) {
  //                   //alert(JSON.stringify(data));
  //                   $("#currentStock").val(data);
  //                   stockQuantity = data;
  //                   $("#currentStock").trigger('input');
  //               }
  //           });      
  //       });

  //       $('#currentStock').on('input',function(){
  //           //$('#currentStockFdiv').removeClass('hidden');
  //           $('#currentStock').css({background:'#ffd1b3'});
  //       });

  //       // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
  //           $('#productQntty').on('input', function(){
  //           var givenProductQuantity = parseFloat($('#productQntty').val()); //alert(givenProductQuantity);
  //           var perProStock = parseFloat(stockQuantity); //alert(perProStock);
  //           if(givenProductQuantity > perProStock){
  //               $('#productQntty').val(perProStock);
  //           }
  //       });
  // //end stock calculation================================================

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

        
        /*Filter Department On change Room*/
        $("#roomId").change(function() {
            var roomId = $(this).val();
            var csrf = "{{csrf_token()}}";

            $.ajax({
                url: './invGetdepOnChangeRoom',
                type: 'POST',
                dataType: 'json',
                async : false,
                data: {roomId: roomId, _token: csrf},
            })
            .done(function(departmentList) {
                $("#departmentId").empty();
                $.each(departmentList, function(index, obj) {
                    $("#departmentId").append("<option value='"+obj.id+"'>"+obj.name+"</option>")
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

    });/*Ready*/

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

@include('dataTableScript')

@endsection
 



