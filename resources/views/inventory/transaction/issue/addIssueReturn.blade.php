@extends('layouts/inventory_layout')
@section('title', '| New Group')
@section('content')
<?php 
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$branchCode = DB::table('gnr_branch')->where('id', $gnrBranchId)->value('branchCode');
?>
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('viewInvissueReturn/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Issue Return List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Issue Return</div>
                                </div>
                    <div class="panel-body">
                    {!! Form::open(array('url' => '', 'role' => 'form',  'class'=>'form-horizontal form-groups', 'id' => 'useReturnForm')) !!}
                     <div class="row">
                        <div class="col-md-1"></div>
                     <div class="col-md-10">
                        
                        <div class="form-group">
                            {!! Form::label('issueReturnBillNo', 'Issue Return No:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                               <?php 
                                    $useMaxId = DB::table('inv_tra_issue_return')->max('id')+1;
                                    $valueForField = 'ISR.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $useMaxId);
                                ?>
                                {!! Form::text('issueReturnBillNo', $value = $valueForField, ['class' => 'form-control', 'id' => 'issueReturnBillNo', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

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
                            {!! Form::label('totalQuantity', 'Return Quantity:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalQuantity', $value = null, ['class' => 'form-control', 'id' => 'totalQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                            </div>
                        </div>

                        <div class="form-group hidden">
                            {!! Form::label('totalAmount', 'Total Price:', ['class' => 'col-sm-4 control-label']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('totalAmount', $value = null, ['class' => 'form-control', 'id' => 'totalAmount', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
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
                                <a href="{{url('viewInvissueReturn/')}}" class="btn btn-danger closeBtn">Close</a>
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
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('purchaseQuantity', 'Cur. Issue Quantity:', ['class' => 'control-label col-sm-4']) !!}
                                    <div class="col-sm-6">
                                        {!! Form::text('purchaseQuantity', $value = null, ['class' => 'form-control text-center', 'id' => 'purchaseQuantity', 'type' => 'text','readonly','autocomplete'=>'off']) !!}
                                    </div>
                                </div>
                            </div>
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
                                    <th style="text-align:center;" class="col-sm-3 hidden">Price</th>
                                    <th style="text-align:center;" class="col-sm-3 hidden">Total</th>
                                    <th style="text-align:center;" class="col-sm-1">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
            <?php 
                $products = DB::table('inv_tra_issue')
                    ->join('inv_tra_issue_details', 'inv_tra_issue.id', '=', 'inv_tra_issue_details.issueId')
                    ->select('inv_tra_issue.issueBillNo', 'inv_tra_issue_details.issueProductId')
                    ->where('inv_tra_issue.branchId',Auth::user()->branchId)
                    ->get();
                $productSize = count($products);
                    if($productSize>0){
                        foreach($products as $product){
                            $productNames [] =  DB::table('inv_product')->select('name','id')->where('id',$product->issueProductId)->first();   
                        }
                        $products = array_map("unserialize", array_unique(array_map("serialize", $productNames)));
                    }
            ?>
                                <select name="productId" id="productId" class="form-control input-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>
                                    
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
                    <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                </div>
             </div>
             <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>

<script>  
 $(document).ready(function(){  
    // Stock Calculation ========================================================================Stock calculation
        var stockQuantity = 0;
        var toralPriceAllTbl    = 0;
        var averagePrice = 0;
        var changeProductQuantity = 0;
        $("#productId").change(function(){ 
                var productId = $('#productId').val(); 
                var useBillNo = $('#useBillNo').find(":selected").text();
                var csrf = "<?php echo csrf_token(); ?>";
                var branchId  = <?php echo $gnrBranchId; ?>;
                $('#productQntty').val('');
              $.ajax({
                  type: 'post',
                  url: './getQtyFrmIssueDtlTblfContorller',
                  data: { _token: csrf, productId:productId, branchId:branchId},
                  dataType: 'json',   
                  success: function( data ){
                    //alert(JSON.stringify(data));
                    //alert(data.issueQty);
                    changeProductQuantity = data;
                     $('#purchaseQuantity').val(data);
                },
                error: function(data){
                  alert("error");
                }
              });
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

        $('#currentStock, #averagePriceInput').on('input',function(){
            //$('#currentStockFdiv').removeClass('hidden');
                averagePrice = parseFloat(Math.round(toralPriceAllTbl/stockQuantity)); 
                $("#averagePriceInput").val(averagePrice||0);
            $('#currentStock').css({background:'#ffd1b3'});
            if(stockQuantity>=changeProductQuantity){
                $('#purchaseQuantity').css({background:'green', color:'white'});
            }else{
                $('#purchaseQuantity').css({background:'#ff3333', color:'white'});
            }
        });

        // Input in quanity field to append and compare with pruchase quantity and given quantity and stock
            $("#productQntty").on("input",function (e) { 
            var checkWhichIdBig = '';
            var productQntyForTotalPrice  = $(this).val();
            if(changeProductQuantity>stockQuantity){
                 checkWhichIdBig = stockQuantity; //alert('1');
            }else{
                 checkWhichIdBig = changeProductQuantity; //alert('2');
            }
            if(productQntyForTotalPrice>checkWhichIdBig){
                $('#productQntty').val(checkWhichIdBig); 
                productQntyForTotalPrice  = checkWhichIdBig; //alert('3');
            } 
            
        });

// Input in quanity field to append row
    // $("#productQntty").on("input",function (e) {
    // var productQntyForTotalPrice  = $(this).val(); 
    // if(productQntyForTotalPrice>changeProductQuantity){$('#productQntty').val(changeProductQuantity) ;}    
    // });
    
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
                    //If return product Quanityr is more Than purchase
                    if(totalQtyforsamePro>changeProductQuantity){
                            totalQtyforsamePro = changeProductQuantity;
                        }

                $(this).closest('tr').find('.apnQnt').val(totalQtyforsamePro);
                var perProPrice = $(this).closest('tr').find('.productPrice').val();
                var totalPrice = parseFloat(perProPrice)*parseFloat(totalQtyforsamePro);
                $(this).closest('tr').find('.totalAmount').val(totalPrice);
                //$(this).closest('tr').addClass('checked');
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
            $("#purchaseQuantity").val(''); 
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
           $('#row'+button_id+'').remove();  
      }); 
     

$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './addIssueReturnItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
        // alert(JSON.stringify(_response));
            if(_response=='false'){$('#productIde').show(); $('#productIde').removeClass('hidden'); return false;}
            if (_response.errors) {
                return false;
        }
            window.location.href = '{{url('viewInvissueReturn/')}}';
        },
        error: function( _response ){
            // Handle error
            alert('errors');
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

        var useBillNo = $("#useBillNo").val();
        if(useBillNo){$('#useBillNoe').hide();}else{$('#useBillNoe').show(); }
    }); 

    $("#useBillNo").change(function(){ 
             var useId      = $('#useBillNo').val();
             var useBillNo  = $('#useBillNo option:selected').text();
             var csrf = "<?php echo csrf_token(); ?>";
             
             $("#purchaseQuantity").val('');
             $("#currentStock").val('');
             $("#productQntty").val('');
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
                    if(data["invUseTable"][0].employeeId==0){
                        //$('#employeeId').val(data["invUseTable"][0].employeeId);
                        //$('#employeeIdFullDiv').show();
                        $('#employeeIdFullDiv').hide();
                        $('#roomId').val(data["invUseTable"][0].roomId);
                        $('#roomIdFullDiv').show();
                    }else{
                        $('#roomIdFullDiv').hide();
                        $('#employeeId').val(data["invUseTable"][0].employeeId);
                        $('#employeeIdFullDiv').show();
                    }

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
                     $('#roomId').prop('disabled', true);
                },
                error: function(_response){
                  alert("error");
                }

              });//End Ajax
        }); //End Change useBillNo




});
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
 
 

 
