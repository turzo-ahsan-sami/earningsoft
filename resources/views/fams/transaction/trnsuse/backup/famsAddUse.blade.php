@extends('welcome')
@section('title', '| New Group')
@section('content')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$grnBranchId = Session::get('branchId');
//echo $grnBranchId;
?>
<div class="row add-data-form">
    <div class="col-md-12">
    		<div class="col-md-1"></div>
    			<div class="col-md-10 fullbody">
    				<div class="viewTitle" style="border-bottom: 1px solid white;">
            			<a href="{{url('famsViewUse/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
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
									$useMaxId = DB::table('fams_tra_use')->max('id')+1;
									$valueForField = 'US'.sprintf('%04d', $grnBranchId) . sprintf('%06d', $useMaxId);
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
                        <div class="form-group">
							{!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
							<div class="col-sm-8 text-right" style="">
								{!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
								<a href="{{url('famsViewUse/')}}" class="btn btn-danger closeBtn">Close</a>
								
							</div>
						</div>
				</div>                  
				</div>		
                </div>       
                        
                  	<div class="row">
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

						</div>
                  	</div>
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
										$productId = array('' => 'Please Select product') + DB::table('fams_product')->pluck('productCode','id')->all(); 
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
    var i=0;
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
	//alert(JSON.stringify(_response));
	var perProCostPrice = data.costPrice;
	var totalCostPrice = perProCostPrice*productQntty;
	sumTotalAmount += totalCostPrice;
	$('#proTotalPriceShow').text(sumTotalAmount);
	$("#productPrice"+i).val(perProCostPrice);
	$("#totalCostPrice"+i).val(totalCostPrice);
	$('#totalUseAmount').val(sumTotalAmount);
},
error: function( data ){
	
  }
});		
		$('#totalQuantity').text(sum);
		
		
		$('.apnQnt').each(function() {
			sum += Number($(this).val());
			$('#totalQuantity').text(sum);
		});
		
		$('.totalAmount').each(function() {
			sumTotalAmount += Number($(this).val());
			
		});
		$('#totlalUseQuentity').val(sum);

        var getProductId    = $("#productId").val();
		var getProductQty 	= $("#productQntty").val();
		//var lallaa = $("#addProductTable tr").length;
			
			$('#addProductTable tr.forhide').each(function() {
			    var cellText = $(this).closest('tr').find('.productIdclass').val();//alert(cellText);
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
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center; cursor:default"  value="'+productQntty+'" readonly/></td><td class="hidden"><input type="number" name="productPrice[]" class="form-control name_list hidden input-sm productPrice" id="productPrice'+i+'" style="text-align:center; cursor:default" value="" readonly/></td><td class="hidden"><input type="number" name="proTotalPrice[]" class="form-control  name_list input-sm hidden totalAmount" id="totalCostPrice'+i+'" style="text-align:center; cursor:default"  value="" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
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
		  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      }); 
	 


$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './famsAddProductUseItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    	//alert(JSON.stringify(_response));
			if(_response=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
    		if (_response.errors) {
            if (_response.errors['employeeId']) {
                $('#employeeIde').empty();
                $('#employeeIde').append('<span class="errormsg" style="color:red;">'+_response.errors.employeeId+'</span>');
                return false;
            }
    	}
			window.location.href = '{{url('famsViewUse/')}}';
        },
        error: function( _response ){
            // Handle error
            //alert('_response.errors');
            
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

});
</script>

{{-- Filtering --}}
<script type="text/javascript">

        /*Change Product Group*/

        $("#productGroupId").change(function(){

            var branchId = $("#branchId").val();
            var productGroupId = $(this).val();
            var productSubCategoryId = $("#productSubCategoryId").val();


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsOnChangeGroup',
                data: {branchId: branchId,productGroupId:productGroupId,productSubCategoryId: productSubCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){


                    $("#productSubCategoryId").empty();
                    $("#productSubCategoryId").prepend('<option selected="selected" value="">Please Select</option>');


                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Select Product</option>');

                    $.each(_response, function (key, value) {
                        {

                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {

                                    $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
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



        //Change Sub Category
        $("#productSubCategoryId").change(function(){
            var branchId = $("#branchId").val();
            var productGroupId =  $("#productGroupId").val();
            var productSubCategoryId = $(this).val();


            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsOnChangeBranch',
                data: {branchId: branchId, productGroupId:productGroupId, productSubCategoryId: productSubCategoryId,_token: csrf},
                dataType: 'json',
                success: function( _response ){


                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Select Product</option>');

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


        }); /*End Change Sub Category*/
    
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
 
 

 
