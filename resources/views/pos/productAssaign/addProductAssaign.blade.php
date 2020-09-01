@extends('layouts/pos_layout')
@section('title', '| Assign Product')
@section('content')
<style type="text/css">
    .select2-results__option[aria-selected=true] {
    display: none;
}
</style>
<div class="row add-data-form">
    <div class="col-md-12">
        <div class="col-md-2"></div>
            <div class="col-md-8 fullbody">
                <div class="viewTitle" style="border-bottom: 1px solid white;">
                    <a href="{{url('pos/posViewProductAssaing/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Product Assign List</a>
                </div>
                <div class="panel panel-default panel-border">
                    <div class="panel-heading">
                        <div class="panel-title">Product Assign</div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-8">
                                    {!! Form::open(array('url' => 'addProductAssaing', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('clientCompanyId', 'Company Name:', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    <?php 
                                                    $posClients = array('' => 'Please Select Client Company') + DB::table('pos_client')->pluck('clientCompanyName','id')->all(); 
                                                    ?>      
                                                    {!! Form::select('clientCompanyId', ($posClients), null, array('class'=>'form-control', 'id' => 'clientCompanyId')) !!}
                                                    <p id='clientCompanyIde' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('productId', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    <?php 
                                                    $posProducts = array('' => 'Please Select Product') + DB::table('pos_product')->pluck('name','id')->all(); 
                                                    ?>      
                                                    {!! Form::select('productId', ($posProducts), null, array('class'=>'form-control', 'id' => 'productId')) !!}
                                                    <p id='productIde' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="packageDiv">
                                            <div class="form-group">
                                                {!! Form::label('productPackage', 'Product Packages:', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                     {!! Form::text('productPackage', $value = null, ['class' => 'form-control', 'id' => 'productPackage', 'type' => 'text', 'readonly']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <h5 style="color:black;"><u>Sales Price :</u></h5>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('salesEmployeeId', 'Sales Person:', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    <?php 
                                                    $salesEmployeeIds =DB::table('hr_emp_general_info')->orderBy('emp_id')
                                                    ->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"),'id')->get(); 
                                                    ?>  
                                                    <select id="salesEmployeeId" name="salesPerson[]" class="form-control" multiple="multiple">    
                                                        @foreach ($salesEmployeeIds as $salesEmployeeId)
                                                           <option  value="{{$salesEmployeeId->id}}" >{{$salesEmployeeId->name}}</option>
                                                       @endforeach
                                                   </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('salesPriceHo', 'Head Office :', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    {!! Form::text('salesPriceHo', $value = null, ['class' => 'form-control', 'id' => 'salesPriceHo', 'type' => 'text', 'placeholder' => 'Enter Amount','autocomplite'=>'off']) !!}
                                                    <p id='salesPriceHoe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('salesPriceBo', 'Branch :', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    {!! Form::text('salesPriceBo', $value = null, ['class' => 'form-control', 'id' => 'salesPriceBo', 'type' => 'text', 'placeholder' => 'Enter Amount','autocomplite'=>'off']) !!}
                                                    <p id='salesPriceBoe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <h5 style="color:black;"><u>Service Charge :</u></h5>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('serviceEmployeeId', 'Service Person:', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    <?php 
                                                    $serviceEmployeeIds =DB::table('hr_emp_general_info')->orderBy('emp_id')
                                                    ->select(DB::raw("CONCAT(emp_id ,' - ', emp_name_english ) AS name"),'id')->get(); 
                                                    ?>  
                                                    <select id="serviceEmployeeId" name="servicePerson[]" class="form-control" multiple="multiple">    
                                                        @foreach ($serviceEmployeeIds as $serviceEmployeeId)
                                                           <option  value="{{$serviceEmployeeId->id}}" >{{$serviceEmployeeId->name}}</option>
                                                       @endforeach
                                                   </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('serviceChargeHo', 'Head Office :', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    {!! Form::text('serviceChargeHo', $value = null, ['class' => 'form-control', 'id' => 'serviceChargeHo', 'type' => 'text', 'placeholder' => 'Enter Amount','autocomplite'=>'off']) !!}
                                                    <p id='serviceChargeHoe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('serviceChargeBo', 'Branch :', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    {!! Form::text('serviceChargeBo', $value = null, ['class' => 'form-control', 'id' => 'serviceChargeBo', 'type' => 'text', 'placeholder' => 'Enter Amount','autocomplite'=>'off']) !!}
                                                    <p id='serviceChargeBoe' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('totalAmount', 'Total Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    {!! Form::text('totalAmount', $value = null, ['class' => 'form-control', 'id' => 'totalAmount', 'type' => 'text', 'placeholder' => 'Enter amount','autocomplite'=>'off']) !!}
                                                    <p id='totalAmounte' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                {!! Form::label('paymentNumber', 'Payment :', ['class' => 'col-sm-3 control-label']) !!}
                                                <div class="col-sm-9">
                                                    {!! Form::text('paymentNumbere', $value = null, ['class' => 'form-control', 'id' => 'paymentNumbere', 'type' => 'text', 'placeholder' => 'Enter payment number','autocomplite'=>'off']) !!}
                                                    <p id='paymentNumberee' style="max-height:3px;"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <table style="width:75%; margin-left:145px;padding-top: 20px;" class="table table-striped table-bordered dataTable no-footer" id="table2">

                                        <thead>
                                            <th>SN:</th>
                                            <th>Percentage(%)</th>
                                            <th>Amount(TK)</th>
                                        </thead>

                                        <p id='RowInfo' class="error" style="max-height:3px;color: red;"></p>
                                       <tbody></tbody>
              
                                    </table>    
                                    <div class="form-group" style="padding-top:20px;">
                                        {!! Form::label('submit', ' ', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::submit('Submit', ['id' => 'add', 'class' => 'btn btn-info']); !!}
                                            <a href="{{url('pos/posViewProductAssaing/')}}" class="btn btn-danger closeBtn">Close</a>
                                            <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-4 emptySpace vert-offset-top-0"><img src="../images/catalog/image15.png" width="60%" height="" style="float:right">
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
         <div class="col-md-2"></div>
    </div>
</div>


<script type="text/javascript">

$(document).ready(function(){
$('#salesEmployeeId,#serviceEmployeeId').select2(); 
$("#salesEmployeeId,#serviceEmployeeId").on("select2:select", function (evt) {
    var element = evt.params.data.element;
    var $element = $(element);
    $element.detach();
    $(this).append($element);
    $(this).trigger("change");
});

    /* Number Formet Validation Start*/
	 $('#salesPriceHo').on('input', function(event) {
         this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
     });
     $('#salesPriceBo').on('input', function(event) {
         this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
     });
     $('#serviceChargeHo').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
     });
     $('#serviceChargeBo').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
     });
     $('#paymentNumbere').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
     });
     $('#totalAmount').on('input', function(event) {
        this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1'); 
     });
     function num(argument){
        return argument.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

     $("#paymentNumbere").keyup(function(){
            var payment =$('#paymentNumbere').val();
            var totalAmount =$('#totalAmount').val();
            var thisPercentage = $(this);
            $('#table2 tbody').empty();
            if(payment>0){
            var NumberPercentage = 100/payment;
            var amountPercentage =totalAmount/payment;
            for(i=0;i<payment;i++){
              $('#table2 tbody').append('<tr><td>'+(i+1)+'</td><td style="text-align:center"><input class="percentage form-control"/></td><td><input class="amount form-control"/></td></tr>');
            }
            $('.percentage').val(num(NumberPercentage));
            $('.amount').val(num(amountPercentage));
            /*Percentage Change*/
            $(document).on('input', '.percentage',function(){
                var percentageVal =$(this).val();
                var thisPer = $(this);
                var paymentNumber =$("#paymentNumbere").val();
                var numberOfPer = paymentNumber-1;
                var amount= 100-percentageVal;
                var perValue =amount/numberOfPer;
                var totalAmount = $('#totalAmount').val();
                var percentageAmount =totalAmount*percentageVal/100;
                var percAmount =totalAmount*amount/100;
                var percAmount = percAmount/numberOfPer
                var perAmount =percentageAmount/numberOfPer;
                $( ".amount" ).not($(this)).val(num(percAmount));//change Amount clocest td
                $( ".percentage" ).not($(this)).val(perValue);
                thisPer.closest('tr').find(".amount").val(num(percentageAmount));

            });
            /*Amount Change */
            $(document).on('input', '.amount',function(){
                var amountVal =$(this).val();
                var thisAmount = $(this);
                var paymentNumber =$("#paymentNumbere").val();
                var totalAmount = $('#totalAmount').val();
                var numberOfAmount = paymentNumber-1;
                var amountDiv= totalAmount-amountVal;
                var amountValue =amountDiv/numberOfAmount;
                var percentageVal =amountVal*100;
                var perVal = percentageVal/totalAmount;
                var amountPerPercentage = 100-perVal;
                var perAmount =amountPerPercentage/numberOfAmount;
                $( ".amount" ).not($(this)).val(num(amountValue));
                $( ".percentage" ).not($(this)).val(perAmount);
                thisAmount.closest('tr').find(".percentage").val(perVal);

            });
        }
     });

    /*Number Formet Validation End*/ 

/*Insert Data Start*/
 $("#packageDiv").hide();  
$('form').submit(function( event ) {
    event.preventDefault();
    $.ajax({
         type: 'post',
         url: './posAddProductAssaignItem',
         data: $('form').serialize(),
         dataType: 'json',
        success: function( _response ){
    if (_response.errors) {
            if (_response.errors['clientCompanyId']) {
                $('#clientCompanyIde').empty();
                $('#clientCompanyIde').append('<span class="errormsg" style="color:red;">'+_response.errors.clientCompanyId+'</span>');
            }
            if (_response.errors['productId']) {
                $('#productIde').empty();
                $('#productIde').append('<span class="errormsg" style="color:red;">'+_response.errors.productId+'</span>');
            }
            if (_response.errors['salesPriceHo']) {
                $('#salesPriceHoe').empty();
                $('#salesPriceHoe').append('<span class="errormsg" style="color:red;">'+_response.errors.salesPriceHo+'</span>');
            }
             if (_response.errors['salesPriceBo']) {
                $('#salesPriceBoe').empty();
                $('#salesPriceBoe').append('<span class="errormsg" style="color:red;">'+_response.errors.salesPriceBo+'</span>');
            }
            if (_response.errors['serviceChargeHo']) {
                $('#serviceChargeHoe').empty();
                $('#serviceChargeHoe').append('<span class="errormsg" style="color:red;">'+_response.errors.serviceChargeHo+'</span>');
            }
            if (_response.errors['serviceChargeBo']) {
                $('#serviceChargeBoe').empty();
                $('#serviceChargeBoe').append('<span class="errormsg" style="color:red;">'+_response.errors.serviceChargeBo+'</span>');
            }
            if (_response.errors['paymentNumbere']) {
                $('#paymentNumberee').empty();
                $('#paymentNumberee').append('<span class="errormsg" style="color:red;">'+_response.errors.paymentNumbere+'</span>');
            }
            if (_response.errors['totalAmount']) {
                $('#totalAmounte').empty();
                $('#totalAmounte').append('<span class="errormsg" style="color:red;">'+_response.errors.totalAmount+'</span>');
            }
          
    } else {
            $("#name").val('');
            $("#clientCompanyName").val('');
            $('.error').addClass("hidden");
            $('#success').text(_response.responseText);
            window.location.href = '{{url('pos/posViewProductAssaing/')}}';
            }
        },
        error: function( _response ){
            // Handle error
            alert(_response.errors);
            
        }
    });
});
/*Insert Data End*/
/*Error Alert Remove Start*/
    $("input").keyup(function(){
            var clientCompanyId = $("#clientCompanyId").val();
            if(clientCompanyId){$('#clientCompanyIde').hide();}else{$('#clientCompanyIde').show();}
            var productId = $("#productId").val();
            if(productId){$('#productIde').hide();}else{$('#productIde').show();}
             var salesPriceHo = $("#salesPriceHo").val();
            if(salesPriceHo){$('#salesPriceHoe').hide();}else{$('#salesPriceHoe').show();}

            var salesPriceBo = $("#salesPriceBo").val();
            if(salesPriceBo){$('#salesPriceBoe').hide();}else{$('#salesPriceBoe').show();}
             var serviceChargeHo = $("#serviceChargeHo").val();
            if(serviceChargeHo){$('#serviceChargeHoe').hide();}else{$('#serviceChargeHoe').show();}
            var nId = $("#serviceChargeBo").val();
            if(nId){$('#serviceChargeBoe').hide();}else{$('#serviceChargeBoe').show();}
             var paymentNumber = $("#paymentNumbere").val();
            if(paymentNumber){$('#paymentNumberee').hide();}else{$('#paymentNumberee').show();}
            var totalAmount = $("#totalAmount").val();
            if(totalAmount){$('#totalAmounte').hide();}else{$('#totalAmounte').show();}
             
  });
    /*Error Alert Remove End */
    /*Prosuct Change Start*/
$("#productId").change(function(){
            var productId = $(this).val();
            if(productId==''){
                $('#productPackage').val('');
                return false;
            }
            var csrf = "<?php echo csrf_token(); ?>";
            $.ajax({
                type: 'post',
                url: './posAssainngOnChangeProductId',
                data: {productId:productId,_token: csrf},
                async: false,
                dataType: 'json',
                success: function(data){
                    //alert(JSON.stringify(data.productName));
                    $('#productPackage').val(data.productName);
                    if(data.productName!=''){
                        $("#packageDiv").show();
                    } else {
                        $("#packageDiv").hide();
                    }
                    
                },
                error: function(_response){
                    alert("error");
                }

        });/*End Ajax*/

    });/*End Change Product*/
});
</script> 

@endsection

