@extends('layouts/fams_layout')
@section('title', '| Use')
@section('content')
@include('successMsg')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$grnBranchId = Session::get('branchId');
$gnrBranchId=$grnBranchId;
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

          <h1 align="center" style="font-family: Antiqua;letter-spacing: 2px; color: white;">USE LIST</h1>
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
                <th>Room No / Department</th>
                <th>Total Quantity</th>
                  @if($grnBranchId==1)
                    <th>Total Amount</th>
                  @endif
                <th>Action</th>
              </tr>
              {{ csrf_field() }}
            </thead>
            <tbody> 
                  <?php $no=0; ?>
                  @foreach($productUses as $productUse)
                    <tr class="item{{$productUse->id}}">
                      <td class="text-center slNo">{{++$no}}</td>
                      <td>
                          {{date('d-m-Y', strtotime($productUse->useDate))}}
                      </td>
                      <td>{{$productUse->useBillNo}}</td>
                      <td>{{$productUse->requisitionNo}}</td>
                      <td style="text-align: left; padding-left: 5px">
                      	<?php
                            $brnchName = DB::table('gnr_branch')->select('name')->where('id',$productUse->branchId)->first();
                          ?>
                      	{{$brnchName->name}}
                      </td>
                      <td style="text-align: left; padding-left: 5px">

                      	<?php
                          $employeehName = DB::table('hr_emp_general_info')->where('id',$productUse->employeeId)->value('emp_name_english');
                        ?>
                      	{{$employeehName}}
                      </td>
                      <td >

                        @php
                          
                          $departmentRoom = DB::table('gnr_room')->where('id',$productUse->roomId)->value('name');
                          if ($departmentRoom!=null) {
                               echo $departmentRoom.str_repeat('&nbsp;', (4*2));

                                $room = DB::table('gnr_room')->where('id',$productUse->roomId)->first();
                                $splitArray=str_replace(array('[', ']', '"', ''), '',  $room->departmentId); 
                                $targetArray = explode(",",$splitArray);
                                $arraySize = sizeof($targetArray);
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

                         
                        @endphp







                      </td>
                      <td >{{$productUse->totlalUseQuantity}}</td>
                      @if($grnBranchId==1)
                        <td style="text-align: right; padding-right: 5px">{{$productUse->totalUseAmount}}</td>
                      @endif
                      <td class="text-center" width="80">
                        <a href="javascript:;" class="forUseDetailsModel" data-token="{{csrf_token()}}" data-id="{{$productUse->id}}">
                            <span class="fa fa-eye"></span>
                        </a>&nbsp
                        <a id="editIcone" href="javascript:;" class="edit-modal" data-id="{{$productUse->id}}" data-usebillno="{{$productUse->useBillNo}}" data-requisitionno="{{$productUse->requisitionNo}}" data-requisition="{{$productUse->requisition}}" data-branchid="{{$grnBranchId}}" data-employeeid="{{$productUse->employeeId}}" data-roomid="{{$productUse->roomId}}" data-totlalusequantity="{{$productUse->totlalUseQuantity}}"  data-totaluseamount="{{$productUse->totalUseAmount}}" data-slno="{{$no}}">
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
                    {!! Form::label('useType', 'Use place:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">

                        {!! Form::radio('useType', 'employee', false, array('class' => '', 'id' => 'radionOne')) !!}
                        {!! Form::label('useType', 'Employee', ['class' => 'control-label']) !!}

                      <span style="padding-left: 10px">    
                        {!! Form::radio('useType', 'room', false, array('class' => '', 'id' => 'radionTwo')) !!}
                        {!! Form::label('room', 'Department / Room', ['class' => 'control-label']) !!}
                      </span>
                        <p id="errorEmptyUsePlace" style="color:red" hidden="hidden">This field is required</p>
                    </div>
                  </div>

                  <div class="form-group useTypeCls" id="hidSoWemployee">
                    {!! Form::label('employeeId', 'Employee Name:', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                    <?php 
                      /*$employeeIds = DB::table('gnr_employee')->where('branchId',$gnrBranchId)->get(); */
                      $employeOfThisBranch = DB::table('hr_emp_org_info')->where('branch_id_fk',$gnrBranchId)->pluck('emp_id_fk')->toArray();
                      $employeeIds = DB::table('hr_emp_general_info')->whereIn('id',$employeOfThisBranch)->get(); 
                    ?>
                      <select class ="form-control" id = "employeeId" autocomplete="off" name="employeeId">
                      <option value="">Select Employee Name</option>
                        @foreach($employeeIds as $employeeId)
                        <option value="{{$employeeId->id}}">{{$employeeId->emp_name_english}}</option>
                        @endforeach
                      </select>
                      <p id='employeeIde' style="max-height:3px;"></p>
                    </div>
                  </div>

                  <div class="form-group useTypeCls" id="hidSoWroom">
                      {!! Form::label('roomId', 'Room Name:', ['class' => 'col-sm-4 control-label']) !!}
                      <div class="col-sm-8">
                      <?php 
                          $roomIds = array(''=>'Select Room / Dept')+DB::table('gnr_room')->where('branchId', $gnrBranchId)->pluck('name','id')->all(); 
                      ?>
                          {!! Form::select('roomId', ($roomIds), null, ['class' => 'form-control', 'id' => 'roomId']) !!}
                          <p id='employeeIde' style="max-height:3px;"></p>
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
							     {!! Form::label('submit', ' ', ['class' => 'col-sm-4 control-label']) !!}
							   <div class="col-sm-8 text-right" style="">
								{!! Form::submit('submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
								  <a href="{{url('viewUse/')}}" class="btn btn-danger closeBtn">Close</a>
							</div>
						</div>
				</div>                  
			</div>		
    </div>
            <!-- filtering -->
            <div class="row" style="margin-top: 5%">
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
                    /*$productId = array('' => 'Select product') + DB::table('fams_product')->pluck('productCode','id')->all(); */
                    $products = DB::table('fams_product')->where('branchId',$grnBranchId)->select('id','name','productCode')->get();
                  ?>

                  <select id="productId" name="productId" class="form-control input-sm">
                    <option value="">Please Select Product</option>
                    @foreach($products as $product)
                    <option value="{{$product->id}}">{{$product->productCode.'-'.$product->name}}</option>
                    @endforeach
                  </select>
									{{-- {!! Form::select('productId', ($productId), null, array('class'=>'form-control input-sm', 'id' => 'productId')) !!} --}}
									
								</td>
								<td class="">
									<input type='number' class="form-control input-sm text-center" id='productQntty' name='productQntty[]' value='1'  min="1" readonly/>
									
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
		$('#totlalUseQuantity').val(sum);

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
           $('#addProductTable').append('<tr id="row'+i+'" class="forhide forEmpty"><td><input type="text" name="productName[]" class="form-control name_list input-sm" style="text-align:left; cursor:default" value="'+productName+'" readonly/></td><td><input type="number" name="productQntty5[]" class="form-control name_list input-sm apnQnt" id="apnQnt" style="text-align:center;"  value="'+productQntty+'" readonly></td><td class="hidden"><input type="number" name="productPrice[]" class="form-control name_list input-sm productPrice hidden" id="productPrice'+i+'" style="text-align:center; cursor:default" value="" readonly/></td><td class="hidden"><input type="number" name="proTotalPrice[]" class="form-control hidden name_list input-sm totalAmount" id="totalCostPrice'+i+'" style="text-align:center; cursor:default"  value="" readonly/></td><td><input type="text" name="productId5[]" class="form-control input-sm name_list hidden productIdclass" style="text-align:center; cursor:default" value="'+productId+'" id="productId5"/><a href="javascript:;" name="remove" id="'+i+'" class="btn_remove" style="width:80px"><i class="glyphicon glyphicon-trash" style="color:red; font-size:16px"></i></a></td></tr>');
		}
      //$('#productQntty').val('');
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
		  $('#totlalUseQuantity').val(qntAfterRemove);
		  var removeAmount = parseFloat($(this).closest('tr').find('.totalAmount').val());
		   var totalAmount    = parseFloat($("#proTotalPriceShow").text());
		   var totalAmountAfterRemove = totalAmount-removeAmount;
		  $('#proTotalPriceShow').text(totalAmountAfterRemove);
		  $('#totalUseAmount').val(totalAmountAfterRemove);
		  
		  //var result = confirm("Want to delete?");
         //if(result){
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();
     //}      
  }); 

    $('.useTypeCls').hide();

    $("input[name$='useType']").click(function() {
        var test = $(this).val();
        $("div.useTypeCls").hide();
        $("#hidSoW"+test).show();
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
  	$('#roomId').val($(this).data('roomid'));
  	$('#totlalUseQuantity').val($(this).data('totlalusequantity'));
  	$('#totalUseAmount').val($(this).data('totaluseamount'));
  	$('#totalQuantity').text($(this).data('totlalusequantity'));
  	$('#proTotalPriceShow').text($(this).data('totaluseamount'));
	
    $('#footer_action_button2').hide();
    $('#footer_action_button').show();
    $('.actionBtn').removeClass('delete');
    $('#myModal').modal('show');

  //for checked roomId radion
  if($(this).data('roomid')){
    $('#hidSoWroom').show();
    $('#radionTwo').prop('checked', 'checked');
    $('#radionOne').prop('checked',false);
  }else{
    $('#hidSoWroom').hide();
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
         url: './famsUseEditAppendRows',
         data: {
            '_token': csrf,
            'id': useIdForCountRow
         },
         dataType: 'json',
        success: function( data ){
            //alert(JSON.stringify(data));
$.each(data, function (key, value) {
				if (key == "useDetailsTables") {
					var i =0;
				$.each(value, function (key1, value1) {
					//alert(value1.productName);
					var string="<tr id='row"+i+"' class='forEmpty'>";
						string+="<td class='' style='text-align: left;'><select name='productId5[]' class='apendSelectOption productIdclass form-control input-sm' id='productId5"+i+"'><option>select Product</option></select></td>";
						string+="<td><input type='number' name='productQntty5[]'' class='form-control name_list input-sm apnQnt' id='apnQnt' style='text-align:center;'  value='"+value1.productQuantity+"' autocomplete='off' readonly/></td>";
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
				$('.apendSelectOption').append("<option value='"+ value1.id+"'>"+value1.productCode+'-'+value1.name+"</option>");

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
			$('#totlalUseQuantity').val(sum);
		});


     });
});


// Edit Data (Modal and function edit data)
$('.modal-footer').on('click', '.edit', function() {

    var employeeId = $("#employeeId").val(); 
    var roomOrDept = $("#roomId").val(); 
    if(!employeeId && !roomOrDept){
       $("#errorEmptyUsePlace").show();
       return false;
    }else{
        $("#errorEmptyUsePlace").hide();
    }

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
                                                                    "</td><td style='text-align:left; padding-left:5px'>" + data["brnchName"].name +
                                                                    "</td><td style='text-align:left; padding-left:5px'>" + data.employeehName +
                                                                    "</td><td style='text-align:left; padding-left:5px'>" + data.roomName +
                                                                    "</td><td style='text-align:right; padding-right:5px'>" + data["updateDatas"][0].totlalUseQuantity + 
                                                                    "</td><td style='text-align:right; padding-right:5px'>" + data["updateDatas"][0].totalUseAmount + 
                                                                    "</td><td class='text-center'><a href='javascript:;' class='forUseDetailsModel' data-id='" + data["updateDatas"][0].id + "'><span class='fa fa-eye'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='edit-modal' data-id='" + data["updateDatas"][0].id + "' data-usebillno='"+ data["updateDatas"][0].useBillNo + "' data-requisitionno='" + data["updateDatas"][0].requisitionNo + "' data-requisition='" + data["updateDatas"][0].requisition + "' data-branchid='" + data["updateDatas"][0].branchId + "' data-employeeid='" + data["updateDatas"][0].employeeId + "' data-roomid='" + data["updateDatas"][0].roomId + "'data-totlalusequantity='" + data["updateDatas"][0].totlalUseQuantity + "' data-totaluseamount='" + data["updateDatas"][0].totalUseAmount + "' data-slno='" + data.slno + "'><span class='glyphicon glyphicon-edit'></span></a>\xa0\xa0\xa0<a href='javascript:;' class='delete-modal' data-id='" + data["updateDatas"][0].id + "'><span class='glyphicon glyphicon-trash'></span></a></td></tr>");
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
        if(data.employeeName==null){
            $('#useEmployeeHeadLabel').text('Room Name :');
            $('#useEmployeeHead').text(data.roomName);
          }else{
            $('#useEmployeeHeadLabel').text('Employee Name :');
            $('#useEmployeeHead').text(data.employeeName);
          }
        //$('#useEmployeeHead').text(data['employeehName'].name);
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
        $('#totalQtyDetails').val(data['useDetails'][0].totlalUseQuantity);
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

        /*Change Product Group*/
$(document).ready(function(){ 

    function getFilteredProducts() {
    var branchId =  $("#branchId option:selected").val();
    var productGroupId =  $("#productGroupId option:selected").val();
    var productSubCategoryId = $("#productSubCategoryId option:selected").val();
    var csrf = "{{csrf_token()}}";

    $.ajax({
      url: './famsUseGetFilteredProduct',
      type: 'POST',
      dataType: 'json',
      data: {branchId: branchId, productGroupId: productGroupId, productSubCategoryId: productSubCategoryId, _token: csrf},
    })
    .done(function(product) {
      $("#productId").empty();
            $("#productId").append('<option selected="selected" value="">Please Select Product</option>');

            $.each(product, function(index, ob) {
               $("#productId").append("<option value='"+ob.id+"'>"+ob.productCode+'-'+ob.name+"</option>");
            });
      console.log("success");
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
    
  }







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


                   /* $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Select Product</option>');*/

                    $.each(_response, function (key, value) {
                        {

                            if (key == "productSubCategoryList") {
                                $.each(value, function (key1,value1) {

                                    $('#productSubCategoryId').append("<option value='"+ value1+"'>"+key1+"</option>");
                                });
                            }


                           /* if (key == "productList") {
                                $.each(value, function (key1,value1) {

                                    $('#productId').append("<option value='"+ value1+"'>"+key1+"</option>");

                                });
                            }*/
                        }
                    });


                    getFilteredProducts();

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

        }); /*End Change Product Group*/



        //Change Sub Category
        $("#productSubCategoryId").change(function(){

          getFilteredProducts();
           /* var branchId = $("#branchId").val();
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

            });*//*End Ajax*/
        }); /*End Change Sub Category*/

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
 



