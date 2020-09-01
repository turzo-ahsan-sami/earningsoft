@extends('layouts/pos_layout')
@section('title', '| Edit Cost Sheet')
@section('content')
<style type="text/css">
.select2-results__option[aria-selected=true] {
    display: none;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: 11px!important; 
    padding: 5px!important;
}
</style>
<div class="row add-data-form" style="height:100%">
    <div class="col-md-12">
        <div class="col-md-1"></div>
        <div class="col-md-10 fullbody">
            
            <div class="viewTitle" style="border-bottom: 1px solid white;">
                <a href="{{url('pos/costSheetList/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                </i>Cost Sheet List</a>
            </div>

            <div class="panel panel-default panel-border">
                <div class="panel-heading">
                    <div class="panel-title">Edit Cost Sheet</div>
                </div>
               
                <div class="panel-body">

                    {!! Form::open(array('url' => '' , 'enctype' => 'multipart/form-data',  'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                    <div class="row">
                        
                        <input type="hidden" value="{{ $productDetails->id }}" name="costSheetId" id="costSheetId">

                        <div class="col-md-8 col-sm-offset-2">

                            <div class="form-group">
                                {!! Form::label('Effect Date', 'Effect Date:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6 row">
                                    {!! Form::text('effectDate', date('d-m-Y', strtotime($productDetails->effectDate)), ['class' => 'form-control', 'id' => 'effectDate', 'type' => 'text','placeholder' => 'Enter date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('Product', 'Product:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6 row">
                                    <select class="form-control" id="product" name="product">
                                        <option value="0">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ ($productDetails->productId == $product->id) ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('Raw Material', 'Raw Material:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-4 row">
                                    <select class="form-control" id="rawMaterial" name="rawMaterial">
                                        <option value="0">Select Raw Material</option>
                                        @foreach($rawMaterials as $rawMaterial)
                                            <option value="{{ $rawMaterial->id }}">{{ $rawMaterial->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-sm-2">
                                    {!! Form::text('rawMetarialQty',null, array('class'=>'form-control', 'id' => 'rawMetarialQty','placeholder'=> 'Quantity')) !!}
                                </div>

                                <div class="col-sm-2">
                                    {!! Form::text('rawMaterialPrice',null, array('class'=>'form-control', 'id' => 'rawMaterialPrice','placeholder'=> 'Cost Price')) !!}
                                </div>

                                <div class="col-sm-1">
                                    {!! Form::button('+', ['id' => 'addRawMaterial', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>

                            <div id="productTable" class="row"></div>
                           
                            <div class="form-group">
                                {!! Form::label('Other Cost', 'Other Cost:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-4 row">
                                    <select class="form-control" id="otherCostType" name="otherCostType">
                                        <option value="0">Select Other Cost</option>
                                        @foreach($otherCosts as $otherCost)
                                            <option value="{{ $otherCost->id }}">{{ $otherCost->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    
                                <div class="col-sm-2">
                                    {!! Form::text('otherCostPrice',null, array('class'=>'form-control', 'id' => 'otherCostPrice','placeholder'=> 'Amount')) !!}
                                </div>

                                <div class="col-sm-2">
                                    <input type="checkbox" id="otherCostVoucher" name="otherCostVoucher" onclick="check()">
                                    <label>Voucher</label><br><br>
                                </div>

                                <div class="col-sm-1">
                                    {!! Form::button('+', ['id' => 'addOtherCost', 'class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            
                            <div id="otherCostTable" class="row"></div>
                            
                            <div class="form-group">
                                {!! Form::label('Total Amount', 'Total Amount:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-6 row">
                                    {!! Form::text('totalAmount', '0.00', ['class' => 'form-control', 'id' => 'totalAmount', 'type' => 'text', 'readonly' => 'true']) !!}
                                </div>
                            </div>

                        </div>
                    
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="form-group col-md-4 text-center">
                            {!! Form::label('submit', ' ', ['class' => 'col-sm-2 control-label']) !!}
                            <div class="col-sm-6">
                                {!! Form::button('Update', ['id' => 'submit', 'class' => 'btn btn-info']); !!}
                                <a href="{!! url('/pos/costSheetList'); !!}" class="btn btn-danger closeBtn">Close</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span id="success" style="color:green; font-size:20px;"></span>
                        </div>
                    </div>
                    {!! Form::close() !!}

                </div>
                
            </div>
            <div class="footerTitle" style="border-top:1px solid white"></div>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>

<script type="text/javascript">

    // $(document).ready(function() {
    //     $('#rawMaterial, #product').select2({});
    // });

    var rawProducts = [];
    var tempRawProducts = [];
    
    var rawMaterials = [];
    rawMaterials = <?php echo json_encode($rawMaterials); ?>

    var otherCostArr = [];
    var tempOtherCostArr = [];

    var otherCosts = [];
    var otherCosts = <?php echo json_encode($otherCosts) ?>;

    var existOtherCost = [];
    var existOtherCost = <?php echo json_encode($existOtherCost) ?>;

    var existRawProduct = [];
    existRawProduct = <?php echo json_encode($existRawProduct) ?>;

    $(document).ready(function() {
        
        rawProducts = existRawProduct;
        
        $(rawProducts).each(function(index, value) 
        {   
            tempRawProducts.push(value.productId);
        });
        appendRawMaterial(rawProducts);

        otherCostArr = existOtherCost;
        
        $(otherCostArr).each(function(index, value) 
        {   
            tempOtherCostArr.push(value.otherCostId);
        });
        appendOtherCost(otherCostArr);

    });

    checkTransaction = <?php echo json_encode($checkTransaction) ?>;
  
    $("#effectDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange : "1998:c",
        minDate: checkTransaction.startDate,
        maxDate: checkTransaction.endDate,
        dateFormat: 'dd-mm-yy',
    });

    // Raw Material Module Start
        
    $('#rawMaterial').change(function()
    {   
        var rawMaterialId = $("#rawMaterial").val();
        var effeactDate = $("#effectDate").val();

        if(effeactDate == '')
        {   
            var elements = document.getElementById("rawMaterial").selectedOptions;
            for(var i = 0; i < elements.length; i++){
                elements[i].selected = false;
            }
            
            alert('Effect Date Is Required');
            return false;
        }
        else
        {
            $.ajax({
                url:'../getProductPrice',
                type: 'GET',
                data: {rawMaterialId: rawMaterialId, effectDate: effeactDate},
                dataType: 'json',
                success: function(data) 
                {     
                    $("#rawMaterialPrice").val(data.price);
                }
            });
        }
    });

    $('#addRawMaterial').click(function(){

        var rawMaterialId = $("#rawMaterial").val();
        var rawMetarialQty = $("#rawMetarialQty").val();
        var rawMaterialPrice = $("#rawMaterialPrice").val();

        if(rawMaterialId == '0')
        {
            alert('Raw Metarial Is Required');
            return false;
        }
        else if(rawMetarialQty == 0 || rawMetarialQty == '')
        {
            alert('Raw Metarial Quantity Is Required');
            return false;
        }
        else if(rawMaterialPrice == 0 || rawMaterialPrice == '')
        {
            alert('Raw Metarial Price Is Required');
            return false;
        }
        else
        {   
            if(jQuery.inArray(parseInt(rawMaterialId), tempRawProducts) != -1) 
            {
                alert('Already Add This Raw Material, Try Another');
                return false;
            } 
            else 
            {   
                var elements = document.getElementById("rawMaterial").selectedOptions;
                for(var i = 0; i < elements.length; i++){
                    elements[i].selected = false;
                }
                $("#rawMetarialQty").val('');
                $("#rawMaterialPrice").val('');

                var rawMaterialObj = new Object();
                for(i=0; i<rawMaterials.length; i++)
                {   
                    if(rawMaterials[i].id == rawMaterialId)
                    {
                        rawMaterialObj["productName"] = rawMaterials[i].name;
                        rawMaterialObj["unitType"] = rawMaterials[i].unitType;
                    }
                }

                rawMaterialObj["productId"] = parseInt(rawMaterialId);
                rawMaterialObj["qty"] = parseInt(rawMetarialQty);
                rawMaterialObj["costPrice"] = parseInt(rawMaterialPrice);
                rawMaterialObj["totalAmount"] = rawMetarialQty * rawMaterialPrice;

                rawProducts.push(rawMaterialObj);
                tempRawProducts.push(rawMaterialId);

                appendRawMaterial(rawProducts);
            }
        }
    
    });

    function appendRawMaterial(rawProducts)
    {   
        totalRawMaterialAmount = 0;
        $("#productTable").html("");
        
        if(rawProducts.length != 0)
        {
            content = '<table class="table table-striped table-bordered" style="color:black;"><tr style="background: #696969!important; color: white;"><th>Product</th><th>UOM</th><th>Qty</th><th>Cost Price</th><th>Total Price</th><th>Action</th></tr>';
            $(rawProducts).each(function(index, value) 
            {
                content += '<tr><td style="text-align: left;">'+value.productName+'</td><td>'+value.unitType+'</td><td>'+value.qty+'</td><td style="text-align: right;">'+value.costPrice+'.00</td><td style="text-align: right;">'+value.totalAmount+'.00</td><td><button type="button" onclick="removeRawMaterial('+index+')" style="color:red">X</button></td></td></tr>';
                totalRawMaterialAmount = totalRawMaterialAmount + value.totalAmount;
            });

            content += '<tr><td colspan="4">Total</td><td style="text-align: right;">'+totalRawMaterialAmount+'.00</td><td></td></tr>';
            $("#productTable").append(content);
            $("#productTable").append('<br>');
        }

        generateTotalAmount();
    }

    function removeRawMaterial(value)
    {   
        rawProducts.splice(value, 1);
        tempRawProducts.splice(value, 1);

        appendRawMaterial(rawProducts);
    }

    // Raw Material Module Stop


    // Other Cost Module Start

    function check()
    {   
        checkValue = document.getElementById("otherCostPrice").checked;

        if(checkValue == false) document.getElementById("otherCostPrice").checked = true;
        else document.getElementById("otherCostPrice").checked = false;
    }

    $('#addOtherCost').click(function(){
        
        var otherCostTypeId = $("#otherCostType").val();
        var otherCostPrice = $("#otherCostPrice").val();
        var otherCostVoucher = document.getElementById("otherCostVoucher").checked;

        if(otherCostTypeId  == '')
        {
            alert('Other Cost Is Required');
            return false;
        }
        else if(otherCostPrice == '' || otherCostPrice == 0)
        {
            alert('Other Cost Amount Is Required');
            return false;
        }
        else
        {  
            if(jQuery.inArray(parseInt(otherCostTypeId), tempOtherCostArr) != -1) 
            {
                alert('Already Add This Other Cost, Try Another');
                return false;
            }
            else
            {
                var elements = document.getElementById("otherCostType").selectedOptions;
                for(var i = 0; i < elements.length; i++){
                    elements[i].selected = false;
                }

                $("#otherCostPrice").val('');
                document.getElementById("otherCostVoucher").checked = false;

                var otherCostObj = new Object();
                for(i=0; i<otherCosts.length; i++)
                {   
                    if(otherCosts[i].id == otherCostTypeId)  otherCostObj["otherCostName"] = otherCosts[i].name;
                }

                otherCostObj["otherCostId"] = parseInt(otherCostTypeId);
                otherCostObj["costAmount"] = parseInt(otherCostPrice);
                otherCostObj["isVoucher"] = otherCostVoucher;

                otherCostArr.push(otherCostObj);
                tempOtherCostArr.push(otherCostTypeId);

                appendOtherCost(otherCostArr);
            } 
        }
    });

    function appendOtherCost(otherCostArr)
    {
        totalOtherCostAmount = 0;
        $("#otherCostTable").html("");

        if(otherCostArr.length != 0)
        {
            content = '<table class="table table-striped table-bordered" style="color:black;"><tr style="background: #696969!important; color: white;"><th>Other Cost</th><th>Voucher</th><th>Amount</th><th>Action</th></tr>';
            $(otherCostArr).each(function(index, value) 
            {   
                (value.isVoucher == true) ? voucher = 'Yes' : voucher = 'No';
                content += '<tr><td style="text-align: left;">'+value.otherCostName+'</td><td>'+voucher+'</td><td style="text-align: right; padding-left: 5px;">'+value.costAmount+'.00'+'</td><td><button type="button" onclick="removeCost('+index+')" style="color:red">X</button></td></tr>';
                totalOtherCostAmount = totalOtherCostAmount + value.costAmount;
            });

            content += '<tr><td colspan="2">Total</td><td style="text-align: right;">'+totalOtherCostAmount+'.00</td><td></td></tr>';
            $("#otherCostTable").append(content);
            $("#otherCostTable").append('<br>');
        }
        
        generateTotalAmount();
    }

    function removeCost(value)
    {
        otherCostArr.splice(value, 1);
        tempOtherCostArr.splice(value, 1);

        appendOtherCost(otherCostArr);
    }

    // Other Cost Module Stop

    // Total Amount Calculation start

    function generateTotalAmount()
    {   
        totalAmount = 0;
        $(rawProducts).each(function(index, value) 
        {
            totalAmount = totalAmount + value.totalAmount;
        });

        $(otherCostArr).each(function(index, value) 
        {
            totalAmount = totalAmount + value.costAmount;
        });

        $("#totalAmount").val(totalAmount+'.00');
    }

    // Total Amount Calculation stop


    // Form Submit Start
    $('#submit').click(function(event) {  

        var product = $("#product").val();
        var totalAmount = $("#totalAmount").val();
        var effectDate = $('#effectDate').val();
        var csrf = "<?php echo csrf_token(); ?>";
        var costSheetId = $('#costSheetId').val();

        if(effectDate == '')
        {
            alert('Effect Date Is Required');
            return false;
        }
        else if(product == 0)
        {
            alert('Product Is Required');
            return false;
        }
        else if(rawProducts.length == 0)
        {   
            alert('Raw Material Information Is Required');
            return false;
        }
        else if(otherCostArr.length == 0)
        {
            alert('Other Cost Information Is Required');
            return false;
        }
        else
        {
            var formData = new FormData();
            formData.append('effectDate', effectDate);
            formData.append('product',product);
            formData.append('rawProducts', JSON.stringify(rawProducts));
            formData.append('otherCostArr', JSON.stringify(otherCostArr));
            formData.append('totalAmount', totalAmount);
            formData.append('costSheetId', costSheetId);
            
            formData.append('_token',csrf);

            $.ajax({
                processData: false,
                contentType: false,
                type: 'post',
                url: '../updateCostSheet',
                data: formData,
                dataType: 'json',
                success: function( data ){

                    if(data == 'success') window.location.href = '{{url('pos/costSheetList/')}}';

                }
            });
        }

    });
    // Form Submit Stop

</script>
@endsection
