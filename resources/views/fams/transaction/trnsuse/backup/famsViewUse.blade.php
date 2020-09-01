@extends('welcome')
@section('title', '| New Group')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$grnBranchId = Session::get('branchId');
$grnBranchId;
?>
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('famsAddUse/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Use</a>
          </div>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
           /* $("#famsTrnsuseView").dataTable().yadcf([
    
            ]);*/
            $("#famsTrnsuseView").dataTable({
                   "oLanguage": {
                  "sEmptyTable": "No Records Available",
                  "sLengthMenu": "Show _MENU_ "
                  }
                });
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="famsTrnsuseView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Date</th>
                <th>Use Bill No</th>
                <th>Requisition No</th>
                <th>Branch Name</th>
                <th>Employee Name</th>
                <th>Totlal Quantity</th>
                <th>Totlal Amount</th>
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
                      <td>
                      	<?php
                            $brnchName = DB::table('gnr_branch')->select('name')->where('id',$productUse->branchId)->first();
                          ?>
                      	{{$brnchName->name}}
                      </td>
                      <td>
                      	<?php
                            $employeehName = DB::table('gnr_employee')->select('name')->where('id',$productUse->employeeId)->first();
                          ?>
                      	{{$employeehName->name}}
                      </td>
                      <td>{{$productUse->totlalUseQuentity}}</td>
                      <td>{{$productUse->totalUseAmount}}</td>
                      <td class="text-center" width="80">
                        <a href="javascript:;" class="forUseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$productUse->id}}">
                            <span class="fa fa-eye"></span>
                        </a>&nbsp
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$productUse->id}}" data-usebillno="{{$productUse->useBillNo}}" data-requisitionno="{{$productUse->requisitionNo}}" data-requisition="{{$productUse->requisition}}" data-branchid="{{$grnBranchId}}" data-employeeid="{{$productUse->employeeId}}" data-totlalusequentity="{{$productUse->totlalUseQuentity}}"  data-totaluseamount="{{$productUse->totalUseAmount}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$productUse->id}}">
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
       {!! Form::open(array('url' => 'addProductUseItem', 'role' => 'form',  'class'=>'form-horizontal form-groups')) !!}
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
							{!! Form::label('employeeId', 'Employee Name:', ['class' => 'col-sm-4 control-label']) !!}
							<div class="col-sm-8">
							<?php 
								$employeeIds = DB::table('gnr_employee')->where('branchId',$grnBranchId)->get(); 
							?>
								<select class ="form-control" id = "employeeId" autocomplete="off" name="employeeId">
								<option value="">Select Employee Name</option>
									@foreach($employeeIds as $employeeId)
									<option value="{{$employeeId->id}}">{{$employeeId->name}}</option>
									@endforeach
								</select>
								<p id='employeeIde' style="max-height:3px;"></p>
							</div>
						</div>

                        <div class="form-group">
                            {!! Form::label('totlalUseQuentity', 'Total Use Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totlalUseQuentity', $value = null, ['class' => 'form-control', 'id' => 'totlalUseQuentity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('totalUseAmount', 'Total Use Amount:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalUseAmount', $value = null, ['class' => 'form-control', 'id' => 'totalUseAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
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
                <div class="hr" style="padding-top:10px"><hr /></div>
                  	<div class="row" style="padding-top:10px">
						<div class="col-md-12">
						  
                           <div class="col-md-3">
								<div class="form-group">
									{!! Form::label('productGroupId', 'Group:', ['class' => 'control-label col-sm-12']) !!}
									<div class="col-sm-12">
									<?php 
										$ProductGroup = array('' => 'Please Select') + DB::table('fams_product_group')->pluck('name','id')->all(); 
									?>      
									{!! Form::select('productGroupId', ($ProductGroup), null, array('class'=>'form-control input-sm', 'id' => 'productGroupId')) !!}
									<p id='productGroupIde' style="max-height:3px;"></p>
									</div>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									{!! Form::label('productCategoryId', 'Category:', ['class' => 'control-label col-sm-12']) !!}
									<div class="col-sm-12">
									<?php 
										$productCategoryId = array('' => 'Please Select') + DB::table('fams_product_category')->pluck('name','id')->all(); 
									?>      
									{!! Form::select('productCategoryId', ($productCategoryId), null, array('class'=>'form-control input-sm', 'id' => 'productCategoryId')) !!}
                                	<p id='productCategoryIde' style="max-height:3px;"></p>
									</div>
								</div>
							</div>
			
                          <div class="col-md-3">
								<div class="form-group">
									{!! Form::label('productSubCategoryId', 'Subcatagory:', ['class' => 'control-label col-sm-12']) !!}
									<div class="col-sm-12">
									<?php 
										$productSubCategoryId = array('' => 'Please Select') + DB::table('fams_product_sub_category')->pluck('name','id')->all(); 
									?>  
									{!! Form::select('productSubCategoryId', ($productSubCategoryId), null, array('class'=>'form-control input-sm', 'id' => 'productSubCategoryId')) !!}
									<p id='productSubCategoryIde' style="max-height:3px;"></p>
									</div>
								</div>
							</div>
                            
                            <div class="col-md-3">
								<div class="form-group">
									{!! Form::label('productBrandId', 'Brand:', ['class' => 'control-label col-sm-12']) !!}
									<div class="col-sm-12">
									 <?php 
										$productBrandId = array('' => 'Please Select') + DB::table('fams_product_brand')->pluck('name','id')->all(); 
									 ?>
										{!! Form::select('productBrandId', ($productBrandId), null, array('class'=>'form-control input-sm', 'id' => 'productBrandId')) !!}
										<p id='productBrandIde' style="max-height:3px;"></p>
									</div>
								</div>
							</div>

						</div>
                  	</div>
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
										$productId = array('' => 'Please Select product') + DB::table('fams_product')->pluck('name','id')->all(); 
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
									<td style="text-align:center;" id='totalQuantity'></td>
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
@include('fams/transaction/trnsuse/useDetails')
@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){ 
    var i=1;
    $('#addProduct').click(function(){
    var testx = '';
    	
	var productId 		= $('#productId').val();
	var productName 	= $('#productId option:selected').text();
	var productQntty 	= parseFloat($('#productQntty').val());
	var csrf = "<?php echo csrf_token(); ?>";
        i++;
    var sum = productQntty;
	var sumTotalAmount = 0;
	if(productId == ''){$('#productIdError').removeClass('hidden'); return false;}
	else if(isNaN(productQntty) || productQntty==''){$('#qnttyError').removeClass('hidden'); return false;}	
		
		
$.ajax({
 type: 'post',
 url: './famsUseGetProductPrice',
 data: { 'productId': productId, _token: csrf},
 dataType: 'json',
success: function( data ){
	//alert(JSON.stringify(data));
	var perProCostPrice = data.costPrice;
	var totalCostPrice = perProCostPrice*productQntty;
	sumTotalAmount += totalCostPrice;
	$('#proTotalPriceShow').text(sumTotalAmount);
	$("#productPrice"+i).val(perProCostPrice); 
	$("#totalCostPrice"+i).val(totalCostPrice);
	$('#totalUseAmount').val(sumTotalAmount);
},
error: function( data ){
	// Handle error
	//alert('data.errors');
  }
});		
		$('#totalQuantity').text(sum);
		$('#productQntty').val('');
		$('#productId').val('');
		
		$('.apnQnt').each(function() {
			sum += Number($(this).val());
			$('#totalQuantity').text(sum);
		});
		
		$('.totalAmount').each(function() {
			sumTotalAmount += Number($(this).val());
			
		});
		$('#totlalUseQuentity').val(sum);

		var getProductId    = productId; //alert(getProductId);
		var getProductQty 	= productQntty; //alert(getProductQty);
		
           $('#addProductTable tr.forEmpty').each(function() {
			    var cellText = $(this).closest('tr').find('.productIdclass').val(); //alert(cellText);
			    if(cellText==getProductId){
			    var getApnProductQty = $(this).closest('tr').find('.apnQnt').val();
			    var totalQtyforsamePro = parseFloat(getApnProductQty)+parseFloat(getProductQty); 
			    $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);
			    var perProPrice = $(this).closest('tr').find('.productPrice').val();
			    var totalPrice = parseFloat(perProPrice)*parseFloat(totalQtyforsamePro);
			    $(this).closest('tr').find('.totalAmount').val(totalPrice);
			    //$(this).closest('tr').addClass('checked');
			    testx = 'yes';
				}
			});

		if(testx!=='yes'){
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide forEmpty"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td class="hidden"><input type="number" name="productPrice[]" class="form-control name_list input-sm productPrice hidden" id="productPrice'+i+'" style="text-align:center; cursor:default" value="" readonly/></td><td class="hidden"><input type="number" name="proTotalPrice[]" class="form-control hidden name_list input-sm totalAmount" id="totalCostPrice'+i+'" style="text-align:center; cursor:default"  value="" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
		}
            $('#productQntty').val('');
			$('#productId').val('');
			$('#productGroupId').val('');
			$('#productCategoryId').val(''); 
			$('#productSubCategoryId').val('');
			$('#productBrandId').val('');   
      });   
      $(document).on('click', '.btn_remove', function(){ 
		   var removeQntty = parseFloat($(this).closest('tr').find('.apnQnt').val());
		   var totalQnt    = parseFloat($("#totalQuantity").text());
		   var qntAfterRemove = totalQnt-removeQntty;
		  $('#totalQuantity').text(qntAfterRemove);
		  $('#totlalUseQuentity').val(qntAfterRemove);
		  var removeAmount = parseFloat($(this).closest('tr').find('.totalAmount').val());
		   var totalAmount    = parseFloat($("#proTotalPriceShow").text());
		   var totalAmountAfterRemove = totalAmount-removeAmount;
		  $('#proTotalPriceShow').text(totalAmountAfterRemove);
		  $('#totalUseAmount').val(totalAmountAfterRemove);
		  
		  var result = confirm("Want to delete?");
         if(result){
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();

         /*var productDetilsDeleteIdSend= $(this).closest('tr').find('.deleteIdSend').val();
         var productDetilsProductId = $(this).closest('tr').find('.apendSelectOption').val();
         var productDetilsTotalAmount = $(this).closest('tr').find('.apnQnt').val();
         var useTableUpdateId = $("#id").val();
         //alert(productDetilsDeleteIdSend);
         var csrf = "";
         
         $.ajax({
         type: 'post',
         url: './useDetaisDeleteIdSend',
         data: { id: productDetilsDeleteIdSend, productIdforPrice: productDetilsProductId, reduceAmount: productDetilsTotalAmount, useTableId: useTableUpdateId, _token: csrf},
         dataType: 'json',
        success: function( _response ){
    	alert(JSON.stringify(_response));
        },
        error: function( _response ){
            // Handle error
            alert('_response.errors');
            
        }
    });*/
     }      
  }); 
   
});//end document.readyfunction
	
	
$( document ).ready(function() {
		
$(document).on('click', '.edit-modal', function() {
	$('#addProductTable tbody .forEmpty').remove();
	//var string = '';
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
    $('#useBillNo').val($(this).data('usebillno'));
	$('#requisitionNo').val($(this).data('requisitionno'));
    $('#requisition').val($(this).data('requisition'));
    $('#branchId').val($(this).data('branchid'));
	$('#employeeId').val($(this).data('employeeid'));
	$('#totlalUseQuentity').val($(this).data('totlalusequentity'));
	$('#totalUseAmount').val($(this).data('totaluseamount'));
	$('#totalQuantity').text($(this).data('totlalusequentity'));
	$('#proTotalPriceShow').text($(this).data('totaluseamount'));
	
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
	
	
var useIdForCountRow = $(this).data('id');
var csrf = "<?php echo csrf_token(); ?>";	
$.ajax({
         type: 'post',
         url: './famsUseEditAppendRows',
         data: {
            '_token': csrf,
            'id': useIdForCountRow
         },
         dataType: 'json',
        success: function( data ){
            alert(JSON.stringify(data));
$.each(data, function (key, value) {
				if (key == "useDetailsTables") {
					var i =0;
				$.each(value, function (key1, value1) {
					//alert(value1.productId);
					var string="<tr id='row"+i+"' class='forEmpty'>";
						string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"'><option>select Product</option></select></td>";
						string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt' style='text-align:center;'  value='"+value1.productQuantity+"' autocomplete='off'/></td>";
						string+="<td class='hidden'><input type='number' name='productPrice[]' class='form-control name_list input-sm' id='productPrice"+i+"' style='text-align:center; cursor:default' value='"+value1.costPrice+"' readonly/></td>";
						string+="<td class='hidden'><input type='number' name='proTotalPrice[]'' class='form-control hidden name_list input-sm totalAmount' id='totalCostPrice"+i+"' style='text-align:center; cursor:default'  value='"+value1.totalCostPrice+"' readonly/></td>";
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
});

$("#addProductTable").hover(function(){
    $(".apnQnt").on('input',function(){
    var sum = 0;
    $('.apnQnt').each(function() {
			sum += Number($(this).val());
			$('#totalQuantity').text(sum);
			$('#totlalUseQuentity').val(sum);
		});


     });
});


// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {

$.ajax({
         type: 'post',
         url: './famsEditProductUseItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
    	//alert(JSON.stringify(data));
			//alert(data["updateDatas"][0].id);
		if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
    		if (data.errors) {
            if (data.errors['employeeId']) {
                $('#employeeIde').empty();
                $('#employeeIde').append('<span class="errormsg" style="color:red;">'+data.errors.employeeId+'</span>');
                return false;
            }
    	}else{	
		$('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["updateDatas"][0].id).replaceWith(
                                    "<tr class='item" + data["updateDatas"][0].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["updateDatas"][0].id + 
                                                                    "</td><td>" + data.dateFromarte + 
                                                                    "</td><td>" + data["updateDatas"][0].useBillNo + 
                                                                    "</td><td>" + data["updateDatas"][0].requisitionNo + 
                                                                    "</td><td>" + data["brnchName"].name +
                                                                    "</td><td>" + data["employeehName"].name +
                                                                    "</td><td>" + data["updateDatas"][0].totlalUseQuentity + 
                                                                    "</td><td>" + data["updateDatas"][0].totalUseAmount + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='forUseDetailsModel' data-id='" + data["updateDatas"][0].id + "'><span class='fa fa-eye'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-usebillno='"+ data["updateDatas"][0].useBillNo + "' data-requisitionno='" + data["updateDatas"][0].requisitionNo + "' data-requisition='" + data["updateDatas"][0].requisition + "' data-branchid='" + data["updateDatas"][0].branchId + "' data-employeeid='" + data["updateDatas"][0].employeeId + "' data-totlalusequentity='" + data["updateDatas"][0].totlalUseQuentity + "' data-totaluseamount='" + data["updateDatas"][0].totalUseAmount + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
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
	
//use detaisl
$(document).on('click', '.forUseDetailsModel', function() {
   $('.modal-header').css({"background-color":"white"});
   $('#swhoAppendRows tbody').empty();
   $('#useDetailsModel').modal('show');
   $('.modal-dialog').css('width','50%');
    var id = ($(this).data('id'));
    var crsf = ($(this).data('token'));
    //alert(id);alert(crsf);
    $.ajax({
         type: 'post',
         url: './famsProductUseDetails',
         data: {
            '_token': $('input[name=_token]').val(),
            'id': id
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
        $('#useBillnoHead').text(data['useDetails'][0].useBillNo);
        $('#useRequisitionHead').text(data['useDetails'][0].requisitionNo);
        $('#useBranchHead').text(data['brnchName'].name);
        $('#useEmployeeHead').text(data['employeehName'].name);
        $('#showUsebillNo').text(data['useDetails'][0].useBillNo);
        $('#useDateHead').text(data.dateUse);
			
        $.each(data, function (key, value) {
			{
				if (key == "useDetailsTables") {
					var i = 1;
				$.each(value, function (key1, value1) {
					//alert(value1.costPrice);
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
        $('#totalQtyDetails').val(data['useDetails'][0].totlalUseQuentity);
		$('#totalAmountDetails').val(data['useDetails'][0].totalUseAmount);
 
        },
        error: function( data ){
        
        }
    });
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
  $('.modal-dialog').css('width','30%');
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
    url: './famsDeleteUseItem',
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
    var employeeId = $("#employeeId").val();
    if(employeeId){$('#employeeIde').hide();}else{$('#employeeIde').show(); }
	
    var productId = $("#productId").val();
    if(productId){$('#productIdError').hide(); $('#productIde').hide();}else{$('#productIdError').show(); $('#productIde').show();}
	
	/*var productGroupId = $('#productGroupId').val();
	if(productGroupId ==''){$("#productCategoryId").val(''); $("#productSubCategoryId").val(''); $("#productBrandId").val(''); $("#productCategoryId").prop("disabled", true); $("#productSubCategoryId").prop("disabled", true); $("#productBrandId").prop("disabled", true);}
	
	var productCategoryId = $('#productCategoryId').val();
	if(productCategoryId ==''){$("#productSubCategoryId").val(''); $("#productBrandId").val(''); $("#productSubCategoryId").prop("disabled", true); $("#productBrandId").prop("disabled", true);}*/
	
});	

});//ready function end






</script>

{{-- Filtering --}}
<script type="text/javascript">

        $("#productGroupId").change(function(){ 
             
             var productGroupId = $('#productGroupId').val();
             
       
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './onChangeGroup',
                  data: {productGroupId:productGroupId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){

                    $("#productCategoryId").empty();
                    $("#productCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#productBrandId").empty();
                    $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                    
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  if (key == "productCategoryList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productSubCategoryList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productBrandList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
            
        }); /*End Change Product Group*/



        //Change Category
        $("#productCategoryId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var productCategoryId = $('#productCategoryId').val();
             
              
              var csrf = "<?php echo csrf_token(); ?>";

              //alert(productGroupId);
              //alert(productCategoryId);
              
              $.ajax({
                  type: 'post',
                  url: './onChangeCategory',
                  data: {productGroupId:productGroupId, productCategoryId:productCategoryId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){

                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');

                    $("#productBrandId").empty();
                    $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                    
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  if (key == "productSubCategoryList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }

                    if (key == "productBrandList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/
              
            
        }); /*End Change Category*/


        //Change Sub Category
        $("#productSubCategoryId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var productCategoryId = $('#productCategoryId').val();
             var productSubCategoryId = $('#productSubCategoryId').val();
             
              
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './onChangeSubCategory',
                  data: {productGroupId:productGroupId, productCategoryId:productCategoryId, productSubCategoryId: productSubCategoryId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){

                    $("#productBrandId").empty();
                    $("#productBrandId").prepend('<option selected="selected" value="">Please Select</option>');
                    
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  if (key == "productBrandList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productBrandId').append("<option value='"+ value1+"'>"+key1+"</option>");
                       });
                    }
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/          

            
        }); /*End Change Category*/


        //Change Brand
        $("#productBrandId").change(function(){ 
             var productGroupId = $('#productGroupId').val();
             var productCategoryId = $('#productCategoryId').val();
             var productSubCategoryId = $('#productSubCategoryId').val();
             var productBrandId = $('#productBrandId').val();
             
              
              var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './onChangeBrand',
                  data: {productGroupId:productGroupId, productCategoryId:productCategoryId, productSubCategoryId: productSubCategoryId,productBrandId: productBrandId,_token: csrf},
                  dataType: 'json',   
                  success: function( _response ){
                    
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                
                $.each(_response, function (key, value) {
                  {
                  
              
                if (key == "productList") {
                    $.each(value, function (key1,value1) {
                      
                      $('#productId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                            
                    });
                  } 
              }
            });
                
                },
                error: function(_response){
                  alert("error");
                }

              });/*End Ajax*/          

            
        }); /*End Change Brand*/
    
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
 



