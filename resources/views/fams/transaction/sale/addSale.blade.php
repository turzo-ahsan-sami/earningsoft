@extends('layouts/fams_layout')
@section('title', '| Sales')
@section('content')

<div class="alert alert-info alert-dismissable" id="soldOutMessage" style="display: none;">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Info!</strong> This product already Sold Out!!
</div>

<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('famsViewSale/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Sales List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Sale</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="padding-right: 0px; padding-left: 0px;">
                                <div class="col-md-8">
                            {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}

                            <div class="form-group">
                                        {!! Form::label('saleId', 'Sale Bill Id:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">                                                  
                                            {!! Form::text('productTypeId',null, array('class'=>'form-control', 'id' => 'saleId','readonly')) !!}
                                            
                                        </div>
                                    </div>
                                <div class="form-group">
                                        {!! Form::label('productTypeId', 'Product Type:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                           <select name="" id="productTypeId" class="form-control">
                                                <option value="">Please Select Product Type</option>
                                                @foreach($productTypes as $productType)
                                                    <option value="{{$productType->id}}">{{str_pad($productType->productTypeCode,3,"0",STR_PAD_LEFT ).'-'.$productType->name}}</option>
                                                @endforeach
                                            </select> 
                                            <p id='productTypeIde' style="max-height:3px;"></p>
                                        </div>
                                    </div>
                                <div class="form-group">
                                        {!! Form::label('productNameId', 'Product Name:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <select name="" id="productNameId" class="form-control">
                                                <option value="">Please Select Product Name</option>
                                                @foreach($productNames as $productName)
                                                    <option value="{{$productName->id}}">{{str_pad($productName->productNameCode,3,"0",STR_PAD_LEFT ).'-'.$productName->name}}</option>
                                                @endforeach
                                        </select> 
                                            <p id='productNameIde' style="max-height:3px;"></p>
                                        </div>
                                </div>
                                <div class="form-group">
                                        {!! Form::label('productId', 'Product ID:', ['class' => 'col-sm-3 control-label']) !!}
                                        <div class="col-sm-9">
                                        <?php 
                                            $writeOffProducts = DB::table('fams_write_off')->pluck('productId')->toArray();
                                            $soldProducts = DB::table('fams_sale')->pluck('productId')->toArray();
                                            $result = array_merge($writeOffProducts, $soldProducts);
                                            $products = DB::table('fams_product')->whereNotIn('id',$result)->select('productCode','id')->get(); 
                                        ?>
                                        <select id="productId" name="productId" class="form-control">
                                            <option value="">Please Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}">{{$prefix.$product->productCode}}</option>
                                            @endforeach
                                        </select> 

                                            <p id='productIde' style="max-height:3px;"></p>
                                        </div>
                                        <p id="productIde" style="max-height:3px;"></p>
                                </div> 
                                                              
                                <div class="form-group">
                                {!! Form::label('saleDate', 'Sale Date:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-8" style="padding-right: 0px;">
                                    {!! Form::text('saleDate', $value = null, ['class' => 'form-control', 'id' => 'saleDate', 'type' => 'text','placeholder' => 'Enter Sale Date','autocomplete'=>'off','style'=>'padding-right:0px']) !!}
                                    <p id='saleDatee' style="max-height:3px;color: red;display: none;">*Required</p>
                                </div>
                                <div class="col-sm-1" style="padding-right: 0px;">
                                <i class="fa fa-calendar" aria-hidden="true" style="font-size: 2em;"></i>
                                </div>
                            </div>
                                
                                
                            {!! Form::close() !!}
                            </div>
                            {{-- <div class="col-md-4 emptySpace vert-offset-top-0"><img src="images/catalog/image15.png" width="60%" height="" style="float:right">
                            </div> --}}
                        </div>



                        <div class="col-md-12" style="padding-left: 10px; padding-right: 10px;">
                        <br>
                        <p id="salePricee" style="max-height:3px;"></p>
                        <br>

                        

                    <table id="productInfoTable" class="table table-bordered responsive">
                        <tr id="headerRow">
                            <th style="text-align:center;">Purchase Date</th>
                            <th style="text-align:center;">Cost Price</th>                            
                            <th style="text-align:center;">Accumulated Dep.</th>
                            <th style="text-align:center;">Written Down Value</th>
                            <th style="text-align:center;display: none;">Dep. Opening Balance</th>
                            <th style="text-align:center;display: none;">Resale Price</th>
                            <th style="text-align:center;">Sales Price</th>
                        </tr>                    
                    
                </table>
            </div>
                    </div>
                    <div class="row">
             <br>
                <div class="col-md-12" style="padding-right: 10px">

            <div class='form-horizontal form-groups'>
            <div class="form-group">
                    
                    <div class="col-sm-12 text-right">
                        {!! Form::button('Submit', ['id' => 'submit', 'class' => 'btn btn-info']) !!}
                        <a href="{{url('famsViewSale/')}}" class="btn btn-danger closeBtn">Close</a>
                        <span id="success" style="color:green; font-size:20px;" class="pull-right"></span>
                    </div>
            </div>
                
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


{{-- Filtering --}}
<script type="text/javascript">
    $(document).ready(function() {
        function pad (str, max) {
              str = str.toString();
              return str.length < max ? pad("0" + str, max) : str;
            }

        $("#submit").click(function(event) {
            var productId = $("#productId").val();
            var salePrice = parseFloat($("#salePrice").val());
            var saleDate = $("#saleDate").val();
            var accDep = parseFloat($("#accDep").val());
            var writtenDownValue = parseFloat($("#writtenDownValue").val());
            var csrf = "<?php echo csrf_token(); ?>";
            
            //alert(accDep);
            
            
            
            if(productId=="" || $("#salePrice").val()=="" || saleDate==""){
                if (productId=="") {
                    $("#productIde").empty();
                    $("#productIde").append('<span class="errormsg" style="color:red;">*Required</span>');
                }
                if ($("#salePrice").val()=="") {
                    $("#salePricee").empty();
                    $("#salePricee").append('<span class="errormsg" style="color:red;">*Sale Price is Required</span>');
                    $("#salePricee").show();
                }

                if (saleDate=="") {
                    $("#saleDatee").show();
                }                
            }
            
            else{

                $.ajax({
                type: 'post',
                url: './famsStoreSale',
                data: {productId:productId,saleDate: saleDate, salePrice: salePrice,accDep: accDep, writtenDownValue: writtenDownValue, _token: csrf},
                dataType: 'json',
                success: function( data ){
                    
                   if (data=="It is already Sold Out.") {
                    $("#soldOutMessage").show();
                    setTimeout(window.location.href = "famsViewSale", 300);
                   }
                   else{
                    window.location.href = "famsViewSale";
                   }
                    
                },
                error: function(){
                    alert("error");
                }
            
             });
        }



    });





         /* Change Product Type*/
         $("#productTypeId").change(function(){
            var productTypeId = $(this).val();
            var prefix = "{{$prefix}}";

            var csrf = "<?php echo csrf_token(); ?>";
            
            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProductType',
                data: {productTypeId:productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productNameId").empty();
                    $("#productNameId").prepend('<option selected="selected" value="">Please Select Product Name</option>');

                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select Product</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productNameList") {
                                $.each(value, function (index,obj) {
                                    $('#productNameId').append("<option value='"+ obj.id+"'>"+pad(obj.productNameCode,3)+"-"+obj.name+"</option>");
                                });
                            }

                            if (key == "productList") {
                                $.each(value, function (key1,value1) {
                                    $('#productId').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Type*/


         /* Change Product Name*/
         $("#productNameId").change(function(){
            var productNameId = $(this).val();
            var productTypeId = $("#productTypeId").val();
            var prefix = "{{$prefix}}";
            

            var csrf = "<?php echo csrf_token(); ?>";

            $.ajax({
                type: 'post',
                url: './famsAddProductOnChangeProductName',
                data: {productNameId:productNameId,productTypeId: productTypeId,_token: csrf},
                dataType: 'json',
                success: function( _response ){
                    
                    $("#productId").empty();
                    $("#productId").prepend('<option selected="selected" value="">Please Select Product</option>');                   

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productList") {
                                $.each(value, function (key1,value1) {
                                    $('#productId').append("<option value='"+ value1+"'>"+prefix+key1+"</option>");
                                });
                            }
                           
                        }
                    });

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product Name*/




         /* Change Product*/
         $("#productId").change(function(){
            var productId = $(this).val();
            var csrf = "<?php echo csrf_token(); ?>";

            //alert(productId);

            $.ajax({
                type: 'post',
                url: './getFamsSaleBillId',
                data: {productId:productId,_token: csrf},
                dataType: 'json',
                success: function( data ){                                 
                                       
                    $("#saleId").val(data['saleId']);
                    //alert(data['lastDepDate']);
                    var lastDepDate = new Date(data['lastDepDate']);
                    lastDepDate.setDate(lastDepDate.getDate() + 1);
                    $("#saleDate").datepicker( "option", "minDate",  lastDepDate );

                },
                error: function(_response){
                    alert("error");
                }

            });/*End Ajax*/

         });/*End Change Product*/
    });
</script>
{{-- End Filtering --}}


{{-- Get Product Information --}}
<script type="text/javascript">
    $(document).ready(function() {
        $("#productId").change(function() {            
            var productId = this.value;

            if (productId=="") {
                $("#productIde").show();
            }
            else{
                $("#productIde").hide();
            }

            var csrf = "{{csrf_token()}}";
            if(productId==""){
                $('#productInfoTable tr:gt(0)').remove();
            }
            else{

                $.ajax({
                    url: './getFamsSaleProductInfo',
                    type: 'post',
                    dataType: 'json',
                    data: {productId: productId, _token: csrf},
                    success: function( data ) {
                        $('#productInfoTable tr:gt(0)').remove();

                        var formattedDate = new Date(data['purchaseDate']);
                        var d = formattedDate.getDate();
                        var m =  formattedDate.getMonth();
                        m += 1;  // JavaScript months are 0-11
                        var y = formattedDate.getFullYear();
                        
                        var purchaseDate = d + "-" + m + "-" + y;
                        
                          var markup = "<tr><td> <input type='text'class='form-control' style='text-align:center;' value='" + purchaseDate + "' readonly> </td><td><input type='text' class='form-control '  style='text-align:right;' value=' " + data['totalCost'] + "' readonly></td><td><input type='text' id='accDep' class='form-control ' style='text-align:right;' value=' " + data['dep'] + "' readonly></td><td><input type='text' class='form-control' id='writtenDownValue' style='text-align:right;' value=' " + data['writtenDownValue'] + "' readonly></td></td><td style='display:none'><input type='text' class='form-control' style='text-align:right;' value=' " + data['depOpeningBalance'] + "' readonly></td><td style='display:none'><input type='text' id='writeOffValue' class='form-control '  style='text-align:right;' value=' " + data['reSalePrice'] + "' readonly></td><td><input type='text' value='' class='form-control salePrice' id='salePrice' style='text-align:right'></td></tr>";

                          /* var markup = "<tr><td> <input type='text'class='form-control' style='text-align:center;' value='" + purchaseDate + "' readonly> </td><td> <input type='text'  class='form-control' style='text-align:right;' value='" + data['costPrice'] + "' readonly></td><td> <input type='text'  class='form-control ' style='text-align:right;' value='" + data['additionalCharge'] + "' readonly></td><td><input type='text' class='form-control '  style='text-align:right;' value=' " + data['totalCost'] + "' readonly></td><td><input type='text' class='form-control ' style='text-align:right;' value=' " + data['dep'] + "' readonly></td></td><td><input type='text' class='form-control ' style='text-align:right;' value=' " + data['depOpeningBalance'] + "' readonly></td><td><input type='text' id='writeOffValue' class='form-control '  style='text-align:right;' value=' " + data['reSalePrice'] + "' readonly></td><td><input type='text' value='' class='form-control salePrice' id='salePrice' style='text-align:right'></td></tr>";*/

                          

                            $("#headerRow").after(markup);
                
                        return false;
                     },
                     error: function( _response ){
                        
                        alert('_response.errors');
                    }
                 });
                
            }
        });       


    });
</script>
{{-- End Get Product Information --}}


<script type="text/javascript">
$(document).ready(function() {
    $(this).on('input', '.salePrice', function() {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        $("#salePricee").hide();
        });
});
    
</script>


 {{-- Sale Date --}}
 <script type="text/javascript">
    $(document).ready(function() {
        
        $("#saleDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1998:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#saleDatee').hide();               
            }
        });
    });
</script> 
{{-- End Sale Date --}}


@endsection






