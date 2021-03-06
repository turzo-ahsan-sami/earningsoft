@extends('layouts/inventory_layout')
@section('title', '| New Group')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$grnBranchId = Session::get('branchId');
?>
<div class="row">
<div class="col-md-12">
<div class="" style="">
    <div class="">
      <div class="panel panel-default" style="background-color:#708090;">
        <div class="panel-heading"  style="padding-bottom:0px">
          <div class="panel-options">
              <a href="{{url('addUseReturn/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-plus-sign addIcon"></i>Add Use Return</a>
          </div>
        </div>
        <div class="panel-body panelBodyView"> 
        <div>
          <script type="text/javascript">
          jQuery(document).ready(function($)
          {
            $("#invUseReturnView").dataTable().yadcf([
    
            ]);
          });
          </script>
        </div>
          <table class="table table-striped table-bordered" id="invUseReturnView">
            <thead>
              <tr>
                <th width="32">SL#</th>
                <th>Date</th>
                <th>Use Return BillNo</th>
                <th>Use BillNo</th>
                <th>Branch Name</th>
                <th>Employee Name</th>
                <th>Room Name</th>
                <th>Totlal Quantity</th>
                    @if($grnBranchId==1)
                      <th>Totlal Amount</th>
                    @endif
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($useReturs as $useRetur)
                    <tr class="item{{$useRetur->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>{{date('d-m-Y', strtotime($useRetur->createdDate))}}</td>
                      <td style="text-align: left; padding: 5px">{{$useRetur->useReturnBillNo}}</td>
                      <td style="text-align: left; padding: 5px">{{$useRetur->useBillNo}}</td>
                      <td style="text-align: left; padding: 5px">
                      	<?php
                            $brnchName = DB::table('gnr_branch')->select('name')->where('id',$useRetur->branchId)->first();
                          ?>
                      	{{$brnchName->name}}
                      </td>
                      <td style="text-align: left; padding: 5px">
                        <?php
                            $employeehName = DB::table('gnr_employee')->where('id',$useRetur->employeeId)->value('name');
                          ?>
                        {{$employeehName}}
                      </td>
                      <td style="text-align: left; padding: 5px">
                      	<?php
                            $roomName = DB::table('gnr_room')->where('id',$useRetur->roomId)->value('name');
                          ?>
                      	{{$roomName}}
                      </td>
                      <td style="text-align: right; padding: 5px">{{$useRetur->totalQuantity}}</td>
                          @if($grnBranchId==1)
                              <td style="text-align: right; padding: 5px">{{$useRetur->totalAmount}}</td>
                          @endif
                      <td class="text-center" width="80">
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$useRetur->id}}" data-useid="{{$useRetur->useId}}" data-usereturnbillno="{{$useRetur->useReturnBillNo}}" data-usebillno="{{$useRetur->useBillNo}}" data-branchid="{{$grnBranchId}}" data-employeeid="{{$useRetur->employeeId}}" data-totalquantity="{{$useRetur->totalQuantity}}"  data-totalamount="{{$useRetur->totalAmount}}" data-createddate="{{$useRetur->createdDate}}" data-slno="{{$no}}">
                          <span class="glyphicon glyphicon-edit"></span>
                        </a>&nbsp
                        <a id="deleteIcone" href="javascript:;" class="delete-modal" data-id="{{$useRetur->id}}">
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
       {!! Form::open(array('url' => 'addProductUseItem', 'role' => 'form',  'class'=>'form-horizontal form-groups', 'id' => 'useReturnForm')) !!}
                     
                      <div class="form-group hidden">
                            {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 control-label']) !!}
                          <div class="col-sm-10">
                            {!! Form::text('id', $value = null, ['class' => 'form-control', 'id' => 'id', 'type' => 'text']) !!}
                            {!! Form::text('slno', $value = null, ['class' => 'form-control', 'id' => 'slno', 'type' => 'text']) !!}
                          </div>
                      </div>    
                <div class="row">
                  <div class="col-md-1"></div>
                    <div class="col-md-10">
						
						              <div class="form-group">
                            {!! Form::label('useReturnBillNo', 'Use Return No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('useReturnBillNo', null, ['class' => 'form-control', 'id' => 'useReturnBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                          </div>

                          <div class="form-group">
                            {!! Form::label('useBillNo', 'Use Bill No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                              <?php 
									             $useBillNo = array('' => 'Please Select')+DB::table('inv_tra_use')->where('branchId',$grnBranchId)->pluck('useBillNo','id')->all(); 
								              ?>
								              {!! Form::select('useBillNo', ($useBillNo), null, array('class'=>'form-control', 'id' => 'useBillNo')) !!}
                            </div>
                          </div>

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
                            {!! Form::label('totalQuantity', 'Return Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('totalAmount', 'Price:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalAmount', $value = null, ['class' => 'form-control', 'id' => 'totalAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                                {!! Form::text('createdDate', $value = null, ['class' => 'form-control', 'id' => 'createdDate', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>
					       </div>    
					       <div class="col-md-1"></div>
              </div>       
                        
                  	
          <div class="row">
            <div class="col-md-0"></div>
						<div class="col-md-12">
						<table id="addProductTable" class="table table-bordered responsive addProductTable">
							<thead>
								<tr class="">
									<th style="text-align:center;" class="col-sm-3">Item Name</th>
									<th style="text-align:center;" class="col-sm-2">Qty</th>
									<td style="text-align:center;" class="col-sm-3 hidden">Price</td>
									<td style="text-align:center;" class="col-sm-3 hidden">Total</td>
									<th style="text-align:center;" class="col-sm-1">Remove</th>
								</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<?php 
										$productId = array('' => 'Please Select product') /*+ DB::table('inv_product')->pluck('name','id')->all()*/; 
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
								<td class="hidden" id=""></td>
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
@include('dataTableScript')
@endsection

<script src="{{asset('js/jquery-1.11.1.min.js')}}"></script>
<script type="text/javascript">

function lookup(arg){
   var id     = arg.getAttribute('id');
   var fieldValue  = parseFloat(arg.value);  //alert(fieldValue);
   var maxVal    = parseFloat(arg.max); //alert(max);

   if(fieldValue>maxVal){
    document.getElementById(id).value = maxVal;
    $(".apnQnt").trigger('input');
   }
}

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
		
		$('#totalQuantityFooter').text(sum);
		$('#totalQuantity').val(sum);
		
		$('.apnQnt').each(function() {
			sum += Number($(this).val());
			$('#totalQuantityFooter').text(sum);
			$('#totalQuantity').val(sum);
		});
		

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
		   var totalQnt    = parseFloat($("#totalQuantityFooter").text());
		   var qntAfterRemove = totalQnt-removeQntty;
		  $('#totalQuantityFooter').text(qntAfterRemove);
		  $('#totalQuantity').val(qntAfterRemove);
		  
		  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      }); 

$("#useBillNo").change(function(){ 
             var useId 		= $('#useBillNo').val();
             var useBillNo  = $('#useBillNo option:selected').text();
             var csrf = "<?php echo csrf_token(); ?>";
              
              $.ajax({
                  type: 'post',
                  url: './invUseBillOnCng',
                  data: {
                    useId: useId,
                    useBillNo: useBillNo,
                    _token: csrf
                },
                  dataType: 'json',   
                  success: function( data ){
                    //alert(JSON.stringify(data));
                    //alert(_response["invUseTable"][0].totalQuantity);
                    $('#employeeId').val(data["invUseTable"][0].employeeId);
                    //$('#totalQuantity').val(data["invUseTable"][0].totalQuantity);
                     $.each(data, function (key, value) {
                     	if(key == 'products'){
                     		$('#productId').empty();
                     		$('#productId').append("<option value=''>Please Select</option>"); 
                     		$.each(value, function(key1, value1){
                     			$('#productId').append("<option value='"+ value1.id+"'>"+value1.name+"</option>"); 
                     		});	
                     	}
                     });
                     $('#employeeId').prop('disabled', true);
                },
                error: function(_response){
                  alert("error");
                }

              });//End Ajax
        }); //End Change useBillNo

		var changeProductQuantity = 0;
		$("#productId").change(function(){ 
                var productId = $('#productId').val(); 
                var useBillNo = $('#useBillNo').find(":selected").text();
                var csrf = "<?php echo csrf_token(); ?>";
              $.ajax({
                  type: 'post',
                  url: './getProQtyFrmUseDetailsTable',
                  data: { _token: csrf, productId:productId, useBillNo:useBillNo},
                  dataType: 'json',   
                  success: function( data ){
                    //alert(JSON.stringify(data));
                    changeProductQuantity = data.productQuantity;
                     $('#productQntty').val(data.productQuantity);
                     $('#price').val(data.costPrice);
                },
                error: function(data){
                  alert("error");
                }

              });/*End Ajax*/       
		});
     
    
// Input in quanity field to append row
    $("#productQntty").on("input",function (e) {
    var productQntyForTotalPrice  = $(this).val(); 
    if(productQntyForTotalPrice>changeProductQuantity){$('#productQntty').val(changeProductQuantity) ;}    
	});

	$("#useReturnForm").mouseover(function(){
        var rows= $('#addProductTable tbody tr.forEmpty').length;
        if(rows>0){
            $("#employeeId").prop('disabled', true);
            $("#useBillNo").prop('disabled', true);
        }else{
        	/*$("#employeeId").prop('disabled', false);*/
        	$("#useBillNo").prop('disabled', false);
        }
    }); 

});//end document.readyfunction
	
	
$( document ).ready(function() {
		
$(document).on('click', '.edit-modal', function() { 
	$('#addProductTable tbody .forEmpty').remove();

			var useId = $(this).data('useid'); 
			$.ajax({
                  type: 'post',
                  url: './useItemPerUseBillNo',
                  data: {'_token': $('input[name=_token]').val(), 'useId':useId},
                  dataType: 'json',   
                  success: function( data ){
                    //alert(JSON.stringify(data));
                  		$("#productId").empty();
                  		$("#productId").prepend('<option selected="selected" value="">Please Select</option>');
                    $.each(data, function (key, value) {
                          $('#productId').append("<option value='"+ value.id+"'>"+value.name+"</option>"); 
                    });
                },
                error: function(data){
                  alert("error");
                }
            });

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
    $('.modal-dialog').css('width','60%');
    $('.deleteContent').hide();
    $('.form-horizontal').show();
    $('#id').val($(this).data('id'));
    $('#slno').val($(this).data('slno'));
    $('#useReturnBillNo').val($(this).data('usereturnbillno'));
	/*$('#useBillNo').val($(this).data('usebillno'));*/
	$('#useBillNo').val($(this).data('useid'));
    $('#branchId').val($(this).data('branchid'));
	$('#employeeId').val($(this).data('employeeid'));
	$('#totalQuantity').val($(this).data('totalquantity'));
	$('#createdDate').val($(this).data('createddate'));
	/*$('#totalAmount').val($(this).data('totalamount'));*/
	$('#totalQuantityFooter').text($(this).data('totalquantity'));
	/*$('#proTotalPriceShow').text($(this).data('totaluseamount'));*/
	
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');
	$('#employeeId').prop('disabled', true);
	
var useReturId = $(this).data('id');
var useId = $(this).data('useid');
var csrf = "<?php echo csrf_token(); ?>";	
$.ajax({
         type: 'post',
         url: './editMultipleAppenRows',
         data: {
            '_token': csrf,
            'id'	: useReturId,
            'useId' : useId
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
$.each(data, function (key, value) {
				if (key == "useRtrnDetailsTables") {
					var i = 0;
				$.each(value, function (key1, value1) {
					//alert(value1.productId);
					var string="<tr id='row"+i+"' class='forEmpty'>";
						string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"'><option>select Product</option></select></td>";
						string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt"+i+"' style='text-align:center;'  value='"+value1.productQuantity+"' autocomplete='off' onkeyup='lookup(this);'/></td>";
						string+="<td class='hidden'><input type='number' name='productPrice[]' class='form-control name_list input-sm' id='productPrice'"+i+"' style='text-align:center; cursor:default' value='"+value1.costPrice+"' readonly/></td>";
						string+="<td class='hidden'><input type='number' name='proTotalPrice[]'' class='form-control hidden name_list input-sm totalAmount' id='totalCostPrice'"+i+"' style='text-align:center; cursor:default'  value='"+value1.totalCostPrice+"' readonly/></td>";
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

			if (key == "purchaseQuantity") { 
	        var j = 0;
	        $.each(value, function (key1, value1) {
	         $('#apnQnt'+j).prop("max",value1.productQuantity);
	         j++
	        })
	      } 
	});

		var productDetailsRowLength = data.useRtrnDetailsTables.length;
			for(var i=0; i<productDetailsRowLength; i++){
				$('#productId5'+i).val(data['useRtrnDetailsTables'][i].productId);
			}
        },
        error: function( data ){
        
        }
    });	
});

//edit in append rows
$("#addProductTable").hover(function(){
    $(".apnQnt").on('input',function(){
        var getApnProductQty  = $(this).closest('tr').find('.apnQnt').val();
        var sum = 0;
      $('.apnQnt').each(function() {
      sum += Number($(this).val());
      $('#totalQuantity').val(sum);
      $('#totalQuantityFooter').text(sum);
    	});
     });
  });


// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {
	$('#useBillNo').removeAttr('disabled');
	$('#employeeId').removeAttr('disabled');
$.ajax({
         type: 'post',
         url: './editUseReturnItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( data ){
    		//alert(JSON.stringify(data));
			//alert(data["updateDatas"][0].id);
		if(data=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
    		if (data.errors) {
            
    	}
		else{	
		$('#MSGE').addClass("hidden"); 
        $('#MSGS').text('Data successfully inserted!');
        $('#myModal').modal('hide');
        //alert(JSON.stringify(data));
        $('.item' + data["updateDatas"][0].id).replaceWith(
                                    "<tr class='item" + data["updateDatas"][0].id + "'><td  class='text-center slNo'>" + data.slno +
                                                                    "</td><td class='hidden'>" + data["updateDatas"][0].id + 
                                                                    "</td><td>" + data.dateFromarte + 
                                                                    "</td><td style='text-align: left; padding: 5px;'>" + data["updateDatas"][0].useReturnBillNo + 
                                                                    "</td><td style='text-align: left; padding: 5px;'>" + data["updateDatas"][0].useBillNo + 
                                                                    "</td><td style='text-align: left; padding: 5px;'>" + data["brnchName"].name +
                                                                    "</td><td style='text-align: left; padding: 5px;'>" + data["employeehName"].name +
                                                                    "</td><td style='text-align: right; padding: 5px;'>" + data["updateDatas"][0].totalQuantity + 
                                                                    "</td><td style='text-align: right; padding: 5px;'>" + data["updateDatas"][0].totalAmount + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-useid='"+ data["updateDatas"][0].useId +"' data-usereturnbillno='"+ data["updateDatas"][0].useReturnBillNo + "' data-usebillno='" + data["updateDatas"][0].useBillNo + "' data-branchid='" + data["updateDatas"][0].branchId + "' data-employeeid='" + data["updateDatas"][0].employeeId + "' data-totalquantity='" + data["updateDatas"][0].totalQuantity + "' data-totalamount='" + data["updateDatas"][0].totalAmount + "' data-createddate='" + data["updateDatas"][0].createdDate + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
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
/*$(document).on('click', '.forUseDetailsModel', function() {
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
});*/


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
    url: './deleteUseReturnItem',
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
		
	});	

});//ready function end

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
 



