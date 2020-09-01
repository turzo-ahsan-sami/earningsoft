@extends('layouts/fams_layout')
@section('title', '| Write Off')
@section('content')
<div class="row add-data-form">
    <div class="col-md-12">
            <div class="col-md-2"></div>
                <div class="col-md-8 fullbody">
                    <div class="viewTitle" style="border-bottom: 1px solid white;">
                        <a href="{{url('famsViewWriteOff/')}}" class="btn btn-info pull-right addViewBtn"><i class="glyphicon glyphicon-th-list viewIcon">
                        </i>Write Off List</a>
                    </div>
                <div class="panel panel-default panel-border">
                                <div class="panel-heading">
                                    <div class="panel-title">Write Off</div>
                                </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12" style="padding-right: 0px; padding-left: 0px;">
                                <div class="col-md-8">
                            {!! Form::open(array('url' => '', 'role' => 'form', 'class'=>'form-horizontal form-groups')) !!}
                            <div class="form-group">
                                {!! Form::label('writeOffId', 'Write Off Id:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('writeOffId', $value = null, ['class' => 'form-control', 'id' => 'writeOffId', 'type' => 'text','placeholder' => '','autocomplete'=>'off','readonly']) !!}
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
                                        <?php 
                                            $productName = array('' => 'Please Select Product Name') + DB::table('fams_product_name')->pluck('name','id')->all(); 
                                        ?>
                                        <select name="" id="productNameId" class="form-control">
                                                <option value="">Please Select Product Name</option>
                                                @foreach($productNames as $productName)
                                                    <option value="{{$productName->id}}">{{$productName->name}}</option>
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
                                            $products = DB::table('fams_product')->whereNotIn('id', $result)->select('productCode','id')->get(); 
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
                                {!! Form::label('writeOffDate', 'Write Off Date:', ['class' => 'col-sm-3 control-label']) !!}
                                <div class="col-sm-8">
                                    {!! Form::text('writeOffDate', $value = null, ['class' => 'form-control', 'id' => 'writeOffDate', 'type' => 'text','placeholder' => 'Enter Write Off Date','autocomplete'=>'off']) !!}
                                    <p id='writeOffDatee' style="max-height:3px;"></p>
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
                        <br><br>

                    <table id="productInfoTable" class="table table-bordered responsive">
                        <tr id="headerRow">
                            <th style="text-align:center;">Purchase Date</th>
                            <th style="text-align:center;display: none;">Cost Price</th>
                            <th style="text-align:center;display: none;">Additional Charge</th>
                            <th style="text-align:center;">Cost Price</th>
                            <th style="text-align:center;">Accumulated Dep.</th>
                            <th style="text-align:center;display: none;">Dep. Opening Balance</th>
                            <th style="text-align:center;display: none;">Written Down Value</th>
                            <th style="text-align:center;display: none;">Resale Value</th>
                            <th style="text-align:center;">Disposal Amount</th>
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
                        {!! Form::button('Generate', ['id' => 'generate', 'class' => 'btn btn-info']) !!}
                        <a href="{{url('famsViewWriteOff/')}}" class="btn btn-danger closeBtn">Close</a>
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

        $("#generate").click(function() {
            var writeOffDate = $("#writeOffDate").val();
            var productId = $("#productId").val();            
            var totalCost = parseFloat($("#totalCost").val());
            var accuDep = parseFloat($("#dep").val());
            var remainingdep = parseFloat($("#remainingDep").val());
            var resaleValue = parseFloat($("#resaleValue").val());
            var writeOffValue = parseFloat($("#writeOffValue").val());
            var csrf = "<?php echo csrf_token(); ?>";

            
            
            if(productId=="" || writeOffDate==""){
                if (productId=="") {
                    $("#productIde").empty();
                    $("#productIde").append('<span class="errormsg" style="color:red;">*Required</span>');
                }
                if (writeOffDate=="") {
                    $("#writeOffDatee").empty();
                    $("#writeOffDatee").append('<span class="errormsg" style="color:red;">*Required</span>');
                }
                
            }



            else{
                $.ajax({
                type: 'post',
                url: './generateWriteOff',
                data: {writeOffDate: writeOffDate, productId:productId,totalCost: totalCost,accuDep: accuDep,remainingdep: remainingdep,resaleValue: resaleValue,writeOffValue: writeOffValue, _token: csrf},
                dataType: 'json',
                success: function( data ){
                    
                    window.location.href = "famsViewWriteOff";
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
            //alert(productTypeId);
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

                    var prefix = "{{$prefix}}";                  

                    $.each(_response, function (key, value) {
                        {
                            
                            if (key == "productNameList") {
                                $.each(value, function (index,obj) {
                                    $('#productNameId').append("<option value='"+ obj.id+"'>"+obj.name+"</option>");
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
                    url: './getFamsWriteOffProductInfo',
                    type: 'post',
                    dataType: 'json',
                    data: {productId: productId, _token: csrf},
                    success: function( data ) {

                        $("#writeOffId").val(data['writeOffId']);
                        $('#productInfoTable tr:gt(0)').remove();

                        var formattedDate = new Date(data['purchaseDate']);
                        var d = formattedDate.getDate();
                        var m =  formattedDate.getMonth();
                        m += 1;  // JavaScript months are 0-11
                        var y = formattedDate.getFullYear();
                        
                        var purchaseDate = d + "-" + m + "-" + y;
                        
                          var markup = "<tr><td> <input type='text'class='form-control' style='text-align:center;' value='" + purchaseDate + "' readonly> </td><td style='display: none;'> <input type='text'  class='form-control' style='text-align:right;' value='" + data['costPrice'] + "' readonly></td><td style='display: none;'> <input type='text'  class='form-control ' style='text-align:right;' value='" + data['additionalCharge'] + "' readonly></td><td><input type='text' id='totalCost' class='form-control'  style='text-align:right;' value=' " + data['totalCost'] + "' readonly></td><td><input type='text' id='dep' class='form-control ' style='text-align:right;' value=' " + data['dep'] + "' readonly></td></td><td style='display: none;'><input type='text' class='form-control ' style='text-align:right;' value=' " + data['depOpeningBalance'] + "' readonly></td><td style='display: none;'><input type='text' id='remainingDep' class='form-control '  style='text-align:right;' value=' " + data['remainingdep'] + "' readonly></td><td style='display: none;'><input type='text' id='resaleValue' class='form-control '  style='text-align:right;' value=' " + data['resaleValue'] + "' readonly></td><td><input type='text' id='writeOffValue' class='form-control '  style='text-align:right;' value=' " + data['writeOff'] + "' readonly></td></tr>";

                            $("#headerRow").after(markup);

                            //Set Write Off Date Limit
                            if (data['lastDepDate']!=null) {
                                //alert(data['lastDepDate']);                                
                                var lastDepDate = new Date(data['lastDepDate']);
                                lastDepDate.setDate(lastDepDate.getDate() + 1); 
                                $("#writeOffDate").datepicker( "option", "minDate",  lastDepDate );
                            }
                            
                
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

{{-- Write Off Date --}}
 <script type="text/javascript">
    $(document).ready(function() {
        
        $("#writeOffDate").datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange : "1998:c",
            maxDate: "dateToday",
            dateFormat: 'dd-mm-yy',
            onSelect: function () {
                $('#writeOffDatee').hide();               
            }
        });
    });
</script> 
{{-- End Write Off Date --}}
 


@endsection






